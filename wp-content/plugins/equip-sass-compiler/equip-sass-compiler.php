<?php
/**
 * Equip SASS Compiler
 *
 * Plugin Name: Equip SASS compiler
 * Plugin URI:  http://8guild.com
 * Description: A lightweight SASS compiler for Equip
 * Version:     0.1.0
 * Author:      8guild
 * Author URI:  http://8guild.com
 * Text Domain: equip-sass-compiler
 * License:     GPL3+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: languages
 *
 * @package Equip
 * @author  8guild <info@8guild.com>
 * @license GNU General Public License, version 3
 *
 * @wordpress-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// include the Leafo ScssPhp compiler
require __DIR__ . '/vendor/scssphp/scss.inc.php';

/**
 * Extension for compiling the SASS
 *
 * @author  8guild
 * @package Equip\Extensions
 */
class Equip_SASS_Compiler {

	/**
	 * @var Equip_SASS_Compiler|null
	 */
	private static $instance = null;

	/**
	 * Return the instance
	 *
	 * @return Equip_SASS_Compiler|null
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function setup() {
		add_action( 'init', array( $this, 'textdomain' ) );
		add_action( 'equip/options/saved', array( $this, 'compile' ), 10, 3 );
		add_action( 'equip/options/reseted', array( $this, 'reset' ), 10, 2 );
	}

	/**
	 * Load plugin translation
	 *
	 * @wp-hook init
	 * @see     setup()
	 */
	public function textdomain() {
		load_plugin_textdomain( 'nucleus', false, plugin_dir_path( __FILE__ ) . 'languages' );
	}

	/**
	 * Compile the SASS
	 *
	 * @param string                     $slug      Element name
	 * @param mixed                      $sanitized Already sanitized and updated values
	 * @param \Equip\Misc\StorageElement $element   Storage element
	 *
	 * @return bool
	 */
	public function compile( $slug, $sanitized, $element ) {
		/**
		 * This filter allows to disable SASS compiling in a child theme
		 *
		 * Default is FALSE which means "Do not disable SASS compilation"
		 *
		 * @example add_filter( 'equip/sass/disable', '__return_true' );
		 *
		 * @params  bool $is_compile Enable or disable compilation
		 */
		if ( true === apply_filters( 'equip/sass/disable', false ) ) {
			return false;
		}
		
		// get SASS settings
		$settings = $this->settings( $element->getArgs() );
		$root     = trailingslashit( untrailingslashit( $settings['root'] ) );

		$uploads = wp_upload_dir();
		if ( false !== $uploads['error'] || ! wp_is_writable( $uploads['basedir'] ) ) {
			// TODO: write error to log
			return false;
		}

		$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, array() );
		if ( ! WP_Filesystem( $creds ) ) {
			return false;
		}

		/** @var WP_Filesystem_Direct $wp_filesystem */
		global $wp_filesystem;

		// get fields
		$fields = equip_layout_get_fields( $element->getLayout() );

		// get options with "sass" param
		$relations = $this->get_relations( $fields );
		if ( empty( $relations ) ) {
			return false;
		}

		$defaults = $this->get_defaults( $fields );
		if ( ! $this->is_compile( $sanitized, $defaults ) ) {
			return false;
		}

		// the SASS code
		$sass = '';

		// prepare and parse SASS variables
		foreach( $settings['variables'] as $v ) {
			if ( ! file_exists( $root . $v ) ) {
				continue;
			}
			$c = $wp_filesystem->get_contents( $root . $v );
			$sass .= $this->parse_variables( $c, $sanitized, $relations );
			unset( $c );
		}
		unset( $v );

		// get SASS code from other files
		foreach( $settings['files'] as $f ) {
			if ( ! file_exists( $root . $f ) ) {
				continue;
			}

			$sass .= $wp_filesystem->get_contents( $root . $f );
		}
		unset( $f );
		
		// prepare FS
		$subdir = trailingslashit( untrailingslashit( $settings['subdir'] ) );
		$file   = $settings['file'];
		$path   = wp_normalize_path( $uploads['basedir'] . DIRECTORY_SEPARATOR . $subdir );
		$uri    = $uploads['baseurl'] . '/' . $subdir;

