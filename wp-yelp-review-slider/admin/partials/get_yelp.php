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
<div class="wrap wp_yelp-settings" id="">
	<h1><img src="<?php echo plugin_dir_url( __FILE__ ) . 'logo.png'; ?>"></h1>
<?php 
include("tabmenu.php");
?>
<div class="wpyelp_margin10">

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
		<p><i>Note: It may take a little time after you hit the Save button to download your reviews.</i></p>
	</form>
	<?php 
// show error/update messages
		settings_errors('yelp-radio');

?>

</div>

</div>

	

