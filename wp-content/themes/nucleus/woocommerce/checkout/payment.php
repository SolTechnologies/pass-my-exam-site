<?php
/**
 * Checkout Payment Section
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woothemes.com/document/template-structure/
 * @author 		8guild
 * @package 	Nucleus\WooCommerce
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_ajax() ) :
	do_action( 'woocommerce_review_order_before_payment' );
endif;
?>

<div class="woocommerce-checkout-payment">
	<?php if ( WC()->cart->needs_payment() ) : ?>
		<div class="wc_payment_methods form-group">
			<?php
			if ( WC()->cart->needs_payment() ) {
				$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
				WC()->payment_gateways()->set_current_gateway( $available_gateways );
			} else {
				$available_gateways = array();
			}

			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				$no_available_payment_methods_message = WC()->customer->get_country()
					? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'nucleus' )
					: esc_html__( 'Please fill in your details above to see available payment methods.', 'nucleus' );

				echo apply_filters( 'woocommerce_no_available_payment_methods_message', $no_available_payment_methods_message );
				unset( $no_available_payment_methods_message );
			}
			?>
		</div>
	<?php endif; ?>
</div>

<?php
if ( ! is_ajax() ) :
	do_action( 'woocommerce_review_order_after_payment' );
endif;