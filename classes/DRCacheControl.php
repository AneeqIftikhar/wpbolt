<?php

class DRCacheControl{
	public function drWPCacheDefine( $turn_it_on ) {
		if ( ( $turn_it_on && defined( 'WP_CACHE' ) && WP_CACHE ) ) {
			return;
		}

		$config_file_path = DRFileSupport::devrec_get_home_path(). 'wp-config.php';
		$can_write = DRFileSupport::devrec_direct_filesystem()->is_writable($config_file_path);
		if($can_write){
			$fileReader = DRFileSupport::devrec_direct_filesystem();
			$config_file = file( $config_file_path );
			$turn_it_on = $turn_it_on ? 'true' : 'false';
			$is_wp_cache_exist = false;
			$constant = "define('WP_CACHE', $turn_it_on); // Added by ". DR_FRIENDLY_NAME . "\r\n";
			foreach ( $config_file as &$line ) {
				if ( ! preg_match( '/^define\(\s*\'([A-Z_]+)\',(.*)\)/', $line, $match ) ) {
					continue;
				}

				if ( 'WP_CACHE' === $match[1] ) {
					$is_wp_cache_exist = true;
					$line              = $constant;
				}
			}
			unset( $line );
			if ( ! $is_wp_cache_exist ) {
				array_shift( $config_file );
				array_unshift( $config_file, "<?php\r\n", $constant );
			}
			$handle = @fopen( $config_file_path, 'w' );
			foreach ( $config_file as $line ) {
				@fwrite( $handle, $line );
			}

			@fclose( $handle );
			$chmod = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : 0644;
			$fileReader->chmod( $config_file_path, $chmod );
		}else{
			return;
		}
	}

	public function generateAdvancedCacheFile() {
		$advanced_cache_file_path = DRFileSupport::devrec_get_home_path() . 'wp-content/advanced-cache.php';
		$fileReader = DRFileSupport::devrec_direct_filesystem();
		$advanced_cache_file_content = '<?php' . "\n" . 'include \''.DR_PLUGIN_DIR.'classes/DRCache.php\';';
		$chmod = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : 0644;
		$fileReader->put_contents( $advanced_cache_file_path, $advanced_cache_file_content, $chmod );
	}

	public function cleanAdvancedCacheFile() {
		$advanced_cache_file_path = DRFileSupport::devrec_get_home_path() . 'wp-content/advanced-cache.php';
		$fileReader = DRFileSupport::devrec_direct_filesystem();
		$advanced_cache_file_content = '';
		$chmod = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : 0644;
		$fileReader->put_contents( $advanced_cache_file_path, $advanced_cache_file_content, $chmod );
	}

	public function get_devrec_advanced_cache_file_content() {
		$advanced_cache_file_local_path = DRFileSupport::devrec_get_home_path() . 'wp-content/plugins/'.DR_PLUGIN_DIR.'/library/advanced-cache.txt';
		$can_write = DRFileSupport::devrec_direct_filesystem()->is_writable($advanced_cache_file_local_path);
		if($can_write){
			$fileReader = DRFileSupport::devrec_direct_filesystem();
			$advanced_cache_file_local_content = $fileReader->get_contents($advanced_cache_file_local_path);
		}
		return $advanced_cache_file_local_content;
	}

	public function createCacheFile($final){
		$uri_path = $this->fileName();
		//if(wp_is_mobile()){
		//	$devrec_cache_filepath = WP_CONTENT_DIR.'/plugins/'.DR_SLUG.'/cached/mobile_'.$uri_path.'.html';
		//}else{
			$devrec_cache_filepath = WP_CONTENT_DIR.'/plugins/'.DR_SLUG.'/cached/'.$uri_path.'.html';
		//}
	    if ( ! is_user_logged_in() ) {
			$final_html_page = apply_filters('final_output', $final);
			$is_html   = false;
			if ( preg_match( '/(<\/html>)/i', $final_html_page ) ) {
				$is_html = true;
			}
			if( $is_html ){
				if(isset($_SERVER['REQUEST_URI'])){
					$fileReader = DRFileSupport::devrec_direct_filesystem();
					$chmod = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : 0644;
					$fileReader->put_contents( $devrec_cache_filepath, $final_html_page, $chmod );

					if(function_exists('gzencode')){
						if(get_magic_quotes_runtime())
						{
						    set_magic_quotes_runtime(false);
						}
						$final_html_page_gzip = gzencode($final_html_page, 9);
						$fileReader->put_contents( $devrec_cache_filepath."_gzip", $final_html_page_gzip, $chmod );
					}

				}
				echo $final_html_page.$this->drFootprint(true, true);
			}else{
				echo $final;
			}
		}else{
			echo $final;
		}
	}

	public function drFootprint( $fresh = false, $debug = true ) {
		$footprint = $fresh ?
						"\n" . '<!-- Cached for great performance by '.DR_FRIENDLY_NAME :
						"\n" . '<!-- This website is like a ThunderBolt, isn\'t it? Performance optimized by ' . DR_FRIENDLY_NAME . '. ';
		if ( $debug ) {
			$footprint .= ' - Debug: cached@' . time();
		}
		$footprint .= ' -->';
		return $footprint;
	}

	public function fileName(){
		$uri_path = $_SERVER['REQUEST_URI'];
		$uri_path = explode('?', $uri_path);
		$uri_path = reset($uri_path);
		$uri_path = explode('#', $uri_path);
		$uri_path = reset($uri_path);
		$uri_path = substr($uri_path, 1, strlen($uri_path)-2);
		$uri_path = str_replace('/', '_', $uri_path);
		if($uri_path == ""){
			$uri_path = "index";
		}
		return $uri_path;
	}
}

?>