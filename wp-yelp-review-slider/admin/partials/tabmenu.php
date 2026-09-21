<?php
$urltrimmedtab = remove_query_arg( array(
	'page',
	'deleterev',
	'editrev',
	'hiderev',
	'newvalue',
	'opt_type',
	'_wpnonce',
	'taction',
	'tid',
	'sortby',
	'sortdir',
	'opt',
	'settings-updated',
	'pnum',
	'wpyelp_skip',
) );

$urlreviewlist    = esc_url( add_query_arg( 'page', 'wp_yelp-reviews', $urltrimmedtab ) );
$urltemplateposts = esc_url( add_query_arg( 'page', 'wp_yelp-templates_posts', $urltrimmedtab ) );
$urlgetyelp       = esc_url( add_query_arg( 'page', 'wp_yelp-get_yelp', $urltrimmedtab ) );
$urlwelcome       = esc_url( add_query_arg( 'page', 'wp_yelp-welcome', $urltrimmedtab ) );
$urlanalytics     = esc_url( add_query_arg( 'page', 'wp_yelp-analytics', $urltrimmedtab ) );
$urlbadges        = esc_url( add_query_arg( 'page', 'wp_yelp-badges', $urltrimmedtab ) );
$urlforms         = esc_url( add_query_arg( 'page', 'wp_yelp-forms', $urltrimmedtab ) );
$urlfloat         = esc_url( add_query_arg( 'page', 'wp_yelp-float', $urltrimmedtab ) );
$urlai            = esc_url( add_query_arg( 'page', 'wp_yelp-ai_analysis', $urltrimmedtab ) );
$probadge         = ' <span style="background: #ff6b35; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; margin-left: 4px;">PRO</span>';
$current_page     = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
?>
	<div class="w3-bar w3-border w3-white">
	<a href="<?php echo $urlwelcome; ?>" class="w3-bar-item w3-button <?php if ( $current_page === 'wp_yelp-welcome' ) { echo 'w3-greentrip'; } ?>"><i class="fa fa-home"></i> <?php _e( 'Welcome', 'wp-yelp-reviews' ); ?></a>
	<a href="<?php echo $urlgetyelp; ?>" class="w3-bar-item w3-button <?php if ( $current_page === 'wp_yelp-get_yelp' ) { echo 'w3-greentrip'; } ?>"><i class="fa fa-search"></i> <?php _e( 'Get Yelp Reviews', 'wp-yelp-reviews' ); ?></a>
	<a href="<?php echo $urlreviewlist; ?>" class="w3-bar-item w3-button <?php if ( $current_page === 'wp_yelp-reviews' ) { echo 'w3-greentrip'; } ?>"><i class="fa fa-list"></i> <?php _e( 'Review List', 'wp-yelp-reviews' ); ?></a>
	<a href="<?php echo $urltemplateposts; ?>" class="w3-bar-item w3-button <?php if ( $current_page === 'wp_yelp-templates_posts' ) { echo 'w3-greentrip'; } ?>"><i class="fa fa-commenting-o"></i> <?php _e( 'Templates', 'wp-yelp-reviews' ); ?></a>
	<a href="<?php echo $urlanalytics; ?>" class="w3-bar-item w3-button <?php if ( $current_page === 'wp_yelp-analytics' ) { echo 'w3-greentrip'; } ?>"><i class="fa fa-bar-chart"></i> <?php _e( 'Analytics', 'wp-yelp-reviews' ); ?></a>
	<a href="<?php echo $urlbadges; ?>" class="w3-bar-item w3-button <?php if ( $current_page === 'wp_yelp-badges' ) { echo 'w3-greentrip'; } ?>"><i class="fa fa-certificate"></i> <?php _e( 'Badges', 'wp-yelp-reviews' ); ?><?php echo $probadge; ?></a>
	<a href="<?php echo $urlforms; ?>" class="w3-bar-item w3-button <?php if ( $current_page === 'wp_yelp-forms' ) { echo 'w3-greentrip'; } ?>"><i class="fa fa-wpforms"></i> <?php _e( 'Forms', 'wp-yelp-reviews' ); ?><?php echo $probadge; ?></a>
	<a href="<?php echo $urlfloat; ?>" class="w3-bar-item w3-button <?php if ( $current_page === 'wp_yelp-float' ) { echo 'w3-greentrip'; } ?>"><i class="fa fa-arrows-alt"></i> <?php _e( 'Floats', 'wp-yelp-reviews' ); ?><?php echo $probadge; ?></a>
	<a href="<?php echo $urlai; ?>" class="w3-bar-item w3-button <?php if ( $current_page === 'wp_yelp-ai_analysis' ) { echo 'w3-greentrip'; } ?>"><i class="fa fa-cogs"></i> <?php _e( 'AI Analysis', 'wp-yelp-reviews' ); ?><?php echo $probadge; ?></a>
	<a href="https://wpreviewslider.com/" target="_blank" class="goprohbtntrip w3-bar-item w3-button"><i class="fa fa-external-link-square" aria-hidden="true"></i> <?php _e( 'Get Pro Version!', 'wp-yelp-reviews' ); ?></a>

	</div>
