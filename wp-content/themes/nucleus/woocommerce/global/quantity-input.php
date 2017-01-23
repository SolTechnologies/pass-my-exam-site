<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="count">
	<span class="label"><?php esc_html_e( 'Qty:', 'nucleus' ); ?> </span>
	<div class="count-input">
		<a class="incr-btn" data-action="decrease" href="#">-</a>
		<input type="text"
		       step="<?php echo esc_attr( $step ); ?>"
		       min="<?php echo esc_attr( $min_value ); ?>"
		       max="<?php echo esc_attr( $max_value ); ?>"
		       name="<?php echo esc_attr( $input_name ); ?>"
		       value="<?php echo esc_attr( $input_value ); ?>"
		       class="quantity qty"
		       pattern="<?php echo esc_attr( $pattern ); ?>"
		       inputmode="<?php echo esc_attr( $inputmode ); ?>" />
		<a class="incr-btn" data-action="increase" href="#">+</a>
	</div>
</div>
