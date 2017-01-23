/**
 * Equip
 *
 * @author 8guild
 */

(function ( $ ) {
	'use strict';

	/**
	 * Equip
	 *
	 * @constructor
	 */
	function Equip() {
		this.media.init();
		this.socials.init();
		this.color.init();
		this.select.init();
		this.switch.init();
		this.icons.init();
		this.slider.init();
		this.imageselect.init();
		this.waves.init();
		this.stickyElement.init();
		this.tabbedNav.init();
		this.scrollSpy.init();
		this.dependencies.init();
		this.options.init();
	}

	/**
	 * Guild Utils Prototype
	 */
	Equip.prototype = {
		/**
		 * Work with WordPress Media Frame
		 */
		media: {
			/**
			 * Selector for "Add" button, which open the media frame
			 */
			addSelector: '.equip-media-add',

			/**
			 * Remove selector, for removing images from the set
			 */
			removeSelector: '.equip-media-remove',

			/**
			 * Remove class, used for template
			 */
			removeItemClass: 'equip-media-remove',

			/**
			 * Selector for the wrapper around "Add" button
			 */
			controlSelector: '.equip-media-control',

			/**
			 * Selector for hidden value input
			 */
			valueSelector: '.equip-media-value',

			/**
			 * Selector for main wrapper of current control
			 */
			wrapperSelector: '.equip-media-wrap',

			/**
			 * Selector for preview ul list
			 */
			itemsSelector: '.equip-media-items',

			/**
			 * Selector for each <li> inside the items preview list
			 */
			itemSelector: '.equip-media-item',

			/**
			 * A single <li> class, used in template
			 */
			itemClass: 'equip-media-item',

			/**
			 * A placeholder class while dragging the item
			 *
			 * @see https://jqueryui.com/sortable/#placeholder
			 * @see http://api.jqueryui.com/sortable/#option-placeholder
			 */
			highlightClass: 'equip-media-item-highlight',

			/**
			 * Main wrapper of current control
			 */
			$wrapper: null,

			/**
			 * The <li> wrapper around the control button
			 */
			$control: null,

			/**
			 * Value input field, keeps attachment ID(s)
			 */
			$value: null,

			/**
			 * Preview ul list element
			 */
			$items: null,

			/**
			 * Media frame itself
			 */
			mediaFrame: null,

			/**
			 * A collection of all sortable items
			 */
			$sortable: null,

			init: function () {
				var self = this;

				$( document ).on( 'click', self.addSelector, function ( e ) {
					var $this = $( this );
					self.openMediaFrame( e, $this );
				} );

				$( document ).on( 'click', self.removeSelector, function ( e ) {
					var $this = $( this );
					self.removeMedia( e, $this );
				} );

				self.$sortable = $( '[data-sortable=true] ' + self.itemsSelector );
				if ( self.$sortable.length ) {
					self.$sortable.sortable( {
						cursor: 'move',
						items: '> ' + self.itemSelector,
						placeholder: self.highlightClass
					} ).disableSelection();
				}

				self.$sortable.on( 'sortupdate', function ( e, ui ) {
					var $this = $( this );
					self.sortUpdate( e, ui, $this );
				} );
			},

			/**
			 * Add new media
			 *
			 * @param e
			 * @param $this
			 */
			openMediaFrame: function ( e, $this ) {
				var self = this;

				e.preventDefault();

				self.$control = $this.parent( self.controlSelector );
				self.$wrapper = self.$control.parents( self.wrapperSelector );
				self.$value = self.$wrapper.find( self.valueSelector );
				self.$items = self.$wrapper.find( self.itemsSelector );

				// Media Library Configuration
				// Control for adding images is inside preview
				var params = $this.data(),
					values = self.$value.val(),
					is_multiple = Boolean( params.multiple || 0 );

				// Store globally
				self.mediaFrame = wp.media( {
					title: params.title,
					multiple: is_multiple,
					button: { text: params.button }
				} );

				self.mediaFrame.on( 'select', function () {
					var IDs;
					var attachments = self.mediaFrame.state().get( 'selection' ).toJSON();

					if ( is_multiple ) {
						IDs = self.handleMultipleImages( values, attachments );
					} else {
						IDs = self.handleSingleImage( attachments );
					}

					// Add IDs to hidden field
					self.$value.val( IDs );

					// trigger the change event
					self.$value.trigger('change');
				} );

				self.mediaFrame.open();
			},

			/**
			 * Remove selected media.
			 * Remove <li> element and remove attachment ID from hidden input.
			 *
			 * @param e
			 * @param $this
			 */
			removeMedia: function ( e, $this ) {
				var self = this;
				e.preventDefault();

				var attachmentID = $this.parents( self.itemSelector ).data( 'id' ).toString(),
					$li = $this.parent( 'li' ),
					$wrapper = $li.parents( self.wrapperSelector ),
					$value = $wrapper.find( self.valueSelector );

				// Delete <li>
				$li.remove();

				// Remove ID from hidden field
				// This should work normally both for single and multiple images
				var IDs = $value.val().split( ',' );
				var position = IDs.indexOf( attachmentID );
				if ( ~ position ) {
					IDs.splice( position, 1 );
				}

				// Save new value
				$value.val( IDs.join( ',' ) );

				// trigger the change event
				self.$value.trigger('change');
			},

			/**
			 * Update the values when user sort the images
			 *
			 * @param {Event} e
			 * @param {Object} ui
			 * @param {jQuery} $this
			 */
			sortUpdate: function ( e, ui, $this ) {
				var self = this;

				var $value = $this.siblings( self.valueSelector ),
					IDs = self.$sortable.sortable( 'toArray', { attribute: 'data-id' } );

				$value.val( IDs.join( ',' ) )
			},

			/**
			 * Select a single image
			 *
			 * @param attachments
			 * @returns {string}
			 */
			handleSingleImage: function ( attachments ) {
				var self = this;

				var IDs = [],
					previewHTML = '';

				// Prepare preview
				$.each( attachments, function ( key, attachment ) {
					IDs.push( attachment.id );
					previewHTML += self.preparePreview( attachment );
				} );

				// Prepend new li before "add" controller or
				// replace the first <li> under the preview ul with a new one
				var LIs = self.$items.find( 'li' );
				if ( LIs.length > 1 ) {
					LIs.first().replaceWith( previewHTML );
				} else {
					self.$items.prepend( previewHTML );
				}

				return IDs.join( '' );
			},

			/**
			 * Showing up multiple images
			 *
			 * @param {string} values List of previously selected IDs
			 * @param {object} attachments List of selected attachments by user as object.
			 *
			 * @returns {string} List of attachment IDs, separated by comma.
			 */
			handleMultipleImages: function ( values, attachments ) {
				var self = this;
				var IDs = values || [],
					previewHTML = '';

				if ( 'string' === typeof IDs ) {
					IDs = IDs.split( ',' );
				}

				// Prepare preview
				$.each( attachments, function ( key, attachment ) {
					IDs.push( attachment.id );
					previewHTML += self.preparePreview( attachment );
				} );

				// Display added images right before the add control,
				// because add control is a <li>, too
				self.$control.before( previewHTML );

				return IDs.join( ',' );
			},

			/**
			 * Prepare the single item preview HTML
			 *
			 * @param attachment
			 * @returns {string} Single item preview HTML
			 */
			preparePreview: function ( attachment ) {
				var self = this;
				//noinspection CssUnknownTarget
				var tpl = '<li class="{{item}}" data-id="{{id}}" style="background-image: url({{src}});">'
					+ '<a href="#" class="{{remove}}">&times;</a>'
					+ '</li>';

				return tpl
					.replace( '{{item}}', self.itemClass )
					.replace( '{{id}}', attachment.id )
					.replace( '{{src}}', attachment.url )
					.replace( '{{remove}}', self.removeItemClass );
			}

		},

		/**
		 * Socials control
		 */
		socials: {
			/**
			 * Selector for "Add" button
			 */
			addSelector: '.equip-socials-add',

			/**
			 * Wrapper for all groups
			 */
			wrapperSelector: '.equip-socials-wrap',

			/**
			 * Wrapper around the single social network group of controls:
			 * select with list of networks, input for URL and remove button.
			 */
			groupSelector: '.equip-socials-group',

			/**
			 * Selector for removing the single group of fields
			 */
			removeSelector: '.equip-socials-group-remove',

			init: function () {
				var self = this;

				$( document ).on( 'click', self.addSelector, function ( e ) {
					var $this = $( this );
					self.addField( e, $this );
				} );

				$( document ).on( 'click', self.removeSelector, function ( e ) {
					var $this = $( this );
					self.removeField( e, $this );
				} );

			},

			/**
			 * Handler for adding new field for a new social network.
			 * Fires when user want to add one more field and click "Add" link.
			 *
			 * @param e
			 * @param $this
			 */
			addField: function ( e, $this ) {
				var self = this;

				e.preventDefault();

				// Detect the wrapper, based on clicked button
				// May be some buttons per one page
				var $wrapper = $this.siblings( self.wrapperSelector );
				// Clone first element
				var $item = $wrapper.find( self.groupSelector ).first().clone();

				// Append this item to container and clear the <input> field
				$item.appendTo( $wrapper ).children( 'input:text' ).val( '' );
			},

			/**
			 * Remove the single control
			 *
			 * Before removing do not forget to check if we have more than one group.
			 * Do not allow to remove the last element, because user won't be able to
			 * add new controls.
			 *
			 * @param e
			 * @param $this
			 */
			removeField: function ( e, $this ) {
				var self = this;

				e.preventDefault();

				// Find wrapper and groups to check if it possible to remove element
				var $wrapper = $this.parents( self.wrapperSelector );
				var $groups = $wrapper.find( self.groupSelector );
				// Find group which user want to remove..
				var $group = $this.parent( self.groupSelector );
				if ( $groups.length > 1 ) {
					// ..and remove it
					$group.remove();
				} else {
					// Do not remove last element, just reset values
					var selectField = $group.find( 'select' );
					var firstOptionsValue = $group.find( 'select option:first' ).val();

					selectField.val( firstOptionsValue );
					$group.find( 'input' ).val( '' );
				}
			}
		},

		/**
		 * Select an icon
		 */
		icons: {
			/**
			 * IconPicker selector.
			 * The fontIconPicker will be initialized on this element.
			 */
			iconPickerSelector: '.equip-iconpicker',

			init: function () {
				var self = this;
				var $pickers = $( self.iconPickerSelector );

				$.each( $pickers, function ( index, picker ) {
					// convert to jQuery object
					var $picker = $( picker );
					var settings = $picker.data( 'settings' ) || {};

					$picker.fontIconPicker( settings );
				} );
			}
		},

		/**
		 * Select field
		 */
		select: {
			selector: '.equip-field > select',

			init: function () {
				var self = this;

				if ( $( self.selector ).length > 0 && typeof $.fn.select2 === 'function' ) {
					$( this.selector ).each( function () {
						var $item = $( this ),
							searchable = $item.data( 'searchable' ),
							placeholder = ( $item.data( 'placeholder' ) === undefined ) ? '' : $item.data( 'placeholder' ),
							isSearchable = ( searchable === false || searchable === undefined ) ? Infinity : 1;

						$item.select2( {
							minimumResultsForSearch: isSearchable,
							placeholder: placeholder
						} );
					} );
				}
			}
		},

		/**
		 * Switch field
		 */
		switch: {
			selector: '.equip-field .equip-switch',

			init: function () {

				$( document ).on( 'click', this.selector, function() {
					if ( !$( this ).hasClass( 'disabled' ) ) {

					  var clicks = $(this).data('clicks'),
					  		inputVal = $(this).find('input').attr('value');

						if (clicks && inputVal === '0') {
							$(this).find('input').attr('value', '1');
							$(this).addClass('on');
						} else if (clicks && inputVal === '1') {
							$(this).find('input').attr('value', '0');
							$(this).removeClass('on');
						} else if (!clicks && inputVal === '0') {
							$(this).find('input').attr('value', '1');
							$( this ).addClass( 'on' );
						} else if ( !clicks && inputVal === '1' ) {
							$( this ).find( 'input' ).attr( 'value', '0' );
							$( this ).removeClass( 'on' );
						}

						$( this ).data( 'clicks', !clicks );

						$(this).trigger('change');
					}

				} );

			}
		},

		/**
		 * Color picker
		 */
		color: {
			selector: '.equip-color',

			init: function () {
				var self = this;

				if ( typeof $.fn.wpColorPicker === 'function' ) {

					//alert($( self.selector + ':before' ).css('border'));
					$( self.selector ).wpColorPicker({
						change: function(event, ui) {
			        $(this).parents('.equip-color-field').find('.equip-color-result').text(ui.color.toString());
				    }
					});
				}
			}
		},

		/**
		 * Slider
		 */
		slider: {
			sliderSelector: '.equip-slider',

			$sliders: [],

			init: function() {
				var self = this;

				self.$sliders = $( self.sliderSelector );

				if ( self.$sliders.length === 0 && typeof window.noUiSlider === "undefined" ) {
					return false;
				}

				for ( var i = 0; i < self.$sliders.length; i++ ) {
					self.createSlider(i);
				}
			},

			createSlider: function ( index ) {
				var self = this;

				var minVal = parseInt( self.$sliders[index].dataset.min, 10 ),
					maxVal = parseInt( self.$sliders[index].dataset.max, 10 ),
					currentVal = parseInt( self.$sliders[index].dataset.current, 10 ),
					stepVal = parseInt( self.$sliders[index].dataset.step, 10 ),
					sliderID = self.$sliders[index].dataset.id;

				var slider = noUiSlider.create( self.$sliders[index], {
					start: currentVal,
					connect: "lower",
					step: stepVal,
					range: {
						'min': minVal,
						'max': maxVal
					},
					format: wNumb( {
						decimals: 0
					} )
				} );

				// add the slider object to the slider element
				// using data for future reference
				$( self.$sliders[index] ).data( 'slider', slider );

				// Update input value
				slider.on( 'update', function ( values, handle ) {
					$( '#' + sliderID ).val( values[handle] );
				} );

				// Update slider position from input value
				$( '#' + sliderID ).on( 'change', function () {
					$( self.$sliders[index] ).data( 'slider' ).set( this.value );
				} );
			}
		},

		/**
		 * Image Select
		 */
		imageselect: {
			selector: '.equip-image-select',

			init: function () {

				var imageselect = this.selector;

				$( document ).on( 'click', imageselect, function() {
					var value = $( this ).data( 'value' );
					$( this ).parent().find( imageselect ).removeClass( 'active' );
					$( this ).addClass( 'active' );
					$( this ).parent().find( 'input[type=hidden]' ).val( value );
				} );
			}
		},

		/**
		 * Waves Effect
		 */
		waves: {
			selector: '.waves-effect',

			init: function () {

				var waves = this.selector;

				if ( waves.length > 0 ) {
					Waves.displayEffect( { duration: 600 } );
				}
			}
		},

		/**
		 * Sticky Elements Footer + Side navigation
		 */
		stickyElement: {
			footerSelector: '.equip-footer',
			naviSelector: 	'.equip-navi',
			parentSelector: '.equip-page',

			init: function () {
				var self = this,
					resizeElement = document.querySelector( self.parentSelector );

				if ( $( self.parentSelector ).length === 0 ) {
					return false;
				}

				if ( $( self.footerSelector ).length > 0 && typeof $.fn.waypoint === 'function' ) {
					$( self.parentSelector ).waypoint(function(direction){
						if(direction === 'down') {
							$( self.footerSelector ).removeClass('stuck');
						} else {
							$( self.footerSelector ).addClass('stuck');
						}
					},{ offset: 'bottom-in-view' } );
				}

				var $navi = $( self.naviSelector );
				if ( $navi.length > 0 ) {
					var stickyNavi = new Waypoint.Sticky( {
						element: $navi[0]
					} );
				}

				self.footerWidth();
				addResizeListener(resizeElement, self.waypointRefresh);
			},

			footerWidth: function () {
				var $window = $( window ),
					$page = $( '.equip-page' ),
					$footer = $( this.footerSelector ).find( '.footer-inner' );

				$footer.width( $page.width() );
				$window.on( 'resize', function () {
					$footer.width( $page.width() );
				} );
			},

			waypointRefresh: function () {
				Waypoint.refreshAll();
			}
		},

		/**
		 * Tabbed Navigation
		 */
		 tabbedNav: {
			 selector: '.nav-tabs li a',

			 init: function () {
				 var self = this;
				 var firstTab = $($( self.selector ).parent()[0]).find('a').attr('href');
				 var lastTab = localStorage.getItem('lastTab') || firstTab;
				 $('.tab-pane').removeClass('active in');
				 $( self.selector ).parent().removeClass('active');
				 $( self.selector + '[href="' + lastTab + '"]').parent().addClass('active');
				 $(lastTab).addClass('active in');
				 $( self.selector ).on('click', function () {
					 var tabID = $(this).attr('href');
					 localStorage.setItem('lastTab', tabID);
				 });
			 }
		 },

		/**
		 * Anchor Navigation + Scroll spy
		 */
		scrollSpy: {
			selector: '.scrollspy',

			init: function () {
				var self = this;
				$( self.selector ).scrollSpy();
			}
		},

		/**
		 * Fields dependencies
		 */
		dependencies: {

			/**
			 * Dependent field selector
			 */
			dependentsSelector: '.equip-field[data-dependent="true"]',

			/**
			 * Field selector
			 */
			fieldSelector: '.equip-field[data-key="{{key}}"]',

			/**
			 * Element container selector
			 */
			containerSelector: '.equip-container',

			init: function () {
				var self = this;

				$( document ).on( 'change', self.containerSelector, function ( e ) {
					var $this = $( this );
					self.hookUp( e, $this );
				} );
			},

			/**
			 * Triggers when .equip-container is changed
			 *
			 * @param e
			 * @param $container
			 * @returns {boolean}
			 */
			hookUp: function ( e, $container ) {
				var self = this;

				// collect the dependent fields within current container
				var $fields = $container.find( self.dependentsSelector );
				if ( $fields.length === 0 ) {
					return false;
				}

				// collect master fields
				// and check if current e.target is a master field
				var $masters = self.getMasters( $fields, $container );
				var $target = $( e.target );
				var $master = $target.parents( '.equip-field' ); // master is a service wrapper
				if ( ! self.isMaster( $master, $masters ) ) {
					return false;
				}

				// collect all [dependent => [master]] relations
				// and dependent fields in appropriate format
				var $relations = self.getRelations( $fields );
				var $dependents = self.getDependents( $fields );

				// master field's data
				var master = $master.data( 'key' ); // master field key
				var value = self.getMasterValue( $master );

				// get dependents assigned to current $master
				var dependents = self.getDependent( $dependents, master );
				$.each( dependents, function ( i, item ) {
					// where
					// item is a dependent object from $dependents
					// dependent is a current dependent field key
					var dependent = item.key;
					var result = self.compare( value, item.operator, item.value );

					console.log( [ 'equip.compare.' + master + '.vs.' + dependent, value, item.operator, item.value, result, 'outer', $container ] );

					if ( result ) {
						// check if current item depends on other masters
						var relations = self.getRelation( $relations, dependent, master );
						if ( relations.length == 0 ) {
							// this item don't depends on other masters, just show it
							self.showDependent( item.field );
						} else {
							// var success will contain the result of comparisons with other masters
							// if at least one master fails item won't show up
							var success = false;
							$.each( relations, function( i, m ) {
								// where
								// m is another master key
								// v is another master value
								// d is a dependent object of current item attached to another master
								// r is a result of comparison with other master
								var v = self.getMasterValue( m, $masters );
								var d = self.getDependent( $dependents, m, dependent );
								var r = self.compare( v, d.operator, d.value );

								console.log( [ 'equip.compare.' + m + '.vs.' + dependent, v, d.operator, d.value, r, 'nested', $container ] );

								if ( r ) {
									success = true;
								} else {
									success = false;

									// no need to check other masters
									// exit from $.each
									return false;
								}
							} );

							if ( success ) {
								self.showDependent( item.field );
							}
						}
					} else {
						self.hideDependent( item.field );
					}
				} );
			},

			/**
			 * Check if current target is a master field
			 *
			 * The problem is that target is a control inside the
			 * service wrapper .equip-field, which is required
			 *
			 * @param {jQuery} target Maybe a master
			 * @param {Array} masters
			 * @returns {boolean}
			 */
			isMaster: function ( target, masters ) {
				var result = $.grep( masters, function( master ) {
					return target.is( master.object );
				} );

				return result.length > 0;
			},

			/**
			 * Parse masters collection and return specified master object
			 *
			 * @param master
			 * @param masters
			 * @returns {*|string|string}
			 */
			getMaster: function( master, masters ) {
				var filtered = $.grep( masters, function ( m ) {
					return master === m.key;
				} );

				return filtered[0].object;
			},

			/**
			 * Collect master fields within current container
			 *
			 * Container is required to limit the search scope
			 * for master fields
			 *
			 * Return the array in format
			 * [ {key: "master", object: "jquery object"}, ...]
			 *
			 * @param {jQuery} fields
			 * @param {jQuery} $container
			 * @returns {Array}
			 */
			getMasters: function ( fields, $container ) {
				var self = this;

				var _masters = [];
				$.each( fields, function ( i, field ) {
					var $field = $( field );
					var required = $field.data( 'required' );

					if ( $.isArray( required[ 0 ] ) ) {
						// nested dependencies
						$.each( required, function ( index, nested ) {
							var master = nested[ 0 ];
							var selector = self.fieldSelector.replace( '{{key}}', master );

							_masters.push( {
								'key': master,
								'object': $container.find( selector )
							} );
						} );
					} else {
						var master = required[ 0 ]; // master key;
						var selector = self.fieldSelector.replace( '{{key}}', master );

						_masters.push( {
							'key': master,
							'object': $container.find( selector )
						} );
					}
				} );

				// filter masters for unique values
				var masters = [];
				var keys = [];
				$.map( _masters, function ( m, i ) {
					if ( $.inArray( m.key, keys ) == -1 ) {
						masters.push( m );
						keys.push( m.key );
					}
				} );

				return masters;
			},

			/**
			 * Get all masters' keys, attached to a dependent field
			 * Specifying the second parameter you can remove this key from a result set
			 *
			 * @param {Array} relations An array of relations within current container
			 * @param {string} dependent
			 * @param {string} except
			 * @returns {*}
			 */
			getRelation: function( relations, dependent, except ) {
				var self = this;

				relations = relations[ dependent ];

				if ( typeof except !== 'undefined' ) {
					relations = $.grep( relations, function( r ) {
						return except != r;
					} );
				}

				return relations;
			},

			/**
			 * Collect relations [dependent => [masters]]
			 * within current element container
			 *
			 * @param {jQuery} $fields
			 * @returns {Array}
			 */
			getRelations: function( $fields ) {
				var self = this;
				var relations = [];

				$.each( $fields, function ( i, field ) {
					var $field = $( field );
					var dependent = $field.data( 'key' ); // dependent key
					var required = $field.data( 'required' );

					if ( ! $.isArray( relations[ dependent ] ) ) {
						relations[ dependent ] = [];
					}

					if ( $.isArray( required[ 0 ] ) ) {
						// nested dependencies
						$.each( required, function ( index, nested ) {
							var master = nested[ 0 ];
							relations[ dependent ].push( master );
						} );
					} else {
						var master = required[ 0 ]; // master field key
						relations[ dependent ].push( master );
					}
				} );

				return relations;
			},

			/**
			 * Return a dependents object, attached to a master
			 *
			 * If second argument is specified function will return the single object
			 * of current dependent attached to a provided master
			 *
			 * @param {Array} dependents A list of all dependent fields within current container
			 * @param {String} master Master key
			 * @param exact Dependent field key
			 * @returns {*}
			 */
			getDependent: function ( dependents, master, exact ) {
				var self = this;

				if ( typeof exact !== 'undefined' ) {
					dependents = $.grep( dependents, function( d ) {
						return master === d.master && exact === d.key;
					} );

					return dependents[0];
				}

				return $.grep( dependents, function ( d ) {
					return master == d.master;
				} );
			},

			/**
			 * Collect dependent fields within
			 * current element container
			 *
			 * @param {Array} fields
			 * @returns {Array}
			 */
			getDependents: function ( fields ) {
				var self = this;

				var dependents = [];
				$.each( fields, function ( i, field ) {
					var $field = $( field );
					var	dependent = $field.data( 'key' ); // dependent key
					var required = $field.data( 'required' );

					if ( $.isArray( required[ 0 ] ) ) {
						// nested dependencies
						$.each( required, function ( index, nested ) {
							var master = nested[ 0 ];
							var operator = nested[ 1 ];
							var value = nested[ 2 ] || false;

							dependents.push( {
								field: $field,
								operator: operator,
								value: value,
								key: dependent,
								master: master
							} );

						} );
					} else {
						var master = required[ 0 ]; // master key
						var operator = required[ 1 ];
						var value = required[ 2 ];

						dependents.push( {
							field: $field,
							operator: operator,
							value: value,
							key: dependent,
							master: master
						} );
					}
				} );

				return dependents;
			},

			/**
			 * Returns the value of master field by provided master key
			 * or jQuery Object containing the master field
			 *
			 * @param {jQuery|string} master
			 * @param masters
			 * @returns {*}
			 */
			getMasterValue: function( master, masters ) {
				var self = this;
				var $master, $control, value;

				if ( typeof master === 'string' && 'undefined' !== typeof masters ) {
					$master = self.getMaster( master, masters );
				} else if ( master instanceof $ ) {
					$master = master;
				} else {
					return false;
				}

				$control = $master.find( ':input:enabled' );
				value = $control.val();

				return value;
			},

			hideDependent: function( $field ) {
				$field.addClass( 'hidden' );
			},

			showDependent: function( $field ) {
				$field.removeClass( 'hidden' );
			},

			/**
			 * Test the dependent with master value by given operator
			 *
			 * @param masterValue
			 * @param operator
			 * @param compareValue
			 *
			 * @returns {boolean}
			 */
			compare: function ( masterValue, operator, compareValue ) {
				var result;

				switch ( operator ) {
					case '!=':
					case 'ne':
						result = ( masterValue != compareValue );
						break;

					case '>':
					case 'gt':
					case 'greater':
						result = ( masterValue > compareValue );
						break;

					case '>=':
					case 'ge':
					case 'greater_equal':
						result = ( masterValue >= compareValue );
						break;

					case '<':
					case 'lt':
					case 'less':
						result = ( masterValue < compareValue );
						break;

					case '<=':
					case 'le':
					case 'less_equal':
						result = ( masterValue <= compareValue );
						break;

					case 'in_array':
						if ( $.isArray( compareValue ) ) {
							// convert all values to string
							var s = compareValue.map( function ( v ) {
								return String( v );
							} );

							result = ( $.inArray( masterValue, s ) > -1 );
						} else {
							result = false;
						}

						break;

					case 'empty':
						result = masterValue.length == 0;
						break;

					case 'not_empty':
						result = masterValue.length > 0;
						break;

					case '=':
					case 'eq':
					case 'equal':
					case 'equals':
					default:
						result = ( masterValue == compareValue );
						break;
				}

				return result;
			}
		},

		options: {

			/**
			 * Form selector for "Save Options"
			 */
			formSelector: '.equip-options-form',

			/**
			 * "Reset All" button selector
			 */
			resetAllSelector: '#equip-options-reset',

			/**
			 * "Reset Section" button selector
			 */
			resetSectionSelector: '#equip-options-reset-section',

			/**
			 * Toast defaults arguments
			 */
			toastDefaults: {
				loader: false,
				position: 'top-right'
			},

			init: function () {
				var self = this;

				$( document ).on( 'submit', self.formSelector, function ( e ) {
					var $form = $( this );
					self.save( e, $form );
				} );

				$( document ).on( 'click', self.resetAllSelector, function ( e ) {
					var $button = $( this );
					self.resetAll( e, $button );
				} );

				$( document ).on( 'click', self.resetSectionSelector, function ( e ) {
					var $button = $( this );
					self.resetSection( e, $button );
				} );
			},

			save: function ( e, $form ) {
				e.preventDefault();

				var self = this;
				var formdata = $form.serializeArray();

				$.post( ajaxurl, formdata ).done( function ( response ) {
					if ( response.success ) {
						self.toast( {
							heading: equip.messages.optionsSaved,
							text: equip.messages.youAreAwesome,
							icon: 'success'
						} );
					} else {
						self.toast( {
							heading: equip.messages.error,
							text: response.data,
							icon: 'error'
						} );
					}
				} ).fail( self.fail );
			},

			resetAll: function( e, $button ) {
				var self = this;

				e.preventDefault();
				var slug = $( '#equip-slug' ).val();
				var nonce = $( '#equip_reset_nonce' ).val();

				var formdata = {
					action: 'equip_reset_options',
					slug: slug,
					nonce: nonce,
					reset: 'all'
				};

				$.post( ajaxurl, formdata ).done( function ( response ) {
					if ( response.success ) {
						self.toast( {
							text: equip.messages.optionsReset,
							icon: 'warning'
						} );

						self.reload();
					} else {
						self.toast( {
							heading: equip.messages.error,
							text: response.data,
							icon: 'error'
						} );
					}
				} ).fail( self.fail );
			},

			resetSection: function( e, $button ) {
				var self = this;

				e.preventDefault();
				var slug = $( '#equip-slug' ).val();
				var nonce = $( '#equip_reset_nonce' ).val();

				// find the active section and collect all field keys
				var keys = [];
				var active = $( '.active[data-element="section"]' );
				var fields = active.find( '[data-element="field"][data-key]' );
				fields.each( function( i, field ) {
					keys.push( $( field ).data( 'key' ) );
				} );

				var formdata = {
					action: 'equip_reset_options',
					slug: slug,
					nonce: nonce,
					reset: 'section',
					keys: keys
				};

				$.post( ajaxurl, formdata ).done( function ( response ) {
					if ( response.success ) {
						self.toast( {
							text: equip.messages.sectionReset,
							icon: 'warning'
						} );

						self.reload();
					} else {
						self.toast( {
							heading: equip.messages.error,
							text: response.data,
							icon: 'error'
						} );
					}
				} ).fail( self.fail );
			},

			fail: function ( xhr, status, error ) {
				console.log( [ 'equip.options.error', status, error, xhr, xhr.responseText ] );
				$.toast( {
					heading: equip.messages.error,
					text: equip.messages.fail,
					icon: 'error',
					loader: false,
					position: { top: 60, right: 30 }
				} );
			},

			toast: function( options ) {
				var self = this;
				var settings = $.extend( self.toastDefaults, options );

				$.toast( settings );
			},

			reload: function () {
				setTimeout( function () {
					location.reload();
				}, 1000 );
			}
		}
	};

	/**
	 * Initialize the Equip
	 */
	$( document ).ready( function () {
		// init the Equip
		new Equip();
	} );

})( jQuery );
