<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Nucleus
 */

/**
 * Display the extra attributes for the body element
 */
function nucleus_body_attributes() {
	$attr = array();

	// Scroll slideshow support
	if ( is_single() && nucleus_is_slideshow() ) {
		$slideshow_id = get_queried_object_id();
		$animation    = nucleus_get_meta( $slideshow_id, '_nucleus_slideshow_settings', 'animation' );

		$attr['data-hijacking'] = 'off';
		$attr['data-animation'] = esc_attr( (string) $animation );
		unset( $animation );
	}

	/**
	 * Filter the attributes for <body>
	 *
	 * @param array $attr A list of attributes
	 */
	$attr = apply_filters( 'nucleus_body_attributes', $attr );

	echo nucleus_get_html_attr( $attr );
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function nucleus_categorized_blog() {
	$all_the_cool_cats = get_transient( NUCLEUS_TRANSIENT_CATEGORIES );
	if ( false === $all_the_cool_cats ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( NUCLEUS_TRANSIENT_CATEGORIES, $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so nucleus_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so nucleus_categorized_blog should return false.
		return false;
	}
}

if ( ! function_exists( 'nucleus_entry_meta' ) ) :
	/**
	 * Prints the HTML with meta information: author, categories,
	 * comments counter and date.
	 *
	 * Used both for posts tile and for single entry
	 */
	function nucleus_entry_meta() {
		?>
		<div class="post-meta">
			<div class="column">
				<span class="post-format"></span>

				<?php
				// author
				printf( '<span><i class="icon-head"></i>&nbsp;<a href="%1$s">%2$s</a></span>',
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					esc_html( get_the_author() )
				);

				if ( 'post' === get_post_type() ) {
					// note: used between list items, there is a space after the comma
					$categories_list = get_the_category_list( ', ' );
					if ( $categories_list && nucleus_categorized_blog() ) {
						echo '<span>', esc_html_x( 'in', 'post categories', 'nucleus' ), '</span>';
						echo '<span><i class="icon-ribbon"></i>&nbsp;', $categories_list, '</span>';
					}
					unset( $categories_list );
				}

				// post comments link. disabled for single posts
				if ( ! is_single()
				     && ! post_password_required()
				     && ( comments_open() || get_comments_number() )
				) {
					echo '<span class="post-comments"><i class="icon-speech-bubble"></i>&nbsp;';
					comments_popup_link( 0, 1, '%' );
					echo '</span>';
				}
				?>
			</div>
			<div class="column">
				<?php
				// post publish date
				printf( '<span>%s</span>', esc_html( get_the_date() ) );

				// edit post link
				edit_post_link(
					esc_html_x( 'Edit', 'post edit', 'nucleus' ),
					'<span class="post-edit-link">',
					'</span>'
				);
				?>
			</div>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'nucleus_entry_tags' ) ) :
	/**
	 * Show entry tags
	 */
	function nucleus_entry_tags() {
		$tags_list = get_the_tag_list();
		if ( $tags_list ) {
			echo '<div class="tags-links">', $tags_list, '</div>';
		}
	}
endif;

if ( ! function_exists( 'nucleus_the_shares' ) ) :
	/**
	 * Show entry share buttons
	 */
	function nucleus_the_shares() {
		/**
		 * This filter allows you easily disable share buttons
		 *
		 * @example
		 * <pre>
		 * add_filter( 'nucleus_entry_shares', '__return_false' );
		 * </pre>
		 *
		 * @param bool $is_shares Enable or disable entry shares
		 */
		if ( ! apply_filters( 'nucleus_entry_shares', true ) ) {
			return;
		}

		// collect data about the post
		$data = array(
			'href'           => '#',
			'data-toggle'    => 'tooltip',
			'data-placement' => 'top',
			'data-text'      => esc_html( get_the_title() ),
			'data-url'       => esc_url( get_the_permalink() ),
			'data-thumb'     => has_post_thumbnail() ? nucleus_get_image_src( get_post_thumbnail_id() ) : '',
		);

		// collect data
		$shares = array(
			'fa fa-twitter'     => array(
				'class' => 'sb-twitter nucleus-share-twitter',
				'title' => esc_html_x( 'Twitter', 'share button', 'nucleus' ),
			),
			'fa fa-facebook'    => array(
				'class' => 'sb-facebook nucleus-share-facebook',
				'title' => esc_html_x( 'Facebook', 'share button', 'nucleus' ),
			),
			'fa fa-google-plus' => array(
				'class' => 'sb-google-plus nucleus-share-google-plus',
				'title' => esc_html_x( 'Google+', 'share button', 'nucleus' ),
			),
			'fa fa-pinterest'   => array(
				'class' => 'sb-pinterest nucleus-share-pinterest',
				'title' => esc_html_x( 'Pinterest', 'share button', 'nucleus' ),
			),
		);

		$html = '';
		foreach ( $shares as $icon => $share ) {
			$html .= sprintf( '<a %1$s><i class="%2$s"></i></a>',
				nucleus_get_html_attr( array_merge( $data, $share ) ),
				$icon
			);
		}

		echo '<div class="social-bar">', $html, '</div>';
	}
endif;

if ( ! function_exists( 'nucleus_entry_footer' ) ) :
	/**
	 * Prints HTML of posts footer
	 */
	function nucleus_entry_footer() {
		?>
		<div class="post-tools space-top-2x">
		<div class="column">
			<?php nucleus_entry_tags(); ?>
		</div>
		<div class="column">
			<?php nucleus_the_shares(); ?>
		</div>
		</div><?php
	}
endif;

if ( ! function_exists( 'nucleus_tile_footer' ) ) :
	/**
	 * Prints the HTML in tile footer
	 *
	 * Used in home for post tiles
	 */
	function nucleus_tile_footer() {
		// read more link
		printf( '<a href="%1$s">%2$s</a>',
			esc_url( get_permalink() ),
			esc_html__( 'Read More', 'nucleus' )
		);
	}
endif;

if ( ! function_exists( 'nucleus_the_logo' ) ) :
	/**
	 * Display the logo
	 *
	 * @uses the_custom_logo()
	 */
	function nucleus_the_logo() {
		// for backward compatibility
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			the_custom_logo();

			return;
		}

		$logo_id = absint( nucleus_get_option( 'global_logo' ) );
		if ( false === (bool) $logo_id ) {
			/**
			 * Filter the URI to logo fallback
			 *
			 * This logo will be loaded if user does not specify the logo
			 * neither through customizer (WP 4.5+), nor in Theme Options
			 *
			 * This filter may be useful if you want to change the default fallback logo
			 *
			 * @param string $uri Logo URI
			 */
			$logo_src = apply_filters( 'nucleus_logo_fallback', NUCLEUS_TEMPLATE_URI . '/img/logo.png' );

			/**
			 * Filter the fallback logo attributes
			 *
			 * This filter allows you to add, remove or change attributes
			 * for <img> tag, containing the logo
			 *
			 * @param array $attributes Fallback logo attributes
			 */
			$logo = nucleus_get_tag( 'img', apply_filters( 'nucleus_logo_fallback_atts', array(
				'src'      => esc_url( $logo_src ),
				'alt'      => esc_attr( get_bloginfo( 'name', 'display' ) ),
				'class'    => 'custom-logo',
				'itemprop' => 'logo',
			) ) );
		} else {
			$logo = wp_get_attachment_image( $logo_id, 'full', false, array(
				'class'    => 'custom-logo',
				'itemprop' => 'logo',
			) );
		}

		printf( '<a href="%1$s" class="site-logo" rel="home" itemprop="url">%2$s</a>',
			esc_url( home_url( '/' ) ),
			$logo
		);
	}
