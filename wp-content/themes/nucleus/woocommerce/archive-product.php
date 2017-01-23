<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woothemes.com/document/template-structure/
 * @author      8guild
 * @package     Nucleus\WooCommerce
 * @version     2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/**
 * Before main content hook
 *
 * @see nucleus_wc_open_wrapper()
 */
do_action( 'woocommerce_before_main_content' );

/**
 * woocommerce_archive_description hook
 */
do_action( 'woocommerce_archive_description' );

if ( have_posts() ) :

	/**
	 * woocommerce_before_shop_loop hook.
	 *
	 * @see nucleus_wc_shop_filters() 10
	 */
	do_action( 'woocommerce_before_shop_loop' );

	woocommerce_product_loop_start();
	woocommerce_product_subcategories();

	while ( have_posts() ) :
		the_post();
		wc_get_template_part( 'content', 'product' );
	endwhile; // end of the loop.

	woocommerce_product_loop_end();

	/**
	 * woocommerce_after_shop_loop hook.
	 *
	 * @see woocommerce_pagination() - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );

elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after'  => woocommerce_product_loop_end( false ) ) ) ) :

	wc_get_template( 'loop/no-products-found.php' );

endif;

/**
 * woocommerce_sidebar hook.
 *
 * @see woocommerce_get_sidebar() 10
 */
do_action( 'woocommerce_sidebar' );

/**
 * woocommerce_after_main_content hook.
 *
 * @see woocommerce_output_content_wrapper_end() - 10
 */
do_action( 'woocommerce_after_main_content' );

get_footer();
