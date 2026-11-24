<?php
/**
 * Setup wizard: from a Yelp business URL to reviews on a page.
 *
 * Steps: get reviews, choose a look, add to the site, finish. Every step uses
 * the same code as the regular screens (source download, template save, the
 * block), so a wizard-made template is an ordinary template.
 *
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/admin
 * @since      9.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup wizard screen and its ajax actions.
 */
class WP_Yelp_Review_Wizard {

	const PAGE         = 'wp_yelp-setup';
	const STATE_OPTION = 'wpyelp_wizard_state';
	const DONE_OPTION  = 'wpyelp_setup_done';
	const NONCE        = 'wpyelp_wizard';
	const TEXT_DOMAIN  = 'wp-yelp-review-slider';

	/**
	 * Admin class, for its source and template helpers.
	 *
	 * @var WP_Yelp_Review_Admin
	 */
	private $admin;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * @param WP_Yelp_Review_Admin $admin   Admin class instance.
	 * @param string               $version Plugin version.
	 */
	public function __construct( $admin, $version ) {
		$this->admin   = $admin;
		$this->version = $version;
	}

	/**
	 * Wizard URL.
	 *
	 * @return string
	 */
	public static function url() {
		return admin_url( 'admin.php?page=' . self::PAGE );
	}

	/**
	 * Whether the wizard has been finished at least once.
	 *
	 * @return bool
	 */
	public static function is_done() {
		return (bool) get_option( self::DONE_OPTION );
	}

	/**
	 * Hidden admin page (no menu item; opened from the Welcome screen).
	 */
	public function menu() {
		add_submenu_page( '', __( 'Set up WP Yelp Review Slider', self::TEXT_DOMAIN ), '', 'manage_options', self::PAGE, array( $this, 'render' ) );
	}

	/**
	 * Hidden pages have no menu title, so give the browser tab one.
	 *
	 * @param string $admin_title Full title.
	 * @param string $title       Page title.
	 * @return string
	 */
	public function admin_title( $admin_title, $title ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only screen check.
		if ( isset( $_GET['page'] ) && self::PAGE === $_GET['page'] && '' === (string) $title ) {
			return __( 'Set up WP Yelp Review Slider', self::TEXT_DOMAIN ) . $admin_title;
		}
		return $admin_title;
	}

	/**
	 * Keep the plugin's menu open, with Welcome highlighted, on the wizard screen.
	 *
	 * @param string $parent_file Parent menu slug.
	 * @return string
	 */
	public function parent_file( $parent_file ) {
		global $plugin_page, $submenu_file, $submenu;
		if ( self::PAGE === $plugin_page ) {
			// Core looks up the parent again right after this filter and finds this page under ''
			// (hidden pages live there), which would undo the highlight. Access is already checked.
			if ( isset( $submenu[''] ) ) {
				foreach ( $submenu[''] as $i => $item ) {
					if ( isset( $item[2] ) && self::PAGE === $item[2] ) {
						unset( $submenu[''][ $i ] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					}
				}
			}
			$submenu_file = 'wp_yelp-welcome'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Core's way to pick the highlighted submenu item.
			return 'wp_yelp-welcome';
		}
		return $parent_file;
	}

	/**
	 * Assets on the wizard screen only.
	 *
	 * @param string $hook Hook suffix.
	 */
	public function assets( $hook ) {
		if ( 'admin_page_' . self::PAGE !== $hook ) {
			return;
		}

		// Front-end template CSS/JS, the lightbox, and window.wprevInitReviewSliders for the live preview.
		$block = new WP_Yelp_Review_Slider_Block();
		$block->enqueue_preview_assets();
		// The public class only loads the CSS of styles some saved template already uses; the preview can show either.
		foreach ( array( '1', '6' ) as $style ) {
			wp_enqueue_style( 'wp-yelp-review-slider-public_template' . $style, wprev_yelp_plugin_url . '/public/css/wprev-public_template' . $style . '.css', array(), $this->version );
		}

		wp_enqueue_style( 'wpyelp-setup-wizard', plugin_dir_url( __FILE__ ) . 'css/wpyelp_setup_wizard.css', array(), $this->asset_version( 'css/wpyelp_setup_wizard.css' ) );
		wp_enqueue_script( 'wpyelp-setup-wizard', plugin_dir_url( __FILE__ ) . 'js/wpyelp_setup_wizard.js', array( 'jquery', 'wp-yelp-review-slider-block-preview' ), $this->asset_version( 'js/wpyelp_setup_wizard.js' ), true );
		wp_localize_script(
			'wpyelp-setup-wizard',
			'wpyelpWizard',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( self::NONCE ),
				'state'   => $this->js_state(),
				'i18n'    => array(
					'stepOf'         => __( 'Step %1$d of %2$d', self::TEXT_DOMAIN ),
					'continue'       => __( 'Continue', self::TEXT_DOMAIN ),
					'finish'         => __( 'Finish', self::TEXT_DOMAIN ),
					'needUrl'        => __( 'Paste your Yelp business page address first.', self::TEXT_DOMAIN ),
					'needReviews'    => __( 'Get your reviews first, or pick a Yelp page that already has reviews.', self::TEXT_DOMAIN ),
					'downloading'    => __( 'Downloading your reviews from Yelp (%s).', self::TEXT_DOMAIN ),
					'canContinue'    => __( 'Yelp can take from a few minutes to a couple of hours. You can keep going with setup now: your reviews show up on your site by themselves as soon as they arrive, even if you close this page.', self::TEXT_DOMAIN ),
					'previewWaiting' => __( 'Your reviews are still downloading from Yelp. This preview fills in when they arrive, and you can keep going in the meantime.', self::TEXT_DOMAIN ),
					'sampleNote'     => __( 'Sample reviews. Your own Yelp reviews take their place as soon as they arrive.', self::TEXT_DOMAIN ),
					'downloadingNote' => __( 'Reviews are downloading', self::TEXT_DOMAIN ),
					'savedNote'      => __( '%s reviews saved', self::TEXT_DOMAIN ),
					'requestFailed'  => __( 'The request failed. Check your connection and try again.', self::TEXT_DOMAIN ),
					'crawlFailed'    => __( 'Yelp didn\'t send your reviews this time. Wait a few minutes, then click Get my reviews again. If it keeps happening, contact support with this message:', self::TEXT_DOMAIN ),
					'saving'         => __( 'Updating the preview…', self::TEXT_DOMAIN ),
					'noTemplate'     => __( 'The preview is still being made. Try again in a moment.', self::TEXT_DOMAIN ),
					'creatingPage'   => __( 'Creating the page…', self::TEXT_DOMAIN ),
					'copied'         => __( 'Copied', self::TEXT_DOMAIN ),
					'copy'           => __( 'Copy', self::TEXT_DOMAIN ),
				),
			)
		);
	}

