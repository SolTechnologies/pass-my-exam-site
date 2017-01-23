<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * CPT "Portfolio"
 * 
 * @author 8guild
 */
class Nucleus_CPT_Portfolio extends Nucleus_CPT {
	/**
	 * Custom Post Type
	 *
	 * @var string
	 */
	protected $post_type = 'nucleus_portfolio';

	/**
	 * Custom taxonomy
	 *
	 * @var string
	 */
	private $taxonomy = 'nucleus_portfolio_category';

	/**#@+
	 * Cache variables
	 *
	 * @see flush_cats_cache
	 * @see flush_posts_cache
	 */
	private $cache_key_for_posts = 'nucleus_portfolio_posts';
	private $cache_key_for_cats = 'nucleus_portfolio_cats';
	private $cache_group = 'nucleus_vc';
	/**#@-*/

	/**
	 * Constructor
	 */
	public function __construct() {}

	public function init() {
		add_action( 'init', array( $this, 'register' ), 0 );

		// Clear cache on adding or deleting portfolio items
		add_action( "save_post_{$this->post_type}", array( $this, 'flush_posts_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_posts_cache' ) );
		
		// and categories
		add_action( "create_{$this->taxonomy}", array( $this, 'flush_cats_cache' ) );
		add_action( "delete_{$this->taxonomy}", array( $this, 'flush_cats_cache' ) );
		// fires for both situations when term is edited and term post count changes
		// @see taxonomy.php :: 3440 wp_update_term()
		// @see taxonomy.php :: 4152 _update_post_term_count
		add_action( 'edit_term_taxonomy', array( $this, 'flush_cats_cache' ), 10, 2 );

		// meta boxes
		// @see nucleus/inc/meta-boxes.php

		// Display Featured Image in entries list
		add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'additional_posts_screen_columns' ) );
		add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'additional_posts_screen_content' ), 10, 2 );

		// AJAX Load More
		if ( is_admin() ) {
			add_action( 'wp_ajax_nucleus_portfolio_more', array( $this, 'load_more' ) );
			add_action( 'wp_ajax_nopriv_nucleus_portfolio_more', array( $this, 'load_more' ) );
		}
	}

	public function register() {
		$this->register_post_type();
		$this->register_taxonomy();
	}

	private function register_post_type() {
		$labels = array(
			'name'               => _x( 'Portfolio', 'post type general name', 'nucleus' ),
			'singular_name'      => _x( 'Portfolio', 'post type singular name', 'nucleus' ),
			'menu_name'          => __( 'Portfolio', 'nucleus' ),
			'all_items'          => __( 'All Items', 'nucleus' ),
			'view_item'          => __( 'View', 'nucleus' ),
			'add_new_item'       => __( 'Add New Item', 'nucleus' ),
			'add_new'            => __( 'Add New', 'nucleus' ),
			'edit_item'          => __( 'Edit', 'nucleus' ),
			'update_item'        => __( 'Update', 'nucleus' ),
			'search_items'       => __( 'Search', 'nucleus' ),
			'not_found'          => __( 'Not found', 'nucleus' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'nucleus' )
		);

		$rewrite = array(
			'slug'       => 'portfolio-item',
			'with_front' => false,
			'pages'      => true,
			'feeds'      => true,
		);

		$args = array(
			'label'               => __( 'Portfolio', 'nucleus' ),
			'labels'              => $labels,
			'description'         => __( 'A fancy portfolio with filters.', 'nucleus' ),
			'public'              => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => '48.1',
			'menu_icon'           => 'dashicons-portfolio',
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'thumbnail', 'editor' ),
			'taxonomies'          => array( $this->taxonomy ),
			'has_archive'         => false,
			'rewrite'             => $rewrite,
			'query_var'           => true,
			'can_export'          => true,
		);

		register_post_type( $this->post_type, $args );
	}

	private function register_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Categories', 'taxonomy general name', 'nucleus' ),
			'singular_name'              => _x( 'Category', 'taxonomy singular name', 'nucleus' ),
			'menu_name'                  => __( 'Categories', 'nucleus' ),
			'all_items'                  => __( 'All Items', 'nucleus' ),
			'parent_item'                => __( 'Parent Item', 'nucleus' ),
			'parent_item_colon'          => __( 'Parent Item:', 'nucleus' ),
			'new_item_name'              => __( 'New Item Name', 'nucleus' ),
			'add_new_item'               => __( 'Add New', 'nucleus' ),
			'edit_item'                  => __( 'Edit', 'nucleus' ),
			'update_item'                => __( 'Update', 'nucleus' ),
			'separate_items_with_commas' => __( 'Separate with commas', 'nucleus' ),
			'search_items'               => __( 'Search', 'nucleus' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'nucleus' ),
			'choose_from_most_used'      => __( 'Choose from the most used items', 'nucleus' ),
			'not_found'                  => __( 'Not Found', 'nucleus' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'For filtration and building queries', 'nucleus' ),
			'public'             => false,
			'show_ui'            => true,
			'show_in_nav_menus'  => false,
			'show_tagcloud'      => false,
			'show_in_quick_edit' => true,
			'show_admin_column'  => true,
			'hierarchical'       => true,
			'query_var'          => false,
			'rewrite'            => false,
		);

		register_taxonomy( $this->taxonomy, array( $this->post_type ), $args );
	}

	/**
	 * Flush object cache for posts
	 *
	 * Fires when portfolio posts creating, updating or deleting.
	 *
	 * @see inc/vc-map.php
	 * @see nucleus_core_portfolio_posts
	 *
	 * @param int $post_id Post ID
	 */
	public function flush_posts_cache( $post_id ) {
		$type = get_post_type( $post_id );
		if ( $this->post_type !== $type ) {
			return;
		}

		wp_cache_delete( $this->cache_key_for_posts, $this->cache_group );
	}

	/**
	 * Flush object cache for categories
	 *
	 * Fires when created, edited, deleted or updated a category
	 *
	 * @see inc/vc-map.php
	 * @see nucleus_core_portfolio_categories
	 *
	 * @param int    $term_id  Term ID or Term Taxonomy ID
	 * @param string $taxonomy Taxonomy name, exists only for "edit_term_taxonomy"
	 */
	public function flush_cats_cache( $term_id, $taxonomy = null ) {
		if ( null === $taxonomy || $this->taxonomy === $taxonomy ) {
			wp_cache_delete( $this->cache_key_for_cats, $this->cache_group );
		}
	}

	/**
	 * AJAX handler for portfolio "Load More" button
	 *
	 * Outputs HTML
	 */
	public function load_more() {
		// Check nonce.
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
			while( $query->have_posts() ) {
				$query->the_post();
				ob_start();
				get_template_part( 'template-parts/portfolio', esc_attr( $_POST['type'] ) );
				$posts[] = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', ob_get_clean() );
			}
		}
		wp_reset_postdata();

		if ( count( $posts ) > 0 ) {
			wp_send_json_success( $posts );
		} else {
			wp_send_json_error( 'Posts not found' );
		}
	}

	/**
	 * Add extra columns to a post type screen
	 *
	 * @param array $columns Current Posts Screen columns
	 *
	 * @return array New Posts Screen columns.
	 */
	public function additional_posts_screen_columns( $columns ) {
		return array_merge( array(
			'cb'     => '<input type="checkbox" />',
			'image'  => __( 'Cover', 'nucleus' ),
			'title'  => __( 'Title', 'nucleus' ),
		), $columns );
	}

	/**
	 * Show data in extra columns
	 *
	 * @param string $column  Column slug
	 * @param int    $post_id Post ID
	 */
	public function additional_posts_screen_content( $column, $post_id ) {
		switch ( $column ) {
			case 'image':
				$cover_id = get_post_thumbnail_id( $post_id );
				echo wp_get_attachment_image( $cover_id, array( 75, 75 ) );
				break;
		}
	}
}