<?php
defined( 'ABSPATH' ) || die( 'Direct Access Not Allowed' );

use MatthiasMullie\Minify;
include_once DR_PLUGIN_DIR."minify/src/Minify.php";
include_once DR_PLUGIN_DIR."minify/src/Exception.php";
include_once DR_PLUGIN_DIR."minify/src/Exceptions/BasicException.php";
include_once DR_PLUGIN_DIR."minify/src/Exceptions/FileImportException.php";
include_once DR_PLUGIN_DIR."minify/src/Exceptions/IOException.php";
include_once DR_PLUGIN_DIR."minify/src/CSS.php";
include_once DR_PLUGIN_DIR."minify/src/JS.php";
include_once DR_PLUGIN_DIR."classes/DRCombineFonts.php";
include_once DR_PLUGIN_DIR."path-converter/src/ConverterInterface.php";
include_once DR_PLUGIN_DIR."path-converter/src/Converter.php";

class DRMinification{
	public $excluded_files=[
		"min.js",
		"jquery",
		"bootstrap.min",
		'html5.js',
		'show_ads.js',
		'histats.com/js',
		'ws.amazon.com/widgets',
		'/ads/',
		'intensedebate.com',
		'scripts.chitika.net/',
		'jotform.com/',
		'gist.github.com',
		'forms.aweber.com',
		'video.unrulymedia.com',
		'stats.wp.com',
		'stats.wordpress.com',
		'widget.rafflecopter.com',
		'widget-prime.rafflecopter.com',
		'releases.flowplayer.org',
		'c.ad6media.fr',
		'cdn.stickyadstv.com',
		'www.smava.de',
		'contextual.media.net',
		'app.getresponse.com',
		'adserver.reklamstore.com',
		's0.wp.com',
		'wprp.zemanta.com',
		'files.bannersnack.com',
		'smarticon.geotrust.com',
		'js.gleam.io',
		'ir-na.amazon-adsystem.com',
		'web.ventunotech.com',
		'verify.authorize.net',
		'ads.themoneytizer.com',
		'embed.finanzcheck.de',
		'imagesrv.adition.com',
		'js.juicyads.com',
		'form.jotformeu.com',
		'speakerdeck.com',
		'content.jwplatform.com',
		'ads.investingchannel.com',
		'app.ecwid.com',
		'www.industriejobs.de',
		's.gravatar.com',
		'googlesyndication.com',
		'a.optmstr.com',
		'a.optmnstr.com',
		'adthrive.com',
		'mediavine.com',
		'js.hsforms.net',
		'googleadservices.com',
		'f.convertkit.com',
		'recaptcha/api.js'
	];

	public $exCss = array();
	public $exJs = array();
	public function __construct($options){
		$this->exCss = explode(PHP_EOL, $options['exclude_css']);
		$this->exJs = explode(PHP_EOL, $options['exclude_js']);
	}

	public function fileName($url){
		$path      = parse_url($url, PHP_URL_PATH);
		$filename  = pathinfo($path, PATHINFO_FILENAME);
		return $filename;
	}

	public function excludedJsFile($url){
		$file = $this->fileName($url);
		for($i=0; $i<sizeof($this->exJs); $i++){
			if($file == $this->exJs[$i]){
				return true;
			}
		}
		return false;
	}

	public function excludedCssFile($url){
		$file = $this->fileName($url);
		for($i=0; $i<sizeof($this->exCss); $i++){
			if($file == $this->exCss[$i]){
				return true;
			}
		}
		return false;
	}

