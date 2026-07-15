<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/admin
 * @author     Your Name <email@example.com>
 */
class WP_Yelp_Review_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugintoken    The ID of this plugin.
	 */
	private $plugintoken;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	private $_token;
	public $errormsg;

	/**
	 * Holds the raw/parsed crawl-server response from the most recent crawl call
	 * so it can be surfaced to the browser console for debugging.
	 *
	 * @var array|null
	 */
	public $last_crawl_debug = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugintoken       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugintoken, $version ) {

		$this->_token = $plugintoken;
		//$this->version = $version;
		//for testing==============
		$this->version = time();
		//===================
				

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Yelp_Review_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Yelp_Review_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		//only load for this plugin admin pages
		if(isset($_GET['page'])){
			if($_GET['page']=="wp_yelp-reviews" || $_GET['page']=="wp_yelp-templates_posts" || $_GET['page']=="wp_yelp-get_yelp" || $_GET['page']=="wp_yelp-get_pro" || $_GET['page']=="wp_yelp-opt" || $_GET['page']=="wp_yelp-welcome"){

			wp_register_style( 'Font_Awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' );
			wp_enqueue_style('Font_Awesome');
				
			wp_enqueue_style( $this->_token."_wprev_w3", plugin_dir_url( __FILE__ ) . 'css/wprev_w3.css', array(), $this->version, 'all' );

			wp_enqueue_style( $this->_token, plugin_dir_url( __FILE__ ) . 'css/wpyelp_admin.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->_token."_wpyelp_w3", plugin_dir_url( __FILE__ ) . 'css/wpyelp_w3.css', array(), $this->version, 'all' );
			}
			//load template styles for preview
			if($_GET['page']=="wp_yelp-templates_posts"|| $_GET['page']=="wp_yelp-get_pro" || $_GET['page']=="wp_yelp-welcome"){
				wp_enqueue_style( $this->_token."_style1", plugin_dir_url(dirname(__FILE__)) . 'public/css/wprev-public_template1.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->_token."_style6", plugin_dir_url(dirname(__FILE__)) . 'public/css/wprev-public_template6.css', array(), $this->version, 'all' );
				//slider styles so the live preview matches the front end
				wp_enqueue_style( $this->_token."_unslider", plugin_dir_url(dirname(__FILE__)) . 'public/css/wprs_unslider.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->_token."_unslider_dots", plugin_dir_url(dirname(__FILE__)) . 'public/css/wprs_unslider-dots.css', array(), $this->version, 'all' );
				//lity for review media lightbox in the template preview
				wp_enqueue_style( $this->_token."_lity", plugin_dir_url(dirname(__FILE__)) . 'public/css/lity.min.css', array(), $this->version, 'all' );
			}
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Yelp_Review_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Yelp_Review_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		

		//scripts for all pages in this plugin
		if(isset($_GET['page'])){
			if($_GET['page']=="wp_yelp-reviews" || $_GET['page']=="wp_yelp-templates_posts" || $_GET['page']=="wp_yelp-get_yelp" || $_GET['page']=="wp_yelp-get_pro" || $_GET['page']=="wp_yelp-welcome"){
				//pop-up script
				wp_register_script( 'simple-popup-js',  plugin_dir_url( __FILE__ ) . 'js/wpyelp_simple-popup.min.js' , '', $this->version, false );
				wp_enqueue_script( 'simple-popup-js' );
				
			}
			//scripts for the get yelp reviews page (multi-source add/download)
			if($_GET['page']=="wp_yelp-get_yelp"){
				wp_enqueue_script('wpyelp_get_yelp-js', plugin_dir_url( __FILE__ ) . 'js/wpyelp_get_yelp.js', array( 'jquery' ), $this->version, false );
				wp_localize_script('wpyelp_get_yelp-js', 'adminjs_script_vars',
					array(
					'wpyelp_nonce'=> wp_create_nonce('randomnoncestring')
					)
				);
			}
		}
		
	
		//scripts for review list page
		if(isset($_GET['page'])){
			if($_GET['page']=="wp_yelp-reviews"){
				//admin js
				// Depend only on jQuery. thickbox/media-upload are enqueued separately below
				// for the avatar uploader; declaring them as hard dependencies here risks
				// WordPress silently dropping this script if either handle isn't registered,
				// which would break the edit popup, hide, and delete AJAX handlers.
				wp_enqueue_script('wpyelp_review_list_page-js', plugin_dir_url( __FILE__ ) . 'js/wpyelp_review_list_page.js', array( 'jquery' ), $this->version, false );

				//list of source (page) names for the "Remove by source" popup
				global $wpdb;
				$reviews_table_name = $wpdb->prefix . 'wpyelp_reviews';
				$pagenamearray = $wpdb->get_col( "SELECT pagename FROM {$reviews_table_name} WHERE pagename != '' GROUP BY pagename" );
				if ( ! is_array( $pagenamearray ) ) {
					$pagenamearray = array();
				}

				//used for ajax
				wp_localize_script('wpyelp_review_list_page-js', 'adminjs_script_vars', 
					array(
					'wpyelp_nonce'=> wp_create_nonce('randomnoncestring'),
					'pagenamearray' => wp_json_encode( $pagenamearray )
					)
				);

				//lity lightbox for review media thumbnails
				wp_enqueue_style( $this->_token."lity_min", plugin_dir_url( __FILE__ ) . 'css/lity.min.css', array(), $this->version, 'all' );
				wp_enqueue_script('wpyelp_lity-js', plugin_dir_url( __FILE__ ) . 'js/lity.min.js', array( 'jquery' ), $this->version, false );

 				wp_enqueue_script('thickbox');
				wp_enqueue_style('thickbox');
		 
				wp_enqueue_script('media-upload');
				wp_enqueue_script('wptuts-upload');

			}
			
			//scripts for templates posts page
			if($_GET['page']=="wp_yelp-templates_posts"){
			
				//slider scripts so the live preview can build a working slider
				wp_enqueue_script('wpyelp_unslider-js', plugin_dir_url(dirname(__FILE__)) . 'public/js/wprs-unslider-min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script('wpyelp_unslider_swipe-js', plugin_dir_url(dirname(__FILE__)) . 'public/js/wprs-unslider-swipe.js', array( 'jquery','wpyelp_unslider-js' ), $this->version, false );

				//public script for the preview (lity lightbox binding for review media thumbnails, tooltips, etc)
				//registered before the admin js below so it's guaranteed to load first (declared as a dependency).
				wp_enqueue_script( $this->_token."_lity", plugin_dir_url(dirname(__FILE__)) . 'public/js/lity.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->_token."_plublic", plugin_dir_url(dirname(__FILE__)) . 'public/js/wprev-public.js', array( 'jquery' ), $this->version, false );
				wp_localize_script( $this->_token."_plublic", 'wprevpublicjs_script_vars', array(
					'wprevplugin_url' => untrailingslashit( plugin_dir_url( dirname( __FILE__ ) ) ),
				) );

				//admin js
				wp_enqueue_script('wpyelp_templates_posts_page-js', plugin_dir_url( __FILE__ ) . 'js/wpyelp_templates_posts_page.js', array( 'jquery','wpyelp_unslider-js', $this->_token."_lity", $this->_token."_plublic" ), $this->version, false );
				//used for ajax
				wp_localize_script('wpyelp_templates_posts_page-js', 'adminjs_script_vars', 
					array(
					'wpyelp_nonce'=> wp_create_nonce('randomnoncestring'),
					'pluginsUrl' => wprev_yelp_plugin_url
					)
				);
 				wp_enqueue_script('thickbox');
				wp_enqueue_style('thickbox');
				wp_enqueue_script('media-upload');
				
				//add color picker here
				wp_enqueue_style( 'wp-color-picker' );
				//enque alpha color add-on wpyelp-wp-color-picker-alpha.js
				wp_enqueue_script( 'wp-color-picker-alpha', plugin_dir_url( __FILE__ ) . 'js/wpyelp-wp-color-picker-alpha.js', array( 'wp-color-picker' ), '2.1.2', false );

			}
		}
		
	}
	
	public function add_menu_pages() {

		/**
		 * adds the menu pages to wordpress
		 */

		$page_title = 'WP Yelp Reviews : Welcome';
		$menu_title = 'WP Yelp Reviews';
		$capability = 'manage_options';
		$menu_slug = 'wp_yelp-welcome';
		
		add_menu_page($page_title, $menu_title, $capability, $menu_slug, array($this,'wp_yelp_welcome'),'dashicons-star-half');
		
		$sub_menu_title = 'Welcome';
		add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, array($this,'wp_yelp_welcome'));
		
		$submenu_page_title = 'WP Yelp Reviews : Reviews List';
		$submenu_title = 'Review List';
		$submenu_slug = 'wp_yelp-reviews';
		add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,'wp_yelp_reviews'));
		
		$submenu_page_title = 'WP Yelp Reviews : Yelp';
		$submenu_title = 'Get Yelp Reviews';
		$submenu_slug = 'wp_yelp-get_yelp';
		add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,'wp_yelp_getyelp'));

		$submenu_page_title = 'WP Yelp Reviews : Templates';
		$submenu_title = 'Templates';
		$submenu_slug = 'wp_yelp-templates_posts';
		add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,'wp_yelp_templates_posts'));
		
		// Opt-in page (hidden from menu; reachable via direct URL)
		$submenu_page_title = 'WP Yelp Reviews : Opt';
		$submenu_title = 'Opt';
		$submenu_slug = 'wp_yelp-opt';
		add_submenu_page(null, $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,'wp_yelp_opt'));
		
		
		// Now add the submenu page for the reviews templates
		//$submenu_page_title = 'WP FB Reviews : Upgrade';
		//$submenu_title = 'Get Pro';
		//$submenu_slug = 'wp_yelp-get_pro';
		//add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,'wp_fb_getpro'));
	

	}
	
	public function wp_yelp_opt() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/opt.php';
	}

	public function wp_yelp_welcome() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/welcome.php';
	}
	
	
	public function wpse_66040_add_jquery() 
	{
		?>
		<script type="text/javascript">
			jQuery(document).ready( function($) {   
				$('#wprev-66040').parent().attr('target','_blank');  
			});
		</script>
		<?php
	}
	public function wp_yelp_reviews() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/review_list.php';
	}
	
	public function wp_yelp_templates_posts() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/templates_posts.php';
	}
	public function wp_yelp_getyelp() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/get_yelp.php';
	}
	public function wp_fb_getpro() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/get_pro.php';
	}

	/**
	 * custom option and settings on yelp page
	 */
	 //===========start yelp page settings===========================================================
	public function wpyelp_yelp_settings_init()
	{
	
		// register a new setting for "wp_yelp-get_yelp" page
		register_setting('wp_yelp-get_yelp', 'wpyelp_yelp_settings');
		
		// register a new section in the "wp_yelp-get_yelp" page
		add_settings_section(
			'wpyelp_yelp_section_developers',
			'',
			array($this,'wpyelp_yelp_section_developers_cb'),
			'wp_yelp-get_yelp'
		);
		
		//register yelp business url input field
		add_settings_field(
			'yelp_business_url', // as of WP 4.6 this value is used only internally
			'Yelp Business URL',
			array($this,'wpyelp_field_yelp_business_id_cb'),
			'wp_yelp-get_yelp',
			'wpyelp_yelp_section_developers',
			[
				'label_for'         => 'yelp_business_url',
				'class'             => 'wpyelp_row',
				'wpyelp_custom_data' => 'custom',
			]
		);

		//Turn on Yelp Reviews Downloader
		/*
		add_settings_field("yelp_radio", "Turn On Yelp Reviews", array($this,'yelp_radio_display'), "wp_yelp-get_yelp", "wpyelp_yelp_section_developers",
			[
				'label_for'         => 'yelp_radio',
				'class'             => 'wpyelp_row',
				'wpyelp_custom_data' => 'custom',
			]); */
	
	}
	//==== developers section cb ====
	public function wpyelp_yelp_section_developers_cb($args)
	{
		//echos out at top of section
		
		_e("<p>Use this page to download your Yelp business reviews and save them in your Wordpress database. Please note that the plugin can only return recommended reviews. They will show up on the Review List page once downloaded.
		</br></br>
		(<b>Note: This may not work for everyone.</b> <a href='https://wpreviewslider.com/' target='_blank'>Contact me</a> for help. Yelp has gotten good at blocking web crawlers. The Pro Version is still working and allows you to grab <b>all your reviews</b> from <b>multiple locations</b>.)", 'wp-yelp-reviews');
		
		/*
		_e("<p>Use this page to download your Yelp business reviews and save them in your Wordpress database. Please note that the plugin can only return recommended reviews. They will show up on the Review List page once downloaded. There are a couple of rules that Yelp has for their reviews.</p>
		<ul>
			<li> - Yelp reviews can only be cached for 24 hours. So your newest reviews will be automatically downloaded and updated every 24 hours. </li>
			<li> - They must contain a link to your Yelp business page and display the Yelp logo and review stars. We do handle this in our templates for you.</li>
		</ul>
		</br>
		(<b>Note: This may not work for everyone.</b> <a href='https://wpreviewslider.com/' target='_blank'>Contact me</a> for help. Yelp has gotten good at blocking web crawlers. The Pro Version is still working and allows you to grab <b>all your reviews</b> from <b>multiple locations</b>.)", 'wp-yelp-reviews'); 
		*/
	}
	
	//==== field cb =====
	public function wpyelp_field_yelp_business_id_cb($args)
	{
		// get the value of the setting we've registered with register_setting()
		$options = get_option('wpyelp_yelp_settings');

		// output the field
		?>
		<input id="<?= esc_attr($args['label_for']); ?>" data-custom="<?= esc_attr($args['wpyelp_custom_data']); ?>" type="text" name="wpyelp_yelp_settings[<?= esc_attr($args['label_for']); ?>]" placeholder="" value="<?php echo $options[$args['label_for']]; ?>">
		
		<p class="description">
			<?= esc_html__('Enter the Yelp URL for your business and click Save Settings. Example:', 'wp-yelp-reviews'); ?>
			</br>
			<?= esc_html__('https://www.yelp.com/biz/earth-and-stone-wood-fired-pizza-huntsville-2', 'wp-yelp-reviews'); ?>
		</p>
		<?php
	}
	public function yelp_radio_display($args)
		{
		$options = get_option('wpyelp_yelp_settings');
		
		   ?>
				<input type="radio" name="wpyelp_yelp_settings[<?= esc_attr($args['label_for']); ?>]" value="yes" <?php checked('yes', $options[$args['label_for']], true); ?>>Yes&nbsp;&nbsp;&nbsp;
				<input type="radio" name="wpyelp_yelp_settings[<?= esc_attr($args['label_for']); ?>]" value="no" <?php checked('no', $options[$args['label_for']], true); ?>>No
		   <?php
		}
	//=======end yelp page settings========================================================

	
	/**
	 * Store reviews in table, called from javascript file admin.js
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function wpyelp_process_ajax(){
	//ini_set('display_errors',1);  
	//error_reporting(E_ALL);
		
		check_ajax_referer('randomnoncestring', 'wpyelp_nonce');
		
		$postreviewarray = $_POST['postreviewarray'];
		
		//var_dump($postreviewarray);

		//loop through each one and insert in to db
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpyelp_reviews';
		
		$stats = array();
		
		foreach($postreviewarray as $item) { //foreach element in $arr
			$pageid = $item['pageid'];
			$pagename = $item['pagename'];
			$created_time = $item['created_time'];
			$created_time_stamp = strtotime($created_time);
			$reviewer_name = $item['reviewer_name'];
			$reviewer_id = $item['reviewer_id'];
			$rating = $item['rating'];
			$review_text = $item['review_text'];
			$review_length = str_word_count($review_text);
			$rtype = $item['type'];
			
			//check to see if row is in db already
			$checkrow = $wpdb->get_row( "SELECT id FROM ".$table_name." WHERE created_time = '$created_time'" );
			if ( null === $checkrow ) {
				$stats[] =array( 
						'pageid' => $pageid, 
						'pagename' => $pagename, 
						'created_time' => $created_time,
						'created_time_stamp' => strtotime($created_time),
						'reviewer_name' => $reviewer_name,
						'reviewer_id' => $reviewer_id,
						'rating' => $rating,
						'review_text' => $review_text,
						'hide' => '',
						'review_length' => $review_length,
						'type' => $rtype
					);
			}
		}
		$i = 0;
		$insertnum = 0;
		foreach ( $stats as $stat ){
			$insertnum = $wpdb->insert( $table_name, $stat );
			$i=$i + 1;
		}
	
		$insertid = $wpdb->insert_id;

		//header('Content-Type: application/json');
		echo $insertnum."-".$insertid."-".$i;

		die();
	}

	/**
	 * Hides or deletes reviews in table, called from javascript file wpyelp_review_list_page.js
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function wpyelp_hidereview_ajax(){
	//ini_set('display_errors',1);  
	//error_reporting(E_ALL);
		
		check_ajax_referer('randomnoncestring', 'wpyelp_nonce');
		
		$rid = intval($_POST['reviewid']);
		$myaction = $_POST['myaction'];

		//loop through each one and insert in to db
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpyelp_reviews';
		
		//check to see if we are deleting or just hiding or showing
		if($myaction=="hideshow"){
			//grab review and see if it is hidden or not
			$myreview = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $rid" );
			
			//pull array from options table of yelp hidden
			$yelphidden = get_option( 'wpyelp_hidden_reviews' );
			if(!$yelphidden){
				$yelphiddenarray = array('');
			} else {
				$yelphiddenarray = json_decode($yelphidden,true);
			}
			if(!is_array($yelphiddenarray)){
				$yelphiddenarray = array('');
			}
			$this_yelp_val = $myreview->reviewer_name."-".$myreview->created_time_stamp."-".$myreview->review_length."-".$myreview->type."-".$myreview->rating;

			if($myreview->hide=="yes"){
				//already hidden need to show
				$newvalue = "";
				
				//remove from $yelphidden
				if(($key = array_search($this_yelp_val, $yelphiddenarray)) !== false) {
					unset($yelphiddenarray[$key]);
				}
				
			} else {
				//shown, need to hide
				$newvalue = "yes";
				
				//need to update Yelp hidden ids in options table here array of name,time,count,type
				 array_push($yelphiddenarray,$this_yelp_val);
			}
			//update hidden yelp reviews option, use this when downloading yelp reviews so we can re-hide them each download
			$yelphiddenjson=json_encode($yelphiddenarray);
			update_option( 'wpyelp_hidden_reviews', $yelphiddenjson );
			
			//update database review table to hide this one
			$data = array( 
				'hide' => "$newvalue"
				);
			$format = array( 
					'%s'
				); 
			$updatetempquery = $wpdb->update($table_name, $data, array( 'id' => $rid ), $format, array( '%d' ));
			if($updatetempquery>0){
				echo $rid."-".$myaction."-".$newvalue;
			} else {
				echo $rid."-".$myaction."-fail";
			}

		}
		if($myaction=="deleterev"){
			$deletereview = $wpdb->delete( $table_name, array( 'id' => $rid ), array( '%d' ) );
			if($deletereview>0){
				echo $rid."-".$myaction."-success";
			} else {
				echo $rid."-".$myaction."-fail";
			}
		
		}

		die();
	}
	
	/**
	 * AJAX: save an edited review (reviewer photo URL + display date) without a
	 * page reload, called from wpyelp_review_list_page.js.
	 *
	 * @access public
	 * @since  9.0
	 * @return void
	 */
	public function wpyelp_savereview_ajax() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'wp-yelp-reviews' ) ) );
			return;
		}

		check_ajax_referer( 'randomnoncestring', 'wpyelp_nonce' );

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpyelp_reviews';

		$r_id       = isset( $_POST['editrid'] ) ? absint( $_POST['editrid'] ) : 0;
		$avatar_url = isset( $_POST['avatar_url'] ) ? esc_url_raw( wp_unslash( $_POST['avatar_url'] ) ) : '';
		$rdate_raw  = isset( $_POST['review_date'] ) ? sanitize_text_field( wp_unslash( $_POST['review_date'] ) ) : '';

		if ( $r_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid review.', 'wp-yelp-reviews' ) ) );
			return;
		}

		$existing = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $r_id ) );
		if ( ! $existing ) {
			wp_send_json_error( array( 'message' => __( 'Review not found.', 'wp-yelp-reviews' ) ) );
			return;
		}

		$parsed_stamp = $rdate_raw !== '' ? strtotime( $rdate_raw ) : false;
		if ( ! $parsed_stamp ) {
			wp_send_json_error( array( 'message' => __( 'Invalid date. Use the format YYYY-MM-DD HH:MM:SS.', 'wp-yelp-reviews' ) ) );
			return;
		}

		$created_time = date( 'Y-m-d H:i:s', $parsed_stamp );
		$data         = array(
			'userpic'            => $avatar_url,
			'created_time'       => $created_time,
			'created_time_stamp' => $parsed_stamp,
		);
		$format = array( '%s', '%s', '%d' );

		// Keep hidden-reviews fingerprint in sync when the date changes.
		if ( $existing->hide === 'yes' ) {
			$old_val = $existing->reviewer_name . '-' . $existing->created_time_stamp . '-' . $existing->review_length . '-' . $existing->type . '-' . $existing->rating;
			$new_val = $existing->reviewer_name . '-' . $parsed_stamp . '-' . $existing->review_length . '-' . $existing->type . '-' . $existing->rating;

			$yelphidden      = get_option( 'wpyelp_hidden_reviews' );
			$yelphiddenarray = $yelphidden ? json_decode( $yelphidden, true ) : array();
			if ( ! is_array( $yelphiddenarray ) ) {
				$yelphiddenarray = array();
			}
			$key = array_search( $old_val, $yelphiddenarray, true );
			if ( $key !== false ) {
				$yelphiddenarray[ $key ] = $new_val;
				update_option( 'wpyelp_hidden_reviews', wp_json_encode( array_values( $yelphiddenarray ) ) );
			}
		}

		$updated = $wpdb->update(
			$table_name,
			$data,
			array( 'id' => $r_id ),
			$format,
			array( '%d' )
		);

		if ( false === $updated ) {
			wp_send_json_error( array( 'message' => __( 'Database error while saving. Please try again.', 'wp-yelp-reviews' ) ) );
			return;
		}

		wp_send_json_success(
			array(
				'id'      => $r_id,
				'userpic' => $avatar_url !== '' ? esc_url( $avatar_url ) : '',
				'date'    => esc_html( $created_time ),
			)
		);
	}

	/**
	 * Ajax, retrieves reviews from table, called from javascript file wpyelp_templates_posts_page.js
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function wpyelp_getreviews_ajax(){
	//ini_set('display_errors',1);  
	//error_reporting(E_ALL);
		
		check_ajax_referer('randomnoncestring', 'wpyelp_nonce');
		$filtertext = htmlentities($_POST['filtertext']);
		$filterrating = htmlentities($_POST['filterrating']);
		$filterrating = intval($filterrating);
		//$curselrevs = $_POST['curselrevs'];
		$curselrevs ="";
		
		//perform db search and return results
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpyelp_reviews';
		$rowsperpage = 20;
		
		//pagenumber
		if(isset($_POST['pnum'])){
		$temppagenum = $_POST['pnum'];
		} else {
		$temppagenum ="";
		}
		if ( $temppagenum=="") {
			$pagenum = 1;
		} else if(is_numeric($temppagenum)){
			$pagenum = intval($temppagenum);
		}
		
		//sort direction
		if($_POST['sortdir']=="ASC" || $_POST['sortdir']=="DESC"){
			$sortdir = $_POST['sortdir'];
		} else {
			$sortdir = "DESC";
		}

		//make sure sortby is valid
		if(!isset($_POST['sortby'])){
			$_POST['sortby'] = "";
		}
		$allowed_keys = ['created_time_stamp', 'reviewer_name', 'rating', 'review_length', 'pagename', 'type' , 'hide'];
		$checkorderby = sanitize_key($_POST['sortby']);
	
		if(in_array($checkorderby, $allowed_keys, true) && $_POST['sortby']!=""){
			$sorttable = $_POST['sortby']. " ";
		} else {
			$sorttable = "created_time_stamp ";
		}
		if($_POST['sortdir']=="ASC" || $_POST['sortdir']=="DESC"){
			$sortdir = $_POST['sortdir'];
		} else {
			$sortdir = "DESC";
		}
		
		//get reviews from db
		$lowlimit = ($pagenum - 1) * $rowsperpage;
		$tablelimit = $lowlimit.",".$rowsperpage;
		
		if($filterrating>0){
			$filterratingtext = "rating = ".$filterrating;
		} else {
			$filterratingtext = "rating > 0";
		}
			
		//check to see if looking for previously selected only
		if (is_array($curselrevs)){
			$query = "SELECT * FROM ".$table_name." WHERE id IN (";
			//loop array and add to query
			$n=1;
			foreach ($curselrevs as $value) {
				if($value!=""){
					if(count($curselrevs)==$n){
						$query = $query." $value";
					} else {
						$query = $query." $value,";
					}
				}
				$n++;
			}
			$query = $query.")";
			//echo $query ;

			$reviewsrows = $wpdb->get_results($query);
			$hidepagination = true;
			$hidesearch = true;
		} else {
		

			//if filtertext set then use different query
			if($filtertext!=""){
				$reviewsrows = $wpdb->get_results("SELECT * FROM ".$table_name."
					WHERE (reviewer_name LIKE '%".$filtertext."%' or review_text LIKE '%".$filtertext."%') AND ".$filterratingtext."
					ORDER BY ".$sorttable." ".$sortdir." 
					LIMIT ".$tablelimit." "
				);
				$hidepagination = true;
			} else {
				$reviewsrows = $wpdb->get_results(
					$wpdb->prepare("SELECT * FROM ".$table_name."
					WHERE id>%d AND ".$filterratingtext."
					ORDER BY ".$sorttable." ".$sortdir." 
					LIMIT ".$tablelimit." ", "0")
				);
			}
		}
		
		//total number of rows
		$reviewtotalcount = $wpdb->get_var( "SELECT COUNT(*) FROM ".$table_name." WHERE id>1 AND ".$filterratingtext );
		//total pages
		$totalpages = ceil($reviewtotalcount/$rowsperpage);
		
		$reviewsrows['reviewtotalcount']=$reviewtotalcount;
		$reviewsrows['totalpages']=$totalpages;
		$reviewsrows['pagenum']=$pagenum;
		if($hidepagination){
			$reviewsrows['reviewtotalcount']=0;
			//$reviewsrows['totalpages']=0;
			//$reviewsrows['pagenum']=0;
		}
		if($hidesearch){
			//$reviewsrows['reviewtotalcount']=0;
			$reviewsrows['totalpages']=0;
			//$reviewsrows['pagenum']=0;
		}
		
		$results = json_encode($reviewsrows);
		echo $results;

		die();
	}
	
	
	
	/**
	 * replaces insert into post text on media uploader when uploading reviewer avatar
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */	
	public function wpyelp_media_text() {
		global $pagenow;
		if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
			// Now we'll replace the 'Insert into Post Button' inside Thickbox
			add_filter( 'gettext', array($this,'replace_thickbox_text') , 1, 3 );
		}
	}
	 
	public function replace_thickbox_text($translated_text, $text, $domain) {
		if ('Insert into Post' == $text) {
			$referer = strpos( wp_get_referer(), 'wp_yelp-reviews' );
			if ( $referer != '' ) {
				return __('Use as Reviewer Avatar', 'wp-yelp-review-slider' );
			}
		}
		return $translated_text;
	}
	

	/**
	 * download csv file of reviews
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */	
	public function wpyelp_download_csv() {
      global $pagenow;
      if ($pagenow=='admin.php' && current_user_can('export') && isset($_GET['taction']) && $_GET['taction']=='downloadallrevs' && $_GET['page']=='wp_yelp-reviews') {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=reviewdata.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpyelp_reviews';		
		$downloadreviewsrows = $wpdb->get_results(
				$wpdb->prepare("SELECT * FROM ".$table_name."
				WHERE id>%d ", "0"),'ARRAY_A'
			);
		$file = fopen('php://output', 'w');
		$delimiter=";";
		
		foreach ($downloadreviewsrows as $line) {
		    fputcsv($file, $line, $delimiter);
		}

        exit();
      }
    }	
	
	/**
	 * adds drop down menu of templates on post edit screen
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */	
	//add_action('media_buttons','add_sc_select',11);
	public function add_sc_select(){
		//get id's and names of templates that are post type 
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpyelp_post_templates';
		$currentforms = $wpdb->get_results("SELECT id, title, template_type FROM $table_name WHERE template_type = 'post'");
		if(count($currentforms)>0){
		echo '&nbsp;<select id="wprs_sc_select"><option value="select">Review Template</option>';
		foreach ( $currentforms as $currentform ){
			$shortcodes_list .= '<option value="[wpyelp_usetemplate tid=\''.$currentform->id.'\']">'.$currentform->title.'</option>';
		}
		 echo $shortcodes_list;
		 echo '</select>';
		}
	}
	//add_action('admin_head', 'button_js');
	public function button_js() {
			echo '<script type="text/javascript">
			jQuery(document).ready(function(){
			   jQuery("#wprs_sc_select").change(function() {
							if(jQuery("#wprs_sc_select :selected").val()!="select"){
							  send_to_editor(jQuery("#wprs_sc_select :selected").val());
							}
							  return false;
					});
			});
			</script>';
	}
	

	/**
	 * download yelp reviews when clicking the save button on Yelp page
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */	
	public function wpyelp_download_yelp() {
      global $pagenow;
      if (isset($_GET['settings-updated']) && $pagenow=='admin.php' && current_user_can('export') && $_GET['page']=='wp_yelp-get_yelp') {
		$this->wpyelp_download_yelp_master();
      }
    }

	/**
	 * Get saved Yelp crawl sources, migrating the legacy single URL if needed.
	 *
	 * @return array
	 */
	public function wpyelp_get_crawls() {
		$raw = get_option( 'wprev_yelp_crawls', 'not-exists' );
		if ( 'not-exists' === $raw ) {
			$crawls = array();
			update_option( 'wprev_yelp_crawls', wp_json_encode( $crawls ) );
		} else {
			$crawls = json_decode( $raw, true );
			if ( ! is_array( $crawls ) ) {
				$crawls = array();
			}
		}

		// Migrate legacy single yelp_business_url into crawls.
		$options = get_option( 'wpyelp_yelp_settings' );
		if ( is_array( $options ) && ! empty( $options['yelp_business_url'] ) ) {
			$url = esc_url_raw( trim( $options['yelp_business_url'] ) );
			if ( $url && filter_var( $url, FILTER_VALIDATE_URL ) ) {
				$pageid = $this->wpyelp_extract_pageid_from_url( $url );
				if ( $pageid && ! isset( $crawls[ $pageid ] ) ) {
					$crawls[ $pageid ] = array(
						'pageid'       => $pageid,
						'businessname' => $this->wpyelp_extract_businessname_from_url( $url ),
						'url'          => $url,
						'avg'          => '',
						'total'        => '',
					);
					update_option( 'wprev_yelp_crawls', wp_json_encode( $crawls ) );
				}
			}
		}

		return $crawls;
	}

	/**
	 * Persist crawls option and keep legacy yelp_business_url in sync.
	 *
	 * @param array $crawls Sources keyed by pageid.
	 */
	public function wpyelp_save_crawls( $crawls ) {
		if ( ! is_array( $crawls ) ) {
			$crawls = array();
		}
		update_option( 'wprev_yelp_crawls', wp_json_encode( $crawls ) );

		// Keep first source URL in legacy option for older template link fallbacks.
		$options = get_option( 'wpyelp_yelp_settings' );
		if ( ! is_array( $options ) ) {
			$options = array();
		}
		$first_url = '';
		foreach ( $crawls as $source ) {
			if ( is_array( $source ) && ! empty( $source['url'] ) ) {
				$first_url = $source['url'];
				break;
			}
		}
		$options['yelp_business_url'] = $first_url;
		update_option( 'wpyelp_yelp_settings', $options );
	}

	/**
	 * Extract a Yelp page id (the /biz/ slug) from a business URL.
	 *
	 * @param string $url Yelp URL.
	 * @return string
	 */
	public function wpyelp_extract_pageid_from_url( $url ) {
		$url = strtok( $url, '?' );
		if ( preg_match( '~/biz/([^/?#]+)~i', $url, $m ) ) {
			return sanitize_title( $m[1] );
		}
		$path = wp_parse_url( $url, PHP_URL_PATH );
		if ( $path ) {
			$base = trim( $path, '/' );
			$base = basename( $base );
			if ( $base ) {
				return sanitize_title( $base );
			}
		}
		return '';
	}

	/**
	 * Best-effort business name from a Yelp URL /biz/ slug.
	 *
	 * @param string $url Yelp URL.
	 * @return string
	 */
	public function wpyelp_extract_businessname_from_url( $url ) {
		$pageid = $this->wpyelp_extract_pageid_from_url( $url );
		if ( $pageid === '' ) {
			return 'Yelp Business';
		}
		// Drop a trailing numeric disambiguator (e.g. "-2") Yelp appends to slugs.
		$slug = preg_replace( '/-\d+$/', '', $pageid );
		$slug = str_replace( '-', ' ', $slug );
		return ucwords( trim( $slug ) );
	}

	/**
	 * Build one source table row HTML for AJAX add.
	 *
	 * @param string $pageid Page id.
	 * @param array  $source Source data.
	 * @return string
	 */
	public function wpyelp_source_row_html( $pageid, $source ) {
		$bname     = isset( $source['businessname'] ) ? $source['businessname'] : '';
		$url       = isset( $source['url'] ) ? $source['url'] : '';
		$avg       = isset( $source['avg'] ) ? $source['avg'] : '';
		$total     = isset( $source['total'] ) ? $source['total'] : '';
		$avg_total = ( $avg !== '' || $total !== '' ) ? esc_html( $avg ) . ' / ' . esc_html( $total ) : '—';
		$del_url   = wp_nonce_url(
			admin_url( 'admin.php?page=wp_yelp-get_yelp&ract=del&pageid=' . rawurlencode( $pageid ) ),
			'wpyelp_del_source'
		);

		ob_start();
		?>
		<tr data-pageid="<?php echo esc_attr( $pageid ); ?>">
			<td>
				<?php echo esc_html( $bname ); ?>
				<?php if ( $url ) : ?>
					<br><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View on Yelp', 'wp-yelp-reviews' ); ?></a>
				<?php endif; ?>
			</td>
			<td><?php echo esc_html( $pageid ); ?></td>
			<td class="yelp-source-stats"><?php echo $avg_total; ?></td>
			<td>
				<button type="button" class="button button-primary downloadrevs" data-pageid="<?php echo esc_attr( $pageid ); ?>"><?php esc_html_e( 'Download Reviews', 'wp-yelp-reviews' ); ?></button>
				<span class="buttonloader2 wprevloader"></span>
				<a class="button" style="color:#a00;" href="<?php echo esc_url( $del_url ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Delete this source and its reviews?', 'wp-yelp-reviews' ) ); ?>');"><?php esc_html_e( 'Delete', 'wp-yelp-reviews' ); ?></a>
				<span class="yelp-source-msg"></span>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Delete a source, its reviews, and averages row.
	 *
	 * @param string $pageid Page id.
	 */
	public function wpyelp_delete_source( $pageid ) {
		$pageid = sanitize_text_field( $pageid );
		if ( $pageid === '' ) {
			return;
		}
		$crawls = $this->wpyelp_get_crawls();
		unset( $crawls[ $pageid ] );
		$this->wpyelp_save_crawls( $crawls );

		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'wpyelp_reviews', array( 'pageid' => $pageid ) );
		$wpdb->delete( $wpdb->prefix . 'wpyelp_total_averages', array( 'btp_id' => $pageid ) );
	}

	/**
	 * AJAX: add a Yelp source URL.
	 */
	public function wpyelp_ajax_add_source() {
		if ( ! current_user_can( 'manage_options' ) ) {
			echo wp_json_encode( array( 'ack' => 'error', 'ackmsg' => 'Insufficient permissions.' ) );
			wp_die();
		}
		check_ajax_referer( 'randomnoncestring', 'wpyelp_nonce' );

		$url  = isset( $_POST['yelp_url'] ) ? esc_url_raw( trim( wp_unslash( $_POST['yelp_url'] ) ) ) : '';
		$name = isset( $_POST['businessname'] ) ? sanitize_text_field( wp_unslash( $_POST['businessname'] ) ) : '';

		if ( ! $url || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			echo wp_json_encode( array( 'ack' => 'error', 'ackmsg' => __( 'Please enter a valid Yelp URL.', 'wp-yelp-reviews' ) ) );
			wp_die();
		}
		if ( stripos( $url, 'yelp.' ) === false ) {
			echo wp_json_encode( array( 'ack' => 'error', 'ackmsg' => __( 'URL must be a Yelp business page.', 'wp-yelp-reviews' ) ) );
			wp_die();
		}

		$pageid = $this->wpyelp_extract_pageid_from_url( $url );
		if ( $pageid === '' ) {
			echo wp_json_encode( array( 'ack' => 'error', 'ackmsg' => __( 'Could not determine a page ID from that URL.', 'wp-yelp-reviews' ) ) );
			wp_die();
		}

		$crawls = $this->wpyelp_get_crawls();
		if ( isset( $crawls[ $pageid ] ) ) {
			echo wp_json_encode( array( 'ack' => 'error', 'ackmsg' => __( 'That source is already added.', 'wp-yelp-reviews' ) ) );
			wp_die();
		}

		if ( $name === '' ) {
			$name = $this->wpyelp_extract_businessname_from_url( $url );
		}

		$source = array(
			'pageid'       => $pageid,
			'businessname' => $name,
			'url'          => strtok( $url, '?' ),
			'avg'          => '',
			'total'        => '',
		);
		$crawls[ $pageid ] = $source;
		$this->wpyelp_save_crawls( $crawls );

		echo wp_json_encode(
			array(
				'ack'      => 'success',
				'ackmsg'   => __( 'Source added. Click Download Reviews to fetch reviews.', 'wp-yelp-reviews' ),
				'pageid'   => $pageid,
				'row_html' => $this->wpyelp_source_row_html( $pageid, $source ),
			)
		);
		wp_die();
	}

	/**
	 * AJAX: download reviews for one saved source.
	 */
	public function wpyelp_ajax_download_source() {
		if ( ! current_user_can( 'manage_options' ) ) {
			echo wp_json_encode( array( 'ack' => 'error', 'ackmsg' => 'Insufficient permissions.' ) );
			wp_die();
		}
		check_ajax_referer( 'randomnoncestring', 'wpyelp_nonce' );

		$pageid = isset( $_POST['pageid'] ) ? sanitize_text_field( wp_unslash( $_POST['pageid'] ) ) : '';
		$crawls = $this->wpyelp_get_crawls();
		if ( $pageid === '' || empty( $crawls[ $pageid ]['url'] ) ) {
			echo wp_json_encode( array( 'ack' => 'error', 'ackmsg' => __( 'Source not found. Add the URL again.', 'wp-yelp-reviews' ) ) );
			wp_die();
		}

		$result = $this->wpyelp_download_one_source(
			$crawls[ $pageid ]['url'],
			$pageid,
			isset( $crawls[ $pageid ]['businessname'] ) ? $crawls[ $pageid ]['businessname'] : ''
		);

		echo wp_json_encode( $result );
		wp_die();
	}

	/**
	 * Download and store reviews for a single Yelp source URL.
	 *
	 * Yelp pages return ~10 recommended reviews each. Page through until we have
	 * at least 10 reviews (free cap), following the crawl server's page numbers.
	 *
	 * @param string $tempurl  Yelp business URL.
	 * @param string $pageid   Page id (biz slug).
	 * @param string $pagename Business name.
	 * @return array
	 */
	public function wpyelp_download_one_source( $tempurl, $pageid = '', $pagename = '' ) {
		ini_set( 'memory_limit', '800M' );
		set_time_limit( 180 );

		$result = array(
			'ack'    => 'success',
			'ackmsg' => '',
			'avg'    => '',
			'total'  => '',
		);

		$tempurl = trim( $tempurl );
		if ( ! filter_var( $tempurl, FILTER_VALIDATE_URL ) ) {
			$result['ack']    = 'error';
			$result['ackmsg'] = __( 'Please enter a valid URL.', 'wp-yelp-reviews' );
			return $result;
		}

		$tempurl = strtok( $tempurl, '?' );

		if ( $pageid === '' ) {
			$pageid = $this->wpyelp_extract_pageid_from_url( $tempurl );
		}
		if ( $pagename === '' ) {
			$pagename = $this->wpyelp_extract_businessname_from_url( $tempurl );
		}

		global $wpdb;
		$table_name  = $wpdb->prefix . 'wpyelp_reviews';
		$totalinsert = 0;

		$listedurl = $tempurl;
		// Manual downloads use iscron=no so the crawl server does not throttle us.
		$iscron = 'no';

		$all_reviews     = array();
		$source_avg      = '';
		$source_total    = '';
		$crawl_debug_log = array();
		$min_reviews     = 10;
		$max_loops       = 3;
		$reviewscrawl    = null;

		for ( $loop = 1; $loop <= $max_loops; $loop++ ) {
			$reviewscrawl = $this->wprpfree_getapps_getrevs_page_yelp( $listedurl, $loop, $iscron );

			if ( ! empty( $this->last_crawl_debug ) ) {
				$crawl_debug_log[] = $this->last_crawl_debug;
			}

			if ( ! is_array( $reviewscrawl ) ) {
				break;
			}

			if ( $source_avg === '' && ! empty( $reviewscrawl['avg'] ) ) {
				$source_avg = $reviewscrawl['avg'];
			}
			if ( $source_total === '' && ! empty( $reviewscrawl['total'] ) ) {
				$source_total = $reviewscrawl['total'];
			}

			$page_reviews = ( ! empty( $reviewscrawl['reviews'] ) && is_array( $reviewscrawl['reviews'] ) ) ? $reviewscrawl['reviews'] : array();
			if ( ! empty( $page_reviews ) ) {
				$all_reviews = array_merge( $all_reviews, $page_reviews );
			}

			// Stop once we have enough, or when a page returns nothing new.
			if ( count( $all_reviews ) >= $min_reviews || empty( $page_reviews ) ) {
				break;
			}

			sleep( 1 );
		}

		// Free version: keep at most 10 reviews.
		if ( count( $all_reviews ) > $min_reviews ) {
			$all_reviews = array_slice( $all_reviews, 0, $min_reviews );
		}

		$result['crawl_debug'] = $crawl_debug_log;

		if ( empty( $all_reviews ) ) {
			$result['ack']    = 'error';
			$result['ackmsg'] = __( 'Unable to find any reviews. Please try again or contact support.', 'wp-yelp-reviews' );
			return $result;
		}

		$result['avg']   = $source_avg;
		$result['total'] = $source_total;

		$reviews = array();
		foreach ( $all_reviews as $review ) {
			$rtext         = isset( $review['review_text'] ) ? $review['review_text'] : '';
			$review_length = str_word_count( $rtext );
			$unixtimestamp = strtotime( isset( $review['updated'] ) ? $review['updated'] : '' );
			if ( ! $unixtimestamp ) {
				$unixtimestamp = time();
			}
			$timestamp = date( 'Y-m-d H:i:s', $unixtimestamp );

			// Dedupe by name + length (+ pageid) so re-downloads don't duplicate rows.
			if ( $pageid !== '' ) {
				$checkrow = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT id, mediaurlsarrayjson, owner_response FROM {$table_name} WHERE reviewer_name = %s AND review_length = %d AND pageid = %s",
						$review['reviewer_name'],
						$review_length,
						$pageid
					)
				);
			} else {
				$checkrow = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT id, mediaurlsarrayjson, owner_response FROM {$table_name} WHERE reviewer_name = %s AND (review_length = %d OR created_time = %s)",
						$review['reviewer_name'],
						$review_length,
						$timestamp
					)
				);
			}

			if ( ! empty( $checkrow ) ) {
				// Backfill fields that earlier downloads couldn't capture yet (e.g. review
				// photos added in a later plugin version) without creating duplicate rows.
				$backfill = array();
				if ( empty( $checkrow->mediaurlsarrayjson ) && ! empty( $review['mediaurlsarrayjson'] ) ) {
					$backfill['mediaurlsarrayjson'] = $review['mediaurlsarrayjson'];
				}
				if ( empty( $checkrow->owner_response ) && ! empty( $review['owner_response'] ) ) {
					$backfill['owner_response'] = sanitize_textarea_field( $review['owner_response'] );
				}
				if ( ! empty( $backfill ) ) {
					$wpdb->update( $table_name, $backfill, array( 'id' => (int) $checkrow->id ) );
				}
				continue;
			}

			$userpic = isset( $review['userpic'] ) ? $review['userpic'] : '';
			// Bump Yelp avatar to the larger size when it isn't a default avatar.
			if ( $userpic !== '' && strpos( $userpic, 'default_avatars' ) === false ) {
				$userpic = str_replace( '60s.jpg', '120s.jpg', $userpic );
			}

			// Re-hide previously hidden reviews on re-download.
			$yelphidden = get_option( 'wpyelp_hidden_reviews' );
			$yelphiddenarray = $yelphidden ? json_decode( $yelphidden, true ) : array( '' );
			if ( ! is_array( $yelphiddenarray ) ) {
				$yelphiddenarray = array( '' );
			}
			$this_yelp_val = trim( $review['reviewer_name'] ) . '-' . $unixtimestamp . '-' . $review_length . '-Yelp-' . (int) $review['rating'];
			$hideme        = in_array( $this_yelp_val, $yelphiddenarray, true ) ? 'yes' : '';

			// Defense in depth: re-sanitize immediately before the DB write.
			$reviews[] = array(
				'pageid'             => $pageid,
				'pagename'           => trim( $pagename ),
				'reviewer_name'      => sanitize_text_field( $review['reviewer_name'] ),
				'userpic'            => esc_url_raw( $userpic ),
				'rating'             => (int) $review['rating'],
				'created_time'       => $timestamp,
				'created_time_stamp' => $unixtimestamp,
				'review_text'        => sanitize_textarea_field( trim( $rtext ) ),
				'hide'               => $hideme,
				'review_length'      => $review_length,
				'type'               => 'Yelp',
				'location'           => isset( $review['location'] ) ? sanitize_text_field( $review['location'] ) : '',
				'owner_response'     => isset( $review['owner_response'] ) ? $review['owner_response'] : '',
				'mediaurlsarrayjson' => isset( $review['mediaurlsarrayjson'] ) ? $review['mediaurlsarrayjson'] : '',
				'from_url'           => esc_url_raw( $listedurl ),
				'from_url_review'    => isset( $review['from_url_review'] ) ? esc_url_raw( $review['from_url_review'] ) : '',
			);
		}

		$reviewtexts   = array();
		$insertreviews = array();
		foreach ( $reviews as $stat ) {
			if ( ! in_array( $stat['review_text'], $reviewtexts, true ) || $stat['review_text'] === '' ) {
				$insertreviews[] = $stat;
			}
			$reviewtexts[] = $stat['review_text'];
		}

		foreach ( $insertreviews as $stat ) {
			$insertnum    = $wpdb->insert( $table_name, $stat );
			$totalinsert += (int) $insertnum;
		}

		// Persist source avg/total for badges.
		if ( $pageid !== '' ) {
			$this->updatetotalavgreviews( 'Yelp', $pageid, $source_avg, $source_total, $pagename );

			$crawls = $this->wpyelp_get_crawls();
			if ( ! isset( $crawls[ $pageid ] ) ) {
				$crawls[ $pageid ] = array(
					'pageid'       => $pageid,
					'businessname' => $pagename,
					'url'          => $listedurl,
				);
			}
			$crawls[ $pageid ]['avg']           = $source_avg;
			$crawls[ $pageid ]['total']         = $source_total;
			$crawls[ $pageid ]['businessname']  = $pagename;
			$crawls[ $pageid ]['url']           = $listedurl;
			$crawls[ $pageid ]['last_download'] = time();
			$this->wpyelp_save_crawls( $crawls );
		}

		$numreturned      = count( $all_reviews );
		$result['ackmsg'] = sprintf(
			/* translators: 1: reviews found, 2: new reviews inserted */
			__( '%1$d reviews found. %2$d new reviews downloaded. Check the Review List page.', 'wp-yelp-reviews' ),
			$numreturned,
			$totalinsert
		);
		$this->errormsg = $result['ackmsg'];

		return $result;
	}

	/**
	 * Sanitize a JSON-encoded array of media URLs from the remote crawling
	 * service before it is stored or re-encoded.
	 *
	 * @param string $json Raw JSON string of URLs.
	 * @return string Re-encoded JSON of sanitized URLs, or '' if invalid.
	 */
	private function wprevpro_sanitize_media_urls_json( $json ) {
		$decoded = json_decode( $json, true );
		if ( ! is_array( $decoded ) ) {
			return '';
		}

		$safe = array();
		foreach ( $decoded as $url ) {
			if ( is_string( $url ) && $url !== '' ) {
				$safe[] = esc_url_raw( $url );
			}
		}

		return wp_json_encode( $safe );
	}

	/**
	 * Store source avg/total for badges (option + averages table).
	 *
	 * @param string $type     Review type.
	 * @param string $pageid   Page id.
	 * @param string $avg      Source average from crawler.
	 * @param string $total    Source total from crawler.
	 * @param string $pagename Business name.
	 */
	public function updatetotalavgreviews( $type, $pageid, $avg, $total, $pagename = '' ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpyelp_reviews';
		$avg        = str_replace( ',', '.', (string) $avg );
		$option     = 'wpyelp_total_avg_reviews';

		$wppro_total_avg_reviews_array = get_option( $option );
		if ( $wppro_total_avg_reviews_array ) {
			$wppro_total_avg_reviews_array = json_decode( $wppro_total_avg_reviews_array, true );
		}
		if ( ! is_array( $wppro_total_avg_reviews_array ) ) {
			$wppro_total_avg_reviews_array = array();
		}

		$ratingsarray = array();
		$fbreviews    = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT rating, type FROM {$table_name} WHERE hide != %s AND pageid = %s",
				'yes',
				$pageid
			)
		);
		$pagetype = $type;
		foreach ( $fbreviews as $fbreview ) {
			if ( $fbreview->rating > 0 ) {
				$ratingsarray[] = (float) $fbreview->rating;
			}
			if ( ! empty( $fbreview->type ) ) {
				$pagetype = $fbreview->type;
			}
		}

		$avgdb   = 0;
		$totaldb = 0;
		if ( count( $ratingsarray ) > 0 ) {
			$avgdb   = round( array_sum( $ratingsarray ) / count( $ratingsarray ), 3 );
			$totaldb = count( $ratingsarray );
		}

		if ( ! isset( $wppro_total_avg_reviews_array[ $pageid ] ) ) {
			$wppro_total_avg_reviews_array[ $pageid ] = array();
		}
		$wppro_total_avg_reviews_array[ $pageid ]['total_indb'] = $totaldb;
		$wppro_total_avg_reviews_array[ $pageid ]['avg_indb']   = $avgdb;
		if ( floatval( $avg ) > 0 ) {
			$wppro_total_avg_reviews_array[ $pageid ]['avg'] = round( floatval( $avg ), 3 );
		}
		if ( intval( $total ) > 0 ) {
			$wppro_total_avg_reviews_array[ $pageid ]['total'] = intval( $total );
		}

		update_option( $option, wp_json_encode( $wppro_total_avg_reviews_array, JSON_FORCE_OBJECT ) );

		$valuearray = array(
			'btp_id'     => $pageid,
			'btp_name'   => $pagename,
			'pagetype'   => $pagetype,
			'total'      => isset( $wppro_total_avg_reviews_array[ $pageid ]['total'] ) ? $wppro_total_avg_reviews_array[ $pageid ]['total'] : '',
			'total_indb' => $totaldb,
			'avg'        => isset( $wppro_total_avg_reviews_array[ $pageid ]['avg'] ) ? $wppro_total_avg_reviews_array[ $pageid ]['avg'] : '',
			'avg_indb'   => $avgdb,
			'numr1'      => '',
			'numr2'      => '',
			'numr3'      => '',
			'numr4'      => '',
			'numr5'      => '',
		);
		$this->updatetotalavgreviewstableinsert( 'page', $valuearray );
	}

	/**
	 * Insert/replace a row in wpyelp_total_averages.
	 *
	 * @param string $btp_type   page|template|badge.
	 * @param array  $valuearray Row values.
	 */
	public function updatetotalavgreviewstableinsert( $btp_type, $valuearray ) {
		global $wpdb;
		$table_name_totalavg = $wpdb->prefix . 'wpyelp_total_averages';
		$data                = array(
			'btp_id'     => isset( $valuearray['btp_id'] ) ? $valuearray['btp_id'] : '',
			'btp_name'   => isset( $valuearray['btp_name'] ) ? $valuearray['btp_name'] : '',
			'btp_type'   => $btp_type,
			'pagetype'   => isset( $valuearray['pagetype'] ) ? $valuearray['pagetype'] : '',
			'total_indb' => isset( $valuearray['total_indb'] ) ? (string) $valuearray['total_indb'] : '',
			'total'      => isset( $valuearray['total'] ) ? (string) $valuearray['total'] : '',
			'avg_indb'   => isset( $valuearray['avg_indb'] ) ? (string) $valuearray['avg_indb'] : '',
			'avg'        => isset( $valuearray['avg'] ) ? (string) $valuearray['avg'] : '',
			'numr1'      => isset( $valuearray['numr1'] ) ? (string) $valuearray['numr1'] : '',
			'numr2'      => isset( $valuearray['numr2'] ) ? (string) $valuearray['numr2'] : '',
			'numr3'      => isset( $valuearray['numr3'] ) ? (string) $valuearray['numr3'] : '',
			'numr4'      => isset( $valuearray['numr4'] ) ? (string) $valuearray['numr4'] : '',
			'numr5'      => isset( $valuearray['numr5'] ) ? (string) $valuearray['numr5'] : '',
		);
		$format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' );
		$wpdb->replace( $table_name_totalavg, $data, $format );
	}

	/**
	 * Call the remote crawling service (crawl.ljapps.com) for one Yelp page and
	 * return an array of normalized, sanitized reviews plus avg/total.
	 *
	 * @param string $listedurl Yelp business URL.
	 * @param int    $pagenum   Page number (1-based).
	 * @param string $iscron    'yes' for cron, 'no' for manual.
	 * @return array
	 */
	public function wprpfree_getapps_getrevs_page_yelp( $listedurl, $pagenum, $iscron ) {
		$result['ack'] = 'success';
		set_time_limit( 150 );

		$reviewsarray = array();

		if ( filter_var( $listedurl, FILTER_VALIDATE_URL ) ) {

			$stripvariableurl = stripslashes( $listedurl );
			$listedurl        = strtok( $stripvariableurl, '?' );

			if ( isset( $_SERVER['SERVER_ADDR'] ) && $_SERVER['SERVER_ADDR'] != '' ) {
				$ip_server = $_SERVER['SERVER_ADDR'];
			} else {
				$ip_server = urlencode( get_site_url() );
			}
			$siteurl = urlencode( get_site_url() );

			$tempurlval = 'https://crawl.ljapps.com/crawlrevs?rip=' . $ip_server . '&surl=' . $siteurl . '&scrapeurl=' . urlencode( $listedurl ) . '&stype=yelp&nhful=&locationtype=&scrapequery=&tempbusinessname=&pagenum=' . $pagenum . '&nextpageurl=&iscron=' . $iscron . '&sfp=free&nobot=1';

			$serverresponse = '';
			$args           = array(
				'timeout' => 120,
				'headers' => array(
					'Content-Type' => ' application/json',
					'Accept'       => 'application/json',
				),
			);
			$response = wp_remote_get( $tempurlval, $args );
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$serverresponse = $response['body'];
			} else {
				$results['ack']         = 'error';
				$results['ackmsg']      = 'Error 0001a: trouble contacting crawling server with remote_get. Please try again or contact support. ' . $response->get_error_message();
				$results['crawl_debug'] = array(
					'request_url' => $tempurlval,
					'raw'         => '',
					'error'       => is_wp_error( $response ) ? $response->get_error_message() : 'unknown',
				);
				echo wp_json_encode( $results );
				die();
			}

			// Check for block or timeout; fall back to the backup crawl server.
			if ( strpos( $serverresponse, 'Please wait while your request is being verified' ) !== false || ! isset( $serverresponse ) || $serverresponse == '' || strpos( $serverresponse, 'Access denied by Imunify360 bot-protection.' ) !== false || strpos( $serverresponse, '415 Unsupported Media Type' ) !== false ) {
				$response = wp_remote_get( 'https://ocean.ljapps.com/crawlrevs.php?rip=' . $ip_server . '&surl=' . $siteurl . '&scrapeurl=' . urlencode( $listedurl ) . '&stype=yelp&nhful=&locationtype=&scrapequery=&tempbusinessname=&pagenum=' . $pagenum . '&nextpageurl=&iscron=' . $iscron . '&sfp=free&nobot=1', array( 'timeout' => 150 ) );
				if ( is_array( $response ) && ! is_wp_error( $response ) ) {
					$serverresponse = $response['body'];
				}
			}

			$serverresponsearray    = json_decode( $serverresponse, true );
			$this->last_crawl_debug = array(
				'request_url' => $tempurlval,
				'iscron'      => $iscron,
				'raw'         => $serverresponse,
				'parsed'      => $serverresponsearray,
			);

			if ( $serverresponse == '' || ! is_array( $serverresponsearray ) ) {
				$results['ack']         = 'error';
				$results['ackmsg']      = 'Error 0001: trouble contacting crawling server. Please try again or contact support.';
				$results['crawl_debug'] = $this->last_crawl_debug;
				echo wp_json_encode( $results );
				die();
			}
			if ( isset( $serverresponsearray['ack'] ) && $serverresponsearray['ack'] == 'error' ) {
				$results['ack']         = 'error';
				$results['ackmsg']      = 'Error 0002: ' . $serverresponsearray['ackmessage'];
				$results['crawl_debug'] = $this->last_crawl_debug;
				echo wp_json_encode( $results );
				die();
			}
			if ( ! isset( $serverresponsearray['result'] ) || ! is_array( $serverresponsearray['result'] ) ) {
				$results['ack']         = 'error';
				$results['ackmsg']      = 'Error 0002b: trouble finding reviews. Contact support with this error code and the URL you are using.';
				$results['crawl_debug'] = $this->last_crawl_debug;
				echo wp_json_encode( $results );
				die();
			}
			if ( isset( $serverresponsearray['result']['ack'] ) && $serverresponsearray['result']['ack'] == 'error' ) {
				$results['ack']         = 'error';
				$results['ackmsg']      = 'Error 0003: Please try again. ' . $serverresponsearray['result']['ackmsg'];
				$results['crawl_debug'] = $this->last_crawl_debug;
				echo wp_json_encode( $results );
				die();
			}

			$crawlerresultarray = $serverresponsearray['result'];

			$result['total'] = isset( $crawlerresultarray['total'] ) ? $crawlerresultarray['total'] : '';
			$result['avg']   = isset( $crawlerresultarray['avg'] ) ? $crawlerresultarray['avg'] : '';
			if ( isset( $crawlerresultarray['callurl'] ) ) {
				$result['callurl'] = $crawlerresultarray['callurl'];
			}

			$crawlerreviewsarray = array();
			if ( isset( $crawlerresultarray['reviews'] ) && is_array( $crawlerresultarray['reviews'] ) ) {
				$crawlerreviewsarray = $crawlerresultarray['reviews'];
			}

			foreach ( $crawlerreviewsarray as $review ) {
				$user_name = isset( $review['user_name'] ) ? trim( $review['user_name'] ) : '';
				if ( $user_name === '' ) {
					continue;
				}

				$tempownerres = '';
				if ( isset( $review['owner_response'] ) && $review['owner_response'] != '' ) {
					$tempownerres = sanitize_textarea_field( $review['owner_response'] );
				}
				$templocation = '';
				if ( isset( $review['location'] ) && $review['location'] != '' ) {
					$templocation = sanitize_text_field( $review['location'] );
				}
				$tempmediaurlsarrayjson = '';
				if ( isset( $review['mediaurlsarrayjson'] ) && $review['mediaurlsarrayjson'] != '' ) {
					$tempmediaurlsarrayjson = $this->wprevpro_sanitize_media_urls_json( $review['mediaurlsarrayjson'] );
				}

				// Untrusted data from the remote crawling service: sanitize every field.
				$reviewsarray[] = array(
					'reviewer_name'      => sanitize_text_field( $user_name ),
					'userpic'            => isset( $review['userimage'] ) ? esc_url_raw( $review['userimage'] ) : '',
					'rating'             => isset( $review['rating'] ) ? (int) $review['rating'] : 0,
					'updated'            => isset( $review['datesubmitted'] ) ? sanitize_text_field( $review['datesubmitted'] ) : '',
					'review_text'        => isset( $review['rtext'] ) ? sanitize_textarea_field( $review['rtext'] ) : '',
					'from_url'           => $listedurl,
					'from_url_review'    => isset( $review['from_url_review'] ) ? esc_url_raw( $review['from_url_review'] ) : '',
					'location'           => $templocation,
					'mediaurlsarrayjson' => $tempmediaurlsarrayjson,
					'owner_response'     => $tempownerres,
				);
			}

			$result['reviews'] = $reviewsarray;
		}

		return $result;
	}
	
	//for using curl instead of fopen
	private function file_get_contents_curl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	
