<?php
/**
 * Plugin Name: Nucleus Core
 * Plugin URI: http://themes.8guild.com/nucleus/
 * Description: Core functionality for 8guild's Nucleus Theme
 * Version: 1.0.3
 * Author: 8guild
 * Author URI: http://8guild.com
 * License: GPLv2 or later
 * Text Domain: nucleus
 *
 * @author  8guild
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**#@+
 * Plugin constants
 *
 * @since 1.0.0
 */
define( 'NUCLEUS_CORE_FILE', __FILE__ );
define( 'NUCLEUS_CORE_ROOT', untrailingslashit( plugin_dir_path( NUCLEUS_CORE_FILE ) ) );
define( 'NUCLEUS_CORE_URI', plugins_url( '/assets', NUCLEUS_CORE_FILE ) );
/**#@-*/

/**
 * Load core classes according to WordPress naming conventions.
 *
 * @param string $class Class name
 *
 * @link  https://make.wordpress.org/core/handbook/coding-standards/php/#naming-conventions
 *
 * @return bool
 */
function nucleus_core_loader( $class ) {
	if ( false === stripos( $class, 'Nucleus_' ) ) {
		// call next loader
		return true;
	}

	$chunks = array_filter( explode( '_', strtolower( $class ) ) );
	$root   = NUCLEUS_CORE_ROOT;
	$subdir = '/classes/';

	$file = 'class-' . implode( '-', $chunks ) . '.php';
	$path = wp_normalize_path( $root . $subdir . $file );
	if ( is_readable( $path ) ) {
		require $path;
	}

	return true;
}

spl_autoload_register( 'nucleus_core_loader' );

/**
 * Plugin textdomain
 */
function nucleus_core_textdomain() {
	load_plugin_textdomain( 'nucleus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', 'nucleus_core_textdomain' );

/**
 * Load custom post types
 */
function nucleus_core_cpt() {
	$loader = Nucleus_Cpt_Loader::instance();

	$path  = wp_normalize_path( NUCLEUS_CORE_ROOT . '/cpt' );
	$files = nucleus_get_dir_contents( $path );

	$loader->init( $files );
}

add_action( 'plugins_loaded', 'nucleus_core_cpt' );

/**
 * Callback for {@see register_activation_hook()}
 *
 * Flushing the rewrite rules
 */
function nucleus_core_activation() {
	$loader = Nucleus_Cpt_Loader::instance();

	$path  = wp_normalize_path( NUCLEUS_CORE_ROOT . '/cpt' );
	$files = nucleus_get_dir_contents( $path );

	$loader->register( $files );
	flush_rewrite_rules();
}

register_activation_hook( NUCLEUS_CORE_FILE, 'nucleus_core_activation' );

/**
 * Init the shortcodes
 */
function nucleus_core_shortcodes() {
	// collect all shortcodes
	$path  = wp_normalize_path( NUCLEUS_CORE_ROOT . '/shortcodes' );
	$files = nucleus_get_dir_contents( $path );

	Nucleus_Shortcodes::init( $files );
}

add_action( 'init', 'nucleus_core_shortcodes' );

/**
 * Enqueue scripts and styles on front-end
 *
 * Callback for "wp_enqueue_scripts"
 */
function nucleus_core_front_scripts() {
	// for animations
	if ( is_singular() && nucleus_is_animation() ) {
		wp_enqueue_script( 'scroll-reveal', NUCLEUS_CORE_URI . '/js/scrollreveal.min.js', array(), null, true );
	}

	// remove isotope, registered by VC
	if ( wp_script_is( 'isotope', 'registered' ) ) {
		wp_deregister_script( 'isotope' );
	}

	// remove waypoints, registered by VC
	if ( wp_script_is( 'waypoints', 'registered' ) ) {
		wp_deregister_script( 'waypoints' );
	}

	// required for Google Map shortcode
	wp_register_script( 'google-map-api', '//maps.googleapis.com/maps/api/js?key=AIzaSyA5DLwPPVAz88_k0yO2nmFe7T9k1urQs84', null, null );

	wp_enqueue_script( 'owl-carousel', NUCLEUS_CORE_URI . '/js/owl.carousel.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'isotope', NUCLEUS_CORE_URI . '/js/isotope.pkgd.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'magnific-popup', NUCLEUS_CORE_URI . '/js/magnific-popup.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'spincrement', NUCLEUS_CORE_URI . '/js/jquery.spincrement.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'waypoints', NUCLEUS_CORE_URI . '/js/waypoints.min.js', array(), null, true );
	wp_enqueue_script( 'down-count', NUCLEUS_CORE_URI . '/js/jquery.downCount.js', array( 'jquery' ), null, true );

	if ( nucleus_is_map() ) {
		wp_enqueue_script( 'gmap3', NUCLEUS_CORE_URI . '/js/gmap3.min.js', array( 'google-map-api' ), null, true );
	}

	wp_enqueue_script( 'nucleus-core', NUCLEUS_CORE_URI . '/js/nucleus-core.js', array( 'jquery' ), null, true );

	// nonce and ajaxurl for AJAX calls
	// attach to "jQuery" because I need this variable in header
	wp_localize_script( 'jquery', 'nucleus', array(
		'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
		'nonce'   => wp_create_nonce( 'nucleus-core-ajax' ),
		'storage' => array(
			'grids'      => array(),
			'filters'    => array(),
			'containers' => array(),
		),
	) );
}

add_action( 'wp_enqueue_scripts', 'nucleus_core_front_scripts' );

function nucleus_core_admin_scripts() {
	wp_enqueue_style( 'nucleus', NUCLEUS_CORE_URI . '/css/admin.css', array(), null );
}

add_action( 'admin_enqueue_scripts', 'nucleus_core_admin_scripts' );

/*
 * Load helpers functions
 */
require NUCLEUS_CORE_ROOT . '/inc/helpers.php';

/*
 * Visual Composer custom shortcodes mapping
 */
require NUCLEUS_CORE_ROOT . '/inc/vc-map.php';

/*
 * Custom actions
 */
require NUCLEUS_CORE_ROOT . '/inc/actions.php';
