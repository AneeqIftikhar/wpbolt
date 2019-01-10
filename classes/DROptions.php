<?php
/*
This class takes care of the settings options
*/

class DROptions{

	public $options = [];
	public $TAG = 'dr_wp_options';

	function __construct(){
		$this->options = get_option( $this->TAG );
		if( $this->options == NULL ){
			$this->defaultValues();
		}
	}

	public function getOption( $key ){
		if( $this->options == NULL ){
			return NULL;
		}
		return $this->options[ $key ];
	}

	public function setOption( $key, $value ){
		$this->options[ $key ] = $value;
		$this->saveOptions();
	}

	public function addOptions(){
		add_option( $this->TAG, $this->options );	
	}

	public function saveOptions(){
		update_option( $this->TAG, $this->options );	
	}

	public function deleteOptions(){
		delete_option($this->TAG);
	}

	public function checked($str){
		$option = $this->getOption($str);
		if($option == NULL){
			return false;
		}

		if($option == "1"){
			return true;
		}

		return false;
	}

	public function defaultValues(){
		$this->options = [
			'cache_web' => 0,
			'cache_mobile' => 0,
			'cache_logged_in' => 0,
			'minify_styles' => 0,
			'minify_scripts' => 0,
			'combine_styles' => 0,
			'defer_styles' => 0,
			'combine_scripts' => 0,
			'defer_scripts' => 0,
			'remove_style_queries' => 0,
			'remove_script_queries' => 0,
			'lazyload' => 0,
			'compress' => 0,
			'optimize' => 0,
			'newsletter' => 0,
			'collection' => 0,
			'combine_google_fonts' => 0

		];
		$this->addOptions();	
	}
}

?>