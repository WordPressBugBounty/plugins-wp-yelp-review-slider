<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/admin/partials
 */
 
     // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
	
	    // wordpress will add the "settings-updated" $_GET parameter to the url
		//https://freegolftracker.com/blog/wp-admin/admin.php?settings-updated=true&page=wp_yelp-reviews
    if (isset($_GET['settings-updated'])) {
        // add settings saved message with the class of "updated"
        add_settings_error('yelp-radio', 'wpyelp_message', __('Settings Saved', 'wp-yelp-review-slider'), 'updated');
    }

	if(isset($this->errormsg)){
		add_settings_error('yelp-radio', 'wpyelp_message', __($this->errormsg, 'wp-yelp-review-slider'), 'error');
	}
?>

<div class="">
<h1></h1>
<div class="wrap" id="wp_rev_maindiv">

<img class="wprev_headerimg" src="<?php echo plugin_dir_url( __FILE__ ) . 'logo.png?v='.$this->version; ?>">
<?php 
include("tabmenu.php");
?>	
	<div class="wpfbr_margin10">
		<div class="w3-col welcomediv w3-container w3-white w3-border w3-border-light-gray2 w3-round-small">

			<form action="options.php" method="post">
		<?php
		// output security fields for the registered setting "wp_yelp-get_yelp"
		settings_fields('wp_yelp-get_yelp');
		// output setting sections and their fields
		// (sections are registered for "wp_yelp-get_yelp", each field is registered to a specific section)
		do_settings_sections('wp_yelp-get_yelp');
		// output save settings button
		submit_button('Save Settings & Download');
		?>
				<p><i><?php _e('Note: It may take a little time after you hit the Save button to download your reviews.', 'wp-yelp-reviews'); ?></i></p>
				<p><b><?php _e('The Pro version can download all your reviews with avatars from multiple locations and check for new reviews daily!', 'wp-yelp-reviews'); ?></b></p>
			</form>
			<?php 
				// show error/update messages
				settings_errors('yelp-radio');
			?>

		</div>
	</div>
	</div>
	</div>

	<div id="popup_info" class="popup-wrapper wpyelp_hide">
	  <div class="popup-content">
		<div class="popup-title">
		  <button type="button" class="popup-close">&times;</button>
		  <h3 id="popup_titletext"></h3>
		</div>
		<div class="popup-body">
		  <div id="popup_bobytext1"></div>
		  <div id="popup_bobytext2"></div>
		</div>
	  </div>
	</div>
	


