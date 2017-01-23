<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * woocommerce_before_single_product hook.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}

?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>"
     id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

	<div class="row">
		<div class="col-lg-5 col-md-6 space-bottom-2x">
			<?php
			/**
			 * woocommerce_before_single_product_summary hook.
			 *
			 * @see nucleus_wc_before_single_product_summary()
			 */
			do_action( 'woocommerce_before_single_product_summary' );
			?>
		</div>
		<div class="col-lg-7 col-md-6 space-bottom-2x">
			<?php
			/**
			 * woocommerce_single_product_summary hook.
			 * 
			 * @see nucleus_wc_single_product_category() 3
			 * @see woocommerce_template_single_title() 5
			 * @see woocommerce_template_single_excerpt() 10
			 * @see woocommerce_template_single_add_to_cart() 30
			 * @see woocommerce_template_single_meta() 40
			 */
			do_action( 'woocommerce_single_product_summary' );
			?>
		</div>
	</div>

	<div class="row padding-top">
		<div class="col-sm-6 space-bottom">
			<?php
			/**
			 * woocommerce_after_single_product_summary hook.
			 *
			 * @see woocommerce_output_product_data_tabs() - 10
			 */
			do_action( 'woocommerce_after_single_product_summary' );
			?>
		</div>
		<div class="col-sm-6 padding-top-2x">
			<?php the_content(); ?>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