	/**
	 * Cache-busting version for a file in admin/.
	 *
	 * @param string $relative Path relative to admin/.
	 * @return string
	 */
	private function asset_version( $relative ) {
		$path = plugin_dir_path( __FILE__ ) . $relative;
		return file_exists( $path ) ? (string) filemtime( $path ) : $this->version;
	}

	/* ---------- Facts the steps use ---------- */

	/**
	 * Saved wizard progress.
	 *
	 * @return array{pageid:string,tid:int,page_id:int}
	 */
	private function state() {
		$state = get_option( self::STATE_OPTION, array() );
		if ( ! is_array( $state ) ) {
			$state = array();
		}
		return array(
			'pageid'  => isset( $state['pageid'] ) ? (string) $state['pageid'] : '',
			'tid'     => isset( $state['tid'] ) ? absint( $state['tid'] ) : 0,
			'page_id' => isset( $state['page_id'] ) ? absint( $state['page_id'] ) : 0,
		);
	}

	/**
	 * Merge values into the saved wizard progress.
	 *
	 * @param array $values Keys of state().
	 */
	private function update_state( $values ) {
		update_option( self::STATE_OPTION, array_merge( $this->state(), $values ), false );
	}

	/**
	 * Number of saved reviews per Yelp page id.
	 *
	 * @return array<string,int>
	 */
	private function review_counts() {
		global $wpdb;
		$table = $wpdb->prefix . 'wpyelp_reviews';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Small count for the setup screen.
		$rows   = $wpdb->get_results( "SELECT pageid, COUNT(*) AS n FROM {$table} GROUP BY pageid" );
		$counts = array();
		foreach ( (array) $rows as $row ) {
			$counts[ (string) $row->pageid ] = (int) $row->n;
		}
		return $counts;
	}

	/**
	 * Saved Yelp sources with their review counts.
	 *
	 * @return array<int,array{pageid:string,name:string,url:string,avg:string,total:string,count:int}>
	 */
	private function sources() {
		$counts  = $this->review_counts();
		$sources = array();
		foreach ( (array) $this->admin->wpyelp_get_crawls() as $pageid => $source ) {
			if ( ! is_array( $source ) || '' === (string) $pageid || '0' === (string) $pageid ) {
				continue;
			}
			$task      = $this->admin->wpyelp_yelp_task( (string) $pageid );
			$sources[] = array(
				'pageid' => (string) $pageid,
				'name'   => isset( $source['businessname'] ) ? (string) $source['businessname'] : (string) $pageid,
				'url'    => isset( $source['url'] ) ? (string) $source['url'] : '',
				'avg'    => isset( $source['avg'] ) && '' !== $source['avg'] ? (string) round( (float) $source['avg'], 1 ) : '',
				'total'  => isset( $source['total'] ) ? (string) $source['total'] : '',
				'count'  => isset( $counts[ $pageid ] ) ? $counts[ $pageid ] : 0,
				// When a DataForSEO download for this page started (0 when none is waiting).
				'pending' => $task ? (int) $task['started'] : 0,
			);
		}
		return $sources;
	}

	/**
	 * A saved template's title, or '' when it no longer exists.
	 *
	 * @param int $tid Template id.
	 * @return string
	 */
	private function template_title( $tid ) {
		if ( $tid < 1 ) {
			return '';
		}
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$title = $wpdb->get_var( $wpdb->prepare( "SELECT title FROM {$wpdb->prefix}wpyelp_post_templates WHERE id = %d", $tid ) );
		return null === $title ? '' : (string) $title;
	}

	/**
	 * Links for a page the wizard created, if it still exists.
	 *
	 * @param int $page_id Page id.
	 * @return array{id:int,title:string,edit:string,view:string,status:string}|null
	 */
	private function page_info( $page_id ) {
		$post = $page_id ? get_post( $page_id ) : null;
		if ( ! $post || 'page' !== $post->post_type || 'trash' === $post->post_status ) {
			return null;
		}
		return array(
			'id'     => (int) $post->ID,
			'title'  => get_the_title( $post ),
			'edit'   => (string) get_edit_post_link( $post->ID, 'raw' ),
			'view'   => 'publish' === $post->post_status ? (string) get_permalink( $post ) : (string) get_preview_post_link( $post ),
			'status' => (string) $post->post_status,
		);
	}

	/**
	 * State handed to the wizard script.
	 *
	 * @return array
	 */
	private function js_state() {
		$state   = $this->state();
		$sources = $this->sources();
		$pageid  = $state['pageid'];
		$known   = wp_list_pluck( $sources, 'pageid' );
		if ( '' === $pageid || ! in_array( $pageid, $known, true ) ) {
			$pageid = '';
			foreach ( $sources as $source ) {
				if ( $source['count'] > 0 || $source['pending'] ) {
					$pageid = $source['pageid'];
					break;
				}
			}
		}
		$title = $this->template_title( $state['tid'] );
		return array(
			'sources' => $sources,
			'pageid'  => $pageid,
			'tid'     => '' === $title ? 0 : $state['tid'],
			'title'   => $title,
			'page'    => $this->page_info( $state['page_id'] ),
		);
	}