		if ( ! $wp_filesystem->exists( $path ) ) {
			$wp_filesystem->mkdir( $path, FS_CHMOD_DIR );
		}

		try {
			$scss = new Leafo\ScssPhp\Compiler();
			$scss->setFormatter( 'Leafo\ScssPhp\Formatter\Crunched' ); // TODO: formatter to options

			// TODO: import path may be different, may be add a filter?
			$scss->addImportPath( function ( $path ) use ( $root ) {
				if ( strpos( $path, '/' ) && false === stripos( $path, '.scss' ) ) {
					// for subdirectories, like bootstrap, but not for files in the subdir
					$chunks = explode( '/', $path );
					$last   = array_pop( $chunks );
					$last   = '_' . $last . '.scss';
					$path   = implode( '/', $chunks ) . '/' . $last;
				} elseif ( false === stripos( $path, '.scss' ) ) {
					// load normal file in /sass dir
					$path = '_' . $path . '.scss'; // convert to _{$path}.scss
				}

				$path = realpath( $root . DIRECTORY_SEPARATOR . $path );
				if ( file_exists( $path ) ) {
					return $path;
				}

				return null;
			} );

			$css = $scss->compile( $sass );
			$wp_filesystem->put_contents( $path . $file, $css, FS_CHMOD_FILE );

			$result = array( 'path' => $path . $file, 'url' => $uri . $file );
		} catch ( Exception $e ) {
			trigger_error( $e->getMessage() );
			$result = false;
		}

		if ( false === get_option( $settings['option'] ) ) {
			add_option( $settings['option'], '', '', 'no' );
		}

		// store to the database
		update_option( $settings['option'], $result, 'no' );

