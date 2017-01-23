<?php
/**
 * Products | nucleus_products
 *
 * @var array $atts    Shortcode attributes
 * @var mixed $content Shortcode content
 *
 * @author 8guild
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Filter the default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_products_atts', array(
	'source'               => 'categories', // categories | ids
	'query_post__in'       => '', // list of IDs
	'query_categories'     => '', // list of term slugs
	'query_post__not_in'   => '',
	'query_posts_per_page' => 'all',
	'query_orderby'        => 'date',
	'query_order'          => 'DESC', // DESC | ASC
	'is_animation'         => 'disable',
	'animation_type'       => 'top',
	'animation_delay'      => 0,
	'animation_easing'     => 'none',
	'class'                => '',
) ), $atts );

$post_type = 'product';
$tax       = 'product_cat';

$source       = sanitize_key( $a['source'] );
$is_all       = ( 'all' === strtolower( $a['query_posts_per_page'] ) );
$is_animation = ( 'enable' === $a['is_animation'] );
$animation    = nucleus_parse_array( $a, 'animation_' );

$query_default = array(
	'post_type'   => $post_type,
	'post_status' => 'publish',
);

$query_args = nucleus_parse_array( $a, 'query_' );
$query_args = array_merge( $query_default, $query_args );
$query_args = nucleus_query_build( $query_args, function( $query ) use ( $tax, $source ) {
	// build a tax_query if getting by categories
	// @see WP_Query
	if ( array_key_exists( 'categories', $query ) ) {
		$categories = $query['categories'];
		unset( $query['categories'] );

		$query['tax_query'] = nucleus_query_single_tax( $categories, $tax );
	}

	// always fetch all posts if getting by IDs
	if ( 'ids' === $source ) {
		$query['posts_per_page'] = -1;
	}

	return $query;
} );

$query = new WP_Query( $query_args );

if ( $query->have_posts() ) :

	// if animation enabled
	if ( $is_animation ) {
		echo '<div class="', nucleus_get_animation_class( $is_animation, $animation ), '">';
	}


	echo '<div class="row">';

	$i = 1;
	woocommerce_product_loop_start();
	while ( $query->have_posts() ) :
		$query->the_post();
		echo '<div class="col-md-6">';
		wc_get_template_part( 'content', 'product' );
		echo '</div>';

		if ( $i % 2 == 0 && $i !== $query->post_count ) {
			echo '</div><div class="row">';
		}

		$i++;
	endwhile; // end of the loop.
	woocommerce_product_loop_end();

	echo '</div>'; // close div.row

	if ( $is_animation ) {
		echo '</div>'; // close div.scrollReveal
	}

endif; // have_posts
wp_reset_postdata();
