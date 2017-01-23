<?php
/**
 * Menu customization
 *
 * @author  8guild
 * @package Nucleus
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Add custom fields into the menu items
 */
function nucleus_add_extra_menu_fields() {
	try {
		$layout = equip_create_menu_layout();
		$layout
			->add_field( 'type', 'select', array(
				'label'       => esc_html__( 'Choose the icon type', 'nucleus' ),
				'description' => esc_html__( 'Choose one from available packs or use the images', 'nucleus' ),
				'default'     => 'font_awesome',
				'options'     => array(
					'font_awesome' => esc_html__( 'Font Awesome', 'nucleus' ),
					'feather'      => esc_html__( 'Feather Icons', 'nucleus' ),
					'flaticon'     => esc_html__( 'Flaticons', 'nucleus' ),
					'image'        => esc_html__( 'Image', 'nucleus' ),
				),
			) )
			->add_field( 'icon_font_awesome', 'icon', array(
				'label'       => esc_html__( 'Font Awesome', 'nucleus' ),
				'description' => esc_html__( 'Choose the icon from the Font Awesome pack', 'nucleus' ),
				'source'      => 'fontawesome',
				'settings'    => array( 'iconsPerPage' => 100 ),
				'required'    => array( 'type', '=', 'font_awesome' ),
			) )
			->add_field( 'icon_feather', 'icon', array(
				'label'       => esc_html__( 'Feather', 'nucleus' ),
				'description' => esc_html__( 'Choose the icon from the Feather Icons pack', 'nucleus' ),
				'source'      => 'feather',
				'settings'    => array( 'iconsPerPage' => 66 ),
				'required'    => array( 'type', '=', 'feather' ),
			) )
			->add_field( 'icon_flaticon', 'icon', array(
				'label'       => esc_html__( 'Flaticon', 'nucleus' ),
				'description' => esc_html__( 'Choose the icon from the Flaticons pack', 'nucleus' ),
				'source'      => 'flaticon',
				'settings'    => array( 'iconsPerPage' => 100 ),
				'required'    => array( 'type', '=', 'flaticon' ),
			) )
			->add_field( 'icon_image', 'media', array(
				'label'       => esc_html__( 'Icon', 'nucleus' ),
				'description' => esc_html__( 'Choose the custom image', 'nucleus' ),
				'source'      => 'flaticon',
				'media'       => array( 'title' => esc_html__( 'Choose the icon', 'nucleus' ) ),
				'required'    => array( 'type', '=', 'image' ),
			) );

		equip_add_menu( 'nucleus_icon', $layout, array(
			'exclude' => 'children',
		) );

	} catch ( Exception $e ) {
		trigger_error( $e->getMessage() );
	}
}

add_action( 'equip/register', 'nucleus_add_extra_menu_fields' );

/**
 * Modified version of {@see Walker_Nav_Menu}.
 *
 * Inject the custom fields into the menu item.
 * Allowed only for top-level menu items.
 *
 * @uses Walker_Nav_Menu
 */
class Nucleus_Nav_Menu extends Walker_Nav_Menu {

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 3.0.0
	 * @since 4.4.0 'nav_menu_item_args' filter was added.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filter the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param array  $args  An array of arguments.
		 * @param object $item  Menu item data object.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		/**
		 * Filter the CSS class(es) applied to a menu item's list item element.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		/**
		 * Filter the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		/**
		 * Filter a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string $title The menu item's title.
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		// check if icon is used for current menu item
		// and prepare the icon, it should be inside <a>
		$nucleus_icon = '';
		$is_icon = (bool) absint( nucleus_get_setting( 'menu_is_icons', 1 ) );
		if ( 0 === $depth && $is_icon && isset( $item->nucleus_icon ) ) {
			$n = wp_parse_args( $item->nucleus_icon, array(
				'type'              => 'font_awesome',
				'icon_font_awesome' => '',
				'icon_feather'      => '',
				'icon_flaticon'     => '',
				'icon_image'        => '',
			) );

			$type = $n['type'];
			if ( 'image' === $type ) {
				$src  = esc_url( nucleus_get_image_src( (int) $n['icon_image'] ) );
				$nucleus_icon = nucleus_get_tag( 'img', array( 'src' => $src, 'class' => 'menu-item-image' ) );
				unset( $src );
			} elseif ( ! empty( $n["icon_{$type}"] )) {
				$class = esc_attr( $n["icon_{$type}"] );
				$nucleus_icon = nucleus_get_tag( 'i', array( 'class' => $class ), '', 'paired' );
				unset( $class );
			}
			unset( $type, $n );
		}

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $nucleus_icon;
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of {@see wp_nav_menu()} arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}