<?php
/**
 * Output a single payment method
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment-method.php.
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
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
	<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="radio">
		<input type="radio" name="payment_method"
		       id="payment_method_<?php echo esc_attr( $gateway->id ); ?>"
		       data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>"
		       value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?>>
		<?php echo esc_html( $gateway->get_title() ); ?>
	</label>
</div>
