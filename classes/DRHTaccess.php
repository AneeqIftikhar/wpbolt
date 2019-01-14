<?php
defined( 'ABSPATH' ) || die( 'Direct Access Not Allowed' );
class DRHtaccess{

	public function buildHtAccess( $force = false ) {
		global $is_apache;

		if ( ! $is_apache ) {
			return;
		}

		$htaccess_file_path = DRFileSupport::devrec_get_home_path(). '.htaccess';
		$can_write = DRFileSupport::devrec_direct_filesystem()->is_writable($htaccess_file_path);
		if($can_write){
			$fileReader = DRFileSupport::devrec_direct_filesystem();
			$htaccess_file_content = $fileReader->get_contents($htaccess_file_path);
			$htaccess_file_content = preg_replace( '/# BEGIN STORM BOLT(.*)# END STORM BOLT/isU', '', $htaccess_file_content );

			$htaccess_rules = $this->devrec_get_htaccess_rules();
			
			$chmod = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : 0644;
			$fileReader->put_contents( $htaccess_file_path, $htaccess_rules . $htaccess_file_content, $chmod );
		}
	}

	public function cleanHtAccess(){
		global $is_apache;

		if ( ! $is_apache ) {
			return;
		}
		$htaccess_file_path = DRFileSupport::devrec_get_home_path(). '.htaccess';
		$can_write = DRFileSupport::devrec_direct_filesystem()->is_writable($htaccess_file_path);
		if($can_write){
			$fileReader = DRFileSupport::devrec_direct_filesystem();
			$htaccess_file_content = $fileReader->get_contents($htaccess_file_path);
			$htaccess_file_content = preg_replace( '/# BEGIN STORM BOLT(.*)# END STORM BOLT/isU', '', $htaccess_file_content );
			$chmod = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : 0644;
			$fileReader->put_contents( $htaccess_file_path, $htaccess_file_content, $chmod );
		}
	}

	public function devrec_get_htaccess_rules() {
		$marker  = '# BEGIN STORM BOLT ' . PHP_EOL;
		$marker .= $this->devrec_get_htaccess_charset();
		$marker .= $this->devrec_get_htaccess_etag();
		$marker .= $this->devrec_get_htaccess_web_fonts_access();
		$marker .= $this->devrec_get_htaccess_files_match();
		$marker .= $this->devrec_get_htaccess_mod_expires();
		$marker .= $this->devrec_get_htaccess_mod_deflate();
		$marker .= $this->devrec_get_htaccess_mod_rewrite();
		$marker .= '# END STORM BOLT' . PHP_EOL;
		return $marker;
	}

	public function devrec_get_htaccess_mod_rewrite() {
		if ( is_multisite() ) {
			return;
		}

		$home_root = _get_component_from_parsed_url_array( wp_parse_url( home_url() ), PHP_URL_PATH );
		$home_root = isset( $home_root ) ? trailingslashit( $home_root ) : '/';

		$site_root = _get_component_from_parsed_url_array( wp_parse_url( site_url() ), PHP_URL_PATH );
		$site_root = isset( $site_root ) ? trailingslashit( $site_root ) : '';

		if ( strpos( ABSPATH, DRSB_CACHE_PATH ) === false ) {
			$cache_root = str_replace( $_SERVER['DOCUMENT_ROOT'] , '', DRSB_CACHE_PATH );
		} else {
			$cache_root = $site_root . str_replace( ABSPATH, '', DRSB_CACHE_PATH );
		}
		$http_host = apply_filters( 'devrec_url_no_dots', false ) ? devrec_remove_url_protocol( home_url() ) : '%{HTTP_HOST}';
		$is_1and1_or_force = apply_filters( 'devrec_force_full_path', strpos( $_SERVER['DOCUMENT_ROOT'], '/johnny/' ) === 0 );

		$rules = '';
		$gzip_rules = '';
		$enc = '';
		if ( function_exists( 'gzencode' ) && apply_filters( 'devrec_force_gzip_htaccess_rules', true ) ) {
			$rules = '<IfModule mod_mime.c>' . PHP_EOL;
			$rules .= 'AddType text/html .html_gzip' . PHP_EOL;
			$rules .= 'AddEncoding gzip .html_gzip' . PHP_EOL;
			$rules .= '</IfModule>' . PHP_EOL;
			$rules .= '<IfModule mod_setenvif.c>' . PHP_EOL;
			$rules .= 'SetEnvIfNoCase Request_URI \.html_gzip$ no-gzip' . PHP_EOL;
			$rules .= '</IfModule>' . PHP_EOL . PHP_EOL;

			$gzip_rules .= 'RewriteCond %{HTTP:Accept-Encoding} gzip' . PHP_EOL;
			$gzip_rules .= 'RewriteRule .* - [E=WPR_ENC:_gzip]' . PHP_EOL;

			$enc = '%{ENV:WPR_ENC}';
		}

		$rules .= '<IfModule mod_rewrite.c>' . PHP_EOL;
		$rules .= 'RewriteEngine On' . PHP_EOL;
		$rules .= 'RewriteBase ' . $home_root . PHP_EOL;
		$rules .= $this->devrec_get_htaccess_ssl_rewritecond();
		$rules .= $gzip_rules;
		$rules .= 'RewriteCond %{REQUEST_METHOD} GET' . PHP_EOL;
		$rules .= 'RewriteCond %{QUERY_STRING} =""' . PHP_EOL;

		$rules .= $this->devrec_get_htaccess_mobile_rewritecond();
		if ( $is_1and1_or_force ) {
			$rules .= 'RewriteCond "' . str_replace( '/kunden/', '/', DRSB_CACHE_PATH ) . $http_host . '%{REQUEST_URI}/index%{ENV:WPR_SSL}.html' . $enc . '" -f' . PHP_EOL;
		} else {
			$rules .= 'RewriteCond "%{DOCUMENT_ROOT}/' . ltrim( $cache_root, '/' ) . $http_host . '%{REQUEST_URI}/index%{ENV:WPR_SSL}.html' . $enc . '" -f' . PHP_EOL;
		}
		$rules .= 'RewriteRule .* "' . $cache_root . $http_host . '%{REQUEST_URI}/index%{ENV:WPR_SSL}.html' . $enc . '" [L]' . PHP_EOL;
		$rules .= '</IfModule>' . PHP_EOL;
		return $rules;
	}

