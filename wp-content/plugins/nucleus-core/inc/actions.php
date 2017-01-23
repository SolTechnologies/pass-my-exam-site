<?php
/**
 * Custom actions
 *
 * @author 8guild
 */

/**
 * Flush object cache for Blog posts
 *
 * Fires when post creating, updating or deleting.
 *
 * @see inc/vc-map.php
 * @see nucleus_core_blog_terms
 *
 * @param int $post_id Post ID
 */
function nucleus_core_flush_posts_cache( $post_id ) {
	$type = get_post_type( $post_id );
	if ( 'post' !== $type ) {
		return;
	}

	wp_cache_delete( 'nucleus_blog_posts', 'nucleus_vc' );
}

// Clear cache on adding or deleting portfolio items
add_action( 'save_post_post', 'nucleus_core_flush_posts_cache' );
add_action( 'deleted_post', 'nucleus_core_flush_posts_cache' );

/**
 * AJAX loading handler for Blog
 *
 * @see shortcodes/nucleus_blog.php
 */
function nucleus_core_blog_more() {
	// check nonce
	if ( empty( $_POST['nonce'] )
	     || ! wp_verify_nonce( $_POST['nonce'], 'nucleus-core-ajax' )
	) {
		wp_send_json_error( __( 'Bad nonce', 'nucleus' ) );
	}

	if ( empty( $_POST['query'] ) ) {
		wp_send_json_error( __( 'Empty query param not allowed', 'nucleus' ) );
	}

	$query_args = nucleus_query_decode( $_POST['query'] );
	if ( null === $query_args ) {
		wp_send_json_error( __( 'Bad query param', 'nucleus' ) );
	}

	$query_args['paged'] = (int) $_POST['page'];

	$posts = array();
	$query = new WP_Query( $query_args );
	if ( $query->have_posts() ) {
		while( $query->have_posts() ) : $query->the_post();
			ob_start();
			get_template_part( 'template-parts/tile', 'isotope' );
			$posts[] = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', ob_get_clean() );
		endwhile;
	}
	wp_reset_postdata();

	if ( count( $posts ) > 0 ) {
		wp_send_json_success( $posts );
	} else {
		wp_send_json_error( 'Posts not found' );
	}
}

if ( is_admin() ) {
	add_action( 'wp_ajax_nucleus_blog_more', 'nucleus_core_blog_more' );
	add_action( 'wp_ajax_nopriv_nucleus_blog_more', 'nucleus_core_blog_more' );
}

/**
 * Clear products cache when user create or delete Product
 *
 * @see inc/vc-map.php
 * @see nucleus_core_products()
 *
 * @param int $post_id Post ID
 */
function nucleus_core_flush_products_cache( $post_id ) {
	$cache_key   = 'nucleus_products';
	$cache_group = 'nucleus_vc';

	$type = get_post_type( $post_id );
	if ( 'product' !== $type ) {
		return;
	}

	wp_cache_delete( $cache_key, $cache_group );
}

add_action( 'save_post_product', 'nucleus_core_flush_products_cache' );
add_action( 'deleted_post', 'nucleus_core_flush_products_cache' );

/**
 * Clear product categories cache when user create, delete,
 * update (edit) category or assign the category to a Product
 * 
 * @see inc/vc-map.php
 * @see nucleus_core_product_cats()
 * 
 * @see wp_update_term() taxonomy.php :: 3440
 * @see _update_post_term_count() taxonomy.php :: 4152
 *
 * @param int     $term_id Term ID
 * @param null|string $taxonomy Taxonomy
 */
function nucleus_core_flush_product_cats_cache( $term_id, $taxonomy = null ) {
	$cache_key   = 'nucleus_product_cats';
	$cache_group = 'nucleus_vc';

	if ( null === $taxonomy || 'product_cat' === $taxonomy ) {
		wp_cache_delete( $cache_key, $cache_group );
	}
}

add_action( 'create_product_cat', 'nucleus_core_flush_product_cats_cache' );
add_action( 'delete_product_cat', 'nucleus_core_flush_product_cats_cache' );
add_action( 'edit_term_taxonomy', 'nucleus_core_flush_product_cats_cache', 10, 2 );
