<?php
/**
 * Filters and actions related to WooCommerce plugin
 *
 * @author 8guild
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// skip if WooCommerce is disabled
if ( ! nucleus_is_woocommerce() ) {
	return;
}

/*
 * Remove the content wrappers
 *
 * @since 1.0.0
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/*
 * Remove the built-in breadcrumbs
 *
 * @see woocommerce/archive-product.php
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/*
 * Remove the built-in Archive and Product description
 *
 * @see woocommerce/archive-product.php
 */
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );

/*
 * Remove product thumbnails
 *
 * @see woocommerce/content-single-product.php
 */
remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );

/*
 * Remove the results count
 *
 * @see woocommerce/archive-product.php
 */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

if ( ! function_exists( 'nucleus_wc_open_wrapper' ) ) :
	/**
	 * Add a custom content wrapper to main catalog
	 *
	 * Open section.container
	 *
	 * @see woocommerce/archive-product.php
	 */
	function nucleus_wc_open_wrapper() {
		if ( is_product() ) :
			echo '<section class="shop-single container">';
		else :
			echo '<section class="container">';
		endif;

		$shop_layout = nucleus_get_option( 'layout_shop', 'no-sidebar' );
		if ( 'no-sidebar' === $shop_layout ) :
			return;
		elseif ( 'right-sidebar' === $shop_layout ) :
			?>
			<div class="row">
				<div class="col-lg-9 col-md-8">
			<?php
		elseif ( 'left-sidebar' === $shop_layout ) :
			?>
			<div class="row">
				<div class="col-lg-9 col-md-8 col-lg-push-3 col-md-push-4">
			<?php
		endif;
	}
endif;

add_action( 'woocommerce_before_main_content', 'nucleus_wc_open_wrapper' );

if ( ! function_exists( 'nucleus_wc_close_wrapper' ) ) :
	/**
	 * Close the custom content wrapper in main catalog
	 *
	 * Close section.container
	 *
	 * @see woocommerce/archive-product.php
	 */
	function nucleus_wc_close_wrapper() {
		$shop_layout = nucleus_get_option( 'layout_shop', 'no-sidebar' );
		if ( in_array( $shop_layout, array( 'left-sidebar', 'right-sidebar' ) ) ) {
			// close div.row
			// NOTE: the div.col-* for products are closed in woocommerce/global/sidebar.php
			echo '</div>';
		}

		echo '</section>';
	}
endif;

add_action( 'woocommerce_after_main_content', 'nucleus_wc_close_wrapper' );

if ( ! function_exists( 'nucleus_wc_before_shop_loop_item' ) ) :
	/**
	 * Display link to product, the flash and product thumbnail
	 *
	 * @see woocommerce/content-product.php
	 */
	function nucleus_wc_before_shop_loop_item() {
		echo '<a href="' . esc_url( get_the_permalink() ) . '" class="thumbnail">';
		wc_get_template( 'loop/sale-flash.php' );
		echo woocommerce_get_product_thumbnail( 'medium' );
		echo '</a>';
	}
endif;

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
add_action( 'woocommerce_before_shop_loop_item', 'nucleus_wc_before_shop_loop_item' );

if ( ! function_exists( 'nucleus_wc_before_shop_loop_item_title' ) ):
	/**
	 * Display the product item meta: category, price
	 * and add to cart button
	 *
	 * Also open the div.description
	 *
	 * @see woocommerce/content-product.php
	 */
	function nucleus_wc_before_shop_loop_item_title() {
		?>
		<div class="description">
			<div class="shop-meta">
				<?php
				// categories column
				wc_get_template( 'loop/categories.php' );

				// price column
				wc_get_template( 'loop/price.php' );

				// add-to-cart button
				woocommerce_template_loop_add_to_cart( array(
					'before' => '<div class="column">',
					'after'  => '</div>',
				) );
				?>
			</div>
		<?php
	}
endif;

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'nucleus_wc_before_shop_loop_item_title' );

if ( ! function_exists( 'nucleus_wc_shop_loop_item_title' ) ) :
	/**
	 * Show the product title in the product loop
	 *
	 * @see woocommerce/content-product.php
	 */
	function nucleus_wc_shop_loop_item_title() {
		$before = '<h3 class="shop-title">' . '<a href="' . esc_url( get_the_permalink() ) . '">';
		$after  = '</a></h3>';

		echo nucleus_get_text( get_the_title(), $before, $after );
	}
