<?php
/**

 * Plugin Name: WP Bolt

 * Plugin URI: http://dev-rec.com/

 * Description: This plugin is designed for website caching and speed enhancement.

 * Version: 0.0.3

 * Author: Abdul Aleem Khan

 * Author URI: http://dev-rec.com/

 * License: GPL2

 */


defined( 'ABSPATH' ) || die( 'Direct Access Not Allowed' );

define( 'DR_SLUG', "wp-bolt" );
define( 'DR_NAME', "WP Bolt" );
define( 'DR_FRIENDLY_NAME', "WP Bolt" );
define( 'DR_VERSION', "1.0.0" );
define( 'DR_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins/'. DR_SLUG .'/' );
define( 'DR_PLUGIN_PATH', WP_CONTENT_URL.'/plugins/'. DR_SLUG .'/' );
define( 'DR_CACHE_PATH', DR_PLUGIN_DIR .'cached/' );



include_once "classes/DRFileSupport.php";
include_once "classes/DROptions.php";
include_once "classes/DRAdminUI.php";
include_once "classes/DRHTaccess.php";
include_once "classes/DRCacheControl.php";
include_once "classes/DRMinification.php";
include_once "classes/DRImageOptimization.php";



function activate_dr_plugin() {
    add_option( 'Activated_Plugin', DR_SLUG );
	$dr_htaccess = new DRHTaccess();
	$dr_cache_control = new DRCacheControl();
	$dr_htaccess->buildHtAccess();
	$dr_cache_control->drWPCacheDefine( true );
  	$dr_cache_control->generateAdvancedCacheFile();

}
register_activation_hook( __FILE__, 'activate_dr_plugin' );


function deactivate_dr_plugin() {
    add_option( 'Activated_Plugin', DR_SLUG );
	$dr_htaccess = new DRHTaccess();
	$dr_cache_control = new DRCacheControl();
	$dr_htaccess->cleanHtAccess();
	$dr_cache_control->cleanAdvancedCacheFile();
}
register_deactivation_hook( __FILE__, 'deactivate_dr_plugin' );

include('classes/DRActions.php');

function dr_plugin_menu( ){
	add_menu_page( 
		DR_FRIENDLY_NAME, 
		DR_FRIENDLY_NAME, 
		'administrator', 
		DR_SLUG, 'dr_admin_ui', 
		'dashicons-admin-generic', 
		100 
	);
}

add_action( 'admin_menu', 'dr_plugin_menu' );

function dr_admin_ui( ){
	$dr_options = new DROptions();
	$dr_adminUi = new DRAdminUI();
	$dr_adminUi->html($dr_options->options);
}