endif;


if ( ! function_exists( 'nucleus_the_socials' ) ) :
	/**
	 * Prints the socials
	 *
	 * @uses nucleus_get_networks()
	 *
	 * @param $socials
	 */
	function nucleus_the_socials( $socials ) {
		if ( empty( $socials ) ) {
			return;
		}

		$networks = nucleus_get_networks( NUCLEUS_STYLESHEET_DIR . '/misc/networks.ini' );
		$tpl      = '<a href="{url}" class="{helper}"><i class="{icon}"></i></a>';

		foreach ( $socials as $network => $url ) {
			$r = array(
				'{url}'    => esc_url( $url ),
				'{helper}' => esc_attr( $networks[ $network ]['helper'] ),
				'{icon}'   => esc_attr( $networks[ $network ]['icon'] ),
			);

			echo str_replace( array_keys( $r ), array_values( $r ), $tpl );
		}
	}
endif;

if ( ! function_exists( 'nucleus_the_menu' ) ) :
	/**
	 * Show the main navigation
	 */
	function nucleus_the_menu() {
		/**
		 * Filter the main menu arguments
		 *
		 * @see https://developer.wordpress.org/reference/functions/wp_nav_menu/
		 *
		 * @param array $args Arguments
		 */
		$args = apply_filters( 'nucleus_menu_args', array(
			'theme_location'  => 'primary',
			'container'       => 'nav',
			'container_class' => 'main-navigation',
			'container_id'    => false,
			'fallback_cb'     => false,
			'depth'           => 2,
			'walker'          => new Nucleus_Nav_Menu(),
		) );

		?>
		<div class="container">
			<?php nucleus_the_logo(); ?>
			<?php wp_nav_menu( $args ); ?>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'nucleus_the_breadcrumbs' ) ) :
	/**
	 * Display the Breadcrumbs
	 *
	 * @see nucleus_page_title()
	 */
	function nucleus_the_breadcrumbs() {
		if ( ! function_exists( 'bcn_display' ) ) {
			return;
		}

		echo '<div class="breadcrumbs">';
		bcn_display();
		echo '</div>';
	}
