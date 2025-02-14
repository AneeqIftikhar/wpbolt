<?php
defined( 'ABSPATH' ) || die( 'Direct Access Not Allowed' );
class DRImageOptimization{

	public $options = array();

	public function __construct($options){
		$this->options = explode(PHP_EOL, $options['exclude_image']);
	}

	public function getImages($html){
		
		$pattern = '<img.*>';
	    preg_match_all( '/' . $pattern . '/Umsi', $html, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return false;
		}
	    return $matches;
	}

	public function getInTagBgImages($html){
		$pattern = '<[^>]*style=[\'"]?[^\'"]*(url\(([^\'" ]*)\)[\'"]?)[^>]*>';
	    preg_match_all( '/' . $pattern . '/Umsi', $html, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return false;
		}
	    return $matches;
	}

	public function fileName($url){
		$path      = parse_url($url, PHP_URL_PATH);
		$filename  = pathinfo($path, PATHINFO_FILENAME);
		return $filename;
	}

	public function excludedImage($url){
		$image = $this->fileName($url);
		for($i=0; $i<sizeof($this->options); $i++){
			if($image == $this->options[$i]){
				return true;
			}
		}
		return false;
	}



	public function lazyLoadInTagBackgroundImages($html){
		$elements = $this->getInTagBgImages($html);
		if($elements){
			foreach($elements as $element) {
				$url = str_replace('&quot;', '', $element[2]);
				if($this->excludedImage($url)){
					continue;
				}
		    	$mod_elem = str_replace($element[1], 'url(data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=)', $element[0]);
				$mod_elem = str_replace("style", 'data-itbgurl="'.$url.'" style', $mod_elem);
	    		$html = str_replace( $element[0], $mod_elem, $html );
			}
		}
		$lazyScript = '
		<script>
			itbg_refresh_handler = function(e) {
	        var itbg_elements = document.querySelectorAll("*[data-itbgurl]");
	        for (var i = 0; i < itbg_elements.length; i++) {
	                var boundingClientRect = itbg_elements[i].getBoundingClientRect();
	                if (itbg_elements[i].hasAttribute("data-itbgurl") && boundingClientRect.top < window.innerHeight) {
						itbg_elements[i].style.backgroundImage = "url("+ itbg_elements[i].getAttribute("data-itbgurl") +")"
	                    itbg_elements[i].removeAttribute("data-itbgurl");
	                }
	            }
	        };

	        window.addEventListener("scroll", itbg_refresh_handler);
	        window.addEventListener("load", itbg_refresh_handler);
	        window.addEventListener("resize", itbg_refresh_handler);
		</script>';

		//$lazyScript = "<script>document.addEventListener('DOMContentLoaded', function(event) {lazyLoad()}); function lazyLoad(){[].forEach.call(document.querySelectorAll('img[data-src]'),function(img){img.setAttribute('src',img.getAttribute('data-src'));img.onload= function(){img.removeAttribute('data-src');};});}</style>";
		$html = str_replace( '</body>', $lazyScript.'</body>', $html );
		return $html;
	}
	public function lazyLoadBackgroundImages($html){
		$regex = "([.#][a-zA-Z0-9_-]*?){[a-zA-Z0-9~;:#! _-]*\(['\"]?(http:.*?)['\"]?\)[a-zA-Z0-9~;:#! _-]*}";
		preg_match_all( '/'.$regex.'/' , $html, $output );
		$filtered = [];
		if(sizeof($output)>0){
			for($i=0; $i<sizeof($output[0]); $i++){
				$str = str_replace($output[2][$i], '', $output[0][$i]);
				$html = str_replace( $image[0][$i], $str, $html );
			}
		}
		$lazyScript = '
		<script>
			var bgLazyLoadClasses = '.json_encode($output[1]).';
			var bgLazyLoadImages = '.json_encode($output[2]).';
			bg_refresh_handler = function(e) {
	        var elements = document.querySelectorAll("*[data-bgsrc]");
	        for (var i = 0; i < elements.length; i++) {
	                var boundingClientRect = elements[i].getBoundingClientRect();
	                if (elements[i].hasAttribute("data-bgsrc") && boundingClientRect.top < window.innerHeight) {
	                    elements[i].setAttribute("src", elements[i].getAttribute("data-bgsrc"));
	                    elements[i].removeAttribute("data-bgsrc");
	                }
	            }
	        };

	        window.addEventListener("scroll", bg_refresh_handler);
	        window.addEventListener("load", bg_refresh_handler);
	        window.addEventListener("resize", bg_refresh_handler);
		</script>';
		$html = str_replace( '</body>', $lazyScript.'</body>', $html );
		return $html;
	}

