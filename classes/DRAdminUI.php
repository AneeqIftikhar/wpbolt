<?php
/* 
This class takes care of the admin UI 
*/

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
							    			<strong>Managing CSS</strong>
							    		</th>
							    	</tr>
							    	<tr valign="top">
							        	<th scope="row">Minify styles</th>
							        	<td><input type="checkbox" name="minify_styles" value="1" <?php checked(1, $options['minify_styles'], true); ?>/></td>
							        </tr>

							        <tr valign="top">
							        	<th scope="row">Combine styles</th>
							        	<td><input type="checkbox" name="combine_styles" value="1" <?php checked(1, $options['combine_styles'], true); ?>/></td>
							        </tr>

							    	<tr>
							    		<th valign="top" colspan="2">
							    			<strong>Managing Scripts</strong>
							    		</th>
							    	</tr>
							    	<tr valign="top">
							        	<th scope="row">Minify scripts</th>
							        	<td><input type="checkbox" name="minify_scripts" value="1" <?php checked(1, $options['minify_scripts'], true); ?>/></td>
							        </tr>

							        <tr valign="top">
							        	<th scope="row">Combine scripts</th>
							        	<td><input type="checkbox" name="combine_scripts" value="1" <?php checked(1, $options['combine_scripts'], true); ?>/></td>
							        </tr>

							        <tr valign="top">
							        	<th scope="row">Defer scripts</th>
							        	<td><input type="checkbox" name="defer_scripts" value="1" <?php checked(1, $options['defer_scripts'], true); ?>/></td>
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
							    			<strong>Managing Media</strong>
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

							    	<tr valign="top">
							        	<th scope="row">Compress images</th>
							        	<td><input type="checkbox" name="compress" value="1" <?php checked(1, $options['compress'], true); ?>/></td>
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
							    			<strong>Managing Permissions</strong>
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