/**
	 * download yelp reviews
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */	
	public function wpyelp_download_yelp_master($iscron='') {
	//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);	
		//make sure file get contents is turned on for this host
		//if (ini_get('allow_url_fopen') == 1) {
		$errormsg='';
		$insertnum='';
			global $wpdb;
			$table_name = $wpdb->prefix . 'wpyelp_reviews';
			$options = get_option('wpyelp_yelp_settings');
			
			//make sure you have valid url, if not display message
			if (filter_var($options['yelp_business_url'], FILTER_VALIDATE_URL)) {
			  // you're good
			  //echo "valid url";

				//echo "passed both tests";
				$stripvariableurl = strtok($options['yelp_business_url'], '?');
				$yelpurl[1] = $stripvariableurl.'?sort_by=date_desc';
				//if($iscron==''){
				//$yelpurl[2] = $stripvariableurl.'?start=10';
				//$yelpurl[3] = $stripvariableurl.'?start=20&sort_by=date_desc';
				//}
				//$yelpurl[4] = $stripvariableurl.'?start=30&sort_by=date_desc';
				
				//include_once('simple_html_dom.php');
				//loop to grab pages
				$reviews = [];
				$n=1;
				if(isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR']!=''){
					$ip_server = $_SERVER['SERVER_ADDR'];
				} else {
					//get url of site.
					$ip_server = urlencode(get_site_url());
				}
				$siteurl = urlencode(get_site_url());
				$x=1;
				foreach ($yelpurl as $urlvalue) {
					// Create DOM from URL or file
					//$html = file_get_html($urlvalue);
						
					// Create DOM from URL or file
					$tempurlval = 'https://crawl.ljapps.com/crawlrevs?rip='.$ip_server.'&surl='.$siteurl.'&scrapeurl='.$urlvalue.'&stype=yelp&sfp=pro&nobot=1&nhful=&locationtype=&scrapequery=&tempbusinessname=&pagenum='.$x.'&nextpageurl=';
					
					
					$serverresponse='';
					
					$args = array(
						'timeout'     => 120,
						'sslverify' => false,
				'headers' => array( 
					'Content-Type' => ' application/json',
					'Accept'=> 'application/json'
				)
					); 
					$response = wp_remote_get( $tempurlval, $args );
					if ( is_array( $response ) && ! is_wp_error( $response ) ) {
						$headers = $response['headers']; // array of http header lines
						$serverresponse    = $response['body']; // use the content
					} else {
						//must have been an error
						$results['ack'] ='error';
						$results['ackmsg'] ='Error 0001a: trouble contacting crawling server with remote_get. Please try again or contact support. '.$response->get_error_message();
						$results = json_encode($results);
						echo $results;
						die();
					}
					
					//check for block or timeout
					//====================
					if (strpos($serverresponse, "Please wait while your request is being verified") !== false || !isset($serverresponse) || $serverresponse=='' || strpos($serverresponse, "Access denied by Imunify360 bot-protection.") !== false) {
					   //this site is greylisted by imunify360 on cloudways, call backup digital ocean server
					   $response = wp_remote_get( 'https://ocean.ljapps.com/crawlrevs.php?rip='.$ip_server.'&surl='.$siteurl.'&scrapeurl='.$listedurl.'&stype=yelp&sfp=pro&nobot=1&nhful='.$nhful.'&locationtype=&scrapequery=&tempbusinessname=&pagenum='.$pagenum.'&nextpageurl='.$nextpageurl, array( 'sslverify' => false, 'timeout' => 60 ) );
						if ( is_array( $response ) && ! is_wp_error( $response ) ) {
							$headers = $response['headers']; // array of http header lines
							$serverresponse    = $response['body']; // use the content
						}
					}
					//=========================
					
					$serverresponsearray = json_decode($serverresponse, true);
					$crawlerresultarray = $serverresponsearray['result'];
					$crawlerreviewsarray = $crawlerresultarray['reviews'];
					
					foreach ($crawlerreviewsarray as $review) {
					
						$tempownerres='';
						if(isset($review['owner_response']) && $review['owner_response']!=''){
							$tempownerres = $review['owner_response'];
						}
						$templocation ='';
						if(isset($review['location']) && $review['location']!=''){
							$templocation = $review['location'];
						}	
						$tempmediaurlsarrayjson ='';
						if(isset($review['mediaurlsarrayjson']) && $review['mediaurlsarrayjson']!=''){
							$tempmediaurlsarrayjson = $review['mediaurlsarrayjson'];
						}					
						/*
						$reviewsarray[] = [
						 'reviewer_name' => $review['user_name'],
						 'reviewer_id' => '',
						 'reviewer_email' => '',
						 'userpic' => $review['userimage'],
						 'rating' => $review['rating'],
						 'updated' => $review['datesubmitted'],
						 'review_text' => $review['rtext'],
						 'review_title' => '',
						 'from_url' => $listedurl,
						 'from_url_review' => $review['from_url_review'],
						 'language_code' => '',
						 'location' => $templocation,
						 'recommendation_type' => '',
						 'company_title' =>  '',
						 'company_url' => '',
						 'company_name' => '',
						 'mediaurlsarrayjson' => $tempmediaurlsarrayjson,
						 'owner_response' => $tempownerres,
						 ];
						 */
						 $timestamp = strtotime($review['datesubmitted']);
						 $timestamp = date("Y-m-d H:i:s", $timestamp);
						 $review_length = str_word_count($review['rtext']);
						 
						 $reviewsarray[] = [
										'reviewer_name' => $review['user_name'],
										'pagename' => '',
										'userpic' => $review['userimage'],
										'rating' => $review['rating'],
										'created_time' => $timestamp,
										'created_time_stamp' => strtotime($review['datesubmitted']),
										'review_text' => trim($review['rtext']),
										'hide' => '',
										'review_length' => $review_length,
										'type' => 'Yelp'
								];
						
						//$x++;
					}
						
					
					//print_r($serverresponsearray);
					//die();

					
						
					//sleep for random 2 seconds
					sleep(rand(1,3));
					$n++;
					
					// clean up memory
					if (!empty($html)) {
						$html->clear();
						unset($html);
					}
					
					
					$x++;
					
				}
				$reviews = array_merge($reviews, $reviewsarray);
				


				// clean up memory
				if (!empty($html)) {
					$html->clear();
					unset($html);
				}
				
				//go ahead and delete first
				if (is_array($reviews)){
					if(count((array)$reviews)>0){
					$wpdb->delete( $table_name, array( 'type' => 'Yelp' ) );
					}
				}
				
				//add all new yelp reviews to db
				foreach ( $reviews as $stat ){
					$insertnum = $wpdb->insert( $table_name, $stat );
				}
				//reviews added to db
				if($insertnum){
					$errormsg = $errormsg . count($reviews).' Yelp reviews downloaded.';
					$this->errormsg = $errormsg;
				} else {
					$errormsg = $errormsg . ' Error: Unable to find the reviews on this page. Please contact support.';
					$this->errormsg = $errormsg;
				}
				
				
			  
			} else {
				$errormsg = $errormsg . ' Please enter a valid URL.';
				$this->errormsg = $errormsg;
			}
			/*
			if($options['yelp_radio']=='no'){
				$wpdb->delete( $table_name, array( 'type' => 'Yelp' ) );
				//cancel wp cron job
			}
			*/
			
	}
	
	
	/**
	 * download yelp reviews
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */	
	public function wpyelp_download_yelp_master_OLD($iscron='') {
	//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);	
		//make sure file get contents is turned on for this host
		//if (ini_get('allow_url_fopen') == 1) {
		$errormsg='';
		$insertnum='';
			global $wpdb;
			$table_name = $wpdb->prefix . 'wpyelp_reviews';
			$options = get_option('wpyelp_yelp_settings');
			
			//make sure you have valid url, if not display message
			if (filter_var($options['yelp_business_url'], FILTER_VALIDATE_URL)) {
			  // you're good
			  //echo "valid url";
			  if($options['yelp_radio']=='yes'){
				//echo "passed both tests";
				$stripvariableurl = strtok($options['yelp_business_url'], '?');
				$yelpurl[1] = $stripvariableurl.'?sort_by=date_desc';
				//if($iscron==''){
				$yelpurl[2] = $stripvariableurl.'?start=10&sort_by=date_desc';
				//$yelpurl[3] = $stripvariableurl.'?start=20&sort_by=date_desc';
				//}
				//$yelpurl[4] = $stripvariableurl.'?start=30&sort_by=date_desc';
				
				//include_once('simple_html_dom.php');
				//loop to grab pages
				$reviews = [];
				$n=1;
				foreach ($yelpurl as $urlvalue) {
					// Create DOM from URL or file
					//$html = file_get_html($urlvalue);
					
				// Create DOM from URL or file
				
					if (ini_get('allow_url_fopen') == true) {
						$fileurlcontents=file_get_contents($urlvalue);
					} else if (function_exists('curl_init')) {
						$fileurlcontents=$this->file_get_contents_curl($urlvalue);
					} else {
						$fileurlcontents='<html><body>fopen is not allowed on this host.</body></html>';
						$errormsg = $errormsg . ' <p style="color: #A00;">fopen is not allowed on this host and cURL did not work either. Please ask your hosting provided to turn fopen on or fix cURL.</p>';
						$this->errormsg = $errormsg;
						break;
					}
					
					
					//echo $fileurlcontents;
					//die();
					
					//get the reviews json string
					$startpos = strpos($fileurlcontents, 'reviewFeedQueryProps');
					
					//echo "<br>startpos:".$startpos;
					
					$firstsubstring = substr($fileurlcontents,$startpos+22);
					
					//$firstsubstring = html_entity_decode($firstsubstring);
					//$firstsubstring = htmlentities($firstsubstring);
					//echo "<br>firstsubstring:".$firstsubstring;
					//die();
					
					//$endpos = strpos($firstsubstring, ',"businessId"');
					$endpos = strpos($firstsubstring, ', "reviewHighlightsProps":');
					if(!$endpos){
						$endpos = strpos($firstsubstring, ',"reviewHighlightsProps":');
					}
					if(!$endpos){
						$endpos = strpos($firstsubstring, ',"seoLinksProps"');
					}
					if(!$endpos){
						$endpos = strpos($firstsubstring, ', "seoLinksProps"');
					}
					
					if(!$startpos || !$endpos){
						//$errormsg = $errormsg . ' Error 01: Unable to find reviews.';
						//$this->errormsg = $errormsg;
						$pagetype = 'old';
					} else {
						$pagetype = 'new';
					}
					
					
					//echo "<br>endpos:".$endpos;
					//echo "<br>errormsg:".$errormsg;
					
					//die();
					
					$finalstring = substr($firstsubstring,0,$endpos);
					$finalstring = htmlentities($finalstring);
					$finalstring = html_entity_decode($finalstring);
					
					//echo "<br>finalstring:".$finalstring;
					//die();
					
					$finalstringjson = json_decode($finalstring,TRUE);

//print_r($finalstringjson);
//die();

					
					require_once('wpyelp_simple_html_dom.php');
					$html = wpyelp_str_get_html($fileurlcontents);
					
					//echo $html;
//die();
					
					if(!$html && $pagetype=='old'){
						
						
						
						$fileurlcontents='<html><body>Oops! Unable to read Yelp page. Please contact support and send them the URL you are using.</body></html>';
						$errormsg = $errormsg . ' <p style="color: #A00;">Oops! Unable to read the Yelp page. Please contact support and send them the URL you are using.</p>';
						$this->errormsg = $errormsg;
						break;
					}
					
					//echo $html;
					//die();
					$pagename ='';
					
					//find yelp business name and add to db under pagename
					//headingLight__09f24__N86u1
					//if($html->find("div[class=headingLight__09f24__N86u1]", 0)){
					//	$pagename = $html->find("div[class=headingLight__09f24__N86u1]", 0)->plaintext;
					//}

					
					//echo $pagename;
					//echo $pagetype;
					
					//die();
					
					
					//$pagetype = 'old';
					
		
					//this is different depending on which yelp page type
					//===========================
					if($pagetype=='old'){
						$reviewsarray = $this->wpyelp_download_yelp_master_typeold($html,$pagename);
						//print_r($reviewsarray);
					//echo $pagetype;
					//die();
						$reviewstemp = $reviewsarray['reviews'];
						$reviews = array_merge($reviews, $reviewstemp);
					} else if($pagetype=='new'){
						$reviewsarray = $this->wpyelp_download_yelp_master_typenew($finalstringjson,$pagename);
						//print_r($reviewsarray);
						$reviewstemp = $reviewsarray['reviews'];
						if(is_array($reviewstemp) && count($reviewstemp)>0){
						$reviews = array_merge($reviews, $reviewstemp);
						}
					} else {
						echo "Error: Page title not found. Please contact support".
						die();
					}
					//================================
					/*
					if($html->find('div.biz-main-info', 0)){
						//find total number here and end break loop early if total number less than 50. review-count
						$totalreviews = $html->find('div.biz-main-info', 0)->find('span.review-count', 0)->plaintext;
						$totalreviews = intval($totalreviews);
						if (($n*20) > $totalreviews) {
										//break;
								}
					} else if($html->find('p[class=lemon--p__373c0__3Qnnj text__373c0__2pB8f text-color--mid__373c0__3G312 text-align--left__373c0__2pnx_ text-size--large__373c0__1568g]', 0)){
						//find total number here and end break loop early if total number less than 50. review-count
						$totalreviews = $html->find('p[class=lemon--p__373c0__3Qnnj text__373c0__2pB8f text-color--mid__373c0__3G312 text-align--left__373c0__2pnx_ text-size--large__373c0__1568g]', 0)->plaintext;
						$totalreviews = intval($totalreviews);
						if (($n*20) > $totalreviews) {
										//break;
								}
					}
					*/
					//sleep for random 2 seconds
					sleep(rand(1,3));
					$n++;
					
					// clean up memory
					if (!empty($html)) {
						$html->clear();
						unset($html);
					}
			
					
				}
				 

				// clean up memory
				if (!empty($html)) {
					$html->clear();
					unset($html);
				}
				
				//go ahead and delete first
				if (is_array($reviews)){
					if(count((array)$reviews)>0){
					$wpdb->delete( $table_name, array( 'type' => 'Yelp' ) );
					}
				}
				
				//add all new yelp reviews to db
				foreach ( $reviews as $stat ){
					$insertnum = $wpdb->insert( $table_name, $stat );
				}
				//reviews added to db
				if($insertnum){
					$errormsg = $errormsg . ' Yelp reviews downloaded.';
					$this->errormsg = $errormsg;
				} else {
					$errormsg = $errormsg . ' Error: Unable to find the reviews on this page. Please contact support.';
					$this->errormsg = $errormsg;
				}
				
				
			  }
			} else {
				$errormsg = $errormsg . ' Please enter a valid URL.';
				$this->errormsg = $errormsg;
			}
			
			if($options['yelp_radio']=='no'){
				$wpdb->delete( $table_name, array( 'type' => 'Yelp' ) );
				//cancel wp cron job
			}
			
	}
	
	public function wpyelp_download_yelp_master_typeold($html,$pagename){
		
		//echo $html;
		//die();
		
		//echo "here";
		//die();
		
		// Find 20 reviews
					$i = 1;
					$reviews = [];
					//print_r($html->find('div.review--with-sidebar'));
					//die();
					//look for review div.
					$reviewdivs = new stdClass();
					if($html->find('div[class=review__09f24__oHr9V]')){
						$reviewdivs = $html->find('div[class=review__09f24__oHr9V]');
					}
					if(count( (array)$reviewdivs) <1){
						if($html->find('li[class=css-1q2nwpv]')){
							$reviewdivs = $html->find('li[class=css-1q2nwpv]');
						}
					}
					//another change on 4/26
					if(count( (array)$reviewdivs) <1){
						if($html->find('li[class=yelp-emotion-1jp2syp]')){
							$reviewdivs = $html->find('li[class=yelp-emotion-1jp2syp]');
						}
					}
					//another change on 5/17  y-css-1jp2syp
					if(count( (array)$reviewdivs) <1){
						//echo "shere";
						if($html->find('li[class=y-css-1jp2syp]')){
							$reviewdivs = $html->find('li[class=y-css-1jp2syp]');
						}
					}
					//another change on 9/23  y-css-mu4kr5
					if(count( (array)$reviewdivs) <1){
						//echo "shere";
						if($html->find('li[class=y-css-mu4kr5]')){
							$reviewdivs = $html->find('li[class=y-css-mu4kr5]');
						}
					}
					
					//print_r($reviewdivs);
					//die();
					
					foreach ($reviewdivs as $review) {
							if ($i > 21) {
									break;
							}
							$user_name='';
							$userimage='';
							$rating='';
							$datesubmitted='';
							$rtext='';
							// Find user_name
							if($review->find('a.user-display-name', 0)){
								$user_name = $review->find('a.user-display-name', 0)->plaintext;
							}
							if($user_name ==''){
								if($review->find('span[class=fs-block css-ux5mu6]', 0)){
									$user_name = $review->find('span[class=fs-block css-ux5mu6]', 0)->find('a', 0)->plaintext;
								}
							}
							if($user_name ==''){
								if($review->find('div[class=user-passport-info]', 0)){
									$user_name = $review->find('div[class=user-passport-info]', 0)->find('a', 0)->plaintext;
								}
							}
							if($user_name ==''){
								if($review->find('span[class=fs-block yelp-emotion-1m3btbh]', 0)){
									$user_name = $review->find('span[class=fs-block yelp-emotion-1m3btbh]', 0)->find('a', 0)->plaintext;
								}
							}
							if($user_name ==''){
								if($review->find('span[class=y-css-w3ea6v]', 0)){
									$user_name = $review->find('span[class=y-css-w3ea6v]', 0)->find('a', 0)->plaintext;
								}
							}
							//echo "username:".$user_name;
							//die();
							
							// Find userimage
						
							
							if($review->find('img.photo-box-img', 0)){
								$userimage = $review->find('img.photo-box-img', 0)->src;
							}
							if($userimage ==''){
								if($review->find('img[class=y-css-1k4vfmo]', 0)){
									$userimage = $review->find('img[class=y-css-1k4vfmo]', 0)->src;
								}
							}
							if($userimage ==''){
								if($review->find('img[class=lemon--img__373c0__3GQUb photo-box-img__373c0__O0tbt]', 0)){
									$userimage = $review->find('img[class=lemon--img__373c0__3GQUb photo-box-img__373c0__O0tbt]', 0)->src;
								}
							}
							if($userimage ==''){
								if($review->find('img[class=css-1pz4y59]', 0)){
									$userimage = $review->find('img[class=css-1pz4y59]', 0)->src;
								}
							}
							if($userimage ==''){
								if($review->find('img[class=yelp-emotion-1k4vfmo]', 0)){
									$userimage = $review->find('img[class=yelp-emotion-1k4vfmo]', 0)->src;
								}
							}
							
							if($userimage==""){
								$userimage='"https://s3-media0.fl.yelpcdn.com/assets/srv0/yelp_styleguide/514f6997a318/assets/img/default_avatars/user_60_square.png';
								
							}
							// find rating
							if($review->find('div.rating-large', 0)){
								$rating = $review->find('div.rating-large', 0)->title;
								$rating = intval($rating);
							}
							if($rating ==''){
								if($review->find("div[class*=i-stars--regular-]", 0)){
									$rating = $review->find("div[class*=i-stars--regular-]", 0)->{'title'};
									$rating = intval($rating);
								}
							}
							if($rating ==''){
								if($review->find("div[class=five-stars__09f24__mBKym]", 0)){
									$rating = $review->find("div[class=five-stars__09f24__mBKym]", 0)->{'aria-label'};
									$rating = intval($rating);
								}
							}
							if($rating ==''){
								if($review->find("div[class=css-14g69b3]", 0)){
									$rating = $review->find("div[class=css-14g69b3]", 0)->{'aria-label'};
									$rating = intval($rating);
								}
							}
							if($rating ==''){
								if($review->find("div[class=yelp-emotion-9tnml4]", 0)){
									$rating = $review->find("div[class=yelp-emotion-9tnml4]", 0)->{'aria-label'};
									$rating = intval($rating);
								}
							}
							if($rating ==''){
								if($review->find("div[class=y-css-9tnml4]", 0)){
									$rating = $review->find("div[class=y-css-9tnml4]", 0)->{'aria-label'};
									$rating = intval($rating);
								}
							}
							if($rating ==''){
								if($review->find("div[class=y-css-1jwbncq]", 0)){
									$rating = $review->find("div[class=y-css-1jwbncq]", 0)->{'aria-label'};
									$rating = intval($rating);
								}
							}
							
							// find date
							if($review->find('span.rating-qualifier', 0)){
								$datesubmitted = $review->find('span.rating-qualifier', 0)->plaintext;
								$datesubmitted = str_replace(array("Updated", "review"), "", $datesubmitted);
							}
							if($datesubmitted ==''){
								if($review->find('span[class=lemon--span__373c0__3997G text__373c0__2pB8f text-color--mid__373c0__3G312 text-align--left__373c0__2pnx_]', 0)){
									$datesubmitted = $review->find('span[class=lemon--span__373c0__3997G text__373c0__2pB8f text-color--mid__373c0__3G312 text-align--left__373c0__2pnx_]', 0)->plaintext;
								}
							}
							if($datesubmitted ==''){
								if($review->find('span[class=css-chan6m]', 0)){
									$datesubmitted = $review->find('span[class=css-chan6m]', 0)->plaintext;
								}
							}
							if($datesubmitted ==''){
								if($review->find('span[class=yelp-emotion-v293gj]', 0)){
									$datesubmitted = $review->find('span[class=yelp-emotion-v293gj]', 0)->plaintext;
								}
							}
							if($datesubmitted ==''){
								if($review->find('span[class=y-css-wfbtsu]', 0)){
									$datesubmitted = $review->find('span[class=y-css-wfbtsu]', 0)->plaintext;
								}
							}
							 
							 
							 
							// find text
							if($review->find('div.review-content', 0)){
								$rtext = $review->find('div.review-content', 0)->find('p', 0)->plaintext;
							}
							if($rtext ==''){
								if($review->find('p.comment__373c0__3EKjH', 0)){
								$rtext = $review->find('p.comment__373c0__3EKjH', 0)->plaintext;
								}
							}
							if($rtext ==''){
								if($review->find('p[class=comment__09f24__gu0rG]', 0)){
								$rtext = $review->find('p[class=comment__09f24__gu0rG]', 0)->plaintext;
								}
							}
							if($rtext ==''){
								if($review->find('p[class=comment__09f24__D0cxf css-qgunke]', 0)){
								$rtext = $review->find('p[class=comment__09f24__D0cxf css-qgunke]', 0)->plaintext;
								}
							}
				if($rtext ==''){
					if($review->find('span[class=raw__09f24__T4Ezm]', 0)){
					$rtext = $review->find('span[class=raw__09f24__T4Ezm]', 0)->plaintext;
					}
				}
							
							/*
							echo "<br><br>";
							echo "<br>--".$user_name;
							echo "<br>--".$userimage;
							echo "<br>--".$rating;
							echo "<br>--".$datesubmitted;
							echo "<br>--".$rtext;
							die();
							*/
							
							
							if($rating>0 && $user_name!=''){
								$review_length = str_word_count($rtext);
								$pos = strpos($userimage, 'default_avatars');
								if ($pos === false) {
									$userimage = str_replace("60s.jpg","120s.jpg",$userimage);
								}
								$timestamp = strtotime($datesubmitted);
								$timestamp = date("Y-m-d H:i:s", $timestamp);
								//check option to see if this one has been hidden
								//pull array from options table of yelp hidden
								$yelphidden = get_option( 'wpyelp_hidden_reviews' );
								if(!$yelphidden){
									$yelphiddenarray = array('');
								} else {
									$yelphiddenarray = json_decode($yelphidden,true);
								}
								$this_yelp_val = trim($user_name)."-".strtotime($datesubmitted)."-".$review_length."-Yelp-".$rating;
								if (in_array($this_yelp_val, $yelphiddenarray)){
									$hideme = 'yes';
								} else {
									$hideme = 'no';
								}
			
								$reviews[] = [
										'reviewer_name' => trim($user_name),
										'pagename' => trim($pagename),
										'userpic' => $userimage,
										'rating' => $rating,
										'created_time' => $timestamp,
										'created_time_stamp' => strtotime($datesubmitted),
										'review_text' => trim($rtext),
										'hide' => $hideme,
										'review_length' => $review_length,
										'type' => 'Yelp'
								];
								$review_length ='';
							}
					 
							$i++;
					}
					//print_r($reviews);
					//die();
					
				$results['reviews'] = $reviews;
				return $results;
		
		
	}
	
	public function wpyelp_download_yelp_master_typenew($finalstringjson,$pagename){
					
					$reviews = [];
					// Find 20 reviews 
					$i = 1;

					foreach ($finalstringjson['reviews'] as $review) {
							if ($i > 21) {
									break;
							}
							$user_name='';
							$userimage='';
							$rating='';
							$datesubmitted='';
							$rtext='';
							
							//check pagename
							if($pagename==''){
								$pagename = $review['business']['name'];
							}
							// Find user_name
							$user_name = $review['user']['altText'];

							// Find userimage
							$userimage = $review['user']['src'];

							// find rating
							$rating = $review['rating'];

							// find date
							$datesubmitted = $review['localizedDate'];
							
							// find text
							$rtext = html_entity_decode($review['comment']['text']);
							$lang = $review['comment']['language'];

							if($rating>0){
								$review_length = str_word_count($rtext);
								$pos = strpos($userimage, 'default_avatars');
								if ($pos === false) {
									$userimage = str_replace("60s.jpg","120s.jpg",$userimage);
								}
								$timestamp = strtotime($datesubmitted);
								$timestamp = date("Y-m-d H:i:s", $timestamp);
								//check option to see if this one has been hidden
								//pull array from options table of yelp hidden
								$yelphidden = get_option( 'wpyelp_hidden_reviews' );
								if(!$yelphidden){
									$yelphiddenarray = array('');
								} else {
									$yelphiddenarray = json_decode($yelphidden,true);
								}
								$this_yelp_val = trim($user_name)."-".strtotime($datesubmitted)."-".$review_length."-Yelp-".$rating;
								if (in_array($this_yelp_val, $yelphiddenarray)){
									$hideme = 'yes';
								} else {
									$hideme = 'no';
								}
			
								$reviews[] = [
										'reviewer_name' => trim($user_name),
										'pagename' => trim($pagename),
										'userpic' => $userimage,
										'rating' => $rating,
										'created_time' => $timestamp,
										'created_time_stamp' => strtotime($datesubmitted),
										'review_text' => trim($rtext),
										'hide' => $hideme,
										'review_length' => $review_length,
										'type' => 'Yelp'
								];
								$review_length ='';
							}
					 
							$i++;
					}
				
					
					//print_r($reviews);
					
					$results['reviews'] = $reviews;

					return $results;

	}
	
	/**
	 * displays message in admin if it's been longer thandays.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function wprp_admin_notice__success () {

		$activatedtime = get_option('wprev_activated_time_yelp');
		//if this is an old install then use12 days ago
		if($activatedtime==''){
			$activatedtime= time() - (86400*12);
			update_option( 'wprev_activated_time_yelp', $activatedtime );
		}
		$thirtydaysago = time() - (86400*15);
		
		//check if an option was clicked on
		if (isset($_GET['wprevpronotice'])) {
		  $wprevpronotice = $_GET['wprevpronotice'];
		} else {
		  //Handle the case where there is no parameter
		   $wprevpronotice = '';
		}
		if($wprevpronotice=='mlater_yelp'){		//hide the notice for another 15 days
			update_option( 'wprev_notice_hide_yelp', 'later' );
			$newtime = time() - (86400*15);
			update_option( 'wprev_activated_time_yelp', $newtime );
			$activatedtime = $newtime;
			
		} else if($wprevpronotice=='notagain_yelp'){		//hide the notice forever
			update_option( 'wprev_notice_hide_yelp', 'never' );
		}
		
		$wprev_notice_hide = get_option('wprev_notice_hide_yelp');

		if($activatedtime<$thirtydaysago && $wprev_notice_hide!='never'){
		
			$urltrimmedtab = remove_query_arg( array('taction', 'tid', 'sortby', 'sortdir', 'opt') );
			$urlmayberlater = esc_url( add_query_arg( 'wprevpronotice', 'mlater_yelp',$urltrimmedtab ) );
			$urlnotagain = esc_url( add_query_arg( 'wprevpronotice', 'notagain_yelp',$urltrimmedtab ) );
			
			$temphtml = '<p>Hey, I noticed you\'ve been using my <b>WP Yelp Review Slider</b> plugin for a while now – that’s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress? <br>
			Thanks!<br>
			~ Josh W.<br></p>
			<ul>
			<li><a href="https://wordpress.org/support/plugin/wp-yelp-review-slider/reviews/#new-post" target="_blank">Ok, you deserve it</a></li>
			<li><a href="'.$urlmayberlater.'">Not right now, maybe later</a></li>
			<li><a href="'.$urlnotagain.'">Don\'t remind me again</a></li>
			</ul>
			<p>P.S. If you\'ve been thinking about upgrading to the <a href="https://wpreviewslider.com/" target="_blank">Pro</a> version, here\'s a 10% off coupon code you can use! ->  <b>wprevpro10off</b></p>';
			
			?>
			<div class="notice notice-info">
				<div class="wprevpro_admin_notice" style="color: #007500;">
				<?php _e( $temphtml, $this->_token ); ?>
				</div>
			</div>
			<?php
		}

	}
				/**
	 * add dashboard widget to wordpress admin
	 * @access  public
	 * @since   5.9
	 * @return  void
	 */
	public function wprevyelp_dashboard_widget() {
		global $wp_meta_boxes;
		//wp_add_dashboard_widget('custom_help_widget', 'Theme Support', 'custom_yelp_dashboard');
		add_meta_box( 'id', 'WP Yelp Review Slider Recent Reviews', array($this,'custom_yelp_dashboard'), 'dashboard', 'side', 'high' );
	}
	 
	public function custom_yelp_dashboard() {
		global $wpdb;
		$reviews_table_name = $wpdb->prefix . 'wpyelp_reviews';
		$tempquery = "select * from ".$reviews_table_name." ORDER by created_time_stamp Desc limit 4";
		$reviewrows = $wpdb->get_results($tempquery);
		$now = time(); // or your date as well
		
		echo '<style>
			img.wprev_dash_avatar {float: left;margin-right: 8px;border-radius: 20px;}
			.wprev_dash_stars {float: right;width: 100px;}
			p.wprev_dash_text {margin-top: -6px;}
			span.wprev_dash_timeago {font-size: 12px;font-style: italic;}
			.wprev_dash_revdiv {min-height: 50px;}
			</style>';
		echo '<ul>';
		foreach ( $reviewrows as $review ) 
		{
			$timesince = '';
			if(strlen($review->review_text)>130){
				$reviewtext = substr($review->review_text,0,130).'...';
			} else {
				$reviewtext = $review->review_text;
			}
			
			$your_date = $review->created_time_stamp;
			$datediff = $now - $your_date;
			$daysago = round($datediff / (60 * 60 * 24));
			if($daysago==1){
				$daysagohtml = $daysago.' day ago';
			} else {
				$daysagohtml = $daysago.' days ago';
			}
			if($review->rating<1){
				if($review->recommendation_type=='positive'){
					$review->rating=5;
				} else {
					$review->rating=2;
				}
			}

			$imgs_url = plugin_dir_url(__DIR__).'/public/partials/imgs/';
			$starfile = 'yelp_stars_'.$review->rating.'.png';
			$starhtml='<img src="'.$imgs_url."".$starfile.'" alt="'.$review->rating.' star rating" class="wprev_dash_stars">';
			
			$avatarhtml = '';
			if(isset($review->userpic) && $review->userpic!=''){
				$avatarhtml = '<img alt="" src="'.$review->userpic.'" class="wprev_dash_avatar" height="40" width="40">';
			}
			
			echo '<li><div class="wprev_dash_revdiv">'.$avatarhtml.'<div class="wprev_dash_stars">'.$starhtml.'</div><h4 class="wprev_dash_name">'.$review->reviewer_name.' - <span class="wprev_dash_timeago">'.$daysagohtml.'</span></h4><p class="wprev_dash_text">'.$reviewtext.'</p></div></li>';
			
		}
		echo '</ul>';
		
		echo '<div><a href="admin.php?page=wp_yelp-reviews">All Reviews</a> - <a href="https://wpreviewslider.com/" target="_blank">Go Pro For More Cool Features!</a></div>';
	}
	
		//add link to menu
	public function wprev_yelp_add_external_link_admin_submenu() {
		global $submenu;

		$menu_slug = 'wp_yelp-welcome'; // used as "key" in menus

		if (array_key_exists($menu_slug, $submenu)) {
		// add the external links to the slug you used when adding the top level menu
		$submenu[$menu_slug][] = array('<div id="wprev-66040">Go Pro!</div>', 'manage_options', 'https://wpreviewslider.com/');
		}
	}

	/**
	 * Ajax: return the rendered template HTML for the live preview.
	 *
	 * @since 9.0
	 */
	public function wpyelp_previewtemplate_ajax() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
			return;
		}

		check_ajax_referer( 'randomnoncestring', 'wpyelp_nonce' );

		$tid = isset( $_POST['tid'] ) ? absint( $_POST['tid'] ) : 0;

		$returnarray = $this->wpyelp_previewtemplate_ajax_get( $tid );
		echo wp_json_encode( $returnarray );
		die();
	}

	/**
	 * Build preview HTML for a template id using the public shortcode renderer.
	 *
	 * @since 9.0
	 * @param int $tid Template id.
	 * @return array
	 */
	public function wpyelp_previewtemplate_ajax_get( $tid ) {
		$atts = array( 'tid' => absint( $tid ) );
		require_once plugin_dir_path( __DIR__ ) . 'public/class-wp-yelp-review-slider-public.php';
		$plugin_public_class = new WP_Yelp_Review_Public( $this->_token, $this->version );
		$templatehtml        = $plugin_public_class->wpyelp_usetemplate_func( $atts, null );

		return array(
			'tid'          => absint( $tid ),
			'ack'          => 'success',
			'templatehtml' => $templatehtml,
		);
	}

	/**
	 * Ajax: save (insert/update) a review template then return its preview HTML.
	 *
	 * Mirrors the page-POST save logic in admin/partials/templates_posts.php so
	 * the live preview reflects exactly what will be stored.
	 *
	 * @since 9.0
	 */
	public function wpyelp_savetemplate_ajax() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
			return;
		}

		check_ajax_referer( 'randomnoncestring', 'wpyelp_nonce' );

		$formdata  = isset( $_POST['data'] ) ? stripslashes( $_POST['data'] ) : '';
		$formarray = json_decode( $formdata, true );
		if ( ! is_array( $formarray ) ) {
			echo wp_json_encode( array( 'ack' => 'error', 'ackmessage' => 'Invalid form data.' ) );
			die();
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpyelp_post_templates';

		$get = function( $key, $default = '' ) use ( $formarray ) {
			return isset( $formarray[ $key ] ) ? $formarray[ $key ] : $default;
		};

		$t_id             = sanitize_text_field( $get( 'edittid' ) );
		$title            = sanitize_text_field( $get( 'wpyelp_template_title' ) );
		$template_type    = sanitize_text_field( $get( 'wpyelp_template_type', 'post' ) );
		$style            = sanitize_text_field( $get( 'wpyelp_template_style', '1' ) );
		$display_num      = sanitize_text_field( $get( 'wpyelp_t_display_num', '3' ) );
		$display_num_rows = sanitize_text_field( $get( 'wpyelp_t_display_num_rows', '1' ) );
		$display_order    = sanitize_text_field( $get( 'wpyelp_t_display_order', 'newest' ) );
		$hide_no_text     = sanitize_text_field( $get( 'wpyelp_t_hidenotext', 'no' ) );
		$template_css     = sanitize_text_field( $get( 'wpyelp_template_css' ) );
		$createslider     = sanitize_text_field( $get( 'wpyelp_t_createslider', 'yes' ) );
		$numslides        = sanitize_text_field( $get( 'wpyelp_t_numslides', '3' ) );
		$read_more        = sanitize_text_field( $get( 'wprevpro_t_read_more', 'no' ) );
		$read_more_text   = sanitize_text_field( $get( 'wprevpro_t_read_more_text', 'read more' ) );
		$min_rating       = sanitize_text_field( $get( 'wpyelp_t_min_rating', '1' ) );
		$rpage            = sanitize_text_field( $get( 'wpyelp_t_filtersource' ) );

		//template misc (style + badge settings, stored as JSON)
		$templatemiscarray = array();
		$templatemiscarray['showstars'] = sanitize_text_field( $get( 'wpyelp_template_misc_showstars' ) );
		$templatemiscarray['showdate']  = sanitize_text_field( $get( 'wpyelp_template_misc_showdate' ) );
		$templatemiscarray['bgcolor1']  = WP_Yelp_Review_Sanitize::sanitize_css_color( $get( 'wpyelp_template_misc_bgcolor1' ) );
		$templatemiscarray['bgcolor2']  = WP_Yelp_Review_Sanitize::sanitize_css_color( $get( 'wpyelp_template_misc_bgcolor2' ) );
		$templatemiscarray['tcolor1']   = WP_Yelp_Review_Sanitize::sanitize_css_color( $get( 'wpyelp_template_misc_tcolor1' ) );
		$templatemiscarray['tcolor2']   = WP_Yelp_Review_Sanitize::sanitize_css_color( $get( 'wpyelp_template_misc_tcolor2' ) );
		$templatemiscarray['tcolor3']   = WP_Yelp_Review_Sanitize::sanitize_css_color( $get( 'wpyelp_template_misc_tcolor3' ) );
		$templatemiscarray['bradius']   = sanitize_text_field( $get( 'wpyelp_template_misc_bradius' ) );
		$templatemiscarray['showmedia'] = sanitize_text_field( $get( 'wpyelp_t_showmedia', 'yes' ) );

		// Style-tab options.
		$templatemiscarray['verified']       = sanitize_text_field( $get( 'wpyelp_template_misc_verified', 'no' ) );
		$templatemiscarray['lastnameformat'] = sanitize_text_field( $get( 'wpyelp_template_misc_lastname', 'show' ) );
		$templatemiscarray['avataropt']      = sanitize_text_field( $get( 'wpyelp_template_misc_avataropt', 'show' ) );
		$templatemiscarray['showicon']       = sanitize_text_field( $get( 'wpyelp_template_misc_showicon', 'lin' ) );
		$ajax_tfont1 = absint( $get( 'wpyelp_template_misc_tfont1', 0 ) );
		$ajax_tfont2 = absint( $get( 'wpyelp_template_misc_tfont2', 0 ) );
		$templatemiscarray['tfont1'] = $ajax_tfont1 > 0 ? (string) $ajax_tfont1 : '';
		$templatemiscarray['tfont2'] = $ajax_tfont2 > 0 ? (string) $ajax_tfont2 : '';

		// General-tab options (slider + read more).
		$templatemiscarray['slidespeed']         = sanitize_text_field( $get( 'wpyelp_t_slidespeed', '1' ) );
		$templatemiscarray['slideautodelay']     = sanitize_text_field( $get( 'wpyelp_t_slideautodelay', '5' ) );
		$templatemiscarray['sliderautoplay']     = sanitize_text_field( $get( 'wpyelp_sliderautoplay' ) );
		$templatemiscarray['sliderhideprevnext'] = sanitize_text_field( $get( 'wpyelp_sliderhideprevnext' ) );
		$templatemiscarray['sliderhidedots']     = sanitize_text_field( $get( 'wpyelp_sliderhidedots' ) );
		$templatemiscarray['sliderfixedheight']  = sanitize_text_field( $get( 'wpyelp_sliderfixedheight' ) );
		$templatemiscarray['slidermobileview']   = sanitize_text_field( $get( 'wpyelp_slidermobileview' ) );
		$templatemiscarray['review_same_height'] = sanitize_text_field( $get( 'wpyelp_t_review_same_height', 'no' ) );
		$templatemiscarray['read_more_num']      = sanitize_text_field( $get( 'wprevpro_t_read_more_num', '30' ) );
		$templatemiscarray['read_more_color']    = WP_Yelp_Review_Sanitize::sanitize_css_color( $get( 'wprevpro_t_read_more_color' ) );

		// Badge options.
		$templatemiscarray['blocation'] = sanitize_text_field( $get( 'wpyelp_t_blocation' ) );
		$templatemiscarray['bname']     = sanitize_text_field( $get( 'wpyelp_t_bname' ) );
		$templatemiscarray['bnameurl']  = esc_url_raw( $get( 'wpyelp_t_bnameurl' ) );
		$templatemiscarray['bimgurl']   = esc_url_raw( $get( 'wpyelp_t_bimgurl' ) );
		$templatemiscarray['bshape']    = sanitize_text_field( $get( 'wpyelp_t_bshape' ) );
		$templatemiscarray['bimgsize']  = sanitize_text_field( $get( 'wpyelp_t_bimgsize', '50' ) );
		$templatemiscarray['bbtnurl']   = esc_url_raw( $get( 'wpyelp_t_bbtnurl' ) );
		$templatemiscarray['bbradius']  = sanitize_text_field( $get( 'wpyelp_t_bbradius', '0' ) );
		$templatemiscarray['bbwidth']   = sanitize_text_field( $get( 'wpyelp_t_bbwidth', '0' ) );
		$templatemiscarray['bbtncolor'] = WP_Yelp_Review_Sanitize::sanitize_css_color( $get( 'wpyelp_t_bbtncolor', '#d32323' ) );
		$templatemiscarray['bbkcolor']  = WP_Yelp_Review_Sanitize::sanitize_css_color( $get( 'wpyelp_t_bbkcolor', '#ffffff' ) );
		$templatemiscarray['bbcolor']   = WP_Yelp_Review_Sanitize::sanitize_css_color( $get( 'wpyelp_t_bbcolor', '#eeeeee' ) );
		$templatemiscarray['bdropsh']   = sanitize_text_field( $get( 'wpyelp_t_bdropsh' ) );
		$templatemiscarray['bcenter']   = sanitize_text_field( $get( 'wpyelp_t_bcenter' ) );
		$templatemiscarray['bhname']    = sanitize_text_field( $get( 'wpyelp_t_bhname' ) );
		$templatemiscarray['bhphoto']   = sanitize_text_field( $get( 'wpyelp_t_bhphoto' ) );
		$templatemiscarray['bhbased']   = sanitize_text_field( $get( 'wpyelp_t_bhbased' ) );
		$templatemiscarray['bhbtn']     = sanitize_text_field( $get( 'wpyelp_t_bhbtn' ) );
		$templatemiscarray['bhpow']     = sanitize_text_field( $get( 'wpyelp_t_bhpow' ) );
		$templatemiscarray['bhreviews'] = sanitize_text_field( $get( 'wpyelp_t_bhreviews' ) );
		$templatemiscarray['bobasedon'] = sanitize_text_field( $get( 'wpyelp_t_bobasedon' ) );
		$templatemiscarray['borevus']   = sanitize_text_field( $get( 'wpyelp_t_borevus' ) );

		$templatemiscjson = wp_json_encode( $templatemiscarray );
		$timenow          = time();

		$data = array(
			'title'              => $title,
			'template_type'      => $template_type,
			'style'              => $style,
			'created_time_stamp' => $timenow,
			'display_num'        => $display_num,
			'display_num_rows'   => $display_num_rows,
			'display_order'      => $display_order,
			'hide_no_text'       => $hide_no_text,
			'template_css'       => $template_css,
			'min_rating'         => $min_rating,
			'min_words'          => '',
			'max_words'          => '',
			'rtype'              => '',
			'rpage'              => $rpage,
			'createslider'       => $createslider,
			'numslides'          => $numslides,
			'sliderautoplay'     => '',
			'sliderdirection'    => '',
			'sliderarrows'       => '',
			'sliderdots'         => '',
			'sliderdelay'        => '',
			'sliderheight'       => '',
			'showreviewsbyid'    => '',
			'template_misc'      => $templatemiscjson,
			'read_more'          => $read_more,
			'read_more_text'     => $read_more_text,
		);
		$format = array(
			'%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%d',
			'%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s',
			'%d', '%s', '%s', '%s', '%s', '%s',
		);

		$returnarray = array(
			'iu'         => '',
			'ack'        => '',
			'ackmessage' => '',
			't_id'       => '',
		);

		if ( $t_id === '' ) {
			$returnarray['iu'] = 'insert';
			$inserttemplate    = $wpdb->insert( $table_name, $data, $format );
			$t_id              = $wpdb->insert_id;
			if ( ! $inserttemplate ) {
				$returnarray['ack']        = 'error';
				$returnarray['ackmessage'] = __( 'Unable to update. Try refreshing the page.', 'wp-yelp-review-slider' );
			} else {
				$returnarray['ack']        = 'success';
				$returnarray['ackmessage'] = __( 'Template Saved!', 'wp-yelp-review-slider' );
			}
		} else {
			$returnarray['iu'] = 'update';
			$updatetempquery   = $wpdb->update( $table_name, $data, array( 'id' => absint( $t_id ) ), $format, array( '%d' ) );
			if ( false === $updatetempquery ) {
				$returnarray['ack']        = 'error';
				$returnarray['ackmessage'] = __( 'Unable to update. Try refreshing the page.', 'wp-yelp-review-slider' );
			} else {
				$returnarray['ack']        = 'success';
				$returnarray['ackmessage'] = __( 'Template Updated!', 'wp-yelp-review-slider' );
			}
		}

		$returnarray['t_id']         = absint( $t_id );
		$returnpreview               = $this->wpyelp_previewtemplate_ajax_get( absint( $t_id ) );
		$returnarray['templatehtml'] = $returnpreview['templatehtml'];

		echo wp_json_encode( $returnarray );
		die();
	}

}
