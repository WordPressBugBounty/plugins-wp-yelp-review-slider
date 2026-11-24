<?php

/**
 * Provide a admin area view for the plugin
 *
 * @link       http://ljapps.com
 * @since      1.0.0
 *
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/admin/partials
 */
 
     // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
?>

<div class="">
<h1></h1>
<div class="wrap" id="wp_rev_maindiv">

<img class="wprev_headerimg" src="<?php echo plugin_dir_url( __FILE__ ) . 'logo.png?v='.$this->version; ?>">
<?php 
include("tabmenu.php");
?>
<?php $wpyelp_wizard_done = WP_Yelp_Review_Wizard::is_done(); ?>

<div class="welcomecontainer wpfbr_margin10 w3-row-padding w3-section w3-stretch">

<div class="w3-col s12 m12 l4 w3-container ">
<div class="welcomediv w3-white w3-border w3-border-light-gray2 w3-round-small">
	<h3><?php _e('Welcome!', 'wp-yelp-reviews'); ?> </h3>
	<p><?php _e('Thank you for being an awesome WP Review Slider customer! If you have trouble, please don\'t hesitate to contact me. Follow the Wizard or use the steps below to get started.', 'wp-yelp-reviews'); ?> </p>
	<a class="button button-primary wpyelp-wizard-cta__btn" href="<?php echo esc_url( WP_Yelp_Review_Wizard::url() ); ?>"><?php echo esc_html( $wpyelp_wizard_done ? __( 'Run the Setup Wizard', 'wp-yelp-review-slider' ) : __( 'Start the Setup Wizard', 'wp-yelp-review-slider' ) ); ?></a>
	<h3><?php _e('Getting Started Steps:', 'wp-yelp-reviews'); ?> </h3>
	<p><?php _e('1) Use the "Get Yelp Reviews" page to download your reviews and save them to your database.', 'wp-yelp-reviews'); ?></p>
	<p><?php _e('2) Once downloaded, the reviews should show up on the "Review List" page of the plugin.', 'wp-yelp-reviews'); ?> </p>
	<p><?php _e('3) Create a Review Slider or Grid for your site on the "Templates" page. By default the review template will show all your reviews; you can use the filters to only show the reviews you want.', 'wp-yelp-reviews'); ?> </p>
	
	<p><?php _e('If you have any trouble please check the', 'wp-yelp-reviews'); ?> <a href="https://wordpress.org/support/plugin/wp-yelp-review-slider/" target="_blank"><?php _e('Support Forum', 'wp-yelp-reviews'); ?></a> <?php _e('first. If you want to contact me privately you can use the form on my website', 'wp-yelp-reviews'); ?> <a href="https://wpreviewslider.com/contact/"><?php _e('here', 'wp-yelp-reviews'); ?></a>. <?php _e('I\'m always happy to help!', 'wp-yelp-reviews'); ?></p>
	<p><?php _e('Thanks!', 'wp-yelp-reviews'); ?><br>Josh<br><?php _e('Developer/Creator', 'wp-yelp-reviews'); ?> </p>

</div>
</div>
<div class="w3-col s12 m12 l8 welcomediv w3-container ">
<div class="welcomediv w3-white w3-border w3-border-light-gray2 w3-round-small">
<?php include plugin_dir_path( __FILE__ ) . 'other_plugins.php'; ?>

</div>
</div>

</div>

<div id="reviewdiv" class="welcomecontainer wpfbr_margin10 w3-row-padding w3-section w3-stretch wpfbr_margin10">
<div class="w3-col s12 m12 w3-container ">
<div class="welcomediv w3-white w3-border w3-border-light-gray2 w3-round-small">
<h5><?php _e('As a thank you for trying my free plugin, here\'s a special promo code to save 15% when you go Pro!', 'wp-yelp-reviews'); ?></h5>
<code>WPPRO15</code>

<h5><?php _e('Some feedback from over 10k+ happy Pro customers:', 'wp-yelp-reviews'); ?></h5>