	public function devrec_remove_url_protocol( $url, $no_dots = false ) {
		return str_replace( '.', '_', str_replace( array( 'http://', 'https://' ), '', $url ));
	}

	public function devrec_get_htaccess_mobile_rewritecond() {
		if ( is_multisite() ) {
			return;
		}
		$htaccess_rules = 'RewriteCond %{HTTP:X-Wap-Profile} !^[a-z0-9\"]+ [NC]' . PHP_EOL;
		$htaccess_rules .= 'RewriteCond %{HTTP:Profile} !^[a-z0-9\"]+ [NC]' . PHP_EOL;
		$htaccess_rules .= 'RewriteCond %{HTTP_USER_AGENT} !^.*(2.0\ MMP|240x320|400X240|AvantGo|BlackBerry|Blazer|Cellphone|Danger|DoCoMo|Elaine/3.0|EudoraWeb|Googlebot-Mobile|hiptop|IEMobile|KYOCERA/WX310K|LG/U990|MIDP-2.|MMEF20|MOT-V|NetFront|Newt|Nintendo\ Wii|Nitro|Nokia|Opera\ Mini|Palm|PlayStation\ Portable|portalmmm|Proxinet|ProxiNet|SHARP-TQ-GX10|SHG-i900|Small|SonyEricsson|Symbian\ OS|SymbianOS|TS21i-10|UP.Browser|UP.Link|webOS|Windows\ CE|WinWAP|YahooSeeker/M1A1-R2D2|iPhone|iPod|Android|BlackBerry9530|LG-TU915\ Obigo|LGE\ VX|webOS|Nokia5800).* [NC]' . PHP_EOL;
		$htaccess_rules .= 'RewriteCond %{HTTP_USER_AGENT} !^(w3c\ |w3c-|acs-|alav|alca|amoi|audi|avan|benq|bird|blac|blaz|brew|cell|cldc|cmd-|dang|doco|eric|hipt|htc_|inno|ipaq|ipod|jigs|kddi|keji|leno|lg-c|lg-d|lg-g|lge-|lg/u|maui|maxo|midp|mits|mmef|mobi|mot-|moto|mwbp|nec-|newt|noki|palm|pana|pant|phil|play|port|prox|qwap|sage|sams|sany|sch-|sec-|send|seri|sgh-|shar|sie-|siem|smal|smar|sony|sph-|symb|t-mo|teli|tim-|tosh|tsm-|upg1|upsi|vk-v|voda|wap-|wapa|wapi|wapp|wapr|webc|winw|winw|xda\ |xda-).* [NC]' . PHP_EOL;
		return $htaccess_rules;
	}

	public function devrec_get_htaccess_ssl_rewritecond() {
		$rules  = 'RewriteCond %{HTTPS} on [OR]' . PHP_EOL;
		$rules .= 'RewriteCond %{SERVER_PORT} ^443$ [OR]' . PHP_EOL;
		$rules .= 'RewriteCond %{HTTP:X-Forwarded-Proto} https' . PHP_EOL;
		$rules .= 'RewriteRule .* - [E=WPR_SSL:-https]' . PHP_EOL;
		return $rules;
	}