function dr_set_options( ) {
	if( is_admin() ){ 
		$params = $_POST;
		$dr_options = new DROptions();
		$changed = 0;
		foreach ( $params as $key => $value ){
			if($key == 'action' || $key == 'submit'){
				continue;
			}
			$dr_options->setOption($key, $value);

			if($key == 'basic_lazyload' && $value == 1){
				$dr_options->setOption('lazyload', 1);
			}

			if($key == 'basic_lazyload' && $value == 0){
				$dr_options->setOption('lazyload', 0);
			}

			if($key == 'basic_cache' && $value == 1){
				$dr_options->setOption('cache_web', 1);
				$dr_cache_control = new DRCacheControl();
  				$dr_cache_control->generateAdvancedCacheFile();
			}

			if($key == 'basic_cache' && $value == 0){
				$dr_options->setOption('cache_web', 0);
				$dr_cache_control = new DRCacheControl();
  				$dr_cache_control->cleanAdvancedCacheFile();
			}

			if($key == 'advance_minify_js' && $value == 1){
				$dr_options->setOption('minify_inline_js', 1);
				$dr_options->setOption('minify_local_js', 1);
				$dr_options->setOption('minify_external_js', 1);
				$dr_options->setOption('defer_js', 1);
				$dr_options->setOption('remove_js_queries', 1);
			}

			if($key == 'advance_minify_js' && $value == 0){
				if($dr_options->getOption('basic_minify_js') == 0){
					$dr_options->setOption('minify_inline_js', 0);
					$dr_options->setOption('minify_local_js', 0);
				}
				$dr_options->setOption('minify_external_js', 0);
				$dr_options->setOption('defer_js', 0);
				$dr_options->setOption('remove_js_queries', 0);
			}

			if($key == 'basic_minify_js' && $value == 1){
				$dr_options->setOption('minify_inline_js', 1);
				$dr_options->setOption('minify_local_js', 1);
			}

			if($key == 'basic_minify_js' && $value == 0){
				if($dr_options->getOption('advance_minify_js') == 0){
					$dr_options->setOption('minify_inline_js', 0);
					$dr_options->setOption('minify_local_js', 0);
				}
			}

			if($key == 'advance_minify_css' && $value == 1){
				$dr_options->setOption('minify_inline_css', 1);
				$dr_options->setOption('minify_local_css', 1);
				$dr_options->setOption('minify_external_css', 1);
				$dr_options->setOption('remove_css_queries', 1);
			}

			if($key == 'advance_minify_css' && $value == 0){
				if($dr_options->getOption('basic_minify_css') == 0){
					$dr_options->setOption('minify_inline_css', 0);
					$dr_options->setOption('minify_local_css', 0);
				}
				$dr_options->setOption('minify_external_css', 0);
				$dr_options->setOption('remove_css_queries', 0);
			}

			if($key == 'basic_minify_css' && $value == 1){
				$dr_options->setOption('minify_inline_css', 1);
				$dr_options->setOption('minify_local_css', 1);
			}

			if($key == 'basic_minify_css' && $value == 0){
				if($dr_options->getOption('advance_minify_css') == 0){
					$dr_options->setOption('minify_inline_css', 0);
					$dr_options->setOption('minify_local_css', 0);
				}
			}

			if(($key == 'minify_inline_css' || $key == 'minify_local_css' || $key == 'minify_external_css' || $key == 'defer_css' || $key == 'remove_css_queries') && $value == 0 ){
				$dr_options->setOption('advance_minify_css', 0);
			}

			if(($key == 'minify_inline_js' || $key == 'minify_local_js' || $key == 'minify_external_js' || $key == 'remove_js_queries') && $value == 0 ){
				$dr_options->setOption('advance_minify_js', 0);
			}

			$changed++;
		}
		if($changed > 0){
			clean_data_cache_again();
		}
		$res["status"] = "success";
		$res["message"] = "Changes has been saved.";
		echo json_encode($res);
	}else{
		$res["status"] = "fail";
		$res["message"] = "Changes has not been saved.";
		echo json_encode($res);
	}
	wp_die();
}
add_action( 'wp_ajax_dr_set_options', 'dr_set_options' );

function drFooterScript(){
	?>
	<script>
		var dr_notice_id;
		var dr_hide_notices = []; 
		function postSettings(id){  
			dr_notice_id = id;
	    	var dr_notice = document.getElementById(id);
 	    	dr_notice.style.display = "inherit";
    		dr_notice.classList.remove("text-success");
    		dr_notice.innerHTML = "Saving...";
  	
	    	var form = document.querySelector("#dr_settings_form");
	    	var data = {};
	    	for(var i=0; i< form.length; i++){
				if(form[i].type=="checkbox"){
					if(form[i].checked == true){
						data[form[i].name] = 1;
					}else{
						data[form[i].name] = 0;
					}
				}else{
					data[form[i].name] = form[i].value;
				}

				if(id == 'basic_minify_js_notice'){
					if(data['basic_minify_js'] == 0){
						data['basic_minify_js'] = 0;
						document.querySelector('#advance_minify_js').checked =  false;
					}
				}

				if(id == 'basic_minify_css_notice'){
					if(data['basic_minify_css'] == 0){
						data['advance_minify_css'] = 0;
						document.querySelector('#advance_minify_css').checked =  false;
					}
				}
				
				if(id == 'advance_minify_css_notice'){
					if(data['advance_minify_css'] == 1){
						data['basic_minify_css'] = 1;
						document.querySelector('#basic_minify_css').checked =  true;
					}
				}
				
				if(id == 'advance_minify_js_notice'){
					if(data['advance_minify_js'] == 1){
						data['basic_minify_js'] = 1;
						document.querySelector('#basic_minify_js').checked =  true;
					}
				}
	    	}
	        data["action"] = 'dr_set_options'; 
			makeServerPost("<?php echo admin_url( 'admin-ajax.php' ); ?>", data);       
	    };

		function param(object) {
			var encodedString = '';
			for (var prop in object) {
				if (object.hasOwnProperty(prop)) {
					if (encodedString.length > 0) {
						encodedString += '&';
					}
					encodedString += encodeURI(prop + '=' + object[prop]);
				}
			}
			return encodedString;
		}

		function makeServerPost(url, data){
			var http = new XMLHttpRequest();
			http.open('POST', url, true);
			http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			http.onreadystatechange = function() {
				if(http.readyState == 4 && http.status == 200) {
					drResponse(http.responseText, "success", null);
				}
			}
			http.send(param(data));
		}

	    function drResponse(a, b, c){
	    	if(b == "success"){
	    		drSuccess(a);
	    	}else{
	    		drError(c);
	    	}
	    }

	    function drSuccess(response){
	    	if( typeof response == "string" ){
	    		response = JSON.parse(response);
	    	}
	    	var dr_notice = document.getElementById(dr_notice_id);
	    	if(response.status == "success"){
	    		dr_notice.style.display = "inherit";
	    		dr_notice.classList.add("text-success");
	    		dr_notice.innerHTML = "Saved.";
	    	}else{
	    		dr_notice.style.display = "inherit";
	    		dr_notice.classList.remove("text-success").add("text-danger");
	    		dr_notice.innerHTML = "Failed.";
	    	}
			dr_hide_notices.push(dr_notice_id);
			setTimeout(
				function(){
					var id = dr_hide_notices.splice(0, 1);
					document.querySelector('#'+id).style.display = 'none';
				}, 3000
			);
	    }

	    function drError(obj){
	    	console.error(obj);
	    }
	</script>
<?php
}
add_action('admin_footer', 'drFooterScript');