endif;

remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'nucleus_wc_shop_loop_item_title' );

/**
 * Display the product item short description
 *
 * Also close div.description
 *
 * @see woocommerce/content-product.php
 */
function nucleus_wc_after_shop_loop_item_title() {
	wc_get_template( 'single-product/short-description.php' );

	// close div.description
	echo '</div>';
}

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'nucleus_wc_after_shop_loop_item_title' );

/*
 * Remove unnecessary WooCommerce wrappers
 *
 * @see woocommerce/content-product.php
 */
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

/**
 * Remove the .button class from "Add to Cart"
 *
 * @param array      $classes Add to cart button classes
 * @param WC_Product $product Product object
 *
 * @return array
 */
function nucleus_wc_add_to_cart_class( $classes, $product ) {
	if ( false !== ( $key = array_search( 'button', $classes ) ) ) {
		unset( $classes[ $key ] );
	}

	return $classes;
}

add_filter( 'woocommerce_loop_add_to_cart_class', 'nucleus_wc_add_to_cart_class', 10, 2 );

/**
 * Wrap "Add to Cart" button to div.column
 *
 * @param string     $link    Add to Cart button HTML
 * @param WC_Product $product Product object
 *
 * @return string
 */
function nucleus_wc_add_to_cart_link( $link, $product ) {
	return '<div class="column">' . $link . '</div>';
}

add_filter( 'woocommerce_loop_add_to_cart_link', 'nucleus_wc_add_to_cart_link', 10, 2 );

/**
 * Custom markup from/to price
 *
 * @see WC_Product::get_price_html_from_to()
 *
 * @param string     $price   Price markup
 * @param int        $from    Regular price
 * @param int        $to      Sale price
 * @param WC_Product $product Product object
 *
 * @return string
 */
function nucleus_wc_get_price_html_from_to( $price, $from, $to, $product ) {
	$_from = is_numeric( $from ) ? wc_price( $from ) : $from;
	$_from = '<span class="old-price">' . $_from . '</span>';

	$_to = is_numeric( $to ) ? wc_price( $to ) : $to;
	$_to = '<span class="price">' . $_to . '</span>';

	return $_from . $_to;
}

add_filter( 'woocommerce_get_price_html_from_to', 'nucleus_wc_get_price_html_from_to', 10, 4 );

/**
 * Strip all tags from wp_price
 *
 * @see wc_price()
 *
 * @param string $markup Markup
 * @param string $price  Price
 * @param array  $args   Args
 *
 * @return string
 */
function nucleus_wc_price( $markup, $price, $args ) {
	return strip_tags( $markup );
}

add_filter( 'wc_price', 'nucleus_wc_price', 10, 3 );

/**
 * Fix the Regular price markup according to design
 *
 * @see WC_Product::get_price_html()
 *
 * @param string     $price   Price
 * @param WC_Product $product Product object
 *
 * @return string
 */
function nucleus_wc_price_html( $price, $product ) {
	if ( is_product() ) {
		return $price;
	}

	return '<span class="price">' . $price . '</span>';
}

add_filter( 'woocommerce_price_html', 'nucleus_wc_price_html', 10, 2 );

/**
 * Fix the "Free" price markup in catalog
 *
 * @see WC_Product::get_price_html()
 *
 * @param string     $price   Markup
 * @param WC_Product $product Product object
 *
 * @return string
 */
function nucleus_wc_free_price_html( $price, $product ) {
	return '<span class="price">' . esc_html__( 'Free!', 'nucleus' ) . '</span>';
}

add_filter( 'woocommerce_free_price_html', 'nucleus_wc_free_price_html', 10, 2 );

/**
 * Show product gallery
 *
 * @see woocommerce/content-single-product.php
 */
function nucleus_wc_before_single_product_summary() {
	wc_get_template( 'single-product/product-image.php' );
}

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
add_action( 'woocommerce_before_single_product_summary', 'nucleus_wc_before_single_product_summary' );

/**
 * Show the categories in Single Product
 *
 * @see woocommerce/content-single-product.php
 */
