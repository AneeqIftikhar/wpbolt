<?php
defined( 'ABSPATH' ) || die( 'Direct Access Not Allowed' );
class DRFileSupport{

	public static function devrec_get_home_path() {
		_deprecated_function( __FUNCTION__, '3.0' );
		$home      = set_url_scheme( get_option( 'home' ), 'http' );
		$siteurl   = set_url_scheme( get_option( 'siteurl' ), 'http' );
		$home_path = ABSPATH;

		if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
			$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
			$pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
			$home_path           = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
			$home_path           = trailingslashit( $home_path );
		}

		return str_replace( '\\', '/', $home_path );
	}

	public static function devrec_direct_filesystem() {
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
		return new WP_Filesystem_Direct( new StdClass() );
	}

}
?>