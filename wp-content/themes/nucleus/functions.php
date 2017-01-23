<?php
/**
 * Nucleus functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Nucleus
 * @author  8guild
 */

if ( ! defined( 'NUCLEUS_TEMPLATE_DIR' ) ) :
	/**
	 * Absolute path to theme directory
	 *
	 * @var string NUCLEUS_TEMPLATE_DIR
	 */
	define( 'NUCLEUS_TEMPLATE_DIR', get_template_directory() );
endif;

if ( ! defined( 'NUCLEUS_TEMPLATE_URI' ) ) :
	/**
	 * Theme full URI
	 *
	 * @var string NUCLEUS_TEMPLATE_URI
	 */
	define( 'NUCLEUS_TEMPLATE_URI', get_template_directory_uri() );
endif;

if ( ! defined( 'NUCLEUS_STYLESHEET_DIR' ) ) :
	/**
	 * Absolute path the the stylesheet directory
	 *
	 * @var string NUCLEUS_STYLESHEET_DIR
	 */
	define( 'NUCLEUS_STYLESHEET_DIR', get_stylesheet_directory() );
endif;

if ( ! defined( 'NUCLEUS_STYLESHEET_URI' ) ) :
	/**
	 * Stylesheet URI
	 *
	 * @var string NUCLEUS_STYLESHEET_URI
	 */
	define( 'NUCLEUS_STYLESHEET_URI', get_stylesheet_directory_uri() );
endif;

if ( ! defined( 'NUCLEUS_OPTIONS' ) ) :
	/**
	 * Nucleus theme options name
	 *
	 * @var string NUCLEUS_OPTIONS
	 */
	define( 'NUCLEUS_OPTIONS', 'nucleus_options' );
endif;

if ( ! defined( 'NUCLEUS_COMPILED' ) ) :
	/**
	 * Nucleus compiled SASS results option name
	 *
	 * @var string NUCLEUS_COMPILED
	 */
	define( 'NUCLEUS_COMPILED', 'nucleus_compiled' );
endif;

if ( ! defined( 'NUCLEUS_CATEGORY_TRANSIENT' ) ) :
	/**
	 * Transient name for counting categories
	 *
	 * @see nucleus_categorized_blog()
	 * @see nucleus_category_transient_flusher()
	 *
	 * @var string NUCLEUS_CATEGORY_TRANSIENT
	 */
	define( 'NUCLEUS_TRANSIENT_CATEGORIES', 'nucleus_categories' );
endif;

if ( ! defined( 'NUCLEUS_PAGE_SETTINGS' ) ) :
	/**
	 * Page Settings meta box name
	 *
	 * @see nucleus_get_page_setting()
	 * @see nucleus_add_meta_boxes()
	 *
	 * @var string NUCLEUS_PAGE_SETTINGS
	 */
	define( 'NUCLEUS_PAGE_SETTINGS', '_nucleus_page_settings' );
endif;

if ( ! isset( $content_width ) ) {
	/**
	 * Filter the template content width
	 *
	 * @param int $content_width Content width in pixels
	 */
	$content_width = apply_filters( 'nucleus_content_width', 1170 );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function nucleus_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Nucleus, use a find and replace
	 * to change 'nucleus' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'nucleus', NUCLEUS_TEMPLATE_DIR . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	/*
	 * Enable the custom logo for WP4.5+
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support( 'custom-logo' );

	/**
	 * Enable WooCommerce support
	 *
	 * @link https://docs.woothemes.com/document/third-party-custom-theme-compatibility/
	 */
	add_theme_support( 'woocommerce' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'nucleus' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'image',
		'gallery',
		'video',
		'audio',
		'quote',
		'link',
		'chat',
	) );
}

add_action( 'after_setup_theme', 'nucleus_setup' );

/**
 * Enqueue scripts and styles.
 */
