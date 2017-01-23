<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see           https://docs.woothemes.com/document/template-structure/
 * @author        8guild
 * @package       Nucleus\WooCommerce
 * @version       2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$add_to_cart_class = array();
if ( isset( $class ) ) {
	$add_to_cart_class = explode( ' ', $class );
}

/**
 * Filter the "Add to Cart" button class
 *
 * @param array      $class   Button class
 * @param WC_Product $product Product object
 */
$add_to_cart_class = apply_filters( 'woocommerce_loop_add_to_cart_class', array_merge( $add_to_cart_class, array(
	'btn',
	'btn-sm',
	'btn-primary',
	'btn-icon-right',
	'waves-effect',
	'waves-light',
) ), $product );

/**
 * Filter the "Add to Cart" button icon
 *
 * @param string     $icon    Icon HTML
 * @param WC_Product $product Product object
 */
$add_to_cart_icon = apply_filters( 'woocommerce_loop_add_to_cart_icon', '<i class="icon-bag"></i>', $product );

/**
 * Filter the "Add to Cart" button attributes
 *
 * @param array      $attr    Button attributes
 * @param WC_Product $product Product object
 */
$add_to_cart_attr = apply_filters( 'woocommerce_loop_add_to_cart_attr', array(
	'href'             => esc_url( $product->add_to_cart_url() ),
	'data-quantity'    => esc_attr( isset( $quantity ) ? $quantity : 1 ),
	'data-product_id'  => esc_attr( $product->id ),
	'data-product_sku' => esc_attr( $product->get_sku() ),
	'class'            => nucleus_get_class_set( $add_to_cart_class ),
	'rel'              => 'nofollow',
) );

/**
 * Filter the "Add to Cart" button HTML
 *
 * @param string     $button  Button HTML
 * @param WC_Product $product Product object
 */
echo apply_filters( 'woocommerce_loop_add_to_cart_link',
	nucleus_get_tag( 'a', $add_to_cart_attr, $product->add_to_cart_text() . $add_to_cart_icon ),
	$product
);

unset( $add_to_cart_class, $add_to_cart_icon, $add_to_cart_attr );
