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
			if($str == 'cache'){
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
			</style>
			<div class="wrap">
				<div class="dr_container">
					<div class="dr_nav">
						<ul>
							<li><a class="<?php echo $this->drActivelink('cache'); ?>" href="<?php echo $this->drTablink('cache'); ?>">Cache</a></li>
							<li><a class="<?php echo $this->drActivelink('minify'); ?>" href="<?php echo $this->drTablink('minify'); ?>">Minification</a></li>
							<li><a class="<?php echo $this->drActivelink('images'); ?>" href="<?php echo $this->drTablink('images'); ?>">Images</a></li>
							<li><a class="<?php echo $this->drActivelink('settings'); ?>" href="<?php echo $this->drTablink('settings'); ?>">Settings</a></li>
							<li><a class="<?php echo $this->drActivelink('contact'); ?>" href="<?php echo $this->drTablink('contact'); ?>">Speed Up</a></li>
						</ul>
					</div>
					<div class="dr_content">
						<div id="dr_notice" style="display:none;"></div>
						<form method="post" action="" id="dr_settings_form" onsubmit="postSettings(); return false;">
							<?php if($this->drTab() == "cache"){ ?>
							    <table class="form-table">
							        <tr valign="top">
								        <th scope="row">Cache Pages</th>
								        <td><input type="checkbox" name="cache_web" value="1" <?php checked(1, $options['cache_web'], true); ?>/></td>
							        </tr>
							        <tr valign="top">
								        <th scope="row">Cache for mobile devices</th>
								        <td><input type="checkbox" name="cache_mobile" value="1" <?php checked(1, $options['cache_mobile'], true); ?>/></td>
							        </tr>
							        <tr valign="top">
								        <th scope="row">Cache for logged in users</th>
								        <td><input type="checkbox" name="cache_logged_in" value="1" <?php checked(1, $options['cache_logged_in'], true); ?>/></td>
							        </tr>
							    </table>
						    <?php 
						    	submit_button(); 
								} 
								if( $this->drTab() == "minify" ){
							?>
							    <table class="form-table">
							    	<tr>
							    		<th valign="top" colspan="2">
							    			<h3>Managing CSS</h3>
							    		</th>
							    	</tr>
							    	<tr valign="top">
							        	<th scope="row">Minify inline CSS</th>
							        	<td><input type="checkbox" name="minify_inline_css" value="1" <?php checked(1, $options['minify_inline_css'], true); ?>/></td>
							        </tr>
							    	<tr valign="top">
							        	<th scope="row">Minify external CSS</th>
							        	<td><input type="checkbox" name="minify_external_css" value="1" <?php checked(1, $options['minify_external_css'], true); ?>/></td>
							        </tr>

							        <tr valign="top">
							        	<th scope="row">Combine CSS</th>
							        	<td><input type="checkbox" name="combine_css" value="1" <?php checked(1, $options['combine_css'], true); ?>/></td>
							        </tr>

							        <tr valign="top">
							        	<th scope="row">Defer CSS</th>
							        	<td><input type="checkbox" name="defer_css" value="1" <?php checked(1, $options['defer_css'], true); ?>/></td>
							        </tr>

									<tr valign="top">
										<th scope="row">Remove queries from CSS</th>
										<td><input type="checkbox" name="remove_css_queries" value="1" <?php checked(1, $options['remove_css_queries'], true); ?>/></td>
									</tr>

							    	<tr>
							    		<th valign="top" colspan="2">
							    			<h3>Managing Javascript</h3>
							    		</th>
							    	</tr>

							    	<tr valign="top">
							        	<th scope="row">Minify inline JS</th>
							        	<td><input type="checkbox" name="minify_iniline_js" value="1" <?php checked(1, $options['minify_iniline_js'], true); ?>/></td>
							        </tr>

							    	<tr valign="top">
							        	<th scope="row">Minify external JS</th>
							        	<td><input type="checkbox" name="minify_external_js" value="1" <?php checked(1, $options['minify_external_js'], true); ?>/></td>
							        </tr>

							        <tr valign="top">
							        	<th scope="row">Combine JS files</th>
							        	<td><input type="checkbox" name="combine_js" value="1" <?php checked(1, $options['combine_js'], true); ?>/></td>
							        </tr>

							        <tr valign="top">
							        	<th scope="row">Defer JS</th>
							        	<td><input type="checkbox" name="defer_js" value="1" <?php checked(1, $options['defer_js'], true); ?>/></td>
							        </tr>

									<tr valign="top">
										<th scope="row">Remove queries from JS</th>
										<td><input type="checkbox" name="remove_js_queries" value="1" <?php checked(1, $options['remove_js_queries'], true); ?>/></td>
									</tr>
							    </table>
						    <?php
						    	submit_button();  
								} 
							?>

							<?php if($this->drTab() == "images"){?>
							    <table class="form-table">
							    	<tr>
							    		<th valign="top" colspan="2">
							    			<h3>Managing Media</h3>
							    		</th>
							    	</tr>
							    	<tr valign="top">
							        	<th scope="row">Lazy Load Images</th>
							        	<td><input type="checkbox" name="lazyload" value="1" <?php checked(1, $options['lazyload'], true); ?>/></td>
							        </tr>

							        <tr valign="top">
							        	<th scope="row">Optimize images</th>
							        	<td><input type="checkbox" name="optimize" value="1" <?php checked(1, $options['optimize'], true); ?>/></td>
							        </tr>
							    </table>
						    <?php
						    	submit_button();  
								} 
							?>

							<?php if($this->drTab() == "settings"){?>
							    <table class="form-table">
							    	<tr>
							    		<th valign="top" colspan="2">
							    			<h3>Managing Permissions</h3>
							    		</th>
							    	</tr>
							    	<tr valign="top">
							        	<th scope="row">Collect Analytics</th>
							        	<td>
							        		<input type="checkbox" name="collection" value="1" <?php checked(1, $options['collection'], true); ?>/>
							        		<br>

							        	</td>
							        </tr>
							        <tr valign="top">
							        	<th scope="row">Subscribe Newsletters</th>
							        	<td><input type="checkbox" name="newsletter" value="1" <?php checked(1, $options['newsletter'], true); ?>/></td>
							        </tr>
							    </table>
						    <?php
						    	submit_button(); 
								} 
							?>

							<?php if($this->drTab() == "contact"){?>
							    <p>
							    	Want to get further customizations? Contact us for further improvements.
							    </p>
						    <?php 
								} 
							?>

						</form>
					</div>
				</div>
			</div>

		<?php

	}
}

?>