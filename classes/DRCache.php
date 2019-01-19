<?php
	defined( 'ABSPATH' ) || die( 'Direct Access Not Allowed' );
	$do_ignore = false;

	if( strstr($_SERVER['REQUEST_URI'], 'robots.txt') || strstr($_SERVER['REQUEST_URI'], '.htaccess') ){
		$do_ignore = true;
	}

	$request_uri = explode( '?', $_SERVER['REQUEST_URI'] );
	$request_uri = reset( ( $request_uri ) );

	if(in_array(pathinfo($request_uri)['extension'], array( 'xml', 'xsl', 'ttf', 'wof', 'woff', 'eof', 'woff2', 'js', 'css', 'tt', 'pdf', 'ma', 'txt', 'svg'))){
		$do_ignore = true;
	}

	if ( strtolower( $_SERVER['REQUEST_URI'] ) !== '/index.php' && in_array( pathinfo( $request_uri, PATHINFO_EXTENSION ), array( 'php', 'xml', 'xsl' , 'ttf', 'wof', 'woff', 'eof', 'woff2', 'js', 'css', 'tt', 'pdf', 'ma', 'txt', 'svg'), true ) ) {
		$do_ignore = true;
	}

	if ( is_admin() ) {
		$do_ignore = true;
	}

	if ( strpos($_SERVER['HTTP_COOKIE'], 'wordpress_logged_in') ) {
		$do_ignore = true;
	}


	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$do_ignore = true;
	}

	if ( isset( $_POST['wp_customize'] ) ) {
		$do_ignore = true;
	}

	if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'GET' !== $_SERVER['REQUEST_METHOD'] ) {
		$do_ignore = true;
	}

	if ( ! empty( $_GET )
		&& ( ! isset( $_GET['utm_source'], $_GET['utm_medium'], $_GET['utm_campaign'] ) )
		&& ( ! isset( $_GET['utm_expid'] ) )
		&& ( ! isset( $_GET['fb_action_ids'], $_GET['fb_action_types'], $_GET['fb_source'] ) )
		&& ( ! isset( $_GET['gclid'] ) )
		&& ( ! isset( $_GET['permalink_name'] ) )
		&& ( ! isset( $_GET['lp-variation-id'] ) )
		&& ( ! isset( $_GET['lang'] ) )
		&& ( ! isset( $_GET['s'] ) )
		&& ( ! isset( $_GET['age-verified'] ) )
		&& ( ! isset( $_GET['ao_noptimize'] ) )
		&& ( ! isset( $_GET['usqp'] ) )
	){
		$do_ignore = true;
	}


	if(!$do_ignore){
		$uri_path = fileName();
		$devrec_cache_filepath = WP_CONTENT_DIR.'/plugins/wp-bolt/cached/'.$uri_path.'.html';
		devrec_serve_cache_file( $devrec_cache_filepath );
	}
	

	function devrec_serve_cache_file( $devrec_cache_filepath ) {
		$devrec_cache_filepath_gzip = $devrec_cache_filepath . '_gzip';
		if ( isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) && false !== strpos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) && file_exists( $devrec_cache_filepath_gzip ) && is_readable( $devrec_cache_filepath_gzip ) ) {
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $devrec_cache_filepath_gzip ) ) . ' GMT' );

			// Getting If-Modified-Since headers sent by the client.
			if ( function_exists( 'apache_request_headers' ) ) {
				$headers                = apache_request_headers();
				$http_if_modified_since = ( isset( $headers['If-Modified-Since'] ) ) ? $headers['If-Modified-Since'] : '';
			} else {
				$http_if_modified_since = ( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : '';
			}

			// Checking if the client is validating his cache and if it is current.
			if ( $http_if_modified_since && ( strtotime( $http_if_modified_since ) === @filemtime( $devrec_cache_filepath_gzip ) ) ) {
				// Client's cache is current, so we just respond '304 Not Modified'.
				header( $_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true, 304 );
				exit;
			}

			// Serve the cache if file isn't store in the client browser cache.
			readgzfile( $devrec_cache_filepath_gzip );
			exit;
		} else if ( file_exists( $devrec_cache_filepath ) && is_readable( $devrec_cache_filepath ) ) {
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $devrec_cache_filepath ) ) . ' GMT' );

			// Getting If-Modified-Since headers sent by the client.
			if ( function_exists( 'apache_request_headers' ) ) {
				$headers                = apache_request_headers();
				$http_if_modified_since = ( isset( $headers['If-Modified-Since'] ) ) ? $headers['If-Modified-Since'] : '';
			} else {
				$http_if_modified_since = ( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : '';
			}

			// Checking if the client is validating his cache and if it is current.
			if ( $http_if_modified_since && ( strtotime( $http_if_modified_since ) === @filemtime( $devrec_cache_filepath ) ) ) {
				// Client's cache is current, so we just respond '304 Not Modified'.
				header( $_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true, 304 );
				exit;
			}

			// Serve the cache if file isn't store in the client browser cache.
			readfile( $devrec_cache_filepath );
			exit;
		}	
	}

	function fileName(){
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

	
?>