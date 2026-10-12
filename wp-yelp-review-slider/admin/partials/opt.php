<?php

/**
 * Admin email opt-in (Brevo) for the free Yelp plugin.
 *
 * Allow, Opt Out, and Skip are handled on admin_init before this template renders.
 *
 * @package    WP_Yelp_Review_Slider
 * @subpackage WP_Yelp_Review_Slider/admin/partials
 */

if ( ! current_user_can( 'manage_options' ) ) {
	return;
}

$pluginname   = 'WP Yelp Reviews';
$optin_status = get_option( 'wp_yelp_optin', 'blank' );
$is_opted_in  = ( 'yes' === $optin_status );
$skip_url     = wp_nonce_url( admin_url( 'admin.php?page=wp_yelp-opt&wpyelp_skip=1' ), 'wpyelp_skip_optin' );
?>
<div class="wrap">
<h1 class="screen-reader-text"><?php esc_html_e( 'Get Started', 'wp-yelp-review-slider' ); ?></h1>

<div id="fs_connect">
	<div class="fs-header">
		<div class="fs-plugin-icon">
			<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'logo.png' ); ?>" alt="">
		</div>
	</div>
	<div class="fs-box-container">
		<div class="fs-content">
			<?php if ( $is_opted_in ) : ?>
				<h2><?php esc_html_e( 'You are opted in', 'wp-yelp-review-slider' ); ?></h2>
				<p><?php esc_html_e( 'You will get email notifications for security & feature updates, educational content, and occasional offers. You can opt out at any time.', 'wp-yelp-review-slider' ); ?></p>
			<?php else : ?>
				<h2><?php esc_html_e( 'Never miss an important update', 'wp-yelp-review-slider' ); ?></h2>
				<p><?php esc_html_e( 'Opt in to get email notifications for security & feature updates, educational content, and occasional offers.', 'wp-yelp-review-slider' ); ?></p>
			<?php endif; ?>
		</div>
		<div class="fs-actions">
			<?php if ( ! $is_opted_in ) : ?>
				<a id="skip_activation" href="<?php echo esc_url( $skip_url ); ?>" class="button button-secondary" tabindex="2"><?php esc_html_e( 'Skip', 'wp-yelp-review-slider' ); ?></a>
			<?php endif; ?>
			<form action="<?php echo esc_url( admin_url( 'admin.php?page=wp_yelp-opt' ) ); ?>" method="post">
				<?php wp_nonce_field( 'wpyelp_submitoptform' ); ?>
				<?php if ( $is_opted_in ) : ?>
					<button type="submit" name="wpyelp_submitoptformoptout" id="wpyelp_submitoptformoptout" class="button button-secondary" tabindex="1" value="1"><?php esc_html_e( 'Opt Out', 'wp-yelp-review-slider' ); ?></button>
				<?php else : ?>
					<button type="submit" name="wpyelp_submitoptform" id="wpyelp_submitoptform" class="button button-primary" tabindex="1" value="1"><?php esc_html_e( 'Allow & Continue', 'wp-yelp-review-slider' ); ?></button>
				<?php endif; ?>
			</form>
		</div>
		<?php if ( ! $is_opted_in ) : ?>
			<div class="fs-permissions">
				<a class="fs-trigger wp-core-ui" href="#" tabindex="1"><?php
					printf(
						/* translators: %s: plugin name */
						esc_html__( 'This will allow %s to', 'wp-yelp-review-slider' ),
						'<nobr class="button-link">' . esc_html( $pluginname ) . '</nobr>'
					);
				?><b class="fs-arrow"></b></a>
				<ul>
					<li class="fs-permission fs-user">
						<i class="dashicons dashicons-admin-users"></i>
						<div class="fs-permission-description">
							<span class="fs-tooltip-trigger"><?php esc_html_e( 'View Basic Profile Info', 'wp-yelp-review-slider' ); ?><i class="dashicons dashicons-editor-help"><span class="fs-tooltip"><?php esc_html_e( 'Never miss important updates, get security warnings before they become public knowledge, and receive notifications about special offers and awesome new features.', 'wp-yelp-review-slider' ); ?></span></i></span>
							<p><?php esc_html_e( 'Your WordPress user\'s: first & last name, and email address', 'wp-yelp-review-slider' ); ?></p>
						</div>
					</li>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var trigger = document.querySelector('#fs_connect .fs-trigger');
	if (!trigger) {
		return;
	}
	trigger.addEventListener('click', function (event) {
		event.preventDefault();
		if (trigger.parentNode) {
			trigger.parentNode.classList.toggle('is-open');
		}
	});
});
</script>
