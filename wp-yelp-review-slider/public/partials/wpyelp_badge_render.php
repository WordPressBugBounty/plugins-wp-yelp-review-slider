<?php
/**
 * Build and optionally emit the Yelp review badge (summary card).
 *
 * Expects in scope:
 *   $template_misc_array (array), $filtersource (string), $currentform (array of objects), $wpdb
 *
 * Sets in scope:
 *   $badgehtml (string), $wprev_badge_active (bool)
 *
 * When $wpyelp_badge_phase === 'open': echoes style, outer wrap, and left/above badge.
 * When $wpyelp_badge_phase === 'close': echoes right badge and closes outer wrap.
 *
 * @package WP_Yelp_Review
 */

if ( ! defined( 'WPINC' ) ) {
	exit;
}

if ( ! isset( $template_misc_array ) || ! is_array( $template_misc_array ) ) {
	$template_misc_array = array();
}
if ( ! isset( $template_misc_array['blocation'] ) ) {
	$template_misc_array['blocation'] = '';
}

$wprev_badge_active = ( $template_misc_array['blocation'] !== '' );

if ( ! $wprev_badge_active ) {
	$badgehtml = '';
	return;
}

if ( ! isset( $wpyelp_badge_phase ) ) {
	$wpyelp_badge_phase = 'open';
}

