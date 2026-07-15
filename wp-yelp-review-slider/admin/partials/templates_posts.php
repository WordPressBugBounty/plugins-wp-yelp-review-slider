<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://ljapps.com
 * @since      1.0.0
 *
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/admin/partials
 */
 
     // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
	$dbmsg = "";
	$html="";
	$currenttemplate= new stdClass();
	$currenttemplate->id="";
	$currenttemplate->title ="";
	$currenttemplate->template_type ="";
	$currenttemplate->style ="";
	$currenttemplate->created_time_stamp ="";
	$currenttemplate->display_num ="";
	$currenttemplate->display_num_rows ="";
	$currenttemplate->display_order ="";
	$currenttemplate->hide_no_text ="";
	$currenttemplate->template_css ="";
	$currenttemplate->min_rating ="";
	$currenttemplate->min_words ="";
	$currenttemplate->max_words ="";
	$currenttemplate->rtype ="";
	$currenttemplate->rpage ="";
	$currenttemplate->showreviewsbyid ="";
	$currenttemplate->createslider ="";
	$currenttemplate->numslides ="";
	$currenttemplate->sliderautoplay ="";
	$currenttemplate->sliderdirection ="";
	$currenttemplate->sliderarrows ="";
	$currenttemplate->sliderdots ="";
	$currenttemplate->sliderdelay ="";
	$currenttemplate->sliderheight ="";
	$currenttemplate->template_misc ="";
	$currenttemplate->read_more ="";
	$currenttemplate->read_more_text ="read more";
	
	//echo $this->_token;
	//if token = wp-fb-reviews then using free version
	
	//db function variables
	global $wpdb;
	$table_name = $wpdb->prefix . 'wpyelp_post_templates';
	
	//form deleting and updating here---------------------------
	if(isset($_GET['taction'])){
		$tid = htmlentities($_GET['tid']);
		$tid = intval($tid);
		//for deleting
		if($_GET['taction'] == "del" && $_GET['tid'] > 0){
			//security
			check_admin_referer( 'tdel_');
			//delete
			$wpdb->delete( $table_name, array( 'id' => $tid ), array( '%d' ) );
		}
		//for updating
		if($_GET['taction'] == "edit" && $_GET['tid'] > 0){
			//security
			check_admin_referer( 'tedit_');
			//get form array
			$currenttemplate = $wpdb->get_row( "SELECT * FROM ".$table_name." WHERE id = ".$tid );
		}
		
	}
	//------------------------------------------

	//form posting here--------------------------------
	//check to see if form has been posted.
	//if template id present then update database if not then insert as new.

	if (isset($_POST['wpyelp_submittemplatebtn'])){
		//verify nonce wp_nonce_field( 'wpyelp_save_template');
		check_admin_referer( 'wpyelp_save_template');

		//get form submission values and then save or update
		$t_id = htmlentities($_POST['edittid']);
		$title = htmlentities($_POST['wpyelp_template_title']);
		$template_type = htmlentities($_POST['wpyelp_template_type']);
		$style = htmlentities($_POST['wpyelp_template_style']);
		$display_num = htmlentities($_POST['wpyelp_t_display_num']);
		$display_num_rows = htmlentities($_POST['wpyelp_t_display_num_rows']);
		$display_order = htmlentities($_POST['wpyelp_t_display_order']);
		$hide_no_text = htmlentities($_POST['wpyelp_t_hidenotext']);
		$template_css = htmlentities($_POST['wpyelp_template_css']);
		
		$createslider = htmlentities($_POST['wpyelp_t_createslider']);
		$numslides = htmlentities($_POST['wpyelp_t_numslides']);
		
		$read_more = sanitize_text_field($_POST['wprevpro_t_read_more']);
		$read_more_text = sanitize_text_field($_POST['wprevpro_t_read_more_text']);
		

		
		//santize
		$title = sanitize_text_field( $title );
		$template_type = sanitize_text_field( $template_type );
		$display_order = sanitize_text_field( $display_order );
		$template_css = sanitize_text_field( $template_css );
		$display_order = sanitize_text_field( $display_order );

		
		//template misc
		$templatemiscarray = array();
		$templatemiscarray['showstars']=htmlentities($_POST['wpyelp_template_misc_showstars']);
		$templatemiscarray['showdate']=htmlentities($_POST['wpyelp_template_misc_showdate']);
		$templatemiscarray['bgcolor1']=htmlentities($_POST['wpyelp_template_misc_bgcolor1']);
		$templatemiscarray['bgcolor2']=htmlentities($_POST['wpyelp_template_misc_bgcolor2']);
		$templatemiscarray['tcolor1']=htmlentities($_POST['wpyelp_template_misc_tcolor1']);
		$templatemiscarray['tcolor2']=htmlentities($_POST['wpyelp_template_misc_tcolor2']);
		$templatemiscarray['tcolor3']=htmlentities($_POST['wpyelp_template_misc_tcolor3']);
		$templatemiscarray['bradius']=htmlentities($_POST['wpyelp_template_misc_bradius']);
		$templatemiscarray['showmedia']= isset($_POST['wpyelp_t_showmedia']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_showmedia'] ) ) : 'yes';

		// Style-tab options (ported from TripAdvisor).
		$templatemiscarray['verified']       = isset($_POST['wpyelp_template_misc_verified']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_template_misc_verified'] ) ) : 'no';
		$templatemiscarray['lastnameformat'] = isset($_POST['wpyelp_template_misc_lastname']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_template_misc_lastname'] ) ) : 'show';
		$templatemiscarray['avataropt']      = isset($_POST['wpyelp_template_misc_avataropt']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_template_misc_avataropt'] ) ) : 'show';
		$templatemiscarray['showicon']       = isset($_POST['wpyelp_template_misc_showicon']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_template_misc_showicon'] ) ) : 'lin';
		$tfont1_val = isset($_POST['wpyelp_template_misc_tfont1']) ? absint( $_POST['wpyelp_template_misc_tfont1'] ) : 0;
		$tfont2_val = isset($_POST['wpyelp_template_misc_tfont2']) ? absint( $_POST['wpyelp_template_misc_tfont2'] ) : 0;
		$templatemiscarray['tfont1'] = $tfont1_val > 0 ? (string) $tfont1_val : '';
		$templatemiscarray['tfont2'] = $tfont2_val > 0 ? (string) $tfont2_val : '';

		// General-tab options (slider + read more).
		$templatemiscarray['slidespeed']         = isset($_POST['wpyelp_t_slidespeed']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_slidespeed'] ) ) : '1';
		$templatemiscarray['slideautodelay']     = isset($_POST['wpyelp_t_slideautodelay']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_slideautodelay'] ) ) : '5';
		$templatemiscarray['sliderautoplay']     = isset($_POST['wpyelp_sliderautoplay']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_sliderautoplay'] ) ) : '';
		$templatemiscarray['sliderhideprevnext'] = isset($_POST['wpyelp_sliderhideprevnext']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_sliderhideprevnext'] ) ) : '';
		$templatemiscarray['sliderhidedots']     = isset($_POST['wpyelp_sliderhidedots']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_sliderhidedots'] ) ) : '';
		$templatemiscarray['sliderfixedheight']  = isset($_POST['wpyelp_sliderfixedheight']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_sliderfixedheight'] ) ) : '';
		$templatemiscarray['slidermobileview']   = isset($_POST['wpyelp_slidermobileview']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_slidermobileview'] ) ) : '';
		$templatemiscarray['review_same_height'] = isset($_POST['wpyelp_t_review_same_height']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_review_same_height'] ) ) : 'no';
		$templatemiscarray['read_more_num']      = isset($_POST['wprevpro_t_read_more_num']) ? sanitize_text_field( wp_unslash( $_POST['wprevpro_t_read_more_num'] ) ) : '30';
		$templatemiscarray['read_more_color']    = isset($_POST['wprevpro_t_read_more_color']) ? WP_Yelp_Review_Sanitize::sanitize_css_color( wp_unslash( $_POST['wprevpro_t_read_more_color'] ) ) : '';

		// Badge options (stored in template_misc JSON).
		$templatemiscarray['blocation'] = isset( $_POST['wpyelp_t_blocation'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_blocation'] ) ) : '';
		$templatemiscarray['bname']     = isset( $_POST['wpyelp_t_bname'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bname'] ) ) : '';
		$templatemiscarray['bnameurl']  = isset( $_POST['wpyelp_t_bnameurl'] ) ? esc_url_raw( wp_unslash( $_POST['wpyelp_t_bnameurl'] ) ) : '';
		$templatemiscarray['bimgurl']   = isset( $_POST['wpyelp_t_bimgurl'] ) ? esc_url_raw( wp_unslash( $_POST['wpyelp_t_bimgurl'] ) ) : '';
		$templatemiscarray['bshape']    = isset( $_POST['wpyelp_t_bshape'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bshape'] ) ) : '';
		$templatemiscarray['bimgsize']  = isset( $_POST['wpyelp_t_bimgsize'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bimgsize'] ) ) : '50';
		$templatemiscarray['bbtnurl']   = isset( $_POST['wpyelp_t_bbtnurl'] ) ? esc_url_raw( wp_unslash( $_POST['wpyelp_t_bbtnurl'] ) ) : '';
		$templatemiscarray['bbradius']  = isset( $_POST['wpyelp_t_bbradius'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bbradius'] ) ) : '0';
		$templatemiscarray['bbwidth']   = isset( $_POST['wpyelp_t_bbwidth'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bbwidth'] ) ) : '0';
		$templatemiscarray['bbtncolor'] = isset( $_POST['wpyelp_t_bbtncolor'] ) ? WP_Yelp_Review_Sanitize::sanitize_css_color( wp_unslash( $_POST['wpyelp_t_bbtncolor'] ) ) : '#d32323';
		$templatemiscarray['bbkcolor']  = isset( $_POST['wpyelp_t_bbkcolor'] ) ? WP_Yelp_Review_Sanitize::sanitize_css_color( wp_unslash( $_POST['wpyelp_t_bbkcolor'] ) ) : '#ffffff';
		$templatemiscarray['bbcolor']   = isset( $_POST['wpyelp_t_bbcolor'] ) ? WP_Yelp_Review_Sanitize::sanitize_css_color( wp_unslash( $_POST['wpyelp_t_bbcolor'] ) ) : '#eeeeee';
		$templatemiscarray['bdropsh']   = isset( $_POST['wpyelp_t_bdropsh'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bdropsh'] ) ) : '';
		$templatemiscarray['bcenter']   = isset( $_POST['wpyelp_t_bcenter'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bcenter'] ) ) : '';
		$templatemiscarray['bhname']    = isset( $_POST['wpyelp_t_bhname'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bhname'] ) ) : '';
		$templatemiscarray['bhphoto']   = isset( $_POST['wpyelp_t_bhphoto'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bhphoto'] ) ) : '';
		$templatemiscarray['bhbased']   = isset( $_POST['wpyelp_t_bhbased'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bhbased'] ) ) : '';
		$templatemiscarray['bhbtn']     = isset( $_POST['wpyelp_t_bhbtn'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bhbtn'] ) ) : '';
		$templatemiscarray['bhpow']     = isset( $_POST['wpyelp_t_bhpow'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bhpow'] ) ) : '';
		$templatemiscarray['bhreviews'] = isset( $_POST['wpyelp_t_bhreviews'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bhreviews'] ) ) : '';
		$templatemiscarray['bobasedon'] = isset( $_POST['wpyelp_t_bobasedon'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_bobasedon'] ) ) : '';
		$templatemiscarray['borevus']   = isset( $_POST['wpyelp_t_borevus'] ) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_borevus'] ) ) : '';

		$templatemiscjson = json_encode($templatemiscarray);
		
		$min_rating = sanitize_text_field($_POST['wpyelp_t_min_rating']);
		
		//only save if using pro version
			$min_words = "";
			$max_words = "";			
			$rtype = "";
			// Chosen source (pageid) reviews are filtered by. Empty = all/fallback.
			$rpage = isset($_POST['wpyelp_t_filtersource']) ? sanitize_text_field( wp_unslash( $_POST['wpyelp_t_filtersource'] ) ) : "";
			$showreviewsbyid="";
			$sliderautoplay = "";
			$sliderdirection = "";
			$sliderarrows = "";
			$sliderdots = "";
			$sliderdelay = "";
			$sliderheight = "";

		$timenow = time();
		
		//+++++++++need to sql escape using prepare+++++++++++++++++++
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++
		//insert or update
			$data = array( 
				'title' => "$title",
				'template_type' => "$template_type",
				'style' => "$style",
				'created_time_stamp' => "$timenow",
				'display_num' => "$display_num",
				'display_num_rows' => "$display_num_rows",
				'display_order' => "$display_order", 
				'hide_no_text' => "$hide_no_text",
				'template_css' => "$template_css", 
				'min_rating' => "$min_rating", 
				'min_words' => "$min_words",
				'max_words' => "$max_words",
				'rtype' => "$rtype", 
				'rpage' => "$rpage",
				'createslider' => "$createslider",
				'numslides' => "$numslides",
				'sliderautoplay' => "$sliderautoplay",
				'sliderdirection' => "$sliderdirection",
				'sliderarrows' => "$sliderarrows",
				'sliderdots' => "$sliderdots",
				'sliderdelay' => "$sliderdelay",
				'sliderheight' => "$sliderheight",
				'showreviewsbyid' => "$showreviewsbyid",
				'template_misc' => "$templatemiscjson",
				'read_more' => "$read_more",
				'read_more_text' => "$read_more_text"
				);
			$format = array( 
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
					'%s',
					'%s'
				); 

		if($t_id==""){
			//insert
			$wpdb->insert( $table_name, $data, $format );
				//exit( var_dump( $wpdb->last_error ) );
				//Print last SQL query string
				//$wpdb->last_query;
				// Print last SQL query result
				//$wpdb->last_result;
				// Print last SQL query Error
				//$wpdb->last_error;
		} else {
			//update
			$updatetempquery = $wpdb->update($table_name, $data, array( 'id' => $t_id ), $format, array( '%d' ));
			if($updatetempquery>0){
				$dbmsg = '<div id="setting-error-wpyelp_message" class="updated settings-error notice is-dismissible">'.__('<p><strong>Template Updated!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>', 'wp-fb-reviews').'</div>';
			}
		}
		
	}

	//Get list of all current forms--------------------------
	$currentforms = $wpdb->get_results("SELECT id, title, template_type, created_time_stamp, style, createslider FROM $table_name");
	
	//-------------------------------------------------------
	
	
	
	//check to see if reviews are in database
	//total number of rows
	$reviews_table_name = $wpdb->prefix . 'wpyelp_reviews';
	$reviewtotalcount = $wpdb->get_var( 'SELECT COUNT(*) FROM '.$reviews_table_name );
	if($reviewtotalcount<1){
		$dbmsg = $dbmsg . '<div id="setting-error-wpyelp_message" class="updated settings-error notice is-dismissible">'.__('<p><strong>No reviews found. Please visit the <a href="?page=wp_yelp-get_yelp">Get Yelp Reviews</a> page to retrieve reviews from Yelp.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>', 'wp-fb-reviews').'</div>';
	}
	
	//add thickbox
	add_thickbox();
	
?>
<div class="">
<h1></h1>
<div class="wrap" id="wp_rev_maindiv">

<img class="wprev_headerimg" src="<?php echo plugin_dir_url( __FILE__ ) . 'logo.png?v='.$this->version; ?>">
<?php 
include("tabmenu.php");
?>	
<div class="wpfbr_margin10">
<div class="w3-col welcomediv w3-container w3-white w3-border w3-border-light-gray2 w3-round-small">


<div class="wpyelp_margin10">
	<a id="wpyelp_helpicon_posts" class="wpyelp_btnicononly button dashicons-before dashicons-editor-help"></a>
	<a id="wpyelp_addnewtemplate" class="button dashicons-before dashicons-plus-alt"><?php _e('Add New Reviews Template', 'wp-fb-reviews'); ?></a>
</div>

<?php
//display message
echo $dbmsg;
		$html .= '
		<table class="wp-list-table widefat striped posts">
			<thead>
				<tr>
					<th scope="col" width="30px" class="manage-column">'.__('ID', 'wp-fb-reviews').'</th>
					<th scope="col" class="manage-column">'.__('Title', 'wp-fb-reviews').'</th>
					<th scope="col" width="100px" class="manage-column">'.__('Slider', 'wp-fb-reviews').'</th>
					<th scope="col" width="170px" class="manage-column">'.__('Date Created', 'wp-fb-reviews').'</th>
					<th scope="col" width="300px" class="manage-column">'.__('Action', 'wp-fb-reviews').'</th>
				</tr>
				</thead>
			<tbody id="review_list">';
	$haswidgettemplate = false;	//for hiding widget type, going to be phasing widget types out.
	foreach ( $currentforms as $currentform ) 
	{
	//remove query args we just used
	$urltrimmed = remove_query_arg( array('taction', 'id') );
		$tempeditbtn =  add_query_arg(  array(
			'taction' => 'edit',
			'tid' => "$currentform->id",
			),$urltrimmed);
			
		$url_tempeditbtn = wp_nonce_url( $tempeditbtn, 'tedit_');
			
		$tempdelbtn = add_query_arg(  array(
			'taction' => 'del',
			'tid' => "$currentform->id",
			),$urltrimmed) ;
			
		$url_tempdelbtn = wp_nonce_url( $tempdelbtn, 'tdel_');
		if($currentform->template_type=='widget'){
			$haswidgettemplate = true;
		}		
		$html .= '<tr id="'.$currentform->id.'">
				<th scope="col" class="wpyelp_upgrade_needed manage-column">'.$currentform->id.'</th>
				<th scope="col" class="wpyelp_upgrade_needed manage-column"><b>'.$currentform->title.'</b></th>
				<th scope="col" class="wpyelp_upgrade_needed manage-column"><b>'.$currentform->createslider.'</b></th>
				<th scope="col" class="wpyelp_upgrade_needed manage-column">'.date("F j, Y",$currentform->created_time_stamp) .'</th>
				<th scope="col" class="manage-column" templateid="'.$currentform->id.'" templatetype="'.$currentform->template_type.'"><a href="'.$url_tempeditbtn.'" class="button button-primary dashicons-before dashicons-admin-generic">'.__('Edit', 'wp-fb-reviews').'</a> <a href="'.$url_tempdelbtn.'" class="button button-secondary dashicons-before dashicons-trash">'.__('Delete', 'wp-fb-reviews').'</a> <a class="wpyelp_displayshortcode button button-secondary dashicons-before dashicons-visibility">'.__('Shortcode', 'wp-fb-reviews').'</a></th>
			</tr>';
	}	
		$html .= '</tbody></table>';
			
 echo $html;			
?>
<div class="wpyelp_margin10" id="wpyelp_new_template">
<form name="newtemplateform" id="newtemplateform" action="?page=wp_yelp-templates_posts" method="post" onsubmit="return validateForm()">
	<table class="wpyelp_margin10 form-table ">
		<tbody>
			<tr class="wpyelp_row">
				<th scope="row">
					<?php _e('Template Title:', 'wp-yelp-review-slider'); ?>
				</th>
				<td>
					<input id="wpyelp_template_title" data-custom="custom" type="text" name="wpyelp_template_title" placeholder="" value="<?php echo esc_attr( $currenttemplate->title ); ?>" required>
					<p class="description">
					<?php _e('Enter a title or name for this template.', 'wp-yelp-review-slider'); ?></p>
				</td>
			</tr>
			<tr <?php if($haswidgettemplate==false){echo "style='display:none;'";} ?> class="wpyelp_row">
				<th scope="row">
					<?php _e('Choose Template Type:', 'wp-yelp-review-slider'); ?>
				</th>
				<td><div id="divtemplatestyles">
					<input type="radio" name="wpyelp_template_type" id="wpyelp_template_type1-radio" value="post" checked="checked">
					<label for="wpyelp_template_type1-radio"><?php _e('Post or Page', 'wp-yelp-review-slider'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="wpyelp_template_type" id="wpyelp_template_type2-radio" value="widget" <?php if($currenttemplate->template_type== "widget"){echo 'checked="checked"';}?>>
					<label for="wpyelp_template_type2-radio"><?php _e('Widget Area', 'wp-yelp-review-slider'); ?></label>
					</div>
					<p class="description">
					<?php _e('Are you going to use this on a Page/Post or in a Widget area like your sidebar?', 'wp-yelp-review-slider'); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

<?php
$template_misc_array = json_decode($currenttemplate->template_misc, true);
if(!is_array($template_misc_array)){
	$template_misc_array=array();
	$template_misc_array['showstars']="";
	$template_misc_array['showdate']="";
	$template_misc_array['bgcolor1']="";
	$template_misc_array['bgcolor2']="";
	$template_misc_array['tcolor1']="";
	$template_misc_array['tcolor2']="";
	$template_misc_array['tcolor3']="";
	$template_misc_array['bradius']="0";
}
if(!isset($template_misc_array['showmedia']) || $template_misc_array['showmedia']===''){
	$template_misc_array['showmedia']='yes';
}
//style-tab defaults (ported from TripAdvisor)
if(!isset($template_misc_array['verified'])){ $template_misc_array['verified']='yes1'; }
if(!isset($template_misc_array['lastnameformat'])){ $template_misc_array['lastnameformat']='show'; }
if(!isset($template_misc_array['avataropt'])){ $template_misc_array['avataropt']='show'; }
if(!isset($template_misc_array['showicon'])){ $template_misc_array['showicon']='lin'; }
if(!isset($template_misc_array['tfont1'])){ $template_misc_array['tfont1']=''; }
if(!isset($template_misc_array['tfont2'])){ $template_misc_array['tfont2']=''; }
//general-tab defaults (slider + read more)
if(!isset($template_misc_array['slidespeed'])){ $template_misc_array['slidespeed']='1'; }
if(!isset($template_misc_array['slideautodelay'])){ $template_misc_array['slideautodelay']='5'; }
if(!isset($template_misc_array['sliderhideprevnext'])){ $template_misc_array['sliderhideprevnext']=''; }
if(!isset($template_misc_array['sliderhidedots'])){ $template_misc_array['sliderhidedots']=''; }
if(!isset($template_misc_array['sliderautoplay'])){ $template_misc_array['sliderautoplay']=''; }
if(!isset($template_misc_array['sliderfixedheight'])){ $template_misc_array['sliderfixedheight']=''; }
if(!isset($template_misc_array['slidermobileview'])){ $template_misc_array['slidermobileview']=''; }
if(!isset($template_misc_array['review_same_height'])){ $template_misc_array['review_same_height']='no'; }
if(!isset($template_misc_array['read_more_num'])){ $template_misc_array['read_more_num']='30'; }
if(!isset($template_misc_array['read_more_color'])){ $template_misc_array['read_more_color']=''; }

// Build the source (pageid) list for the Choose Source filter.
$wpyelp_sources = $this->wpyelp_get_crawls();
$wpyelp_source_ids = array();
$wpyelp_source_urls = array();
if ( is_array( $wpyelp_sources ) ) {
	foreach ( $wpyelp_sources as $spageid => $ssource ) {
		if ( is_array( $ssource ) && $spageid !== '' && $spageid !== '0' ) {
			$wpyelp_source_ids[ $spageid ] = isset( $ssource['businessname'] ) && $ssource['businessname'] !== '' ? $ssource['businessname'] : $spageid;
			$wpyelp_source_urls[ $spageid ] = isset( $ssource['url'] ) ? $ssource['url'] : '';
		}
	}
}
$wpyelp_selected_source = isset( $currenttemplate->rpage ) ? $currenttemplate->rpage : '';
if ( $wpyelp_selected_source === '' && $currenttemplate->id === '' && ! empty( $wpyelp_source_ids ) ) {
	$wpyelp_source_keys     = array_keys( $wpyelp_source_ids );
	$wpyelp_selected_source = end( $wpyelp_source_keys );
}
$wpyelp_default_badge_url = 'https://www.yelp.com/';
if ( $wpyelp_selected_source !== '' && ! empty( $wpyelp_source_urls[ $wpyelp_selected_source ] ) ) {
	$wpyelp_default_badge_url = $wpyelp_source_urls[ $wpyelp_selected_source ];
} elseif ( ! empty( $wpyelp_source_urls ) ) {
	$wpyelp_default_badge_url = end( $wpyelp_source_urls );
}
$wpyelp_imgs_base = trailingslashit( wprev_yelp_plugin_url ) . 'public/partials/imgs/';
?>

<h2 class="nav-tab-wrapper">
	<span id="settingtab0" class="settingtab nav-tab cursorpointer gotopage0 nav-tab-active"><?php _e('Template Style', 'wp-yelp-review-slider'); ?></span>
	<span id="settingtab1" class="settingtab nav-tab cursorpointer gotopage1"><?php _e('General Settings', 'wp-yelp-review-slider'); ?></span>
	<span id="settingtab2" class="settingtab nav-tab cursorpointer gotopage2"><?php _e('Filter Settings', 'wp-yelp-review-slider'); ?></span>
	<span id="settingtab3" class="settingtab nav-tab cursorpointer gotopage3"><?php _e('Badge Settings', 'wp-yelp-review-slider'); ?></span>
</h2>

<table id="settingtable0" class="form-table settingstable ">
	<tr class="wpyelp_row">
		<td>
			<div class="w3_wprs-row">
				  <div class="w3_wprs-col s6">
					<div class="w3_wprs-col s6">
						<div class="wprevpre_temp_label_row"><?php _e('Style:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Show Stars:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Show Verified:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Show Date:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Last Name:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Display Avatar:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Show Icon:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Border Radius:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Background Color 1:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row wprevpre_bgcolor2"><?php _e('Background Color 2:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Text Color 1:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Text Color 2:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row wprevpre_tcolor3"><?php _e('Text Color 3:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Review Font Size:', 'wp-yelp-review-slider'); ?></div>
						<div class="wprevpre_temp_label_row"><?php _e('Name/Date Font Size:', 'wp-yelp-review-slider'); ?></div>
					</div>
					<div class="w3_wprs-col s6">
						<div class="wprevpre_temp_label_row">
							<select name="wpyelp_template_style" id="wpyelp_template_style">
							  <option value="1" <?php if($currenttemplate->style=='1' || $currenttemplate->style==""){echo "selected";} ?>>Style 1</option>
							  <option value="6" <?php if($currenttemplate->style=='6'){echo "selected";} ?>>Style 6</option>
							</select>
						</div>
						<div class="wprevpre_temp_label_row">
							<select name="wpyelp_template_misc_showstars" id="wpyelp_template_misc_showstars">
							  <option value="yes" <?php if($template_misc_array['showstars']=='yes'){echo "selected";} ?>>Yes</option>
							  <option value="no" <?php if($template_misc_array['showstars']=='no'){echo "selected";} ?>>No</option>
							</select>
						</div>
						<div class="wprevpre_temp_label_row">
							<select name="wpyelp_template_misc_verified" id="wpyelp_template_misc_verified">
							  <option value="no" <?php if($template_misc_array['verified']=='no' || $template_misc_array['verified']==''){echo "selected";} ?>><?php _e('No', 'wp-yelp-review-slider'); ?></option>
							  <option value="yes1" <?php if($template_misc_array['verified']=='yes1'){echo "selected";} ?>><?php _e('Yes', 'wp-yelp-review-slider'); ?></option>
							</select>
						</div>
						<div class="wprevpre_temp_label_row">
							<select name="wpyelp_template_misc_showdate" id="wpyelp_template_misc_showdate">
							  <option value="yes" <?php if($template_misc_array['showdate']=='yes'){echo "selected";} ?>>Yes</option>
							  <option value="no" <?php if($template_misc_array['showdate']=='no'){echo "selected";} ?>>No</option>
							</select>
						</div>
						<div class="wprevpre_temp_label_row">
							<select name="wpyelp_template_misc_lastname" id="wpyelp_template_misc_lastname">
							  <option value="show" <?php if($template_misc_array['lastnameformat']=='show'){echo "selected";} ?>><?php _e('Show', 'wp-yelp-review-slider'); ?></option>
							  <option value="hide" <?php if($template_misc_array['lastnameformat']=='hide'){echo "selected";} ?>><?php _e('Hide', 'wp-yelp-review-slider'); ?></option>
							  <option value="initial" <?php if($template_misc_array['lastnameformat']=='initial'){echo "selected";} ?>><?php _e('Initial', 'wp-yelp-review-slider'); ?></option>
							</select>
						</div>
						<div class="wprevpre_temp_label_row">
							<select name="wpyelp_template_misc_avataropt" id="wpyelp_template_misc_avataropt">
							  <option value="show" <?php if($template_misc_array['avataropt']=='show'){echo "selected";} ?>><?php _e('Yes', 'wp-yelp-review-slider'); ?></option>
							  <option value="hide" <?php if($template_misc_array['avataropt']=='hide'){echo "selected";} ?>><?php _e('No', 'wp-yelp-review-slider'); ?></option>
							  <option value="mystery" <?php if($template_misc_array['avataropt']=='mystery'){echo "selected";} ?>><?php _e('Mystery', 'wp-yelp-review-slider'); ?></option>
							  <option value="init" <?php if($template_misc_array['avataropt']=='init'){echo "selected";} ?>><?php _e('Initial', 'wp-yelp-review-slider'); ?></option>
							</select>
						</div>
						<div class="wprevpre_temp_label_row">
							<select name="wpyelp_template_misc_showicon" id="wpyelp_template_misc_showicon">
							  <option value="no" <?php if($template_misc_array['showicon']=='no'){echo "selected";} ?>><?php _e('No', 'wp-yelp-review-slider'); ?></option>
							  <option value="yes" <?php if($template_misc_array['showicon']=='yes'){echo "selected";} ?>><?php _e('Yes', 'wp-yelp-review-slider'); ?></option>
							  <option value="lin" <?php if($template_misc_array['showicon']=='lin'){echo "selected";} ?>><?php _e('Yes + Link', 'wp-yelp-review-slider'); ?></option>
							</select>
						</div>
						<div class="wprevpre_temp_label_row">
							<input id="wpyelp_template_misc_bradius" type="number" min="0" name="wpyelp_template_misc_bradius" placeholder="" value="<?php echo esc_attr( $template_misc_array['bradius'] ); ?>" style="width: 4em">
						</div>
						<div class="wprevpre_temp_label_row">
							<input type="text" data-alpha="true" value="<?php echo esc_attr( $template_misc_array['bgcolor1'] ); ?>" name="wpyelp_template_misc_bgcolor1" id="wpyelp_template_misc_bgcolor1" class="my-color-field" />
						</div>
						<div class="wprevpre_temp_label_row wprevpre_bgcolor2">
							<input type="text" data-alpha="true" value="<?php echo esc_attr( $template_misc_array['bgcolor2'] ); ?>" name="wpyelp_template_misc_bgcolor2" id="wpyelp_template_misc_bgcolor2" class="my-color-field" />
						</div>
						<div class="wprevpre_temp_label_row">
							<input type="text" value="<?php echo esc_attr( $template_misc_array['tcolor1'] ); ?>" name="wpyelp_template_misc_tcolor1" id="wpyelp_template_misc_tcolor1" class="my-color-field" />
						</div>
						<div class="wprevpre_temp_label_row">
							<input type="text" value="<?php echo esc_attr( $template_misc_array['tcolor2'] ); ?>" name="wpyelp_template_misc_tcolor2" id="wpyelp_template_misc_tcolor2" class="my-color-field" />
						</div>
						<div class="wprevpre_temp_label_row wprevpre_tcolor3">
							<input type="text" value="<?php echo esc_attr( $template_misc_array['tcolor3'] ); ?>" name="wpyelp_template_misc_tcolor3" id="wpyelp_template_misc_tcolor3" class="my-color-field" />
						</div>
						<div class="wprevpre_temp_label_row">
							<input type="number" value="<?php echo esc_attr( $template_misc_array['tfont1'] ); ?>" style="width: 4em;min-width: 4em;" min="0" name="wpyelp_template_misc_tfont1" id="wpyelp_template_misc_tfont1" />px
						</div>
						<div class="wprevpre_temp_label_row">
							<input type="number" value="<?php echo esc_attr( $template_misc_array['tfont2'] ); ?>" style="width: 4em;min-width: 4em;" min="0" name="wpyelp_template_misc_tfont2" id="wpyelp_template_misc_tfont2" />px
						</div>
						<a id="wpyelp_pre_resetbtn" class="button"><?php _e('Reset Colors', 'wp-yelp-review-slider'); ?></a>
					</div>
				  </div>
				  <div class="w3_wprs-col s6">
						<div class="wprevpre_temp_label_row"><strong><?php _e('Live Preview:', 'wp-yelp-review-slider'); ?></strong></div>
						<div id="wpyelp_template_preview"></div>
						<p class="description"><i><?php _e('This preview updates as you change the settings on the left. Date format is based on your WordPress > Settings value.', 'wp-yelp-review-slider'); ?></i></p>
						<div>
							<?php _e('Custom CSS:', 'wp-yelp-review-slider'); ?><br>
							<textarea name="wpyelp_template_css" id="wpyelp_template_css" cols="50" rows="4"><?php echo esc_textarea( $currenttemplate->template_css ); ?></textarea>
							<p class="description"><?php _e('Enter custom CSS code to control the look even more.', 'wp-yelp-review-slider'); ?></p>
						</div>
				  </div>
			</div>
			<p class="description">
			<?php _e('More styles available in <a href="https://wpreviewslider.com/" target="_blank">Pro Version</a> of plugin!', 'wp-yelp-review-slider'); ?></p>
		</td>
	</tr>
	<tr class="wpyelp_row">
		<th scope="row" colspan="1">
			<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-right-after gotopage1"><?php _e('Next', 'wp-yelp-review-slider'); ?></span>
		</th>
	</tr>
</table>

<table id="settingtable1" class="form-table settingstable " style="display:none;">
	<tr class="wpyelp_row">
		<th scope="row"><?php _e('Number of Reviews:', 'wp-yelp-review-slider'); ?><a class="wpyelp_helpicon_p wpyelp_btnicononlyhelp dashicons-before dashicons-editor-help"></a></th>
		<td><div class="divtemplatestyles">
			<label for="wpyelp_t_display_num"><?php _e('How many per a row?', 'wp-yelp-review-slider'); ?></label>
			<select name="wpyelp_t_display_num" id="wpyelp_t_display_num">
			  <option value="1" <?php if($currenttemplate->display_num==1){echo "selected";} ?>>1</option>
			  <option value="2" <?php if($currenttemplate->display_num==2){echo "selected";} ?>>2</option>
			  <option value="3" <?php if($currenttemplate->display_num==3 || $currenttemplate->display_num==""){echo "selected";} ?>>3</option>
			  <option value="4" <?php if($currenttemplate->display_num==4){echo "selected";} ?>>4</option>
			</select>
			<label for="wpyelp_t_display_num_rows"><?php _e('How many total rows?', 'wp-yelp-review-slider'); ?></label>
			<input id="wpyelp_t_display_num_rows" type="number" name="wpyelp_t_display_num_rows" placeholder="" value="<?php if($currenttemplate->display_num_rows>0){echo esc_attr( $currenttemplate->display_num_rows );} else {echo "1";}?>">
			</div>
			<p class="description"><?php _e('How many reviews to display on the page at a time. Widget style templates can only display 1 per row.', 'wp-yelp-review-slider'); ?></p>
		</td>
	</tr>
	<tr class="wpyelp_row">
		<th scope="row"><?php _e('Show Review Photos:', 'wp-yelp-review-slider'); ?><a class="wpyelp_helpicon_p wpyelp_btnicononlyhelp dashicons-before dashicons-editor-help"></a></th>
		<td>
			<select name="wpyelp_t_showmedia" id="wpyelp_t_showmedia">
				<option value="yes" <?php if($template_misc_array['showmedia']=='yes'){echo "selected";} ?>><?php _e('Yes', 'wp-yelp-review-slider'); ?></option>
				<option value="no" <?php if($template_misc_array['showmedia']=='no'){echo "selected";} ?>><?php _e('No', 'wp-yelp-review-slider'); ?></option>
			</select>
			<p class="description"><?php _e('Display any photos attached to a Yelp review. Click a photo to open it in a pop-up.', 'wp-yelp-review-slider'); ?></p>
		</td>
	</tr>
	<tr class="wpyelp_row">
		<th scope="row" style="min-width:220px"><?php _e('Slider or Grid:', 'wp-yelp-review-slider'); ?><a class="wpyelp_helpicon_p wpyelp_btnicononlyhelp dashicons-before dashicons-editor-help"></a></th>
		<td>
			<div class="divtemplatestyles">
				<label for="wpyelp_t_createslider"></label>
				<select name="wpyelp_t_createslider" id="wpyelp_t_createslider">
					<option value="no" <?php if($currenttemplate->createslider=="no"){echo "selected";} ?>><?php _e('Grid', 'wp-yelp-review-slider'); ?></option>
					<option value="yes" <?php if($currenttemplate->createslider=="yes"){echo "selected";} ?>><?php _e('Slider', 'wp-yelp-review-slider'); ?></option>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="wpyelp_t_numslides"><?php _e('Total slides:', 'wp-yelp-review-slider'); ?>&nbsp;</label>
				<select name="wpyelp_t_numslides" id="wpyelp_t_numslides">
					<?php for ( $si = 2; $si <= 10; $si++ ) : ?>
					<option value="<?php echo $si; ?>" <?php if($currenttemplate->numslides== (string) $si){echo "selected";} ?>><?php echo $si; ?></option>
					<?php endfor; ?>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="wpyelp_t_slidespeed"><?php _e('Slide speed:', 'wp-yelp-review-slider'); ?>&nbsp;</label>
				<input id="wpyelp_t_slidespeed" type="number" name="wpyelp_t_slidespeed" placeholder="" value="<?php echo esc_attr( $template_misc_array['slidespeed'] > 0 ? $template_misc_array['slidespeed'] : '1' ); ?>" style="width: 4em">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="wpyelp_t_slideautodelay"><?php _e('Auto-play delay:', 'wp-yelp-review-slider'); ?>&nbsp;</label>
				<input id="wpyelp_t_slideautodelay" type="number" name="wpyelp_t_slideautodelay" placeholder="" value="<?php echo esc_attr( $template_misc_array['slideautodelay'] > 0 ? $template_misc_array['slideautodelay'] : '5' ); ?>" style="width: 4em">
			</div>
			<div class="divtemplatestyles" style="margin-top:8px;">
				<label style="margin-right:12px;"><input type="checkbox" id="wpyelp_slidermobileview" name="wpyelp_slidermobileview" value="one" <?php if($template_misc_array['slidermobileview']=='one'){echo 'checked="checked"';}?>> <?php _e('One review on mobile', 'wp-yelp-review-slider'); ?></label>
				<label style="margin-right:12px;"><input type="checkbox" id="wpyelp_sliderhideprevnext" name="wpyelp_sliderhideprevnext" value="yes" <?php if($template_misc_array['sliderhideprevnext']== "yes"){echo 'checked="checked"';}?>> <?php _e('Hide Prev/Next', 'wp-yelp-review-slider'); ?></label>
				<label style="margin-right:12px;"><input type="checkbox" id="wpyelp_sliderhidedots" name="wpyelp_sliderhidedots" value="yes" <?php if($template_misc_array['sliderhidedots']== "yes"){echo 'checked="checked"';}?>> <?php _e('Hide Dots', 'wp-yelp-review-slider'); ?></label>
				<label style="margin-right:12px;"><input type="checkbox" id="wpyelp_sliderautoplay" name="wpyelp_sliderautoplay" value="yes" <?php if($template_misc_array['sliderautoplay']== "yes"){echo 'checked="checked"';}?>> <?php _e('Auto-play', 'wp-yelp-review-slider'); ?></label>
				<label style="margin-right:12px;"><input type="checkbox" id="wpyelp_sliderfixedheight" name="wpyelp_sliderfixedheight" value="yes" <?php if($template_misc_array['sliderfixedheight']== "yes"){echo 'checked="checked"';}?>> <?php _e('Static Height', 'wp-yelp-review-slider'); ?></label>
			</div>
			<p class="description"><?php _e('Allows you to create a slide show with your reviews.', 'wp-yelp-review-slider'); ?></p>
		</td>
	</tr>
	<?php
	if(!isset($currenttemplate->read_more)){
		$currenttemplate->read_more='';
		$currenttemplate->read_more_text='';
	}
	?>
	<tr class="wpyelp_row">
		<th scope="row"><?php _e('Add Read More Link:', 'wp-yelp-review-slider'); ?><a class="wpyelp_helpicon_p wpyelp_btnicononlyhelp dashicons-before dashicons-editor-help"></a></th>
		<td><div class="divtemplatestyles">
			<select name="wprevpro_t_read_more" id="wprevpro_t_read_more" class="mt2">
				<option value="no" <?php if($currenttemplate->read_more=='no' || $currenttemplate->read_more==''){echo "selected";} ?>>No</option>
				<option value="yes" <?php if($currenttemplate->read_more=='yes'){echo "selected";} ?>>Yes</option>
			</select>
			<label for="wprevpro_t_read_more_text">&nbsp;&nbsp;<?php _e('Read More Text:', 'wp-yelp-review-slider'); ?></label>
			<input id="wprevpro_t_read_more_text" type="text" name="wprevpro_t_read_more_text" placeholder="read more" value="<?php if($currenttemplate->read_more_text!=''){echo esc_attr( $currenttemplate->read_more_text );} else {echo "read more";}?>" style="width: 6em">
			<label for="wprevpro_t_read_more_num">&nbsp;&nbsp;<?php _e('Number of Words:', 'wp-yelp-review-slider'); ?>&nbsp;</label>
			<input id="wprevpro_t_read_more_num" type="number" name="wprevpro_t_read_more_num" placeholder="30" value="<?php echo esc_attr( $template_misc_array['read_more_num']!='' ? $template_misc_array['read_more_num'] : '30' ); ?>" style="width: 4em">
			<label for="wprevpro_t_read_more_color">&nbsp;&nbsp;<?php _e('Color:', 'wp-yelp-review-slider'); ?>&nbsp;</label>
			<input type="text" value="<?php echo esc_attr( $template_misc_array['read_more_color'] ); ?>" name="wprevpro_t_read_more_color" id="wprevpro_t_read_more_color" class="my-color-field" />
			</div>
			<p class="description"><?php _e('Allows you to cut off long reviews and add a read more link that will show the rest of the review when clicked.', 'wp-yelp-review-slider'); ?></p>
		</td>
	</tr>
	<tr class="wpyelp_row">
		<th scope="row"><?php _e('Reviews Same Height:', 'wp-yelp-review-slider'); ?><a class="wpyelp_helpicon_p wpyelp_btnicononlyhelp dashicons-before dashicons-editor-help"></a></th>
		<td>
			<select name="wpyelp_t_review_same_height" id="wpyelp_t_review_same_height">
				<option value="no" <?php if($template_misc_array['review_same_height']=="no" || $template_misc_array['review_same_height']==""){echo "selected";} ?>><?php _e('No', 'wp-yelp-review-slider'); ?></option>
				<option value="yes" <?php if($template_misc_array['review_same_height']=="yes"){echo "selected";} ?>><?php _e('Yes', 'wp-yelp-review-slider'); ?></option>
			</select>
			<p class="description"><?php _e('The individual review boxes will all be equal to the biggest one in all slides.', 'wp-yelp-review-slider'); ?></p>
		</td>
	</tr>
	<tr class="wpyelp_row">
		<th scope="row" colspan="2">
			<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-left gotopage0"><?php _e('Previous', 'wp-yelp-review-slider'); ?></span>
			<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-right-after gotopage2"><?php _e('Next', 'wp-yelp-review-slider'); ?></span>
		</th>
	</tr>
</table>

<table id="settingtable2" class="form-table settingstable " style="display:none;">
	<tr class="wpyelp_row">
		<th scope="row"><?php _e('Choose Source:', 'wp-yelp-review-slider'); ?><a class="wpyelp_helpicon_p wpyelp_btnicononlyhelp dashicons-before dashicons-editor-help"></a></th>
		<td>
			<select name="wpyelp_t_filtersource" id="wpyelp_t_filtersource">
			<?php if ( empty( $wpyelp_source_ids ) ) : ?>
				<option value=""><?php esc_html_e( 'No sources yet — add one on the Get Yelp Reviews page', 'wp-yelp-review-slider' ); ?></option>
			<?php else : ?>
				<?php foreach ( $wpyelp_source_ids as $spageid => $sname ) : ?>
					<option value="<?php echo esc_attr( $spageid ); ?>" data-fromurl="<?php echo esc_attr( isset( $wpyelp_source_urls[ $spageid ] ) ? $wpyelp_source_urls[ $spageid ] : '' ); ?>" <?php selected( $wpyelp_selected_source, $spageid ); ?>><?php echo esc_html( $sname ); ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
			</select>
			<p class="description"><?php _e('Which Yelp business page should this template show reviews from?', 'wp-yelp-review-slider'); ?></p>
		</td>
	</tr>
	<tr class="wpyelp_row">
		<th scope="row"><?php _e('Display Order:', 'wp-yelp-review-slider'); ?><a class="wpyelp_helpicon_p wpyelp_btnicononlyhelp dashicons-before dashicons-editor-help"></a></th>
		<td>
			<select name="wpyelp_t_display_order" id="wpyelp_t_display_order">
				<option value="random" <?php if($currenttemplate->display_order=="random"){echo "selected";} ?>><?php _e('Random', 'wp-yelp-review-slider'); ?></option>
				<option value="newest" <?php if($currenttemplate->display_order=="newest"){echo "selected";} ?>><?php _e('Newest', 'wp-yelp-review-slider'); ?></option>
			</select>
			<p class="description"><?php _e('The order in which the reviews are displayed.', 'wp-yelp-review-slider'); ?></p>
		</td>
	</tr>
	<tr class="wpyelp_row">
		<th scope="row" style="min-width:220px"><?php _e('Hide Reviews Without Text:', 'wp-yelp-review-slider'); ?><a class="wpyelp_helpicon_p wpyelp_btnicononlyhelp dashicons-before dashicons-editor-help"></a></th>
		<td>
			<select name="wpyelp_t_hidenotext" id="wpyelp_t_hidenotext">
				<option value="yes" <?php if($currenttemplate->hide_no_text=="yes"){echo "selected";} ?>><?php _e('Yes', 'wp-yelp-review-slider'); ?></option>
				<option value="no" <?php if($currenttemplate->hide_no_text=="no"){echo "selected";} ?>><?php _e('No', 'wp-yelp-review-slider'); ?></option>
			</select>
			<p class="description"><?php _e('Set to Yes and only display reviews that have text included.', 'wp-yelp-review-slider'); ?></p>
		</td>
	</tr>
	<?php
	if(!isset($currenttemplate->min_rating)){
		$currenttemplate->min_rating=1;
	}
	?>
	<tr class="wpyelp_row">
		<th scope="row"><?php _e('Filter By Rating:', 'wp-yelp-review-slider'); ?><a class="wpyelp_helpicon_p wpyelp_btnicononlyhelp dashicons-before dashicons-editor-help"></a></th>
		<td>
			<select name="wpyelp_t_min_rating" id="wpfbr_t_min_rating">
			  <option value="1" <?php if($currenttemplate->min_rating==1){echo "selected";} ?>><?php _e('Show All', 'wp-yelp-review-slider'); ?></option>
			  <option value="2" <?php if($currenttemplate->min_rating==2){echo "selected";} ?>><?php _e('2 & Higher', 'wp-yelp-review-slider'); ?></option>
			  <option value="3" <?php if($currenttemplate->min_rating==3){echo "selected";} ?>><?php _e('3 & Higher', 'wp-yelp-review-slider'); ?></option>
			  <option value="4" <?php if($currenttemplate->min_rating==4){echo "selected";} ?>><?php _e('4 & Higher', 'wp-yelp-review-slider'); ?></option>
			  <option value="5" <?php if($currenttemplate->min_rating==5){echo "selected";} ?>><?php _e('Only 5 Star', 'wp-yelp-review-slider'); ?></option>
			</select>
			<p class="description"><?php _e('Show only reviews with at least this value rating. Allows you to hide low reviews.', 'wp-yelp-review-slider'); ?></p>
		</td>
	</tr>
	<tr class="wpyelp_row">
		<th scope="row" colspan="2">
			<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-left gotopage1"><?php _e('Previous', 'wp-yelp-review-slider'); ?></span>
			<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-right-after gotopage3"><?php _e('Next', 'wp-yelp-review-slider'); ?></span>
		</th>
	</tr>
</table>

<table id="settingtable3" class="form-table settingstable " style="display:none;">
<?php
if(!isset($template_misc_array['blocation'])){ $template_misc_array['blocation']=""; }
if(!isset($template_misc_array['bname'])){ $template_misc_array['bname']=""; }
if(!isset($template_misc_array['bimgurl'])){ $template_misc_array['bimgurl']=$wpyelp_imgs_base.'yelp_badge_icon.svg'; }
if(!isset($template_misc_array['bbtncolor'])){ $template_misc_array['bbtncolor']="#d32323"; }
if(!isset($template_misc_array['bbtnurl'])){ $template_misc_array['bbtnurl']=$wpyelp_default_badge_url; }
if(!isset($template_misc_array['bnameurl'])){ $template_misc_array['bnameurl']=$wpyelp_default_badge_url; }
if(!isset($template_misc_array['bbkcolor'])){ $template_misc_array['bbkcolor']="#ffffff"; }
if(!isset($template_misc_array['bbradius'])){ $template_misc_array['bbradius']="0"; }
if(!isset($template_misc_array['bbwidth'])){ $template_misc_array['bbwidth']="0"; }
if(!isset($template_misc_array['bbcolor'])){ $template_misc_array['bbcolor']="#eeeeee"; }
if(!isset($template_misc_array['bshape'])){ $template_misc_array['bshape']=""; }
if(!isset($template_misc_array['bimgsize'])){ $template_misc_array['bimgsize']="50"; }
if(!isset($template_misc_array['bdropsh'])){ $template_misc_array['bdropsh']="yes"; }
if(!isset($template_misc_array['bcenter'])){ $template_misc_array['bcenter']=""; }
if(!isset($template_misc_array['bhname'])){ $template_misc_array['bhname']=""; }
if(!isset($template_misc_array['bhphoto'])){ $template_misc_array['bhphoto']=""; }
if(!isset($template_misc_array['bhbased'])){ $template_misc_array['bhbased']=""; }
if(!isset($template_misc_array['bhbtn'])){ $template_misc_array['bhbtn']=""; }
if(!isset($template_misc_array['bhpow'])){ $template_misc_array['bhpow']=""; }
if(!isset($template_misc_array['bhreviews'])){ $template_misc_array['bhreviews']=""; }
if(!isset($template_misc_array['bobasedon'])){ $template_misc_array['bobasedon']="Based on # reviews"; }
if(!isset($template_misc_array['borevus'])){ $template_misc_array['borevus']="Review us on Yelp!"; }
?>
	<tr class="wpyelp_row tabnoterow">
		<td colspan="2">
			<div class="tabnote">&nbsp;&nbsp;<?php _e('Use this page to place a badge next to your reviews. This is a brand new feature so let me know if you see any formatting issues.', 'wp-yelp-review-slider'); ?></div>
		</td>
	</tr>
	<tr class="wpyelp_row">
		<td colspan="2">
		<div class="badgeinfo">
			<div class="badgeinfosetting">
				<div class="bsetlabel"><?php _e('Location:', 'wp-yelp-review-slider'); ?></div>
				<select name="wpyelp_t_blocation" id="wpyelp_t_blocation">
					<option value="" <?php if($template_misc_array['blocation']==""){echo "selected";} ?>><?php _e('Select One', 'wp-yelp-review-slider'); ?></option>
					<option value="left" <?php if($template_misc_array['blocation']=="left"){echo "selected";} ?>><?php _e('Left', 'wp-yelp-review-slider'); ?></option>
					<option value="leftmid" <?php if($template_misc_array['blocation']=="leftmid"){echo "selected";} ?>><?php _e('Left Middle', 'wp-yelp-review-slider'); ?></option>
					<option value="above" <?php if($template_misc_array['blocation']=="above"){echo "selected";} ?>><?php _e('Above', 'wp-yelp-review-slider'); ?></option>
					<option value="abovewide" <?php if($template_misc_array['blocation']=="abovewide"){echo "selected";} ?>><?php _e('Above Wide', 'wp-yelp-review-slider'); ?></option>
					<option value="right" <?php if($template_misc_array['blocation']=="right"){echo "selected";} ?>><?php _e('Right', 'wp-yelp-review-slider'); ?></option>
					<option value="rightmid" <?php if($template_misc_array['blocation']=="rightmid"){echo "selected";} ?>><?php _e('Right Middle', 'wp-yelp-review-slider'); ?></option>
				</select>
			</div>
			<div class="badgeinfosetting badgehide">
				<div class="bsetlabel"><?php _e('Name:', 'wp-yelp-review-slider'); ?></div>
				<input id="wpyelp_t_bname" type="text" name="wpyelp_t_bname" value="<?php echo esc_attr( $template_misc_array['bname'] ); ?>" style="width: 15em">
			</div>
			<div class="badgeinfosetting badgehide">
				<div class="bsetlabel"><?php _e('Name Link URL:', 'wp-yelp-review-slider'); ?></div>
				<input id="wpyelp_t_bnameurl" type="text" name="wpyelp_t_bnameurl" value="<?php echo esc_attr( $template_misc_array['bnameurl'] ); ?>" style="width: 15em">
			</div>
		</div>
		</td>
	</tr>
	<tr class="wpyelp_row badgehide">
		<td colspan="2">
		<div class="badgeinfo">
			<div class="badgeinfosetting">
				<div class="bsetlabel"><?php _e('Business Image URL:', 'wp-yelp-review-slider'); ?></div>
				<input id="wpyelp_t_bimgurl" type="text" name="wpyelp_t_bimgurl" value="<?php echo esc_attr( $template_misc_array['bimgurl'] ); ?>" style="width: 15em"><a id="upload_licon_button" class="button"><?php _e('Upload', 'wp-yelp-review-slider'); ?></a>
			</div>
			<div class="badgeinfosetting">
				<div class="bsetlabel"><?php _e('Image Shape:', 'wp-yelp-review-slider'); ?></div>
				<select name="wpyelp_t_bshape" id="wpyelp_t_bshape">
					<option value="" <?php if($template_misc_array['bshape']==""){echo "selected";} ?>>&nbsp;<?php _e('Square', 'wp-yelp-review-slider'); ?>&nbsp;&nbsp;&nbsp;</option>
					<option value="round" <?php if($template_misc_array['bshape']=="round"){echo "selected";} ?>>&nbsp;<?php _e('Round', 'wp-yelp-review-slider'); ?>&nbsp;&nbsp;&nbsp;</option>
				</select>
			</div>
			<div class="badgeinfosetting">
				<div class="bsetlabel"><?php _e('Image Size:', 'wp-yelp-review-slider'); ?></div>
				<input id="wpyelp_t_bimgsize" type="number" name="wpyelp_t_bimgsize" value="<?php echo esc_attr( $template_misc_array['bimgsize'] ); ?>" style="width: 6em">
			</div>
			<div class="badgeinfosetting">
				<div class="bsetlabel"><?php _e('Button Color:', 'wp-yelp-review-slider'); ?></div>
				<input type="text" data-alpha="true" value="<?php echo esc_attr( $template_misc_array['bbtncolor'] ); ?>" name="wpyelp_t_bbtncolor" id="wpyelp_t_bbtncolor" class="my-color-field" />
			</div>
			<div class="badgeinfosetting">
				<div class="bsetlabel"><?php _e('Button Link URL:', 'wp-yelp-review-slider'); ?></div>
				<input id="wpyelp_t_bbtnurl" type="text" name="wpyelp_t_bbtnurl" value="<?php echo esc_attr( $template_misc_array['bbtnurl'] ); ?>" style="width: 15em">
			</div>
			<div class="badgeinfosetting">
				<div class="bsetlabel"><?php _e('Background:', 'wp-yelp-review-slider'); ?></div>
				<input type="text" data-alpha="true" value="<?php echo esc_attr( $template_misc_array['bbkcolor'] ); ?>" name="wpyelp_t_bbkcolor" id="wpyelp_t_bbkcolor" class="my-color-field" />
			</div>
			<div class="badgeinfosetting">
				<div class="bsetlabel"><?php _e('Border Radius:', 'wp-yelp-review-slider'); ?></div>
				<input id="wpyelp_t_bbradius" type="number" min="0" name="wpyelp_t_bbradius" value="<?php echo esc_attr( $template_misc_array['bbradius'] ); ?>" style="width: 7em">
			</div>
			<div class="badgeinfosetting">
				<div class="bsetlabel"><?php _e('Border Size:', 'wp-yelp-review-slider'); ?></div>
				<input id="wpyelp_t_bbwidth" type="number" min="0" name="wpyelp_t_bbwidth" value="<?php echo esc_attr( $template_misc_array['bbwidth'] ); ?>" style="width: 7em">
			</div>
			<div class="badgeinfosetting">
				<div class="bsetlabel"><?php _e('Border Color:', 'wp-yelp-review-slider'); ?></div>
				<input type="text" data-alpha="true" value="<?php echo esc_attr( $template_misc_array['bbcolor'] ); ?>" name="wpyelp_t_bbcolor" id="wpyelp_t_bbcolor" class="my-color-field" />
			</div>
		</div>
		</td>
	</tr>
	<tr class="wpyelp_row badgehide">
		<td colspan="2">
		<div class="badgeinfo">
			<div class="badgeinfosetting checkboxes">
				<input type="checkbox" id="wpyelp_t_bdropsh" name="wpyelp_t_bdropsh" value="yes" <?php if($template_misc_array['bdropsh']== "yes"){echo 'checked="checked"';}?>>
				<label for="wpyelp_t_bdropsh"><?php _e('Drop Shadow', 'wp-yelp-review-slider'); ?></label>
			</div>
			<div class="badgeinfosetting checkboxes">
				<input type="checkbox" id="wpyelp_t_bcenter" name="wpyelp_t_bcenter" value="yes" <?php if($template_misc_array['bcenter']== "yes"){echo 'checked="checked"';}?>>
				<label for="wpyelp_t_bcenter"><?php _e('Center Text', 'wp-yelp-review-slider'); ?></label>
			</div>
			<div class="badgeinfosetting checkboxes">
				<input type="checkbox" id="wpyelp_t_bhphoto" name="wpyelp_t_bhphoto" value="yes" <?php if($template_misc_array['bhphoto']== "yes"){echo 'checked="checked"';}?>>
				<label for="wpyelp_t_bhphoto"><?php _e('Hide Photo', 'wp-yelp-review-slider'); ?></label>
			</div>
			<div class="badgeinfosetting checkboxes">
				<input type="checkbox" id="wpyelp_t_bhname" name="wpyelp_t_bhname" value="yes" <?php if($template_misc_array['bhname']== "yes"){echo 'checked="checked"';}?>>
				<label for="wpyelp_t_bhname"><?php _e('Hide Name', 'wp-yelp-review-slider'); ?></label>
			</div>
			<div class="badgeinfosetting checkboxes">
				<input type="checkbox" id="wpyelp_t_bhbased" name="wpyelp_t_bhbased" value="yes" <?php if($template_misc_array['bhbased']== "yes"){echo 'checked="checked"';}?>>
				<label for="wpyelp_t_bhbased"><?php _e('Hide "Based On..."', 'wp-yelp-review-slider'); ?></label>
			</div>
			<div class="badgeinfosetting checkboxes">
				<input type="checkbox" id="wpyelp_t_bhpow" name="wpyelp_t_bhpow" value="yes" <?php if($template_misc_array['bhpow']== "yes"){echo 'checked="checked"';}?>>
				<label for="wpyelp_t_bhpow"><?php _e('Hide "powered By..."', 'wp-yelp-review-slider'); ?></label>
			</div>
			<div class="badgeinfosetting checkboxes">
				<input type="checkbox" id="wpyelp_t_bhbtn" name="wpyelp_t_bhbtn" value="yes" <?php if($template_misc_array['bhbtn']== "yes"){echo 'checked="checked"';}?>>
				<label for="wpyelp_t_bhbtn"><?php _e('Hide "Review Us..."', 'wp-yelp-review-slider'); ?></label>
			</div>
			<div class="badgeinfosetting checkboxes">
				<input type="checkbox" id="wpyelp_t_bhreviews" name="wpyelp_t_bhreviews" value="yes" <?php if($template_misc_array['bhreviews']== "yes"){echo 'checked="checked"';}?>>
				<label for="wpyelp_t_bhreviews"><?php _e('Hide Reviews', 'wp-yelp-review-slider'); ?></label>
			</div>
		</div>
		</td>
	</tr>
	<tr class="wpyelp_row">
		<td colspan="2">
		<div class="badgeinfo">
			<div class="badgeinfosetting badgehide">
				<div class="bsetlabel"><?php _e('Override "Based on..":', 'wp-yelp-review-slider'); ?></div>
				<input id="wpyelp_t_bobasedon" type="text" name="wpyelp_t_bobasedon" value="<?php echo esc_attr( $template_misc_array['bobasedon'] ); ?>" style="width: 15em">
			</div>
			<div class="badgeinfosetting badgehide">
				<div class="bsetlabel"><?php _e('Override "Review us..":', 'wp-yelp-review-slider'); ?></div>
				<input id="wpyelp_t_borevus" type="text" name="wpyelp_t_borevus" value="<?php echo esc_attr( $template_misc_array['borevus'] ); ?>" style="width: 15em">
			</div>
		</div>
		</td>
	</tr>
	<tr class="wpyelp_row">
		<th scope="row" colspan="2">
			<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-left gotopage2"><?php _e('Previous', 'wp-yelp-review-slider'); ?></span>
		</th>
	</tr>
</table>
	<?php 
	//security nonce
	wp_nonce_field( 'wpyelp_save_template');
	?>
	<input type="hidden" name="edittid" id="edittid"  value="<?php echo esc_attr( $currenttemplate->id ); ?>">
	<a id="wpyelp_addnewtemplate_cancel" class="button button-secondary"><?php _e('Cancel', 'wp-yelp-review-slider'); ?></a>
	<input type="submit" name="wpyelp_submittemplatebtn" id="wpyelp_submittemplatebtn" class="button button-primary" value="<?php _e('Save &amp; Close', 'wp-yelp-review-slider'); ?>">
	<a id="wpyelp_addnewtemplate_update" class="button button-primary"><?php _e('Update', 'wp-yelp-review-slider'); ?></a>
	<div id="update_form_msg_div">
		<span class="spinner wpyelp_form_spinner" id="savingformimg"></span>
		<span id="update_form_msg" style="display:none;"><span class="dashicons dashicons-saved"></span> <?php _e('Saved!', 'wp-yelp-review-slider'); ?></span>
	</div>
	</form>
</div>

<div class="wpyelp_margin10 w3-white" id="wpyelp_preview_outermost" style="display:none;">
	<div id="wpyelp_loading_prev_div">
		<span class="spinner wpyelp_preview_spinner" id="loadingpreview"></span>
	</div>
	<div class="wpyelp_margin10 w3-white" id="wpyelp_preview_outer"></div>
</div>
<div class=""><p>Do you like this plugin? If so please take a moment to leave me a review <a href="https://wordpress.org/plugins/wp-yelp-review-slider/" target="blank">here!</a> If it's missing something then please contact me <a href="https://wpreviewslider.com/contact/" target="blank">here</a>. Thanks!</p></div>

</div></div></div>
	
<div id="popup_review_list" class="popup-wrapper wpyelp_hide">
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
</div>

<div id="mythickboxid" style="display:none;">
     <p>
         <img src="<?php echo plugin_dir_url( __FILE__ ); ?>pro_settings.png">
     </p>
</div>