endif;

if ( ! function_exists( 'nucleus_the_toolbar' ) ) :
	/**
	 * Show the toolbar
	 */
	function nucleus_the_toolbar() {
		if ( ! nucleus_is_header_search() && ! nucleus_is_header_signup_login() ) {
			return;
		}

		echo '<div class="toolbar">';
		nucleus_header_buttons();

		if ( nucleus_is_woocommerce()
		     && ( ! is_cart() && ! is_checkout() )
		) {
			nucleus_the_cart();
		}

		nucleus_header_search();
		echo '</div>';
	}
endif;

if ( ! function_exists( 'nucleus_the_slideshow' ) ) :
	/**
	 * Display the Scroll Slideshow
	 *
	 * This template tag is used inside the Loop
	 *
	 * @see Nucleus_CPT_Slideshow
	 */
	function nucleus_the_slideshow() {
		$slideshow_id = absint( get_the_ID() );
		if ( empty( $slideshow_id ) ) {
			return;
		}

		$cache_key   = nucleus_get_unique_key( 'nucleus_slideshow', $slideshow_id );
		$cache_value = get_transient( $cache_key );
		if ( false !== $cache_value ) {
			echo nucleus_content_decode( $cache_value );

			return;
		}

		$query = new WP_Query( array(
			'post_type'     => 'nucleus_slideshow',
			'no_found_rows' => true,
			'p'             => $slideshow_id,
		) );

		// do not execute if post not found
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) : $query->the_post();

				// get meta box data
				$gallery = nucleus_get_meta( $slideshow_id, '_nucleus_slideshow_settings', 'gallery' );
				if ( empty( $gallery ) ) {
					break;
				}

				// template
				ob_start();
				?>
				<section class="{class}">
					<div>
						<div class="content" style="{image}"></div>
					</div>
				</section>
				<?php
				$template = ob_get_clean();
				$slides   = array();
				foreach ( $gallery as $i => $slide ) {
					$r = array(
						'{class}' => $i === 0 ? 'cd-section visible' : 'cd-section',
						'{image}' => nucleus_css_background_image( (int) $slide ),
					);

					$slides[] = str_replace( array_keys( $r ), array_values( $r ), $template );
				}

				$slides = implode( '', $slides );

				// cache for 1 day
				$cache_value = nucleus_content_encode( $slides );
				set_transient( $cache_key, $cache_value, DAY_IN_SECONDS );

				// display slideshow
				echo '<!-- Scroll Slideshow -->', $slides;

			endwhile;
		endif;
		wp_reset_postdata();
	}
endif;

