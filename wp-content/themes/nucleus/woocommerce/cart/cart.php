<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
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
 * @version 2.3.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" class="shopping-cart">
	<table class="shop_table cart" style="display: none;"></table>
	<div class="row">
		<div class="col-lg-9 col-md-8">

			<?php do_action( 'woocommerce_before_cart_table' ); ?>

			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product
				     && $_product->exists()
				     && $cart_item['quantity'] > 0
				     && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key )
				) :
					$product_permalink = $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '';
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $product_permalink, $cart_item, $cart_item_key );

					$product_class = apply_filters( 'woocommerce_cart_item_class', array(
						'shop-tile',
						'cart_item',
					), $cart_item, $cart_item_key );
					?>
					<div class="<?php echo esc_attr( nucleus_get_class_set( $product_class ) ); ?>">

						<a href="<?php echo esc_url( $product_permalink ); ?>" class="thumbnail">
							<?php echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key ); ?>
						</a>

						<div class="description">
							<div class="shop-meta">
								<div class="column">
									<span class="hidden-md"><?php esc_html_e( 'in', 'nucleus' ); ?></span>
				                    <span>
				                      <i class="icon-ribbon hidden-md"></i>
				                      <?php echo $_product->get_categories( ', ' ); ?>
				                    </span>
								</div>
								<div class="column">
									<span class="subtotal">
										<span><?php esc_html_e( 'Subtotal:', 'nucleus' ); ?></span>
										<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
									</span>
								</div>
								<div class="column product-remove">
									<?php
									echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
										'<a href="%s" class="remove delete-item" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
										esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
										esc_html__( 'Remove this item', 'nucleus' ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									), $cart_item_key );
									?>
								</div>
							</div>

							<h3 class="shop-title">
								<a href="<?php echo esc_url( $product_permalink ); ?>">
									<?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ); ?>
								</a>
							</h3>

							<?php
							// Meta data
							echo WC()->cart->get_item_data( $cart_item );

							// Backorder notification
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								echo '<p class="backorder_notification">', esc_html__( 'Available on backorder', 'nucleus' ), '</p>';
							}
							?>

							<span class="price">
								<span><?php esc_html_e( 'Price:', 'nucleus' ); ?></span>
								<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
							</span>

							<?php
							if ( $_product->is_sold_individually() ) :
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							else :
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => '0'
								), $_product, false );
							endif;

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
							?>

						</div>
					</div>
					<?php
				endif;
			endforeach;
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>

			<?php do_action( 'woocommerce_after_cart_table' ); ?>

		</div>
		<div class="col-lg-3 col-md-4">
			<aside class="sidebar cart-collaterals">

				<?php do_action( 'woocommerce_cart_collaterals' ); ?>
				
			</aside>
		</div>
	</div>

</form>

<?php do_action( 'woocommerce_after_cart' ); ?>
