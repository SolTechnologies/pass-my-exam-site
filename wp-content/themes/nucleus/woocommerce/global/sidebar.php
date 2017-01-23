<?php
/**
 * Sidebar
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/sidebar.php.
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
 * @version       1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$shop_layout = nucleus_get_option( 'layout_shop', 'no-sidebar' );
if ( 'no-sidebar' === $shop_layout ) {
	return;
}

if ( 'right-sidebar' === $shop_layout ) :
	// close shop div.col-* first
	?>
	</div>
	<div class="col-lg-3 col-md-4">
		<?php get_sidebar( 'shop' ); ?>
	</div>
	<?php
elseif ( 'left-sidebar' === $shop_layout ) :
	// close shop div.col-* first
	?>
	</div>
	<div class="col-lg-3 col-md-4 col-lg-pull-9 col-md-pull-8">
		<?php get_sidebar( 'shop' ); ?>
	</div>
	<?php
endif;
unset( $shop_layout );
