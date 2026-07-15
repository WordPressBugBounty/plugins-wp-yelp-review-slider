(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
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
	 
		$( ".wprs_rd_more" ).click(function() {
			$(this ).hide();
			$(this ).next("span").show();
			
			//change height of wprev-slider-widget
			$(this ).closest( ".wprev-slider-widget" ).css( "height", "auto" );
			
			//change height of wprev-slider
			$(this ).closest( ".wprev-slider" ).css( "height", "auto" );
			
			
		});
		
		//check to see if we need to create slider;
		$( ".wprev-slider" ).each(function( index ) {
			createaslider(this,'shortcode');
		});
		$( ".wprev-slider-widget" ).each(function( index ) {
			createaslider(this,'widget');
		});
		function createaslider(thissliderdiv,type){

			var $slider = $( thissliderdiv );
			//slider options passed via data-attributes from the template settings
			var sliderhideprevnext = $slider.attr( "data-sliderhideprevnext" );
			var sliderhidedots = $slider.attr( "data-sliderhidedots" );
			var sliderautoplay = $slider.attr( "data-sliderautoplay" );
			var slidespeed = $slider.attr( "data-slidespeed" );
			var slideautodelay = $slider.attr( "data-slideautodelay" );
			var sliderfixedheight = $slider.attr( "data-sliderfixedheight" );
			var revsameheight = $slider.attr( "data-revsameheight" );

			var showarrows = true;
			if(type=='widget'){
				showarrows = false;
			}
			if(sliderhideprevnext=="yes"){
				showarrows = false;
			}
			var shownav = true;
			if(sliderhidedots=="yes"){
				shownav = false;
			}
			var sautoplay = false;
			if(sliderautoplay=="yes"){
				sautoplay = true;
			}
			var sspeed = parseFloat(slidespeed) * 1000;
			if(isNaN(sspeed) || sspeed<=0){ sspeed = 750; }
			var sdelay = parseFloat(slideautodelay) * 1000;
			if(isNaN(sdelay) || sdelay<=0){ sdelay = 5000; }
			if(sdelay<sspeed){ sdelay = sspeed; }
			var sanimate = true;
			if(sliderfixedheight=="yes"){
				sanimate = false;
			}

			//unhide other rows.
			$slider.find('li').show();
			var slider = $slider.wprs_unslider(
					{
					autoplay:sautoplay,
					infinite:false,
					delay: sdelay,
					speed: sspeed,
					animation: 'horizontal',
					arrows: showarrows,
					nav: shownav,
					animateHeight: sanimate,
					activeClass: 'wprs_unslider-active',
					}
				);

			if(sanimate==true){
				setTimeout(function(){
					//height of active slide
					var firstheight = $slider.find('.wprs_unslider-active').height();
					$slider.css( 'height', firstheight );
				}, 500);
			}

			if(sautoplay==true){
				slider.on('mouseover', function() {slider.data('wprs_unslider').stop();}).on('mouseout', function() {slider.data('wprs_unslider').start();});
			}

			//force equal review-box heights when "Reviews Same Height" is on
			if(revsameheight=='yes'){
				var maxheights = $slider.find(".indrevdiv").map(function (){return $(this).outerHeight();}).get();
				var maxHeightofslide = Math.max.apply(null, maxheights);
				if(maxHeightofslide>0){ $slider.find(".indrevdiv").css( "min-height", maxHeightofslide ); }
			}

		};

		//simple tooltip for the "Verified on..." badge (and other .wprevtooltip elements)
		var wprevTooltipRoots = ".wpyelp_t1_outer_div, .wprevpro_t6_outer_div, .wpyelp_t1_outer_div_widget, .wprevpro_t6_outer_div_widget";
		$( wprevTooltipRoots ).on('mouseenter touchstart', '.wprevtooltip', function(e) {
			var titleText = $(this).attr('data-wprevtooltip');
			$(this).data('tiptext', titleText).removeAttr('data-wprevtooltip');
			$('<p class="wprevpro_tooltip"></p>').text(titleText).appendTo('body').css('top', (e.pageY - 15) + 'px').css('left', (e.pageX + 10) + 'px').fadeIn('slow');
		});
		$( wprevTooltipRoots ).on('mouseleave touchend', '.wprevtooltip', function(e) {
			$(this).attr('data-wprevtooltip', $(this).data('tiptext'));
			$('.wprevpro_tooltip').remove();
		});
		$( wprevTooltipRoots ).on('mousemove', '.wprevtooltip', function(e) {
			$('.wprevpro_tooltip').css('top', (e.pageY - 15) + 'px').css('left', (e.pageX + 10) + 'px');
		});

		// Lazy-load Lity only when review media thumbnails are on the page.
		// Use a manual open so we don't also fire Lity's built-in [data-lity] handler (double lightbox).
		function wprevBindMediaLightbox($root) {
			var $scope = $root && $root.length ? $root : $(document);
			$scope.off('click.wprevlity', 'a.wprev_media_img_a').on('click.wprevlity', 'a.wprev_media_img_a', function(e) {
				e.preventDefault();
				e.stopImmediatePropagation();
				var href = $(this).attr('href');
				if (!href || typeof lity !== 'function') {
					return;
				}
				lity(href);
			});
		}

		function wprevEnsureLity(callback) {
			if (typeof lity === 'function') {
				callback();
				return;
			}
			var base = (typeof wprevpublicjs_script_vars !== 'undefined' && wprevpublicjs_script_vars.wprevplugin_url)
				? wprevpublicjs_script_vars.wprevplugin_url
				: '';
			if (!base) {
				return;
			}
			if (!document.getElementById('wprev_lity_css')) {
				var head = document.getElementsByTagName('head')[0];
				var link = document.createElement('link');
				link.id = 'wprev_lity_css';
				link.rel = 'stylesheet';
				link.type = 'text/css';
				link.href = base + '/public/css/lity.min.css';
				link.media = 'all';
				head.appendChild(link);
			}
			$.getScript(base + '/public/js/lity.min.js', callback);
		}

		if ($('.wprev_media_div a.wprev_media_img_a').length > 0) {
			wprevEnsureLity(function() {
				wprevBindMediaLightbox($(document));
			});
		}

	});

})( jQuery );
