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

		// Replies are JSON, sometimes sent as text; the crawl code can print its own error JSON.
		function parseReply( response ) {
			if ( typeof response === 'object' && response !== null ) {
				return response;
			}
			var text = String( response || '' );
			var start = text.indexOf( '{' );
			try {
				return start > -1 ? JSON.parse( text.slice( start ) ) : null;
			} catch ( e ) {
				return null;
			}
		}

		function elapsedText( started ) {
			var secs = Math.max( 0, Math.round( ( Date.now() - started ) / 1000 ) );
			var mins = Math.floor( secs / 60 );
			if ( mins >= 60 ) {
				return Math.floor( mins / 60 ) + 'h ' + ( mins % 60 ) + 'm';
			}
			return mins + ':' + ( '0' + ( secs % 60 ) ).slice( -2 );
		}

		// Check often at first, then less often. Yelp (through DataForSEO) takes from a few
		// minutes to a couple of hours, and the site keeps checking every five minutes on its
		// own (WP-Cron), so the page can be closed at any time.
		function nextCheckIn( started ) {
			return ( Date.now() - started ) < 2 * 60 * 1000 ? 5000 : 20000;
		}

		// Start (or, with resumeFrom, keep following) the download for one source row.
		function runDownload( $row, resumeFrom ) {
			var pageid = $row.attr( 'data-pageid' );
			var $btn = $row.find( '.downloadrevs' );
			var $loader = $row.find( '.buttonloader2' );
			var $msg = $row.find( '.yelp-source-msg' );
			var started = resumeFrom || Date.now();

			if ( ! pageid ) {
				showMsg( $msg, 'Missing page ID.', true );
				return;
			}

			$btn.hide();
			$loader.css( 'display', 'inline-block' );

			function waiting() {
				var text = 'Downloading… (' + elapsedText( started ) + ')';
				if ( Date.now() - started > 60 * 1000 ) {
					text += ' Yelp can take from a few minutes to a couple of hours. You can leave this page; the reviews are saved automatically when Yelp finishes.';
				} else {
					text += ' Yelp reviews can take a few minutes to collect.';
				}
				showMsg( $msg, text, false );
			}

			function stop() {
				$loader.hide();
				$btn.show();
				$row.removeAttr( 'data-pending' );
			}

			function finished( obj ) {
				if ( ! obj ) {
					showMsg( $msg, 'Unexpected response. Please try again or contact support.', true );
					stop();
					return;
				}
				if ( obj.ack === 'pending' ) {
					if ( obj.started ) {
						started = obj.started * 1000;
					}
					waiting();
					window.setTimeout( check, nextCheckIn( started ) );
					return;
				}
				console.log( 'Yelp download response:', obj );
				if ( obj.ack !== 'success' ) {
					showMsg( $msg, obj.ackmsg || 'Download failed.', true );
					stop();
					return;
				}
				showMsg( $msg, obj.ackmsg || 'Download complete.', false );
				if ( typeof obj.avg !== 'undefined' || typeof obj.total !== 'undefined' ) {
					var avg = ( typeof obj.avg !== 'undefined' && obj.avg !== '' ) ? obj.avg : '—';
					var total = ( typeof obj.total !== 'undefined' && obj.total !== '' ) ? obj.total : '—';
					$row.find( '.yelp-source-stats' ).text( avg + ' / ' + total );
				}
				stop();
			}

			function failed( xhr ) {
				// The crawl code can answer an error with its own JSON and a non-200 status.
				var obj = xhr && xhr.responseText ? parseReply( xhr.responseText ) : null;
				if ( obj && obj.ack ) {
					finished( obj );
					return;
				}
				console.log( 'Yelp download AJAX failed:', xhr && xhr.responseText ? xhr.responseText : xhr );
				showMsg( $msg, 'Request failed. Please try again.', true );
				stop();
			}

			function check() {
				$.post( ajaxurl, {
					action: 'wpyelp_download_poll',
					pageid: pageid,
					wpyelp_nonce: adminjs_script_vars.wpyelp_nonce
				} ).done( function( response ) {
					finished( parseReply( response ) );
				} ).fail( function( xhr ) {
					// A dropped check isn't fatal; the task is still running. Try again.
					if ( ! xhr || ! xhr.responseText ) {
						window.setTimeout( check, nextCheckIn( started ) );
						return;
					}
					failed( xhr );
				} );
			}

			if ( resumeFrom ) {
				waiting();
				check();
				return;
			}

			showMsg( $msg, 'Downloading… Yelp reviews can take a few minutes to collect.', false );
			$.post( ajaxurl, {
				action: 'wpyelp_download_start',
				pageid: pageid,
				wpyelp_nonce: adminjs_script_vars.wpyelp_nonce
			} ).done( function( response ) {
				finished( parseReply( response ) );
			} ).fail( failed );
		}

		$( '#currentsources' ).on( 'click', '.downloadrevs', function() {
			runDownload( $( this ).closest( 'tr' ), 0 );
		} );

		// Downloads that were still running when the page loaded: keep following them.
		$( '#currentsources tr[data-pending]' ).each( function() {
			var startedAt = parseInt( $( this ).attr( 'data-pending' ), 10 );
			runDownload( $( this ), startedAt > 0 ? startedAt * 1000 : Date.now() );
		} );
	});

})( jQuery );