if ( $wpyelp_badge_phase === 'open' ) {

	if ( ! isset( $template_misc_array['bname'] ) ) { $template_misc_array['bname'] = ''; }
	if ( ! isset( $template_misc_array['bimgurl'] ) ) { $template_misc_array['bimgurl'] = ''; }
	if ( ! isset( $template_misc_array['bbtnurl'] ) ) { $template_misc_array['bbtnurl'] = ''; }
	if ( ! isset( $template_misc_array['bnameurl'] ) ) { $template_misc_array['bnameurl'] = ''; }
	if ( ! isset( $template_misc_array['bbtncolor'] ) ) { $template_misc_array['bbtncolor'] = ''; }
	if ( ! isset( $template_misc_array['bbkcolor'] ) ) { $template_misc_array['bbkcolor'] = ''; }
	if ( ! isset( $template_misc_array['bbradius'] ) ) { $template_misc_array['bbradius'] = ''; }
	if ( ! isset( $template_misc_array['bbwidth'] ) ) { $template_misc_array['bbwidth'] = '0'; }
	if ( ! isset( $template_misc_array['bbcolor'] ) ) { $template_misc_array['bbcolor'] = ''; }
	if ( ! isset( $template_misc_array['bdropsh'] ) ) { $template_misc_array['bdropsh'] = ''; }
	if ( ! isset( $template_misc_array['bcenter'] ) ) { $template_misc_array['bcenter'] = ''; }
	if ( ! isset( $template_misc_array['bhname'] ) ) { $template_misc_array['bhname'] = ''; }
	if ( ! isset( $template_misc_array['bhphoto'] ) ) { $template_misc_array['bhphoto'] = ''; }
	if ( ! isset( $template_misc_array['bhbased'] ) ) { $template_misc_array['bhbased'] = ''; }
	if ( ! isset( $template_misc_array['bhbtn'] ) ) { $template_misc_array['bhbtn'] = ''; }
	if ( ! isset( $template_misc_array['bhpow'] ) ) { $template_misc_array['bhpow'] = ''; }
	if ( ! isset( $template_misc_array['bshape'] ) ) { $template_misc_array['bshape'] = ''; }
	if ( ! isset( $template_misc_array['bobasedon'] ) ) { $template_misc_array['bobasedon'] = ''; }
	if ( ! isset( $template_misc_array['borevus'] ) ) { $template_misc_array['borevus'] = ''; }

	$businessname = $template_misc_array['bname'];
	$imageurl     = $template_misc_array['bimgurl'];
	$butnlinkurl  = $template_misc_array['bbtnurl'];
	$bnameurl     = $template_misc_array['bnameurl'];

	$badge_imgs_base = trailingslashit( wprev_yelp_plugin_url ) . 'public/partials/imgs/';
	$yelp_outline    = $badge_imgs_base . 'yelp_outline.png';
	if ( $imageurl === '' ) {
		$imageurl = $yelp_outline;
	}
	$powered_by_img = $yelp_outline;

	$bbtncolor        = class_exists( 'WP_Yelp_Review_Sanitize' ) ? WP_Yelp_Review_Sanitize::sanitize_css_color( $template_misc_array['bbtncolor'] ) : sanitize_text_field( $template_misc_array['bbtncolor'] );
	$bbackgroundcolor = class_exists( 'WP_Yelp_Review_Sanitize' ) ? WP_Yelp_Review_Sanitize::sanitize_css_color( $template_misc_array['bbkcolor'] ) : sanitize_text_field( $template_misc_array['bbkcolor'] );
	if ( $bbtncolor === '' ) { $bbtncolor = '#d32323'; }
	if ( $bbackgroundcolor === '' ) { $bbackgroundcolor = '#ffffff'; }
	$bborderradius = intval( $template_misc_array['bbradius'] );
	$bborderwidth  = absint( $template_misc_array['bbwidth'] );
	$bbordercolor  = '';
	if ( $template_misc_array['bbcolor'] !== '' ) {
		$bbordercolor = class_exists( 'WP_Yelp_Review_Sanitize' ) ? WP_Yelp_Review_Sanitize::sanitize_css_color( $template_misc_array['bbcolor'] ) : sanitize_text_field( $template_misc_array['bbcolor'] );
	}

	$bdropsh = esc_html( $template_misc_array['bdropsh'] );
	$bcenter = esc_html( $template_misc_array['bcenter'] );
	$bshape  = esc_html( $template_misc_array['bshape'] );

	$bhnameclass  = ( $template_misc_array['bhname'] === 'yes' ) ? 'badgehideclass' : '';
	$bhphotoclass = ( $template_misc_array['bhphoto'] === 'yes' ) ? 'badgehideclass' : '';
	$bhbasedclass = ( $template_misc_array['bhbased'] === 'yes' ) ? 'badgehideclass' : '';
	$bhbtnclass   = ( $template_misc_array['bhbtn'] === 'yes' ) ? 'badgehideclass' : '';
	$bhpowclass   = ( $template_misc_array['bhpow'] === 'yes' ) ? 'badgehideclass' : '';

	$badge_style = '';
	$badge_style .= 'a.wprev-yelp-wr-a {background: ' . $bbtncolor . ' !important;}';
	$badge_style .= 'a.wprev-yelp-wr-a:hover {background: ' . $bbtncolor . 'de !important;}';

	$badge_place_style = 'background: ' . $bbackgroundcolor . ' !important;border-radius:' . $bborderradius . 'px !important;';
	if ( $bborderwidth > 0 ) {
		if ( $bbordercolor === '' ) {
			$bbordercolor = '#eeeeee';
		}
		$badge_place_style .= 'border:' . $bborderwidth . 'px solid ' . $bbordercolor . ' !important;';
	} else {
		$badge_place_style .= 'border:none !important;';
	}
	$badge_style .= '.wprev-yelp-place {' . $badge_place_style . '}';

	if ( $bdropsh === 'yes' ) {
		$badge_style .= '.wprev-yelp-place {box-shadow: rgba(0, 0, 0, .08) 2px 2px 3px 0px !important;}';
	} else {
		$badge_style .= '.wprev-yelp-place {box-shadow: none !important;}';
	}
	if ( $bcenter === 'yes' && $template_misc_array['blocation'] !== 'abovewide' ) {
		$badge_style .= '.wprev-yelp-place {flex-direction: column !important;align-items: center !important;}';
		$badge_style .= '.wprev-yelp-right {display: flex!important;align-items: center!important;flex-direction: column!important;width: 100% !important;text-align: center !important;}';
		$badge_style .= '.wprev-yelp-name{margin-bottom: 3px !important;}';
		$badge_style .= '.wprev-yelp-powered,.wprev-yelp-wr {display: flex !important;justify-content: center !important;width: 100% !important;}';
		$badge_style .= '.wprev-yelp-powered img {margin-left: auto !important;margin-right: auto !important;}';
	}
	if ( $bshape === 'round' ) {
		$badge_style .= 'img.sprev-yelp-left-src {border-radius: 50% !important;}';
	}

	// Avg / total from crawler source values in wpyelp_total_averages.
	$templaceid   = isset( $filtersource ) ? $filtersource : '';
	$badgeavg     = '';
	$badgetotal   = '';
	$table_name_avg = $wpdb->prefix . 'wpyelp_total_averages';
	if ( $templaceid !== '' ) {
		$currentlocation = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT avg, total FROM $table_name_avg WHERE pagetype = %s AND btp_id = %s LIMIT 1",
				'Yelp',
				$templaceid
			)
		);
		if ( ! empty( $currentlocation ) ) {
			$badgeavg   = $currentlocation[0]->avg;
			$badgetotal = intval( $currentlocation[0]->total );
		}
	} else {
		$all_avgs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT avg, total FROM $table_name_avg WHERE pagetype = %s AND btp_type = %s",
				'Yelp',
				'page'
			)
		);
		if ( is_array( $all_avgs ) && count( $all_avgs ) === 1 ) {
			$badgeavg   = $all_avgs[0]->avg;
			$badgetotal = intval( $all_avgs[0]->total );
		}
	}

	if ( $template_misc_array['blocation'] === 'leftmid' || $template_misc_array['blocation'] === 'rightmid' ) {
		$badge_style .= '.wprev_outer_wb {align-items: center !important;}';
	}

	$wprev_outer_wb_class = 'wprev_outer_wb';
	if ( isset( $currentform[0]->style ) && (string) $currentform[0]->style === '6' ) {
		$badge_side_locations = array( 'left', 'right', 'leftmid', 'rightmid' );
		if ( in_array( $template_misc_array['blocation'], $badge_side_locations, true ) ) {
			$wprev_outer_wb_class .= ' wprev_badge_style6_side';
		}
	}

	if ( $template_misc_array['blocation'] === 'above' ) {
		$badge_style .= '.wprev_outer_wb {flex-direction: column !important;}.wprev_badge_div.badgeleft {margin-left: auto !important;margin-right: auto !important;}';
	}

	$badgeabovewide1     = '';
	$badgeabovewide2     = '';
	$badgeabovewideclose = '';
	if ( $template_misc_array['blocation'] === 'abovewide' ) {
		$badge_style        .= '.wprev_outer_wb {flex-direction: column !important;}.wprev_badge_div.badgeleft {margin-left: auto !important;margin-right: auto !important;}.wprev_badge_div.badgeleft {margin: 0px 46px !important;}.wprev-yelp-place {justify-content: space-between !important;align-items: center !important;}.wprev-yelp-leftboth {display: flex !important;}  @media only screen and (max-width: 600px) {.wprev-yelp-place {flex-direction: column;}}';
		$badgeabovewide1     = '<div class="wprev-yelp-leftboth">';
		$badgeabovewide2     = '<div class="wprev-yelp-right">';
		$badgeabovewideclose = '</div>';
	}

	$bimgsize = 50;
	if ( isset( $template_misc_array['bimgsize'] ) && $template_misc_array['bimgsize'] > 0 ) {
		$bimgsize     = absint( $template_misc_array['bimgsize'] );
		$badge_style .= 'img.sprev-yelp-left-src {min-width: ' . $bimgsize . 'px !important;min-height: ' . $bimgsize . 'px !important;}';
	}

	echo '<style>' . $badge_style . '</style>';
	echo '<div class="' . esc_attr( $wprev_outer_wb_class ) . '">';

	$basedontext = 'Based on <span class="wprev_btot">' . $badgetotal . '</span> reviews';
	if ( $template_misc_array['bobasedon'] !== '' ) {
		$basedontext = esc_html( $template_misc_array['bobasedon'] );
	}
	$basedontext = str_replace( '#', '<span class="wprev_btot">' . $badgetotal . '</span>', $basedontext );

	$reviewusontext = 'Review us on';
	if ( $template_misc_array['borevus'] !== '' ) {
		$reviewusontext = esc_html( $template_misc_array['borevus'] );
	}

	$badgehtml = '<div class="wprev-yelp-place">' . $badgeabovewide1
		. '<div class="wprev-yelp-left ' . $bhphotoclass . '"><img class="sprev-yelp-left-src" src="' . esc_url( $imageurl ) . '" alt="' . esc_attr( $businessname ) . '" width="' . $bimgsize . '" height="' . $bimgsize . '" title="' . esc_attr( $businessname ) . '"></div>'
		. '<div class="wprev-yelp-right"><div class="wprev-yelp-name ' . $bhnameclass . '"><a href="' . esc_url( $bnameurl ) . '" target="_blank" rel="nofollow noopener"><span class="wprev-businessname">' . esc_html( $businessname ) . '</span></a></div>'
		. '<div class="wprevstardiv"><span class="wprev-yelp-rating">' . esc_html( $badgeavg ) . '</span><span class="wprevpro_star_imgs_T1"><span class="starloc1 wprevpro_star_imgs wprevpro_star_imgsloc1">'
		. '<span class="svgicons svg-wprsp-star"></span><span class="svgicons svg-wprsp-star"></span><span class="svgicons svg-wprsp-star"></span><span class="svgicons svg-wprsp-star"></span><span class="svgicons svg-wprsp-star"></span>'
		. '</span></span></div>'
		. '<div class="wprev-yelp-basedon ' . $bhbasedclass . '">' . $basedontext . '</div>'
		. $badgeabovewideclose . $badgeabovewideclose . $badgeabovewide2
		. '<div class="wprev-yelp-powered ' . $bhpowclass . '"><img class="wprev-yelp-powered-img" src="' . esc_url( $powered_by_img ) . '" alt="powered by Yelp" width="102" height="20" title="powered by Yelp"></div>'
		. '<div class="wprev-yelp-wr ' . $bhbtnclass . '"><a class="wprev-yelp-wr-a" target="_blank" rel="nofollow noopener" href="' . esc_url( $butnlinkurl ) . '">' . $reviewusontext . '</a></div>'
		. '</div></div>';

	if ( in_array( $template_misc_array['blocation'], array( 'left', 'leftmid', 'above', 'abovewide' ), true ) ) {
		echo '<div class="wprev_badge_div badgeleft">';
		echo $badgehtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_* above.
		echo '</div>';
	}

} elseif ( $wpyelp_badge_phase === 'close' ) {

	if ( ! isset( $badgehtml ) ) {
		$badgehtml = '';
	}
	if ( in_array( $template_misc_array['blocation'], array( 'right', 'rightmid' ), true ) ) {
		echo '<div class="wprev_badge_div badgeright">';
		echo $badgehtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}
	echo '</div>';
}