<div class="w3_wprs-row">
							<div class="w3_wprs-col s4">
							  <style>.wpyelp_t1_DIV_2::after{ border-top: 30px solid #fdfdfd; }.wpyelp_t1_DIV_1 {margin: 5px;}a {
    text-decoration: none;
}</style>
							  <div class="w3_wprs-col">
							  <div class="wpyelp_t1_DIV_1">	
							  <div class="wpyelp_t1_DIV_2 wprev_preview_bg1 wprev_preview_bradius" style="border-radius: 0px; background: rgb(253, 253, 253);"><p class="wpyelp_t1_P_3 wprev_preview_tcolor1" style="color: rgb(85, 85, 85);"><span class="wpyelp_star_imgs"><img src="https://ljapps.com/wp-content/plugins/wp-review-slider-pro-premium/public/partials/imgs/stars_5_yellow.png" alt="">&nbsp;&nbsp;</span><?php _e('Great for my site! Good choice of styles & formats, easy to use. Show cases our 5* reviews from Yelp easy to update. Good all round plugin.', 'wp-yelp-reviews'); ?></p>									</div><span class="wpyelp_t1_A_8"><img src="https://s3-us-west-2.amazonaws.com/freemius/plugins//reviews/c8174af85095ea546c03cddd103abfd2.jpg" alt="thumb" class="wpyelp_t1_IMG_4"></span> <span class="wpyelp_t1_SPAN_5 wprev_preview_tcolor2" style="color: rgb(85, 85, 85);">Antony Bowers<br>Director, <a href="https://www.sweetfantasies.co.uk" target="_blank">Sweet Fantasies Cakes </a></span>								
							  </div>								
							  </div>
							</div>
							<div class="w3_wprs-col s4">
							  <div class="w3_wprs-col">							
							  <div class="wpyelp_t1_DIV_1">									
							  <div class="wpyelp_t1_DIV_2 wprev_preview_bg1 wprev_preview_bradius" style="border-radius: 0px; background: rgb(253, 253, 253);">										<p class="wpyelp_t1_P_3 wprev_preview_tcolor1" style="color: rgb(85, 85, 85);">											<span class="wpyelp_star_imgs"><img src="https://ljapps.com/wp-content/plugins/wp-review-slider-pro-premium/public/partials/imgs/stars_5_yellow.png" alt="">&nbsp;&nbsp;</span><?php _e('Great product, great support! Love this product and the support received has been amazing and fast.', 'wp-yelp-reviews'); ?></p>									</div><span class="wpyelp_t1_A_8"><img src="https://wpreviewslider.com/wp-content/uploads/wprevslider/avatars/1633774408_188.jpg" alt="thumb" class="wpyelp_t1_IMG_4"></span> <span class="wpyelp_t1_SPAN_5 wprev_preview_tcolor2" style="color: rgb(85, 85, 85);">Russ Kemp<br>Owner, <a href="https://www.russkempphotography.com" target="_blank">Russ Kemp Photography </a></span>								</div>								
							  </div>
							</div>
						  <div class="w3_wprs-col s4">
							  <div class="w3_wprs-col">							
							  <div class="wpyelp_t1_DIV_1">
							  <div class="wpyelp_t1_DIV_2 wprev_preview_bg1 wprev_preview_bradius" style="border-radius: 0px; background: rgb(253, 253, 253);">										<p class="wpyelp_t1_P_3 wprev_preview_tcolor1" style="color: rgb(85, 85, 85);">											<span class="wpyelp_star_imgs"><img src="https://ljapps.com/wp-content/plugins/wp-review-slider-pro-premium/public/partials/imgs/stars_5_yellow.png" alt="">&nbsp;&nbsp;</span><b><?php _e('Wow this thing really works!', 'wp-yelp-reviews'); ?></b> <?php _e('I\'m really happy with this plug-in. It\'s doing exactly what it supposed to do. I even needed a little bit of help and got it quickly. Highly recommend!', 'wp-yelp-reviews'); ?>		</p>									</div><span class="wpyelp_t1_A_8"><img src="https://wpreviewslider.com/wp-content/uploads/wprevslider/avatars/1649464747_442.jpg" alt="thumb" class="wpyelp_t1_IMG_4"></span> <span class="wpyelp_t1_SPAN_5 wprev_preview_tcolor2" style="color: rgb(85, 85, 85);">Andrea Barnes<br>Developer, <a href="https://websitessandiego.com" target="_blank">Websites San Diego </a> </span>								</div>								
							  </div>
						  </div>
					</div>
<br>
<a href="https://wpreviewslider.com/pricing/#customerfeedback" target="_blank" class="w3-button w3-round w3-border w3-greentrip w3-margin-bottom w3-margin-top"><?php _e('Read More Pro Version Feedback Here', 'wp-yelp-reviews'); ?></a>

</div>

	</div>
	</div>
	
	
</div>
	</div>

