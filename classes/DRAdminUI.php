<?php
/* 
This class takes care of the admin UI 
*/
defined( 'ABSPATH' ) || die( 'Direct Access Not Allowed' );
class DRAdminUI{

	public $tab = "cache";

	function __construct(){
		$this->tab = $this->drTab();
	}

	public function drTablink($str){
		return admin_url()."index.php?page=".DR_SLUG."&amp;tab=".$str;
	}

	public function drTab(){
		if(!isset($_REQUEST['tab'])){
			return "cache";
		}
		return $_REQUEST['tab'];
	}

	public function drActivelink($str){
		if(!isset($_REQUEST['tab'])){
			if($str == 'basic'){
				return "active";
			}
		}
		if($str == $_REQUEST['tab']){
			return "active";
		}
		return "";
	}

	public function html($options){
		$currentTab = $this->tab;
		?>
			<?php
			 	$dr_primary = "#ffc107";
			 	$dr_secondary = "#ffab40";
			 	$dr_active = "#ff9800";
			 	$dr_border = "#ffab40";
			?>
			<link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Roboto:400,700,300|Material+Icons" rel="stylesheet" type="text/css">
			<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/material-kit/2.0.4/css/material-kit.min.css">
			<script src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>
			<script src="https://unpkg.com/popper.js@1.14.7/dist/umd/popper.min.js" ></script>
			<script src="https://demos.creative-tim.com/material-dashboard/assets/js/core/bootstrap-material-design.min.js" ></script>
			<script src="https://demos.creative-tim.com/material-dashboard/assets/js/plugins/perfect-scrollbar.jquery.min.js" ></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/material-kit/2.0.4/js/material-kit.min.js"></script>
			<style type="text/css">
				.dr_container{
					width:100%; 
					display:flex;
				}

				.dr_nav{
					width:20%;
					background: <?php echo $dr_primary; ?>;
				}

				.dr_nav > ul{
					margin:0px;
				}

				.dr_nav > ul > li{
					display: flex;
					border-bottom: 1px solid  <?php echo $dr_border; ?>;
					margin: 0px;
				}

				.dr_nav>ul>li>a{
					width:100%;
					text-decoration: none;
					color:  black;
					padding: 10px 20px;
					font-size: 16px;
					font-weight: 500;
				}

				.dr_nav>ul>li>a.active{
					background:  <?php echo $dr_active; ?>;
				}

				.dr_nav>ul>li>a.active:after{
					content: "";
				}

				.dr_nav>ul>li>a:hover{
					background:  <?php echo $dr_active; ?>;
				}

				.dr_content{
					width:80%;
					background: #dddfd4;
					padding: 20px;
				}

				#dr_notice{
					background: orange;
					padding: 20px;
				}

