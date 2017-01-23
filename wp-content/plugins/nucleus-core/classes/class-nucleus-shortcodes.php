<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Shortcodes
 *
 * @author 8guild
 */
class Nucleus_Shortcodes {
	/**
	 * @var null|Nucleus_Shortcodes Instance of class
	 */
	private static $instance;

	/**
	 * @var array List of shortcodes
	 */
	private $shortcodes = array();

	/**
	 * Initialization
	 *
	 * @param array $files A list of file names and path to shortcode output template
	 *
	 * @return Nucleus_Shortcodes
	 */
	public static function init( $files ) {
		if ( null === self::$instance ) {
			self::$instance = new self( $files );
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @param array $files A list of file names and path to shortcode output template
	 */
	private function __construct( $files ) {
		/**
		 * Filter the shortcodes list. The best place to add or remove shortcode(s).
		 *
		 * @since 1.0.0
		 *
		 * @param array $shortcodes Shortcodes list
		 */
		$this->shortcodes = apply_filters( 'nucleus_shortcodes_list', array_keys( $files ) );

		// add shortcodes
		foreach( $this->shortcodes as $shortcode ) {
			add_shortcode( $shortcode, array( $this, 'render' ) );
		}
	}

	/**
	 * Get shortcode output
	 *
	 * @param array       $atts      Shortcode attributes
	 * @param string|null $content   Shortcode content
	 * @param string      $shortcode Shortcode tag
	 *
	 * @return string Shortcode HTML
	 */
	public function render( $atts = array(), $content = null, $shortcode = '' ) {
		// shortcode output
		$_html = '';

		// custom fonts
		$this->maybe_enqueue_font( $atts, $shortcode );

		// check cache first
		$is_cache  = (bool) absint( nucleus_get_option( 'cache_is_shortcodes', 1 ) );
		$cache_key = nucleus_shortcode_cache_key( $shortcode, $atts, $content );
		if ( $is_cache ) {
			$cache_value = get_transient( $cache_key );
			if ( false !== $cache_value ) {
				return nucleus_content_decode( $cache_value );
			}
		}

		/**
		 * Filter the list of directories with shortcode templates
		 *
		 * @param array $dirs Directories list
		 */
		$dirs = apply_filters( 'nucleus_shortcodes_dirs', array(
			get_stylesheet_directory() . '/shortcodes',
			get_template_directory() . '/shortcodes',
			NUCLEUS_CORE_ROOT . '/shortcodes',
		) );


		foreach ( $dirs as $path ) {
			$template = "{$path}/{$shortcode}.php";
			if ( ! file_exists( $template ) ) {
				continue;
			}

			ob_start();
			require $template;
			$_html = ob_get_contents();
			ob_end_clean();

			unset( $template );

			// break loop after first found template
			break;
		}

		// set cache
		if ( $is_cache ) {
			// cache for 1 day
			$cache_value = nucleus_content_encode( $_html );
			set_transient( $cache_key, $cache_value, 86400 );
		}

		return $_html;
	}

	/**
	 * Enqueue the custom fonts
	 *
	 * @param array  $atts      Shortcode attributes
	 * @param string $shortcode Shortcode attributes
	 */
	public function maybe_enqueue_font( $atts, $shortcode ) {
		if ( ! function_exists( 'vc_icon_element_fonts_enqueue' ) ) {
			return;
		}

		$atts = (array) $atts;
		if ( ! array_key_exists( 'icon_library', $atts ) ) {
			return;
		}

		vc_icon_element_fonts_enqueue( $atts['icon_library'] );
	}
}
