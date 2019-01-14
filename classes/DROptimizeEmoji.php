<?php
defined( 'ABSPATH' ) || die( 'Direct Access Not Allowed' );
function drConvertEmoji( $text ) {
	global $wp_smiliessearch;

	if ( ! get_option( 'use_smilies' ) || empty( $wp_smiliessearch ) ) {
		return $text;
	}
	$output = '';
	$textarr = preg_split( '/(<.*>)/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
	$stop    = count( $textarr );
	$tags_to_ignore       = 'code|pre|style|script|textarea';
	$ignore_block_element = '';

	for ( $i = 0; $i < $stop; $i++ ) {
		$content = $textarr[ $i ];
		if ( '' === $ignore_block_element && preg_match( '/^<(' . $tags_to_ignore . ')>/', $content, $matches ) ) {
			$ignore_block_element = $matches[1];
		}
		if ( '' === $ignore_block_element && strlen( $content ) > 0 && '<' !== $content[0] ) {
			$content = preg_replace_callback( $wp_smiliessearch, 'drTranslateEmoji', $content );
		}

		if ( '' !== $ignore_block_element && '</' . $ignore_block_element . '>' === $content ) {
			$ignore_block_element = '';
		}
		$output .= $content;
	}
	return $output;
}

function drTranslateEmoji( $matches ) {
	global $wpsmiliestrans;
	if ( count( $matches ) === 0 ) {
		return '';
	}
	$smiley = trim( reset( $matches ) );
	$img    = $wpsmiliestrans[ $smiley ];
	$matches    = array();
	$ext        = preg_match( '/\.([^.]+)$/', $img, $matches ) ? strtolower( $matches[1] ) : false;
	$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
	if ( ! in_array( $ext, $image_exts, true ) ) {
		return $img;
	}
	$src_url = apply_filters( 'smilies_src', includes_url( "images/smilies/$img" ), $img, site_url() );
	if ( is_feed() || is_preview() ) {
		return sprintf( ' <img src="%s" alt="%s" class="wp-smiley" /> ', esc_url( $src_url ), esc_attr( $smiley ) );
	}
	$placeholder = apply_filters( 'dr_lazyload_placeholder', 'data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=' );
	return sprintf( ' <img src="%s" data-lazy-src="%s" alt="%s" class="wp-smiley" /> ', $placeholder, esc_url( $src_url ), esc_attr( $smiley ) );
}

function drLazyloadSmilies() {	
	remove_filter( 'the_content', 'convert_smilies' );
	remove_filter( 'the_excerpt', 'convert_smilies' );
	remove_filter( 'comment_text', 'convert_smilies', 20 );

	add_filter( 'the_content', 'drConvertEmoji' );
	add_filter( 'the_excerpt', 'drConvertEmoji' );
	add_filter( 'comment_text', 'drConvertEmoji', 20 );
}
add_action( 'init', 'drLazyloadSmilies' );
?>