<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
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
 * @version       2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="widget widget_shop_totals woocommerce-checkout-review-order-table">
	<div class="subtotal cart-subtotal">
		<div class="text-default"><?php esc_html_e( 'Subtotal', 'nucleus' ); ?></div>
		<div class="text-right"><?php wc_cart_totals_subtotal_html(); ?></div>
	</div>
	<div class="panel-group" id="accordion">
		<?php if ( wc_coupons_enabled() && ( $coupons = WC()->cart->get_coupons() ) ) : ?>
			<div class="panel">
				<div class="panel-heading">
					<a class="panel-title collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseCoupon">
						<?php esc_html_e( 'Coupon Code', 'nucleus' ); ?>
					</a>
				</div>
				<div id="collapseCoupon" class="panel-collapse collapse" role="tabpanel">
					<div class="panel-body">
						<?php foreach ( $coupons as $code => $coupon ) : ?>
							<div class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
								<span class="coupon-label"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
								<span class="coupon-html"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
							</div>
						<?php endforeach; unset( $code, $coupon ); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<div class="panel">
				<div class="panel-heading">
					<a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapseShipping">
						<?php esc_html_e( 'Shipping', 'nucleus' ); ?>
					</a>
				</div>
				<div id="collapseShipping" class="panel-collapse collapse">
					<div class="panel-body">
						<?php
						do_action( 'woocommerce_review_order_before_shipping' );
						wc_cart_totals_shipping_html();
						do_action( 'woocommerce_review_order_after_shipping' );
						?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php $fees = WC()->cart->get_fees(); if ( ! empty( $fees ) ) : ?>
			<div class="panel">
				<div class="panel-heading">
					<a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapseFees">
						<?php esc_html_e( 'Fees', 'nucleus' ); ?>
					</a>
				</div>
				<div id="collapseFees" class="panel-collapse collapse">
					<div class="panel-body">
						<?php foreach ( (array) $fees as $fee ) : ?>
							<span class="fee-label"><?php echo esc_html( $fee->name ); ?></span>
							<span class="fee-amount"><?php wc_cart_totals_fee_html( $fee ); ?></span>
						<?php endforeach; unset( $fee ); ?>
					</div>
				</div>
			</div>
		<?php endif; unset( $fees ); ?>

		<?php if ( wc_tax_enabled() && 'excl' === WC()->cart->tax_display_cart ) : ?>
			<div class="panel">
				<div class="panel-heading">
					<a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapseTax">
						<?php esc_html_e( 'Tax', 'nucleus' ); ?>
					</a>
				</div>
				<div id="collapseTax" class="panel-collapse collapse">
					<div class="panel-body">
						<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
							<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
								<div class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
									<span class="tax-label"><?php echo esc_html( $tax->label ); ?></span>
									<span class="tax-amount"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
								</div>
							<?php endforeach; ?>
						<?php else : ?>
							<div class="tax-total">
								<span class="tax-label tax-or-vat"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
								<span class="tax-amount tax-total-amount"><?php wc_cart_totals_taxes_total_html(); ?></span>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class="panel">
			<div class="panel-heading">
				<a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapsePayment">
					<?php esc_html_e( 'Payment', 'nucleus' ); ?>
				</a>
			</div>
			<div id="collapsePayment" class="panel-collapse collapse in">
				<div class="panel-body">
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

								unset( $available_gateways );
								?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div><!-- .panel -->
	</div>

	<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

	<div class="total order-total">
		<div class="text-default"><?php esc_html_e( 'Total', 'nucleus' ); ?></div>
		<div class="text-right"><strong><?php wc_cart_totals_order_total_html(); ?></strong></div>
	</div>

	<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	<div class="form-row place-order">
		<noscript>
			<?php esc_html_e( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'nucleus' ); ?>
			<br/><input type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'nucleus' ); ?>" />
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<?php
		$order_button_text = apply_filters( 'woocommerce_order_button_text', esc_html__( 'Checkout', 'nucleus' ) );
		$order_button_html = sprintf( '<input type="submit" class="btn btn-primary btn-block" name="woocommerce_checkout_place_order" id="place_order" value="%1$s" data-value="%1$s">',
			esc_attr( $order_button_text )
		);

		echo apply_filters( 'woocommerce_order_button_html', $order_button_html );
		unset( $order_button_html, $order_button_text );
		?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout' ); ?>
	</div>
</section>