		return true;
	}

	/**
	 * Reset the compiled option
	 *
	 * @param string                     $slug    Element name
	 * @param \Equip\Misc\StorageElement $element Storage element
	 *
	 * @return bool
	 */
	public function reset( $slug, $element ) {
		// get SASS settings
		$settings = $this->settings( $element->getArgs() );

		$c = get_option( $settings['option'] );
		if ( is_array( $c )
		     && array_key_exists( 'path', $c )
		     && file_exists( $c['path'] )
		) {
			unlink( $c['path'] );
		}

		return delete_option( $settings['option'] );
	}

	/**
	 * Returns the prepared SASS variables.
	 *
	 * Parse the variables and combine those variables
	 * with the values from the theme options.
	 *
	 * Define the relations between SASS variables and options from the admin.
	 * The format is important! Because we store in database only the,
	 * e.g. number without "px" etc. You can prepend or append something to the value.
	 *
	 * If extension not needed just set to NULL.
	 *
	 * @example
	 *
	 * in SASS:
	 * $gray-darker: #000;
	 *
	 * defined as:
	 * [ key = 'color_gray_darker', sass = 'gray-darker' ]
	 *
	 * @example
	 *
	 * in SASS:
	 * $font-size-lead: floor((16px * 1.125));
	 *
	 * defined as:
	 * [key = typography_lead_font_size, sass = [prepend = 'floor((', var = 'font-size-lead', append = 'px * 1.125))']]
	 *
	 * @param string $content   A content of "variable.scss" file
	 * @param array  $options   Array of theme options, already sanitized
	 * @param array  $relations Array of theme options
	 *
	 * @return array|string
	 */
	public function parse_variables( $content, $options, $relations ) {
		if ( empty( $content ) ) {
			return '';
		}

		preg_match_all( "/\\$(.*?):(.*?);/m", $content, $match );
		if ( empty( $match[1] ) || empty( $match[2] ) ) {
			// may be broken _variables.scss, return as is
			return $content;
		}

		// build a key-value pairs of variables from "variables".scss
		$variables = array_combine( $match[1], $match[2] );
		unset( $match );

		$real = [];
		foreach ( $variables as $var => $val ) {
			// if option not present in $relations array no need to change its value
			if ( ! array_key_exists( $var, $relations ) ) {
				$real[ $var ] = trim( $val );

				continue;
			}

			// define the option name, format is important! And get the option value
			$option = $relations[ $var ]['option'];
			if ( array_key_exists( $option, $options ) && ! empty( $options[ $option ] ) ) {
				$prepend = $relations[ $var ]['prepend'];
				$append  = $relations[ $var ]['append'];
				$value   = $prepend . $options[ $option ] . $append;

				$real[ $var ] = $value;
				unset( $prepend, $value, $append );
			} else {
				$real[ $var ] = trim( $val );
			}
			unset( $option );
		}
		unset( $var, $val );

		// Prepare SASS variables
		$sass_vars = array();
		foreach( $real as $var => $val ) {
			$sass_vars[] = sprintf( '$%1$s: %2$s;', $var, $val );
		}
		unset( $var, $val );

		return implode( PHP_EOL, $sass_vars );
	}

	/**
	 * Get SASS variable to Theme Options relations
	 *
	 * in format [variable => relation],
	 * where `variable` is a SASS variable name, and
	 * relation is an associative arrays with extended
	 * info about the variable and related option name
	 *
	 * @param array $fields Fields
	 *
	 * @return array
	 */
	public function get_relations( $fields ) {
		$relations = [];

		/** @var \Equip\Layout\FieldLayout $field */
		foreach ( $fields as $key => $field ) {
			$sass = $field->get_setting( 'sass' );
			if ( empty( $sass ) ) {
				continue;
			}

			if ( is_array( $sass ) ) {
				if ( empty( $sass['var'] ) ) {
					continue;
				}

				$var = $sass['var'];

				$relations[ $var ]['option']  = $key;
				$relations[ $var ]['prepend'] = array_key_exists( 'prepend', $sass ) ? $sass['prepend'] : null;
				$relations[ $var ]['append']  = array_key_exists( 'append', $sass ) ? $sass['append'] : null;
			} else {
				$relations[ $sass ] = [
					'prepend' => null,
					'option'  => $key,
					'append'  => null,
				];
			}
		}

		return $relations;
	}

	/**
	 * Get default values for fields in format [key => default value]
	 *
	 * @param array $fields Fields
	 *
	 * @return array
	 */
	public function get_defaults( $fields ) {
		$defaults = [];

		/** @var \Equip\Layout\FieldLayout $field */
		foreach ( $fields as $key => $field ) {
			$sass = $field->get_setting( 'sass' );
			if ( empty( $sass ) ) {
				continue;
			}

			$defaults[ $key ] = $field->get_setting( 'default', null );
		}

		return $defaults;
	}

	/**
	 * Check if compilation is required
	 *
	 * This method will check if the values of options
	 * used in SASS compilation differs from their defaults
	 *
	 * @param array $options Saved theme options
	 * @param array $defaults Default values
	 *
	 * @return bool
	 */
	public function is_compile( $options, $defaults ) {
		/**
		 * Get only those options that are affected the front-end
		 * and required for SASS compiling.
		 *
		 * @var array All settings affecting the front end
		 */
		$customizable = array_intersect_key( $options, $defaults );

		// just in case
		ksort( $defaults, SORT_STRING );
		ksort( $customizable, SORT_STRING );

		// As compiling sass is a very resource intensive operation,
		// check if any options that are affect the front-end was changed.

		$affected = array();
		array_walk( $customizable, function ( $value, $key, $defaults ) use ( &$affected ) {
			// non-strict comparison
			if ( array_key_exists( $key, $defaults ) && $value != $defaults[ $key ] ) {
				$affected[ $key ] = $value;
			}
		}, $defaults );

		return ( count( $affected ) > 0 );
	}

	/**
	 * Get SASS settings
	 * 
	 * @param array $args Arguments
	 *
	 * @return array
	 */
	public function settings( $args ) {
		if ( empty( $args['sass'] ) ) {
			return [];
		}

		return wp_parse_args( $args['sass'], [
			'root'      => '', // absolute path to "sass" dir
			'variables' => [], // path to "variables" files
			'files'     => [], // path to .scss files, order is necessary
			'subdir'    => '',
			'file'      => 'compiled.css',
			'option'    => 'equip_compiled',
		] );
	}
}

add_action( 'plugins_loaded', array( Equip_SASS_Compiler::instance(), 'setup' ) );
