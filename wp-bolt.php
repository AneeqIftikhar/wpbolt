<?php
/**

 * Plugin Name: WP Bolt

 * Plugin URI: http://dev-rec.com/

 * Description: This plugin is designed for website caching and speed enhancement.

 * Version: 1.0.0

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
	    function postSettings(){  
	    	var dr_notice = document.getElementById("dr_notice");
 	    	dr_notice.style.display = "inherit";
    		dr_notice.classList.remove("success");
    		dr_notice.innerHTML = "Saving settings.";
  	
	    	var form = jQuery("#dr_settings_form")[0];
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
	    	}
	        data["action"] = 'dr_set_options';
	        jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', data, drResponse);        
	    };

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
	    	var dr_notice = document.getElementById("dr_notice");
	    	if(response.status == "success"){
	    		dr_notice.style.display = "inherit";
	    		dr_notice.classList.add("success");
	    		dr_notice.innerHTML = response.message;
	    	}else{
	    		dr_notice.style.display = "inherit";
	    		dr_notice.classList.remove("success");
	    		dr_notice.innerHTML = response.message;
	    	}
	    	console.log(response);
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

	if($dr_options->checked("combine_css")){
		$drDeferCss = false;
		if($dr_options->checked("defer_css")){
			$drDeferCss = true;
		}
		$drNoQueries = false;
		if($dr_options->checked("remove_css_queries")){
			$drNoQueries = true;
		}
		$html = $drMinification->minifyAllCss($html, $drDeferCss, $drNoQueries);
	}else{
		if($dr_options->checked("remove_css_queries")){
			$html = $drMinification->removeQueriesCss($html);
		}
		if($dr_options->checked("minify_external_css")){
			$drCombineCss = false;
			$drDeferCss = false;
			if($dr_options->checked("defer_css")){
				$drDeferCss = true;
			}
			$drNoQueries = false;
			if($dr_options->checked("remove_css_queries")){
				$drNoQueries = true;;
			}
			$html = $drMinification->minifyExternalCss($html, $drCombineCss, $drDeferCss, $drNoQueries);
		}
		if($dr_options->checked("minify_inline_css")){
			$html = $drMinification->minifyInlineCss($html);
		}
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
		$dr_cache_control = new DRCacheControl();
	    echo $dr_cache_control->createCacheFile($final);
	}else{
		echo $final;
	}
}, 0);


include_once "classes/DROptimizeEmoji.php";
include_once "classes/DROptimizeIFrame.php";



?>