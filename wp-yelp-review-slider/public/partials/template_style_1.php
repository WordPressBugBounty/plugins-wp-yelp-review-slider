<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Yelp_Review
 * @subpackage WP_Yelp_Review/public/partials
 */
 //html code for the template style
$plugin_dir = WP_PLUGIN_DIR;
$imgs_url = esc_url( plugins_url( 'imgs/', __FILE__ ) );

require_once 'template_class.php';
$templateclass = new WP_Yelp_Template_Functions();
if ( ! isset( $template_misc_array ) || ! is_array( $template_misc_array ) ) {
	$template_misc_array = array();
}

//loop if more than one row
for ($x = 0; $x < count($rowarray); $x++) {
	if(	$currentform[0]->template_type=="widget"){
		?>
		<div class="wpyelp_t1_outer_div_widget w3_wprs-row-padding-small">
		<?php
		} else {
		?>
		<div class="wpyelp_t1_outer_div w3_wprs-row-padding">
		<?php
	}
	//loop 
	foreach ( $rowarray[$x] as $review ) 
	{
		//reviewer name (honors "Last Name" setting)
		$reviewername = $templateclass->wprevpro_get_reviewername( $review, $template_misc_array );

		//avatar (honors "Display Avatar" setting)
		$avataropt = isset($template_misc_array['avataropt']) ? $template_misc_array['avataropt'] : 'show';
		if($review->type=="Facebook"){
			$userpic = 'https://graph.facebook.com/'.$review->reviewer_id.'/picture?width=60&height=60';
		} else {
			$userpic = $review->userpic;
		}
		if($avataropt=='init'){
			$userpic = $templateclass->wprev_get_initials_avatar_url( $reviewername, 100 );
		} elseif($avataropt=='mystery'){
			$userpic = $imgs_url.'fb_profile.jpg';
		}
		$avatarhidestyle = '';
		if($avataropt=='hide' || $userpic===''){
			$avatarhidestyle = ' style="display:none;"';
		}

		//verified badge (honors "Show Verified" setting)
		$verifiedhtml = '';
		if(isset($template_misc_array['verified']) && $template_misc_array['verified']=='yes1'){
			$verifiedhtml = '<span class="verifiedloc1 wprevpro_verified_svg wprevtooltip" data-wprevtooltip="Verified on '.esc_attr($review->type).'"><span class="svgicons svg-wprsp-verified"></span></span>';
		}

		//site icon/logo (honors "Show Icon" setting: no / yes / lin)
		$showicon = isset($template_misc_array['showicon']) ? $template_misc_array['showicon'] : 'lin';
		if($review->type=="Yelp"){
			$options = get_option('wpyelp_yelp_settings');
			$burl = isset($options['yelp_business_url']) ? $options['yelp_business_url'] : '';
			if(isset($review->from_url) && $review->from_url!=''){
				$burl = $review->from_url;
			}
			if($burl==""){
				$burl="https://www.yelp.com";
			}
			$starfile = "yelp_stars_".$review->rating.".png";
			$logoimg = '<img src="'.$imgs_url.'yelp_outline.png" alt="" class="wpyelp_t1_yelp_logo">';
			if($showicon=='no'){
				$logo = '';
			} elseif($showicon=='yes'){
				$logo = $logoimg;
			} else {
				$logo = '<a href="'.esc_url($burl).'" target="_blank" rel="nofollow">'.$logoimg.'</a>';
			}
		} else if($review->type=="Facebook" && isset($currentform[0]->facebook_icon) && $currentform[0]->facebook_icon=="yes"){
			$starfile = "stars_".$review->rating."_yellow.png";
			$burl = "https://www.facebook.com/pg/".$review->pageid."/reviews/";
			$logo = ($showicon=='no') ? '' : '<a href="'.esc_url($burl).'" target="_blank" rel="nofollow"><img src="'.$imgs_url.'fb_logo.png" alt="" class="wpyelp_t1_yelp_logo"></a>';
		}  else  {
			$starfile = "stars_".$review->rating."_yellow.png";
			$logo ="";
		}
		
		$reviewtext = "";
		if($review->review_text !=""){
			$reviewtext = html_entity_decode($review->review_text);

			$reviewtext = nl2br($reviewtext);
		}
		//if read more is turned on then divide then add read more span links
		if($currentform[0]->read_more_text==''){
			$currentform[0]->read_more_text = 'read more';
		}
		if(	$currentform[0]->read_more=="yes"){
			$readmorenum = ( isset($template_misc_array['read_more_num']) && intval($template_misc_array['read_more_num'])>0 ) ? intval($template_misc_array['read_more_num']) : 30;
			$countwords = str_word_count($reviewtext);
			
			if($countwords>$readmorenum){
				//split in to array
				$pieces = explode(" ", $reviewtext);
				//slice the array in to two
				$part1 = array_slice($pieces, 0, $readmorenum);
				$part2 = array_slice($pieces, $readmorenum);
				$reviewtext = implode(" ",$part1)."<a class='wprs_rd_more'>... ".$currentform[0]->read_more_text."</a><span class='wprs_rd_more_text' style='display:none;'> ".implode(" ",$part2)."</span>";
			}
		}

		//per a row
		if($currentform[0]->display_num>0){
			$perrow = 12/$currentform[0]->display_num;
		} else {
			$perrow = 4;
		}

		$media = $templateclass->wprevpro_get_media( $review, $template_misc_array );
	?>
		<div class="wpyelp_t1_DIV_1<?php if(	$currentform[0]->template_type=="widget"){echo ' marginb10';}?> w3_wprs-col l<?php echo $perrow; ?>">
			<div class="wpyelp_t1_DIV_2 wprev_preview_bg1_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?> wprev_preview_bradius_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?>">
				<p class="wpyelp_t1_P_3 wprev_preview_tcolor1_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?>">
					<span class="wpyelp_star_imgs_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?>"><img src="<?php echo $imgs_url."".$starfile; ?>" alt="" class="wpyelp_t1_star_img_file">&nbsp;&nbsp;</span><?php echo $verifiedhtml; ?><?php echo stripslashes($reviewtext); ?>
				</p>
				<?php echo $media; ?>
				<?php echo $logo; ?>
			</div><span class="wpyelp_t1_A_8"<?php echo $avatarhidestyle; ?>><img src="<?php echo $templateclass->wprev_esc_avatar_src( $userpic ); ?>" alt="thumb" class="wpyelp_t1_IMG_4" loading="lazy"/></span> <span class="wpyelp_t1_SPAN_5 wprev_preview_tcolor2_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?>"><?php echo esc_html( wp_unslash( $reviewername ) ); ?><br/><span class="wprev_showdate_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?>"><?php echo date("n/d/Y",$review->created_time_stamp); ?></span> </span>
		</div>
	<?php
	}
	//end loop
	?>
	</div>
<?php
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
