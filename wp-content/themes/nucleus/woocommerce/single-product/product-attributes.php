<?php
/**
 * Product attributes
 *
 * Used by list_attributes() in the products class.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-attributes.php.
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
 * @version     2.1.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$has_row    = false;
/** @var WC_Product_Simple $attributes */
$attributes = $product->get_attributes();

ob_start();

if ( $product->enable_dimensions_display() ) :

	if ( $product->has_weight() ) :
		$has_row = true;
		?>
		<span class="text-bold"><?php esc_html_e( 'Weight', 'nucleus' ); ?></span>
		<p class="text-gray"><?php echo wc_format_localized_decimal( $product->get_weight() ) . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ); ?></p>
		<?php
	endif;

	if ( $product->has_dimensions() ) :
		$has_row = true;
		?>
		<span class="text-bold"><?php esc_html_e( 'Dimensions', 'nucleus' ); ?></span>
		<p class="text-gray"><?php echo $product->get_dimensions(); ?></p>
		<?php
	endif;

endif;

foreach ( $attributes as $attribute ) :
	if ( empty( $attribute['is_visible'] )
	     || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) )
	) {
		continue;
	} else {
		$has_row = true;
	}

	?>
	<span class="text-bold"><?php echo wc_attribute_label( $attribute['name'] ); ?></span>
	<p class="text-gray">
		<?php
		if ( $attribute['is_taxonomy'] ) :
			$values = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
			echo apply_filters( 'woocommerce_attribute', esc_html( implode( ', ', $values ) ), $attribute, $values );
		else :
			// Convert pipes to commas and display values
			$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
			echo apply_filters( 'woocommerce_attribute', esc_html( implode( ', ', $values ) ), $attribute, $values );
		endif;
		?>
	</p>
	<?php
endforeach;

if ( $has_row ) {
	echo ob_get_clean();
} else {
	ob_end_clean();
}
