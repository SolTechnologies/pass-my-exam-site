<?php
/**
 * The Template for displaying filters in Shop
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/shop-filters.php
 *
 * @see         https://docs.woothemes.com/document/template-structure/
 * @author      8guild
 * @package     Nucleus\WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="shop-filters-wrap">
	<div class="shop-filters">
		<div class="inner">
			<div class="shop-filter">
				<div class="shop-filter-dropdown">
					<span><?php esc_html_e( 'Categories', 'nucleus' ); ?></span>
					<div class="dropdown">
						<?php nucleus_wc_shop_categories_filter(); ?>
					</div>
				</div>
			</div>
			<div class="shop-filter">
				<div class="shop-filter-dropdown">
					<span><?php esc_html_e( 'Price', 'nucleus' ); ?></span>
					<div class="dropdown">
						<?php nucleus_wc_shop_price_filter(); ?>
					</div>
				</div>
			</div>
			<div class="shop-filter">
				<div class="shop-filter-dropdown">
					<span>Sorting</span>
					<div class="dropdown">
						<?php nucleus_wc_shop_sorting_filter(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="shop-search">
		<div class="widget widget_search">
			<?php get_product_search_form(); ?>
		</div>
	</div>
</div>