function drMinifyContent($html){
	$dr_options = new DROptions();
	$drMinification = new DRMinification();
	$html = $drMinification->drRemoveComments($html);

	if($dr_options->checked("remove_css_queries")){
		$html = $drMinification->removeQueriesCss($html);
	}
	if($dr_options->checked("minify_local_css")){
		$drCombineCss = false;
		$drScope = "local";
		$html = $drMinification->minifyLinkedCss($html, $drCombineCss, $drScope);
	}
	if($dr_options->checked("minify_external_css")){
		$drCombineCss = false;
		$drScope = "external";
		$html = $drMinification->minifyLinkedCss($html, $drCombineCss, $drScope);
	}
	if($dr_options->checked("minify_inline_css")){
		$html = $drMinification->minifyInlineCss($html);
	}

	if($dr_options->checked("remove_js_queries")){
		$html = $drMinification->removeQueriesJs($html);
	}

	if($dr_options->checked("minify_external_js")){
		$html = $drMinification->minifyExternalJs($html);
	}

	if($dr_options->checked("minify_inline_js")){
		$html = $drMinification->minifyInlineJs($html);
	}

	$drImageOptimization = new DRImageOptimization($dr_options->options);
	
	if($dr_options->checked("optimize")){
		$html = $drImageOptimization->specifyImageDimensions($html);
	}

	if($dr_options->checked("lazyload")){
		$html = $drImageOptimization->lazyLoadImages($html);
	}	

	if($dr_options->checked("lazyload_bg")){
		$html = $drImageOptimization->lazyLoadInTagBackgroundImages($html);
	}	

	return $html;
}

$output = "";

add_filter('final_output', 'drMinifyContent', $output);

ob_start();
add_action('shutdown', function() {
    $final = '';
    $levels = ob_get_level();
    for ($i = 0; $i < $levels; $i++) {
        $final .= ob_get_clean();
    }
    if ( ! is_admin() ) {
		if ( $GLOBALS['pagenow'] === 'wp-login.php' ) {
			echo $final;
		}else{
			$dr_cache_control = new DRCacheControl();
			echo $dr_cache_control->createCacheFile($final);
		}
	}else{
		echo $final;
	}
	//after_files_included();
}, 0);


function get_linked_files() {

    $result = [];
    $result['scripts'] = [];
    $result['styles'] = [];

    // Print all loaded Scripts
    global $wp_scripts;
    foreach( $wp_scripts->queue as $script ) :
       $result['scripts'][] =  $wp_scripts->registered[$script]->src . ";";
    endforeach;

    // Print all loaded Styles (CSS)
    global $wp_styles;
    foreach( $wp_styles->queue as $style ) :
       $result['styles'][] =  $wp_styles->registered[$style]->src . ";";
    endforeach;
    return $result;
}

//add_action( 'wp_head', 'after_files_included');

function after_files_included(){
	$allscripts_and_styles = get_linked_files();
	echo "<script>var included_files = '".json_encode($allscripts_and_styles)."';</script>";
}


include_once "classes/DROptimizeEmoji.php";
include_once "classes/DROptimizeIFrame.php";



?>