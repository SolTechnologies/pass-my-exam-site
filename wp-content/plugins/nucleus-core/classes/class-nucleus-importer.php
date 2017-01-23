<?php

/**
 * "One click" demo install
 *
 * @author  8nucleus
 * @package Nucleus\Import
 */
class Nucleus_Importer {

	/**#@+
	 * WordPress page and menu settings
	 */
	private $page_title;
	private $menu_title;
	private $menu_slug;
	/**#@-*/

	/**
	 * List of import variants. Should be an array or arrays,
	 * where key will be used as option for import process.
	 *
	 * Possible keys:
	 * - preview
	 * - title
	 *
	 * @example
	 * 'variants' => array(
	 *   'preview1' => array(
	 *     'preview' => plugins_url( 'assets/img/preview.png', __FILE__ ),
	 *     'title'   => __( 'Preview 1', 'textdomain' ),
	 *     'xml'     => $template_dir . '/demo/preview1/demo.xml',
	 *     'extra'   => $template_dir . '/demo/preview1/extra.json',
	 *   ),
	 * )
	 *
	 * @var array
	 */
	private $variants;

	/**#@+
	 * Nonce name and POST field name
	 */
	private $nonce;
	private $nonce_field;
	/**#@-*/

	/**#@+
	 * Import options
	 */
	private $import_id = 0;
	private $import_attachments = true;
	/**#@-*/


	/**
	 * Nucleus_Importer constructor.
	 *
	 * @param array $settings Import settings
	 */
	public function __construct( $settings = array() ) {
		if ( empty( $settings ) ) {
			return;
		}

		foreach ( (array) $settings as $option => $value ) {
			$this->$option = $value;
		}

		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
		if ( is_admin() ) {
			add_action( 'wp_ajax_nucleus_import', array( $this, 'do_import' ) );
		}
	}