if ( ! function_exists( 'nucleus_the_slideshow_nav' ) ) :
	/**
	 * Display the Scroll Slideshow navigation
	 */
	function nucleus_the_slideshow_nav() {
		?>
		<nav>
			<ul class="cd-vertical-nav">
				<li><a href="#" class="cd-prev inactive"><i class="icon-arrow-up"></i></a></li>
				<li><a href="#" class="cd-next"><i class="icon-arrow-down"></i></a></li>
			</ul>
		</nav>
		<?php
	}
endif;

if ( ! function_exists( 'nucleus_the_404' ) ) :
	/**
	 * Display the 404 page contents
	 */
	function nucleus_the_404() {
		$attr  = nucleus_get_options_slice( '404_' );
		$title = esc_html( $attr['title'] );
		$desc  = wp_kses( $attr['description'], wp_kses_allowed_html( 'data' ) );
		$text  = esc_html( $attr['button_text'] );

		nucleus_the_text( $title, '<h2 class="text-thin space-top">', '</h2>' );
		nucleus_the_text( $desc, '<p class="text-xs text-muted text-uppercase space-bottom-2x">', '</p>' );

		if ( ! empty( $text ) ) {
			nucleus_the_tag( 'a', array(
				'href'  => esc_url( home_url( '/' ) ),
				'class' => 'btn btn-default waves-effect waves-light',
			), $text );
		}

		if ( ! empty( $attr['featured'] ) ) {
			echo wp_get_attachment_image( (int) $attr['featured'], 'full', false, array(
				'alt'   => esc_html__( '404', 'nucleus' ),
				'class' => 'block-center space-top-2x',
			) );
		}
	}
endif;

if ( ! function_exists( 'nucleus_the_cart' ) ) :
	/**
	 * Show the link to WooCommerce Cart
	 *
	 * @uses nucleus_is_woocommerce()
	 */
	function nucleus_the_cart() {
		?>
		<div class="cart-toggle">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-btn cart-contents">
				<i class="icon-bag"></i>
				<span class="count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
			</a>
			<div class="cart-dropdown">
				<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
			</div>
		</div>
		<?php
	}
endif;

/**
 * Checks if current page is "Scroll Slideshow"
 *
 * @return bool
 */
function nucleus_is_slideshow() {
	return ( 'nucleus_slideshow' === get_post_type() );
}

if ( ! function_exists( 'nucleus_is_preloader' ) ):
	/**
	 * Check if preloader in enabled
	 *
	 * @see inc/options.php
	 * @see nucleus_the_preloader
	 *
	 * @return bool
	 */
	function nucleus_is_preloader() {
		$is_preloader = (int) nucleus_get_option( 'global_is_preloader', 1 );

		return (bool) $is_preloader;
	}
endif;

if ( ! function_exists( 'nucleus_is_navbar_sticky' ) ) :
	/**
	 * Check if "sticky" navbar is enabled in Theme Options
	 *
	 * @see inc/options.php
	 * @see nucleus_navbar_class
	 *
	 * @return bool
	 */
	function nucleus_is_navbar_sticky() {
		$is_sticky = (int) nucleus_get_setting( 'header_is_sticky', 1 );

		return (bool) $is_sticky;
	}
endif;

if ( ! function_exists( 'nucleus_is_navbar_fullwidth' ) ) :
	/**
	 * Check if "fullwidth" navbar is enabled in Theme Options
	 *
	 * @see inc/options.php
	 * @see nucleus_navbar_class()
	 *
	 * @return bool
	 */
	function nucleus_is_navbar_fullwidth() {
		$template = (string) nucleus_get_setting( 'header_layout', 'boxed' );

		return ( 'fullwidth' === $template );
	}
endif;

if ( ! function_exists( 'nucleus_is_page_title' ) ) :
	/**
	 * Check is page title is enabled
	 *
	 * Page title also could be disabled in Page Settings
	 *
	 * @see inc/options.php
	 * @see inc/meta-boxes.php
	 * @see nucleus_page_title()
	 *
	 * @return bool
	 */
	function nucleus_is_page_title() {
		$is_page_title = (int) nucleus_get_setting( 'global_is_page_title', 1 );

		return (bool) $is_page_title;
	}
