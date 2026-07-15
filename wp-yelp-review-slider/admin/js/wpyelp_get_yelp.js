(function( $ ) {
	'use strict';

	$(function(){

		function showMsg( $el, html, isError ) {
			$el.css( 'color', isError ? '#a00' : '#0073aa' ).html( html );
		}

		// Add a new Yelp source.
		$( '#wpyelp_add_source' ).on( 'click', function() {
			var $btn = $( this );
			var url = $.trim( $( '#yelp_new_url' ).val() );
			var name = $.trim( $( '#yelp_new_name' ).val() );

			if ( url === '' ) {
				showMsg( $( '#wpyelp_add_msg' ), 'Please enter a Yelp business URL.', true );
				return;
			}

			$btn.prop( 'disabled', true );
			$( '#wpyelp_add_loader' ).css( 'display', 'inline-block' );
			showMsg( $( '#wpyelp_add_msg' ), '', false );

			$.post( ajaxurl, {
				action: 'wpyelp_add_source',
				yelp_url: url,
				businessname: name,
				wpyelp_nonce: adminjs_script_vars.wpyelp_nonce
			} ).done( function( response ) {
				var obj;
				try {
					obj = ( typeof response === 'object' ) ? response : JSON.parse( response );
				} catch ( e ) {
					showMsg( $( '#wpyelp_add_msg' ), 'Unexpected response. Please try again.', true );
					return;
				}
				if ( obj.ack !== 'success' ) {
					showMsg( $( '#wpyelp_add_msg' ), obj.ackmsg || 'Error adding source.', true );
					return;
				}
				showMsg( $( '#wpyelp_add_msg' ), obj.ackmsg || 'Source added.', false );
				$( '#yelp_new_url' ).val( '' );
				$( '#yelp_new_name' ).val( '' );
				if ( obj.row_html ) {
					$( '.wpyelp-no-sources' ).remove();
					$( '#wpyelp_sources_tbody' ).append( obj.row_html );
				} else {
					window.location.reload();
				}
			} ).fail( function() {
				showMsg( $( '#wpyelp_add_msg' ), 'Request failed. Please try again.', true );
			} ).always( function() {
				$btn.prop( 'disabled', false );
				$( '#wpyelp_add_loader' ).hide();
			} );
		} );

		// Download reviews for one source.
		$( '#currentsources' ).on( 'click', '.downloadrevs', function() {
			var $btn = $( this );
			var pageid = $btn.attr( 'data-pageid' );
			var $row = $btn.closest( 'tr' );
			var $loader = $btn.siblings( '.buttonloader2' );
			var $msg = $row.find( '.yelp-source-msg' );

			if ( ! pageid ) {
				showMsg( $msg, 'Missing page ID.', true );
				return;
			}

			$btn.hide();
			$loader.css( 'display', 'inline-block' );
			showMsg( $msg, 'Downloading… this can take a minute or two while we crawl the Yelp page.', false );

			$.post( ajaxurl, {
				action: 'wpyelp_download_source',
				pageid: pageid,
				wpyelp_nonce: adminjs_script_vars.wpyelp_nonce
			} ).done( function( response ) {
				var obj;
				try {
					obj = ( typeof response === 'object' ) ? response : JSON.parse( response );
				} catch ( e ) {
					console.log( 'Yelp crawl raw response (parse failed):', response );
					showMsg( $msg, 'Unexpected response. Please try again or contact support.', true );
					return;
				}
				if ( typeof obj.crawl_debug !== 'undefined' ) {
					console.log( 'Yelp crawl server response:', obj.crawl_debug );
				} else {
					console.log( 'Yelp download response:', obj );
				}
				if ( obj.ack !== 'success' ) {
					showMsg( $msg, obj.ackmsg || 'Download failed.', true );
					return;
				}
				showMsg( $msg, obj.ackmsg || 'Download complete.', false );
				if ( typeof obj.avg !== 'undefined' || typeof obj.total !== 'undefined' ) {
					var avg = ( typeof obj.avg !== 'undefined' && obj.avg !== '' ) ? obj.avg : '—';
					var total = ( typeof obj.total !== 'undefined' && obj.total !== '' ) ? obj.total : '—';
					$row.find( '.yelp-source-stats' ).text( avg + ' / ' + total );
				}
			} ).fail( function( xhr ) {
				console.log( 'Yelp download AJAX failed:', xhr && xhr.responseText ? xhr.responseText : xhr );
				showMsg( $msg, 'Request failed. Please try again.', true );
			} ).always( function() {
				$loader.hide();
				$btn.show();
			} );
		} );

	});

})( jQuery );
