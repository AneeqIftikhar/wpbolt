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
					padding-left:25px;
				}

				.pro-feature > i{
					position: absolute;
					left: 30px;
				}

				input{
					border:0px solid white !important;
					box-shadow: inset 0 1px 2px rgba(0,0,0,0) !important;
					background-image: none !important;
					border: 1px solid #9c27b0 !important;
					border-radius: 4px !important;
					padding-left: 4px !important;
					padding-right: 4px !important;
				}

				textarea{
					border: 1px solid #9c27b0 !important;
					border-radius: 4px !important;
					padding: 4px !important;
					background-image: none !important;
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
				<nav class="navbar navbar-expand-lg row">
					<div class="container">
						<a class="navbar-brand" href="#" style="height:80px;"><img width="180px" src="<?php echo DR_PLUGIN_PATH.'res/logo.png'?>"></a>
					</div>
				</nav>
				<div class="row">
					<div class="col-lg-8 col-md-8 col-xs-12 bg-light py-4">
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
							<li class="nav-item">
								<a class="nav-link <?php echo $this->drActivelink('faq'); ?>" href="<?php echo $this->drTablink('faq'); ?>">
									<i class="material-icons">question_answer</i>
									FAQs
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link <?php echo $this->drActivelink('support'); ?>" href="<?php echo $this->drTablink('support'); ?>">
									<i class="material-icons">chat</i>
									Support
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
													<div class="card-body">
														<button type="button" onclick="clearCache('clear_cache_notice')" class="btn btn-primary">Clear Cache</button>
														<strong class="mx-4" style="float:right; margin-top:15px;" id="clear_cache_notice"></strong>
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
													</div>
													<div class="card-body">
														<button type="button" onclick="clearCache('clear_cache_notice')" class="btn btn-primary">Clear Cache</button>
														<strong class="mx-4" style="float:right; margin-top:15px;" id="clear_cache_notice"></strong>
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
														<h4>Exclude Images</h4>
														<p>Enter one image name per line. If the image url is (http://yourdomain.com/your/path/my_image.jpg) then enter <strong>my_image</strong></p>
														<textarea placeholder="Excluded images here" class="form-control w-100" type="textarea" name="exclude_image" cols="60" rows="5"><?php echo $options['exclude_image']; ?></textarea>
														<div class="row">
															<div class="col-8">
																<button type="button" class="btn btn-primary" onclick="postSettings('exclude_image_notice')">Exclude Images</button>
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
														<h4>Minify Local Files</h4>
														<p>Enabling this option will minify the local files of CSS.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="minify_local_css" onchange="postSettings('minify_local_css_notice')" class="onoffswitch-checkbox" id="minify_local_css" value="1" <?php checked(1, $options['minify_local_css'], true); ?>>
																	<label class="onoffswitch-label" for="minify_local_css">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="minify_local_css_notice"></strong>
															</div>
														</div>
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
														<h4>Exclude CSS Files</h4>
														<p>Enter one file name per line. If the file url is (http://yourdomain.com/your/path/my_style.css) then enter <strong>my_style</strong></p>
														<textarea placeholder="Excluded CSS files here" class="form-control w-100" type="textarea" name="exclude_css" cols="60" rows="5"><?php echo $options['exclude_css']; ?></textarea>
														<div class="row">
															<div class="col-8">
																<button type="button" class="btn btn-primary" onclick="postSettings('exclude_css_notice')">Exclude Styles</button>
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
														<h4>Minify Local Files</h4>
														<p>Enabling this option will minify the local files of Javascript.</p>
														<div class="row">
															<div class="col-8">
																<div class="onoffswitch" class="pull-right">
																	<input type="checkbox" name="minify_local_js" onchange="postSettings('minify_local_js_notice')" class="onoffswitch-checkbox" id="minify_local_js" value="1" <?php checked(1, $options['minify_local_js'], true); ?>>
																	<label class="onoffswitch-label" for="minify_local_js">
																		<span class="onoffswitch-inner"></span>
																		<span class="onoffswitch-switch"></span>
																	</label>
																</div>
															</div>
															<div class="col-4 text-right">
																<strong class="mx-4" id="minify_local_js_notice"></strong>
															</div>
														</div>
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
														<h4>Exclude JS Files</h4>
														<p>Enter one file name per line. If the file url is (http://yourdomain.com/your/path/my_script.js) then enter <strong>my_script</strong></p>
														<textarea placeholder="Excluded JS files here" class="form-control w-100" type="textarea" name="exclude_js" cols="60" rows="5"><?php echo $options['exclude_js']; ?></textarea>
														<div class="row">
															<div class="col-8">
																<button type="button" class="btn btn-primary" onclick="postSettings('exclude_js_notice')">Exclude Scripts</button>
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
								<div class="tab-pane <?php echo $this->drActivelink('faq'); ?>" id="faq">
									<h2 class="text-center">Have question?</h2>
									<p>
										You can find your query in the frequently asked questions. 
										If your issues is not mentioned then you can contact our support in 'Support' section.
									</p>
									<div id="accordion" role="tablist">
										<div class="card card-collapse">
											<div class="card-header card-header-primary m-0 p-0 w-100" role="tab" id="headingOne">
												<a data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
													<h4 class="px-2 py-2 m-0 text-white">
														What is Cache?
														<i class="material-icons" style="float:right;">add</i>
													</h4>
												</a>
											</div>
											<div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion">
												<div class="card-body">
													Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
												</div>
											</div>
										</div>
										<div class="card card-collapse">
											<div class="card-header card-header-primary m-0 p-0 w-100" role="tab" id="headingTwo">
												<a data-toggle="collapse" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
													<h4 class="px-2 py-2 m-0 text-white">
														What is Lazyload?
														<i class="material-icons" style="float:right;">add</i>
													</h4>
												</a>
											</div>
											<div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo" data-parent="#accordion">
												<div class="card-body">
													Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
												</div>
											</div>
										</div>
										<div class="card card-collapse">
											<div class="card-header card-header-primary m-0 p-0 w-100" role="tab" id="headingThree">
												<a data-toggle="collapse" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
													<h4 class="px-2 py-2 m-0 text-white">
														What is minification?
														<i class="material-icons" style="float:right;">add</i>
													</h4>
												</a>
											</div>
											<div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree" data-parent="#accordion">
												<div class="card-body">
													Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane <?php echo $this->drActivelink('support'); ?>" id="support">
									<div class="card">
										<div class="card-header card-header-primary">
											<h3 class="m-0 p-0">Contact support</h3>
										</div>
										<div class="card-body px-5 pt-5">
											<div class="form-group">
												<label for="name">Full Name</label>
												<input type="text" class="form-control" id="dr_contact_name" placeholder="John Doe">
											</div>
											<div class="form-group">
												<label for="email">Email address</label>
												<input type="email" class="form-control" id="dr_contact_email" placeholder="name@example.com">
											</div>
											<div class="form-group">
												<label for="message">Message</label>
												<textarea placeholder="Type message here" class="form-control" id="dr_contact_message" rows="3"></textarea>
											</div>
										</div>
										<div class="card-footer px-5 text-right" style="display: initial !important;">
											<strong id="dr_sending_message" style="float:left; margin-top:15px;"></strong>
											<button onclick="contactSupport('dr_sending_message')" type="button" class="btn btn-primary">Submit</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-lg-4 col-md-4 col-xs-12">
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