endif;

if ( ! function_exists( 'nucleus_is_social_bar' ) ) :
	/**
	 * Check is Social Bar is enabled in Theme Options
	 *
	 * Also disabled for Scroll Slideshow posts
	 *
	 * @see inc/options.php
	 * @see nucleus_the_social_bar()
	 *
	 * @return bool
	 */
	function nucleus_is_social_bar() {
		$is_bar = (int) nucleus_get_option( 'global_is_social_bar', 0 );

		return (bool) ( $is_bar && ! nucleus_is_slideshow() );
	}
endif;

if ( ! function_exists( 'nucleus_is_scroll_to_top' ) ) :
	/**
	 * Check if Scroll to Top button is enabled in Theme Options
	 *
	 * @see inc/options.php
	 * @see nucleus_scroll_to_top()
	 *
	 * @return bool
	 */
	function nucleus_is_scroll_to_top() {
		$is_scroll = (int) nucleus_get_option( 'global_is_scroll_to_top', 1 );

		return (bool) $is_scroll;
	}
endif;

if ( ! function_exists( 'nucleus_is_footer_copyright' ) ) :
	/**
	 * Check if copyright is enabled in Theme Options
	 *
	 * @return bool
	 */
	function nucleus_is_footer_copyright() {
		$is_copy = (int) nucleus_get_option( 'footer_is_copy', 1 );

		return (bool) $is_copy;
	}
endif;

if ( ! function_exists( 'nucleus_is_header_search' ) ) :
	/**
	 * Check if search form is enabled
	 *
	 * Based on Page Settings and Theme Options
	 *
	 * @see inc/options.php
	 * @see inc/meta-boxes.php
	 * @see nucleus_the_toolbar()
	 * @see nucleus_header_search()
	 *
	 * @return bool
	 */
	function nucleus_is_header_search() {
		$is_search = (int) nucleus_get_setting( 'header_is_search', 1 );

		return (bool) $is_search;
	}
endif;

if ( ! function_exists( 'nucleus_is_header_signup_login' ) ) :
	/**
	 * Check if Sign up / Log in Buttons are enabled
	 *
	 * Based op Page Settings and Theme Options
	 *
	 * @see inc/option.php
	 * @see inc/meta-boxes.php
	 * @see nucleus_the_toolbar()
	 * @see nucleus_header_buttons()
	 *
	 * @return bool
	 */
	function nucleus_is_header_signup_login() {
		$is_signup = (int) nucleus_get_setting( 'header_is_signup_login', 1 );

		return (bool) $is_signup;
	}
endif;

if ( ! function_exists( 'nucleus_is_footer_action' ) ) :
	/**
	 * Checks if Actions Links in footer are enabled
	 *
	 * @see inc/options.php
	 * @see footer.php
	 * @see nucleus_footer_action()
	 *
	 * @return bool
	 */
	function nucleus_is_footer_action() {
		$is_action = (int) nucleus_get_option( 'footer_is_action', 0 );

		return (bool) $is_action;
	}
endif;

if ( ! function_exists( 'nucleus_is_footer_subscribe' ) ) :
	/**
	 * Check if subscribe form in footer is enabled
	 *
	 * Based on Theme Options
	 *
	 * @see inc/options.php
	 * @see nucleus_footer_subscribe()
	 *
	 * @return bool
	 */
	function nucleus_is_footer_subscribe() {
		$is_subscribe = (int) nucleus_get_option( 'footer_is_subscribe', 0 );

		return (bool) $is_subscribe;
	}
endif;

if ( ! function_exists( 'nucleus_is_google_fonts' ) ) :
	/**
	 * Check if Google Fonts is enabled
	 *
	 * Based on Theme Options
	 *
	 * @see inc/options.php
	 * @see nucleus_scripts()
	 *
	 * @return bool
	 */
	function nucleus_is_google_fonts() {
		$is_fonts = (int) nucleus_get_option( 'typography_is_google_fonts', 1 );

		return (bool) $is_fonts;
	}
endif;

if ( ! function_exists( 'nucleus_is_woocommerce' ) ) :
	/**
	 * Check if WooCommerce is activated
	 *
	 * @return bool
	 */
	function nucleus_is_woocommerce() {
		return class_exists( 'WooCommerce' );
	}