	/**
	 * Add sub menu item under the Appearance menu
	 */
	public function add_menu_item() {
		add_theme_page( $this->page_title, $this->menu_title, 'import', $this->menu_slug,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the import page content
	 */
	public function render_page() {
		?>
		<style type="text/css">
			.nucleus-unordered-list {
				list-style: disc;
				padding-left: 20px;
			}

			.nucleus-unordered-list ul {
				list-style: circle;
				padding-left: 20px;
				padding-top: 15px;
				padding-bottom: 15px;
			}

			.nucleus-info {
				padding: 15px 20px;
				margin-top: 10px;
				margin-bottom: 20px;
				background-color: #fff;
				border-left: 4px solid #0073aa;
			}

			.nucleus-alert {
				border-left-color: #d54e21;
			}

			.nucleus-success {
				border-left-color: #7ad03a;
			}

			.nucleus-button {
				padding: 10px 26px;
				color: #fff;
				background-color: #0073aa;
				border: none;
				cursor: pointer;
				border-radius: 6px;
				text-transform: uppercase;
			}

			.nucleus-button[disabled] {
				background-color: #ccc;
				cursor: not-allowed;
			}

			.nucleus-text-info {
				color: #0073aa;
			}

			.nucleus-text-success {
				color: #7ad03a;
			}

			.nucleus-text-alert {
				color: #d54e21;
			}

			.nucleus-loading {
				position: fixed;
				z-index: 9999;
				width: 100%;
				height: 100%;
				top: 0;
				left: 0;
				background-color: rgba(0, 0, 0, .8);
				opacity: 0;
				visibility: hidden;
				-webkit-transition: all .35s;
				-moz-transition: all .35s;
				-ms-transition: all .35s;
				-o-transition: all .35s;
				transition: all .35s;
			}

			.nucleus-loading.nucleus-show {
				opacity: 1;
				visibility: visible;
			}

			.nucleus-loading img {
				position: absolute;
				top: 50%;
				left: 50%;
				margin: -32px 0 0 -32px;
			}

			.nucleus-previews > li {
				display: inline-block;
				text-align: center;
				font-weight: bold;
				margin-right: 10px;
				border: 3px solid transparent;
			}

			.nucleus-previews > li img {
				border: 3px solid transparent;
			}

			.nucleus-previews > li.active img {
				border: 3px solid #0073aa;
			}

			.nucleus-previews input[type=radio] {
				display: none;
			}

		</style>
		<div class="wrap">
			<div class="nucleus-loading">
				<img
					src="data:image/gif;base64,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">
			</div>

			<h1><?php esc_html_e( 'Welcome to One Click Demo Import', 'nucleus' ); ?></h1>
			<div class="notice is-dismissible nucleus-response-holder" hidden>
				<div></div>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text">&times;</span></button>
			</div>

			<div class="nucleus-info">
				<h4><?php esc_html_e( 'Importing demo data allows you to setup your theme fast and easily without the need to create everything from scratch.', 'nucleus' ); ?></h4>
				<h4><?php esc_html_e( 'When you import the data following things will happen:', 'nucleus' ); ?></h4>
				<ul class="nucleus-unordered-list">
					<li><?php esc_html_e( 'Your existing content will NOT be deleted or modified.', 'nucleus' ); ?></li>
					<li>
						<?php esc_html_e( 'All demo content will be imported including:', 'nucleus' ); ?>
						<ul>
							<li>Posts</li>
							<li>Pages</li>
							<li>Custom Post Types</li>
							<li>Taxonomies (Tags and Categories), including Custom Taxonomies</li>
							<li>Menus</li>
							<li>Media</li>
							<li>Widgets</li>
							<li>Layouts and Shortcodes</li>
							<li>User Extra Settings</li>
							<li>Theme Options</li>
							<li>Some WordPress Settings</li>
						</ul>
					</li>
				</ul>
			</div>

			<div class="nucleus-info nucleus-alert">
				<strong class="nucleus-text-alert"><?php esc_html_e( 'Please note:', 'nucleus' ); ?></strong>
				<ul class="nucleus-unordered-list">
					<li class="nucleus-text-alert"><?php esc_html_e( 'Real photos used in our live demo will not be imported due to copyright / license restrictions. Placeholders will be used instead.', 'nucleus' ); ?></li>
					<li class="nucleus-text-alert"><?php esc_html_e( 'All existing widgets will be overwritten.', 'nucleus' ); ?></li>
					<li class="nucleus-text-alert"><?php esc_html_e( 'Instagram widget also will be omitted due to private API keys settings.', 'nucleus' ); ?></li>
					<li class="nucleus-text-alert">
						<strong><?php esc_html_e( 'We recommend to run this importer on clean WordPress installation.', 'nucleus' ); ?></strong>
						<?php esc_html_e( 'You can use ', 'nucleus' ); ?>
						<a href="https://srd.wordpress.org/plugins/wordpress-reset/"
						   target="_blank"><?php esc_html_e( 'WordPress Reset', 'nucleus' ); ?></a>
						<?php esc_html_e( 'plugin to quickly reset your WordPress installation.', 'nucleus' ); ?>
					</li>
				</ul>
				<?php // TODO: add action for this section: "nucleus_import_page_after_notes" ?>
			</div>

			<div class="nucleus-info nucleus-success">
				<strong>
					<?php esc_html_e( 'Importing process can take 1-5 minutes. Please be patient and do not close this page until importing is finished.', 'nucleus' ); ?>
				</strong>
			</div>

			<?php
			/**
			 * Do some action before import form will be rendered
			 */
			do_action( 'nucleus_import_page_before_form' );
			?>

			<form method="POST" id="nucleus-do-import" action>
				<?php
				wp_nonce_field( $this->nonce, $this->nonce_field );

				// check if passed a single variant to the importer
				if ( count( $this->variants ) > 1 ) {
					$i       = 0;
					echo '<ul class="nucleus-previews">';
					foreach ( $this->variants as $key => $variant ) :
						$is_first = ( 0 === $i );
						$key = esc_attr( $key );

						?>
					<li <?php if ( $is_first ) : echo 'class="active"'; endif; ?>>
						<label for="nucleus-<?php echo $key; ?>-preview">
							<?php
							printf( '<input type="radio" name="variant" value="%1$s" id="%2$s" %3$s>',
								$key,
								'nucleus-' . $key . '-preview',
								checked( $is_first, true, false )
							);
							?>

							<img src="<?php echo esc_url( $variant['preview'] ); ?>">
						</label>
						<p><?php echo esc_html( $variant['title'] ); ?></p>
						</li><?php

						$i ++;
					endforeach;
					echo '</ul>';
				} else {
					$variants = array_keys( $this->variants );
					printf( '<input type="hidden" name="variant" value="%s">',
						esc_attr( reset( $variants ) )
					);
				}

				?>

				<button type="submit" class="nucleus-button" name="nucleus_do_import"
				        value="1"><?php esc_html_e( 'Import', 'nucleus' ); ?></button>
			</form>

			<?php
			/**
			 * Do some action when import form already rendered
			 */
			do_action( 'nucleus_import_page_after_form' );
			?>

		</div>
		<script type="text/javascript">
			(
				function ( $ ) {
					'use strict';

					/**
					 * Switch active preview
					 */
					$( document ).on( 'click', '.nucleus-previews > li', function ( e ) {
						$( '.nucleus-previews > li' ).removeClass( 'active' );
						$( this ).addClass( 'active' );
					} );

					function nucleusShowLoader() {
						var loader = $( '.nucleus-loading' );
						loader.addClass( 'nucleus-show' );
					}

					function nucleusHideLoader() {
						var loader = $( '.nucleus-loading' );
						loader.removeClass( 'nucleus-show' );
					}

					function nucleusShowResponse( response, status ) {
						var responseHolder = $( '.nucleus-response-holder' ),
							responseArea = responseHolder.find( 'div' );

						responseHolder.prop( 'hidden', false ).addClass( status );
						responseArea.html( response );
					}

					function nucleusHideResponse() {
						var responseHolder = $( '.nucleus-response-holder' ),
							responseArea = responseHolder.find( 'div' );

						responseHolder.prop( 'hidden', true );
						responseArea.html( '' );
					}

					/**
					 * Do import
					 */
					$( document ).on( 'submit', '#nucleus-do-import', function ( e ) {
						e.preventDefault();

						var self = $( this ),
							formdata = self.serializeArray();

						formdata.push( { name: 'action', value: 'nucleus_import' } );

						// Show loader & hide response holder
						nucleusShowLoader();
						nucleusHideResponse();

						$.post( ajaxurl, formdata ).done( function ( response ) {
							console.log( [ 'nucleus.import.done', response ] );

							nucleusHideLoader();
							setTimeout( function () {
								if ( false === response.success ) {
									nucleusShowResponse( response.data, 'error' );
								} else {
									nucleusShowResponse( response.data, 'updated' );
								}

								$( 'html, body' ).animate( { scrollTop: 0 }, 800 );
							}, 400 );

						} ).fail( function ( xhr, status, error ) {
							console.log( [ 'nucleus.import.fail', status, error, xhr.responseText ] );

							nucleusHideLoader();
							setTimeout( function () {
								nucleusShowResponse( xhr.responseText );

								$( 'html, body' ).animate( { scrollTop: 0 }, 800 );
							}, 400 );
						} )
					} );
				}
			)( jQuery );
		</script>
		<?php
	}

	/**
	 * Do the import
	 *
	 * Callback for "wp_ajax_nucleus_import"
	 *
	 * @see __construct
	 */
	public function do_import() {
		if ( empty( $_POST[ $this->nonce_field ] )
		     || ! wp_verify_nonce( $_POST[ $this->nonce_field ], $this->nonce )
		) {
			wp_send_json_error( '<p>Bad nonce</p>' );
		}

		// Path to import files
		$variant = sanitize_key( $_POST['variant'] );
		if ( ! array_key_exists( $variant, $this->variants ) ) {
			wp_send_json_error( sprintf( '<p>Given variant `%s` not found</p>',
				esc_html( $variant )
			) );
		}

		$file_xml  = wp_normalize_path( $this->variants[ $variant ]['xml'] );
		$file_json = wp_normalize_path( $this->variants[ $variant ]['extra'] );
		if ( ! file_exists( $file_xml ) || ! file_exists( $file_json ) ) {
			wp_send_json_error(
				sprintf( '<p>Demo files are missing.</p>',
					esc_html( $file_xml ),
					esc_html( $file_json )
				)
			);
		}

		$current_user_id = get_current_user_id();

		$time_limit = ini_get( 'max_execution_time' );
		set_time_limit( 0 );


		// Load the WordPress Importer class
		define( 'WP_LOAD_IMPORTERS', true );
		define( 'IMPORT_DEBUG', false );

		// Load Importer API
		require_once ABSPATH . 'wp-admin/includes/import.php';

		// Remove the admin_init action added in wordpress-importer plugin
		remove_action( 'admin_init', 'wordpress_importer_init' );

		$wp_import = new WP_Import();

		// Map the authors
		$import_data = $wp_import->parse( $file_xml );
		$wp_import->get_authors_from_import( $import_data );

		$imported_authors        = $wp_import->authors;
		$imported_authors_logins = array_keys( $imported_authors );
		$imported_author_login   = sanitize_user( array_shift( $imported_authors_logins ) );
		$imported_author_id      = $imported_authors[ $imported_author_login ]['author_id'];
		unset( $imported_authors_logins );

		// Set important data for import process
		$wp_import->id                                       = $this->import_id; // prevent cleanup
		$wp_import->fetch_attachments                        = $this->import_attachments;
		$wp_import->processed_authors[ $imported_author_id ] = $current_user_id; // user mapping
		$wp_import->author_mapping[ $imported_author_login ] = $current_user_id;

		// Do .xml import
		ob_start();
		$wp_import->import( $file_xml );
		$response = ob_get_clean();

		// Do .json extra import
		$extra_data = json_decode( file_get_contents( $file_json ), true );

		$this->import_extra_users( $extra_data );
		$this->import_extra_menu_locations( $extra_data );
		$this->import_extra_options( $extra_data );
		$this->import_extra_widgets( $extra_data, $import_data['terms'] );
		$this->import_extra_menu_meta( $extra_data, $wp_import );

		flush_rewrite_rules();
		set_time_limit( $time_limit );
		wp_send_json_success( $response );
	}

	private function import_extra_users( $extra_data ) {
		if ( ! array_key_exists( 'users', $extra_data ) || empty( $extra_data['users'] ) ) {
			return;
		}

		$users = $extra_data['users'];
		foreach ( $users as $user_login => $user_data ) {
			$user = get_user_by( 'login', $user_login );
			if ( false === $user ) {
				continue;
			}

			if ( ! is_array( $user_data ) || empty( $user_data ) ) {
				continue;
			}

			foreach ( $user_data as $meta_key => $meta_value ) {
				update_user_meta( $user->ID, $meta_key, maybe_unserialize( base64_decode( $meta_value ) ) );
			}
		}
	}

	private function import_extra_menu_locations( $extra_data ) {
		if ( ! array_key_exists( 'menu_locations', $extra_data )
		     || empty( $extra_data['menu_locations'] )
		) {
			return;
		}

		$menus           = $extra_data['menu_locations'];
		$locations_menus = array();
		foreach ( $menus as $location => $menu_slug ) {
			$menu                         = wp_get_nav_menu_object( $menu_slug );
			$locations_menus[ $location ] = (int) $menu->term_id;
		}

		set_theme_mod( 'nav_menu_locations', $locations_menus );
	}

	private function import_extra_options( $extra_data ) {
		if ( ! array_key_exists( 'options', $extra_data ) || empty( $extra_data['options'] ) ) {
			return;
		}

		$options = (array) $extra_data['options'];
		foreach ( $options as $option => $value ) {
			update_option( $option, maybe_unserialize( base64_decode( $value ) ) );
		}
	}

	private function import_extra_widgets( $extra_data, $terms ) {
		if ( ! array_key_exists( 'widgets', $extra_data ) ) {
			return;
		}

		/*
		 * Fix the situation with nav_menu widgets.
		 *
		 * Because imported menus has another IDs, than exported ones.
		 * Create the map with [exported_menu_id => imported_menu_id]
		 */
		$nav_menus_map = array();
		foreach ( (array) $terms as $term ) {
			if ( 'nav_menu' !== $term['term_taxonomy'] ) {
				continue;
			}

			$exporter_menu = (int) $term['term_id'];
			$imported_menu = (int) wp_get_nav_menu_object( $term['slug'] )->term_id;

			$nav_menus_map[ $exporter_menu ] = $imported_menu;
			unset( $exporter_menu, $imported_menu );
		}
		unset( $term );

		$imported_sidebars  = $extra_data['widgets'];
		$processed_sidebars = array();
		foreach ( $imported_sidebars as $sidebar => $widgets ) {
			foreach ( $widgets as $widget => $widget_data ) {
				$processed_sidebars[ $sidebar ][] = $widget;

				$widget_base   = trim( substr( $widget, 0, strrpos( $widget, '-' ) ) );
				$widget_idx    = (int) trim( substr( $widget, strrpos( $widget, '-' ) + 1 ) );
				$widget_option = 'widget_' . $widget_base;
				$widget_data   = maybe_unserialize( base64_decode( $widget_data ) );

				// Fix nav_menu
				if ( 'nav_menu' === $widget_base
				     && is_array( $widget_data )
				     && array_key_exists( 'nav_menu', $widget_data )
				) {
					$exported_menu_id        = (int) $widget_data['nav_menu'];
					$widget_data['nav_menu'] = (int) $nav_menus_map[ $exported_menu_id ];
				}

				$widgets_settings = get_option( $widget_option );
				if ( false === $widgets_settings || empty( $widgets_settings ) ) {
					$widgets_settings = array(
						$widget_idx    => $widget_data,
						'_multiwidget' => 1,
					);
				} elseif ( is_array( $widgets_settings ) ) {
					$widgets_settings[ $widget_idx ] = $widget_data;
				}

				update_option( $widget_option, $widgets_settings );
				unset( $widget_base, $widget_idx, $widget_option, $widget_data, $widgets_settings );

				continue;
			}
			unset( $widget, $widget_data );
		}

		wp_set_sidebars_widgets( $processed_sidebars );
	}

	private function import_extra_menu_meta( $extra_data, $wp_import ) {
		if ( ! array_key_exists( 'menu_meta', $extra_data ) || empty( $extra_data['menu_meta'] ) ) {
			return;
		}

		$menu_meta = $extra_data['menu_meta'];
		foreach ( $menu_meta as $post_id => $metas ) {
			if ( ! array_key_exists( (int) $post_id, $wp_import->processed_menu_items ) ) {
				continue;
			}

			$menu_item_id = $wp_import->processed_menu_items[ $post_id ];
			foreach ( $metas as $meta_key => $meta_value ) {
				update_post_meta( $menu_item_id, $meta_key, maybe_unserialize( base64_decode( $meta_value ) ) );
			}
		}
	}
}

if ( ! class_exists( 'WXR_Parser', false ) ) :
	/**
	 * WordPress Importer class for managing parsing of WXR files.
	 */
	class WXR_Parser {
		function parse( $file ) {
			// Attempt to use proper XML parsers first
			if ( extension_loaded( 'simplexml' ) ) {
				$parser = new WXR_Parser_SimpleXML;
				$result = $parser->parse( $file );

				// If SimpleXML succeeds or this is an invalid WXR file then return the results
				if ( ! is_wp_error( $result ) || 'SimpleXML_parse_error' != $result->get_error_code() ) {
					return $result;
				}
			} else if ( extension_loaded( 'xml' ) ) {
				$parser = new WXR_Parser_XML;
				$result = $parser->parse( $file );

				// If XMLParser succeeds or this is an invalid WXR file then return the results
				if ( ! is_wp_error( $result ) || 'XML_parse_error' != $result->get_error_code() ) {
					return $result;
				}
			}

			// We have a malformed XML file, so display the error and fallthrough to regex
			if ( isset( $result ) && defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
				echo '<pre>';
				if ( 'SimpleXML_parse_error' == $result->get_error_code() ) {
					foreach ( $result->get_error_data() as $error ) {
						echo $error->line . ':' . $error->column . ' ' . esc_html( $error->message ) . "\n";
					}
				} else if ( 'XML_parse_error' == $result->get_error_code() ) {
					$error = $result->get_error_data();
					echo $error[0] . ':' . $error[1] . ' ' . esc_html( $error[2] );
				}
				echo '</pre>';
				echo '<p><strong>' . __( 'There was an error when reading this WXR file', 'wordpress-importer' ) . '</strong><br />';
				echo __( 'Details are shown above. The importer will now try again with a different parser...', 'wordpress-importer' ) . '</p>';
			}

			// use regular expressions if nothing else available or this is bad XML
			$parser = new WXR_Parser_Regex;

			return $parser->parse( $file );
		}
	}
endif;

if ( ! class_exists( 'WXR_Parser_SimpleXML', false ) ) :
	/**
	 * WXR Parser that makes use of the SimpleXML PHP extension.
	 */
	class WXR_Parser_SimpleXML {
		function parse( $file ) {
			$authors = $posts = $categories = $tags = $terms = array();

			$internal_errors = libxml_use_internal_errors( true );

			$dom       = new DOMDocument;
			$old_value = null;
			if ( function_exists( 'libxml_disable_entity_loader' ) ) {
				$old_value = libxml_disable_entity_loader( true );
			}
			$success = $dom->loadXML( file_get_contents( $file ) );
			if ( ! is_null( $old_value ) ) {
				libxml_disable_entity_loader( $old_value );
			}

			if ( ! $success || isset( $dom->doctype ) ) {
				return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this WXR file', 'wordpress-importer' ), libxml_get_errors() );
			}

			$xml = simplexml_import_dom( $dom );
			unset( $dom );

			// halt if loading produces an error
			if ( ! $xml ) {
				return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this WXR file', 'wordpress-importer' ), libxml_get_errors() );
			}

			$wxr_version = $xml->xpath( '/rss/channel/wp:wxr_version' );
			if ( ! $wxr_version ) {
				return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );
			}

			$wxr_version = (string) trim( $wxr_version[0] );
			// confirm that we are dealing with the correct file format
			if ( ! preg_match( '/^\d+\.\d+$/', $wxr_version ) ) {
				return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );
			}

			$base_url = $xml->xpath( '/rss/channel/wp:base_site_url' );
			$base_url = (string) trim( $base_url[0] );

			$namespaces = $xml->getDocNamespaces();
			if ( ! isset( $namespaces['wp'] ) ) {
				$namespaces['wp'] = 'http://wordpress.org/export/1.1/';
			}
			if ( ! isset( $namespaces['excerpt'] ) ) {
				$namespaces['excerpt'] = 'http://wordpress.org/export/1.1/excerpt/';
			}

			// grab authors
			foreach ( $xml->xpath( '/rss/channel/wp:author' ) as $author_arr ) {
				$a                 = $author_arr->children( $namespaces['wp'] );
				$login             = (string) $a->author_login;
				$authors[ $login ] = array(
					'author_id'           => (int) $a->author_id,
					'author_login'        => $login,
					'author_email'        => (string) $a->author_email,
					'author_display_name' => (string) $a->author_display_name,
					'author_first_name'   => (string) $a->author_first_name,
					'author_last_name'    => (string) $a->author_last_name,
				);
			}

			// grab cats, tags and terms
			foreach ( $xml->xpath( '/rss/channel/wp:category' ) as $term_arr ) {
				$t            = $term_arr->children( $namespaces['wp'] );
				$categories[] = array(
					'term_id'              => (int) $t->term_id,
					'category_nicename'    => (string) $t->category_nicename,
					'category_parent'      => (string) $t->category_parent,
					'cat_name'             => (string) $t->cat_name,
					'category_description' => (string) $t->category_description,
				);
			}

			foreach ( $xml->xpath( '/rss/channel/wp:tag' ) as $term_arr ) {
				$t      = $term_arr->children( $namespaces['wp'] );
				$tags[] = array(
					'term_id'         => (int) $t->term_id,
					'tag_slug'        => (string) $t->tag_slug,
					'tag_name'        => (string) $t->tag_name,
					'tag_description' => (string) $t->tag_description,
				);
			}

			foreach ( $xml->xpath( '/rss/channel/wp:term' ) as $term_arr ) {
				$t       = $term_arr->children( $namespaces['wp'] );
				$terms[] = array(
					'term_id'          => (int) $t->term_id,
					'term_taxonomy'    => (string) $t->term_taxonomy,
					'slug'             => (string) $t->term_slug,
					'term_parent'      => (string) $t->term_parent,
					'term_name'        => (string) $t->term_name,
					'term_description' => (string) $t->term_description,
				);
			}

			// grab posts
			foreach ( $xml->channel->item as $item ) {
				$post = array(
					'post_title' => (string) $item->title,
					'guid'       => (string) $item->guid,
				);

				$dc                  = $item->children( 'http://purl.org/dc/elements/1.1/' );
				$post['post_author'] = (string) $dc->creator;

				$content              = $item->children( 'http://purl.org/rss/1.0/modules/content/' );
				$excerpt              = $item->children( $namespaces['excerpt'] );
				$post['post_content'] = (string) $content->encoded;
				$post['post_excerpt'] = (string) $excerpt->encoded;

				$wp                     = $item->children( $namespaces['wp'] );
				$post['post_id']        = (int) $wp->post_id;
				$post['post_date']      = (string) $wp->post_date;
				$post['post_date_gmt']  = (string) $wp->post_date_gmt;
				$post['comment_status'] = (string) $wp->comment_status;
				$post['ping_status']    = (string) $wp->ping_status;
				$post['post_name']      = (string) $wp->post_name;
				$post['status']         = (string) $wp->status;
				$post['post_parent']    = (int) $wp->post_parent;
				$post['menu_order']     = (int) $wp->menu_order;
				$post['post_type']      = (string) $wp->post_type;
				$post['post_password']  = (string) $wp->post_password;
				$post['is_sticky']      = (int) $wp->is_sticky;

				if ( isset( $wp->attachment_url ) ) {
					$post['attachment_url'] = (string) $wp->attachment_url;
				}

				foreach ( $item->category as $c ) {
					$att = $c->attributes();
					if ( isset( $att['nicename'] ) ) {
						$post['terms'][] = array(
							'name'   => (string) $c,
							'slug'   => (string) $att['nicename'],
							'domain' => (string) $att['domain'],
						);
					}
				}

				foreach ( $wp->postmeta as $meta ) {
					$post['postmeta'][] = array(
						'key'   => (string) $meta->meta_key,
						'value' => (string) $meta->meta_value,
					);
				}

				foreach ( $wp->comment as $comment ) {
					$meta = array();
					if ( isset( $comment->commentmeta ) ) {
						foreach ( $comment->commentmeta as $m ) {
							$meta[] = array(
								'key'   => (string) $m->meta_key,
								'value' => (string) $m->meta_value,
							);
						}
					}

					$post['comments'][] = array(
						'comment_id'           => (int) $comment->comment_id,
						'comment_author'       => (string) $comment->comment_author,
						'comment_author_email' => (string) $comment->comment_author_email,
						'comment_author_IP'    => (string) $comment->comment_author_IP,
						'comment_author_url'   => (string) $comment->comment_author_url,
						'comment_date'         => (string) $comment->comment_date,
						'comment_date_gmt'     => (string) $comment->comment_date_gmt,
						'comment_content'      => (string) $comment->comment_content,
						'comment_approved'     => (string) $comment->comment_approved,
						'comment_type'         => (string) $comment->comment_type,
						'comment_parent'       => (string) $comment->comment_parent,
						'comment_user_id'      => (int) $comment->comment_user_id,
						'commentmeta'          => $meta,
					);
				}

				$posts[] = $post;
			}

			return array(
				'authors'    => $authors,
				'posts'      => $posts,
				'categories' => $categories,
				'tags'       => $tags,
				'terms'      => $terms,
				'base_url'   => $base_url,
				'version'    => $wxr_version,
			);
		}
	}
