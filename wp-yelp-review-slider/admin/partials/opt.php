<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Yelp_Review_Slider
 * @subpackage WP_Yelp_Review_Slider/admin/partials
 */
 
     // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
	
	$current_user = wp_get_current_user();
	$pluginname = "WP Yelp Reviews";
	$logo = plugin_dir_url( __FILE__ ) . 'logo.png';
	$skippage = "wp_yelp-get_yelp";
	$optname = "wp_yelp_optin";
	$brevolistid = 14;
	$choicemade = false;
	
	if (isset($_POST['wprevpro_submitoptform'])){

		check_admin_referer( 'wprevpro_submitoptform');
		$choicemade = true;
		//update option
		update_option( 'wp_yelp_optin', "yes" );
		
		$firstname = esc_html( $current_user->user_firstname );
		if($firstname==""){
			$firstname = esc_html( $current_user->display_name  );
		}
		
		//add email to brevo list and then send them to the $skippage.
		$json='
		{"type": "plugin.premium.downloaded",
		 "id": "123456789",
		  "objects": {
			"user": {
			  "plugin_id": null,
			  "user_id": null,
			  "gross": 0,
			  "is_marketing_allowed": true,
			  "source": 0,
			  "last_login_at": null,
			  "email_status": null,
			  "email": "'.esc_html( $current_user->user_email ).'",
			  "first": "'.$firstname.'",
			  "last": "'.esc_html( $current_user->user_lastname ).'",
			  "picture": null,
			  "ip": "",
			  "is_verified": true
			},
			"install": {
			}
		  },
		  "is_live": true
		}';
		
		$endpoint = 'https://phpstack-110055-3529608.cloudwaysapps.com/frwebhook_pro.php?freev=y&listid='.$brevolistid;

		$options = [
			'body'        => $json,
			'headers'     => [
				'Content-Type' => 'application/json',
			],
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'data_format' => 'body',
		];

		wp_remote_post( $endpoint, $options );
		

	}
	
	if (isset($_POST['wprevpro_submitoptformoptout'])){

		check_admin_referer( 'wprevpro_submitoptform');
		$choicemade = true;
		//update option
		update_option( 'wp_yelp_optin', "no" );
		
		//set marketing allowed to no
		$firstname = esc_html( $current_user->user_firstname );
		if($firstname==""){
			$firstname = esc_html( $current_user->display_name  );
		}
		
		//add email to brevo list and then send them to the $skippage.
		$json='
		{"type": "plugin.premium.downloaded",
		 "id": "123456789",
		  "objects": {
			"user": {
			  "plugin_id": null,
			  "user_id": null,
			  "gross": 0,
			  "is_marketing_allowed": null,
			  "source": 0,
			  "last_login_at": null,
			  "email_status": null,
			  "email": "'.esc_html( $current_user->user_email ).'",
			  "first": "'.$firstname.'",
			  "last": "'.esc_html( $current_user->user_lastname ).'",
			  "picture": null,
			  "ip": "",
			  "is_verified": true
			},
			"install": {
			}
		  },
		  "is_live": true
		}';
		
		$endpoint = 'https://phpstack-110055-3529608.cloudwaysapps.com/frwebhook_pro.php?freev=y&listid='.$brevolistid;

		$options = [
			'body'        => $json,
			'headers'     => [
				'Content-Type' => 'application/json',
			],
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'data_format' => 'body',
		];

		wp_remote_post( $endpoint, $options );
		

	}
	
	//echo get_option('wp_yelp_optin',"no");
?>
<h1></h1>
<div class="wrap wp_yelp-settings">

<div id="fs_connect" class="wrap fs-anonymous-disabled require-license-key">
        <div class="fs-header">
            <!--			<b class="fs-site-icon"><i class="dashicons dashicons-wordpress-alt"></i></b>-->
            <div class="fs-plugin-icon">
	<img src="https://wordpress-117036-4772548.cloudwaysapps.com/wp-content/plugins/wp-review-slider-pro/admin/partials/logo_star.png" width="50" height="50">
</div>        </div>
        <div class="fs-box-container">
		<div class="fs-content" style="<?php if($choicemade==true){echo "display:none;";}?>">
            
		<p>Welcome to <b><?php echo $pluginname;?></b>! Please let us know if you'd like us to contact you for security &amp; feature updates, educational content, and occasional offers. If you skip this, that’s okay! <?php echo $pluginname;?> will still work just fine.
						</p>
						<br>
						
		<form name="optchoice" id="optchoice" action="?page=wp_yelp-opt" method="post">
		<div class='optbuttons'>
		
		<input style="<?php if(get_option('wp_yelp_optin',"no")=="yes"){echo "display:none;";}?>" type="submit" name="wprevpro_submitoptform" id="wprevpro_submitoptform" class="button button-primary allowbutton" tabindex="1" value="Allow and Continue">

		<input style="<?php if(get_option('wp_yelp_optin',"no")!="yes"){echo "display:none;";}?>" type="submit" name="wprevpro_submitoptformoptout" id="wprevpro_submitoptformoptout" class="button button-primary allowbutton" tabindex="1" value="Opt Out">


		<a href="?page=<?php echo $skippage;?>" class="button button skipbutton" tabindex="1" type="submit">Skip</a>
		
		</div>
			<?php 
		//security nonce
		wp_nonce_field( 'wprevpro_submitoptform');
		?>
		</form>
		</div>
		
		<div class="fs-content" style="<?php if($choicemade==false){echo "display:none;";}?>">
            
		<p>Great! You can come back to this page at any time and change your selection. 
						</p>
						<br>

		<div class='optbuttons'>
		
		<a href="?page=<?php echo $skippage;?>" class="button button skipbutton" tabindex="1" type="submit">Continue</a>
		
		</div>

		</div>
            		
			

		</div>

	</div>
	

	
	
<?php 

?>
<div class="wpfbr_margin10">

</div>

</div>

	

