<?php
/**

 * Plugin Name: WP Development

 * Plugin URI: http://dev-rec.com/

 * Description: This plugin is designed for website caching and speed enhancement.

 * Version: 1.0.0

 * Author: Abdul Aleem Khan

 * Author URI: http://dev-rec.com/

 * License: GPL2

 */


defined( 'ABSPATH' ) || die( 'Not Allowed' );

define( 'DR_SLUG', "development" );
define( 'DR_NAME', "WPDevelopment" );
define( 'DR_FRIENDLY_NAME', "WP Development" );
define( 'DR_VERSION', "1.0.0.0" );
define( 'DR_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins/'. DR_SLUG .'/' );
define( 'DR_CACHE_PATH', DR_PLUGIN_DIR .'cached/' );



include "classes/DRFileSupport.php";
include "classes/DROptions.php";
include "classes/DRAdminUI.php";
include "classes/DRHTaccess.php";
include "classes/DRCacheControl.php";
include "classes/DRMinification.php";
include "classes/DRImageOptimization.php";



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
	    	var form = jQuery("#dr_settings_form")[0];
	    	var data = {};
	    	for(var i=0; i< form.length; i++){
	    		if(form[i].checked == true){
	    			data[form[i].name] = 1;
	    		}else{
	    			data[form[i].name] = 0;
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

	if($dr_options->checked("minify_styles")){
		$html = $drMinification->minifyInlineCss($html);
		$html = $drMinification->minifyExternalCss($html);
	}

	if($dr_options->checked("remove_style_queries")){
		$html = $drMinification->removeQueriesCss($html);
	}

	if($dr_options->checked("minify_scripts")){
		$html = $drMinification->minifyInlineJs($html);
		$html = $drMinification->minifyExternalJs($html);
	}	

	if($dr_options->checked("remove_script_queries")){
		$html = $drMinification->removeQueriesJs($html);
	}

	$drImageOptimization = new DRImageOptimization();
	
	if($dr_options->checked("optimize")){
		$html = $drImageOptimization->specifyImageDimensions($html);
	}

	if($dr_options->checked("lazyload")){
		$html = $drImageOptimization->lazyLoadImages($html);
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


include "classes/DROptimizeEmoji.php";
include "classes/DROptimizeIFrame.php";



?>