<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/price.php.
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
 * @version       1.6.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$price_html = $product->get_price_html();
if ( $price_html ) {
	echo '<div class="column">', $price_html, '</div>';
}

unset( $price_html );
