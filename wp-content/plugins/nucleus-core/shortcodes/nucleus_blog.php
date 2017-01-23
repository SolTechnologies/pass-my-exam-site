<?php
/**
 * Blog | nucleus_blog
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_blog_atts', array(
	'source'               => 'posts', // posts | ids
	'query_post__in'       => '',
	'query_taxonomies'     => '',
	'query_post__not_in'   => '',
	'query_posts_per_page' => 'all',
	'query_orderby'        => 'date',
	'query_order'          => 'DESC',
	'is_more'              => 'no',
	'more_pos'             => 'center',
	'more_text'            => __( 'Load More', 'nucleus' ),
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
$unique_id = nucleus_get_unique_id( 'blog-' );
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

	// unique ID for grid
	$grid_id = $unique_id . '-grid';
	$grid_class = nucleus_get_class_set( array(
		'grid',
		'isotope-grid',
		'col-2',
		'nucleus-isotope',
		nucleus_get_animation_class( $a['is_animation'], $animation ),
		$a['class'],
	) );

	$grid_attr = array(
		'id'    => esc_attr( $grid_id ),
		'class' => esc_attr( $grid_class ),
	);

	?>
	<div <?php echo nucleus_get_html_attr( $grid_attr ); ?>>
		<div class="gutter-sizer"></div>
		<div class="grid-sizer"></div>
		
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			get_template_part( 'template-parts/tile', 'isotope' );
		endwhile;
		?>
		
	</div>
	<?php

	/*
	 * Load More
	 *
	 * Show load more only when user perform a request by categories
	 * and limiting the number of posts. In case if user try to load
	 * "all" posts, or perform a request by posts - Load More button
	 * won't display.
	 */
	if ( $is_more
	     && false === $is_all
	     && 'posts' === $source
	     && (int) $query->found_posts > $query_args['posts_per_page']
	) {
		$more_pos   = 'text-' . $a['more_pos'];
		$more_class = nucleus_get_class_set( array(
			'btn',
			'btn-default',
			'btn-ghost',
			'waves-effect',
			'nucleus-blog-more',
		) );

		$more_attr = array(
			'href'           => '#',
			'class'          => esc_attr( $more_class ),
			'data-query'     => nucleus_query_encode( $query_args ),
			'data-page'      => 2,
			'data-max-pages' => (int) $query->max_num_pages,
			'data-grid-id'   => $grid_id,
		);

		$more_text = wp_kses( $a['more_text'], array(
			'i' => array( 'class' => true ),
		) );

		echo '<div class="padding-top ', esc_attr( $more_pos ), '">';
		echo '<img class="preloader hidden" src="data:image/gif;base64,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">';
		echo nucleus_get_tag( 'a', $more_attr, $more_text );
		echo '</div>';
		unset( $more_pos, $more_class, $more_attr, $more_text );
	}

endif; // have_posts
wp_reset_postdata();
