<?php
/**
 * Shared template helper functions for public review templates (Style 6, media, avatars).
 *
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/public/partials
 */

class WP_Yelp_Template_Functions {

	/**
	 * Format reviewer name based on template_misc lastnameformat.
	 * Defaults to full name when unset.
	 *
	 * @param object $review Review row.
	 * @param array  $template_misc_array Decoded template_misc.
	 * @return string
	 */
	public function wprevpro_get_reviewername( $review, $template_misc_array ) {
		$tempreviewername = stripslashes( strip_tags( $review->reviewer_name ) );
		$words            = explode( ' ', $tempreviewername );
		$firstname        = isset( $words[0] ) ? $words[0] : $tempreviewername;

		if ( ! isset( $template_misc_array['lastnameformat'] ) || $template_misc_array['lastnameformat'] === '' ) {
			$template_misc_array['lastnameformat'] = 'show';
		}

		if ( $template_misc_array['lastnameformat'] === 'hide' ) {
			$tempreviewername = $firstname;
		} elseif ( $template_misc_array['lastnameformat'] === 'initial' ) {
			$tempreviewername = $firstname;
			if ( isset( $words[1] ) && $words[1] !== '' ) {
				$tempreviewername .= ' ' . strtoupper( substr( $words[1], 0, 1 ) ) . '.';
			}
		}

		return $tempreviewername;
	}

	/**
	 * Build a local initials avatar as a data-URI SVG (no external service).
	 *
	 * @param string $name Reviewer name.
	 * @param int    $size Pixel size.
	 * @return string data:image/svg+xml;base64,... URL
	 */
	public function wprev_get_initials_avatar_url( $name, $size = 100 ) {
		$name = trim( wp_strip_all_tags( (string) $name ) );
		$size = absint( $size );
		if ( $size < 1 ) {
			$size = 100;
		}
		if ( $size > 500 ) {
			$size = 500;
		}

		$words = preg_split( '/\s+/', $name );
		if ( is_array( $words ) && count( $words ) >= 2 ) {
			$initials = strtoupper( substr( $words[0], 0, 1 ) . substr( $words[ count( $words ) - 1 ], 0, 1 ) );
		} elseif ( $name !== '' ) {
			$initials = strtoupper( substr( $name, 0, 1 ) );
		} else {
			$initials = 'U';
		}

		$hash       = md5( $name !== '' ? $name : 'U' );
		$background = '#' . substr( $hash, 0, 6 );
		$r          = hexdec( substr( $hash, 0, 2 ) );
		$g          = hexdec( substr( $hash, 2, 2 ) );
		$b          = hexdec( substr( $hash, 4, 2 ) );
		$brightness = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;
		if ( $brightness > 200 ) {
			$background = sprintf( '#%02x%02x%02x', max( 0, $r - 50 ), max( 0, $g - 50 ), max( 0, $b - 50 ) );
		}

		$font_size = (int) round( $size * 0.4 );
		$svg       = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '">';
		$svg      .= '<rect width="100%" height="100%" fill="' . $background . '"/>';
		$svg      .= '<text x="50%" y="50%" dy=".1em" fill="#ffffff" font-family="Arial,Helvetica,sans-serif" font-size="' . $font_size . '" font-weight="bold" text-anchor="middle" dominant-baseline="middle">' . htmlspecialchars( $initials, ENT_QUOTES, 'UTF-8' ) . '</text>';
		$svg      .= '</svg>';

		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}

	/**
	 * Escape an image src that may be https or a data URI.
	 *
	 * @param string $url Image URL.
	 * @return string
	 */
	public function wprev_esc_avatar_src( $url ) {
		if ( strpos( (string) $url, 'data:' ) === 0 ) {
			return esc_attr( $url );
		}
		return esc_url( $url );
	}

	/**
	 * Get media HTML for a review. Yelp only stores full-size media URLs, which
	 * double as the thumbnail. Defaults to showing media unless explicitly off.
	 *
	 * @param object $review Review row.
	 * @param array  $template_misc_array Decoded template_misc.
	 * @return string
	 */
	public function wprevpro_get_media( $review, $template_misc_array ) {
		$media = '';
		if ( ! isset( $template_misc_array['showmedia'] ) || $template_misc_array['showmedia'] === '' ) {
			$template_misc_array['showmedia'] = 'yes';
		}
		if ( $template_misc_array['showmedia'] === 'yes' && isset( $review->mediaurlsarrayjson ) ) {
			$mediaurls = stripslashes( $review->mediaurlsarrayjson );
			if ( $mediaurls !== '' ) {
				$mediaurlsarray = json_decode( $mediaurls, true );
				if ( is_array( $mediaurlsarray ) ) {
					$mediaurlsarray = array_values( array_filter( $mediaurlsarray ) );
					if ( count( $mediaurlsarray ) > 0 ) {
						$media = '<div class="wprev_media_div ' . count( $mediaurlsarray ) . '">';
						$n     = 0;
						foreach ( $mediaurlsarray as $urlvalue ) {
							if ( $urlvalue !== '' ) {
								$urlvalue  = esc_url( $urlvalue );
								$thumburl  = $urlvalue;
								$tempclass = ( stripos( $urlvalue, 'youtu' ) === false ) ? 'notyoutu' : 'youtu';
								$media    .= '<a class="wprev_media_img_a ' . $tempclass . '" href="' . $urlvalue . '" data-lity-desc="Review media"><img src="' . $thumburl . '" class="wprev_media_img" alt="media thumbnail ' . $n . '"></a>';
							}
							$n++;
						}
						$media .= '</div>';
					}
				}
			}
		}
		return $media;
	}

}