function nucleus_wc_single_product_category() {
	global $post, $product;

	$cat_count = count( get_the_terms( $post->ID, 'product_cat' ) );
	if ( $cat_count > 0 ) : ?>
		<div class="shop-meta space-bottom-2x">
			<span class="hidden-xs"><?php esc_html_e( 'in', 'nucleus' ); ?></span>
			<span>
				<?php echo '<i class="icon-ribbon hidden-xs"></i>', $product->get_categories( ', ' ); ?>
			</span>
		</div>
	<?php
	endif;
	unset( $cat_count );
}

add_action( 'woocommerce_single_product_summary', 'nucleus_wc_single_product_category', 3 );

/*
 * Change the priority of the Single Product excerpt
 *
 * @see woocommerce/content-single-product.php
 */
remove_action('woocommerce_single_product_summary','woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 10 );

/*
 * Removes the built-in actions for single product page
 *
 * @see woocommerce/content-single-product.php
 */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price',10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

/**
 * Remove the "Description" tab
 *
 * @param array $tabs
 *
 * @return array
 */
function nucleus_wc_remove_description_tab( $tabs ) {
	if ( array_key_exists( 'description', $tabs ) ) {
		unset( $tabs['description'] );
	}

	return $tabs;
}

add_filter( 'woocommerce_product_tabs', 'nucleus_wc_remove_description_tab' );

/**
 * Wrap the "author" and "name" fields into the columns in reviews form
 *
 * @param array $comment_form Comment form args
 *
 * @return array
 */
function nucleus_wc_product_review_comment_form_args( $comment_form ) {
	$author = $comment_form['fields']['author'];
	$email  = $comment_form['fields']['email'];

	$comment_form['fields']['author'] = '<div class="col-sm-6">' . $author . '</div>';
	$comment_form['fields']['email'] = '<div class="col-sm-6">' . $email . '</div>';

	$comment_form['submit_button'] = '<button type="submit" name="%1$s" id="%2$s" class="%3$s">%4$s</button>';
	$comment_form['class_submit'] = 'btn btn-default btn-block';

	return $comment_form;
}

add_filter( 'woocommerce_product_review_comment_form_args', 'nucleus_wc_product_review_comment_form_args' );

/**
 * Returns the custom Page Title for WooCommerce Pages
 *
 * @param string $title Page Title
 *
 * @return string
 */
function nucleus_wc_page_title( $title ) {
	if ( ! nucleus_is_woocommerce() ) {
		return $title;
	}

	if ( is_shop() && apply_filters( 'woocommerce_show_page_title', true ) ) {
		$title = woocommerce_page_title( false );
	}

	return $title;
}

add_filter( 'nucleus_page_title', 'nucleus_wc_page_title' );

/**
 * Add "Custom Title" option for single product page
 *
 * @see woocommerce/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
 */
function nucleus_wc_product_options_pricing() {
	// custom title
	woocommerce_wp_text_input( array(
		'id'          => '_custom_title',
		'label'       => esc_html__( 'Custom Title', 'nucleus' ),
		'description' => esc_html__( 'Page Title. Leave this field empty to disable Breadcrumbs', 'nucleus' ),
	) );
}

add_action( 'woocommerce_product_options_pricing', 'nucleus_wc_product_options_pricing' );

/**
 * Save "Custom Title" field
 *
 * @see woocommerce/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
 */
function nucleus_wc_process_product_meta( $post_id ) {
	if ( array_key_exists( '_custom_title', $_POST ) ) {
		update_post_meta( $post_id, '_custom_title', sanitize_text_field( $_POST['_custom_title'] ) );
	}
}

add_action( 'woocommerce_process_product_meta_simple', 'nucleus_wc_process_product_meta' );
add_action( 'woocommerce_process_product_meta_external', 'nucleus_wc_process_product_meta' );

/*
 * Checkout page
 */
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

/*
 * Catalog
 */

/**
 * Display the Shop filters in Catalog
 *
 * @see  woocommerce/archive-product.php
 * @hook woocommerce_before_shop_loop
 */
function nucleus_wc_shop_filters() {
	if ( 'no-sidebar' !== nucleus_get_option( 'layout_shop', 'no-sidebar' ) ) {
		return;
	}

	wc_get_template( 'shop/filters.php' );
}

add_action( 'woocommerce_before_shop_loop', 'nucleus_wc_shop_filters' );

if ( ! function_exists('nucleus_wc_shop_categories_filter')) :
	/**
	 * Display the product categories
	 *
	 * @see woocommerce/shop/filters.php
	 */
	function nucleus_wc_shop_categories_filter() {

		$args = array(
			'show_count'   => 1,
			'hierarchical' => 0,
			'taxonomy'     => 'product_cat',
			'hide_empty'   => 1,
			'menu_order' => false,
			'orderby' => 'title',
		);

		include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-list-walker.php' );

		$args['walker']                     = new WC_Product_Cat_List_Walker;
		$args['title_li']                   = '';
		$args['pad_counts']                 = 1;
		$args['show_option_none']           = esc_html__( 'No product categories exist.', 'nucleus' );
		$args['current_category']           = '';
		$args['current_category_ancestors'] = array();

		echo '<ul class="product-categories">';

		wp_list_categories( apply_filters( 'woocommerce_product_categories_widget_args', $args ) );

		echo '</ul>';
	}
endif;

if ( ! function_exists( 'nucleus_wc_shop_price_filter' ) ) :
	/**
	 * Display the price filter in Shop
	 *
	 * @see woocommerce/shop/filters.php
	 */
	function nucleus_wc_shop_price_filter() {
		global $wp, $wp_the_query;

		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}

		if ( ! $wp_the_query->post_count ) {
			return;
		}

		$min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '';
		$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : '';

		// Find min and max price in current result set
		$prices = nucleus_wc_get_filtered_price();
		$min    = floor( $prices->min_price );
		$max    = ceil( $prices->max_price );

		if ( $min === $max ) {
			return;
		}

		if ( '' === get_option( 'permalink_structure' ) ) {
			$form_action = remove_query_arg( array(
				'page',
				'paged',
			), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
		} else {
			$form_action = preg_replace( '%\/page/[0-9]+%', '', home_url( trailingslashit( $wp->request ) ) );
		}

		/**
		 * Adjust max if the store taxes are not displayed how they are stored.
		 * Min is left alone because the product may not be taxable.
		 * Kicks in when prices excluding tax are displayed including tax.
		 */
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_classes = array_merge( array( '' ), WC_Tax::get_tax_classes() );
			$class_max   = $max;

			foreach ( $tax_classes as $tax_class ) {
				if ( $tax_rates = WC_Tax::get_rates( $tax_class ) ) {
					$class_max = $max + WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max, $tax_rates ) );
				}
			}

			$max = $class_max;
		}

		?>
		<form method="get" action="<?php echo esc_url( $form_action ); ?>">
			<div class="widget_price_filter">
				<div class="price_slider_wrapper">
					<div class="price_slider" style="display:none;"></div>
					<div class="price_slider_amount">
						<input type="text" id="min_price" name="min_price"
						       value="<?php echo esc_attr( $min_price ); ?>"
						       data-min="<?php echo esc_attr( apply_filters( 'woocommerce_price_filter_widget_min_amount', $min ) ); ?>"
						       placeholder="<?php echo esc_attr__( 'Min price', 'nucleus' ); ?>">
						<input type="text" id="max_price" name="max_price"
						       value="<?php echo esc_attr( $max_price ); ?>"
						       data-max="<?php echo esc_attr( apply_filters( 'woocommerce_price_filter_widget_max_amount', $max ) ); ?>"
						       placeholder="<?php echo esc_attr__( 'Max price', 'nucleus' ); ?>">
						<button type="submit" class="button"><?php echo esc_html__( 'Filter', 'nucleus' ); ?></button>
						<div class="price_label" style="display:none;">
							<span class="from"></span> &mdash; <span class="to"></span>
						</div>
						<?php
						// Remember current filters/search
						if ( get_search_query() ) {
							echo '<input type="hidden" name="s" value="' . get_search_query() . '">';
						}

						if ( ! empty( $_GET['post_type'] ) ) {
							echo '<input type="hidden" name="post_type" value="' . esc_attr( $_GET['post_type'] ) . '">';
						}

						if ( ! empty ( $_GET['product_cat'] ) ) {
							echo '<input type="hidden" name="product_cat" value="' . esc_attr( $_GET['product_cat'] ) . '">';
						}

						if ( ! empty( $_GET['product_tag'] ) ) {
							echo '<input type="hidden" name="product_tag" value="' . esc_attr( $_GET['product_tag'] ) . '">';
						}

						if ( ! empty( $_GET['orderby'] ) ) {
							echo '<input type="hidden" name="orderby" value="' . esc_attr( $_GET['orderby'] ) . '">';
						}

						if ( ! empty( $_GET['min_rating'] ) ) {
							echo '<input type="hidden" name="min_rating" value="' . esc_attr( $_GET['min_rating'] ) . '">';
						}

						if ( $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes() ) {
							foreach ( $_chosen_attributes as $attribute => $data ) {
								$taxonomy_filter = 'filter_' . str_replace( 'pa_', '', $attribute );

								echo '<input type="hidden" name="' . esc_attr( $taxonomy_filter ) . '" value="' . esc_attr( implode( ',', $data['terms'] ) ) . '">';

								if ( 'or' == $data['query_type'] ) {
									echo '<input type="hidden" name="' . esc_attr( str_replace( 'pa_', 'query_type_', $attribute ) ) . '" value="or">';
								}
							}
						}
						?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</form>
		<?php
	}