endif;

/**
 * Display the navbar class
 *
 * @see header.php
 */
function nucleus_navbar_class() {
	$classes   = array();
	$classes[] = 'navbar';

	// sticky navbar
	if ( nucleus_is_navbar_sticky() ) {
		$classes[] = 'navbar-sticky';
	}

	// fullwidth navbar
	if ( nucleus_is_navbar_fullwidth() ) {
		$classes[] = 'navbar-fullwidth';
	}

	/**
	 * Filter the navbar classes
	 *
	 * @param array $classes A list of classes
	 */
	$classes = apply_filters( 'nucleus_navbar_class', $classes );

	echo esc_attr( nucleus_get_class_set( $classes ) );
}

/**
 * Display the footer class
 *
 * @see footer.php
 */
function nucleus_footer_class() {
	$classes   = array();
	$classes[] = 'footer';

	// skin
	$classes[] = 'footer-' . sanitize_key( nucleus_get_setting( 'footer_skin', 'light' ) );

	/**
	 * Filter the footer class
	 *
	 * @param array $classes A list of footer classes
	 */
	$classes = apply_filters( 'nucleus_footer_class', $classes );

	echo esc_attr( nucleus_get_class_set( $classes ) );
}

if ( ! function_exists( 'nucleus_blog_layout' ) ) :
	/**
	 * Returns the template name for blog, based on theme options
	 *
	 * @see index.php
	 * @see inc/options.php
	 * @see template-parts/blog-*.php
	 *
	 * @return string
	 */
	function nucleus_blog_layout() {
		$layout = nucleus_get_option( 'layout_blog', 'right-sidebar' );

		return sanitize_key( $layout );
	}
endif;

if ( ! function_exists( 'nucleus_single_layout' ) ) :
	/**
	 * Returns the template for single post, based on page settings
	 *
	 * @see  single.php
	 * @see  inc/options.php
	 * @see  template-parts/single-*.php
	 *
	 * @uses nucleus_get_page_setting
	 *
	 * @return string
	 */
	function nucleus_single_layout() {
		$layout = nucleus_get_page_setting( 'post_layout', 'right-sidebar' );

		return sanitize_key( $layout );
	}
endif;

if ( ! function_exists( 'nucleus_search_layout' ) ) :
	/**
	 * Returns the template for search page, based on Theme Options
	 *
	 * @see search.php
	 * @see inc/options.php
	 * @see template-parts/blog-*.php
	 *
	 * @return string
	 */
	function nucleus_search_layout() {
		$layout = nucleus_get_option( 'layout_search', 'no-sidebar' );

		return sanitize_key( $layout );
	}
endif;

if ( ! function_exists( 'nucleus_archive_layout' ) ) :
	/**
	 * Returns the template for archive page
	 *
	 * Based on Theme Options
	 *
	 * @see archive.php
	 * @see inc/options.php
	 * @see template-parts/blog-*.php
	 *
	 * @return string
	 */
	function nucleus_archive_layout() {
		$layout = nucleus_get_option( 'layout_archive', 'no-sidebar' );

		return sanitize_key( $layout );
	}
endif;

if ( ! function_exists( 'nucleus_footer_layout' ) ) :
	/**
	 * Returns the template for footer
	 *
	 * Base on Theme Options
	 *
	 * @see footer.php
	 * @see inc/options.php
	 * @see template-parts/footer-*.php
	 *
	 * @return string
	 */
	function nucleus_footer_layout() {
		$layout = (int) nucleus_get_option( 'footer_layout', 4 );

		return 'col-' . $layout;
	}
endif;

