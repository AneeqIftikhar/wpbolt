<?php
defined( 'ABSPATH' ) || die( 'Direct Access Not Allowed' );
function clean_data_cache_again($path=""){
	if($path==""){
		$files = glob(DR_CACHE_PATH.'*'); // get all file names
	}else{
		$files = glob($path); // get all file names
	}
	foreach($files as $file){ // iterate files
	  if(is_file($file)){
	    unlink($file); // delete file
	  }else if(is_dir($file)){
	  	clean_data_cache_again($file."/*");
	  }
	}
}
// Launch hooks that deletes all the cache domain.
add_action( 'wp_trash_post', 'clean_data_cache_again' );
add_action( 'delete_post', 'clean_data_cache_again' );
add_action( 'wp_update_comment_count', 'clean_data_cache_again' ); 
add_action( 'clean_post_cache', 'clean_data_cache_again' );
add_action( 'save_post', 'clean_data_cache_again' ); 
add_action( 'switch_theme', 'clean_data_cache_again' ); 
add_action( 'user_register', 'clean_data_cache_again' );
add_action( 'profile_update', 'clean_data_cache_again' );
add_action( 'deleted_user', 'clean_data_cache_again' ); 
add_action( 'wp_update_nav_menu', 'clean_data_cache_again' );
add_action( 'update_option_sidebars_widgets', 'clean_data_cache_again' );
add_action( 'update_option_category_base', 'clean_data_cache_again' );
add_action( 'update_option_tag_base', 'clean_data_cache_again' ); 
add_action( 'permalink_structure_changed', 'clean_data_cache_again' );
add_action( 'create_term', 'clean_data_cache_again' ); 
add_action( 'edited_terms', 'clean_data_cache_again' );
add_action( 'delete_term', 'clean_data_cache_again' );
add_action( 'add_link', 'clean_data_cache_again' ); 
add_action( 'edit_link', 'clean_data_cache_again' );
add_action( 'delete_link', 'clean_data_cache_again' ); 
add_action( 'customize_save', 'clean_data_cache_again' ); 
add_action( 'transition_comment_status', 'clean_data_cache_again' );
add_action( 'update_option_theme_mods_' . get_option( 'stylesheet' ), 'clean_data_cache_again' ); // When location of a menu is updated.
?>