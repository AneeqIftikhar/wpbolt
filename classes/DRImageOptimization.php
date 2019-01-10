<?php

class DRImageOptimization{

	public function getImages($html){
		
		$pattern = '<img.*>';
	    preg_match_all( '/' . $pattern . '/Umsi', $html, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return false;
		}
	    return $matches;
	}

	public function lazyloadImages($html){
		$images = $this->getImages($html);
		if($images){
			foreach($images as $image) {
				
		    	$mod_img = str_replace('src=', 'class="lazyload" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" data-src=', $image[0]);
		    	if(!is_ssl()){
					$mod_img = str_replace('https', 'http', $mod_img);
				}
	    		$html = str_replace( $image[0], $mod_img, $html );
			}
		}
		$lazyScript = "<script>[].forEach.call(document.querySelectorAll('img[data-src]'),function(img){img.setAttribute('src',img.getAttribute('data-src'));img.onload= function(){img.removeAttribute('data-src');};});</script><style>img{opacity: 1;transition: opacity 0.3s;} img[data-src]{opacity: 0;}</style>";

		//$lazyScript = "<script>document.addEventListener('DOMContentLoaded', function(event) {lazyLoad()}); function lazyLoad(){[].forEach.call(document.querySelectorAll('img[data-src]'),function(img){img.setAttribute('src',img.getAttribute('data-src'));img.onload= function(){img.removeAttribute('data-src');};});}</style>";
		$html = str_replace( '</body>', $lazyScript.'</body>', $html );
		return $html;
	}

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