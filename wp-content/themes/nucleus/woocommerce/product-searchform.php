<?php
/**
 * The template for displaying product search form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/product-searchform.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form role="search" method="get" class="woocommerce-product-search search-box" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
	<label class="screen-reader-text" for="woocommerce-product-search-field"><?php esc_html_e( 'Search for:', 'nucleus' ); ?></label>
	<input type="search" name="s" class="form-control"
	       id="woocommerce-product-search-field"
	       placeholder="<?php echo esc_attr_x( 'Search Products&hellip;', 'placeholder', 'nucleus' ); ?>"
	       value="<?php echo get_search_query(); ?>">
	<button type="submit"><i class="icon-search"></i></button>
	<input type="hidden" name="post_type" value="product" />
</form>