endif;

if ( ! function_exists( 'nucleus_wc_get_filtered_price' ) ) :
	/**
	 * Get filtered min price for current products.
	 *
	 * @return int
	 */
	function nucleus_wc_get_filtered_price() {
		global $wpdb, $wp_the_query;

		$args       = $wp_the_query->query_vars;
		$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
		$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

		if ( ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
			$tax_query[] = array(
				'taxonomy' => $args['taxonomy'],
				'terms'    => array( $args['term'] ),
				'field'    => 'slug',
			);
		}

		foreach ( $meta_query as $key => $query ) {
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}

		$meta_query = new WP_Meta_Query( $meta_query );
		$tax_query  = new WP_Tax_Query( $tax_query );

		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		$sql = "SELECT min( CAST( price_meta.meta_value AS UNSIGNED ) ) as min_price, max( CAST( price_meta.meta_value AS UNSIGNED ) ) as max_price FROM {$wpdb->posts} ";
		$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " 	WHERE {$wpdb->posts}.post_type = 'product'
					AND {$wpdb->posts}.post_status = 'publish'
					AND price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
					AND price_meta.meta_value > '' ";
		$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

		return $wpdb->get_row( $sql );
	}
endif;

if ( ! function_exists( 'nucleus_wc_shop_sorting_filter' ) ) :
	/**
	 * Display the "Sorting" filter
	 *
	 * @see woocommerce/shop/filters.php
	 */
	function nucleus_wc_shop_sorting_filter() {
		global $wp_query;

		if ( 1 === $wp_query->found_posts || ! woocommerce_products_will_display() ) {
			return;
		}

		$orderby                 = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		$show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		$catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
			'menu_order' => esc_html__( 'Default sorting', 'nucleus' ),
			'popularity' => esc_html__( 'Sort by popularity', 'nucleus' ),
			'rating'     => esc_html__( 'Sort by average rating', 'nucleus' ),
			'date'       => esc_html__( 'Sort by newness', 'nucleus' ),
			'price'      => esc_html__( 'Sort by price: low to high', 'nucleus' ),
			'price-desc' => esc_html__( 'Sort by price: high to low', 'nucleus' ),
		) );

		if ( ! $show_default_orderby ) {
			unset( $catalog_orderby_options['menu_order'] );
		}

		if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
			unset( $catalog_orderby_options['rating'] );
		}

		wc_get_template( 'loop/orderby.php', array(
			'catalog_orderby_options' => $catalog_orderby_options,
			'orderby'                 => $orderby,
			'show_default_orderby'    => $show_default_orderby,
		) );

	}
endif;

/*
 * Remove built-in sorting
 */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/**
 * Add cart fragments
 *
 * Ensure cart contents update when products are added to the cart via AJAX
 *
 * @param  array $fragments Fragments to refresh via AJAX.
 *
 * @return array
 */
function nucleus_wc_cart_fragments( $fragments ) {
	ob_start();
	nucleus_the_cart();
	$fragments['a.cart-contents'] = ob_get_clean();

	return $fragments;
}

if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3', '>=' ) ) {
	add_filter( 'woocommerce_add_to_cart_fragments', 'nucleus_wc_cart_fragments' );
} else {
	add_filter( 'add_to_cart_fragments', 'nucleus_wc_cart_fragments' );
}

/*
 * Cart page
 */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10 );
