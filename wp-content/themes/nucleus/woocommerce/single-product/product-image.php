<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
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
 * @version       2.6.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;

$attachments = array();
if ( has_post_thumbnail() ) {
	$attachments[] = get_post_thumbnail_id();
}

$attachment_ids = $product->get_gallery_attachment_ids();
$attachments    = array_merge( $attachments, (array) $attachment_ids );
$gallery_count  = count( $attachments );

$gallery = '';

/*
 * Data API:
 *
 * data-loop="true/false" enable/disable looping
 * data-autoplay="true/false" enable/disable carousel autoplay
 * data-interval="3000" autoplay interval timeout in miliseconds
 * data-autoheight="true/false" enable/disable autoheight with transition
 *
 * Simply add necessary data attribute to the ".image-carousel" with
 * appropriate value to adjust carousel functionality.
 */
$gallery .= '<div class="image-carousel" data-loop="true" data-autoheight="true">';
if ( $gallery_count > 1 ) {
	$gallery .= '<div class="inner">';
}

foreach ( $attachments as $attachment ) {
	$gallery .= wp_get_attachment_image( (int) $attachment, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
}

if ( $gallery_count > 1 ) {
	$gallery .= '</div>';
}

$gallery .= '</div>';

/**
 * Filter the gallery
 *
 * @param string     $html    Gallery html
 * @param WP_Post    $post    Post object
 * @param WC_Product $product Product object
 */
echo apply_filters( 'woocommerce_single_product_image_html', $gallery, $post, $product );

do_action( 'woocommerce_product_thumbnails' );
