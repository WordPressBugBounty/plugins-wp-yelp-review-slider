<?php

/**
 * Provide a admin area view for the plugin
 *
 * Get Yelp Reviews — multiple sources with AJAX download per source.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/admin/partials
 */

// check user capabilities
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}

// Ensure crawls option exists and migrate legacy single URL.
$yelp_crawls = $this->wpyelp_get_crawls();

// Delete source if requested.
if ( isset( $_GET['ract'] ) && $_GET['ract'] === 'del' && isset( $_GET['pageid'] ) ) {
	check_admin_referer( 'wpyelp_del_source' );
	$del_pageid = sanitize_text_field( wp_unslash( $_GET['pageid'] ) );
	$this->wpyelp_delete_source( $del_pageid );
	$yelp_crawls = $this->wpyelp_get_crawls();
	add_settings_error( 'yelp-radio', 'wpyelp_message', __( 'Source deleted.', 'wp-yelp-reviews' ), 'updated' );
}

$del_base = wp_nonce_url(
	admin_url( 'admin.php?page=wp_yelp-get_yelp&ract=del' ),
	'wpyelp_del_source'
);
?>

<div class="">
<h1></h1>
<div class="wrap" id="wp_rev_maindiv">

<img class="wprev_headerimg" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'logo.png?v=' . $this->version ); ?>">
<?php
include 'tabmenu.php';
?>
	<div class="wpfbr_margin10">
		<div class="w3-col welcomediv w3-container w3-white w3-border w3-border-light-gray2 w3-round-small">

			<p>
				<?php esc_html_e( 'Add one or more Yelp business pages, then download reviews for each source. Please note that Yelp may not return all reviews.', 'wp-yelp-reviews' ); ?>
			</p>

			<?php settings_errors( 'yelp-radio' ); ?>

			<div id="wpyelp_add_source_box" style="margin-bottom:20px;">
				<h3><?php esc_html_e( 'Add Yelp Source', 'wp-yelp-reviews' ); ?></h3>
				<p>
					<label for="yelp_new_url"><strong><?php esc_html_e( 'Business URL', 'wp-yelp-reviews' ); ?></strong></label><br>
					<input type="text" id="yelp_new_url" class="regular-text" style="width:100%;max-width:700px;" placeholder="https://www.yelp.com/biz/earth-and-stone-wood-fired-pizza-huntsville-2" value="">
				</p>
				<p>
					<label for="yelp_new_name"><strong><?php esc_html_e( 'Business Name (optional)', 'wp-yelp-reviews' ); ?></strong></label><br>
					<input type="text" id="yelp_new_name" class="regular-text" style="width:100%;max-width:400px;" placeholder="<?php esc_attr_e( 'Leave blank to detect from URL', 'wp-yelp-reviews' ); ?>" value="">
				</p>
				<p>
					<button type="button" id="wpyelp_add_source" class="button button-primary"><?php esc_html_e( 'Add Source', 'wp-yelp-reviews' ); ?></button>
					<span id="wpyelp_add_loader" class="wprevloader" style="display:none;width:20px;height:20px;border-width:3px;vertical-align:middle;margin-left:8px;"></span>
					<span id="wpyelp_add_msg" style="margin-left:8px;"></span>
				</p>
				<p class="description">
					<?php esc_html_e( 'Example:', 'wp-yelp-reviews' ); ?>
					https://www.yelp.com/biz/earth-and-stone-wood-fired-pizza-huntsville-2
				</p>
			</div>

			<div id="currentsources">
				<style>
				#currentsources table { max-width: 100%; table-layout: fixed; word-wrap: break-word; }
				#currentsources table td { word-wrap: break-word; overflow-wrap: break-word; }
				#currentsources .yelp-source-msg { display: inline-block; margin-left: 8px; }
				#currentsources .buttonloader2.wprevloader { display:none; width:20px; height:20px; border-width:3px; vertical-align:middle; margin-left:6px; }
				</style>
				<table class="w3-table-all wpfbr_mb15 w3-white w3-border w3-border-light-gray2 w3-round-small">
					<tr>
						<th><?php esc_html_e( 'Business Name', 'wp-yelp-reviews' ); ?></th>
						<th><?php esc_html_e( 'Page ID', 'wp-yelp-reviews' ); ?></th>
						<th><?php esc_html_e( 'Source Avg / Total', 'wp-yelp-reviews' ); ?></th>
						<th><?php esc_html_e( 'Action', 'wp-yelp-reviews' ); ?></th>
					</tr>
					<tbody id="wpyelp_sources_tbody">
					<?php
					$source_count = 0;
					if ( is_array( $yelp_crawls ) ) {
						foreach ( $yelp_crawls as $pageid => $source ) {
							if ( ! is_array( $source ) || $pageid === '' || $pageid === '0' ) {
								continue;
							}
							$source_count++;
							$bname  = isset( $source['businessname'] ) ? $source['businessname'] : '';
							$url    = isset( $source['url'] ) ? $source['url'] : '';
							$avg    = isset( $source['avg'] ) ? $source['avg'] : '';
							$total  = isset( $source['total'] ) ? $source['total'] : '';
							$avg_total_label = ( $avg !== '' || $total !== '' ) ? esc_html( $avg ) . ' / ' . esc_html( $total ) : '—';
							$del_url = add_query_arg( 'pageid', rawurlencode( $pageid ), $del_base );
							?>
							<tr data-pageid="<?php echo esc_attr( $pageid ); ?>">
								<td>
									<?php echo esc_html( $bname ); ?>
									<?php if ( $url ) : ?>
										<br><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View on Yelp', 'wp-yelp-reviews' ); ?></a>
									<?php endif; ?>
								</td>
								<td><?php echo esc_html( $pageid ); ?></td>
								<td class="yelp-source-stats"><?php echo $avg_total_label; ?></td>
								<td>
									<button type="button" class="button button-primary downloadrevs" data-pageid="<?php echo esc_attr( $pageid ); ?>"><?php esc_html_e( 'Download Reviews', 'wp-yelp-reviews' ); ?></button>
									<span class="buttonloader2 wprevloader"></span>
									<a class="button" style="color:#a00;" href="<?php echo esc_url( $del_url ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Delete this source and its reviews?', 'wp-yelp-reviews' ) ); ?>');"><?php esc_html_e( 'Delete', 'wp-yelp-reviews' ); ?></a>
									<span class="yelp-source-msg"></span>
								</td>
							</tr>
							<?php
						}
					}
					if ( $source_count === 0 ) {
						echo '<tr class="wpyelp-no-sources"><td colspan="4">' . esc_html__( 'No sources yet. Add a Yelp business URL above.', 'wp-yelp-reviews' ) . '</td></tr>';
					}
					?>
					</tbody>
				</table>
			</div>

			<p><b><?php esc_html_e( 'The Pro version can download all your reviews with avatars from multiple locations and check for new reviews daily!', 'wp-yelp-reviews' ); ?></b></p>

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