	public function lazyloadImages($html){
		$images = $this->getImages($html);
		if($images){
			foreach($images as $image) {
				
		    	$mod_img = str_replace('src=', 'src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" data-src=', $image[0]);
		    	if(!is_ssl()){
					$mod_img = str_replace('https', 'http', $mod_img);
				}
	    		$html = str_replace( $image[0], $mod_img, $html );
			}
		}
		//$lazyScript = "<script>[].forEach.call(document.querySelectorAll('img[data-src]'),function(img){img.setAttribute('src',img.getAttribute('data-src'));img.onload= function(){img.removeAttribute('data-src');};});</script><style>img{opacity: 1;transition: opacity 0.3s;} img[data-src]{opacity: 0;}</style>";

		$lazyScript = '
		<script>
			refresh_handler = function(e) {
	        var elements = document.querySelectorAll("*[data-src]");
	        for (var i = 0; i < elements.length; i++) {
	                var boundingClientRect = elements[i].getBoundingClientRect();
	                if (elements[i].hasAttribute("data-src") && boundingClientRect.top < window.innerHeight) {
	                    elements[i].setAttribute("src", elements[i].getAttribute("data-src"));
	                    elements[i].removeAttribute("data-src");
	                }
	            }
	        };

	        window.addEventListener("scroll", refresh_handler);
	        window.addEventListener("load", refresh_handler);
	        window.addEventListener("resize", refresh_handler);
		</script>';

		//$lazyScript = "<script>document.addEventListener('DOMContentLoaded', function(event) {lazyLoad()}); function lazyLoad(){[].forEach.call(document.querySelectorAll('img[data-src]'),function(img){img.setAttribute('src',img.getAttribute('data-src'));img.onload= function(){img.removeAttribute('data-src');};});}</style>";
		$html = str_replace( '</body>', $lazyScript.'</body>', $html );
		return $html;
	}

	//(?:\(['"]?)(.*?)(?:['"]?\))
	//[(.|#)][a-zA-Z0-9_-]*?\{.*?(?:\(['"]?)(.*?)(?:['"]?\))?.*?\}
	//([.#][a-zA-Z0-9_-]*?){.*?\(['"]?(.*?)['"]?\).*?}?
	//([.#][a-zA-Z0-9_-]*?){[a-zA-Z0-9 :!~#_-]*?\(['"]?(.*?)['"]?\)[a-zA-Z0-9 :!~#_-]*?}

	public function specifyImageDimensions( $html ) {
		preg_match_all( '/<img(?:[^>](?!(height|width)=))*+>/i' , $html, $images_match );		
		foreach ( $images_match[0] as $image ) {
			$tmp = $image;
			preg_match( '/src=[\'"]([^\'"]+)/', $image, $src_match );
			$image_url = wp_parse_url( $src_match[1] );
			if ( empty( $image_url['host'] ) || $this->removeUrlProtocol( home_url() ) === $image_url['host'] ) {
				$sizes = getimagesize( ABSPATH . $image_url['path'] );
			} else {
				if ( ini_get( 'allow_url_fopen' ) ) {
					$sizes = getimagesize( $image_url['scheme'] . '://' . $image_url['host'] . $image_url['path'] );
				}
			}
			if ( ! empty( $sizes ) ) {
				$image = str_replace( '<img', '<img ' . $sizes[3], $image );
				$html = str_replace( $tmp, $image, $html );
			}
		}
		return $html;
	}

	public function removeUrlProtocol( $url ) {
		$url = str_replace( array( 'http://', 'https://' ), '', $url );
		return $url;
	}

}

?>