endif;

if ( ! class_exists( 'WXR_Parser_XML', false ) ) :
	/**
	 * WXR Parser that makes use of the XML Parser PHP extension.
	 */
	class WXR_Parser_XML {
		var $wp_tags = array(
			'wp:post_id',
			'wp:post_date',
			'wp:post_date_gmt',
			'wp:comment_status',
			'wp:ping_status',
			'wp:attachment_url',
			'wp:status',
			'wp:post_name',
			'wp:post_parent',
			'wp:menu_order',
			'wp:post_type',
			'wp:post_password',
			'wp:is_sticky',
			'wp:term_id',
			'wp:category_nicename',
			'wp:category_parent',
			'wp:cat_name',
			'wp:category_description',
			'wp:tag_slug',
			'wp:tag_name',
			'wp:tag_description',
			'wp:term_taxonomy',
			'wp:term_parent',
			'wp:term_name',
			'wp:term_description',
			'wp:author_id',
			'wp:author_login',
			'wp:author_email',
			'wp:author_display_name',
			'wp:author_first_name',
			'wp:author_last_name',
		);
		var $wp_sub_tags = array(
			'wp:comment_id',
			'wp:comment_author',
			'wp:comment_author_email',
			'wp:comment_author_url',
			'wp:comment_author_IP',
			'wp:comment_date',
			'wp:comment_date_gmt',
			'wp:comment_content',
			'wp:comment_approved',
			'wp:comment_type',
			'wp:comment_parent',
			'wp:comment_user_id',
		);

		function parse( $file ) {
			$this->wxr_version = $this->in_post = $this->cdata = $this->data = $this->sub_data = $this->in_tag = $this->in_sub_tag = false;
			$this->authors     = $this->posts = $this->term = $this->category = $this->tag = array();

			$xml = xml_parser_create( 'UTF-8' );
			xml_parser_set_option( $xml, XML_OPTION_SKIP_WHITE, 1 );
			xml_parser_set_option( $xml, XML_OPTION_CASE_FOLDING, 0 );
			xml_set_object( $xml, $this );
			xml_set_character_data_handler( $xml, 'cdata' );
			xml_set_element_handler( $xml, 'tag_open', 'tag_close' );

			if ( ! xml_parse( $xml, file_get_contents( $file ), true ) ) {
				$current_line   = xml_get_current_line_number( $xml );
				$current_column = xml_get_current_column_number( $xml );
				$error_code     = xml_get_error_code( $xml );
				$error_string   = xml_error_string( $error_code );

				return new WP_Error( 'XML_parse_error', 'There was an error when reading this WXR file', array(
					$current_line,
					$current_column,
					$error_string,
				) );
			}
			xml_parser_free( $xml );

			if ( ! preg_match( '/^\d+\.\d+$/', $this->wxr_version ) ) {
				return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );
			}

			return array(
				'authors'    => $this->authors,
				'posts'      => $this->posts,
				'categories' => $this->category,
				'tags'       => $this->tag,
				'terms'      => $this->term,
				'base_url'   => $this->base_url,
				'version'    => $this->wxr_version,
			);
		}

		function tag_open( $parse, $tag, $attr ) {
			if ( in_array( $tag, $this->wp_tags ) ) {
				$this->in_tag = substr( $tag, 3 );

				return;
			}

			if ( in_array( $tag, $this->wp_sub_tags ) ) {
				$this->in_sub_tag = substr( $tag, 3 );

				return;
			}

			switch ( $tag ) {
				case 'category':
					if ( isset( $attr['domain'], $attr['nicename'] ) ) {
						$this->sub_data['domain'] = $attr['domain'];
						$this->sub_data['slug']   = $attr['nicename'];
					}
					break;
				case 'item':
					$this->in_post = true;
				case 'title':
					if ( $this->in_post ) {
						$this->in_tag = 'post_title';
					}
					break;
				case 'guid':
					$this->in_tag = 'guid';
					break;
				case 'dc:creator':
					$this->in_tag = 'post_author';
					break;
				case 'content:encoded':
					$this->in_tag = 'post_content';
					break;
				case 'excerpt:encoded':
					$this->in_tag = 'post_excerpt';
					break;

				case 'wp:term_slug':
					$this->in_tag = 'slug';
					break;
				case 'wp:meta_key':
					$this->in_sub_tag = 'key';
					break;
				case 'wp:meta_value':
					$this->in_sub_tag = 'value';
					break;
			}
		}

		function cdata( $parser, $cdata ) {
			if ( ! trim( $cdata ) ) {
				return;
			}

			$this->cdata .= trim( $cdata );
		}

		function tag_close( $parser, $tag ) {
			switch ( $tag ) {
				case 'wp:comment':
					unset( $this->sub_data['key'], $this->sub_data['value'] ); // remove meta sub_data
					if ( ! empty( $this->sub_data ) ) {
						$this->data['comments'][] = $this->sub_data;
					}
					$this->sub_data = false;
					break;
				case 'wp:commentmeta':
					$this->sub_data['commentmeta'][] = array(
						'key'   => $this->sub_data['key'],
						'value' => $this->sub_data['value'],
					);
					break;
				case 'category':
					if ( ! empty( $this->sub_data ) ) {
						$this->sub_data['name'] = $this->cdata;
						$this->data['terms'][]  = $this->sub_data;
					}
					$this->sub_data = false;
					break;
				case 'wp:postmeta':
					if ( ! empty( $this->sub_data ) ) {
						$this->data['postmeta'][] = $this->sub_data;
					}
					$this->sub_data = false;
					break;
				case 'item':
					$this->posts[] = $this->data;
					$this->data    = false;
					break;
				case 'wp:category':
				case 'wp:tag':
				case 'wp:term':
					$n = substr( $tag, 3 );
					array_push( $this->$n, $this->data );
					$this->data = false;
					break;
				case 'wp:author':
					if ( ! empty( $this->data['author_login'] ) ) {
						$this->authors[ $this->data['author_login'] ] = $this->data;
					}
					$this->data = false;
					break;
				case 'wp:base_site_url':
					$this->base_url = $this->cdata;
					break;
				case 'wp:wxr_version':
					$this->wxr_version = $this->cdata;
					break;

				default:
					if ( $this->in_sub_tag ) {
						$this->sub_data[ $this->in_sub_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
						$this->in_sub_tag                    = false;
					} else if ( $this->in_tag ) {
						$this->data[ $this->in_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
						$this->in_tag                = false;
					}
			}

			$this->cdata = false;
		}
	}
endif;

if ( ! class_exists( 'WXR_Parser_Regex', false ) ) :
	/**
	 * WXR Parser that uses regular expressions. Fallback for installs without an XML parser.
	 */
	class WXR_Parser_Regex {
		var $authors = array();
		var $posts = array();
		var $categories = array();
		var $tags = array();
		var $terms = array();
		var $base_url = '';

		function __construct() {
			$this->has_gzip = is_callable( 'gzopen' );
		}

		function parse( $file ) {
			$wxr_version = $in_post = false;

			$fp = $this->fopen( $file, 'r' );
			if ( $fp ) {
				while ( ! $this->feof( $fp ) ) {
					$importline = rtrim( $this->fgets( $fp ) );

					if ( ! $wxr_version && preg_match( '|<wp:wxr_version>(\d+\.\d+)</wp:wxr_version>|', $importline, $version ) ) {
						$wxr_version = $version[1];
					}

					if ( false !== strpos( $importline, '<wp:base_site_url>' ) ) {
						preg_match( '|<wp:base_site_url>(.*?)</wp:base_site_url>|is', $importline, $url );
						$this->base_url = $url[1];
						continue;
					}
					if ( false !== strpos( $importline, '<wp:category>' ) ) {
						preg_match( '|<wp:category>(.*?)</wp:category>|is', $importline, $category );
						$this->categories[] = $this->process_category( $category[1] );
						continue;
					}
					if ( false !== strpos( $importline, '<wp:tag>' ) ) {
						preg_match( '|<wp:tag>(.*?)</wp:tag>|is', $importline, $tag );
						$this->tags[] = $this->process_tag( $tag[1] );
						continue;
					}
					if ( false !== strpos( $importline, '<wp:term>' ) ) {
						preg_match( '|<wp:term>(.*?)</wp:term>|is', $importline, $term );
						$this->terms[] = $this->process_term( $term[1] );
						continue;
					}
					if ( false !== strpos( $importline, '<wp:author>' ) ) {
						preg_match( '|<wp:author>(.*?)</wp:author>|is', $importline, $author );
						$a                                   = $this->process_author( $author[1] );
						$this->authors[ $a['author_login'] ] = $a;
						continue;
					}
					if ( false !== strpos( $importline, '<item>' ) ) {
						$post    = '';
						$in_post = true;
						continue;
					}
					if ( false !== strpos( $importline, '</item>' ) ) {
						$in_post       = false;
						$this->posts[] = $this->process_post( $post );
						continue;
					}
					if ( $in_post ) {
						$post .= $importline . "\n";
					}
				}

				$this->fclose( $fp );
			}

			if ( ! $wxr_version ) {
				return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );
			}

			return array(
				'authors'    => $this->authors,
				'posts'      => $this->posts,
				'categories' => $this->categories,
				'tags'       => $this->tags,
				'terms'      => $this->terms,
				'base_url'   => $this->base_url,
				'version'    => $wxr_version,
			);
		}

		function get_tag( $string, $tag ) {
			preg_match( "|<$tag.*?>(.*?)</$tag>|is", $string, $return );
			if ( isset( $return[1] ) ) {
				if ( substr( $return[1], 0, 9 ) == '<![CDATA[' ) {
					if ( strpos( $return[1], ']]]]><![CDATA[>' ) !== false ) {
						preg_match_all( '|<!\[CDATA\[(.*?)\]\]>|s', $return[1], $matches );
						$return = '';
						foreach ( $matches[1] as $match ) {
							$return .= $match;
						}
					} else {
						$return = preg_replace( '|^<!\[CDATA\[(.*)\]\]>$|s', '$1', $return[1] );
					}
				} else {
					$return = $return[1];
				}
			} else {
				$return = '';
			}

			return $return;
		}

		function process_category( $c ) {
			return array(
				'term_id'              => $this->get_tag( $c, 'wp:term_id' ),
				'cat_name'             => $this->get_tag( $c, 'wp:cat_name' ),
				'category_nicename'    => $this->get_tag( $c, 'wp:category_nicename' ),
				'category_parent'      => $this->get_tag( $c, 'wp:category_parent' ),
				'category_description' => $this->get_tag( $c, 'wp:category_description' ),
			);
		}

		function process_tag( $t ) {
			return array(
				'term_id'         => $this->get_tag( $t, 'wp:term_id' ),
				'tag_name'        => $this->get_tag( $t, 'wp:tag_name' ),
				'tag_slug'        => $this->get_tag( $t, 'wp:tag_slug' ),
				'tag_description' => $this->get_tag( $t, 'wp:tag_description' ),
			);
		}

		function process_term( $t ) {
			return array(
				'term_id'          => $this->get_tag( $t, 'wp:term_id' ),
				'term_taxonomy'    => $this->get_tag( $t, 'wp:term_taxonomy' ),
				'slug'             => $this->get_tag( $t, 'wp:term_slug' ),
				'term_parent'      => $this->get_tag( $t, 'wp:term_parent' ),
				'term_name'        => $this->get_tag( $t, 'wp:term_name' ),
				'term_description' => $this->get_tag( $t, 'wp:term_description' ),
			);
		}

		function process_author( $a ) {
			return array(
				'author_id'           => $this->get_tag( $a, 'wp:author_id' ),
				'author_login'        => $this->get_tag( $a, 'wp:author_login' ),
				'author_email'        => $this->get_tag( $a, 'wp:author_email' ),
				'author_display_name' => $this->get_tag( $a, 'wp:author_display_name' ),
				'author_first_name'   => $this->get_tag( $a, 'wp:author_first_name' ),
				'author_last_name'    => $this->get_tag( $a, 'wp:author_last_name' ),
			);
		}

		function process_post( $post ) {
			$post_id        = $this->get_tag( $post, 'wp:post_id' );
			$post_title     = $this->get_tag( $post, 'title' );
			$post_date      = $this->get_tag( $post, 'wp:post_date' );
			$post_date_gmt  = $this->get_tag( $post, 'wp:post_date_gmt' );
			$comment_status = $this->get_tag( $post, 'wp:comment_status' );
			$ping_status    = $this->get_tag( $post, 'wp:ping_status' );
			$status         = $this->get_tag( $post, 'wp:status' );
			$post_name      = $this->get_tag( $post, 'wp:post_name' );
			$post_parent    = $this->get_tag( $post, 'wp:post_parent' );
			$menu_order     = $this->get_tag( $post, 'wp:menu_order' );
			$post_type      = $this->get_tag( $post, 'wp:post_type' );
			$post_password  = $this->get_tag( $post, 'wp:post_password' );
			$is_sticky      = $this->get_tag( $post, 'wp:is_sticky' );
			$guid           = $this->get_tag( $post, 'guid' );
			$post_author    = $this->get_tag( $post, 'dc:creator' );

			$post_excerpt = $this->get_tag( $post, 'excerpt:encoded' );
			$post_excerpt = preg_replace_callback( '|<(/?[A-Z]+)|', array(
				&$this,
				'_normalize_tag',
			), $post_excerpt );
			$post_excerpt = str_replace( '<br>', '<br />', $post_excerpt );
			$post_excerpt = str_replace( '<hr>', '<hr />', $post_excerpt );

			$post_content = $this->get_tag( $post, 'content:encoded' );
			$post_content = preg_replace_callback( '|<(/?[A-Z]+)|', array(
				&$this,
				'_normalize_tag',
			), $post_content );
			$post_content = str_replace( '<br>', '<br />', $post_content );
			$post_content = str_replace( '<hr>', '<hr />', $post_content );

			$postdata = compact( 'post_id', 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_excerpt',
				'post_title', 'status', 'post_name', 'comment_status', 'ping_status', 'guid', 'post_parent',
				'menu_order', 'post_type', 'post_password', 'is_sticky'
			);

			$attachment_url = $this->get_tag( $post, 'wp:attachment_url' );
			if ( $attachment_url ) {
				$postdata['attachment_url'] = $attachment_url;
			}

			preg_match_all( '|<category domain="([^"]+?)" nicename="([^"]+?)">(.+?)</category>|is', $post, $terms, PREG_SET_ORDER );
			foreach ( $terms as $t ) {
				$post_terms[] = array(
					'slug'   => $t[2],
					'domain' => $t[1],
					'name'   => str_replace( array( '<![CDATA[', ']]>' ), '', $t[3] ),
				);
			}
			if ( ! empty( $post_terms ) ) {
				$postdata['terms'] = $post_terms;
			}

			preg_match_all( '|<wp:comment>(.+?)</wp:comment>|is', $post, $comments );
			$comments = $comments[1];
			if ( $comments ) {
				foreach ( $comments as $comment ) {
					preg_match_all( '|<wp:commentmeta>(.+?)</wp:commentmeta>|is', $comment, $commentmeta );
					$commentmeta = $commentmeta[1];
					$c_meta      = array();
					foreach ( $commentmeta as $m ) {
						$c_meta[] = array(
							'key'   => $this->get_tag( $m, 'wp:meta_key' ),
							'value' => $this->get_tag( $m, 'wp:meta_value' ),
						);
					}

					$post_comments[] = array(
						'comment_id'           => $this->get_tag( $comment, 'wp:comment_id' ),
						'comment_author'       => $this->get_tag( $comment, 'wp:comment_author' ),
						'comment_author_email' => $this->get_tag( $comment, 'wp:comment_author_email' ),
						'comment_author_IP'    => $this->get_tag( $comment, 'wp:comment_author_IP' ),
						'comment_author_url'   => $this->get_tag( $comment, 'wp:comment_author_url' ),
						'comment_date'         => $this->get_tag( $comment, 'wp:comment_date' ),
						'comment_date_gmt'     => $this->get_tag( $comment, 'wp:comment_date_gmt' ),
						'comment_content'      => $this->get_tag( $comment, 'wp:comment_content' ),
						'comment_approved'     => $this->get_tag( $comment, 'wp:comment_approved' ),
						'comment_type'         => $this->get_tag( $comment, 'wp:comment_type' ),
						'comment_parent'       => $this->get_tag( $comment, 'wp:comment_parent' ),
						'comment_user_id'      => $this->get_tag( $comment, 'wp:comment_user_id' ),
						'commentmeta'          => $c_meta,
					);
				}
			}
			if ( ! empty( $post_comments ) ) {
				$postdata['comments'] = $post_comments;
			}

			preg_match_all( '|<wp:postmeta>(.+?)</wp:postmeta>|is', $post, $postmeta );
			$postmeta = $postmeta[1];
			if ( $postmeta ) {
				foreach ( $postmeta as $p ) {
					$post_postmeta[] = array(
						'key'   => $this->get_tag( $p, 'wp:meta_key' ),
						'value' => $this->get_tag( $p, 'wp:meta_value' ),
					);
				}
			}
			if ( ! empty( $post_postmeta ) ) {
				$postdata['postmeta'] = $post_postmeta;
			}

			return $postdata;
		}

		function _normalize_tag( $matches ) {
			return '<' . strtolower( $matches[1] );
		}

		function fopen( $filename, $mode = 'r' ) {
			if ( $this->has_gzip ) {
				return gzopen( $filename, $mode );
			}

			return fopen( $filename, $mode );
		}

		function feof( $fp ) {
			if ( $this->has_gzip ) {
				return gzeof( $fp );
			}

			return feof( $fp );
		}

		function fgets( $fp, $len = 8192 ) {
			if ( $this->has_gzip ) {
				return gzgets( $fp, $len );
			}

			return fgets( $fp, $len );
		}

		function fclose( $fp ) {
			if ( $this->has_gzip ) {
				return gzclose( $fp );
			}

			return fclose( $fp );
		}
	}
