<?php
/**
 * Checkout coupon form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-coupon.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  8guild
 * @package Nucleus\WooCommerce
 * @version 2.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! wc_coupons_enabled() ) {
	return;
}

if ( empty( WC()->cart->applied_coupons ) ) {
	$info_message = apply_filters( 'woocommerce_checkout_coupon_message', __( 'Have a coupon?', 'nucleus' ) . ' <a href="#" class="showcoupon">' . __( 'Click here to enter your code', 'nucleus' ) . '</a>' );
	wc_print_notice( $info_message, 'notice' );
}
?>

<form class="checkout_coupon" method="post" style="display:none">
	<div class="row">
		<div class="col-sm-8">
			<input type="text" name="coupon_code" class="form-control space-bottom-none" id="coupon_code"
			       placeholder="<?php esc_attr_e( 'Enter code here', 'nucleus' ); ?>">
		</div>
		<div class="col-sm-4">
			<input type="submit" name="apply_coupon" value="<?php esc_html_e( 'Apply Coupon', 'nucleus' ); ?>"
			       class="btn btn-default btn-block space-top-none space-bottom-none">
		</div>
	</div>
</form>