	public function devrec_get_htaccess_mod_deflate() {
		$htaccess_rules .= '<IfModule mod_deflate.c>' . PHP_EOL;
		$htaccess_rules .= 'SetOutputFilter DEFLATE' . PHP_EOL;
		$htaccess_rules .= '<IfModule mod_setenvif.c>' . PHP_EOL;
		$htaccess_rules .= '<IfModule mod_headers.c>' . PHP_EOL;
		$htaccess_rules .= 'SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$ ^((gzip|deflate)\s*,?\s*)+|[X~-]{4,13}$ HAVE_Accept-Encoding' . PHP_EOL;
		$htaccess_rules .= 'RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding' . PHP_EOL;
		$htaccess_rules .= 'SetEnvIfNoCase Request_URI \\' . PHP_EOL;
		$htaccess_rules .= '\\.(?:gif|jpe?g|png|rar|zip|exe|flv|mov|wma|mp3|avi|swf|mp?g|mp4|webm|webp|pdf)$ no-gzip dont-vary' . PHP_EOL;
		$htaccess_rules .= '</IfModule>' . PHP_EOL;
		$htaccess_rules .= '</IfModule>' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= '<IfModule mod_filter.c>' . PHP_EOL;
		$htaccess_rules .= 'AddOutputFilterByType DEFLATE application/atom+xml \
		                          application/javascript \
		                          application/json \
		                          application/rss+xml \
		                          application/vnd.ms-fontobject \
		                          application/x-font-ttf \
		                          application/xhtml+xml \
		                          application/xml \
		                          font/opentype \
		                          image/svg+xml \
		                          image/x-icon \
		                          text/css \
		                          text/html \
		                          text/plain \
		                          text/x-component \
		                          text/xml' . PHP_EOL;
		$htaccess_rules .= '</IfModule>' . PHP_EOL;
		$htaccess_rules .= '<IfModule mod_headers.c>' . PHP_EOL;
		$htaccess_rules .= 'Header append Vary: Accept-Encoding' . PHP_EOL;
	   	$htaccess_rules .= '</IfModule>' . PHP_EOL;
		$htaccess_rules .= '</IfModule>' . PHP_EOL . PHP_EOL;
		return $htaccess_rules;
	}

	public function devrec_get_htaccess_mod_expires() {
		$ebt = "ExpiresByType";
		$htaccess_rules .= '<IfModule mod_expires.c>' . PHP_EOL;
		$htaccess_rules .= 'ExpiresActive on' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= 'ExpiresDefault "' . $this->accessTime(1, "month") . '"' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= $ebt. ' text/cache-manifest "' . $this->accessTime(0, "seconds") . '"' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= $ebt. ' text/html "' . $this->accessTime(0, "seconds") . '"' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= $ebt. ' text/xml "' . $this->accessTime(0, "seconds") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' application/xml "' . $this->accessTime(0, "seconds") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' application/json "' . $this->accessTime(0, "seconds") . '"' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= $ebt. ' application/rss+xml "' . $this->accessTime(1, "hour") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' application/atom+xml "' . $this->accessTime(1, "hour") . '"' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= $ebt. ' image/x-icon "' . $this->accessTime(1, "week") . '"' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= $ebt. ' image/gif "' . $this->accessTime(1, "month") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' image/png "' . $this->accessTime(1, "month") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' image/jpeg "' . $this->accessTime(1, "month") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' video/ogg "' . $this->accessTime(1, "month") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' audio/ogg "' . $this->accessTime(1, "month") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' video/mp4 "' . $this->accessTime(1, "month") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' video/webm "' . $this->accessTime(1, "month") . '"' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= $ebt. ' text/x-component "' . $this->accessTime(1, "month") . '"' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= $ebt. ' application/x-font-ttf "' . $this->accessTime(1, "month") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' font/opentype "' . $this->accessTime(1, "month") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' application/x-font-woff "' . $this->accessTime(1, "month") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' application/x-font-woff2 "' . $this->accessTime(1, "month") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' image/svg+xml "' . $this->accessTime(1, "month") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' application/vnd.ms-fontobject "' . $this->accessTime(1, "month") . '"' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= $ebt. ' text/css "' . $this->accessTime(1, "year") . '"' . PHP_EOL;
		$htaccess_rules .= $ebt. ' application/javascript "' . $this->accessTime(1, "year") . '"' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= '</IfModule>' . PHP_EOL . PHP_EOL;
		return $htaccess_rules;
	}

	public function accessTime($value, $unit){
		return "access plus " . $value . " " . $unit;
	}

