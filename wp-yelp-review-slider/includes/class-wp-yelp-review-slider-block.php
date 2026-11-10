<?php
/**
 * Gutenberg block for inserting a saved review template.
 *
 * @package WP_Yelp_Review_Slider
 */

/**
 * Registers the Yelp Reviews block and renders the selected template.
 *
 * @since 9.6
 */
class WP_Yelp_Review_Slider_Block {

	/**
	 * Editor script handle generated from the block name.
	 *
	 * @var string
	 */
	const EDITOR_SCRIPT_HANDLE = 'wp-yelp-review-slider-template-editor-script';

	/**
	 * Register the block from block.json.
	 *
	 * @since 9.6
	 */
	public function register_block() {
		if ( ! function_exists( 'register_block_type_from_metadata' ) ) {
			return;
		}

		register_block_type_from_metadata(
			wprev_yelp_plugin_dir . 'blocks/template',
			array(
				'render_callback' => array( $this, 'render_block' ),
			)
		);

		$style_handle = 'wp-yelp-review-slider-template-editor-style';
		if ( wp_style_is( $style_handle, 'registered' ) ) {
			wp_styles()->registered[ $style_handle ]->ver = $this->asset_version( 'blocks/template/editor.css' );
		}
	}

	/**
	 * Pass saved templates to the block editor.
	 *
	 * @since 9.6
	 */
	public function enqueue_editor_data() {
		static $added = false;

		if ( $added || ! wp_script_is( self::EDITOR_SCRIPT_HANDLE, 'registered' ) ) {
			return;
		}

		$added = true;

		wp_add_inline_script(
			self::EDITOR_SCRIPT_HANDLE,
			'window.wprevYelpReviewsBlock = ' . wp_json_encode( $this->editor_config() ) . ';',
			'before'
		);
	}

	/**
	 * Load the front-end template assets in the editor canvas.
	 *
	 * @since 9.6
	 */
	public function enqueue_preview_assets() {
		if ( ! is_admin() ) {
			return;
		}

		$public = new WP_Yelp_Review_Public( 'wp-yelp-review-slider', '9.6' );
		$public->enqueue_styles();
		$public->enqueue_scripts();

		if ( ! wp_style_is( 'wp-yelp-review-slider-lity', 'registered' ) ) {
			wp_register_style(
				'wp-yelp-review-slider-lity',
				wprev_yelp_plugin_url . '/public/css/lity.min.css',
				array(),
				$this->asset_version( 'public/css/lity.min.css' )
			);
		}
		wp_enqueue_style( 'wp-yelp-review-slider-lity' );

		if ( ! wp_script_is( 'wp-yelp-review-slider-lity', 'registered' ) ) {
			wp_register_script(
				'wp-yelp-review-slider-lity',
				wprev_yelp_plugin_url . '/public/js/lity.min.js',
				array( 'jquery' ),
				$this->asset_version( 'public/js/lity.min.js' ),
				true
			);
		}
		wp_enqueue_script( 'wp-yelp-review-slider-lity' );

		wp_enqueue_script(
			'wp-yelp-review-slider-block-preview',
			wprev_yelp_plugin_url . '/public/js/wprev-block-preview.js',
			array( 'jquery', 'wp-yelp-review-slider_plublic', 'wp-yelp-review-slider_unslider-min', 'wp-yelp-review-slider-lity' ),
			$this->asset_version( 'public/js/wprev-block-preview.js' ),
			true
		);
	}

	/**
	 * Cache-busting version for a plugin file.
	 *
	 * @since 9.6
	 *
	 * @param string $relative Path relative to the plugin root.
	 * @return string
	 */
	private function asset_version( $relative ) {
		$path = wprev_yelp_plugin_dir . ltrim( $relative, '/' );
		$time = file_exists( $path ) ? filemtime( $path ) : false;

		return $time ? (string) $time : '9.6';
	}

	/**
	 * Render the selected template on the front end.
	 *
	 * @since 9.6
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render_block( $attributes ) {
		$template_id = isset( $attributes['templateId'] ) ? absint( $attributes['templateId'] ) : 0;
		if ( $template_id < 1 ) {
			return '';
		}

		$html = $this->render_template( $template_id );
		if ( '' === trim( $html ) ) {
			return '';
		}

		$wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
			? get_block_wrapper_attributes()
			: 'class="wp-block-wp-yelp-review-slider-template"';

		return '<div ' . $wrapper_attributes . '>' . $html . '</div>';
	}

	/**
	 * Build the template markup through the same display file as the shortcode.
	 *
	 * @since 9.6
	 *
	 * @param int $template_id Template ID.
	 * @return string
	 */
	private function render_template( $template_id ) {
		$display_file = wprev_yelp_plugin_dir . 'public/partials/wp-yelp-review-slider-public-display.php';
		
		// The display partial reads $a['tid'], the same contract as the shortcode.
		$a = array(
			'tid' => (string) $template_id,
		);

		ob_start();
		include $display_file;
		return (string) ob_get_clean();
	}

	/**
	 * Data used by the editor template dropdown.
	 *
	 * @since 9.6
	 *
	 * @return array{templates:array<int,array{id:int,title:string}>,templatesUrl:string,editUrl:string}
	 */
	private function editor_config() {
		$edit_url = '';
		if ( current_user_can( 'manage_options' ) ) {
			// Keep raw ampersands. wp_nonce_url() HTML-escapes them, which turns taction into amp;taction.
			$edit_url = add_query_arg(
				array(
					'page'     => 'wp_yelp-templates_posts',
					'taction'  => 'edit',
					'_wpnonce' => wp_create_nonce( 'tedit_' ),
				),
				admin_url( 'admin.php' )
			);
		}

		return array(
			'templates'    => $this->get_templates(),
			'templatesUrl' => admin_url( 'admin.php?page=wp_yelp-templates_posts' ),
			'editUrl'      => $edit_url,
		);
	}

	/**
	 * Saved post/page templates for the editor dropdown.
	 *
	 * Widget templates are omitted. That template type is being phased out.
	 *
	 * @since 9.6
	 *
	 * @return array<int,array{id:int,title:string}>
	 */
	private function get_templates() {
		global $wpdb;

		$table = $wpdb->prefix . 'wpyelp_post_templates';
		$rows  = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, title FROM {$table} WHERE ( template_type IS NULL OR template_type != %s ) AND title != %s ORDER BY title ASC, id ASC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				'widget',
				''
			)
		);

		if ( ! is_array( $rows ) ) {
			return array();
		}

		$templates = array();
		foreach ( $rows as $row ) {
			$title = trim( wp_strip_all_tags( (string) $row->title ) );
			if ( '' === $title ) {
				continue;
			}
			$templates[] = array(
				'id'    => (int) $row->id,
				'title' => $title,
			);
		}

		return $templates;
	}
}