function nucleus_scripts() {

	// Google Fonts
	if ( nucleus_is_google_fonts() ) {
		$body = nucleus_get_option( 'typography_font_for_body', '//fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,600,600italic,700' );
		if ( ! empty( $body ) ) {
			wp_enqueue_style( 'nucleus-body-font', nucleus_google_font_url( $body ), array(), null, 'screen' );
		}

		$headings = nucleus_get_option( 'typography_font_for_headings' );
		if ( ! empty( $headings ) ) {
			wp_enqueue_style( 'nucleus-headings-font', nucleus_google_font_url( $headings ), array(), null );
		}
		unset( $body, $headings );
	}

	// font icons
	if ( ! wp_style_is( 'font-awesome' ) ) {
		wp_enqueue_style( 'font-awesome', NUCLEUS_TEMPLATE_URI . '/css/vendor/font-awesome.min.css', array(), null, 'screen' );
	}

	if ( ! wp_style_is( 'feather' ) ) {
		wp_enqueue_style( 'feather', NUCLEUS_TEMPLATE_URI . '/css/vendor/feather.min.css', array(), null, 'screen' );
	}

	if ( ! wp_style_is( 'flaticon' ) ) {
		wp_enqueue_style( 'flaticon', NUCLEUS_TEMPLATE_URI . '/css/vendor/flaticon.min.css', array(), null, 'screen' );
	}

	// styles
	//wp_enqueue_style( 'source-sans-pro', '//fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,600,600italic,700', array(), null, 'screen' );
	wp_enqueue_style( 'bootstrap', NUCLEUS_TEMPLATE_URI . '/css/vendor/bootstrap.min.css', array(), null, 'screen' );
	wp_enqueue_style( 'nucleus', nucleus_stylesheet_uri(), array(), null, 'screen' );

	// scripts
	wp_enqueue_script( 'modernizr', NUCLEUS_TEMPLATE_URI . '/js/vendor/modernizr.custom.js', array(), null );
	wp_enqueue_script( 'detectizer', NUCLEUS_TEMPLATE_URI . '/js/vendor/detectizr.min.js', array(), null );

	wp_enqueue_script( 'bootstrap', NUCLEUS_TEMPLATE_URI . '/js/vendor/bootstrap.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'velocity', NUCLEUS_TEMPLATE_URI . '/js/vendor/velocity.min.js', array(), null, true );
	wp_enqueue_script( 'waves', NUCLEUS_TEMPLATE_URI . '/js/vendor/waves.min.js', array(), null, true );
	wp_enqueue_script( 'jquery-easing', NUCLEUS_TEMPLATE_URI . '/js/vendor/jquery.easing.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'icheck', NUCLEUS_TEMPLATE_URI . '/js/vendor/icheck.min.js', array( 'jquery' ), null, true );

	if ( ! wp_script_is( 'waypoints' ) ) {
		wp_enqueue_script( 'waypoints', NUCLEUS_TEMPLATE_URI . '/js/vendor/waypoints.min.js', array(), null, true );
	}

	// @see template-parts/blog-no-sidebar.php
	if ( ! wp_script_is( 'isotope' ) ) {
		wp_enqueue_script( 'isotope', NUCLEUS_TEMPLATE_URI . '/js/vendor/isotope.pkgd.min.js', array(), null, true );
	}

	if ( nucleus_is_preloader() ) {
		wp_enqueue_script( 'preloader', NUCLEUS_TEMPLATE_URI . '/js/vendor/preloader.min.js', array( 'jquery' ), null, true );
	}

	// @see vc_row
	if ( is_singular() && nucleus_is_parallax() ) {
		wp_enqueue_script( 'stellar', NUCLEUS_TEMPLATE_URI . '/js/vendor/jquery.stellar.min.js', array( 'jquery' ), null, true );
	}

	// @see single-nucleus_slideshow.php
	if ( is_single() && nucleus_is_slideshow() ) {
		wp_enqueue_script( 'velocity-ui', NUCLEUS_TEMPLATE_URI . '/js/vendor/velocity.ui.min.js', null, null, true );
		wp_enqueue_script( 'scroll-effect', NUCLEUS_TEMPLATE_URI . '/js/vendor/scroll-effect.js', array(
			'jquery', 'velocity', 'velocity-ui'
		), null, true );
	}

	wp_enqueue_script( 'nucleus', NUCLEUS_TEMPLATE_URI . '/js/scripts.js', array( 'jquery' ), null, true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	
	// @see woocommerce/shop/filters.php
	if ( nucleus_is_woocommerce()
	     && is_shop()
	     && ! wp_script_is( 'wc-price-slider' )
	     && 'no-sidebar' === nucleus_get_option( 'layout_shop', 'no-sidebar' )
	) {
		wp_register_script( 'wc-jquery-ui-touchpunch', WC()->plugin_url() . '/assets/js/jquery-ui-touch-punch/jquery-ui-touch-punch.min.js', array( 'jquery-ui-slider' ), null, true );
		wp_enqueue_script( 'wc-price-slider', WC()->plugin_url() . '/assets/js/frontend/price-slider.min.js', array( 'jquery-ui-slider', 'wc-jquery-ui-touchpunch' ), null, true );
		wp_localize_script( 'wc-price-slider', 'woocommerce_price_slider_params', array(
			'currency_symbol' 	=> get_woocommerce_currency_symbol(),
			'currency_pos'      => get_option( 'woocommerce_currency_pos' ),
			'min_price'			=> isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '',
			'max_price'			=> isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : ''
		) );
	}
}

add_action( 'wp_enqueue_scripts', 'nucleus_scripts' );

/**
 * Enqueue font icons on admin screens
 */
function nucleus_admin_scripts() {
	wp_enqueue_style( 'font-awesome', NUCLEUS_TEMPLATE_URI . '/css/vendor/font-awesome.min.css', array(), null );
	wp_enqueue_style( 'feather', NUCLEUS_TEMPLATE_URI . '/css/vendor/feather.min.css', array(), null );
	wp_enqueue_style( 'flaticon', NUCLEUS_TEMPLATE_URI . '/css/vendor/flaticon.min.css', array(), null );
}

add_action( 'admin_enqueue_scripts', 'nucleus_admin_scripts' );

/**
 * One click demo import
 */
function nucleus_importer_init() {
	if ( ! class_exists( 'Nucleus_Importer' ) ) {
		return;
	}

	$settings = array(
		'page_title'         => esc_html__( 'Import Demo', 'nucleus' ),
		'menu_title'         => esc_html__( 'Import Demo', 'nucleus' ),
		'menu_slug'          => 'nucleus-import',
		'nonce'              => 'nucleus_import',
		'nonce_field'        => 'nucleus_import_nonce',
		'import_id'          => 0, // do not change
		'import_attachments' => true, // bool
		'variants'           => array(
			'nucleus' => array(
				'preview' => '',
				'title'   => esc_html__( 'Nucleus', 'nucleus' ),
				'xml'     => NUCLEUS_TEMPLATE_DIR . '/demo/demo.xml',
				'extra'   => NUCLEUS_TEMPLATE_DIR . '/demo/extra.json',
			)
		),
	);

	$importer = new Nucleus_Importer( $settings ) ;
}

add_action( 'init', 'nucleus_importer_init' );

/**
 * Register the required plugins for this theme.
 *
 * @uses tgmpa()
 */
function nucleus_tgm_init() {
	$dir = wp_normalize_path( NUCLEUS_STYLESHEET_DIR . '/plugins' );

	$plugins = array(
		array(
			'name'               => esc_html__( 'Nucleus Core', 'nucleus' ),
			'slug'               => 'nucleus-core',
			'source'             => $dir . '/nucleus-core.zip',
			'version'            => '1.0.3',
			'required'           => true,
			'force_activation'   => true,
			'force_deactivation' => false,
		),
		array(
			'name'               => esc_html__( 'Visual Composer', 'nucleus' ),
			'slug'               => 'js_composer',
			'source'             => $dir . '/js_composer.zip',
			'version'            => '4.12',
			'required'           => true,
			'force_activation'   => true,
			'force_deactivation' => false,
		),
		array(
			'name'               => esc_html__( 'Equip', 'nucleus' ),
			'slug'               => 'equip',
			'source'             => $dir . '/equip.zip',
			'version'            => '0.7.3',
			'required'           => true,
			'force_activation'   => true,
			'force_deactivation' => false,
		),
		array(
			'name'     => esc_html__( 'Equip SASS Compiler', 'nucleus' ),
			'slug'     => 'equip-sass-compiler',
			'source'   => $dir . '/equip-sass-compiler.zip',
			'version'  => '0.1.0',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Master Slider', 'nucleus' ),
			'slug'     => 'masterslider',
			'source'   => $dir . '/masterslider.zip',
			'version'  => '3.0.3',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'WooCommerce', 'nucleus' ),
			'slug'     => 'woocommerce',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Contact Form 7', 'nucleus' ),
			'slug'     => 'contact-form-7',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Breadcrumb NavXT', 'nucleus' ),
			'slug'     => 'breadcrumb-navxt',
			'required' => false,
		),
	);

	$config = array(
		'id'           => 'nucleus-tgm',
		'menu'         => 'nucleus-plugins',
		'has_notices'  => true,
		'dismissable'  => true,
		'is_automatic' => true,
	);

	tgmpa( $plugins, $config );
}

add_action( 'tgmpa_register', 'nucleus_tgm_init' );

/**
 * Add the TGM_Plugin_Activation class
 */
require_once NUCLEUS_TEMPLATE_DIR . '/vendor/tgm/class-tgm-plugin-activation.php';

/**
 * Theme helpers and utilities
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/helpers.php';

/**
 * Custom template tags for this theme
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/extras.php';

/**
 * The parts of theme
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/structure.php';

/**
 * Customizer additions
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/customizer.php';

/**
 * Theme widgets
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/widgets.php';

/**
 * Menu customizations
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/menus.php';

/**
 * Theme Options
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/options.php';

/**
 * Theme meta boxes
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/meta-boxes.php';

/**
 * Theme comments customizations
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/comments.php';

/**
 * Visual Composer additions
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/vc.php';

/**
 * WooCommerce
 */
require NUCLEUS_TEMPLATE_DIR . '/inc/woocommerce.php';
