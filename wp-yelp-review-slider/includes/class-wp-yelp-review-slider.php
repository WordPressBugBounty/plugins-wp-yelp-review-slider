<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://ljapps.com
 * @since      1.0.0
 *
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/includes
 * @author     Josh <landjllc@gmail.com>
 */
class WP_Yelp_Review {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WP_Yelp_Review_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugintoken    The string used to uniquely identify this plugin.
	 */
	protected $plugintoken;
	protected $_token;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->_token = 'wp-yelp-review-slider';
		$this->version = '8.0';
		//using this for development
		//$this->version = time();

		$this->load_dependencies();
		$this->set_locale();
		
		if (is_admin()) {
			$this->define_admin_hooks();
			//update db if not at latest
			$this->_updatedb_version_number();
		}
		$this->define_public_hooks();
		//save version number to db
		$this->_log_version_number();

	}
	
	
		/**
	 * Update DB if not at latest
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _updatedb_version_number () {
		$current_version = get_option($this->_token . '_current_db_version', 0);
		
		if($current_version!=$this->version){
			
					//create table in database
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$table_name = $wpdb->prefix . 'wpyelp_reviews';
		
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				pageid varchar(150) DEFAULT '' NOT NULL,
				pagename tinytext NOT NULL,
				created_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				created_time_stamp int(12) NOT NULL,
				reviewer_name tinytext NOT NULL,
				reviewer_email tinytext NOT NULL,
				company_name varchar(100) DEFAULT '' NOT NULL,
				company_title varchar(100) DEFAULT '' NOT NULL,
				company_url varchar(100) DEFAULT '' NOT NULL,
				reviewer_id varchar(50) DEFAULT '' NOT NULL,
				rating varchar(3) NOT NULL,
				recommendation_type varchar(12) DEFAULT '' NOT NULL,
				review_text text NOT NULL,
				hide varchar(3) DEFAULT '' NOT NULL,
				review_length int(5) NOT NULL,
				review_length_char int(5) NOT NULL,
				type varchar(20) DEFAULT '' NOT NULL,
				userpic varchar(500) DEFAULT '' NOT NULL,
				userpic_small varchar(500) DEFAULT '' NOT NULL,
				from_name varchar(20) DEFAULT '' NOT NULL,
				from_url varchar(800) DEFAULT '' NOT NULL,
				from_logo varchar(500) DEFAULT '' NOT NULL,
				from_url_review varchar(800) DEFAULT '' NOT NULL,
				review_title varchar(500) DEFAULT '' NOT NULL,
				categories text NOT NULL,
				posts text NOT NULL,
				consent varchar(3) DEFAULT '' NOT NULL,
				userpiclocal varchar(500) DEFAULT '' NOT NULL,
				hidestars varchar(3) DEFAULT '' NOT NULL,
				miscpic varchar(500) DEFAULT '' NOT NULL,
				location varchar(500) DEFAULT '' NOT NULL,
				verified_order varchar(10) DEFAULT '' NOT NULL,
				language_code varchar(10) DEFAULT '' NOT NULL,
				unique_id tinytext DEFAULT '' NOT NULL,
				meta_data text DEFAULT '' NOT NULL,
				custom_data text DEFAULT '' NOT NULL,
				custom_stars text DEFAULT '' NOT NULL,
				owner_response text NOT NULL,
				sort_weight int(5) NOT NULL,
				tags text NOT NULL,
				mediaurlsarrayjson text NOT NULL,
				mediathumburlsarrayjson text NOT NULL,
				reviewfunnel varchar(3) DEFAULT '' NOT NULL,
				UNIQUE KEY id (id),
				PRIMARY KEY (id)
			) $charset_collate;";
		dbDelta( $sql );
		
		//create template posts table in dbDelta 
		$table_name = $wpdb->prefix . 'wpyelp_post_templates';
		
		$sql_template = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				title varchar(200) DEFAULT '' NOT NULL,
				template_type varchar(7) DEFAULT '' NOT NULL,
				style int(2) NOT NULL,
				created_time_stamp int(12) NOT NULL,
				display_num int(2) NOT NULL,
				display_num_rows int(3) NOT NULL,
				load_more varchar(3) DEFAULT '' NOT NULL,
				load_more_text varchar(50) DEFAULT '' NOT NULL,
				display_order varchar(10) DEFAULT '' NOT NULL,
				display_order_second varchar(10) DEFAULT '' NOT NULL,
				hide_no_text varchar(3) DEFAULT '' NOT NULL,
				template_css text NOT NULL,
				min_rating int(2) NOT NULL,
				min_words int(4) NOT NULL,
				max_words int(4) NOT NULL,
				word_or_char varchar(5) DEFAULT '' NOT NULL,
				rtype varchar(200) DEFAULT '' NOT NULL,
				rpage varchar(1000) DEFAULT '' NOT NULL,
				createslider varchar(3) DEFAULT '' NOT NULL,
				numslides int(2) NOT NULL,
				sliderautoplay varchar(3) DEFAULT '' NOT NULL,
				sliderdirection varchar(12) DEFAULT '' NOT NULL,
				sliderarrows varchar(3) DEFAULT '' NOT NULL,
				sliderdots varchar(3) DEFAULT '' NOT NULL,
				sliderdelay int(2) NOT NULL,
				sliderspeed int(5) NOT NULL,
				sliderheight varchar(3) DEFAULT '' NOT NULL,
				slidermobileview varchar(5) DEFAULT '' NOT NULL,
				showreviewsbyid varchar(600) DEFAULT '' NOT NULL,
				template_misc text DEFAULT '' NOT NULL,
				read_more varchar(3) DEFAULT '' NOT NULL,
				read_more_num int(4) NOT NULL,
				read_more_text varchar(20) DEFAULT '' NOT NULL,
				facebook_icon varchar(3) DEFAULT '' NOT NULL,
				facebook_icon_link varchar(3) DEFAULT '' NOT NULL,
				google_snippet_add varchar(3) DEFAULT '' NOT NULL,
				google_snippet_type varchar(50) DEFAULT '' NOT NULL,
				google_snippet_name varchar(500) DEFAULT '' NOT NULL,
				google_snippet_desc varchar(1000) DEFAULT '' NOT NULL,
				google_snippet_business_image varchar(500) DEFAULT '' NOT NULL,
				google_snippet_more text DEFAULT '' NOT NULL,
				cache_settings varchar(5) DEFAULT '' NOT NULL,
				review_same_height varchar(3) DEFAULT '' NOT NULL,
				add_profile_link varchar(3) DEFAULT '' NOT NULL,
				display_order_limit varchar(3) DEFAULT '' NOT NULL,
				display_masonry varchar(3) DEFAULT '' NOT NULL,
				read_less_text varchar(20) DEFAULT '' NOT NULL,
				string_sel varchar(3) DEFAULT '' NOT NULL,
				string_selnot varchar(3) DEFAULT '' NOT NULL,
				string_text varchar(300) DEFAULT '' NOT NULL,
				string_textnot varchar(300) DEFAULT '' NOT NULL,
				showreviewsbyid_sel varchar(9) DEFAULT '' NOT NULL,
				UNIQUE KEY id (id),
				PRIMARY KEY (id)
			) $charset_collate;";
		
		dbDelta( $sql_template );
			
		}
		
		update_option( $this->_token . '_current_db_version', $this->version );
		
	}
	
	
	
	
	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->version );
	} // End _log_version_number ()

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Yelp_Review_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Yelp_Review_i18n. Defines internationalization functionality.
	 * - WP_Yelp_Review_Admin. Defines all hooks for the admin area.
	 * - WP_Yelp_Review_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-yelp-review-slider-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-yelp-review-slider-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-yelp-review-slider-admin.php';
		
		/**
		 * The class responsible for parsing yelp and tripadvisor pages
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wpyelp_simple_html_dom.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-yelp-review-slider-public.php';
		
		/**
		 * The class responsible for the widget admin and public
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-yelp-review-slider-widget.php';
		
		/**
		 * The class responsible for displaying review template via do_action in template file
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-yelp-review-slider-template_action.php';

		//register the loader
		$this->loader = new WP_Yelp_Review_Loader();
		

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Yelp_Review_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WP_Yelp_Review_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WP_Yelp_Review_Admin( $this->get_token(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// register our wpyelp_settings_init to the admin_init action hook, add setting inputs
		//$this->loader->add_action('admin_init', $plugin_admin, 'wpyelp_settings_init');
		
		// register our wpyelp_yelp_settings_init to the admin_init action hook, add setting inputs
		$this->loader->add_action('admin_init', $plugin_admin, 'wpyelp_yelp_settings_init');

		//add menu page
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_pages' );
		
		//add ajax for adding feedback to table
		$this->loader->add_action( 'wp_ajax_yelp_get_results', $plugin_admin, 'wpyelp_process_ajax' );

		//add ajax for hiding and deleting reviews in table
		$this->loader->add_action( 'wp_ajax_yelp_hide_review', $plugin_admin, 'wpyelp_hidereview_ajax' ); 

		//add ajax for hiding and deleting reviews in table
		$this->loader->add_action( 'wp_ajax_yelp_find_reviews', $plugin_admin, 'wpyelp_getreviews_ajax' ); 		
		

		//add download csv file function wpyelp_download_csv
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'wpyelp_download_csv' ); 
		
		//add select shortcode list to post edit page
		//$this->loader->add_action( 'media_buttons', $plugin_admin, 'add_sc_select',11 ); 
		//$this->loader->add_action( 'admin_head', $plugin_admin, 'button_js' ); 
		
		//replaces insert into post text on media uploader when uploading reviewer avatar
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wpyelp_media_text' );
		
		//add download csv file function wpyelp_download_csv
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'wpyelp_download_yelp' ); 
		
		//cron for refreshing yelp
		$this->loader->add_action('wpyelp_daily_event', $plugin_admin, 'wpyelp_do_this_daily');
		
				//for displaying leave review admin notice
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'wprp_admin_notice__success' ); 
		
		//dashboard widget to show newest reviews
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'wprevyelp_dashboard_widget' );

		//add custom link to menu
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wprev_yelp_add_external_link_admin_submenu' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'wpse_66040_add_jquery' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_Yelp_Review_Public( $this->get_token(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		//add shortcode shortcode_wpyelp_usetemplate
		$plugin_public->shortcode_wpyelp_usetemplate();
		
		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
	
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_token() {
		return $this->_token;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WP_Yelp_Review_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