	public function devrec_get_htaccess_charset() {
		$char_set = preg_replace( '/[^a-zA-Z0-9_\-\.:]+/', '', get_bloginfo( 'charset', 'display' ) );
		$htaccess_rules .= "AddDefaultCharset $char_set" . PHP_EOL;
		$htaccess_rules .= '<IfModule mod_mime.c>' . PHP_EOL;
		$htaccess_rules .= "AddCharset $char_set .atom .css .js .json .rss .vtt .xml" . PHP_EOL;
		$htaccess_rules .= '</IfModule>' . PHP_EOL . PHP_EOL;
		return $htaccess_rules;
	}

	public function devrec_get_htaccess_files_match() {
		$htaccess_rules = '<IfModule mod_alias.c>' . PHP_EOL;
		$htaccess_rules .= '<FilesMatch "\.(html|htm|rtf|rtx|txt|xsd|xsl|xml)$">' . PHP_EOL;
		$htaccess_rules .= '<IfModule mod_headers.c>' . PHP_EOL;
		$htaccess_rules .= 'Header set X-Powered-By "' . DRSB_NAME . '"' . PHP_EOL;
		$htaccess_rules .= 'Header unset Pragma' . PHP_EOL;
		$htaccess_rules .= 'Header append Cache-Control "public"' . PHP_EOL;
		$htaccess_rules .= 'Header unset Last-Modified' . PHP_EOL;
		$htaccess_rules .= '</IfModule>' . PHP_EOL;
		$htaccess_rules .= '</FilesMatch>' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= '<FilesMatch "\.(css|htc|js|asf|asx|wax|wmv|wmx|avi|bmp|class|divx|doc|docx|eot|exe|gif|gz|gzip|ico|jpg|jpeg|jpe|json|mdb|mid|midi|mov|qt|mp3|m4a|mp4|m4v|mpeg|mpg|mpe|mpp|otf|odb|odc|odf|odg|odp|ods|odt|ogg|pdf|png|pot|pps|ppt|pptx|ra|ram|svg|svgz|swf|tar|tif|tiff|ttf|ttc|wav|wma|wri|xla|xls|xlsx|xlt|xlw|zip)$">' . PHP_EOL;
		$htaccess_rules .= '<IfModule mod_headers.c>' . PHP_EOL;
		$htaccess_rules .= 'Header unset Pragma' . PHP_EOL;
		$htaccess_rules .= 'Header append Cache-Control "public"' . PHP_EOL;
		$htaccess_rules .= '</IfModule>' . PHP_EOL;
		$htaccess_rules .= '</FilesMatch>' . PHP_EOL;
		$htaccess_rules .= '</IfModule>' . PHP_EOL . PHP_EOL;
		return $htaccess_rules;
	}

	public function devrec_get_htaccess_etag() {
		$htaccess_rules .= '<IfModule mod_headers.c>' . PHP_EOL;
		$htaccess_rules .= 'Header unset ETag' . PHP_EOL;
		$htaccess_rules .= '</IfModule>' . PHP_EOL . PHP_EOL;
		$htaccess_rules .= 'FileETag None' . PHP_EOL . PHP_EOL;
		return $htaccess_rules;
	}

	public function devrec_get_htaccess_web_fonts_access() {
		//if ( ! get_rocket_option( 'cdn', false ) ) {
		//	return;
		//}
		$htaccess_rules  .= '<IfModule mod_setenvif.c>' . PHP_EOL;
		$htaccess_rules  .= '<IfModule mod_headers.c>' . PHP_EOL;
		$htaccess_rules  .= '<FilesMatch "\.(cur|gif|png|jpe?g|svgz?|ico|webp)$">' . PHP_EOL;
		$htaccess_rules  .= 'SetEnvIf Origin ":" IS_CORS' . PHP_EOL;
		$htaccess_rules  .= 'Header set Access-Control-Allow-Origin "*" env=IS_CORS' . PHP_EOL;
		$htaccess_rules  .= '</FilesMatch>' . PHP_EOL;
		$htaccess_rules  .= '</IfModule>' . PHP_EOL;
		$htaccess_rules  .= '</IfModule>' . PHP_EOL . PHP_EOL;
		$htaccess_rules  .= '<FilesMatch "\.(eot|otf|tt[cf]|woff2?)$">' . PHP_EOL;
		$htaccess_rules  .= '<IfModule mod_headers.c>' . PHP_EOL;
		$htaccess_rules  .= 'Header set Access-Control-Allow-Origin "*"' . PHP_EOL;
		$htaccess_rules  .= '</IfModule>' . PHP_EOL;
		$htaccess_rules  .= '</FilesMatch>' . PHP_EOL . PHP_EOL;
		return $htaccess_rules;
	}

}