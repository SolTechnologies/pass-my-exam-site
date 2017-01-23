<?php
/**
 * Loop Categories
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/categories.php.
 *
 * This is a custom template for Nucleus
 *
 * @see           https://docs.woothemes.com/document/template-structure/
 * @author        8guild
 * @package       Nucleus\WooCommerce
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;

$cat_count = count( get_the_terms( $post->ID, 'product_cat' ) );
if ( $cat_count > 0 ) :
	?>
	<div class="column">
		<span class="hidden-xs"><?php esc_html_e( 'in', 'nucleus' ); ?></span>
		<span>
			<?php echo '<i class="icon-ribbon hidden-xs"></i>', $product->get_categories( ', ' ); ?>
		</span>
	</div>
	<?php
endif;
unset( $cat_count );