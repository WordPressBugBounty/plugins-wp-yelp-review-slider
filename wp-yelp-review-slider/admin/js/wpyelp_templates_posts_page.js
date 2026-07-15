(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 * $( document ).ready(function() same as
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 
	 //document ready
	$(function(){
		var prestyle = "";
		var isResettingColors = false;
		//color picker
		var myOptions = {
			// a callback to fire whenever the color changes to a valid color
			change: function(event, ui){
				var color = ui.color.toString();
				var element = event.target;
				var curid = $(element).attr('id');
				$( element ).val(color)
				if(isResettingColors){
					return;
				}
				//manuall change after css. hack since jquery can't access before and after elements    border-top: 30px solid #943939;
				if(curid=='wpyelp_template_misc_bgcolor1'){
					prestyle = "<style>.wpyelp_t1_DIV_2::after{ border-top: 30px solid "+color+"; }</style>";
				}
				changepreviewhtml();
			},
			// a callback to fire when the input is emptied or an invalid color
			clear: function() {}
		};
		 
		$('.my-color-field').wpColorPicker(myOptions);

		//for style preview changes.-------------
		var starhtml = '<span class="wpyelp_star_imgs"><img src="'+adminjs_script_vars.pluginsUrl + '/public/partials/imgs/yelp_stars_5.png" alt="" style="width: 100px;" >&nbsp;&nbsp;</span>';
		var sampltext = 'This is a sample review. Hands down the best experience we have had in the southeast! Awesome accommodations, great staff. We will gladly drive four hours for this gem!';
		var datehtml = '<span id="wprev_showdate">1/12/2017</span>';
		var lastnamehtml = '<span id="wprev_lastname">Wilson</span>';
		var verified1 = '<span class="verifiedloc1 wprevpro_verified_svg wprevtooltip" data-wprevtooltip="Verified on Yelp"><span class="svgicons svg-wprsp-verified"></span></span>';

		var imagehref = adminjs_script_vars.pluginsUrl + '/admin/partials/sample_avatar.jpg';
		var imagehrefmystery = adminjs_script_vars.pluginsUrl + '/public/partials/imgs/fb_profile.jpg';
		var avatarimg = imagehref;

		var displayname = 'Josh '+lastnamehtml;

		var style1html ='<div class="wpyelp_t1_outer_div w3_wprs-row-padding">	\
							<div class="wpyelp_t1_DIV_1 w3_wprs-col">	\
								<div class="wpyelp_t1_DIV_2 wprev_preview_bg1 wprev_preview_bradius">	\
									<p class="wpyelp_t1_P_3 wprev_preview_tcolor1" style="margin-bottom: 10px;">	\
										'+starhtml+''+verified1+''+sampltext+'		</p>	\
									<a href="" target="_blank" rel="nofollow">	\
									<img id="wprev_showicon" src="'+adminjs_script_vars.pluginsUrl + '/public/partials/imgs/yelp_outline.png" alt="" class="wpyelp_t1_yelp_logo"></a>	\
								</div><span class="wpyelp_t1_A_8"><img src="'+avatarimg+'" alt="thumb" class="wpyelp_t1_IMG_4 wprev_avatar_opt"></span> <span class="wpyelp_t1_SPAN_5 wprev_preview_tcolor2">'+displayname+'<br>'+datehtml+' </span>	\
							</div>	\
							</div>';

		var starhtmlt6 = '<span class="wpyelp_star_imgs"><img src="'+adminjs_script_vars.pluginsUrl + '/public/partials/imgs/yelp_stars_5.png" alt="" style="width: 100px;">'+verified1+'</span>';
		var style6html = '<div class="wprevpro_t6_outer_div w3_wprs-row wprevprodiv">	\
							<div class="wprevpro_t6_DIV_1 w3_wprs-col outerrevdiv">	\
								<div class="wpproslider_t6_DIV_1a">	\
									<div class="indrevdiv wpproslider_t6_DIV_2 wprev_preview_bg1 wprev_preview_bradius">	\
										<div class="wpproslider_t6_DIV_2_top">	\
											<div class="wpproslider_t6_DIV_3L"><img src="'+avatarimg+'" alt="thumb" class="wpproslider_t6_IMG_2 wprev_avatar_opt"></div>	\
											<div class="wpproslider_t6_DIV_3">	\
												<div class="t6displayname wpproslider_t6_STRONG_5 wprev_preview_tcolor2">'+displayname+'</div>	\
												<div class="wpproslider_t6_star_DIV">'+starhtmlt6+'</div>	\
												<div class="wpproslider_t6_SPAN_6 wprev_preview_tcolor2"><span id="wprev_showdate">1/12/2017</span></div>	\
											</div>	\
										</div>	\
										<div class="wpproslider_t6_DIV_4">	\
											<div class="indrevtxt wpproslider_t6_P_4 wprev_preview_tcolor1">'+sampltext+'</div>	\
										</div>	\
										<div class="wpproslider_t6_DIV_3_logo"><a href="" target="_blank" rel="nofollow"><img id="wprev_showicon" src="'+adminjs_script_vars.pluginsUrl + '/public/partials/imgs/yelp_outline.png" alt="" class="wprevpro_t6_site_logo"></a></div>	\
									</div>	\
								</div>	\
							</div>	\
							</div>';

		changepreviewhtml();

		function buildInitialsAvatarDataUri(name, size){
			size = size || 100;
			name = (name || 'U').toString().trim();
			var words = name.split(/\s+/).filter(Boolean);
			var initials;
			if(words.length >= 2){
				initials = (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
			} else if(name){
				initials = name.charAt(0).toUpperCase();
			} else {
				initials = 'U';
			}
			var hash = 0;
			for(var i = 0; i < name.length; i++){
				hash = ((hash << 5) - hash) + name.charCodeAt(i);
				hash |= 0;
			}
			var r = (hash >> 16) & 255;
			var g = (hash >> 8) & 255;
			var b = hash & 255;
			if(((r * 299) + (g * 587) + (b * 114)) / 1000 > 200){
				r = Math.max(0, r - 50);
				g = Math.max(0, g - 50);
				b = Math.max(0, b - 50);
			}
			var bg = '#' + [r, g, b].map(function(v){
				var h = v.toString(16);
				return h.length === 1 ? '0' + h : h;
			}).join('');
			var fontSize = Math.round(size * 0.4);
			var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'+size+'" height="'+size+'" viewBox="0 0 '+size+' '+size+'">' +
				'<rect width="100%" height="100%" fill="'+bg+'"/>' +
				'<text x="50%" y="50%" dy=".1em" fill="#ffffff" font-family="Arial,Helvetica,sans-serif" font-size="'+fontSize+'" font-weight="bold" text-anchor="middle" dominant-baseline="middle">'+initials+'</text>' +
				'</svg>';
			return 'data:image/svg+xml;base64,' + btoa(svg);
		}

		//simple tooltip for the "Verified on..." badge in the live preview + AJAX preview
		var wpyelpTooltipRoots = "#wpyelp_template_preview, #wpyelp_preview_outer";
		$( wpyelpTooltipRoots ).on('mouseenter touchstart', '.wprevtooltip', function(e) {
			var titleText = $(this).attr('data-wprevtooltip');
			$(this).data('tiptext', titleText).removeAttr('data-wprevtooltip');
			$('<p class="wprevpro_tooltip"></p>').text(titleText).appendTo('body').css('top', (e.pageY - 15) + 'px').css('left', (e.pageX + 10) + 'px').fadeIn('slow');
		});
		$( wpyelpTooltipRoots ).on('mouseleave touchend', '.wprevtooltip', function(e) {
			$(this).attr('data-wprevtooltip', $(this).data('tiptext'));
			$('.wprevpro_tooltip').remove();
		});
		$( wpyelpTooltipRoots ).on('mousemove', '.wprevtooltip', function(e) {
			$('.wprevpro_tooltip').css('top', (e.pageY - 15) + 'px').css('left', (e.pageX + 10) + 'px');
		});
		
		//reset colors to default
		$( "#wpyelp_pre_resetbtn" ).click(function() {
			resetcolors();
		});
		function resetcolors(){
				isResettingColors = true;
				var templatenum = $( "#wpyelp_template_style" ).val();
				//reset colors to default (Yelp free version only ships Style 1 and Style 6)
				if(templatenum=='1'){
					
					$( "#wpyelp_template_misc_bradius" ).val('0');
					$( "#wpyelp_template_misc_bgcolor1" ).val('#ffffff');
					$( "#wpyelp_template_misc_bgcolor2" ).val('#ffffff');
					$( "#wpyelp_template_misc_tcolor1" ).val('#777777');
					$( "#wpyelp_template_misc_tcolor2" ).val('#555555');
					prestyle="";
					//reset color picker
					$('#wpyelp_template_misc_bgcolor1').iris('color', '#ffffff');
					$('#wpyelp_template_misc_bgcolor2').iris('color', '#ffffff');
					$( "#wpyelp_template_misc_tcolor1" ).iris('color','#777777');
					$( "#wpyelp_template_misc_tcolor2" ).iris('color','#555555');
					
				} else if(templatenum=='6'){
					$( "#wpyelp_template_misc_bradius" ).val('4');
					$( "#wpyelp_template_misc_bgcolor1" ).val('#fdfdfd');
					$( "#wpyelp_template_misc_bgcolor2" ).val('#ffffff');
					$( "#wpyelp_template_misc_tcolor1" ).val('#555555');
					$( "#wpyelp_template_misc_tcolor2" ).val('#555555');
					prestyle="";
					//reset color picker
					$('#wpyelp_template_misc_bgcolor1').iris('color', '#fdfdfd');
					$('#wpyelp_template_misc_bgcolor2').iris('color', '#ffffff');
					$( "#wpyelp_template_misc_tcolor1" ).iris('color','#555555');
					$( "#wpyelp_template_misc_tcolor2" ).iris('color','#555555');
				}
				isResettingColors = false;
				changepreviewhtml();
		}

		
		//on template num change
		$( "#wpyelp_template_style" ).change(function() {
				//reset colors if not editing, otherwise leave alone
				if($( "#edittid" ).val()==""){
				resetcolors();
				}
				changepreviewhtml();
		});
		
		$( "#wpyelp_template_misc_showstars" ).change(function() {
				changepreviewhtml();
		});
		$( "#wpyelp_template_misc_showdate" ).change(function() {
				changepreviewhtml();
		});
		$( "#wpyelp_template_misc_bradius" ).change(function() {
				changepreviewhtml();
		});
		$( "#wpyelp_template_misc_bgcolor1" ).change(function() {
				changepreviewhtml();
		});
		$( "#wpyelp_template_misc_tcolor1" ).change(function() {
				changepreviewhtml();
		});
		$( "#wpyelp_template_misc_tcolor2" ).change(function() {
				changepreviewhtml();
		});
		$( "#wpyelp_template_misc_showicon" ).change(function() {
				changepreviewhtml();
		});
		$( "#wpyelp_template_misc_tfont1" ).on('change keyup', function() {
				changepreviewhtml();
		});
		$( "#wpyelp_template_misc_tfont2" ).on('change keyup', function() {
				changepreviewhtml();
		});
		$( "#wpyelp_template_misc_avataropt" ).change(function() {
				changepreviewhtml();
		});
		$( "#wpyelp_template_misc_verified" ).change(function() {
				changepreviewhtml();
		});
		$( "#wpyelp_template_misc_lastname" ).change(function() {
				changepreviewhtml();
		});
		//custom css change preview
		var lastValue = '';
		$("#wpyelp_template_css").on('change keyup paste mouseup', function() {
			if ($(this).val() != lastValue) {
				lastValue = $(this).val();
				changepreviewhtml();
			}
		});
		
		function changepreviewhtml(){
			var templatenum = $( "#wpyelp_template_style" ).val();
			var bradius = $( "#wpyelp_template_misc_bradius" ).val();
			var bg1 = $( "#wpyelp_template_misc_bgcolor1" ).val();
			var bg2 = $( "#wpyelp_template_misc_bgcolor2" ).val();
			var tcolor1 = $( "#wpyelp_template_misc_tcolor1" ).val();
			var tcolor2 = $( "#wpyelp_template_misc_tcolor2" ).val();
			var tcolor3 = $( "#wpyelp_template_misc_tcolor3" ).val();
			var tfont1 = $( "#wpyelp_template_misc_tfont1" ).val();
			var tfont2 = $( "#wpyelp_template_misc_tfont2" ).val();
			var avataropt = $( "#wpyelp_template_misc_avataropt" ).val();
			var verified = $( "#wpyelp_template_misc_verified" ).val();
			var lastname = $( "#wpyelp_template_misc_lastname" ).val();

			if(templatenum=='1'){
				prestyle = "<style>.wpyelp_t1_DIV_2::after{ border-top: 30px solid "+bg1+"; }</style>";
			} else {
				prestyle = "";
			}
			if($( "#wpyelp_template_css" ).val()!=""){
				prestyle += '<style>'+$( "#wpyelp_template_css" ).val()+'</style>';
			}

				//Yelp free version only ships Style 1 and Style 6
				if(templatenum=='1'){
					$( "#wpyelp_template_preview" ).html(prestyle+style1html);
					//hide background 2 select
					$( ".wprevpre_bgcolor2" ).hide();
					$( ".wprevpre_tcolor3" ).hide();
				} else if(templatenum=='6'){
					$( "#wpyelp_template_preview" ).html(prestyle+style6html);
					$( ".wprevpre_bgcolor2" ).hide();
					$( ".wprevpre_tcolor3" ).hide();
				}
			//now hide and show things based on values in select boxes
			if($( "#wpyelp_template_misc_showstars" ).val()=="no"){
				$( ".wpyelp_star_imgs" ).hide();
			} else {
				$( ".wpyelp_star_imgs" ).show();
			}
			if($( "#wpyelp_template_misc_showdate" ).val()=="no"){
				$( "#wprev_showdate" ).hide();
			} else {
				$( "#wprev_showdate" ).show();
			}
			if($( "#wpyelp_template_misc_showicon" ).val()=="no"){
				$( "#wprev_showicon" ).hide();
			} else {
				$( "#wprev_showicon" ).show();
			}
			//set colors and bradius by changing css via jQuery     border-radius: 10px 10px 10px 10px;
			$( '.wprev_preview_bradius' ).css( "border-radius", bradius+'px' );
			$( '.wprev_preview_bg1' ).css( "background", bg1 );
			$( '.wprev_preview_bg2' ).css( "background", bg2 );
			$( '.wprev_preview_tcolor1' ).css( "color", tcolor1 );
			$( '.wprev_preview_tcolor2' ).css( "color", tcolor2 );
			if(tfont1 > 0){
				$( '.wprev_preview_tcolor1' ).css( {"font-size": tfont1+"px", "line-height": "normal"} );
			} else {
				$( '.wprev_preview_tcolor1' ).css( {"font-size": "", "line-height": ""} );
			}
			if(tfont2 > 0){
				$( '.wprev_preview_tcolor2' ).css( {"font-size": tfont2+"px", "line-height": "normal"} );
			} else {
				$( '.wprev_preview_tcolor2' ).css( {"font-size": "", "line-height": ""} );
			}

			//avatar option: show real photo / hide / mystery silhouette / initials
			if(avataropt=='hide'){
				$( ".wprev_avatar_opt" ).hide();
				if(templatenum=='6'){
					$( ".wpproslider_t6_DIV_3L" ).hide();
				}
			} else if(avataropt=='mystery'){
				$(".wprev_avatar_opt").attr("src",imagehrefmystery);
				$( ".wprev_avatar_opt" ).show();
				if(templatenum=='6'){
					$( ".wpproslider_t6_DIV_3L" ).show();
				}
			} else if(avataropt=='init'){
				// Local SVG initials avatar (matches TripAdvisor/Google behavior).
				$(".wprev_avatar_opt").attr("src", buildInitialsAvatarDataUri('Josh Wilson'));
				$( ".wprev_avatar_opt" ).show();
				if(templatenum=='6'){
					$( ".wpproslider_t6_DIV_3L" ).show();
				}
			} else {
				$(".wprev_avatar_opt").attr("src",imagehref);
				$( ".wprev_avatar_opt" ).show();
				if(templatenum=='6'){
					$( ".wpproslider_t6_DIV_3L" ).show();
				}
			}

			//verified badge toggle
			if(verified=='yes1'){
				$( ".verifiedloc1" ).show();
			} else {
				$( ".verifiedloc1" ).hide();
			}

			//last name format
			if(lastname=="hide"){
				$( "#wprev_lastname" ).hide();
				$(".t6displayname").html('Josh');
			} else if(lastname=="initial"){
				$("#wprev_lastname").html("W.").show();
				$(".t6displayname").html('Josh <span id="wprev_lastname">W.</span>');
			} else {
				$("#wprev_lastname").html("Wilson").show();
				$(".t6displayname").html('Josh '+lastnamehtml);
			}

		}
		
		
		
		//help button clicked
		$( "#wpyelp_helpicon_posts" ).click(function() {
		  openpopup("Tips", '<p>This page will let you create multiple Reviews Templates that you can then add to your Posts or Pages via a shortcode or template function.</p>', "");
		});
		//for showing description after clicking help icon next to a setting label
		$( ".wpyelp_helpicon_p" ).click(function() {
			$(this).closest('tr').find('p.description').each(function() {
				$( this ).toggle('fast');
			});
		});
		//display shortcode button click wpyelp_addnewtemplate
		$( ".wpyelp_displayshortcode" ).click(function() {
			//get id and template type
			var tid = $( this ).parent().attr( "templateid" );
			var ttype = $( this ).parent().attr( "templatetype" );
			
		  if(ttype=="widget"){
			openpopup("Widget Instructions", '<p>To display this in your Sidebar or other Widget areas, add the WP Reviews widget under Appearance > Widgets, and then select this template in the drop down.</p>', '');
		  } else {
			openpopup("How to Display", '<p>Enter this shortcode on a post, page, or text widget: </br></br>[wpyelp_usetemplate tid="'+tid+'"]</p><p>Or you can add the following php code to your template: </br></br><code> do_action( \'wprev_yelp_plugin_action\', '+tid+' ); </code></p>', '');
		  }
		  
		});
		
		//when checking yelp type, uncheck all other types, not allowed to be displayed along side them
		/*
		$('#wpyelp_t_rtype_yelp').change(function() {
			if($(this).is(":checked")) {
				$('#wpyelp_t_rtype_fb').attr('checked', false); // Unchecks it
				$('#wpyelp_t_rtype_manual').attr('checked', false); // Unchecks it
			}
		});
		$('#wpyelp_t_rtype_fb').change(function() {
			if($(this).is(":checked")) {
				$('#wpyelp_t_rtype_yelp').attr('checked', false); // Unchecks it
			}
		});
		$('#wpyelp_t_rtype_manual').change(function() {
			if($(this).is(":checked")) {
				$('#wpyelp_t_rtype_yelp').attr('checked', false); // Unchecks it
			}
		});
		*/
		//hide show fb stuff only when checked.
		$('#wpyelp_t_rtype_fb').change(function() {
			if($('#wpyelp_t_rtype_fb').is(":checked")) {
				$('.fbhide').show('slow');
			} else {
				$('.fbhide').hide('slow');
			}
		});
		$('#wpyelp_t_rtype_yelp').change(function() {
			if($('#wpyelp_t_rtype_fb').is(":checked")) {
				$('.fbhide').show('slow');
			} else {
				$('.fbhide').hide('slow');
			}
		});
		$('#wpyelp_t_rtype_manual').change(function() {
			if($('#wpyelp_t_rtype_fb').is(":checked")) {
				$('.fbhide').show('slow');
			} else {
				$('.fbhide').hide('slow');
			}
		});
		
		
		//launch pop-up windows code--------
		function openpopup(title, body, body2){

			//set text
			jQuery( "#popup_titletext").html(title);
			jQuery( "#popup_bobytext1").html(body);
			jQuery( "#popup_bobytext2").html(body2);
			
			var popup = jQuery('#popup_review_list').popup({
				width: 400,
				offsetX: -100,
				offsetY: 0,
			});
			
			popup.open();
			//set height
			var bodyheight = Number(jQuery( ".popup-content").height()) + 10;
			jQuery( "#popup_review_list").height(bodyheight);

		}
		//--------------------------------
		//get the url parameter-----------
		function getParameterByName(name, url) {
			if (!url) {
			  url = window.location.href;
			}
			name = name.replace(/[\[\]]/g, "\\$&");
			var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
				results = regex.exec(url);
			if (!results) return null;
			if (!results[2]) return '';
			return decodeURIComponent(results[2].replace(/\+/g, " "));
		}
		//---------------------------------
		
		//hide or show new template form ----------
		var checkedittemplate = getParameterByName('taction'); // "lorem"
		if(checkedittemplate=="edit"){
			jQuery("#wpyelp_new_template").show("slow");
			checkwidgetradio();
			//auto-load the server-side preview for the template being edited
			showtemplatepreview();
		} else {
			jQuery("#wpyelp_new_template").hide();
		}
		
		$( "#wpyelp_addnewtemplate" ).click(function() {
		  jQuery("#wpyelp_new_template").show("slow");
		});	
		$( "#wpyelp_addnewtemplate_cancel" ).click(function() {
		  jQuery("#wpyelp_new_template").hide("slow");
		  //reload page without taction and tid
		  setTimeout(function(){ 
			window.location.href = "?page=wp_yelp-templates_posts"; 
		  }, 500);
		  
		});	
		
		//-------------------------------
		//------- Server-side preview + save (matches TripAdvisor/Google) -------

		// Build a working slider on preview markup, mirroring the public front end.
		function createaslider(thissliderdiv, type){
			var $slider = $( thissliderdiv );
			if(typeof $slider.wprs_unslider !== 'function'){
				return;
			}
			//slider options passed via data-attributes from the rendered template
			var sliderhideprevnext = $slider.attr( "data-sliderhideprevnext" );
			var sliderhidedots = $slider.attr( "data-sliderhidedots" );
			var sliderautoplay = $slider.attr( "data-sliderautoplay" );
			var slidespeed = $slider.attr( "data-slidespeed" );
			var slideautodelay = $slider.attr( "data-slideautodelay" );
			var sliderfixedheight = $slider.attr( "data-sliderfixedheight" );
			var revsameheight = $slider.attr( "data-revsameheight" );

			var showarrows = true;
			if(type=='widget'){ showarrows = false; }
			if(sliderhideprevnext=="yes"){ showarrows = false; }
			var shownav = true;
			if(sliderhidedots=="yes"){ shownav = false; }
			var sautoplay = false;
			if(sliderautoplay=="yes"){ sautoplay = true; }
			var sspeed = parseFloat(slidespeed) * 1000;
			if(isNaN(sspeed) || sspeed<=0){ sspeed = 750; }
			var sdelay = parseFloat(slideautodelay) * 1000;
			if(isNaN(sdelay) || sdelay<=0){ sdelay = 5000; }
			if(sdelay<sspeed){ sdelay = sspeed; }
			var sanimate = true;
			if(sliderfixedheight=="yes"){ sanimate = false; }

			//unhide other rows.
			$slider.find('li').show();
			var slider = $slider.wprs_unslider({
				autoplay:sautoplay,
				infinite:false,
				delay: sdelay,
				speed: sspeed,
				animation: 'horizontal',
				arrows: showarrows,
				nav: shownav,
				animateHeight: sanimate,
				activeClass: 'wprs_unslider-active'
			});
			if(sanimate==true){
				setTimeout(function(){
					var firstheight = $slider.find('.wprs_unslider-active').height();
					$slider.css( 'height', firstheight );
				}, 500);
			}
			if(sautoplay==true){
				slider.on('mouseover', function() {slider.data('wprs_unslider').stop();}).on('mouseout', function() {slider.data('wprs_unslider').start();});
			}
			if(revsameheight=='yes'){
				var maxheights = $slider.find(".indrevdiv").map(function (){return $(this).outerHeight();}).get();
				var maxHeightofslide = Math.max.apply(null, maxheights);
				if(maxHeightofslide>0){ $slider.find(".indrevdiv").css( "min-height", maxHeightofslide ); }
			}
		}

		// Init any sliders present in the freshly injected preview markup.
		function initpreviewsliders(){
			$( "#wpyelp_preview_outer .wprev-slider" ).each(function(){
				createaslider(this, 'shortcode');
			});
			$( "#wpyelp_preview_outer .wprev-slider-widget" ).each(function(){
				createaslider(this, 'widget');
			});
			missingimgcheck();
			initPreviewLightbox();
		}

		// Hide review media thumbnails that fail to load (scope to preview only).
		function missingimgcheck(){
			$('#wpyelp_preview_outer img.wprev_media_img').each(function () {
				var img = this;
				var $img = $(this);
				function markMissing() {
					$img.addClass('wprev_missing_image');
				}
				if (img.complete) {
					if (img.naturalWidth === 0) {
						markMissing();
					}
					return;
				}
				$img.one('error', markMissing);
			});
		}

		// Bind the Lity lightbox to review media thumbnails in the AJAX-rendered preview.
		// (The front-end binding in wprev-public.js delegates from document, but its
		// initial "does media exist yet" check runs before this preview HTML is injected,
		// so the admin preview needs its own binding run after each render.)
		function initPreviewLightbox(){
			var $preview = $('#wpyelp_preview_outer');
			if (!$preview.find('.wprev_media_div a.wprev_media_img_a').length) {
				return;
			}

			var pluginsUrl = '';
			if (typeof wprevpublicjs_script_vars !== 'undefined' && wprevpublicjs_script_vars.wprevplugin_url) {
				pluginsUrl = wprevpublicjs_script_vars.wprevplugin_url;
			} else if (typeof adminjs_script_vars !== 'undefined' && adminjs_script_vars.pluginsUrl) {
				pluginsUrl = adminjs_script_vars.pluginsUrl;
			}
			if (!pluginsUrl) {
				return;
			}

			function bindMediaLightbox() {
				$preview.off('click.wprevlity', 'a.wprev_media_img_a').on('click.wprevlity', 'a.wprev_media_img_a', function(e) {
					e.preventDefault();
					e.stopImmediatePropagation();
					var href = $(this).attr('href');
					if (!href || typeof lity !== 'function') {
						return;
					}
					lity(href);
				});
			}

			function ensureLity(callback) {
				if (typeof lity === 'function') {
					callback();
					return;
				}
				if (!document.getElementById('wprev_lity_css')) {
					$('<link/>', {
						id: 'wprev_lity_css',
						rel: 'stylesheet',
						type: 'text/css',
						href: pluginsUrl + '/public/css/lity.min.css'
					}).appendTo('head');
				}
				$.getScript(pluginsUrl + '/public/js/lity.min.js', callback);
			}

			ensureLity(bindMediaLightbox);
		}

		// Fetch a fresh preview for the currently saved template id.
		function showtemplatepreview(){
			var tid = $( "#edittid" ).val();
			if(!tid || tid=='' || tid=='0'){
				return; //nothing saved yet; nothing to preview
			}
			$( "#wpyelp_preview_outermost" ).show();
			$( "#loadingpreview" ).addClass('is-active');
			var senddata = {
				action: 'wpyelp_get_preview',
				wpyelp_nonce: adminjs_script_vars.wpyelp_nonce,
				tid: tid
			};
			jQuery.post(ajaxurl, senddata, function(response){
				$( "#loadingpreview" ).removeClass('is-active');
				if(response){
					try {
						var result = JSON.parse(response);
						$( "#wpyelp_preview_outer" ).html(result.templatehtml);
						initpreviewsliders();
					} catch(e){
						alert('Error loading preview. Contact support. ' + e);
					}
				}
			});
		}

		// "Update" button: save via ajax, then render the returned preview (matches TripAdvisor's single Update button).
		$( "#wpyelp_addnewtemplate_update" ).click(function(e){
			e.preventDefault();
			$( "#wpyelp_preview_outermost" ).show();
			$( "#savingformimg" ).addClass('is-active');
			$( "#update_form_msg" ).hide();

			var formArray = $( "#newtemplateform" ).serializeArray();
			var returnArray = {};
			for (var i = 0; i < formArray.length; i++){
				returnArray[formArray[i]['name']] = formArray[i]['value'];
			}
			var jsonfields = JSON.stringify(returnArray);
			var senddata = {
				action: 'wpyelp_save_template',
				wpyelp_nonce: adminjs_script_vars.wpyelp_nonce,
				data: jsonfields
			};
			jQuery.post(ajaxurl, senddata, function(response){
				$( "#savingformimg" ).removeClass('is-active');
				if(response){
					try {
						var saveresult = JSON.parse(response);
						if(saveresult.ack=='success'){
							$( "#update_form_msg" ).show();
							//store new id when this was an insert so further saves update it
							if(saveresult.iu=='insert'){
								$( "#edittid" ).val(saveresult.t_id);
							}
							$( "#wpyelp_preview_outer" ).html(saveresult.templatehtml);
							initpreviewsliders();
						} else {
							alert('Error saving/updating template. Please contact support. ' + saveresult.ackmessage);
						}
					} catch(e){
						alert('Error saving/updating template. Contact support. ' + e);
					}
				} else {
					alert('Error saving/updating template. Please contact support.');
				}
				setTimeout(function(){ $( "#update_form_msg" ).hide(); }, 2500);
			});
		});

		// Read-more toggle inside the server-rendered preview.
		$( "#wpyelp_preview_outer" ).on( "click", ".wprs_rd_more", function(){
			$(this).hide();
			$(this).next("span").show(0, function(){ $(this).css('opacity','1.0'); });
			$(this).closest( ".wprev-slider-widget" ).css( "height", "auto" );
			$(this).closest( ".wprev-slider" ).css( "height", "auto" );
		});
		//-------------------------------
		
		//form validation
		$("#newtemplateform").submit(function(){   
			if(jQuery( "#wpyelp_template_title").val()==""){
				alert("Please enter a title.");
				$( "#wpyelp_template_title" ).focus();
				return false;
			} else if(jQuery( "#wpyelp_t_display_num_total").val()<1){
				alert("Please enter a 1 or greater.");
				$( "#wpyelp_t_display_num_total" ).focus();
				return false;
			} else {
			return true;
			}

		});
		
		//widget radio clicked
		$('input[type=radio][name=wpyelp_template_type]').change(function() {
			checkwidgetradio();
		});
		
		//check widget radio----------------------
		function checkwidgetradio() {
			var widgetvalue = $("input[name=wpyelp_template_type]:checked").val();
			if (widgetvalue == 'widget') {
				//change how many per a row to 1
				$('#wpyelp_t_display_num').val("1");
				$('#wpyelp_t_display_num').hide();
				$('#wpyelp_t_display_num').prev().hide();
				//force hide arrows and do not allow horizontal scroll on slideshow
				//$('input:radio[name=wpyelp_sliderdirection]').val(['vertical']);
				//$('input[id=wpyelp_sliderdirection1-radio]').attr("disabled",true);
				$('input:radio[name=wpyelp_sliderarrows]').val(['no']);
				$('input[id=wpyelp_sliderarrows1-radio]').attr("disabled",true);
			}
			else if (widgetvalue == 'post') {
				//alert("post type");
				if($('#edittid').val()==""){
				$('#wpyelp_t_display_num').val("3");
				}
				$('#wpyelp_t_display_num').show();
				$('#wpyelp_t_display_num').prev().show();
				$('input[id=wpyelp_sliderdirection1-radio]').attr("disabled",false);
				$('input[id=wpyelp_sliderarrows1-radio]').attr("disabled",false);
			}
		}
		
		//wpyelp_btn_pickreviews open thickbox----------------
		$( "#wpyelp_btn_pickreviews" ).click(function() {
		  sendtoajax('','','',"");
			var url = "#TB_inline?width=600&height=600&inlineId=tb_content";
			tb_show("Select Reviews to Display", url);
			$( "#wpyelp_filter_table_name" ).focus();
			$( "#TB_window" ).css({ "width":"830px","margin-left": "-415px" });
			$( "#TB_ajaxContent" ).css({ "width":"800px" });
		});
		
		//for search box------------------------------
		$('#wpyelp_filter_table_name').on('input', function() {
			// do something
			var myValue = $("#wpyelp_filter_table_name").val();
			var myLength = myValue.length;
			if(myLength>1 || myLength==0){
			//search here
				sendtoajax('','','',"");
			}
		});
		
		//for search select box------------------------------
		$( "#wpyelp_filter_table_min_rating" ).change(function() {
				sendtoajax('','','',"");
		});
		
		//for pagination bar-----------------------------------
		$("#wpyelp_list_pagination_bar").on("click", "span", function (event) {
			var pageclicked = $(this).text();
			sendtoajax(pageclicked,'','',"");
		});
		
		//for sorting table--------------wpyelp_sortname, wpyelp_sorttext, wpyelp_sortdate
		$( ".wpyelp_tablesort" ).click(function() {
			//remove all green classes
			$(this).parent().find('i').removeClass("text_green");

			//add back on this one
			$(this).children( "i" ).addClass("text_green");
			
			var sortdir = $(this).attr("sortdir");
			var sorttype = $(this).attr("sorttype");
			if(sortdir=="DESC"){
				$(this).attr("sortdir","ASC");
			} else {
				$(this).attr("sortdir","DESC");
			}
			if(sorttype=="name"){
				sorttype="reviewer_name";
			} else if(sorttype=="rating") {
				sorttype="rating";
			} else if(sorttype=="stext") {
				sorttype="review_length";
			} else if(sorttype=="stime") {
				sorttype="created_time_stamp";
			}
		  sendtoajax('1',sorttype,sortdir,"");
		});
		
		//=====for only displaying the ones selected so far========
		$('#wpyelp_selectedrevsdiv').click(function() {
			//find the currently selected
			var currentlyselected = $('#wpyelp_t_showreviewsbyid').val();
			if(currentlyselected==""){
				var temparray =  Array();
			} else {
				var temparray = currentlyselected.split("-");
			}
			//convert to object
			var temparrayobj = temparray.reduce(function(acc, cur, i) {acc[i] = cur;return acc;}, {});
			sendtoajax('1','','',temparrayobj);
			var url = "#TB_inline?width=600&height=600&inlineId=tb_content";
			tb_show("Currenlty Selected", url);
			$( "#wpyelp_filter_table_name" ).focus();
			$( "#TB_window" ).css({ "width":"830px","margin-left": "-415px" });
			$( "#TB_ajaxContent" ).css({ "width":"800px" });
		});
		
		//============for clearing all currently selected============
		$('#wpyelp_clearselectedrevsbtn').click(function() {
			$('#wpyelp_t_showreviewsbyid').val("");
			$('#wpyelp_selectedrevsdiv').hide();
			$('#wpyelp_t_showreviewsbyid').hide();
		});
		//======send to ajax to retrieve reviews==========
		function sendtoajax(pageclicked,sortbyval,sortd,selrevs){
			var filterbytext = $("#wpyelp_filter_table_name").val();
			var filterbyrating = $("#wpyelp_filter_table_min_rating").val();
			//clear list and pagination bar
			$( "#review_list_select" ).html("");
			$( "#wpyelp_list_pagination_bar" ).html("");
			var senddata = {
					action: 'wpyelp_find_reviews',	//required
					wpyelp_nonce: adminjs_script_vars.wpyelp_nonce,
					sortby: sortbyval,
					sortdir: sortd,
					filtertext: filterbytext,
					filterrating: filterbyrating,
					pnum:pageclicked,
					curselrevs:selrevs
					};

				jQuery.post(ajaxurl, senddata, function (response){
					//console.log(response);
					var object = JSON.parse(response);
				//console.log(object);

				var htmltext;
				var userpic;
				var reviewtext;

				
					$.each(object, function(index) {
						if(object[index]){
						if(object[index].reviewer_name){
							//check to see if this one should be checked
							//get currently selected
							var currentlyselected = $('#wpyelp_t_showreviewsbyid').val();
							if(currentlyselected==""){
								var temparray =  Array();
							} else {
								var temparray = currentlyselected.split("-");
							}
							//see if id is in array
							var prevselected="";
							if(jQuery.inArray( object[index].id, temparray )>-1){
								prevselected = 'checked="checked"';
							}
							
							//userpic
							userpic="";
							if(object[index].type=="Facebook"){
								userpic = '<img style="-webkit-user-select: none;width: 50px;" src="https://graph.facebook.com/'+object[index].reviewer_id+'/picture?type=square">';
							} else {
								userpic = '<img style="-webkit-user-select: none;width: 50px;" src="'+object[index].userpic+'">';
							}
							//stripslashes
							reviewtext = String(object[index].review_text);
							reviewtext = reviewtext.replace(/\\'/g,'\'').replace(/\"/g,'"').replace(/\\\\/g,'\\').replace(/\\0/g,'\0');
						
							htmltext = htmltext + '<tr id="wprev_id_'+object[index].id+'">	\
								<th scope="col" class="manage-column"><input type="checkbox" name="wpyelp_selected_revs[]" value="'+object[index].id+'" '+prevselected+'></th>	\
								<th scope="col">'+userpic+'</th>	\
								<th scope="col" class="manage-column">'+object[index].reviewer_name+'</th>	\
								<th scope="col" class="manage-column"><b>'+object[index].rating+'</b></th>	\
								<th scope="col" class="manage-column">'+reviewtext+'</th>	\
								<th scope="col" class="manage-column">'+object[index].created_time+'</th>	\
							</tr>';
							reviewtext ='';
						}
						}
					});
					
					$( "#review_list_select" ).html(htmltext);
					
					//pagination bar
					var numpages = Number(object['totalpages']);
					var reviewtotalcount = Number(object['reviewtotalcount']);
					if(numpages>1){
						var pagebarhtml="";
						var blue_grey;
						var i;
						var numpages = Number(object['totalpages']);
						var curpage = Number(object['pagenum']);
						for (i = 1; i <= numpages; i++) {
							if(i==curpage){blue_grey = " blue_grey";} else {blue_grey ="";}
							pagebarhtml = pagebarhtml + '<span class="button'+blue_grey+'">'+i+'</span>';
						}
					}
						$( "#wpyelp_list_pagination_bar" ).html(pagebarhtml);
					//hide sort arrows and search bar if totalcount is zero
					if(reviewtotalcount==0){
						//$("#wpyelp_searchbar").hide();
						$(".dashicons-sort").hide();
						$("#wpyelp_list_pagination_bar").hide();
					} else {
						//$("#wpyelp_searchbar").show();
						$(".dashicons-sort").show();
						$("#wpyelp_list_pagination_bar").show();
					}
					if(numpages==0){
						$("#wpyelp_searchbar").hide();
						//$(".dashicons-sort").hide();
						//$("#wpyelp_list_pagination_bar").hide();
					} else {
						$("#wpyelp_searchbar").show();
						//$(".dashicons-sort").show();
						//$("#wpyelp_list_pagination_bar").show();
					}
					
				});
		}
	
		
		//========when selecting a review add it to top so we can easily select or unselect it.==========
		$("#review_list_select").on("click", "input", function (event) {
			var revid = $(this).val();
			
			//get currently selected
			var currentlyselected = $('#wpyelp_t_showreviewsbyid').val();
			if(currentlyselected==""){
				var temparray =  Array();
			} else {
				var temparray = currentlyselected.split("-");
			}
			
			//check to see if unchecking or checking
			if($(this).is(':checked')){
				//add revid to hidden input field
				temparray.push(revid);
			} else {
				//remove from array
				temparray = jQuery.grep(temparray, function(value) {
				  return value != revid;
				});
			}

			//html number currently selected
			if (temparray[0] != null && temparray[0]!="") {
				if(temparray.length==1){
					$('#wpyelp_selectedrevsdiv').html('<b>'+temparray.length + '</b> Review Selected (<span class="dashicons dashicons-search" style="font-size: 16px;vertical-align: middle;"></span>Show)');
				} else if(temparray.length>1){
					$('#wpyelp_selectedrevsdiv').html('<b>'+temparray.length + '</b> Reviews Selected (<span class="dashicons dashicons-search" style="font-size: 16px;vertical-align: middle;"></span>Show)');
				} else {
					$('#wpyelp_selectedrevsdiv').html('');
				}
			} else {
				$('#wpyelp_selectedrevsdiv').html('');
			}
			
			//convert array back to string and input it to field
			var stringtemparray = temparray.join('-');
			$('#wpyelp_t_showreviewsbyid').val(stringtemparray);
		});
		
		//------------when clicking row in review table, check or uncheck the check box-----------------------------------
		/*
		$("#review_list_select").on("click", "tr", function (event) {
			var rcheckbox = $(this).find("input[type='checkbox']");
			rcheckbox.trigger('click');
		});
		*/

		//------------Template settings tabs (Style / General / Filter / Badge)------------
		var currenttab = 0;
		$( ".gotopage0" ).click(function() {
			$( "#settingtable0" ).fadeIn();
			$( "#settingtable1" ).hide();
			$( "#settingtable2" ).hide();
			$( "#settingtable3" ).hide();
			currenttab = 0;
			changecurrenttab(currenttab);
		});
		$( ".gotopage1" ).click(function() {
			$( "#settingtable0" ).hide();
			$( "#settingtable1" ).fadeIn();
			$( "#settingtable2" ).hide();
			$( "#settingtable3" ).hide();
			currenttab = 1;
			changecurrenttab(currenttab);
		});
		$( ".gotopage2" ).click(function() {
			$( "#settingtable0" ).hide();
			$( "#settingtable1" ).hide();
			$( "#settingtable2" ).fadeIn();
			$( "#settingtable3" ).hide();
			currenttab = 2;
			changecurrenttab(currenttab);
		});
		$( ".gotopage3" ).click(function() {
			$( "#settingtable0" ).hide();
			$( "#settingtable1" ).hide();
			$( "#settingtable2" ).hide();
			$( "#settingtable3" ).fadeIn();
			currenttab = 3;
			changecurrenttab(currenttab);
		});
		function changecurrenttab(ctab){
			$( ".settingtab" ).removeClass( "nav-tab-active" );
			if(ctab==0){ $( "#settingtab0" ).addClass("nav-tab-active"); }
			if(ctab==1){ $( "#settingtab1" ).addClass("nav-tab-active"); }
			if(ctab==2){ $( "#settingtab2" ).addClass("nav-tab-active"); }
			if(ctab==3){ $( "#settingtab3" ).addClass("nav-tab-active"); }
		}

		//------------Badge settings: show/hide options when Location is set------------
		hideshowbadgeoptions();
		$( "#wpyelp_t_blocation" ).change(function() {
			hideshowbadgeoptions();
		});
		function hideshowbadgeoptions(){
			if($( "#wpyelp_t_blocation" ).val()==""){
				$( ".badgehide" ).hide('slow');
			} else {
				$( ".badgehide" ).show('slow');
			}
		}

		//------------Badge: fill name/URLs from Choose Source dropdown------------
		function setbadgetitle(){
			var $opt = $( "#wpyelp_t_filtersource option:selected" );
			if(!$opt.length || !$opt.val()){ return; }
			var sname = $opt.text();
			var surl = $opt.attr('data-fromurl') || '';
			if(sname && $( "#wpyelp_t_bname" ).val()==""){
				$( "#wpyelp_t_bname" ).val(sname);
			}
			if(surl){
				if($( "#wpyelp_t_bnameurl" ).val()=="" || $( "#wpyelp_t_bnameurl" ).val().indexOf("yelp.com")>-1){
					$( "#wpyelp_t_bnameurl" ).val(surl);
				}
				if($( "#wpyelp_t_bbtnurl" ).val()=="" || $( "#wpyelp_t_bbtnurl" ).val().indexOf("yelp.com")>-1){
					$( "#wpyelp_t_bbtnurl" ).val(surl);
				}
			}
		}
		$( "#wpyelp_t_filtersource" ).change(function() {
			setbadgetitle();
		});
		//prefill the badge name/urls from the selected source on page load too (new templates)
		setbadgetitle();

		//------------Badge business image thickbox uploader------------
		$('#upload_licon_button').on("click",function() {
			tb_show('Upload Icon', 'media-upload.php?referer=wp_yelp-templates_posts&type=image&TB_iframe=true&post_id=0', false);
			window.restore_send_to_editor = window.send_to_editor;
			window.send_to_editor = function(html) {
				var image_url = jQuery("<div>" + html + "</div>").find('img').attr('src');
				$('#wpyelp_t_bimgurl').val(image_url);
				tb_remove();
				window.send_to_editor = window.restore_send_to_editor;
			};
			return false;
		});
		
	});

})( jQuery );