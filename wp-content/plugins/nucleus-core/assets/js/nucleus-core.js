(function ( $ ) {
	'use strict';

	/**
	 * Global callback function for handling AJAX errors
	 *
	 * @param xhr
	 * @param status
	 * @param error
	 */
	var nucleusCoreOnAjaxFail = function ( xhr, status, error ) {
		console.log( [ 'nucleus.core.ajax.error', status, error, xhr, xhr.responseText ] );
	};

	/**
	 * Convert loaded posts through AJAX from raw HTML to jQuery Object.
	 * Used for "Load More" and "Infinite Scroll" function.
	 *
	 * @param {Array} data Array of posts, raw HTML
	 * @returns {Array}
	 */
	var nucleusCoreParsePosts = function ( data ) {
		var $posts = [];

		$.each( data, function ( index, post ) {
			var parsed = $.parseHTML( post );
			$posts.push( parsed[0] );
		} );

		return $posts;
	};

	/**
	 * Collect all Isotope Grids from shortcodes.
	 *
	 * @param selector Isotopes service class
	 * @returns {Array}
	 */
	function nucleusGetIsotopeGrids( selector ) {
		var $isotopes = $( selector );
		var grids = [];
		if ( $isotopes.length > 0 ) {
			$.each( $isotopes, function ( i, isotope ) {
				var ID = $( isotope ).attr( 'id' );
				grids.push( ID );
			} );
		}

		return grids;
	}

	/**
	 * Collect all Isotope Filters from shortcodes.
	 *
	 * @param selector Isotope Filters service class
	 * @returns {Array}
	 */
	function nucleusGetIsotopeFilters( selector ) {
		var $filters = $( selector );
		var filters = [];
		if ( $filters.length > 0 ) {
			$.each( $filters, function ( i, filter ) {
				var ID = $( filter ).attr( 'id' );
				filters.push( ID );
			} );
		}

		return filters;
	}

	/**
	 * Convert the special for Pricing Table
	 *
	 * @param special
	 * @returns {*}
	 */
	function nucleus_convert_special( special ) {
		var result;
		switch ( special ) {
			case 'infinity':
				result = '<span class="infinity"></span>';
				break;

			case 'available':
				result = '<span class="available"></span>';
				break;

			case 'not-available':
				result = '<span class="not-available"></span>';
				break;

			default:
				result = special;
		}

		return result;
	}

	// Counters (Animated Digits)
	function counterOnScrollAnimation( items, trigger ) {
		items.each( function() {
			var counterElement = $(this),
				decimals = $(this).data('decimals'),
				duration = $(this).data('duration');

			var counterTrigger = ( trigger ) ? trigger : counterElement;

			counterTrigger.waypoint(function(direction) {
				if(direction == 'down') {
					counterElement.find('.digits').spincrement({
						from: 0.0,
						decimalPlaces: decimals,
						duration: duration
					});
				} else {
					this.destroy();
				}
			},{
				offset: '95%'
			});
		});
	}

	counterOnScrollAnimation( $('.counter') );

	// Countdown Function
	function countDownFunc( items, trigger ) {
		items.each( function() {
			var countDown = $(this),
				dateTime = $(this).data('date-time');

			var countDownTrigger = ( trigger ) ? trigger : countDown;
			countDownTrigger.downCount({
				date: dateTime
			});
		});
	}

	countDownFunc( $('.countdown') );

	// Progress Bars on Scroll Animation
	function pbOnScrollAnimation( items, trigger ) {
		items.each( function() {
			var pbElement = $(this),
				curVal = pbElement.find('.progress-bar').attr('data-valuenow');

			var pbTrigger = ( trigger ) ? trigger : pbElement;

			pbTrigger.waypoint(function() {
				pbElement.find('.progress-bar').css({'width': curVal + '%'});
			},{
				offset: '90%'
			});
		});
	}

	pbOnScrollAnimation( $('.progress-animated') );

	/**
	 * Portfolio AJAX loading
	 */
	$( document ).on( 'click', '.nucleus-portfolio-more', function ( e ) {
		e.preventDefault();

		var $button = $( this ),
			$items = $( '.nucleus_portfolio' ),
			$preloader = $items.siblings( '.preloader' ),
			query = $button.data( 'query' ),
			page = $button.data( 'page' ),
			maxPages = $button.data( 'max-pages' ),
			type = $button.data( 'type' ),
			filtersId = $button.data( 'filters-id' ),
			gridId = $button.data( 'grid-id' );

		var formdata = {
			action: 'nucleus_portfolio_more',
			nonce: nucleus.nonce,
			query: query,
			page: page,
			max: maxPages,
			type: type
		};

		$button.hide();
		$items.addClass( 'loading' );
		$preloader.addClass( 'active' );

		$.post( nucleus.ajaxurl, formdata ).done( function ( response ) {
			if ( false === response.success ) {
				return false;
			}

			// after preloading
			$button.show();
			$items.removeClass( 'loading' );
			$preloader.removeClass( 'active' );

			// Some isotope magic: convert html string to jQuery Object
			var $posts = nucleusCoreParsePosts( response.data );
			var $container = nucleus.storage.containers[ gridId ];

			$container.append( $posts ).isotope( 'appended', $posts );
			setTimeout( function () {
				$container.isotope( 'layout' );
			}, 50 );

			// may be remove button
			if ( page >= maxPages ) {
				$button.hide();
			}

			$button.data( 'page', page + 1 );

		} ).fail( nucleusCoreOnAjaxFail );
	} );

	/**
	 * Blog AJAX loading
	 */
	$( document ).on( 'click', '.nucleus-blog-more', function ( e ) {
		e.preventDefault();

		var $button = $( this ),
			$preloader = $button.siblings( '.preloader' ),
			query = $button.data( 'query' ),
			page = $button.data( 'page' ),
			maxPages = $button.data( 'max-pages' ),
			gridId = $button.data( 'grid-id' );

		var formdata = {
			action: 'nucleus_blog_more',
			nonce: nucleus.nonce,
			query: query,
			page: page,
			max: maxPages
		};

		$button.hide();
		$preloader.removeClass( 'hidden' );

		$.post( nucleus.ajaxurl, formdata ).done( function ( response ) {
			if ( false === response.success ) {
				return false;
			}

			// after preloading
			$button.show();
			$preloader.addClass( 'hidden' );

			// Some isotope magic: convert html string to jQuery Object
			var $posts = nucleusCoreParsePosts( response.data );
			var $container = nucleus.storage.containers[ gridId ];

			$container.append( $posts ).isotope( 'appended', $posts );
			setTimeout( function () {
				$container.isotope( 'layout' );
			}, 50 );

			// may be remove button
			if ( page >= maxPages ) {
				$button.hide();
			}

			$button.data( 'page', page + 1 );

		} ).fail( nucleusCoreOnAjaxFail );
	} );

	// On window load functions
	$( window ).on( 'load', function () {

		/**
		 * Collect all isotope grids for further initialization
		 */
		nucleus.storage.grids = nucleusGetIsotopeGrids( '.nucleus-isotope' );
		nucleus.storage.filters = nucleusGetIsotopeFilters( '.nucleus-isotope-filter' );

		/**
		 * Dynamically init the isotope grids
		 */
		if (nucleus.storage.grids.length > 0) {
			$.each(nucleus.storage.grids, function (index, gridID) {
				var $grid = $('#' + gridID);

				nucleus.storage.containers[gridID] = $grid.isotope({
					itemSelector: '.grid-item',
					transitionDuration: '0.7s',
					masonry: {
						columnWidth: '.grid-sizer',
						gutter: '.gutter-sizer'
					}
				});
			});
		}

		/**
		 * Dynamically attach the isotope filtration to isotope grids
		 */
		if ( nucleus.storage.filters.length > 0 ) {
			$.each( nucleus.storage.filters, function ( index, filtersID ) {
				if ( 'false' === filtersID || filtersID.length === 0 ) {
					return false;
				}

				// Create $filters
				// Where $filters is a jQuery object of container <ul> with filters.
				var $filters = $( '#' + filtersID );

				// Find all <a> inside <ul> filters and bind the click on them
				$filters.find( 'a' ).click( function ( e ) {
					e.preventDefault();
					var $this = $( this ),
						filter = $this.data( 'filter' ),
						gridId = $filters.data( 'grid-id' );

					// don't proceed if already selected
					if ( $this.parent( 'li' ).hasClass( 'active' ) ) {
						return false;
					}

					// Add class .active for recently clicked item
					$filters.find( '.active' ).removeClass( 'active' );
					$this.parent( 'li' ).addClass( 'active' );

					// make option object dynamically, i.e. { filter: '.my-filter-class' }
					// and apply new options to isotope containers
					nucleus.storage.containers[ gridId ].isotope( { filter: filter } );

					return false;
				} );
			} );
		}

		// Scroll Reveal Animations
		var $scrollReveal = $( '.scrollReveal' );
		if ( $scrollReveal.length && ! $( 'html.ie9' ).length ) {
			$scrollReveal.parent().css( 'overflow', 'hidden' );
			window.sr = ScrollReveal( {
				reset: true,
				distance: '32px',
				mobile: true,
				duration: 850,
				scale: 1,
				viewFactor: 0.3,
				easing: 'ease-in-out'
			} );
			sr.reveal( '.sr-top', {origin: 'top'} );
			sr.reveal( '.sr-bottom', {origin: 'bottom'} );
			sr.reveal( '.sr-left', {origin: 'left'} );
			sr.reveal( '.sr-long-left', {origin: 'left', distance: '70px', duration: 1000} );
			sr.reveal( '.sr-right', {origin: 'right'} );
			sr.reveal( '.sr-scaleUp', {scale: '0.8'} );
			sr.reveal( '.sr-scaleDown', {scale: '1.15'} );

			sr.reveal( '.sr-delay-1', {delay: 200} );
			sr.reveal( '.sr-delay-2', {delay: 400} );
			sr.reveal( '.sr-delay-3', {delay: 600} );
			sr.reveal( '.sr-delay-4', {delay: 800} );
			sr.reveal( '.sr-delay-5', {delay: 1000} );
			sr.reveal( '.sr-delay-6', {delay: 1200} );
			sr.reveal( '.sr-delay-7', {delay: 1400} );
			sr.reveal( '.sr-delay-8', {delay: 1600} );

			sr.reveal( '.sr-ease-in-out-quad', {easing: 'cubic-bezier(0.455,  0.030, 0.515, 0.955)'} );
			sr.reveal( '.sr-ease-in-out-cubic', {easing: 'cubic-bezier(0.645,  0.045, 0.355, 1.000)'} );
			sr.reveal( '.sr-ease-in-out-quart', {easing: 'cubic-bezier(0.770,  0.000, 0.175, 1.000)'} );
			sr.reveal( '.sr-ease-in-out-quint', {easing: 'cubic-bezier(0.860,  0.000, 0.070, 1.000)'} );
			sr.reveal( '.sr-ease-in-out-sine', {easing: 'cubic-bezier(0.445,  0.050, 0.550, 0.950)'} );
			sr.reveal( '.sr-ease-in-out-expo', {easing: 'cubic-bezier(1.000,  0.000, 0.000, 1.000)'} );
			sr.reveal( '.sr-ease-in-out-circ', {easing: 'cubic-bezier(0.785,  0.135, 0.150, 0.860)'} );
			sr.reveal( '.sr-ease-in-out-back', {easing: 'cubic-bezier(0.680, -0.550, 0.265, 1.550)'} );
		}

		// Image Carousel
		var $imageCarousel = $( '.image-carousel .inner' );
		if ( $imageCarousel.length > 0 ) {
			$imageCarousel.each( function () {

				var dataLoop = $( this ).parent().data( 'loop' ),
					autoPlay = $( this ).parent().data( 'autoplay' ),
					timeOut = $( this ).parent().data( 'interval' ),
					autoheight = $( this ).parent().data( 'autoheight' );

				$( this ).owlCarousel( {
					items: 1,
					loop: dataLoop,
					margin: 0,
					nav: true,
					dots: false,
					navText: [,],
					autoplay: autoPlay,
					autoplayTimeout: timeOut,
					autoHeight: autoheight
				} );
			} );
		}

		// Logo Carousel
		var $logoCarousel = $( '.logo-carousel .inner' );
		if ( $logoCarousel.length > 0 ) {
			$logoCarousel.each( function () {

				var dataLoop = $( this ).parent().data( 'loop' ),
					autoPlay = $( this ).parent().data( 'autoplay' ),
					timeOut = $( this ).parent().data( 'interval' );

				$( this ).owlCarousel( {
					loop: dataLoop,
					margin: 20,
					nav: false,
					dots: false,
					autoplay: autoPlay,
					autoplayTimeout: timeOut,
					responsiveClass: true,
					responsive: {
						0: {items: 2},
						480: {items: 3},
						700: {items: 4},
						1000: {items: 5},
						1200: {items: 6}
					}
				} );
			} );
		}

		// Video Popup
		var $videoBtn = $( '.video-popup-btn > .play-btn, .video-popup-tile .play-btn' );
		if( $videoBtn.length > 0 ) {
			$videoBtn.magnificPopup( {
				type: 'iframe',
				mainClass: 'mfp-fade',
				removalDelay: 500
			} );
		}

		// Gallery Popup
		var $gallItem = $( '.gallery-item' );
		if( $gallItem.length > 0 ) {
			$gallItem.magnificPopup( {
				type: 'image',
				mainClass: 'mfp-fade',
				gallery: {
					enabled: true
				},
				removalDelay: 500,
				image: {
					titleSrc: 'data-title'
				}
			} );
		}

	} );

	$( document ).ready( function () {
		// Toggleable Switch component
		var $sWitch = $( '.switch' );
		$sWitch.on( 'click', function () {

			var clicks = $( this ).data( 'clicks' ),
				inputVal = $( this ).find( 'input' ).attr( 'value' );

			if ( clicks && inputVal == 'off' ) {
				$( this ).find( 'input' ).attr( 'value', 'on' );
				$( this ).addClass( 'on' );
			} else if ( clicks && inputVal == 'on' ) {
				$( this ).find( 'input' ).attr( 'value', 'off' );
				$( this ).removeClass( 'on' );
			} else if ( ! clicks && inputVal == 'off' ) {
				$( this ).find( 'input' ).attr( 'value', 'on' );
				$( this ).addClass( 'on' );
			} else if ( ! clicks && inputVal == 'on' ) {
				$( this ).find( 'input' ).attr( 'value', 'off' );
				$( this ).removeClass( 'on' );
			}

			$( this ).data( "clicks", ! clicks );
			$( this ).find( 'input' ).trigger( 'change' );
		} );

		// Google Maps API
		var $googleMap = $( '.google-map' );
		if ( $googleMap.length > 0 ) {
			$googleMap.each( function () {
				var mapHeight = $( this ).data( 'height' ) || 500,
					address = $( this ).data( 'address' ) || '',
					zoom = $( this ).data( 'zoom' ) || 14,
					controls = $( this ).data( 'disable-controls' ),
					scrollwheel = $( this ).data( 'scrollwheel' ),
					marker = $( this ).data( 'marker' ) || '',
					markerTitle = $( this ).data( 'marker-title' ),
					styles = $( this ).data( 'styles' ) || '';
				$( this ).height( mapHeight );
				$( this ).gmap3( {
					marker: {
						address: address,
						data: markerTitle,
						options: {
							icon: marker
						},
						events: {
							mouseover: function ( marker, event, context ) {
								if ( typeof markerTitle !== 'undefined' ) {
									var map = $( this ).gmap3( "get" ),
										infowindow = $( this ).gmap3( {get: {name: "infowindow"}} );
									if ( infowindow ) {
										infowindow.open( map, marker );
										infowindow.setContent( context.data );
									} else {
										$( this ).gmap3( {
											infowindow: {
												anchor: marker,
												options: {content: context.data}
											}
										} );
									}
								}
							},
							mouseout: function () {
								var infowindow = $( this ).gmap3( {get: {name: "infowindow"}} );
								if ( infowindow ) {
									infowindow.close();
								}
							}
						}
					},
					map: {
						options: {
							zoom: zoom,
							disableDefaultUI: controls,
							scrollwheel: scrollwheel,
							styles: styles
						}
					}
				} );
			} );
		}

		// Feature Tabs (Changing screens of Tablet and Phone)
		$( '.feature-tabs .nav-tabs li a' ).on( 'click', function ( e ) {
			var currentPhoneSlide = $( this ).data( "phone" );
			var currentTabletSlide = $( this ).data( "tablet" );
			console.log( ['nucleus.phone.tablet', currentPhoneSlide, currentTabletSlide] );
			$( this ).parents( '.feature-tabs' ).find( ".devices .phone .screens li" ).removeClass( "active" );
			$( this ).parents( '.feature-tabs' ).find( ".devices .tablet .screens li" ).removeClass( "active" );
			$( currentPhoneSlide ).addClass( "active" );
			$( currentTabletSlide ).addClass( "active" );
		} );

		// Feature Tabs Auto Switching
		var autoSwitchedTabs = $( '.feature-tabs .nav-tabs[data-autoswitch="true"]' );
		if ( autoSwitchedTabs.length > 0 ) {
			autoSwitchedTabs.each( function ( i, tabs ) {
				var $tabs = $( tabs );
				var changeInterval = $tabs.data( 'interval' );
				setInterval( function () {
					var tabs = $tabs.find( 'li' ),
						active = tabs.filter( '.active' ),
						next = active.next( 'li' ),
						toClick = next.length ? next.find( 'a' ) : tabs.eq( 0 ).find( 'a' );

					toClick.trigger( 'click' );
				}, changeInterval );
			} );
		}
	} );

	/* Switch the data in Pricing Table shortcode */
	$( document ).on( 'change', '.switch input', function ( e ) {
		// NOTE: on = right, off = left
		var state = $( this ).val();
		var props = $( '.pricing-table' ).find( '.pp-prop' );

		$.each( props, function( i, td ) {
			var $td = $( td );
			var left = $td.data( 'left' );
			var right = $td.data( 'right' );
			var v = ( 'on' === state ) ? right : left;

			if ( v.length == 0 || '#' === v ) {
				return true;
			}

			$td.html( nucleus_convert_special( v ) );
		} );

	} );

})( jQuery );