	/* ---------- Screen ---------- */

	/**
	 * Render the wizard.
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$d        = self::TEXT_DOMAIN;
		$state    = $this->js_state();
		$sources  = $state['sources'];
		$with     = array_filter(
			$sources,
			static function ( $source ) {
				return $source['count'] > 0 || $source['pending'];
			}
		);
		$prefill  = '';
		foreach ( $sources as $source ) {
			if ( 0 === $source['count'] && ! $source['pending'] && '' !== $source['url'] ) {
				$prefill = $source['url'];
				break;
			}
		}
		$steps    = array(
			'source' => __( 'Get reviews', $d ),
			'look'   => __( 'Choose a look', $d ),
			'place'  => __( 'Add to your site', $d ),
			'finish' => __( 'Finish', $d ),
		);
		$welcome  = admin_url( 'admin.php?page=wp_yelp-welcome' );
		$pro_url  = 'https://wpreviewslider.com/';
		?>
		<div class="wrap wpyelp-wiz" data-wpyelp-wizard>
			<hr class="wp-header-end">
			<div class="wpyelp-wiz__header">
				<img class="wpyelp-wiz__logo" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'partials/logo.png?v=' . $this->version ); ?>" alt="">
				<div class="wpyelp-wiz__titles">
					<h1><?php esc_html_e( 'Set up WP Yelp Review Slider', $d ); ?></h1>
					<p><?php esc_html_e( 'About three minutes. You can change all of it later.', $d ); ?></p>
				</div>
				<a class="button wpyelp-wiz__exit" href="<?php echo esc_url( $welcome ); ?>"><?php esc_html_e( 'Exit setup', $d ); ?></a>
			</div>

			<ol class="wpyelp-wiz__steps" aria-label="<?php esc_attr_e( 'Setup steps', $d ); ?>">
				<?php
				$number = 0;
				foreach ( $steps as $key => $label ) :
					++$number;
					?>
					<li class="wpyelp-wiz__stepitem" data-step-item="<?php echo esc_attr( $key ); ?>">
						<button type="button" class="wpyelp-wiz__stepbtn" data-goto="<?php echo esc_attr( $key ); ?>" disabled>
							<span class="wpyelp-wiz__stepnum" aria-hidden="true"><span class="wpyelp-wiz__n"><?php echo esc_html( number_format_i18n( $number ) ); ?></span><span class="dashicons dashicons-yes"></span></span>
							<span class="wpyelp-wiz__steplabel"><?php echo esc_html( $label ); ?></span>
						</button>
					</li>
				<?php endforeach; ?>
			</ol>

			<?php /* ---- Step 1: get reviews ---- */ ?>
			<section class="wpyelp-wiz__card" data-step="source" aria-labelledby="wpyelp-wiz-h-source">
				<h2 id="wpyelp-wiz-h-source" tabindex="-1"><?php esc_html_e( 'Get your Yelp reviews', $d ); ?></h2>
				<p class="wpyelp-wiz__lead"><?php esc_html_e( 'Paste the address of your business on Yelp. The plugin downloads your reviews and saves them on your site, so they load fast and keep showing even if Yelp is slow.', $d ); ?></p>

				<?php if ( $with ) : ?>
					<fieldset class="wpyelp-wiz__sources">
						<legend><?php esc_html_e( 'Which reviews do you want to show?', $d ); ?></legend>
						<?php foreach ( $with as $source ) : ?>
							<label class="wpyelp-wiz__source">
								<input type="radio" name="wpyelp-wiz-source" value="<?php echo esc_attr( $source['pageid'] ); ?>" <?php checked( $state['pageid'], $source['pageid'] ); ?>>
								<span>
									<strong><?php echo esc_html( $source['name'] ); ?></strong>
									<span class="wpyelp-wiz__muted" data-source-note="<?php echo esc_attr( $source['pageid'] ); ?>">
										<?php
										if ( $source['count'] > 0 ) {
											/* translators: %s: number of reviews. */
											echo esc_html( sprintf( _n( '%s review saved', '%s reviews saved', $source['count'], $d ), number_format_i18n( $source['count'] ) ) );
										} else {
											esc_html_e( 'Reviews are downloading', $d );
										}
										?>
									</span>
								</span>
							</label>
						<?php endforeach; ?>
						<label class="wpyelp-wiz__source">
							<input type="radio" name="wpyelp-wiz-source" value="new" <?php checked( '', $state['pageid'] ); ?>>
							<span><strong><?php esc_html_e( 'Add a different Yelp page', $d ); ?></strong></span>
						</label>
					</fieldset>
				<?php endif; ?>

				<div class="wpyelp-wiz__newsource" data-new-source <?php echo $with && '' !== $state['pageid'] ? 'hidden' : ''; ?>>
					<p>
						<label for="wpyelp-wiz-url"><strong><?php esc_html_e( 'Yelp business page address', $d ); ?></strong></label><br>
						<input type="url" id="wpyelp-wiz-url" class="wpyelp-wiz__input" placeholder="https://www.yelp.com/biz/your-business-city" value="<?php echo esc_attr( $prefill ); ?>" autocomplete="off" spellcheck="false">
						<span class="description"><?php esc_html_e( 'Find your business on yelp.com and copy the address from your browser. It starts with https://www.yelp.com/biz/.', $d ); ?></span>
					</p>
					<p>
						<label for="wpyelp-wiz-name"><strong><?php esc_html_e( 'Business name (optional)', $d ); ?></strong></label><br>
						<input type="text" id="wpyelp-wiz-name" class="wpyelp-wiz__input wpyelp-wiz__input--short" placeholder="<?php esc_attr_e( 'Taken from the address if blank', $d ); ?>">
					</p>
					<p class="wpyelp-wiz__actions">
						<button type="button" class="button button-primary" data-get-reviews><?php esc_html_e( 'Get my reviews', $d ); ?></button>
						<span class="spinner" data-get-spinner></span>
					</p>
				</div>