if ( ! function_exists( 'nucleus_footer_copyright' ) ) :
	/**
	 * Display the footer copyright
	 *
	 * @uses wp_kses_post()
	 */
	function nucleus_footer_copyright() {
		if ( ! nucleus_is_footer_copyright() ) {
			return;
		}

		$copy_left  = nucleus_get_option( 'footer_copy_left' );
		$copy_right = nucleus_get_option( 'footer_copy_right' );

		?>
		<div class="copyright">
			<div class="column">
				<?php echo wp_kses_post( $copy_left ); ?>
			</div>
			<div class="column">
				<?php echo wp_kses_post( $copy_right ); ?>
			</div>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'nucleus_footer_action' ) ) :
	/**
	 * Display Action Links in footer
	 *
	 * @see footer.php
	 * @see inc/options.php
	 */
	function nucleus_footer_action() {
		if ( ! nucleus_is_footer_action() ) {
			return;
		}

		$actions = array(
			'first'  => nucleus_get_options_slice( 'footer_action_1_' ),
			'second' => nucleus_get_options_slice( 'footer_action_2_' ),
			'third'  => nucleus_get_options_slice( 'footer_action_3_' ),
		);

		$cell_tpl = '<div class="cell">{icon}<span><a href="{url}">{title}</a></span></div>';
		$tools    = '';
		foreach ( $actions as $k => $cell ) {
			if ( empty( $cell['url'] ) ) {
				continue;
			}

			$r = array();

			$r['{url}']   = esc_attr( $cell['url'] );
			$r['{title}'] = esc_html( $cell['title'] );

			if ( empty( $cell['icon'] ) ) {
				$r['{icon}'] = '';
			} else {
				$src = nucleus_get_image_src( (int) $cell['icon'] );
				$img = nucleus_get_tag( 'img', array(
					'src' => $src,
					'alt' => ucfirst( $k ) . ' ' . esc_html__( 'Action', 'nucleus' ),
				) );

				$r['{icon}'] = '<i>' . $img . '</i>';
				unset( $src, $img );
			}

			$tools .= str_replace( array_keys( $r ), array_values( $r ), $cell_tpl );
		}

		if ( empty( $tools ) ) {
			return;
		}

		echo '<div class="tools">', $tools, '</div>';
	}
endif;

if ( ! function_exists( 'nucleus_footer_subscribe' ) ) :
	/**
	 * Display the subscribe form in footer
	 *
	 * @see inc/options.php
	 * @see footer.php
	 */
	function nucleus_footer_subscribe() {
		if ( ! nucleus_is_footer_subscribe() ) {
			return;
		}

		$a = nucleus_get_options_slice( 'footer_subscribe_' );
		/**
		 * Filter the subscribe form args
		 *
		 * @param array $args Subscribe form attrbiutes
		 */
		$a = apply_filters( 'nucleus_footer_subscribe_atts', wp_parse_args( $a, array(
			'icon'        => '',
			'url'         => '',
			'placeholder' => esc_html__( 'Your Email', 'nucleus' ),
		) ) );

		if ( empty( $a['url'] ) ) {
			return;
		}

		$url = htmlspecialchars_decode( $a['url'] );

		// build MC AntiSPAM
		$mc_antispam = '';
		$request_uri = parse_url( $url, PHP_URL_QUERY );
		parse_str( $request_uri, $c );
		if ( array_key_exists( 'u', $c ) && array_key_exists( 'id', $c ) ) {
			$mc_antispam = sprintf( 'b_%1$s_%2$s', $c['u'], $c['id'] );
		}
		unset( $request_uri, $c );

		// icon
		$icon = '';
		if ( ! empty( $a['icon'] ) ) {
			$src  = nucleus_get_image_src( (int) $a['icon'] );
			$icon = nucleus_get_tag( 'img', array(
				'src' => $src,
				'alt' => esc_html__( 'Footer Subscribe Icon', 'nucleus' ),
			) );
			unset( $src );
		}

		$form = array(
			'method'       => 'post',
			'action'       => esc_url( $url ),
			'target'       => '_blank',
			'novalidate'   => true,
			'autocomplete' => 'off',
		);

		?>
		<div class="subscribe">
			<div class="subscribe-form">
				<?php echo nucleus_get_text( $icon, '<i>', '</i>' ); ?>
				<form <?php echo nucleus_get_html_attr( $form ); ?>>
					<label for="subscr_email" class="sr-only"><?php esc_html_e( 'Subscribe to latest news', 'nucleus' ); ?></label>
					<input type="email" class="form-control" id="subscr_email"
					       placeholder="<?php echo esc_html( $a['placeholder'] ); ?>">
					<div style="position: absolute; left: -5000px;">
						<input type="text" name="<?php echo esc_attr( $mc_antispam ); ?>"
						       tabindex="-1" value="">
					</div>
					<button type="submit"><i class="icon-mail"></i></button>
				</form>
			</div>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'nucleus_header_search' ) ) :
	/**
	 * Display the header search form
	 *
	 * @uses get_search_form()
	 * @uses nucleus_is_header_search()
	 */
	function nucleus_header_search() {
		if ( ! nucleus_is_header_search() ) {
			return;
		}

		?>
		<div class="search-btn">
			<i class="icon-search"></i>
			<form method="get" class="search-box" action="<?php echo esc_url( home_url( '/' ) ); ?>" autocomplete="off">
				<input type="text" name="s" class="form-control input-sm"
				       placeholder="<?php echo esc_attr_x( 'Search', 'search form placeholder', 'nucleus' ); ?>"
				       value="<?php the_search_query(); ?>">
				<button type="submit"><i class="icon-search"></i></button>
			</form>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'nucleus_header_buttons' ) ) :
	/**
	 * Display the header Sign up / Log in Buttons
	 *
	 * @uses nucleus_is_header_signup_login()
	 */
	function nucleus_header_buttons() {
		if ( ! nucleus_is_header_signup_login() ) {
			return;
		}

		nucleus_signup();
		nucleus_login();
	}
