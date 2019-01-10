<?php

class Combine_Google_Fonts {

	protected $fonts;

	protected $subsets;

	public function __construct() {
		$this->fonts   = [];
		$this->subsets = [];
	}

	public function optimize( $html ) {
		$html_nocomments = preg_replace( '/<!--(.*)-->/Uis', '', $html );
		$fonts           = $this->find( '<link(?:\s+(?:(?!href\s*=\s*)[^>])+)?(?:\s+href\s*=\s*([\'"])((?:https?:)?\/\/fonts\.googleapis\.com\/css(?:(?!\1).)+)\1)(?:\s+[^>]*)?>', $html_nocomments );

		if ( ! $fonts ) {
			return $html;
		}

		$this->parse( $fonts );

		if ( empty( $this->fonts ) ) {
			return $html;
		}

		$html = str_replace( '</title>', '</title>' . $this->get_combine_tag(), $html );

		foreach ( $fonts as $font ) {
			$html = str_replace( $font[0], '', $html );
		}

		return $html;
	}

	protected function find( $pattern, $html ) {
		preg_match_all( '/' . $pattern . '/Umsi', $html, $matches, PREG_SET_ORDER );

		if ( count( $matches ) <= 1 ) {
			return false;
		}

		return $matches;
	}

	protected function parse( $matches ) {
		foreach ( $matches as $match ) {
			$query = _get_component_from_parsed_url_array( wp_parse_url( $match[2] ), PHP_URL_QUERY);

			if ( ! isset( $query ) ) {
				return;
			}

			$query = html_entity_decode( $query );
			$font  = wp_parse_args( $query );

			// Add font to the collection.
			$this->fonts[] = rawurlencode( htmlentities( $font['family'] ) );

			// Add subset to collection.
			$this->subsets[] = isset( $font['subset'] ) ? rawurlencode( htmlentities( $font['subset'] ) ) : '';
		}

		// Concatenate fonts tag.
		$this->subsets = ( $this->subsets ) ? '&subset=' . implode( ',', array_filter( array_unique( $this->subsets ) ) ) : '';
		$this->fonts   = implode( '|', array_filter( array_unique( $this->fonts ) ) );
		$this->fonts   = str_replace( '|', '%7C', $this->fonts );
	}

	protected function get_combine_tag() {
		return '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=' . $this->fonts . $this->subsets . '" />';
	}
}