	public function getFileSize($url){
		$ch = curl_init(); 
	    curl_setopt($ch, CURLOPT_HEADER, true); 
	    curl_setopt($ch, CURLOPT_NOBODY, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
	    curl_setopt($ch, CURLOPT_URL, $url); //specify the url
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
	    $head = curl_exec($ch);

	    $size = curl_getinfo($ch,CURLINFO_CONTENT_LENGTH_DOWNLOAD);
	    return $size;
	}

	public function removeQueriesCss($html) {
		$styles = $this->getCSSLinkedFiles( $html );
		if(!$styles){
			$styles =  [];
		}else{
			foreach($styles as $style) {
				$noquery_link = explode('?', $style[2]);
		    	$noquery_link = reset($noquery_link);
		    	$html = str_replace( $style[2], $noquery_link, $html );
			}
		}	    
		return $html;
	}

	public function removeQueriesJs($html) {
		$scripts = $this->getJSLinkedFiles( $html );
		if(!$scripts){
			$scripts =  [];
		}else{
			foreach($scripts as $script) {
				$noquery_link = explode('?', $script[2]);
			    $noquery_link = reset($noquery_link);
			    $html = str_replace( $script[2], $noquery_link, $html );
			}

		}
	    return $html;
	}

	public function getCSSBlocks($html){

		$pattern = '<style.*>(.*)<\/style>';
	    preg_match_all( '/' . $pattern . '/Umsi', $html, $matches, PREG_SET_ORDER );
		if ( empty( $matches ) ) {
			return false;
		}

	    return $matches;
	}

	public function getCSSLinkedFiles($html) {

	    $pattern = '<link\s+([^>]+[\s\'"])?href\s*=\s*[\'"]\s*?([^\'"]+\.css(?:\?[^\'"]*)?)\s*?[\'"]([^>]+)?\/?>';
	    preg_match_all( '/' . $pattern . '/Umsi', $html, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return false;
		}

	    return $matches;
	}

	public function getCSSMinifiedFiles($html) {
	    $pattern = '<link.*href=.*development_style.*>';
	    preg_match_all( '/' . $pattern . '/', $html, $matches, PREG_SET_ORDER );
		if ( empty( $matches ) ) {
			return false;
		}

	    return $matches;
	}

	public function getJSInline($html){
		preg_match_all( '/<script.*<\/script>/Umsi', $html, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return false;
		}
		$arr=[];
		foreach ($matches as $inline_script) {
			if(preg_match("/<script[^\>]+src=[\'\"][^\>]+>/i", $inline_script[0])){
				continue;
			}

			if(preg_match("/<script[^\>]+text\/template[^\>]+>/i", $inline_script[0])){
				continue;
			}
			array_push($arr,$inline_script);
		}
		
		return $arr;
	}

	public function getJSLinkedFiles($html){
		
		$pattern = '<script\s+([^>]+[\s\'"])?src\s*=\s*[\'"]\s*?([^\'"]+\.js(?:\?[^\'"]*)?)\s*?[\'"]([^>]+)?\/?>';
	    preg_match_all( '/' . $pattern . '/Umsi', $html, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return false;
		}
	    return $matches;
	}

	public function getAbsolutePath($file_path){
		$script_path=wp_parse_url(  $file_path,  -1 );
	    return $_SERVER['DOCUMENT_ROOT'].$script_path['path'];
	}

	public function isExcludedFileCss( $tag ) {
		if ( false !== strpos( $tag[0], 'data-minify=' ) || false !== strpos( $tag[0], 'data-no-minify=' ) ) {
			return true;
		}

		if ( false !== strpos( $tag[0], 'media=' ) && ! preg_match( '/media=["\'](?:\s*|[^"\']*?\b(all|screen)\b[^"\']*?)["\']/i', $tag[0] ) ) {
			return true;
		}

		if ( false !== strpos( $tag[0], 'only screen and' ) ) {
			return true;
		}

		$file_path = $this->getAbsolutePath($tag[2]);

		if ( pathinfo( $file_path, PATHINFO_EXTENSION ) !== "css" ) {
			return true;
		}

		foreach ( $this->excluded_files as $excluded_file ) {
	        if (strpos( $tag[2], $excluded_file ) !== false ) {
	          return true;
	        }
	    }

	    return false;
	}

	public function isExcludedFile( $tag ) {
	    $file_path = ['DOCUMENT_ROOT'].$tag[2];
	    //echo $file_path;
	    if ( false !== strpos( $tag[0], 'data-minify=' ) || false !== strpos( $tag[0], 'data-no-minify=' ) ) {
	        return true;
	    }
	    if ( pathinfo( $this->getAbsolutePath($file_path), PATHINFO_EXTENSION ) !== "js" ) {
	        return true;
	    }
	    $arr=wp_parse_url(  $tag[2],  -1 );
	   // print_r($arr);
	   
	    foreach ( $this->excluded_files as $excluded_file ) {
	        if (strpos( $tag[2], $excluded_file ) !== false ) {
	          return true;
	        }
	    }

	    return false;
	}

	public function drRemoveComments($html){
		return preg_replace( '/<!--(.*)-->/Uis', '', $html );
	}

	public function isLocalFile($url){
		$parsed = parse_url($url);
        if ( isset($parsed['host']) || isset($parsed['query']) ) {
			return false;
        }

        return strlen($path) < PHP_MAXPATHLEN && @is_file($path) && is_readable($path);
	}

	public function isLocalUrl($url){
		$url = explode("?",$url)[0];
		$url = explode("#",$url)[0];
		$parsed = parse_url($url);
        if ( isset($parsed['host']) || isset($parsed['query']) ) {
			$local = parse_url(get_site_url());
			if($local['host'] == $parsed['host']){
				return true;
			}else{
				return false;
			}
        }

        return false;
	}

	public function urlToDirectory($url){
		$url = explode("?",$url)[0];
		$url = explode("#",$url)[0];
		$parsed = parse_url($url);
        if ( isset($parsed['host']) || isset($parsed['query']) ) {
			$path = explode("wp-content", $url);
			if(sizeof($path)>1){
				$path = $path[1];
				return WP_CONTENT_DIR.$path;
			}else{
				return false;
			}
        }
	}

	public function minifyInlineCss($html){
		$styleBlocks = $this->getCSSBlocks($html);
		if(!$styleBlocks){
			$styleBlocks =  [];
		}else{
			foreach($styleBlocks as $style) {
				try{
					$minifier = new Minify\CSS($style[1]);
					$minifiedContent=$minifier->minify();
				    $replace_style = str_replace( '<style', '<style data-minify=1', $style[0] );
					$replace_style = str_replace( $style[1],$minifiedContent, $replace_style );
					$html = str_replace( $style[0], $replace_style, $html );
				}
				catch(Exception $e){
					echo "Exception";
				}		
			}
		}
		return $html; 	
	}

	public function minifyLinkedCss($html, $combine=false, $scope = 'local'){
		$styles = $this->getCSSLinkedFiles( $html );
		$i=0;
		if(!$styles){
			$styles =  [];
		}else{
			foreach($styles as $style) {
				$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				if($scope == "local"){
					if(parse_url($style[2], PHP_URL_HOST) != parse_url($actual_link, PHP_URL_HOST)){
						continue;
					}
				}else if($scope == "external"){
					if(parse_url($style[2], PHP_URL_HOST) == parse_url($actual_link, PHP_URL_HOST)){
						continue;
					}
				}
				$url = str_replace('&quot;', '', $style[2]);
				if($this->excludedCssFile($url)){
					continue;
				}
				$minifier = null;
				try{
					if($this->skipCss($style)){
						$html = str_replace( $style[0], '', $html );
						$touched = str_replace('<style', '<style data-minify=0', $style[0]);
						$html = str_replace('</head>', $touched."</head>",$html );
						continue;
					}else{
						$i++;
						$parsed = $this->getFileName($style[2]);
						$minifier = $this->cssMinifyExternal($minifier, $style, false);
						$minifiedPathCSS = DR_PLUGIN_DIR.'/cached/css/'.$parsed.'.min.css';
						$minifiedContent = $minifier->minify($minifiedPathCSS);
						if(sizeof($minifiedContent)>0){
							$minifiedPathCSS = WP_CONTENT_URL.'/plugins/'.DR_SLUG.'/cached/css/'.$parsed.'.min.css';
							$replace_style = str_replace( $style[2], $minifiedPathCSS, $style[0] );
							$replace_style = str_replace( '<style', '<style data-minify=1', $replace_style );
							$html = str_replace( $style[0], '', $html );
							$html = str_replace( '</head>', $replace_style.'</head>', $html );
						}
					}
				}catch(Exception $e){
					
				}
			}
		}
		return $html;
	}

	public function getFileName($url){
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$file = parse_url( $url );
		$file = $file['path'];
		$file = explode('/', $file);
		$file = $file[sizeof($file)-1];
		$file = explode('.', $file);
		$file = $file[0];

		$page = parse_url( $actual_link );
		$page = $page['path'];
		$page = explode('/', $page);
		$page = $page[sizeof($page)-1];
		if($page == ''){
			return DR_SLUG.'-index-'.$file;
		}
		return DR_SLUG.'-'.$page.'-'.$file;
	}

	public function skipCss($style){
		if ( preg_match( '/(?:-|\.)min.css/iU', $style[2] ) ) {
			return true;
		}

		if ( !file_exists($this->getAbsolutePath($style[2])) ) {
			return true;
		}

		if ( $this->isExcludedFileCss( $style ) ) {
			return  true;
		}

		return false;
	}

	public function cssSave($minifier, $index, $html){
		if($minifier){
			$minifiedPathCSS = DR_PLUGIN_DIR.'/cached/css/'.DR_SLUG.'_style_major_'.$index.'.min.css';
			$minifier->minify($minifiedPathCSS); 
			$minifiedPathCSS = WP_CONTENT_URL.'/plugins/'.DR_SLUG.'/cached/css/'.DR_SLUG.'_style_major_'.$index.'.min.css';
			$html = str_replace( '</head>', '<link rel="stylesheet" href="'.$minifiedPathCSS.'" type="text/css" media="all"></head>', $html );
		}

		return $html;
	}

	public function cssMinifyExternal($minifier, $style, $all=false){
		if($minifier !== null){
			$minifier = null;
		}
		if($minifier == null){
			if($this->isLocalFile($style[2])){
				$minifier = new Minify\CSS($style[2]);
			}else if(!$this->isLocalUrl($style[2])){
				if(DrFileSupport::get_http_response_code($style[2])=="200"){
					$minifier = new Minify\CSS(@file_get_contents($style[2]));
				}
			}else if($this->isLocalUrl($style[2])){
				$minifier = new Minify\CSS($this->urlToDirectory($style[2]));
			}
		}
		return $minifier;
	}

	public function cssMinify($minifier, $style, $all=false){
		if($minifier == null){
			if(strlen($style[1])>0 && $all){
				$minifier = new Minify\CSS($style[1]);
			}else{
				if($this->isLocalFile($style[2])){
					$minifier = new Minify\CSS($style[2]);
				}else if(!$this->isLocalUrl($style[2])){
					if(DrFileSupport::get_http_response_code($style[2])=="200"){
						$minifier = new Minify\CSS(@file_get_contents($style[2]));
					}
				}else if($this->isLocalUrl($style[2])){
					$minifier = new Minify\CSS($this->urlToDirectory($style[2]));
				}
			}
		}else{
			if(strlen($style[1])>0 && $all){
				$minifier->add($style[1]);
			}else{
				if($this->isLocalFile($style[2])){
					$minifier->add($style[2]);
				}else if(!$this->isLocalUrl($style[2])){
					if(DrFileSupport::get_http_response_code($style[2])=="200"){
						$minifier->add(@file_get_contents($style[2]));
					}
				}else{ 
					if($this->isLocalUrl($style[2])){
						$minifier->add($this->urlToDirectory($style[2]));
					}
				}
			}
		}
		return $minifier;
	}

	public function everythingInTags($string, $tagname){
	    $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
	    preg_match($pattern, $string, $matches);
	    return $matches[1];
	}

	public function minifyInlineJs($html){
		$inline_scripts=$this->getJSInline($html);
		foreach ($inline_scripts as $inline_script) {
			$in_tag=$this->everythingInTags($inline_script[0],'script');
			if(strlen($in_tag)>0)
			{
				$minifier = new Minify\JS($in_tag);
				$minifiedContent=$minifier->minify();
				$replace_script = str_replace( $in_tag,$minifiedContent, $inline_script[0] );
			    $replace_script = str_replace( '<script', '<script data-minify=1', $replace_script );
			    $html = str_replace( $inline_script[0], '', $html );
			    $html = str_replace( '</body>', $replace_script.'</body>', $html );
			}
		}
		return $html;
	}

	public function minifyExternalJs($html, $scope){
		$scripts = $this->getJSLinkedFiles( $html );
		$minifier = null;
		global $wp_scripts;

		$i=0;

		if(!$scripts){
			$scripts =  [];
		}else{
			foreach($scripts as $script) {
				$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				if($scope == "local"){
					if(parse_url($script[2], PHP_URL_HOST) != parse_url($actual_link, PHP_URL_HOST)){
						continue;
					}
				}else if($scope == "external"){
					if(parse_url($script[2], PHP_URL_HOST) == parse_url($actual_link, PHP_URL_HOST)){
						continue;
					}
				}
				$url = str_replace('&quot;', '', $script[2]);
				if($this->excludedJsFile($url)){
					continue;
				}
				$minifier = null;
				try{
					if($this->jsSkip($script)){
						$file_number++;
						$html = str_replace( $script[0], '', $html );
						$touched = str_replace('<script', '<script data-minify=0', $script[0]);
						$touched = str_replace('>', 'defer></script>', $touched);
						$html = str_replace('</body>', $touched."</body>",$html );
					}else{
						$i++;
						$minifier = $this->jsMinify($minifier, $script, false);
						$parsed = parse_url( $script[2] );
						$parsed = $parsed['path'];
						$parsed = explode('/', $parsed);
						$parsed = $parsed[sizeof($parsed)-1];
						$parsed = explode('.', $parsed);
						$parsed = $parsed[0];
						$minifiedPathJS = DR_PLUGIN_DIR.'/cached/js/'.DR_SLUG.'-'.$parsed.'.min.js';
						$minifier->minify($minifiedPathJS);
						$minifiedPathJS = WP_CONTENT_URL.'/plugins/'.DR_SLUG.'/cached/js/'.DR_SLUG.'-'.$parsed.'.min.js';
						$replace_script = str_replace( $script[2], $minifiedPathJS, $script[0] );
						$replace_script = str_replace( '<script', '<script data-minify=1', $replace_script );
						$replace_script = str_replace( '>', ' defer></script>', $replace_script );
						$html = str_replace( $script[0], '', $html );
						$html = str_replace( '</body>', $replace_script.'</body>', $html );
					}
				}catch(Exception $e){
					
				}
			}
		}
		return $html;
	}

	public function jsMinify($minifier, $script, $all=false){
		if($minifier == null){
			if(strlen($script[1])>0 && $all){
				$minifier = new Minify\JS($script[1]);
			}else{
				if($this->isLocalFile($script[2])){
					$minifier = new Minify\JS($script[2]);
				}else if(!$this->isLocalUrl($script[2])){
					if(DrFileSupport::get_http_response_code($script[2])=="200"){
						$minifier = new Minify\JS(@file_get_contents($script[2]));
					}
				}else if($this->isLocalUrl($script[2])){
					$minifier = new Minify\JS($this->urlToDirectory($script[2]));
				}
			}
		}else{
			if(strlen($script[1])>0 && $all){
				$minifier->add($script[1]);
			}else{
				if($this->isLocalFile($script[2])){
					$minifier->add($script[2]);
				}else if(!$this->isLocalUrl($script[2])){
					if(DrFileSupport::get_http_response_code($script[2])=="200"){
						$minifier->add(@file_get_contents($script[2]));
					}
				}else{ 
					if($this->isLocalUrl($script[2])){
						$minifier->add($this->urlToDirectory($script[2]));
					}
				}
			}
		}

		return $minifier;
	}

	public function jsSkip($script){
		$skip = false;
		if(!empty( $wp_scripts->registered['jquery-core']->src ) && false !== strpos( $script[2], $wp_scripts->registered['jquery-core']->src ) ) {
			$skip = true;
		}else if ( preg_match( '/[-.]min\.js/iU', $script[2] ) ) {
			$skip=true;
		}else if ( !file_exists($this->getAbsolutePath($script[2]))) {
			$skip=true;
		}else if ( $this->isExcludedFile( $script ) ) {
			$skip = true;
		}
		return $skip;
	}

	public function jsSave($minifier, $index, $html){
		$minifiedPathJS = DR_PLUGIN_DIR.'/cached/js/'.DR_SLUG.'_script_major_'.$index.'.min.js';
		$minifier->minify($minifiedPathJS); 
		$minifiedPathJS = WP_CONTENT_URL.'/plugins/'.DR_SLUG.'/cached/js/'.DR_SLUG.'_script_major_'.$index.'.min.js';
		$html = str_replace( '</body>', '<script data-minify=1 type="text/javascript" src="'.$minifiedPathJS.'" defer></script></body>', $html );
		return $html;
	}

}
?>