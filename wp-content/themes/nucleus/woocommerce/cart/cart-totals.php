<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
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
 * @version       2.3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<section class="widget widget_shop_totals cart_totals <?php if ( WC()->customer->has_calculated_shipping() ) echo 'calculated_shipping'; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<div class="subtotal cart-subtotal">
		<div class="text-default"><?php esc_html_e( 'Subtotal', 'nucleus' ); ?></div>
		<div class="text-right" data-title="<?php esc_attr_e( 'Subtotal', 'nucleus' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></div>
	</div>

	<div class="panel-group" id="accordion">

		<?php if ( wc_coupons_enabled() ) : ?>
			<div class="panel">
				<div class="panel-heading">
					<a class="panel-title collapsed"
					   data-toggle="collapse"
					   data-parent="#accordion"
					   href="#collapseCoupons"><?php esc_html_e( 'Coupon Code', 'nucleus' ); ?></a>
				</div>
				<div id="collapseCoupons" class="panel-collapse collapse" role="tabpanel">
					<div class="panel-body">

						<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
							<div class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
								<span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
								<span data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>">
									<?php wc_cart_totals_coupon_html( $coupon ); ?>
								</span>
							</div>
						<?php endforeach; unset( $code, $coupon ); ?>

						<input type="text"
						       name="coupon_code"
						       class="form-control"
						       id="coupon_code"
						       placeholder="<?php esc_attr_e( 'Enter code here', 'nucleus' ); ?>">
						<input type="submit"
						       name="apply_coupon"
						       value="<?php esc_html_e( 'Apply Coupon', 'nucleus' ); ?>"
						       class="btn btn-default btn-block">
						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<div class="panel">
				<div class="panel-heading">
					<a class="panel-title collapsed"
					   data-toggle="collapse"
					   data-parent="#accordion"
					   href="#collapseShipping">
						<?php esc_html_e( 'Shipping', 'nucleus' ); ?>
					</a>
				</div>
				<div id="collapseShipping" class="panel-collapse collapse">
					<div class="panel-body">
						<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

						<?php wc_cart_totals_shipping_html(); ?>

						<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
					</div>
				</div>
			</div>
		<?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
			<div class="panel">
				<div class="panel-heading">
					<a class="panel-title collapsed"
					   data-toggle="collapse"
					   data-parent="#accordion"
					   href="#collapseShipping">
						<?php esc_html_e( 'Shipping', 'nucleus' ); ?>
					</a>
				</div>
				<div id="collapseShipping" class="panel-collapse collapse">
					<div class="panel-body" data-title="<?php esc_attr_e( 'Shipping', 'nucleus' ); ?>">
						<?php woocommerce_shipping_calculator(); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php $fees = WC()->cart->get_fees(); if ( ! empty( $fees ) ) : ?>
			<div class="panel">
				<div class="panel-heading">
					<a class="panel-title collapsed"
					   data-toggle="collapse"
					   data-parent="#accordion"
					   href="#collapseFees">
						<?php esc_html_e( 'Fees', 'nucleus' ); ?>
					</a>
				</div>
				<div id="collapseFees" class="panel-collapse collapse">
					<div class="panel-body">
						<?php foreach ( (array) $fees as $fee ) : ?>
							<span><?php echo esc_html( $fee->name ); ?></span>
							<span data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></span>
						<?php endforeach; unset( $fee ); ?>
					</div>
				</div>
			</div>
		<?php endif; unset( $fees ); ?>

		<?php if ( wc_tax_enabled() && 'excl' === WC()->cart->tax_display_cart ) : ?>
			<div class="panel">
				<div class="panel-heading">
					<a class="panel-title collapsed"
					   data-toggle="collapse"
					   data-parent="#accordion"
					   href="#collapseTax">
						<?php esc_html_e( 'Tax', 'nucleus' ); ?>
					</a>
				</div>
				<div id="collapseTax" class="panel-collapse collapse">
					<div class="panel-body">
						<?php
						$taxable_address = WC()->customer->get_taxable_address();
						$estimated_text  = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping()
							? sprintf( __( 'estimated for %s', 'nucleus' ), WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] )
							: '';

						if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
							<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
								<div class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
									<span><?php echo esc_html( $tax->label ) . $estimated_text; ?></span>
									<span data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
								</div>
							<?php endforeach; ?>
						<?php else : ?>
							<div class="tax-total">
								<span><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; ?></span>
								<span data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></span>
							</div>
						<?php endif;
						?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

	<div class="total order-total">
		<div class="text-default"><?php esc_html_e( 'Total', 'nucleus' ); ?></div>
		<div class="text-right" data-title="<?php esc_attr_e( 'Total', 'nucleus' ); ?>"><strong><?php wc_cart_totals_order_total_html(); ?></strong></div>
	</div>

	<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	<input type="submit" name="update_cart"
	       value="<?php esc_html_e( 'Update Cart', 'nucleus' ); ?>"
	       class="btn btn-default btn-block">

	<?php do_action( 'woocommerce_cart_actions' ); ?>
	<?php wp_nonce_field( 'woocommerce-cart' ); ?>

	<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</section>
