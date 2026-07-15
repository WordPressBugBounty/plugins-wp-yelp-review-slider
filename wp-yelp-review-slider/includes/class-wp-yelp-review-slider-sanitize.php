<?php

/**
 * Shared sanitization helpers for template CSS / badge colors.
 *
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/includes
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class WP_Yelp_Review_Sanitize {

	/**
	 * Sanitize a value destined for use inside a CSS declaration (e.g. color).
	 *
	 * @param string $value Raw color/CSS value.
	 * @return string Safe CSS value, or '' if it cannot be made safe.
	 */
	public static function sanitize_css_color( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		if ( preg_match( '/[<>"\'`;{}\\\\()]/', $value ) && ! self::is_css_color_function( $value ) ) {
			return '';
		}

		if ( preg_match( '/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value ) ) {
			return $value;
		}

		if ( self::is_css_color_function( $value ) ) {
			return $value;
		}

		if ( preg_match( '/^[a-zA-Z]+$/', $value ) ) {
			return $value;
		}

		return '';
	}

	/**
	 * Whether a value is a safe rgb/rgba/hsl/hsla functional color notation.
	 *
	 * @param string $value Raw value.
	 * @return bool
	 */
	private static function is_css_color_function( $value ) {
		return (bool) preg_match(
			'/^(rgb|rgba|hsl|hsla)\(\s*[0-9.,%\s\/]+\)$/i',
			trim( (string) $value )
		);
	}
}