endif;

if ( ! function_exists( 'nucleus_signup' ) ):
	/**
	 * Display the registration link
	 */
	function nucleus_signup() {
		if ( is_user_logged_in() ) {
			return;
		}

		if ( false === (bool) get_option( 'users_can_register' ) ) {
			return;
		}

		printf( '<a href="%1$s" class="text-sm">%2$s</a>',
			esc_url( wp_registration_url() ),
			esc_html__( 'Sign up', 'nucleus' )
		);
	}
endif;

if ( ! function_exists( 'nucleus_login' ) ) :
	/**
	 * Display Log In button
	 */
	function nucleus_login() {
		if ( is_user_logged_in() && current_user_can( 'read' ) ) {
			$user = wp_get_current_user();
			printf( '<a href="%1$s" class="btn btn-sm btn-default btn-icon-right waves-effect waves-light">%2$s<i class="icon-head"></i></a>',
				admin_url(),
				$user->display_name
			);
		} else {
			printf( '<a href="#" class="btn btn-sm btn-default btn-icon-right waves-effect waves-light" data-toggle="modal" data-target="#loginModal">%s<i class="icon-head"></i></a>',
				esc_html__( 'Log In', 'nucleus' )
			);
		}
	}
endif;

if ( ! function_exists( 'nucleus_mobile_socials' ) ) :
	/**
	 * Display the mobile socials
	 *
	 * @uses nucleus_is_social_bar()
	 * @uses nucleus_the_socials();
	 *
	 * @return void
	 */
	function nucleus_mobile_socials() {
		if ( ! nucleus_is_social_bar() ) {
			return;
		}

		$socials = nucleus_get_option( 'global_social_bar_socials', array() );
		if ( empty( $socials ) ) {
			return;
		}

		echo '<div class="social-bar mobile-socials">';
		nucleus_the_socials( $socials );
		echo '</div>';
	}
endif;

if ( ! function_exists( 'nucleus_portfolio_permalink' ) ) :
	/**
	 * Display the permalink for the current Portfolio post.
	 */
	function nucleus_portfolio_permalink() {
		$post   = get_post();
		$custom = '';

		if ( $post instanceof WP_Post && 'nucleus_portfolio' === $post->post_type ) {
			$custom = nucleus_get_meta( $post->ID, NUCLEUS_PAGE_SETTINGS, 'portfolio_permalink' );
		} else {

		}

		if ( empty( $custom ) ) {
			$link = get_permalink( $post );
		} else {
			$link = $custom;
		}

		/**
		 * Filter the display of the permalink for the current post.
		 *
		 * @param string  $permalink The permalink for the current post.
		 * @param WP_Post $post      WP_Post object
		 */
		echo esc_url( apply_filters( 'the_permalink', $link, $post ) );
	}
endif;
