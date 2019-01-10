<?php

function clean_data_cache_again(){
	$files = glob(DR_CACHE_PATH.'*'); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file))
	    unlink($file); // delete file
	}
}
// Launch hooks that deletes all the cache domain.
add_action( 'wp_trash_post', 'clean_data_cache_again' );  // When user change theme.
add_action( 'delete_post', 'clean_data_cache_again' );  // When user change theme.
add_action( 'wp_update_comment_count', 'clean_data_cache_again' );  // When user change theme.
add_action( 'clean_post_cache', 'clean_data_cache_again' );  // When user change theme.
add_action( 'save_post', 'clean_data_cache_again' );  // When user change theme.
add_action( 'switch_theme', 'clean_data_cache_again' );  // When user change theme.
add_action( 'user_register', 'clean_data_cache_again' );  // When a user is added.
add_action( 'profile_update', 'clean_data_cache_again' );  // When a user is updated.
add_action( 'deleted_user', 'clean_data_cache_again' );  // When a user is deleted.
add_action( 'wp_update_nav_menu', 'clean_data_cache_again' );  // When a custom menu is update.
add_action( 'update_option_sidebars_widgets', 'clean_data_cache_again' );  // When you change the order of widgets.
add_action( 'update_option_category_base', 'clean_data_cache_again' );  // When category permalink prefix is update.
add_action( 'update_option_tag_base', 'clean_data_cache_again' );  // When tag permalink prefix is update.
add_action( 'permalink_structure_changed', 'clean_data_cache_again' );  // When permalink structure is update.
add_action( 'create_term', 'clean_data_cache_again' );  // When a term is created.
add_action( 'edited_terms', 'clean_data_cache_again' );  // When a term is updated.
add_action( 'delete_term', 'clean_data_cache_again' );  // When a term is deleted.
add_action( 'add_link', 'clean_data_cache_again' );  // When a link is added.
add_action( 'edit_link', 'clean_data_cache_again' );  // When a link is updated.
add_action( 'delete_link', 'clean_data_cache_again' );  // When a link is deleted.
add_action( 'customize_save', 'clean_data_cache_again' );  // When customizer is saved.
add_action( 'update_option_theme_mods_' . get_option( 'stylesheet' ), 'clean_data_cache_again' ); // When location of a menu is updated.
?>