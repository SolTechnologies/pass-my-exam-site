/*
 * Nucleus Multi-Purpose Technology Template
 * Copyright 2016 8Guild.com
 * Theme Custom Scripts
 */
 /* ╔══╗╔═══╗╔╗╔╗╔══╗╔╗──╔══╗
  	║╔╗║║╔══╝║║║║╚╗╔╝║║──║╔╗╚╗
  	║╚╝║║║╔═╗║║║║─║║─║║──║║╚╗║
  	║╔╗║║║╚╗║║║║║─║║─║║──║║─║║
  	║╚╝║║╚═╝║║╚╝║╔╝╚╗║╚═╗║╚═╝║
  	╚══╝╚═══╝╚══╝╚══╝╚══╝╚═══╝
*/
(function ( $ ) {
	'use strict';

	$( document ).ready( function ( $ ) {
		// Disable default link behavior for dummy links that have href='#'
		var $emptyLink = $( 'a[href="#"]' );
		$emptyLink.on( 'click', function ( e ) {
			e.preventDefault();
		} );

		// Stuck navbar on scroll
		if ( $( '.navbar-sticky' ).length && $( '.main-navigation' ).length ) {
			var sticky = new Waypoint.Sticky( {
				element: $( '.navbar-sticky .main-navigation' )[ 0 ]
			} );
		}

		// Search form expand (Topbar)
		var $searchToggle = $( '.search-btn > i, .toolbar' );
		$searchToggle.on( 'click', function () {
			$( this ).parent().find( '.search-box' ).addClass( 'open' );
		} );
		$( '.search-btn' ).on( 'click', function ( e ) {
			e.stopPropagation();
		} );
		$( document ).on( 'click', function ( e ) {
			$( '.search-box' ).removeClass( 'open' );
		} );

		// Waves Effect (on Buttons)
		if ( $( '.waves-effect' ).length > 0 ) {
			Waves.displayEffect( { duration: 600 } );
		}

		// Animated Scroll to Top Button
		var $scrollTop = $( '.scroll-to-top-btn' );
		if ( $scrollTop.length > 0 ) {
			$( window ).on( 'scroll', function () {
				if ( $( window ).scrollTop() > 600 ) {
					$scrollTop.addClass( 'visible' );
				} else {
					$scrollTop.removeClass( 'visible' );
				}
			} );
			$scrollTop.on( 'click', function ( e ) {
				e.preventDefault();
				$( 'html' ).velocity( "scroll", { offset: 0, duration: 1000, easing: 'easeOutExpo', mobileHA: false } );
			} );
		}

		// Smooth scroll to element
		var $scrollTo = $( '.scroll-to' );
		$scrollTo.on( 'click', function ( event ) {
			var $elemOffsetTop = $( this ).data( 'offset-top' );
			$( 'html' ).velocity( "scroll", {
				offset: $( this.hash ).offset().top - $elemOffsetTop,
				duration: 1000,
				easing: 'easeOutExpo',
				mobileHA: false
			} );
			event.preventDefault();
		} );


		// Toggle Mobile Navigation
		var $navToggle = $( '.nav-toggle', '.navbar' );
		$navToggle.on( 'click', function () {
			$( this ).toggleClass( 'active' ).parents( '.navbar' ).find( '.toolbar, .main-navigation, .mobile-socials' ).toggleClass( 'expanded' );
		} );

		// Mobile Submenu
		var $hasSubmenu = $( '.menu-item-has-children > a', '.main-navigation' );
		$hasSubmenu.on( 'click', function () {
			$( this ).parent().toggleClass( 'active' ).find( '.sub-menu, .mega-menu' ).toggleClass( 'expanded' );
		} );

		// Custom Checkboxes and Radios
		if ( $( 'input[type=checkbox], input[type=radio]' ).length ) {
			$( 'input' ).iCheck();
		}

		// Count Input
		$( document ).on( 'click', '.count-input .incr-btn', function ( e ) {
			e.preventDefault();

			var $button = $( this );
			var oldValue = $button.parent().find( '.quantity' ).val();
			var newVal;

			$button.parent().find( '.incr-btn[data-action="decrease"]' ).removeClass( 'inactive' );
			if ( $button.data( 'action' ) == "increase" ) {
				newVal = parseFloat( oldValue ) + 1;
			} else {
				// Don't allow decrementing below 1
				if ( oldValue > 1 ) {
					newVal = parseFloat( oldValue ) - 1;
				} else {
					newVal = 1;
					$button.addClass( 'inactive' );
				}
			}

			$button.parent().find( '.quantity' ).val( newVal );
			$button.parent().find( '.quantity' ).trigger( 'change' );
		} );

		// Tooltips
		var $tooltip = $( '[data-toggle="tooltip"]' );
		if ( $tooltip.length > 0 ) {
			$tooltip.tooltip();
		}

		// Tile Tabs Switching Class
		var $tileTab = $( '.tile-tabs .tab' );
		$tileTab.on( 'click', function () {
			$tileTab.removeClass( 'active' );
			$( this ).addClass( 'active' );
		} );

		// Domain Types Dropdown
		var $domainDropdown = $( '.domain-dropdown > span' ),
			$domainDropdownWrap = $( '.domain-dropdown' );
		$domainDropdown.on( 'click', function () {
			$( this ).parent().toggleClass( 'active' );
		} );
		$domainDropdownWrap.on( 'click', function ( e ) {
			e.stopPropagation();
		} );
		$( document ).on( 'click', function ( e ) {
			$domainDropdownWrap.removeClass( 'active' );
		} );

		// Shop Filters Dropdown
		var $shopDropdown = $( '.shop-filter-dropdown > span' ),
			$shopDropdownWrap = $( '.shop-filter-dropdown' );
		$shopDropdown.on( 'click', function () {
			$shopDropdown.parent().removeClass( 'active' );
			$( this ).parent().addClass( 'active' );
		} );
		$shopDropdownWrap.on( 'click', function ( e ) {
			e.stopPropagation();
		} );
		$( document ).on( 'click', function ( e ) {
			$shopDropdownWrap.removeClass( 'active' );
		} );

		// On window load functions
		$( window ).on( 'load', function () {

			// Isotope Grid
			var $grid = $( '.isotope-masonry-grid, .isotope-grid' );
			if ( $grid.length > 0 ) {
				$grid.isotope( {
					itemSelector: '.grid-item',
					transitionDuration: '0.7s',
					masonry: {
						columnWidth: '.grid-sizer',
						gutter: '.gutter-sizer'
					}
				} );
			}

			// Filtering
			if ( $( '.filter-grid' ).length > 0 ) {
				var $filterGrid = $( '.filter-grid' );
				$( '.nav-filters' ).on( 'click', 'a', function ( e ) {
					e.preventDefault();
					$( '.nav-filters li' ).removeClass( 'active' );
					$( this ).parent().addClass( 'active' );
					var $filterValue = $( this ).attr( 'data-filter' );
					$filterGrid.isotope( { filter: $filterValue } );
				} );
			}

			/** Background Parallax **/
			if ( ! Modernizr.touch && ! $( 'html.ie' ).length ) {
				if ( $( "body.parallax" ).length > 0 ) {
					$.stellar( {
						scrollProperty: 'scroll',
						verticalOffset: 0,
						horizontalOffset: 0,
						horizontalScrolling: false,
						responsive: true
					} );
					$grid.bind( "transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function () {
						$.stellar( 'refresh' );
					} );
				}
			}
		} );

		// Contacts Slider (Master Slider)
		if ( $( '#contact-slider' ).length ) {
			var contactSlider = new MasterSlider();

			contactSlider.control( 'arrows' );
			contactSlider.setup( 'contact-slider', {
				width: 1140,
				height: 480,
				space: 0,
				loop: true,
				view: 'basic',
				layout: 'partialview',
				filters: {
					opacity: 0.1
				}
			} );
		}

		// Conference Slider (Master Slider)
		if ( $( '#conference-slider' ).length ) {
			var conferSlider = new MasterSlider();

			conferSlider.control( 'arrows', { hideUnder: 800 } );
			conferSlider.control( "bullets", { autohide: false } );
			conferSlider.control( 'timebar', { color: 'rgba(255,255,255,.5)', align: 'top' } );

			conferSlider.setup( 'conference-slider', {
				width: 1920,
				height: 10,
				space: 0,
				layout: "fillwidth",
				autoHeight: true,
				loop: true,
				view: 'flow',
				autoplay: true
			} );
		}

		// Intro Page Slider (Master Slider)
		if ( $( '#intro-slider' ).length ) {
			var introSlider = new MasterSlider();

			introSlider.control( 'arrows', { hideUnder: 800 } );
			introSlider.control( "bullets", { autohide: false } );

			introSlider.setup( 'intro-slider', {
				width: 1920,
				height: 10,
				space: 0,
				layout: "fillwidth",
				autoHeight: true,
				loop: true,
				view: 'stack'
			} );
		}

		// Gallery Popup
		var $gallItem = $( '.gallery-item' );
		if ( $gallItem.length > 0 ) {
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

		/**
		 * Twitter share window
		 *
		 * @uses Twitter Web Intents
		 * @link https://dev.twitter.com/web/tweet-button/web-intent
		 */
		$( document ).on( 'click', '.nucleus-share-twitter', function ( e ) {
			e.preventDefault();
			var self = $( this ),
				query = {
					text: self.data( 'text' ),
					url: self.data( 'url' )
				};

			var uri = $.param( query );
			window.open( 'http://twitter.com/intent/tweet?' + uri, 'twitter', 'menubar=no,toolbar=no,resizable=yes,scrollbars=no,status=0,location=0,height=380,width=660' );
		} );

		/**
		 * Facebook share
		 *
		 * @link https://developers.google.com/+/web/share/#sharelink
		 */
		$( document ).on( 'click', '.nucleus-share-facebook', function ( e ) {
			e.preventDefault();
			var self = $( this ),
				query = { u: self.data( 'url' ) };

			var uri = $.param( query );
			window.open( 'https://www.facebook.com/sharer/sharer.php?' + uri, 'facebook', 'menubar=yes,toolbar=yes,resizable=yes,scrollbars=yes,height=600,width=600' );
		} );

		/**
		 * Google+ share
		 *
		 * @link https://developers.google.com/+/web/share/#sharelink
		 */
		$( document ).on( 'click', '.nucleus-share-google-plus', function ( e ) {
			e.preventDefault();
			var self = $( this ),
				query = { url: self.data( 'url' ) };

			var uri = $.param( query );
			window.open( 'https://plus.google.com/share?' + uri, 'googleplus', 'menubar=no,toolbar=no,resizable=yes,scrollbars=no,height=600,width=600' );
		} );

		/**
		 * Pinterest share
		 */
		$( document ).on( 'click', '.nucleus-share-pinterest', function ( e ) {
			e.preventDefault();
			var self = $( this ),
				query = {
					url: self.data( 'url' ),
					media: self.data( 'thumb' ),
					description: self.data( 'text' )
				};

			var uri = $.param( query );
			window.open( 'https://pinterest.com/pin/create/button/?' + uri, 'pinterest', 'menubar=no,toolbar=no,resizable=yes,scrollbars=no,height=600,width=600' );
		} );

		/**
		 * Shop sorting filter
		 *
		 * @see /shop/
		 * @see woocommerce/shop/filters.php
		 * @see http://icheck.fronteed.com/
		 */
		$( '.nucleus-wc-sorting' ).on( 'ifChanged', '[name="orderby"]', function ( e ) {
			$( this ).closest( 'form' ).submit();
		} );

	} );
})( jQuery );