				#dr_notice.success{
					background: #a4c506;
				}

				.dr-bolt-ui, .card-title, .info .info-title{
					font-family: 'Roboto' !important;
				}
				.card{
					padding: 0px;
					max-width: initial !important;
				}
				.card-header{
					width: fit-content;
				}
				.card-title{
					margin: 0px !important;
				}

				.pro-feature{
					padding-left:22px;
				}

				.pro-feature > i{
					position: absolute;
					left: 30px;
				}
			</style>
			<style>
				.onoffswitch {
					position: relative; width: 90px;
					-webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
				}
				.onoffswitch-label {
					display: block; overflow: hidden; cursor: pointer;
					border: 2px solid #999999; border-radius: 20px;
				}
				.onoffswitch-inner {
					display: block; width: 200%; margin-left: -100%;
					transition: margin 0.3s ease-in 0s;
				}
				.onoffswitch-inner:before, .onoffswitch-inner:after {
					display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
					font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
					box-sizing: border-box;
				}
				.onoffswitch-inner:before {
					content: "ON";
					padding-left: 10px;
					background-color: #9423D1; color: #FFFFFF;
				}
				.onoffswitch-inner:after {
					content: "OFF";
					padding-right: 10px;
					background-color: #EEEEEE; color: #999999;
					text-align: right;
				}
				.onoffswitch-switch {
					display: block; width: 22px; margin: 6px;
					height: 22px;
					background: #FFFFFF;
					position: absolute; top: 0; bottom: 0;
					right: 56px;
					border: 2px solid #999999; border-radius: 20px;
					transition: all 0.3s ease-in 0s; 
				}
				.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
					margin-left: 0;
				}
				.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
					right: 0px; 
				}
				.onoffswitch-checkbox {
					display: none !important;
				}
			</style>
			<div class="wrap dr-bolt-ui">
				<div class="row">
					<div class="col-lg-7 col-md-8 col-xs-12 bg-light py-4">
						<ul class="nav nav-pills nav-pills-icons" role="tablist">
							<li class="nav-item">
								<a class="nav-link <?php echo $this->drActivelink('basic'); ?>" href="<?php echo $this->drTablink('basic'); ?>">
									<i class="material-icons">settings</i>
									Basic
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link <?php echo $this->drActivelink('advance'); ?>" href="<?php echo $this->drTablink('advance'); ?>">
									<i class="material-icons">settings_input_component</i>
									Advance
								</a>
							</li>
						</ul>
						<hr>
						<form method="post" action="" id="dr_settings_form" onsubmit="postSettings(); return false;">
							<div class="tab-content tab-space">
								<div class="tab-pane <?php echo $this->drActivelink('basic'); ?>" id="basic">
									<?php if($this->drActivelink('basic')=='active'){ ?>
										<div class="row">
											<div class="col-12">
												<div class="card text-left mb-4">
													<div class="card-header card-header-text card-header-primary">
														<div class="card-text">
															<h4 class="card-title">Enable Caching</h4>
														</div>
													</div>
													<div class="card-body">
														<p>
															Enabling this option will start caching web pages and users will receive 
															cached copies. Some plugins or themes may not refresh so you have to clear
															cache in case your changes are not being reflected.
														</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="basic_cache" onchange="postSettings('basic_cache_notice')" class="onoffswitch-checkbox" id="basic_cache" value="1" <?php checked(1, $options['basic_cache'], true); ?>>
																	<label class="onoffswitch-label" for="basic_cache">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="basic_cache_notice"></strong>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="card text-left mb-4">
													<div class="card-header card-header-text card-header-primary">
														<div class="card-text">
															<h4 class="card-title">Lazy Load</h4>
														</div>
													</div>
													<div class="card-body">
														<p>
															Activate this option so that images do not block the UI rendering. 
															Furthermore, images that are out of view port will only be loaded when you will scroll to them.
														</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="basic_lazyload" onchange="postSettings('basic_lazyload_notice')" class="onoffswitch-checkbox" id="basic_lazyload" value="1" <?php checked(1, $options['basic_lazyload'], true); ?>>
																	<label class="onoffswitch-label" for="basic_lazyload">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="basic_lazyload_notice"></strong>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="card text-left mb-4">
													<div class="card-header card-header-text card-header-primary">
														<div class="card-text">
															<h4 class="card-title">CSS Minification</h4>
														</div>
													</div>
													<div class="card-body">
														<h4>Basic Minification</h4>
														<p>Enabling this option will minify the inline blocks and external files of CSS.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="basic_minify_css" onchange="postSettings('basic_minify_css_notice')" class="onoffswitch-checkbox" id="basic_minify_css" value="1" <?php checked(1, $options['basic_minify_css'], true); ?>>
																	<label class="onoffswitch-label" for="basic_minify_css">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="basic_minify_css_notice"></strong>
															</div>
														</div>
														<h4>Advance Optimization</h4>
														<p>Enabling this option will minify and optimize the inline blocks and external files of CSS.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="advance_minify_css" onchange="postSettings('advance_minify_css_notice')" class="onoffswitch-checkbox" id="advance_minify_css" value="1" <?php checked(1, $options['advance_minify_css'], true); ?>>
																	<label class="onoffswitch-label" for="advance_minify_css">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="advance_minify_css_notice"></strong>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="card text-left mb-4">
													<div class="card-header card-header-text card-header-primary">
														<div class="card-text">
															<h4 class="card-title">JS Minification</h4>
														</div>
													</div>
													<div class="card-body">
														<h4>Basic Minification</h4>
														<p>Enabling this option will minify the inline blocks and external files of Javascript.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="basic_minify_js" onchange="postSettings('basic_minify_js_notice')" class="onoffswitch-checkbox" id="basic_minify_js" value="1" <?php checked(1, $options['basic_minify_js'], true); ?>>
																	<label class="onoffswitch-label" for="basic_minify_js">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="basic_minify_js_notice"></strong>
															</div>
														</div>
														<h4>Advance Optimization</h4>
														<p>Enabling this option will minify and optimize the inline blocks and external files of Javascript.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="advance_minify_js" onchange="postSettings('advance_minify_js_notice')" class="onoffswitch-checkbox" id="advance_minify_js" value="1" <?php checked(1, $options['advance_minify_js'], true); ?>>
																	<label class="onoffswitch-label" for="advance_minify_js">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="advance_minify_js_notice"></strong>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
								</div>
								<div class="tab-pane <?php echo $this->drActivelink('advance'); ?>" id="advance">
									<?php if($this->drActivelink('advance')=='active'){ ?>
										<div class="row">
											<div class="col-12">
												<div class="card text-left mb-4">
													<div class="card-header card-header-text card-header-primary">
														<div class="card-text">
															<h4 class="card-title">Enable Caching</h4>
														</div>
													</div>
													<div class="card-body">
														<p>
															Enabling this option will start caching web pages and users will receive 
															cached copies. Some plugins or themes may not refresh so you have to clear
															cache in case your changes are not being reflected.
														</p>
														<h4>Enable Web Cache</h4>
														<p>Enabling this option will cache web for desktop browsers.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="basic_cache" onchange="postSettings('basic_cache_notice')" class="onoffswitch-checkbox" id="basic_cache" value="1" <?php checked(1, $options['basic_cache'], true); ?>>
																	<label class="onoffswitch-label" for="basic_cache">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="basic_cache_notice"></strong>
															</div>
														</div>
														<h4 class="pro-feature"><i class="material-icons text-primary">lock</i>Enable Mobile Cache <small class="text-primary">Pro Feature</small></h4>
														<p>Enabling this option will cache web for visitors on mobile devices.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="basic_cache_mobile" class="onoffswitch-checkbox" id="basic_cache_mobile" value="1" <?php checked(1, $options['basic_cache_mobile'], true); ?> disabled>
																	<label class="onoffswitch-label" for="basic_cache_mobile">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="basic_cache_mobile"></strong>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="card text-left mb-4">
													<div class="card-header card-header-text card-header-primary">
														<div class="card-text">
															<h4 class="card-title">Lazy Load</h4>
														</div>
													</div>
													<div class="card-body">
														<p>
															Activate these option so that images do not block the UI rendering. 
															Furthermore, images that are out of view port will only be loaded when you will scroll to them.
														</p>
														<h4>Images</h4>
														<p>Enabling this option will lazyload all images in img tag of html.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="basic_lazyload" onchange="postSettings('basic_lazyload_notice')" class="onoffswitch-checkbox" id="basic_lazyload" value="1" <?php checked(1, $options['basic_lazyload'], true); ?>>
																	<label class="onoffswitch-label" for="basic_lazyload">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="basic_lazyload_notice"></strong>
															</div>
														</div>
														<h4 class="pro-feature"><i class="material-icons text-primary">lock</i>Background Images <small class="text-primary">Pro Feature</small></h4>
														<p>Enabling this option will lazyload all images that are added in the backgrounds of different UI elements.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="lazyload_bg" class="onoffswitch-checkbox" id="lazyload_bg" value="1" disabled>
																	<label class="onoffswitch-label" for="lazyload_bg">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="lazyload_bg_notice"></strong>
															</div>
														</div>
														<h4>Exclude Images</h4>
														<p>Enter one image name per line. If the image url is (http://yourdomain.com/your/path/my_image.jpg) then enter <strong>my_image</strong></p>
														<textarea placeholder="Excluded images here" class="form-control w-100" type="textarea" name="exclude_image" cols="60" rows="5"><?php echo $options['exclude_image']; ?></textarea>
														<div class="row">
															<div class="col-8">
																<button type="button" class="btn btn-primary" onclick="postSettings('exclude_image_notice')">Save</button>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4 pt-3" id="exclude_image_notice"></strong>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="card text-left mb-4">
													<div class="card-header card-header-text card-header-primary">
														<div class="card-text">
															<h4 class="card-title">CSS Minification and Optimization</h4>
														</div>
													</div>
													<div class="card-body">
														<h4>Minify Inline Blocks</h4>
														<p>Enabling this option will minify the inline blocks of CSS.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="minify_inline_css" onchange="postSettings('minify_inline_css_notice')" class="onoffswitch-checkbox" id="minify_inline_css" value="1" <?php checked(1, $options['minify_inline_css'], true); ?>>
																	<label class="onoffswitch-label" for="minify_inline_css">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="minify_inline_css_notice"></strong>
															</div>
														</div>
														<h4>Minify Linked Files</h4>
														<p>Enabling this option will minify the external files of CSS.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="minify_external_css" onchange="postSettings('minify_external_css_notice')" class="onoffswitch-checkbox" id="minify_external_css" value="1" <?php checked(1, $options['minify_external_css'], true); ?>>
																	<label class="onoffswitch-label" for="minify_external_css">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="minify_external_css_notice"></strong>
															</div>
														</div>
														<h4>Remove Queries from CSS</h4>
														<p>Enabling this option will remove the version queries from external files of CSS. It will increase the serving speed.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="remove_css_queries" onchange="postSettings('remove_css_queries_notice')" class="onoffswitch-checkbox" id="remove_css_queries" value="1" <?php checked(1, $options['remove_css_queries'], true); ?>>
																	<label class="onoffswitch-label" for="remove_css_queries">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="remove_css_queries_notice"></strong>
															</div>
														</div>
														<h4 class="pro-feature"><i class="material-icons text-primary">lock</i>Combine CSS <small class="text-primary">Pro Feature</small></h4>
														<p>Enabling this option will combine all CSS into as few files as possible. It will reduce the number of HTTP requests and will increase the load speed magically.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="combine_css" class="onoffswitch-checkbox" id="combine_css" value="1" disabled>
																	<label class="onoffswitch-label" for="combine_css">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="combine_css_notice"></strong>
															</div>
														</div>
														<h4>Exclude CSS Files</h4>
														<p>Enter one file name per line. If the file url is (http://yourdomain.com/your/path/my_style.css) then enter <strong>my_style</strong></p>
														<textarea placeholder="Excluded CSS files here" class="form-control w-100" type="textarea" name="exclude_css" cols="60" rows="5"><?php echo $options['exclude_css']; ?></textarea>
														<div class="row">
															<div class="col-8">
																<button type="button" class="btn btn-primary" onclick="postSettings('exclude_css_notice')">Save</button>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4 pt-3" id="exclude_css_notice"></strong>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="card text-left mb-4">
													<div class="card-header card-header-text card-header-primary">
														<div class="card-text">
															<h4 class="card-title">JS Minification and Optimization</h4>
														</div>
													</div>
													<div class="card-body">
														<h4>Minify Inline Blocks</h4>
														<p>Enabling this option will minify the inline blocks of Javascript.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="minify_inline_js" onchange="postSettings('minify_inline_js_notice')" class="onoffswitch-checkbox" id="minify_inline_js" value="1" <?php checked(1, $options['minify_inline_js'], true); ?>>
																	<label class="onoffswitch-label" for="minify_inline_js">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="minify_inline_js_notice"></strong>
															</div>
														</div>
														<hr>
														<h4>Minify Linked Files</h4>
														<p>Enabling this option will minify the external files of Javascript.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="minify_external_js" onchange="postSettings('minify_external_js_notice')" class="onoffswitch-checkbox" id="minify_external_js" value="1" <?php checked(1, $options['minify_external_js'], true); ?>>
																	<label class="onoffswitch-label" for="minify_external_js">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="minify_external_js_notice"></strong>
															</div>
														</div>
														<h4>Remove Queries from JS</h4>
														<p>Enabling this option will remove the version queries from external files of Javascript. It will increase the serving speed.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="remove_js_queries" onchange="postSettings('remove_js_queries_notice')" class="onoffswitch-checkbox" id="remove_js_queries" value="1" <?php checked(1, $options['remove_js_queries'], true); ?>>
																	<label class="onoffswitch-label" for="remove_js_queries">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="remove_js_queries_notice"></strong>
															</div>
														</div>
														<h4>Defer JS</h4>
														<p>Enabling this option will load the Javascript files after the UI has been loaded. This will stop render blocking due to large script files.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="defer_js" onchange="postSettings('defer_js_notice')" class="onoffswitch-checkbox" id="defer_js" value="1" <?php checked(1, $options['defer_js'], true); ?>>
																	<label class="onoffswitch-label" for="defer_js">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="defer_js_notice"></strong>
															</div>
														</div>
														<h4 class="pro-feature"><i class="material-icons text-primary">lock</i>Combine Javascript <small class="text-primary">Pro Feature</small></h4>
														<p>Enabling this option will combine all Javascript into as few files as possible. It will reduce the number of HTTP requests and will increase the load speed magically.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="combine_js" class="onoffswitch-checkbox" id="combine_js" value="1" disabled>
																	<label class="onoffswitch-label" for="combine_js">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="combine_js_notice"></strong>
															</div>
														</div>
														<h4>Exclude JS Files</h4>
														<p>Enter one file name per line. If the file url is (http://yourdomain.com/your/path/my_script.js) then enter <strong>my_script</strong></p>
														<textarea placeholder="Excluded JS files here" class="form-control w-100" type="textarea" name="exclude_js" cols="60" rows="5"><?php echo $options['exclude_js']; ?></textarea>
														<div class="row">
															<div class="col-8">
																<button type="button" class="btn btn-primary" onclick="postSettings('exclude_js_notice')">Save</button>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4 pt-3" id="exclude_js_notice"></strong>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
						</form>
					</div>
					<div class="col-lg-5 col-md-4 col-xs-12">
						<div class="info">
							<div class="icon icon-primary">
								<i class="material-icons">stars</i>
							</div>
							<h4 class="info-title">WP Bolt Pro is Free</h4>
							<p class="text-dark">
								1. No signup required.
							</p>
							<p class="text-dark">
								2. No credit card required.
							</p>
							<p class="text-dark">
								3. Upgrade to premium version for free. 
							</p>
							<a href="http://wp-bolt.ripcordsystems.com" target="_blank" class="btn btn-primary">Upgrade Now</a>
						</div>
						<hr>
						<div class="info">
							<div class="icon icon-success">
								<i class="material-icons">developer_mode</i>
							</div>
							<h4 class="info-title">Hire an Expert</h4>
							<p class="text-dark">
								If you require further customized experience you can hire one of our expert devs.
							</p>
							<a href="https://ripcordsystems.com#contact" target="_blank" class="btn btn-success">Hire Now</a>
						</div>
					</div>
				</div>
			</div>
		<?php

	}
}

?>