				<div class="wpyelp-wiz__status" data-source-status role="status" aria-live="polite" hidden></div>

				<p class="wpyelp-wiz__note">
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: %s: link to the Pro version. */
							__( 'The free version saves up to 10 recent Yelp reviews for each business page. %s downloads all of them and checks for new reviews every day.', $d ),
							'<a href="' . esc_url( $pro_url ) . '" target="_blank" rel="noopener">' . esc_html__( 'WP Review Slider Pro', $d ) . '</a>'
						)
					);
					?>
				</p>
			</section>

			<?php /* ---- Step 2: choose a look ---- */ ?>
			<section class="wpyelp-wiz__card" data-step="look" aria-labelledby="wpyelp-wiz-h-look" hidden>
				<h2 id="wpyelp-wiz-h-look" tabindex="-1"><?php esc_html_e( 'Choose how your reviews look', $d ); ?></h2>
				<p class="wpyelp-wiz__lead"><?php esc_html_e( 'The preview uses your own reviews and updates as you choose.', $d ); ?></p>

				<div class="wpyelp-wiz__controls">
					<fieldset class="wpyelp-wiz__group">
						<legend><?php esc_html_e( 'Style', $d ); ?></legend>
						<div class="wpyelp-wiz__segmented">
							<label><input type="radio" name="wpyelp-wiz-style" value="1" checked><span><?php esc_html_e( 'Speech bubble', $d ); ?></span></label>
							<label><input type="radio" name="wpyelp-wiz-style" value="6"><span><?php esc_html_e( 'Card', $d ); ?></span></label>
						</div>
					</fieldset>
					<fieldset class="wpyelp-wiz__group">
						<legend><?php esc_html_e( 'Layout', $d ); ?></legend>
						<div class="wpyelp-wiz__segmented">
							<label><input type="radio" name="wpyelp-wiz-layout" value="slider" checked><span><?php esc_html_e( 'Slider', $d ); ?></span></label>
							<label><input type="radio" name="wpyelp-wiz-layout" value="grid"><span><?php esc_html_e( 'Grid', $d ); ?></span></label>
						</div>
					</fieldset>
					<fieldset class="wpyelp-wiz__group">
						<legend><?php esc_html_e( 'Reviews per row', $d ); ?></legend>
						<div class="wpyelp-wiz__segmented">
							<?php foreach ( array( 1, 2, 3, 4 ) as $n ) : ?>
								<label><input type="radio" name="wpyelp-wiz-perrow" value="<?php echo esc_attr( $n ); ?>" <?php checked( 3, $n ); ?>><span><?php echo esc_html( number_format_i18n( $n ) ); ?></span></label>
							<?php endforeach; ?>
						</div>
					</fieldset>
					<fieldset class="wpyelp-wiz__group">
						<legend><?php esc_html_e( 'Number of rows', $d ); ?></legend>
						<div class="wpyelp-wiz__segmented">
							<?php foreach ( array( 1, 2, 3, 4 ) as $n ) : ?>
								<label><input type="radio" name="wpyelp-wiz-rows" value="<?php echo esc_attr( $n ); ?>" <?php checked( 1, $n ); ?>><span><?php echo esc_html( number_format_i18n( $n ) ); ?></span></label>
							<?php endforeach; ?>
						</div>
					</fieldset>
					<fieldset class="wpyelp-wiz__group">
						<legend><?php esc_html_e( 'Rating badge', $d ); ?></legend>
						<div class="wpyelp-wiz__segmented">
							<?php
							foreach ( array(
								'left'  => __( 'Left', $d ),
								'above' => __( 'Above', $d ),
								'right' => __( 'Right', $d ),
								'none'  => __( 'None', $d ),
							) as $value => $label ) :
								?>
								<label><input type="radio" name="wpyelp-wiz-badge" value="<?php echo esc_attr( $value ); ?>" <?php checked( 'above', $value ); ?>><span><?php echo esc_html( $label ); ?></span></label>
							<?php endforeach; ?>
						</div>
					</fieldset>
					<fieldset class="wpyelp-wiz__group">
						<legend><?php esc_html_e( 'Read more', $d ); ?></legend>
						<div class="wpyelp-wiz__segmented">
							<?php
							// Long reviews are cut after this many words with a "read more" link. 30 keeps the cards even.
							foreach ( array( 'off', '20', '30', '50' ) as $value ) :
								/* translators: %s: number of words. */
								$label = 'off' === $value ? __( 'Off', $d ) : sprintf( __( '%s words', $d ), number_format_i18n( (int) $value ) );
								?>
								<label><input type="radio" name="wpyelp-wiz-readmore" value="<?php echo esc_attr( $value ); ?>" <?php checked( '30', $value ); ?>><span><?php echo esc_html( $label ); ?></span></label>
							<?php endforeach; ?>
						</div>
					</fieldset>
				</div>
				<p class="wpyelp-wiz__hint" data-badge-hint hidden><?php esc_html_e( 'Tip: with the badge beside the reviews, 1 or 2 reviews per row fit best.', $d ); ?></p>

				<div class="wpyelp-wiz__preview" data-preview-wrap>
					<div class="wpyelp-wiz__previewbar">
						<strong><?php esc_html_e( 'Preview', $d ); ?></strong>
						<span class="spinner" data-preview-spinner></span>
						<span class="wpyelp-wiz__muted" data-preview-msg></span>
					</div>
					<div class="wpyelp-wiz__previewbody" data-preview></div>
				</div>
				<p class="wpyelp-wiz__note"><?php esc_html_e( 'More colors, filters, and settings are on the Templates page after setup. Pro adds 15 more styles.', $d ); ?></p>
			</section>

			<?php /* ---- Step 3: add to the site ---- */ ?>
			<section class="wpyelp-wiz__card" data-step="place" aria-labelledby="wpyelp-wiz-h-place" hidden>
				<h2 id="wpyelp-wiz-h-place" tabindex="-1"><?php esc_html_e( 'Add the reviews to your site', $d ); ?></h2>
				<p class="wpyelp-wiz__lead">
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: %s: template name. */
							__( 'Your reviews are saved as the template %s. Put it on a new page, or add it to a page you already have.', $d ),
							'<strong data-template-title></strong>'
						)
					);
					?>
				</p>

				<div class="wpyelp-wiz__choices">
					<div class="wpyelp-wiz__choice">
						<h3><?php esc_html_e( 'Create a new page', $d ); ?></h3>
						<p><?php esc_html_e( 'Makes a draft page with your reviews on it. Nothing is public until you publish it.', $d ); ?></p>
						<div data-page-form>
							<p>
								<label for="wpyelp-wiz-pagetitle"><strong><?php esc_html_e( 'Page title', $d ); ?></strong></label><br>
								<input type="text" id="wpyelp-wiz-pagetitle" class="wpyelp-wiz__input wpyelp-wiz__input--short" value="<?php esc_attr_e( 'Reviews', $d ); ?>">
							</p>
							<p class="wpyelp-wiz__actions">
								<button type="button" class="button button-primary" data-create-page><?php esc_html_e( 'Create the page', $d ); ?></button>
								<span class="spinner" data-page-spinner></span>
							</p>
						</div>
						<div class="wpyelp-wiz__status" data-page-status role="status" aria-live="polite" hidden></div>
						<p class="wpyelp-wiz__actions" data-page-links hidden>
							<a class="button button-primary" data-page-edit href="#" target="_blank" rel="noopener"><?php esc_html_e( 'Edit and publish the page', $d ); ?></a>
							<a class="button" data-page-view href="#" target="_blank" rel="noopener"><?php esc_html_e( 'Preview', $d ); ?></a>
						</p>
					</div>

					<div class="wpyelp-wiz__choice">
						<h3><?php esc_html_e( 'Add to a page you already have', $d ); ?></h3>
						<p>
							<?php
							echo wp_kses_post(
								sprintf(
									/* translators: 1: block name, 2: template name. */
									__( 'In the block editor, click the + button, search for %1$s, and pick %2$s from the block toolbar.', $d ),
									'<strong>' . esc_html__( 'WP Yelp Review Slider', $d ) . '</strong>',
									'<strong data-template-title></strong>'
								)
							);
							?>
						</p>
						<p><?php esc_html_e( 'Using the classic editor, a page builder, or a text widget? Paste this shortcode:', $d ); ?></p>
						<p class="wpyelp-wiz__shortcode">
							<code data-shortcode></code>
							<button type="button" class="button" data-copy-shortcode><?php esc_html_e( 'Copy', $d ); ?></button>
						</p>
					</div>
				</div>
			</section>

			<?php /* ---- Step 4: finish ---- */ ?>
			<section class="wpyelp-wiz__card" data-step="finish" aria-labelledby="wpyelp-wiz-h-finish" hidden>
				<h2 id="wpyelp-wiz-h-finish" tabindex="-1"><?php esc_html_e( 'You\'re all set', $d ); ?></h2>
				<p class="wpyelp-wiz__status is-info" data-finish-pending hidden>
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: %s: link to the Get Yelp Reviews page. */
							__( 'Your Yelp reviews are still downloading. They\'ll show on your site as soon as Yelp sends them, even if you close this page. You can see how it\'s going on the %s page.', $d ),
							'<a href="' . esc_url( admin_url( 'admin.php?page=wp_yelp-get_yelp' ) ) . '">' . esc_html__( 'Get Yelp Reviews', $d ) . '</a>'
						)
					);
					?>
				</p>
				<ul class="wpyelp-wiz__list">
					<li>
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: %s: link to the Templates page. */
								__( 'Change the colors, filters, and other settings of your template on the %s page.', $d ),
								'<a href="' . esc_url( admin_url( 'admin.php?page=wp_yelp-templates_posts' ) ) . '">' . esc_html__( 'Templates', $d ) . '</a>'
							)
						);
						?>
					</li>
					<li>
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: %s: link to the Review List page. */
								__( 'Hide any review you don\'t want to show on the %s page.', $d ),
								'<a href="' . esc_url( admin_url( 'admin.php?page=wp_yelp-reviews' ) ) . '">' . esc_html__( 'Review List', $d ) . '</a>'
							)
						);
						?>
					</li>
					<li>
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: %s: link to the Get Yelp Reviews page. */
								__( 'When you get new Yelp reviews, download them again on the %s page. Pro does this for you every day.', $d ),
								'<a href="' . esc_url( admin_url( 'admin.php?page=wp_yelp-get_yelp' ) ) . '">' . esc_html__( 'Get Yelp Reviews', $d ) . '</a>'
							)
						);
						?>
					</li>
				</ul>
				<p class="wpyelp-wiz__note">
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: 1: link to the Pro version, 2: promo code. */
							__( 'Want reviews from Google, Facebook, and 100+ other sites, more styles, and badges? Try %1$s and use code %2$s for 15%% off.', $d ),
							'<a href="' . esc_url( $pro_url ) . '" target="_blank" rel="noopener">' . esc_html__( 'WP Review Slider Pro', $d ) . '</a>',
							'<code>WPPRO15</code>'
						)
					);
					?>
				</p>
			</section>

			<div class="wpyelp-wiz__nav">
				<button type="button" class="button" data-prev><?php esc_html_e( 'Back', $d ); ?></button>
				<span class="wpyelp-wiz__count" data-count aria-live="polite"></span>
				<span class="wpyelp-wiz__navmsg" data-nav-msg role="alert"></span>
				<button type="button" class="button button-primary" data-next><?php esc_html_e( 'Continue', $d ); ?></button>
			</div>
		</div>
		<?php
	}

	/* ---------- Ajax ---------- */

	/**
	 * Common checks for the wizard's ajax actions.
	 */
	private function check_request() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You don\'t have permission to do this.', self::TEXT_DOMAIN ) ), 403 );
		}
		check_ajax_referer( self::NONCE, 'nonce' );
	}

	/**
	 * Add the Yelp page (if new) and download its reviews.
	 */
	public function ajax_get_reviews() {
		$this->check_request();

		$url  = isset( $_POST['url'] ) ? esc_url_raw( trim( wp_unslash( $_POST['url'] ) ) ) : '';
		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$d    = self::TEXT_DOMAIN;

		if ( ! $url || ! filter_var( $url, FILTER_VALIDATE_URL ) || false === stripos( (string) wp_parse_url( $url, PHP_URL_HOST ), 'yelp.' ) ) {
			wp_send_json_error( array( 'message' => __( 'That doesn\'t look like a Yelp business page. Copy the address that starts with https://www.yelp.com/biz/.', $d ) ) );
		}
		$pageid = $this->admin->wpyelp_extract_pageid_from_url( $url );
		if ( '' === $pageid ) {
			wp_send_json_error( array( 'message' => __( 'Couldn\'t find the business in that address. Copy it again from your Yelp business page.', $d ) ) );
		}

		$crawls = $this->admin->wpyelp_get_crawls();
		if ( ! isset( $crawls[ $pageid ] ) ) {
			$crawls[ $pageid ] = array(
				'pageid'       => $pageid,
				'businessname' => '' !== $name ? $name : $this->admin->wpyelp_extract_businessname_from_url( $url ),
				'url'          => strtok( $url, '?' ),
				'avg'          => '',
				'total'        => '',
			);
			$this->admin->wpyelp_save_crawls( $crawls );
		} elseif ( '' !== $name ) {
			$crawls[ $pageid ]['businessname'] = $name;
			$this->admin->wpyelp_save_crawls( $crawls );
		}
		// Starts the DataForSEO task and returns right away; the script then calls ajax_poll_reviews().
		$this->download_reply( $pageid, $this->admin->wpyelp_yelp_download_start( $pageid ) );
	}

	/**
	 * Check once on the download started by ajax_get_reviews().
	 */
	public function ajax_poll_reviews() {
		$this->check_request();
		$pageid = isset( $_POST['pageid'] ) ? sanitize_text_field( wp_unslash( $_POST['pageid'] ) ) : '';
		$crawls = $this->admin->wpyelp_get_crawls();
		if ( '' === $pageid || ! isset( $crawls[ $pageid ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Pick your Yelp page again.', self::TEXT_DOMAIN ) ) );
		}
		$this->download_reply( $pageid, $this->admin->wpyelp_yelp_download_poll( $pageid ) );
	}

	/**
	 * Answer a download start or check: still running, saved, or failed.
	 *
	 * @param string $pageid Page id.
	 * @param array  $result Result from wpyelp_yelp_download_start() or wpyelp_yelp_download_poll().
	 */
	private function download_reply( $pageid, $result ) {
		$d = self::TEXT_DOMAIN;
		if ( isset( $result['ack'] ) && 'pending' === $result['ack'] ) {
			$this->update_state( array( 'pageid' => $pageid ) );
			wp_send_json_success(
				array(
					'pending' => true,
					'pageid'  => $pageid,
					'sources' => $this->sources(),
					'started' => isset( $result['started'] ) ? (int) $result['started'] : time(),
					'status'  => isset( $result['status'] ) ? (string) $result['status'] : '',
				)
			);
		}

		$crawls = $this->admin->wpyelp_get_crawls();
		$source = isset( $crawls[ $pageid ] ) ? $crawls[ $pageid ] : array( 'businessname' => $pageid );
		$counts = $this->review_counts();
		$count  = isset( $counts[ $pageid ] ) ? $counts[ $pageid ] : 0;
		$data   = array(
			'pageid'  => $pageid,
			'sources' => $this->sources(),
			'count'   => $count,
		);

		if ( $count < 1 ) {
			$data['message'] = ! empty( $result['ackmsg'] ) && 'success' !== $result['ack']
				? wp_strip_all_tags( (string) $result['ackmsg'] )
				: __( 'No reviews were found for that page. Check the address, or try again in a few minutes.', $d );
			wp_send_json_error( $data );
		}

		$current = $source;
		$message = sprintf(
			/* translators: 1: number of reviews, 2: business name. */
			_n( '%1$s review from %2$s is saved on your site.', '%1$s reviews from %2$s are saved on your site.', $count, $d ),
			number_format_i18n( $count ),
			$current['businessname']
		);
		if ( ! empty( $current['avg'] ) && ! empty( $current['total'] ) ) {
			$message .= ' ' . sprintf(
				/* translators: 1: average rating, 2: total number of reviews on Yelp. */
				__( 'Yelp shows %1$s stars from %2$s reviews.', $d ),
				number_format_i18n( (float) $current['avg'], 1 ),
				number_format_i18n( (int) $current['total'] )
			);
		}
		if ( isset( $result['ack'] ) && 'error' === $result['ack'] && ! empty( $result['ackmsg'] ) ) {
			// Reviews saved earlier are still there, so the wizard can go on with them.
			$message = sprintf(
				/* translators: 1: error message, 2: the saved reviews sentence. */
				__( 'This download didn\'t finish (%1$s). %2$s', $d ),
				wp_strip_all_tags( (string) $result['ackmsg'] ),
				$message
			);
		}
		$data['message'] = $message;

		$this->update_state( array( 'pageid' => $pageid ) );
		wp_send_json_success( $data );
	}

	/**
	 * Create or update the wizard's template and return its preview.
	 */
	public function ajax_save_template() {
		$this->check_request();
		$d = self::TEXT_DOMAIN;

		$pageid  = isset( $_POST['pageid'] ) ? sanitize_text_field( wp_unslash( $_POST['pageid'] ) ) : '';
		$style   = isset( $_POST['style'] ) && '6' === $_POST['style'] ? '6' : '1';
		$slider  = ! isset( $_POST['layout'] ) || 'grid' !== $_POST['layout'];
		$per_row = isset( $_POST['per_row'] ) ? min( 4, max( 1, absint( $_POST['per_row'] ) ) ) : 3;
		$rows    = isset( $_POST['rows'] ) ? min( 4, max( 1, absint( $_POST['rows'] ) ) ) : 1;
		// Read more: 'off', or the number of words shown before the "read more" link.
		$readmore = isset( $_POST['read_more'] ) && in_array( $_POST['read_more'], array( 'off', '20', '30', '50' ), true ) ? $_POST['read_more'] : '30';
		// Badge position. "Above" is the full-width bar, which leaves every review card its full width.
		$badges  = array(
			'left'  => 'left',
			'above' => 'abovewide',
			'right' => 'right',
			'none'  => '',
		);
		$badge   = ( isset( $_POST['badge'] ) && isset( $badges[ $_POST['badge'] ] ) ) ? $badges[ $_POST['badge'] ] : 'abovewide';
		$tid     = isset( $_POST['tid'] ) ? absint( $_POST['tid'] ) : 0;

		$crawls = $this->admin->wpyelp_get_crawls();
		if ( '' === $pageid || ! isset( $crawls[ $pageid ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Pick your Yelp page in the first step.', $d ) ) );
		}
		if ( $tid && '' === $this->template_title( $tid ) ) {
			$tid = 0; // Deleted since; make a new one.
		}

		$source = $crawls[ $pageid ];
		$name   = ! empty( $source['businessname'] ) ? $source['businessname'] : $pageid;
		$url    = ! empty( $source['url'] ) ? $source['url'] : '';
		$counts = $this->review_counts();
		// Size the slider/grid for the reviews saved, or for the 10 the free version saves while they're still downloading.
		$count  = ( isset( $counts[ $pageid ] ) && $counts[ $pageid ] > 0 ) ? min( 10, $counts[ $pageid ] ) : 10;
		// A slide (or the grid) holds per_row x rows reviews; a slider gets enough slides for the rest.
		$slides = (int) ceil( $count / ( $per_row * $rows ) );

		// Colors match what the Templates page fills in for a new template of each style.
		$colors = '6' === $style
			? array( 'bradius' => '4', 'bg1' => '#fdfdfd', 'bg2' => '#ffffff', 't1' => '#555555', 't2' => '#555555' )
			: array( 'bradius' => '0', 'bg1' => '#ffffff', 'bg2' => '#ffffff', 't1' => '#777777', 't2' => '#555555' );

		$fields = array(
			'edittid'                        => $tid ? (string) $tid : '',
			/* translators: %s: business name. */
			'wpyelp_template_title'          => sprintf( __( '%s Yelp Reviews', $d ), $name ),
			'wpyelp_template_type'           => 'post',
			'wpyelp_template_style'          => $style,
			'wpyelp_t_display_num'           => (string) $per_row,
			'wpyelp_t_display_num_rows'      => (string) $rows,
			'wpyelp_t_display_order'         => 'newest',
			'wpyelp_t_hidenotext'            => 'yes',
			'wpyelp_template_css'            => '',
			'wpyelp_t_createslider'          => $slider ? 'yes' : 'no',
			'wpyelp_t_numslides'             => (string) min( 10, max( 2, $slides ) ),
			'wprevpro_t_read_more'           => 'off' === $readmore ? 'no' : 'yes',
			'wprevpro_t_read_more_text'      => __( 'read more', $d ),
			'wpyelp_t_min_rating'            => '1',
			'wpyelp_t_filtersource'          => $pageid,
			'wpyelp_template_misc_showstars' => 'yes',
			'wpyelp_template_misc_showdate'  => 'yes',
			'wpyelp_template_misc_bgcolor1'  => $colors['bg1'],
			'wpyelp_template_misc_bgcolor2'  => $colors['bg2'],
			'wpyelp_template_misc_tcolor1'   => $colors['t1'],
			'wpyelp_template_misc_tcolor2'   => $colors['t2'],
			'wpyelp_template_misc_tcolor3'   => '',
			'wpyelp_template_misc_bradius'   => $colors['bradius'],
			'wpyelp_t_showmedia'             => 'yes',
			'wpyelp_template_misc_verified'  => 'yes1',
			'wpyelp_template_misc_lastname'  => 'show',
			'wpyelp_template_misc_avataropt' => 'show',
			'wpyelp_template_misc_showicon'  => 'lin',
			'wpyelp_sliderautoplay'          => 'no',
			'wpyelp_t_slidespeed'            => '1',
			'wpyelp_t_slideautodelay'        => '5',
			'wpyelp_t_review_same_height'    => 'no',
			'wprevpro_t_read_more_num'       => 'off' === $readmore ? '30' : $readmore,
			'wpyelp_t_blocation'             => $badge,
			'wpyelp_t_bname'                 => $name,
			'wpyelp_t_bnameurl'              => $url,
			'wpyelp_t_bimgurl'               => wprev_yelp_plugin_url . '/public/partials/imgs/yelp_badge_icon.svg',
			'wpyelp_t_bshape'                => '',
			'wpyelp_t_bimgsize'              => '50',
			'wpyelp_t_bbtnurl'               => $url,
			'wpyelp_t_bbradius'              => '0',
			'wpyelp_t_bbwidth'               => '1',
			'wpyelp_t_bbtncolor'             => '#d32323',
			'wpyelp_t_bbkcolor'              => '#ffffff',
			'wpyelp_t_bbcolor'               => '#eeeeee',
			'wpyelp_t_bobasedon'             => __( 'Based on # reviews', $d ),
			'wpyelp_t_borevus'               => __( 'Review us on Yelp!', $d ),
		);

		// No reviews saved yet (Yelp is still sending them): preview the template with sample reviews.
		// The filters only last for this request, so the real template on the site is never affected.
		$sample = empty( $counts[ $pageid ] );
		if ( $sample ) {
			// Show no more samples than the template would show real reviews (one grid, or every slide).
			$shown   = $per_row * $rows * ( $slider ? (int) $fields['wpyelp_t_numslides'] : 1 );
			$samples = array_slice( $this->sample_reviews( $pageid, $name ), 0, $shown );
			add_filter(
				'wpyelp_template_reviews',
				static function ( $reviews ) use ( $samples ) {
					return empty( $reviews ) ? $samples : $reviews;
				}
			);
			add_filter(
				'wpyelp_badge_avg_total',
				static function ( $avg_total ) {
					return ( '' === (string) $avg_total[0] ) ? array( '4.8', 126 ) : $avg_total;
				}
			);
		}

		$result = $this->admin->wpyelp_save_template_fields( $fields );
		if ( empty( $result['ack'] ) || 'success' !== $result['ack'] || empty( $result['t_id'] ) ) {
			wp_send_json_error( array( 'message' => ! empty( $result['ackmessage'] ) ? $result['ackmessage'] : __( 'Couldn\'t save the template.', $d ) ) );
		}

		$tid = absint( $result['t_id'] );
		$this->update_state( array( 'pageid' => $pageid, 'tid' => $tid ) );
		wp_send_json_success(
			array(
				'tid'       => $tid,
				'title'     => $this->template_title( $tid ),
				'html'      => isset( $result['templatehtml'] ) ? $result['templatehtml'] : '',
				'shortcode' => '[wpyelp_usetemplate tid="' . $tid . '"]',
				'count'     => isset( $counts[ $pageid ] ) ? (int) $counts[ $pageid ] : 0,
				'sample'    => $sample,
			)
		);
	}

	/**
	 * Sample reviews for the preview while the real ones are still downloading.
	 *
	 * Same fields as rows of the wpyelp_reviews table, so the real template
	 * code draws them.
	 *
	 * @param string $pageid Page id the template is filtered to.
	 * @param string $name   Business name.
	 * @return object[]
	 */
	private function sample_reviews( $pageid, $name ) {
		require_once wprev_yelp_plugin_dir . 'public/partials/template_class.php';
		$avatars = new WP_Yelp_Template_Functions();
		$d       = self::TEXT_DOMAIN;
		$samples = array(
			array( 'Sarah M.', 5, 6, __( 'Friendly staff and quick, careful service. They explained everything up front and the price matched the quote. We\'ll definitely be back.', $d ) ),
			array( 'David R.', 5, 13, __( 'Great experience from start to finish. Easy to book, right on time, and the result was even better than we hoped.', $d ) ),
			array( 'Priya K.', 4, 21, __( 'Really good overall. It was busy when we came in, but the team kept us updated and took good care of us.', $d ) ),
			array( 'Marcus T.', 5, 34, __( 'Everyone here is so welcoming. You can tell they care about doing things right. Highly recommend!', $d ) ),
			array( 'Emily W.', 5, 48, __( 'Clean, organized, and professional. They went the extra mile to make sure we were happy.', $d ) ),
			array( 'Jordan L.', 4, 63, __( 'Solid service at a fair price. A couple of small hiccups, but they fixed them right away.', $d ) ),
		);

		$rows = array();
		foreach ( $samples as $i => $sample ) {
			list( $reviewer, $rating, $days_ago, $review_text ) = $sample;
			$time   = time() - $days_ago * DAY_IN_SECONDS;
			$rows[] = (object) array(
				'id'                 => -1 - $i,
				'pageid'             => $pageid,
				'pagename'           => $name,
				'reviewer_name'      => $reviewer,
				'reviewer_id'        => '',
				'userpic'            => $avatars->wprev_get_initials_avatar_url( $reviewer, 100 ),
				'rating'             => $rating,
				'created_time'       => gmdate( 'Y-m-d H:i:s', $time ),
				'created_time_stamp' => $time,
				'review_text'        => $review_text,
				'hide'               => '',
				'review_length'      => str_word_count( $review_text ),
				'type'               => 'Yelp',
				'location'           => '',
				'owner_response'     => '',
				'mediaurlsarrayjson' => '',
				'from_url'           => '',
				'from_url_review'    => '',
			);
		}
		return $rows;
	}

	/**
	 * Create a draft page that shows the template with the block.
	 */
	public function ajax_create_page() {
		$this->check_request();
		$d = self::TEXT_DOMAIN;

		if ( ! current_user_can( 'edit_pages' ) ) {
			wp_send_json_error( array( 'message' => __( 'You don\'t have permission to create pages.', $d ) ), 403 );
		}
		$tid   = isset( $_POST['tid'] ) ? absint( $_POST['tid'] ) : 0;
		$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		if ( '' === $this->template_title( $tid ) ) {
			wp_send_json_error( array( 'message' => __( 'The template is missing. Go back a step to make it again.', $d ) ) );
		}
		if ( '' === $title ) {
			$title = __( 'Reviews', $d );
		}

		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'draft',
				'post_title'   => $title,
				'post_content' => '<!-- wp:wp-yelp-review-slider/template ' . wp_json_encode( array( 'templateId' => $tid ) ) . ' /-->',
			),
			true
		);
		if ( is_wp_error( $page_id ) ) {
			wp_send_json_error( array( 'message' => $page_id->get_error_message() ) );
		}

		$this->update_state( array( 'page_id' => (int) $page_id ) );
		wp_send_json_success(
			array(
				'page'    => $this->page_info( (int) $page_id ),
				'message' => __( 'Draft page created. Open it to check it and publish it when you\'re ready.', $d ),
			)
		);
	}

	/**
	 * Mark setup as done. A later run starts a new template instead of changing this one.
	 */
	public function ajax_finish() {
		$this->check_request();
		update_option( self::DONE_OPTION, time(), false );
		$state = $this->state();
		update_option( self::STATE_OPTION, array( 'pageid' => $state['pageid'] ), false );
		wp_send_json_success( array( 'redirect' => admin_url( 'admin.php?page=wp_yelp-templates_posts' ) ) );
	}
}
