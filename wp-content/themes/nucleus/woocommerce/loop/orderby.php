<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
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
 * @version       2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<form class="woocommerce-ordering nucleus-wc-sorting" method="get">
	<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
		<label class="radio orderby">
			<input type="radio" name="orderby" value="<?php echo esc_attr( $id ); ?>"
			       <?php checked( $orderby, $id ); ?>> <?php echo esc_html( $name ); ?>
		</label>
	<?php endforeach; unset( $id, $name ); ?>
	<?php
	// Keep query string vars intact
	foreach ( $_GET as $key => $val ) :
		if ( 'orderby' === $key || 'submit' === $key ) {
			continue;
		}
		if ( is_array( $val ) ) {
			foreach( $val as $innerVal ) {
				echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
			}
		} else {
			echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
		}
	endforeach;
	?>
</form>
