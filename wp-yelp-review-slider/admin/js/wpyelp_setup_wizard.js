/**
 * Setup wizard screen (admin.php?page=wp_yelp-setup).
 *
 * Steps: get reviews, choose a look (saves a real template and shows its
 * preview), add to the site, finish.
 */
( function ( $ ) {
	'use strict';

	var cfg = window.wpyelpWizard || {};
	var i18n = cfg.i18n || {};
	var state = cfg.state || {};
	var steps = [ 'source', 'look', 'place', 'finish' ];

	var current = 0;
	var furthest = 0;
	var savedKey = '';
	var saveTimer = null;
	var saveRequest = null;

	var $root;

	function text( key ) {
		return i18n[ key ] || '';
	}

	function post( action, data ) {
		return $.post( cfg.ajaxurl, $.extend( { action: action, nonce: cfg.nonce }, data ) );
	}

	function setStatus( $el, message, type ) {
		$el.removeClass( 'is-error is-success is-info' ).addClass( 'is-' + ( type || 'info' ) );
		$el.text( message ).prop( 'hidden', ! message );
	}

	// Ajax replies are normally { success, data }, but the Yelp crawl code answers
	// errors itself with { ack: 'error', ackmsg } (sometimes as a string).
	function parseResponse( response ) {
		if ( 'string' === typeof response ) {
			var start = response.indexOf( '{' );
			try {
				return start > -1 ? JSON.parse( response.slice( start ) ) : null;
			} catch ( e ) {
				return null;
			}
		}
		return response;
	}

	function errorMessage( response ) {
		response = parseResponse( response );
		if ( response && response.data && response.data.message ) {
			return response.data.message;
		}
		if ( response && response.ackmsg ) {
			return text( 'crawlFailed' ) + ' (' + response.ackmsg + ')';
		}
		return text( 'requestFailed' );
	}

	/* ---------- Step 1: reviews ---------- */

	function findSource( pageid ) {
		var list = state.sources || [];
		for ( var i = 0; i < list.length; i++ ) {
			if ( list[ i ].pageid === pageid ) {
				return list[ i ];
			}
		}
		return null;
	}

	// The Yelp page downloaded (or downloading) in this visit, for "Add a different Yelp page".
	var downloadedPageid = '';

	// Yelp reviews come from DataForSEO, which takes from a few minutes to a couple of hours.
	// The site keeps checking on its own (WP-Cron), so this page only follows along while it's
	// open: often at first, then every 20 seconds.
	var follow = { pageid: '', started: 0, timer: null };

	function nextCheckIn() {
		return ( Date.now() - follow.started ) < 2 * 60 * 1000 ? 5000 : 20000;
	}

	function elapsed( since ) {
		var secs = Math.max( 0, Math.round( ( Date.now() - since ) / 1000 ) );
		var mins = Math.floor( secs / 60 );
		if ( mins >= 60 ) {
			return Math.floor( mins / 60 ) + 'h ' + ( mins % 60 ) + 'm';
		}
		return mins + ':' + ( '0' + ( secs % 60 ) ).slice( -2 );
	}

	function setBusy( busy ) {
		$root.find( '[data-get-reviews]' ).prop( 'disabled', busy );
		$root.find( '[data-get-spinner]' ).toggleClass( 'is-active', busy );
	}

	function updateSourceNote( src ) {
		if ( ! src ) {
			return;
		}
		$root.find( '[data-source-note="' + src.pageid + '"]' ).text(
			src.count > 0 ? text( 'savedNote' ).replace( '%s', src.count ) : text( 'downloadingNote' )
		);
	}

	function showWaiting() {
		var $status = $root.find( '[data-source-status]' );
		setStatus( $status, text( 'downloading' ).replace( '%s', elapsed( follow.started ) ) + ' ' + text( 'canContinue' ), 'info' );
		$root.find( '[data-finish-pending]' ).prop( 'hidden', false );
	}

	// Whether the followed download is the one picked in step 1 (or a new page being added).
	function followsSelection() {
		var $radio = $root.find( 'input[name="wpyelp-wiz-source"]:checked' );
		return ! $radio.length || 'new' === $radio.val() || $radio.val() === follow.pageid || ! follow.pageid;
	}

	// Handle any reply about the download: still running, saved, or failed.
	function handleDownload( response ) {
		response = parseResponse( response );
		var $status = $root.find( '[data-source-status]' );
		if ( response && response.data && response.data.sources ) {
			state.sources = response.data.sources;
		}
		if ( response && response.success && response.data.pending ) {
			follow.pageid = response.data.pageid;
			follow.started = ( response.data.started || 0 ) * 1000 || follow.started || Date.now();
			updateSourceNote( findSource( follow.pageid ) );
			setBusy( false );
			if ( followsSelection() ) {
				state.pageid = follow.pageid;
				downloadedPageid = follow.pageid;
				setNavMessage( '' );
				showWaiting();
			}
			window.clearTimeout( follow.timer );
			follow.timer = window.setTimeout( checkDownload, nextCheckIn() );
			return;
		}

		var followed = follow.pageid;
		var shown = followsSelection();
		follow.pageid = '';
		setBusy( false );
		$root.find( '[data-finish-pending]' ).prop( 'hidden', true );
		if ( ! shown ) {
			// Finished for a page that isn't picked right now: just update its label.
			updateSourceNote( findSource( followed ) );
			return;
		}
		if ( response && response.success ) {
			state.pageid = response.data.pageid;
			downloadedPageid = response.data.pageid;
			updateSourceNote( findSource( state.pageid ) );
			setStatus( $status, response.data.message, 'success' );
			setNavMessage( '' );
			// The reviews just arrived: redraw the preview with them.
			if ( state.tid ) {
				savedKey = '';
				queueSave( 0 );
			}
		} else {
			setStatus( $status, errorMessage( response ), 'error' );
		}
	}

	function checkDownload() {
		if ( ! follow.pageid ) {
			return;
		}
		post( 'wpyelp_wizard_poll_reviews', { pageid: follow.pageid } )
			.done( handleDownload )
			.fail( function ( xhr ) {
				// A dropped check isn't fatal; the job is still running. Try again.
				if ( ! xhr || ! xhr.responseText ) {
					follow.timer = window.setTimeout( checkDownload, nextCheckIn() );
					return;
				}
				handleDownload( xhr.responseText );
			} );
	}

	function getReviews() {
		var url = $.trim( $( '#wpyelp-wiz-url' ).val() );
		var $status = $root.find( '[data-source-status]' );

		if ( '' === url ) {
			setStatus( $status, text( 'needUrl' ), 'error' );
			$( '#wpyelp-wiz-url' ).trigger( 'focus' );
			return;
		}

		window.clearTimeout( follow.timer );
		follow.pageid = '';
		follow.started = Date.now();
		setBusy( true );
		setStatus( $status, text( 'downloading' ).replace( '%s', '0:00' ), 'info' );

		post( 'wpyelp_wizard_get_reviews', { url: url, name: $.trim( $( '#wpyelp-wiz-name' ).val() ) } )
			.done( handleDownload )
			.fail( function ( xhr ) {
				handleDownload( xhr && xhr.responseText ? xhr.responseText : null );
			} );
	}

	// Reviews saved, or on their way: either way setup can go on.
	function usable( src ) {
		return !! ( src && ( src.count > 0 || src.pending || follow.pageid === src.pageid ) );
	}

	function canLeaveSource() {
		var $radio = $root.find( 'input[name="wpyelp-wiz-source"]:checked' );
		if ( $radio.length && 'new' !== $radio.val() ) {
			state.pageid = $radio.val();
		} else if ( $radio.length ) {
			// "Add a different Yelp page" counts once its download worked or started.
			if ( ! downloadedPageid ) {
				return false;
			}
			state.pageid = downloadedPageid;
		}
		return usable( findSource( state.pageid ) );
	}

	/* ---------- Step 2: look ---------- */

	function lookValues() {
		return {
			pageid: state.pageid,
			style: $root.find( 'input[name="wpyelp-wiz-style"]:checked' ).val() || '1',
			layout: $root.find( 'input[name="wpyelp-wiz-layout"]:checked' ).val() || 'slider',
			per_row: $root.find( 'input[name="wpyelp-wiz-perrow"]:checked' ).val() || '3',
			rows: $root.find( 'input[name="wpyelp-wiz-rows"]:checked' ).val() || '1',
			badge: $root.find( 'input[name="wpyelp-wiz-badge"]:checked' ).val() || 'above',
			read_more: $root.find( 'input[name="wpyelp-wiz-readmore"]:checked' ).val() || '30'
		};
	}

	// A badge beside the reviews leaves little room for 3 or 4 reviews per row.
	function updateBadgeHint() {
		var values = lookValues();
		var side = 'left' === values.badge || 'right' === values.badge;
		$root.find( '[data-badge-hint]' ).prop( 'hidden', ! ( side && parseInt( values.per_row, 10 ) >= 3 ) );
	}

	function queueSave( delay ) {
		updateBadgeHint();
		window.clearTimeout( saveTimer );
		saveTimer = window.setTimeout( saveTemplate, delay );
	}

	function saveTemplate() {
		var values = lookValues();
		var key = JSON.stringify( values );
		if ( key === savedKey && state.tid ) {
			return;
		}
		if ( saveRequest ) {
			saveRequest.abort();
		}

		var $spin = $root.find( '[data-preview-spinner]' );
		var $msg = $root.find( '[data-preview-msg]' );
		$spin.addClass( 'is-active' );
		$msg.text( text( 'saving' ) );
		$root.find( '[data-preview-wrap]' ).addClass( 'is-loading' );

		saveRequest = post( 'wpyelp_wizard_save_template', $.extend( { tid: state.tid || 0 }, values ) )
			.done( function ( response ) {
				if ( ! response || ! response.success ) {
					$msg.text( errorMessage( response ) );
					return;
				}
				savedKey = key;
				state.tid = response.data.tid;
				state.title = response.data.title;
				state.shortcode = response.data.shortcode;
				// Before the real reviews arrive, the preview uses sample reviews; say so.
				$msg.text( response.data.sample ? text( 'sampleNote' ) : '' );
				$root.find( '[data-preview-wrap]' ).toggleClass( 'is-sample', !! response.data.sample );
				var $preview = $root.find( '[data-preview]' );
				if ( ! response.data.html ) {
					$preview.html( $( '<p class="wpyelp-wiz__waiting">' ).text( text( 'previewWaiting' ) ) );
				} else {
					$preview.html( response.data.html );
				}
				if ( typeof window.wprevInitReviewSliders === 'function' ) {
					window.wprevInitReviewSliders( $preview[ 0 ] );
				}
				fillTemplateDetails();
			} )
			.fail( function ( xhr, status ) {
				if ( 'abort' !== status ) {
					$msg.text( text( 'requestFailed' ) );
				}
			} )
			.always( function ( a, status ) {
				if ( 'abort' === status ) {
					return;
				}
				saveRequest = null;
				$spin.removeClass( 'is-active' );
				$root.find( '[data-preview-wrap]' ).removeClass( 'is-loading' );
			} );
	}

	/* ---------- Step 3: place ---------- */

	function fillTemplateDetails() {
		$root.find( '[data-template-title]' ).text( state.title ? '“' + state.title + '”' : '' );
		$root.find( '[data-shortcode]' ).text( state.tid ? ( state.shortcode || '[wpyelp_usetemplate tid="' + state.tid + '"]' ) : '' );
	}

	function showPage( page ) {
		if ( ! page ) {
			return;
		}
		state.page = page;
		$root.find( '[data-page-edit]' ).attr( 'href', page.edit );
		$root.find( '[data-page-view]' ).attr( 'href', page.view );
		$root.find( '[data-page-links]' ).prop( 'hidden', false );
		// One page is enough; the form would only make a duplicate.
		$root.find( '[data-page-form]' ).prop( 'hidden', true );
	}

	function createPage() {
		var $btn = $root.find( '[data-create-page]' );
		var $spin = $root.find( '[data-page-spinner]' );
		var $status = $root.find( '[data-page-status]' );

		$btn.prop( 'disabled', true );
		$spin.addClass( 'is-active' );
		setStatus( $status, text( 'creatingPage' ), 'info' );

		post( 'wpyelp_wizard_create_page', { tid: state.tid, title: $.trim( $( '#wpyelp-wiz-pagetitle' ).val() ) } )
			.done( function ( response ) {
				if ( response && response.success ) {
					setStatus( $status, response.data.message, 'success' );
					showPage( response.data.page );
				} else {
					setStatus( $status, errorMessage( response ), 'error' );
				}
			} )
			.fail( function () {
				setStatus( $status, text( 'requestFailed' ), 'error' );
			} )
			.always( function () {
				$btn.prop( 'disabled', false );
				$spin.removeClass( 'is-active' );
			} );
	}

	function copyShortcode() {
		var value = $root.find( '[data-shortcode]' ).text();
		var $btn = $root.find( '[data-copy-shortcode]' );
		var done = function () {
			$btn.text( text( 'copied' ) );
			window.setTimeout( function () {
				$btn.text( text( 'copy' ) );
			}, 1500 );
		};
		if ( navigator.clipboard && window.isSecureContext ) {
			navigator.clipboard.writeText( value ).then( done );
			return;
		}
		var $tmp = $( '<textarea readonly>' ).val( value ).css( { position: 'absolute', left: '-9999px' } ).appendTo( 'body' );
		$tmp[ 0 ].select();
		try {
			document.execCommand( 'copy' );
			done();
		} catch ( e ) {}
		$tmp.remove();
	}

	/* ---------- Navigation ---------- */

	function setNavMessage( message ) {
		$root.find( '[data-nav-msg]' ).text( message );
	}

	function show( index, focus ) {
		current = index;
		furthest = Math.max( furthest, index );
		var key = steps[ index ];

		$root.find( '[data-step]' ).each( function () {
			$( this ).prop( 'hidden', $( this ).attr( 'data-step' ) !== key );
		} );
		$root.find( '[data-step-item]' ).each( function ( i ) {
			$( this ).toggleClass( 'is-current', i === index ).toggleClass( 'is-done', i < index );
			$( this ).find( '[data-goto]' ).prop( 'disabled', i > furthest ).attr( 'aria-current', i === index ? 'step' : null );
		} );

		$root.find( '[data-prev]' ).prop( 'hidden', 0 === index );
		$root.find( '[data-next]' ).text( index === steps.length - 1 ? text( 'finish' ) : text( 'continue' ) );
		$root.find( '[data-count]' ).text( text( 'stepOf' ).replace( '%1$d', index + 1 ).replace( '%2$d', steps.length ) );
		setNavMessage( '' );

		if ( 'look' === key ) {
			queueSave( 0 );
		}
		if ( 'place' === key ) {
			fillTemplateDetails();
		}
		if ( focus ) {
			$root.find( '[data-step="' + key + '"] h2' ).trigger( 'focus' );
		}
	}

	function next() {
		var key = steps[ current ];
		if ( 'source' === key && ! canLeaveSource() ) {
			setNavMessage( text( 'needReviews' ) );
			return;
		}
		if ( 'look' === key && ( ! state.tid || saveRequest ) ) {
			setNavMessage( text( 'noTemplate' ) );
			return;
		}
		if ( current === steps.length - 1 ) {
			finish();
			return;
		}
		show( current + 1, true );
	}

	function finish() {
		var $btn = $root.find( '[data-next]' ).prop( 'disabled', true );
		post( 'wpyelp_wizard_finish', {} )
			.done( function ( response ) {
				if ( response && response.success && response.data.redirect ) {
					window.location.href = response.data.redirect;
					return;
				}
				setNavMessage( errorMessage( response ) );
				$btn.prop( 'disabled', false );
			} )
			.fail( function () {
				setNavMessage( text( 'requestFailed' ) );
				$btn.prop( 'disabled', false );
			} );
	}

	$( function () {
		$root = $( '[data-wpyelp-wizard]' );
		if ( ! $root.length ) {
			return;
		}

		$root.on( 'change', 'input[name="wpyelp-wiz-source"]', function () {
			var isNew = 'new' === $( this ).val();
			$root.find( '[data-new-source]' ).prop( 'hidden', ! isNew );
			setStatus( $root.find( '[data-source-status]' ), '', 'info' );
			setNavMessage( '' );
			if ( ! isNew ) {
				state.pageid = $( this ).val();
				var picked = findSource( state.pageid );
				if ( picked && picked.pending && ! picked.count && follow.pageid !== picked.pageid ) {
					// Its reviews are still on their way: show that and follow along.
					follow.pageid = picked.pageid;
					follow.started = picked.pending * 1000;
					showWaiting();
					checkDownload();
				} else if ( picked && picked.pending && ! picked.count ) {
					showWaiting();
				}
			} else {
				$( '#wpyelp-wiz-url' ).trigger( 'focus' );
			}
		} );
		$root.on( 'click', '[data-get-reviews]', getReviews );
		$root.on( 'keydown', '#wpyelp-wiz-url, #wpyelp-wiz-name', function ( e ) {
			if ( 13 === e.which ) {
				e.preventDefault();
				getReviews();
			}
		} );

		$root.on( 'change', 'input[name="wpyelp-wiz-style"], input[name="wpyelp-wiz-layout"], input[name="wpyelp-wiz-perrow"], input[name="wpyelp-wiz-rows"], input[name="wpyelp-wiz-badge"], input[name="wpyelp-wiz-readmore"]', function () {
			queueSave( 250 );
		} );

		$root.on( 'click', '[data-create-page]', createPage );
		$root.on( 'click', '[data-copy-shortcode]', copyShortcode );

		$root.on( 'click', '[data-next]', next );
		$root.on( 'click', '[data-prev]', function () {
			if ( current > 0 ) {
				show( current - 1, true );
			}
		} );
		$root.on( 'click', '[data-goto]', function () {
			var index = steps.indexOf( $( this ).attr( 'data-goto' ) );
			if ( index > -1 && index <= furthest && index !== current ) {
				if ( index > current ) {
					// Moving forward through the stepper follows the same checks as Continue.
					while ( current < index ) {
						var before = current;
						next();
						if ( current === before ) {
							return;
						}
					}
					return;
				}
				show( index, true );
			}
		} );

		if ( state.page ) {
			showPage( state.page );
		}
		var resumeSource = findSource( state.pageid );
		if ( resumeSource && resumeSource.pending ) {
			follow.pageid = resumeSource.pageid;
			follow.started = resumeSource.pending * 1000;
			downloadedPageid = resumeSource.pageid;
			showWaiting();
			checkDownload();
		}
		show( 0, false );
	} );
}( window.jQuery ) );
