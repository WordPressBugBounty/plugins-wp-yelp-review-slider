<?php
/**
 * Editor script dependencies for the Yelp Reviews block.
 *
 * @package WP_Yelp_Review_Slider
 */

return array(
	'dependencies' => array(
		'wp-block-editor',
		'wp-blocks',
		'wp-components',
		'wp-element',
		'wp-i18n',
		'wp-server-side-render',
	),
	'version'      => (string) filemtime( __DIR__ . '/index.js' ),
);
