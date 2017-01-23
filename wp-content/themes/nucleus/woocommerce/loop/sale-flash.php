<?php
/**
 * Product loop sale flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sale-flash.php.
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
 * @version     1.6.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;

if ( $product->is_on_sale() ) {
	$sale = '<span class="shop-label label-danger">' . esc_html__( 'Sale', 'nucleus' ) . '</span>';
	/**
	 * Filter the WooCommerce Sale Flash
	 *
	 * @param string     $sale    Sale flash HTML
	 * @param WP_Post    $post    Post object
	 * @param WC_Product $product Product object
	 */
	echo apply_filters( 'woocommerce_sale_flash', $sale, $post, $product );
	unset( $sale );
}
