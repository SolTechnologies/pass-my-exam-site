<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

// availability
$availability      = $product->get_availability();
$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );

if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) :
	?>
	<span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'nucleus' ); ?></span>
	<?php
endif;

if ( $product->is_in_stock() ) : ?>

	<div class="shop-tools">

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

		<form class="cart" method="post" enctype='multipart/form-data'>

			<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

			<?php
			if ( ! $product->is_sold_individually() ) {
				woocommerce_quantity_input( array(
					'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
					'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
					'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 )
				) );
			}
			?>

			<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />
			<button type="submit" class="single_add_to_cart_button btn btn-primary btn-icon-right waves-effect waves-light">
				<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
				<i class="icon-bag"></i>
			</button>

			<span itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="text-lg">
				<?php echo $product->get_price_html(); ?>

				<meta itemprop="price" content="<?php echo esc_attr( $product->get_display_price() ); ?>" />
				<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>" />
				<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
			</span>

			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
		</form>
		
	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

	</div>

<?php endif;
