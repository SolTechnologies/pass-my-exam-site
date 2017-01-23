<?php
/**
 * Theme widgets
 *
 * @package Nucleus
 */

/**
 * Register widget area(s).
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function nucleus_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Sidebar', 'nucleus' ),
		'id'            => 'blog-sidebar',
		'description'   => esc_html__( 'For use inside Blog', 'nucleus' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	for ( $i = 1, $n = nucleus_get_footer_sidebars(); $i <= $n; $i ++ ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Footer Sidebar ', 'nucleus' ) . $i, // whitespace at the end
			'id'            => 'footer-sidebar-' . $i,
			'description'   => esc_html__( 'For use inside Footer', 'nucleus' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
	unset( $i, $n );

	for ( $i = 1, $n = 4; $i <= $n; $i ++ ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Widgetized Sidebar ', 'nucleus' ) . $i, // whitespace at the end
			'id'            => 'widgetized-sidebar-' . $i,
			'description'   => esc_html__( 'For use inside Widgetized Area', 'nucleus' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
	unset( $i, $n );

	if ( nucleus_is_woocommerce() ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Shop Sidebar', 'nucleus' ),
			'id'            => 'shop-sidebar',
			'description'   => esc_html__( 'For use inside the Shop Area', 'nucleus' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}

	register_widget( 'Nucleus_Widget_Logo' );
	register_widget( 'Nucleus_Widget_Categories' );
	register_widget( 'Nucleus_Widget_Recent_Posts' );
	register_widget( 'Nucleus_Widget_Tags' );
	register_widget( 'Nucleus_Widget_Menu' );
}

add_action( 'widgets_init', 'nucleus_widgets_init' );

/**
 * Autoloader for Widgets
 *
 * @param string $widget Widget class
 *
 * @return bool
 */
function nucleus_widgets_loader( $widget ) {
	if ( false === stripos( $widget, 'Nucleus_Widget' ) ) {
		return true;
	}

	// convert class name to file
	$chunks = array_filter( explode( '_', strtolower( $widget ) ) );

	/**
	 * Filter the widget file name
	 *
	 * @param string $file   File name according to WP coding standards
	 * @param string $widget Class name
	 */
	$class = apply_filters( 'nucleus_widget_file', 'class-' . implode( '-', $chunks ) . '.php', $widget );

	/**
	 * Filter the directories where widgets class will be loaded
	 *
	 * @param array $targets Directories
	 */
	$targets = apply_filters( 'nucleus_widget_directories', array(
		NUCLEUS_STYLESHEET_DIR . '/widgets',
		NUCLEUS_TEMPLATE_DIR . '/widgets',
	) );

	foreach ( $targets as $target ) {
		if ( file_exists( $target . '/' . $class ) ) {
			require $target . '/' . $class;
			break;
		}
	}

	return true;
}

spl_autoload_register( 'nucleus_widgets_loader' );

/**
 * Custom walker for
 *
 * @see wp_list_categories()
 * @see Nucleus_Widget_Categories
 */
class Nucleus_Category_Walker extends Walker_Category {
	/**
	 * Starts the element output.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @see Walker::start_el()
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int    $depth    Optional. Depth of category in reference to parents. Default 0.
	 * @param array  $args     Optional. An array of arguments. See wp_list_categories(). Default empty array.
	 * @param int    $id       Optional. ID of the current category. Default 0.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		/** This filter is documented in wp-includes/category-template.php */
		$cat_name = apply_filters(
			'list_cats',
			esc_attr( $category->name ),
			$category
		);

		// Don't generate an element if the category name is empty.
		if ( ! $cat_name ) {
			return;
		}

		$link = '<a href="' . esc_url( get_term_link( $category ) ) . '" ';
		if ( $args['use_desc_for_title'] && ! empty( $category->description ) ) {
			/**
			 * Filter the category description for display.
			 *
			 * @since 1.2.0
			 *
			 * @param string $description Category description.
			 * @param object $category    Category object.
			 */
			$link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
		}

		$link .= '>';
		$link .= $cat_name;

		// counter should be inside <a>
		if ( ! empty( $args['show_count'] ) ) {
			$link .= '<span>' . number_format_i18n( $category->count ).  '</span>';
		}

		$link .= '</a>';

		if ( ! empty( $args['feed_image'] ) || ! empty( $args['feed'] ) ) {
			$link .= ' ';

			if ( empty( $args['feed_image'] ) ) {
				$link .= '(';
			}

			$link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $args['feed_type'] ) ) . '"';

			if ( empty( $args['feed'] ) ) {
				$alt = ' alt="' . sprintf( esc_html__( 'Feed for all posts filed under %s', 'nucleus' ), $cat_name ) . '"';
			} else {
				$alt = ' alt="' . $args['feed'] . '"';
				$name = $args['feed'];
				$link .= empty( $args['title'] ) ? '' : $args['title'];
			}

			$link .= '>';

			if ( empty( $args['feed_image'] ) ) {
				$link .= $name;
			} else {
				$link .= "<img src='" . $args['feed_image'] . "'$alt" . ' />';
			}
			$link .= '</a>';

			if ( empty( $args['feed_image'] ) ) {
				$link .= ')';
			}
		}

		if ( 'list' == $args['style'] ) {
			$output .= "\t<li";
			$css_classes = array(
				'cat-item',
				'cat-item-' . $category->term_id,
			);

			if ( ! empty( $args['current_category'] ) ) {
				// 'current_category' can be an array, so we use `get_terms()`.
				$_current_terms = get_terms( $category->taxonomy, array(
					'include' => $args['current_category'],
					'hide_empty' => false,
				) );

				foreach ( $_current_terms as $_current_term ) {
					if ( $category->term_id == $_current_term->term_id ) {
						$css_classes[] = 'current-cat';
					} elseif ( $category->term_id == $_current_term->parent ) {
						$css_classes[] = 'current-cat-parent';
					}
					while ( $_current_term->parent ) {
						if ( $category->term_id == $_current_term->parent ) {
							$css_classes[] =  'current-cat-ancestor';
							break;
						}
						$_current_term = get_term( $_current_term->parent, $category->taxonomy );
					}
				}
			}

			/**
			 * Filter the list of CSS classes to include with each category in the list.
			 *
			 * @since 4.2.0
			 *
			 * @see wp_list_categories()
			 *
			 * @param array  $css_classes An array of CSS classes to be applied to each list item.
			 * @param object $category    Category data object.
			 * @param int    $depth       Depth of page, used for padding.
			 * @param array  $args        An array of wp_list_categories() arguments.
			 */
			$css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );

			$output .=  ' class="' . $css_classes . '"';
			$output .= ">$link\n";
		} elseif ( isset( $args['separator'] ) ) {
			$output .= "\t$link" . $args['separator'] . "\n";
		} else {
			$output .= "\t$link<br />\n";
		}
	}
}
