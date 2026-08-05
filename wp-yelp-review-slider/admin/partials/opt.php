<?php

/**
 * Admin email opt-in (Brevo) for the free Yelp plugin.
 *
 * @package    WP_Yelp_Review_Slider
 * @subpackage WP_Yelp_Review_Slider/admin/partials
 */

// check user capabilities
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}

$current_user = wp_get_current_user();
$pluginname   = 'WP Yelp Reviews';
$skippage     = 'wp_yelp-welcome';
$brevolistid  = 14;
$choicemade   = false;

if ( isset( $_GET['wpyelp_skip'] ) && '1' === $_GET['wpyelp_skip'] ) {
	check_admin_referer( 'wpyelp_skip_optin' );
	update_option( 'wp_yelp_optin', 'skipped' );
	wp_safe_redirect( admin_url( 'admin.php?page=' . $skippage ) );
	exit;
}

if ( ! function_exists( 'wpyelp_send_brevo_optin' ) ) {
	/**
	 * Post admin email to Brevo via the LJ Apps free-plugin webhook.
	 *
	 * @param string $email               User email.
	 * @param string $firstname           First name.
	 * @param string $lastname            Last name.
	 * @param mixed  $is_marketing_allowed true|null for allow / opt-out.
	 * @param int    $brevolistid         Brevo list ID.
	 */
	function wpyelp_send_brevo_optin( $email, $firstname, $lastname, $is_marketing_allowed, $brevolistid ) {
		$payload = array(
			'type'    => 'plugin.premium.downloaded',
			'id'      => '123456789',
			'objects' => array(
				'user'    => array(
					'plugin_id'            => null,
					'user_id'              => null,
					'gross'                => 0,
					'is_marketing_allowed' => $is_marketing_allowed,
					'source'               => 0,
					'last_login_at'        => null,
					'email_status'         => null,
					'email'                => $email,
					'first'                => $firstname,
					'last'                 => $lastname,
					'picture'              => null,
					'ip'                   => '',
					'is_verified'          => true,
				),
				'install' => new stdClass(),
			),
			'is_live' => true,
		);

		$endpoint = 'https://phpstack-110055-3529608.cloudwaysapps.com/frwebhook_pro.php?freev=y&listid=' . intval( $brevolistid );

		wp_remote_post(
			$endpoint,
			array(
				'body'        => wp_json_encode( $payload ),
				'headers'     => array(
					'Content-Type' => 'application/json',
				),
				'timeout'     => 60,
				'redirection' => 5,
				'blocking'    => true,
				'httpversion' => '1.0',
				'sslverify'   => true,
				'data_format' => 'body',
			)
		);
	}
}

if ( isset( $_POST['wprevpro_submitoptform'] ) ) {
	check_admin_referer( 'wprevpro_submitoptform' );
	$choicemade = true;
	update_option( 'wp_yelp_optin', 'yes' );

	$firstname = $current_user->user_firstname;
	if ( $firstname === '' ) {
		$firstname = $current_user->display_name;
	}

	wpyelp_send_brevo_optin(
		$current_user->user_email,
		$firstname,
		$current_user->user_lastname,
		true,
		$brevolistid
	);
}

if ( isset( $_POST['wprevpro_submitoptformoptout'] ) ) {
	check_admin_referer( 'wprevpro_submitoptform' );
	$choicemade = true;
	update_option( 'wp_yelp_optin', 'no' );

	$firstname = $current_user->user_firstname;
	if ( $firstname === '' ) {
		$firstname = $current_user->display_name;
	}

	wpyelp_send_brevo_optin(
		$current_user->user_email,
		$firstname,
		$current_user->user_lastname,
		null,
		$brevolistid
	);
}

$optin_status = get_option( 'wp_yelp_optin', 'blank' );
?>
<div class="">
<h1></h1>
<div class="wrap" id="wp_rev_maindiv">

<img class="wprev_headerimg" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'logo.png' ); ?>">

<div id="fs_connect" class="fs-anonymous-disabled require-license-key">
	<div class="fs-header">
		<div class="fs-plugin-icon">
			<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'logo.png' ); ?>" width="50" height="50" alt="">
		</div>
	</div>
	<div class="fs-box-container">
		<div class="fs-content" style="<?php echo $choicemade ? 'display:none;' : ''; ?>">

		<p>Welcome to <b><?php echo esc_html( $pluginname ); ?></b>! Please let us know if you'd like us to contact you for security &amp; feature updates, educational content, and occasional offers. If you skip this, that’s okay! <?php echo esc_html( $pluginname ); ?> will still work just fine.</p>
		<br>

		<form name="optchoice" id="optchoice" action="<?php echo esc_url( admin_url( 'admin.php?page=wp_yelp-opt' ) ); ?>" method="post">
		<div class="optbuttons">

		<input style="<?php echo ( 'yes' === $optin_status ) ? 'display:none;' : ''; ?>" type="submit" name="wprevpro_submitoptform" id="wprevpro_submitoptform" class="button button-primary allowbutton" tabindex="1" value="Allow and Continue">

		<input style="<?php echo ( 'yes' !== $optin_status ) ? 'display:none;' : ''; ?>" type="submit" name="wprevpro_submitoptformoptout" id="wprevpro_submitoptformoptout" class="button button-primary allowbutton" tabindex="1" value="Opt Out">

		<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=wp_yelp-opt&wpyelp_skip=1' ), 'wpyelp_skip_optin' ) ); ?>" class="button button skipbutton" tabindex="1">Skip</a>

		</div>
			<?php wp_nonce_field( 'wprevpro_submitoptform' ); ?>
		</form>
		</div>

		<div class="fs-content" style="<?php echo $choicemade ? '' : 'display:none;'; ?>">

		<p>Great! You can come back to this page at any time and change your selection.</p>
		<br>

		<div class="optbuttons">

		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $skippage ) ); ?>" class="button button skipbutton" tabindex="1">Continue</a>

		</div>

		</div>

	</div>

</div>

</div>