endif;

if ( ! class_exists( 'WP_Importer', false ) ) :
	require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
endif;

if ( ! class_exists( 'WP_Import', false ) ) :
	/**
	 * WordPress Importer class for managing the import process of a WXR file
	 *
	 * @package    WordPress
	 * @subpackage Importer
	 */
	class WP_Import extends WP_Importer {
		var $max_wxr_version = 1.2; // max. supported WXR version

		var $id; // WXR attachment ID

		// information to import from WXR file
		var $version;
		var $authors = array();
		var $posts = array();
		var $terms = array();
		var $categories = array();
		var $tags = array();
		var $base_url = '';

		// mappings from old information to new
		var $processed_authors = array();
		var $author_mapping = array();
		var $processed_terms = array();
		var $processed_posts = array();
		var $post_orphans = array();
		var $processed_menu_items = array();
		var $menu_item_orphans = array();
		var $missing_menu_items = array();

		var $fetch_attachments = false;
		var $url_remap = array();
		var $featured_images = array();

		/**
		 * Registered callback function for the WordPress Importer
		 *
		 * Manages the three separate stages of the WXR import process
		 */
		function dispatch() {
			$this->header();

			$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
			switch ( $step ) {
				case 0:
					$this->greet();
					break;
				case 1:
					check_admin_referer( 'import-upload' );
					if ( $this->handle_upload() ) {
						$this->import_options();
					}
					break;
				case 2:
					check_admin_referer( 'import-wordpress' );
					$this->fetch_attachments = ( ! empty( $_POST['fetch_attachments'] ) && $this->allow_fetch_attachments() );
					$this->id                = (int) $_POST['import_id'];
					$file                    = get_attached_file( $this->id );
					set_time_limit( 0 );
					$this->import( $file );
					break;
			}

			$this->footer();
		}

		/**
		 * The main controller for the actual import stage.
		 *
		 * @param string $file Path to the WXR file for importing
		 */
		function import( $file ) {
			add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
			add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );

			$this->import_start( $file );

			$this->get_author_mapping();

			wp_suspend_cache_invalidation( true );
			$this->process_categories();
			$this->process_tags();
			$this->process_terms();
			$this->process_posts();
			wp_suspend_cache_invalidation( false );

			// update incorrect/missing information in the DB
			$this->backfill_parents();
			$this->backfill_attachment_urls();
			$this->remap_featured_images();

			$this->import_end();
		}

		/**
		 * Parses the WXR file and prepares us for the task of processing parsed data
		 *
		 * @param string $file Path to the WXR file for importing
		 */
		function import_start( $file ) {
			if ( ! is_file( $file ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				echo __( 'The file does not exist, please try again.', 'wordpress-importer' ) . '</p>';
				$this->footer();
				die();
			}

			$import_data = $this->parse( $file );

			if ( is_wp_error( $import_data ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				echo esc_html( $import_data->get_error_message() ) . '</p>';
				$this->footer();
				die();
			}

			$this->version = $import_data['version'];
			$this->get_authors_from_import( $import_data );
			$this->posts      = $import_data['posts'];
			$this->terms      = $import_data['terms'];
			$this->categories = $import_data['categories'];
			$this->tags       = $import_data['tags'];
			$this->base_url   = esc_url( $import_data['base_url'] );

			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );

			do_action( 'import_start' );
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		function import_end() {
			wp_import_cleanup( $this->id );

			wp_cache_flush();
			foreach ( get_taxonomies() as $tax ) {
				delete_option( "{$tax}_children" );
				_get_term_hierarchy( $tax );
			}

			wp_defer_term_counting( false );
			wp_defer_comment_counting( false );

			echo '<p>' . __( 'All done.', 'wordpress-importer' ) . ' <a href="' . admin_url() . '">' . __( 'Have fun!', 'wordpress-importer' ) . '</a>' . '</p>';
			echo '<p>' . __( 'Remember to update the passwords and roles of imported users.', 'wordpress-importer' ) . '</p>';

			do_action( 'import_end' );
		}

		/**
		 * Handles the WXR upload and initial parsing of the file to prepare for
		 * displaying author import options
		 *
		 * @return bool False if error uploading or invalid file, true otherwise
		 */
		function handle_upload() {
			$file = wp_import_handle_upload();

			if ( isset( $file['error'] ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				echo esc_html( $file['error'] ) . '</p>';

				return false;
			} else if ( ! file_exists( $file['file'] ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				printf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'wordpress-importer' ), esc_html( $file['file'] ) );
				echo '</p>';

				return false;
			}

			$this->id    = (int) $file['id'];
			$import_data = $this->parse( $file['file'] );
			if ( is_wp_error( $import_data ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				echo esc_html( $import_data->get_error_message() ) . '</p>';

				return false;
			}

			$this->version = $import_data['version'];
			if ( $this->version > $this->max_wxr_version ) {
				echo '<div class="error"><p><strong>';
				printf( __( 'This WXR file (version %s) may not be supported by this version of the importer. Please consider updating.', 'wordpress-importer' ), esc_html( $import_data['version'] ) );
				echo '</strong></p></div>';
			}

			$this->get_authors_from_import( $import_data );

			return true;
		}

		/**
		 * Retrieve authors from parsed WXR data
		 *
		 * Uses the provided author information from WXR 1.1 files
		 * or extracts info from each post for WXR 1.0 files
		 *
		 * @param array $import_data Data returned by a WXR parser
		 */
		function get_authors_from_import( $import_data ) {
			if ( ! empty( $import_data['authors'] ) ) {
				$this->authors = $import_data['authors'];
				// no author information, grab it from the posts
			} else {
				foreach ( $import_data['posts'] as $post ) {
					$login = sanitize_user( $post['post_author'], true );
					if ( empty( $login ) ) {
						printf( __( 'Failed to import author %s. Their posts will be attributed to the current user.', 'wordpress-importer' ), esc_html( $post['post_author'] ) );
						echo '<br />';
						continue;
					}

					if ( ! isset( $this->authors[ $login ] ) ) {
						$this->authors[ $login ] = array(
							'author_login'        => $login,
							'author_display_name' => $post['post_author'],
						);
					}
				}
			}
		}

		/**
		 * Display pre-import options, author importing/mapping and option to
		 * fetch attachments
		 */
		function import_options() {
			$j = 0;
			?>
			<form action="<?php echo admin_url( 'admin.php?import=wordpress&amp;step=2' ); ?>"
			      method="post">
				<?php wp_nonce_field( 'import-wordpress' ); ?>
				<input type="hidden" name="import_id" value="<?php echo $this->id; ?>"/>

				<?php if ( ! empty( $this->authors ) ) : ?>
					<h3><?php _e( 'Assign Authors', 'wordpress-importer' ); ?></h3>
					<p><?php _e( 'To make it easier for you to edit and save the imported content, you may want to reassign the author of the imported item to an existing user of this site. For example, you may want to import all the entries as <code>admin</code>s entries.', 'wordpress-importer' ); ?></p>
					<?php if ( $this->allow_create_users() ) : ?>
						<p><?php printf( __( 'If a new user is created by WordPress, a new password will be randomly generated and the new user&#8217;s role will be set as %s. Manually changing the new user&#8217;s details will be necessary.', 'wordpress-importer' ), esc_html( get_option( 'default_role' ) ) ); ?></p>
					<?php endif; ?>
					<ol id="authors">
						<?php foreach ( $this->authors as $author ) : ?>
							<li><?php $this->author_select( $j ++, $author ); ?></li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>

				<?php if ( $this->allow_fetch_attachments() ) : ?>
					<h3><?php _e( 'Import Attachments', 'wordpress-importer' ); ?></h3>
					<p>
						<input type="checkbox" value="1" name="fetch_attachments"
						       id="import-attachments"/>
						<label
							for="import-attachments"><?php _e( 'Download and import file attachments', 'wordpress-importer' ); ?></label>
					</p>
				<?php endif; ?>

				<p class="submit"><input type="submit" class="button"
				                         value="<?php esc_attr_e( 'Submit', 'wordpress-importer' ); ?>"/>
				</p>
			</form>
			<?php
		}

		/**
		 * Display import options for an individual author. That is, either create
		 * a new user based on import info or map to an existing user
		 *
		 * @param int   $n      Index for each author in the form
		 * @param array $author Author information, e.g. login, display name, email
		 */
		function author_select( $n, $author ) {
			_e( 'Import author:', 'wordpress-importer' );
			echo ' <strong>' . esc_html( $author['author_display_name'] );
			if ( $this->version != '1.0' ) {
				echo ' (' . esc_html( $author['author_login'] ) . ')';
			}
			echo '</strong><br />';

			if ( $this->version != '1.0' ) {
				echo '<div style="margin-left:18px">';
			}

			$create_users = $this->allow_create_users();
			if ( $create_users ) {
				if ( $this->version != '1.0' ) {
					_e( 'or create new user with login name:', 'wordpress-importer' );
					$value = '';
				} else {
					_e( 'as a new user:', 'wordpress-importer' );
					$value = esc_attr( sanitize_user( $author['author_login'], true ) );
				}

				echo ' <input type="text" name="user_new[' . $n . ']" value="' . $value . '" /><br />';
			}

			if ( ! $create_users && $this->version == '1.0' ) {
				_e( 'assign posts to an existing user:', 'wordpress-importer' );
			} else {
				_e( 'or assign posts to an existing user:', 'wordpress-importer' );
			}
			wp_dropdown_users( array(
				'name'            => "user_map[$n]",
				'multi'           => true,
				'show_option_all' => __( '- Select -', 'wordpress-importer' ),
			) );
			echo '<input type="hidden" name="imported_authors[' . $n . ']" value="' . esc_attr( $author['author_login'] ) . '" />';

			if ( $this->version != '1.0' ) {
				echo '</div>';
			}
		}

		/**
		 * Map old author logins to local user IDs based on decisions made
		 * in import options form. Can map to an existing user, create a new user
		 * or falls back to the current user in case of error with either of the previous
		 */
		function get_author_mapping() {
			if ( ! isset( $_POST['imported_authors'] ) ) {
				return;
			}

			$create_users = $this->allow_create_users();

			foreach ( (array) $_POST['imported_authors'] as $i => $old_login ) {
				// Multisite adds strtolower to sanitize_user. Need to sanitize here to stop breakage in process_posts.
				$santized_old_login = sanitize_user( $old_login, true );
				$old_id             = isset( $this->authors[ $old_login ]['author_id'] ) ? intval( $this->authors[ $old_login ]['author_id'] ) : false;

				if ( ! empty( $_POST['user_map'][ $i ] ) ) {
					$user = get_userdata( intval( $_POST['user_map'][ $i ] ) );
					if ( isset( $user->ID ) ) {
						if ( $old_id ) {
							$this->processed_authors[ $old_id ] = $user->ID;
						}
						$this->author_mapping[ $santized_old_login ] = $user->ID;
					}
				} else if ( $create_users ) {
					if ( ! empty( $_POST['user_new'][ $i ] ) ) {
						$user_id = wp_create_user( $_POST['user_new'][ $i ], wp_generate_password() );
					} else if ( $this->version != '1.0' ) {
						$user_data = array(
							'user_login'   => $old_login,
							'user_pass'    => wp_generate_password(),
							'user_email'   => isset( $this->authors[ $old_login ]['author_email'] ) ? $this->authors[ $old_login ]['author_email'] : '',
							'display_name' => $this->authors[ $old_login ]['author_display_name'],
							'first_name'   => isset( $this->authors[ $old_login ]['author_first_name'] ) ? $this->authors[ $old_login ]['author_first_name'] : '',
							'last_name'    => isset( $this->authors[ $old_login ]['author_last_name'] ) ? $this->authors[ $old_login ]['author_last_name'] : '',
						);
						$user_id   = wp_insert_user( $user_data );
					}

					if ( ! is_wp_error( $user_id ) ) {
						if ( $old_id ) {
							$this->processed_authors[ $old_id ] = $user_id;
						}
						$this->author_mapping[ $santized_old_login ] = $user_id;
					} else {
						printf( __( 'Failed to create new user for %s. Their posts will be attributed to the current user.', 'wordpress-importer' ), esc_html( $this->authors[ $old_login ]['author_display_name'] ) );
						if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
							echo ' ' . $user_id->get_error_message();
						}
						echo '<br />';
					}
				}

				// failsafe: if the user_id was invalid, default to the current user
				if ( ! isset( $this->author_mapping[ $santized_old_login ] ) ) {
					if ( $old_id ) {
						$this->processed_authors[ $old_id ] = (int) get_current_user_id();
					}
					$this->author_mapping[ $santized_old_login ] = (int) get_current_user_id();
				}
			}
		}

		/**
		 * Create new categories based on import information
		 *
		 * Doesn't create a new category if its slug already exists
		 */
		function process_categories() {
			$this->categories = apply_filters( 'wp_import_categories', $this->categories );

			if ( empty( $this->categories ) ) {
				return;
			}

			foreach ( $this->categories as $cat ) {
				// if the category already exists leave it alone
				$term_id = term_exists( $cat['category_nicename'], 'category' );
				if ( $term_id ) {
					if ( is_array( $term_id ) ) {
						$term_id = $term_id['term_id'];
					}
					if ( isset( $cat['term_id'] ) ) {
						$this->processed_terms[ intval( $cat['term_id'] ) ] = (int) $term_id;
					}
					continue;
				}

				$category_parent      = empty( $cat['category_parent'] ) ? 0 : category_exists( $cat['category_parent'] );
				$category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';
				$catarr               = array(
					'category_nicename'    => $cat['category_nicename'],
					'category_parent'      => $category_parent,
					'cat_name'             => $cat['cat_name'],
					'category_description' => $category_description,
				);

				$id = wp_insert_category( $catarr );
				if ( ! is_wp_error( $id ) ) {
					if ( isset( $cat['term_id'] ) ) {
						$this->processed_terms[ intval( $cat['term_id'] ) ] = $id;
					}
				} else {
					printf( __( 'Failed to import category %s', 'wordpress-importer' ), esc_html( $cat['category_nicename'] ) );
					if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
						echo ': ' . $id->get_error_message();
					}
					echo '<br />';
					continue;
				}
			}

			unset( $this->categories );
		}

		/**
		 * Create new post tags based on import information
		 *
		 * Doesn't create a tag if its slug already exists
		 */
		function process_tags() {
			$this->tags = apply_filters( 'wp_import_tags', $this->tags );

			if ( empty( $this->tags ) ) {
				return;
			}

			foreach ( $this->tags as $tag ) {
				// if the tag already exists leave it alone
				$term_id = term_exists( $tag['tag_slug'], 'post_tag' );
				if ( $term_id ) {
					if ( is_array( $term_id ) ) {
						$term_id = $term_id['term_id'];
					}
					if ( isset( $tag['term_id'] ) ) {
						$this->processed_terms[ intval( $tag['term_id'] ) ] = (int) $term_id;
					}
					continue;
				}

				$tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
				$tagarr   = array( 'slug' => $tag['tag_slug'], 'description' => $tag_desc );

				$id = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );
				if ( ! is_wp_error( $id ) ) {
					if ( isset( $tag['term_id'] ) ) {
						$this->processed_terms[ intval( $tag['term_id'] ) ] = $id['term_id'];
					}
				} else {
					printf( __( 'Failed to import post tag %s', 'wordpress-importer' ), esc_html( $tag['tag_name'] ) );
					if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
						echo ': ' . $id->get_error_message();
					}
					echo '<br />';
					continue;
				}
			}

			unset( $this->tags );
		}

		/**
		 * Create new terms based on import information
		 *
		 * Doesn't create a term its slug already exists
		 */
		function process_terms() {
			$this->terms = apply_filters( 'wp_import_terms', $this->terms );

			if ( empty( $this->terms ) ) {
				return;
			}

			foreach ( $this->terms as $term ) {
				// if the term already exists in the correct taxonomy leave it alone
				$term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
				if ( $term_id ) {
					if ( is_array( $term_id ) ) {
						$term_id = $term_id['term_id'];
					}
					if ( isset( $term['term_id'] ) ) {
						$this->processed_terms[ intval( $term['term_id'] ) ] = (int) $term_id;
					}
					continue;
				}

				if ( empty( $term['term_parent'] ) ) {
					$parent = 0;
				} else {
					$parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
					if ( is_array( $parent ) ) {
						$parent = $parent['term_id'];
					}
				}
				$description = isset( $term['term_description'] ) ? $term['term_description'] : '';
				$termarr     = array(
					'slug'        => $term['slug'],
					'description' => $description,
					'parent'      => intval( $parent ),
				);

				$id = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );
				if ( ! is_wp_error( $id ) ) {
					if ( isset( $term['term_id'] ) ) {
						$this->processed_terms[ intval( $term['term_id'] ) ] = $id['term_id'];
					}
				} else {
					printf( __( 'Failed to import %s %s', 'wordpress-importer' ), esc_html( $term['term_taxonomy'] ), esc_html( $term['term_name'] ) );
					if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
						echo ': ' . $id->get_error_message();
					}
					echo '<br />';
					continue;
				}
			}

			unset( $this->terms );
		}

		/**
		 * Create new posts based on import information
		 *
		 * Posts marked as having a parent which doesn't exist will become top level items.
		 * Doesn't create a new post if: the post type doesn't exist, the given post ID
		 * is already noted as imported or a post with the same title and date already exists.
		 * Note that new/updated terms, comments and meta are imported for the last of the above.
		 */
		function process_posts() {
			$this->posts = apply_filters( 'wp_import_posts', $this->posts );

			foreach ( $this->posts as $post ) {
				$post = apply_filters( 'wp_import_post_data_raw', $post );

				if ( ! post_type_exists( $post['post_type'] ) ) {
					printf( __( 'Failed to import &#8220;%s&#8221;: Invalid post type %s', 'wordpress-importer' ),
						esc_html( $post['post_title'] ), esc_html( $post['post_type'] ) );
					echo '<br />';
					do_action( 'wp_import_post_exists', $post );
					continue;
				}

				if ( isset( $this->processed_posts[ $post['post_id'] ] ) && ! empty( $post['post_id'] ) ) {
					continue;
				}

				if ( $post['status'] == 'auto-draft' ) {
					continue;
				}

				if ( 'nav_menu_item' == $post['post_type'] ) {
					$this->process_menu_item( $post );
					continue;
				}

				$post_type_object = get_post_type_object( $post['post_type'] );

				$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
				if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
					printf( __( '%s &#8220;%s&#8221; already exists.', 'wordpress-importer' ), $post_type_object->labels->singular_name, esc_html( $post['post_title'] ) );
					echo '<br />';
					$comment_post_ID = $post_id = $post_exists;
				} else {
					$post_parent = (int) $post['post_parent'];
					if ( $post_parent ) {
						// if we already know the parent, map it to the new local ID
						if ( isset( $this->processed_posts[ $post_parent ] ) ) {
							$post_parent = $this->processed_posts[ $post_parent ];
							// otherwise record the parent for later
						} else {
							$this->post_orphans[ intval( $post['post_id'] ) ] = $post_parent;
							$post_parent                                      = 0;
						}
					}

					// map the post author
					$author = sanitize_user( $post['post_author'], true );
					if ( isset( $this->author_mapping[ $author ] ) ) {
						$author = $this->author_mapping[ $author ];
					} else {
						$author = (int) get_current_user_id();
					}

					$postdata = array(
						'import_id'      => $post['post_id'],
						'post_author'    => $author,
						'post_date'      => $post['post_date'],
						'post_date_gmt'  => $post['post_date_gmt'],
						'post_content'   => $post['post_content'],
						'post_excerpt'   => $post['post_excerpt'],
						'post_title'     => $post['post_title'],
						'post_status'    => $post['status'],
						'post_name'      => $post['post_name'],
						'comment_status' => $post['comment_status'],
						'ping_status'    => $post['ping_status'],
						'guid'           => $post['guid'],
						'post_parent'    => $post_parent,
						'menu_order'     => $post['menu_order'],
						'post_type'      => $post['post_type'],
						'post_password'  => $post['post_password'],
					);

					$original_post_ID = $post['post_id'];
					$postdata         = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

					if ( 'attachment' == $postdata['post_type'] ) {
						$remote_url = ! empty( $post['attachment_url'] ) ? $post['attachment_url'] : $post['guid'];

						// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
						// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
						$postdata['upload_date'] = $post['post_date'];
						if ( isset( $post['postmeta'] ) ) {
							foreach ( $post['postmeta'] as $meta ) {
								if ( $meta['key'] == '_wp_attached_file' ) {
									if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) ) {
										$postdata['upload_date'] = $matches[0];
									}
									break;
								}
							}
						}

						$comment_post_ID = $post_id = $this->process_attachment( $postdata, $remote_url );
					} else {
						$comment_post_ID = $post_id = wp_insert_post( $postdata, true );
						do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );
					}

					if ( is_wp_error( $post_id ) ) {
						printf( __( 'Failed to import %s &#8220;%s&#8221;', 'wordpress-importer' ),
							$post_type_object->labels->singular_name, esc_html( $post['post_title'] ) );
						if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
							echo ': ' . $post_id->get_error_message();
						}
						echo '<br />';
						continue;
					}

					if ( $post['is_sticky'] == 1 ) {
						stick_post( $post_id );
					}
				}

				// map pre-import ID to local ID
				$this->processed_posts[ intval( $post['post_id'] ) ] = (int) $post_id;

				if ( ! isset( $post['terms'] ) ) {
					$post['terms'] = array();
				}

				$post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

				// add categories, tags and other terms
				if ( ! empty( $post['terms'] ) ) {
					$terms_to_set = array();
					foreach ( $post['terms'] as $term ) {
						// back compat with WXR 1.0 map 'tag' to 'post_tag'
						$taxonomy    = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
						$term_exists = term_exists( $term['slug'], $taxonomy );
						$term_id     = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
						if ( ! $term_id ) {
							$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
							if ( ! is_wp_error( $t ) ) {
								$term_id = $t['term_id'];
								do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
							} else {
								printf( __( 'Failed to import %s %s', 'wordpress-importer' ), esc_html( $taxonomy ), esc_html( $term['name'] ) );
								if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
									echo ': ' . $t->get_error_message();
								}
								echo '<br />';
								do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
								continue;
							}
						}
						$terms_to_set[ $taxonomy ][] = intval( $term_id );
					}

					foreach ( $terms_to_set as $tax => $ids ) {
						$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
						do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
					}
					unset( $post['terms'], $terms_to_set );
				}

				if ( ! isset( $post['comments'] ) ) {
					$post['comments'] = array();
				}

				$post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

				// add/update comments
				if ( ! empty( $post['comments'] ) ) {
					$num_comments      = 0;
					$inserted_comments = array();
					foreach ( $post['comments'] as $comment ) {
						$comment_id                                         = $comment['comment_id'];
						$newcomments[ $comment_id ]['comment_post_ID']      = $comment_post_ID;
						$newcomments[ $comment_id ]['comment_author']       = $comment['comment_author'];
						$newcomments[ $comment_id ]['comment_author_email'] = $comment['comment_author_email'];
						$newcomments[ $comment_id ]['comment_author_IP']    = $comment['comment_author_IP'];
						$newcomments[ $comment_id ]['comment_author_url']   = $comment['comment_author_url'];
						$newcomments[ $comment_id ]['comment_date']         = $comment['comment_date'];
						$newcomments[ $comment_id ]['comment_date_gmt']     = $comment['comment_date_gmt'];
						$newcomments[ $comment_id ]['comment_content']      = $comment['comment_content'];
						$newcomments[ $comment_id ]['comment_approved']     = $comment['comment_approved'];
						$newcomments[ $comment_id ]['comment_type']         = $comment['comment_type'];
						$newcomments[ $comment_id ]['comment_parent']       = $comment['comment_parent'];
						$newcomments[ $comment_id ]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
						if ( isset( $this->processed_authors[ $comment['comment_user_id'] ] ) ) {
							$newcomments[ $comment_id ]['user_id'] = $this->processed_authors[ $comment['comment_user_id'] ];
						}
					}
					ksort( $newcomments );

					foreach ( $newcomments as $key => $comment ) {
						// if this is a new post we can skip the comment_exists() check
						if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
							if ( isset( $inserted_comments[ $comment['comment_parent'] ] ) ) {
								$comment['comment_parent'] = $inserted_comments[ $comment['comment_parent'] ];
							}
							$comment                   = wp_filter_comment( $comment );
							$inserted_comments[ $key ] = wp_insert_comment( $comment );
							do_action( 'wp_import_insert_comment', $inserted_comments[ $key ], $comment, $comment_post_ID, $post );

							foreach ( $comment['commentmeta'] as $meta ) {
								$value = maybe_unserialize( $meta['value'] );
								add_comment_meta( $inserted_comments[ $key ], $meta['key'], $value );
							}

							$num_comments ++;
						}
					}
					unset( $newcomments, $inserted_comments, $post['comments'] );
				}

				if ( ! isset( $post['postmeta'] ) ) {
					$post['postmeta'] = array();
				}

				$post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

				// add/update post meta
				if ( ! empty( $post['postmeta'] ) ) {
					foreach ( $post['postmeta'] as $meta ) {
						$key   = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
						$value = false;

						if ( '_edit_last' == $key ) {
							if ( isset( $this->processed_authors[ intval( $meta['value'] ) ] ) ) {
								$value = $this->processed_authors[ intval( $meta['value'] ) ];
							} else {
								$key = false;
							}
						}

						if ( $key ) {
							// export gets meta straight from the DB so could have a serialized string
							if ( ! $value ) {
								$value = maybe_unserialize( $meta['value'] );
							}

							add_post_meta( $post_id, $key, $value );
							do_action( 'import_post_meta', $post_id, $key, $value );

							// if the post has a featured image, take note of this in case of remap
							if ( '_thumbnail_id' == $key ) {
								$this->featured_images[ $post_id ] = (int) $value;
							}
						}
					}
				}
			}

			unset( $this->posts );
		}

		/**
		 * Attempt to create a new menu item from import data
		 *
		 * Fails for draft, orphaned menu items and those without an associated nav_menu
		 * or an invalid nav_menu term. If the post type or term object which the menu item
		 * represents doesn't exist then the menu item will not be imported (waits until the
		 * end of the import to retry again before discarding).
		 *
		 * @param array $item Menu item details from WXR file
		 */
		function process_menu_item( $item ) {
			// skip draft, orphaned menu items
			if ( 'draft' == $item['status'] ) {
				return;
			}

			$menu_slug = false;
			if ( isset( $item['terms'] ) ) {
				// loop through terms, assume first nav_menu term is correct menu
				foreach ( $item['terms'] as $term ) {
					if ( 'nav_menu' == $term['domain'] ) {
						$menu_slug = $term['slug'];
						break;
					}
				}
			}

			// no nav_menu term associated with this menu item
			if ( ! $menu_slug ) {
				_e( 'Menu item skipped due to missing menu slug', 'wordpress-importer' );
				echo '<br />';

				return;
			}

			$menu_id = term_exists( $menu_slug, 'nav_menu' );
			if ( ! $menu_id ) {
				printf( __( 'Menu item skipped due to invalid menu slug: %s', 'wordpress-importer' ), esc_html( $menu_slug ) );
				echo '<br />';

				return;
			} else {
				$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
			}

			foreach ( $item['postmeta'] as $meta ) {
				$$meta['key'] = $meta['value'];
			}

			if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[ intval( $_menu_item_object_id ) ] ) ) {
				$_menu_item_object_id = $this->processed_terms[ intval( $_menu_item_object_id ) ];
			} else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[ intval( $_menu_item_object_id ) ] ) ) {
				$_menu_item_object_id = $this->processed_posts[ intval( $_menu_item_object_id ) ];
			} else if ( 'custom' != $_menu_item_type ) {
				// associated object is missing or not imported yet, we'll retry later
				$this->missing_menu_items[] = $item;

				return;
			}

			if ( isset( $this->processed_menu_items[ intval( $_menu_item_menu_item_parent ) ] ) ) {
				$_menu_item_menu_item_parent = $this->processed_menu_items[ intval( $_menu_item_menu_item_parent ) ];
			} else if ( $_menu_item_menu_item_parent ) {
				$this->menu_item_orphans[ intval( $item['post_id'] ) ] = (int) $_menu_item_menu_item_parent;
				$_menu_item_menu_item_parent                           = 0;
			}

			// wp_update_nav_menu_item expects CSS classes as a space separated string
			$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
			if ( is_array( $_menu_item_classes ) ) {
				$_menu_item_classes = implode( ' ', $_menu_item_classes );
			}

			$args = array(
				'menu-item-object-id'   => $_menu_item_object_id,
				'menu-item-object'      => $_menu_item_object,
				'menu-item-parent-id'   => $_menu_item_menu_item_parent,
				'menu-item-position'    => intval( $item['menu_order'] ),
				'menu-item-type'        => $_menu_item_type,
				'menu-item-title'       => $item['post_title'],
				'menu-item-url'         => $_menu_item_url,
				'menu-item-description' => $item['post_content'],
				'menu-item-attr-title'  => $item['post_excerpt'],
				'menu-item-target'      => $_menu_item_target,
				'menu-item-classes'     => $_menu_item_classes,
				'menu-item-xfn'         => $_menu_item_xfn,
				'menu-item-status'      => $item['status'],
			);

			$id = wp_update_nav_menu_item( $menu_id, 0, $args );
			if ( $id && ! is_wp_error( $id ) ) {
				$this->processed_menu_items[ intval( $item['post_id'] ) ] = (int) $id;
			}
		}

		/**
		 * If fetching attachments is enabled then attempt to create a new attachment
		 *
		 * @param array  $post Attachment post details from WXR
		 * @param string $url  URL to fetch attachment from
		 *
		 * @return int|WP_Error Post ID on success, WP_Error otherwise
		 */
		function process_attachment( $post, $url ) {
			if ( ! $this->fetch_attachments ) {
				return new WP_Error( 'attachment_processing_error',
					__( 'Fetching attachments is not enabled', 'wordpress-importer' ) );
			}

			// if the URL is absolute, but does not contain address, then upload it assuming base_site_url
			if ( preg_match( '|^/[\w\W]+$|', $url ) ) {
				$url = rtrim( $this->base_url, '/' ) . $url;
			}

			$upload = $this->fetch_remote_file( $url, $post );
			if ( is_wp_error( $upload ) ) {
				return $upload;
			}

			if ( $info = wp_check_filetype( $upload['file'] ) ) {
				$post['post_mime_type'] = $info['type'];
			} else {
				return new WP_Error( 'attachment_processing_error', __( 'Invalid file type', 'wordpress-importer' ) );
			}

			$post['guid'] = $upload['url'];

			// as per wp-admin/includes/upload.php
			$post_id = wp_insert_attachment( $post, $upload['file'] );
			wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

			// remap resized image URLs, works by stripping the extension and remapping the URL stub.
			if ( preg_match( '!^image/!', $info['type'] ) ) {
				$parts = pathinfo( $url );
				$name  = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

				$parts_new = pathinfo( $upload['url'] );
				$name_new  = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

				$this->url_remap[ $parts['dirname'] . '/' . $name ] = $parts_new['dirname'] . '/' . $name_new;
			}

			return $post_id;
		}

		/**
		 * Attempt to download a remote file attachment
		 *
		 * @param string $url  URL of item to fetch
		 * @param array  $post Attachment details
		 *
		 * @return array|WP_Error Local file location details on success, WP_Error otherwise
		 */
		function fetch_remote_file( $url, $post ) {
			// extract the file name and extension from the url
			$file_name = basename( $url );

			// get placeholder file in the upload dir with a unique, sanitized filename
			$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
			if ( $upload['error'] ) {
				return new WP_Error( 'upload_dir_error', $upload['error'] );
			}

			// fetch the remote url and write it to the placeholder file
			$headers = wp_get_http( $url, $upload['file'] );

			// request failed
			if ( ! $headers ) {
				@unlink( $upload['file'] );

				return new WP_Error( 'import_file_error', __( 'Remote server did not respond', 'wordpress-importer' ) );
			}

			// make sure the fetch was successful
			if ( $headers['response'] != '200' ) {
				@unlink( $upload['file'] );

				return new WP_Error( 'import_file_error', sprintf( __( 'Remote server returned error response %1$d %2$s', 'wordpress-importer' ), esc_html( $headers['response'] ), get_status_header_desc( $headers['response'] ) ) );
			}

			$filesize = filesize( $upload['file'] );

			if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
				@unlink( $upload['file'] );

				return new WP_Error( 'import_file_error', __( 'Remote file is incorrect size', 'wordpress-importer' ) );
			}

			if ( 0 == $filesize ) {
				@unlink( $upload['file'] );

				return new WP_Error( 'import_file_error', __( 'Zero size file downloaded', 'wordpress-importer' ) );
			}

			$max_size = (int) $this->max_attachment_size();
			if ( ! empty( $max_size ) && $filesize > $max_size ) {
				@unlink( $upload['file'] );

				return new WP_Error( 'import_file_error', sprintf( __( 'Remote file is too large, limit is %s', 'wordpress-importer' ), size_format( $max_size ) ) );
			}

			// keep track of the old and new urls so we can substitute them later
			$this->url_remap[ $url ]          = $upload['url'];
			$this->url_remap[ $post['guid'] ] = $upload['url']; // r13735, really needed?
			// keep track of the destination if the remote url is redirected somewhere else
			if ( isset( $headers['x-final-location'] ) && $headers['x-final-location'] != $url ) {
				$this->url_remap[ $headers['x-final-location'] ] = $upload['url'];
			}

			return $upload;
		}

		/**
		 * Attempt to associate posts and menu items with previously missing parents
		 *
		 * An imported post's parent may not have been imported when it was first created
		 * so try again. Similarly for child menu items and menu items which were missing
		 * the object (e.g. post) they represent in the menu
		 */
		function backfill_parents() {
			global $wpdb;

			// find parents for post orphans
			foreach ( $this->post_orphans as $child_id => $parent_id ) {
				$local_child_id = $local_parent_id = false;
				if ( isset( $this->processed_posts[ $child_id ] ) ) {
					$local_child_id = $this->processed_posts[ $child_id ];
				}
				if ( isset( $this->processed_posts[ $parent_id ] ) ) {
					$local_parent_id = $this->processed_posts[ $parent_id ];
				}

				if ( $local_child_id && $local_parent_id ) {
					$wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
				}
			}

			// all other posts/terms are imported, retry menu items with missing associated object
			$missing_menu_items = $this->missing_menu_items;
			foreach ( $missing_menu_items as $item ) {
				$this->process_menu_item( $item );
			}

			// find parents for menu item orphans
			foreach ( $this->menu_item_orphans as $child_id => $parent_id ) {
				$local_child_id = $local_parent_id = 0;
				if ( isset( $this->processed_menu_items[ $child_id ] ) ) {
					$local_child_id = $this->processed_menu_items[ $child_id ];
				}
				if ( isset( $this->processed_menu_items[ $parent_id ] ) ) {
					$local_parent_id = $this->processed_menu_items[ $parent_id ];
				}

				if ( $local_child_id && $local_parent_id ) {
					update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
				}
			}
		}

		/**
		 * Use stored mapping information to update old attachment URLs
		 */
		function backfill_attachment_urls() {
			global $wpdb;
			// make sure we do the longest urls first, in case one is a substring of another
			uksort( $this->url_remap, array( &$this, 'cmpr_strlen' ) );

			foreach ( $this->url_remap as $from_url => $to_url ) {
				// remap urls in post_content
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url ) );
				// remap enclosure urls
				$result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url ) );
			}
		}

		/**
		 * Update _thumbnail_id meta to new, imported attachment IDs
		 */
		function remap_featured_images() {
			// cycle through posts that have a featured image
			foreach ( $this->featured_images as $post_id => $value ) {
				if ( isset( $this->processed_posts[ $value ] ) ) {
					$new_id = $this->processed_posts[ $value ];
					// only update if there's a difference
					if ( $new_id != $value ) {
						update_post_meta( $post_id, '_thumbnail_id', $new_id );
					}
				}
			}
		}

		/**
		 * Parse a WXR file
		 *
		 * @param string $file Path to WXR file for parsing
		 *
		 * @return array Information gathered from the WXR file
		 */
		function parse( $file ) {
			$parser = new WXR_Parser();

			return $parser->parse( $file );
		}

		// Display import page title
		function header() {
			echo '<div class="wrap">';
			screen_icon();
			echo '<h2>' . __( 'Import WordPress', 'wordpress-importer' ) . '</h2>';

			$updates  = get_plugin_updates();
			$basename = plugin_basename( __FILE__ );
			if ( isset( $updates[ $basename ] ) ) {
				$update = $updates[ $basename ];
				echo '<div class="error"><p><strong>';
				printf( __( 'A new version of this importer is available. Please update to version %s to ensure compatibility with newer export files.', 'wordpress-importer' ), $update->update->new_version );
				echo '</strong></p></div>';
			}
		}

		// Close div.wrap
		function footer() {
			echo '</div>';
		}

		/**
		 * Display introductory text and file upload form
		 */
		function greet() {
			echo '<div class="narrow">';
			echo '<p>' . __( 'Howdy! Upload your WordPress eXtended RSS (WXR) file and we&#8217;ll import the posts, pages, comments, custom fields, categories, and tags into this site.', 'wordpress-importer' ) . '</p>';
			echo '<p>' . __( 'Choose a WXR (.xml) file to upload, then click Upload file and import.', 'wordpress-importer' ) . '</p>';
			wp_import_upload_form( 'admin.php?import=wordpress&amp;step=1' );
			echo '</div>';
		}

		/**
		 * Decide if the given meta key maps to information we will want to import
		 *
		 * @param string $key The meta key to check
		 *
		 * @return string|bool The key if we do want to import, false if not
		 */
		function is_valid_meta_key( $key ) {
			// skip attachment metadata since we'll regenerate it from scratch
			// skip _edit_lock as not relevant for import
			if ( in_array( $key, array(
				'_wp_attached_file',
				'_wp_attachment_metadata',
				'_edit_lock',
			) ) ) {
				return false;
			}

			return $key;
		}

		/**
		 * Decide whether or not the importer is allowed to create users.
		 * Default is true, can be filtered via import_allow_create_users
		 *
		 * @return bool True if creating users is allowed
		 */
		function allow_create_users() {
			return apply_filters( 'import_allow_create_users', true );
		}

		/**
		 * Decide whether or not the importer should attempt to download attachment files.
		 * Default is true, can be filtered via import_allow_fetch_attachments. The choice
		 * made at the import options screen must also be true, false here hides that checkbox.
		 *
		 * @return bool True if downloading attachments is allowed
		 */
		function allow_fetch_attachments() {
			return apply_filters( 'import_allow_fetch_attachments', true );
		}

		/**
		 * Decide what the maximum file size for downloaded attachments is.
		 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
		 *
		 * @return int Maximum attachment file size to import
		 */
		function max_attachment_size() {
			return apply_filters( 'import_attachment_size_limit', 0 );
		}

		/**
		 * Added to http_request_timeout filter to force timeout at 60 seconds during import
		 *
		 * @param int $val
		 *
		 * @return int 60
		 */
		function bump_request_timeout( $val ) {
			return 60;
		}

		// return the difference in length between two strings
		function cmpr_strlen( $a, $b ) {
			return strlen( $b ) - strlen( $a );
		}
	}

endif;