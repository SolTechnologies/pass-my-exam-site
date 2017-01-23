<?php
/**
 * Posts | nucleus_posts
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_posts_atts', array(
	'source'               => 'posts', // posts | ids
	'query_post__in'       => '',
	'query_taxonomies'     => '',
	'query_post__not_in'   => '',
	'query_posts_per_page' => 'all',
	'query_orderby'        => 'date',
	'query_order'          => 'DESC',
	'is_animation'         => 'disable',
	'animation_type'       => 'top',
	'animation_delay'      => 0,
	'animation_easing'     => 'none',
	'class'                => '',
) ), $atts );

$source    = sanitize_key( $a['source'] );
$is_by_ids = ( 'ids' === $source );
$is_more   = ( 'yes' === $a['is_more'] );
$is_all    = ( 'all' === strtolower( $a['query_posts_per_page'] ) );
$animation = nucleus_parse_array( $a, 'animation_' );

$query_default = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
);

$query_args = nucleus_parse_array( $a, 'query_' );
$query_args = array_merge( $query_default, $query_args );
$query_args = nucleus_query_build( $query_args, function( $query ) use ( $is_by_ids ) {

	// "post__not_in" allowed only for "posts" source type
	// Exclude it if exists to correctly handle "by IDs" option
	if ( $is_by_ids && array_key_exists( 'post__not_in', $query ) ) {
		unset( $query['post__not_in'] );
	}

	// Otherwise, "post__in" allowed only for "IDs" source type
	// Exclude it if exists
	if ( ! $is_by_ids && array_key_exists( 'post__in', $query ) ) {
		unset( $query['post__in'] );
	}

	// If user specify a list of IDs, fetch all posts without pagination
	if ( $is_by_ids && array_key_exists( 'posts_per_page', $query ) ) {
		$query['posts_per_page'] = - 1;
	}

	// "taxonomies" allowed only for "posts" source type
	if ( $is_by_ids && array_key_exists( 'taxonomies', $query ) ) {
		unset( $query['taxonomies'] );
	}

	// Build the tax_query based on the list of term slugs
	if ( ! $is_by_ids && array_key_exists( 'taxonomies', $query ) ) {
		$terms = $query['taxonomies'];
		unset( $query['taxonomies'] );

		$taxonomies = get_taxonomies( array(
			'public'      => true,
			'object_type' => array( 'post' ),
		), 'objects' );

		// Exclude post_formats
		if ( array_key_exists( 'post_format', $taxonomies ) ) {
			unset( $taxonomies['post_format'] );
		}

		// Get only taxonomies slugs
		$taxonomies         = array_keys( $taxonomies );
		$query['tax_query'] = nucleus_query_multiple_tax( $terms, $taxonomies );

		// relations for multiple tax_queries
		if ( count( $query['tax_query'] ) > 1 ) {
			$query['tax_query']['relations'] = 'AND';
		}
	}

	return $query;
} );

$query = new WP_Query( $query_args );
if ( $query->have_posts() ) :

	$grid_class = nucleus_get_class_set( array(
		'nucleus-posts',
		nucleus_get_animation_class( $a['is_animation'], $animation ),
		$a['class'],
	) );

	$grid_attr = array(
		'id'    => esc_attr( $grid_id ),
		'class' => esc_attr( $grid_class ),
	);

	echo '<div ', nucleus_get_html_attr( $grid_attr ), '>';
	
	while ( $query->have_posts() ) :
		$query->the_post();
		get_template_part( 'template-parts/tile' );
	endwhile;
	
	echo '</div>';

endif; // have_posts
wp_reset_postdata();
