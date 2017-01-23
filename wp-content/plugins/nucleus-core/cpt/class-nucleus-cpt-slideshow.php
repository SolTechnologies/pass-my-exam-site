<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * CPT "Scroll Slideshow"
 *
 * @author 8guild
 */
class Nucleus_CPT_Slideshow extends Nucleus_CPT {
	/**
	 * Custom Post Type
	 *
	 * @var string
	 */
	protected $post_type = 'nucleus_slideshow';

	/**
	 * Meta box name
	 *
	 * @var string
	 */
	protected $meta_box = '_nucleus_slideshow_settings';

	/**
	 * Constructor
	 */
	public function __construct() {}

	public function init() {
		add_action( 'init', array( $this, 'register' ), 0 );
		add_action( "save_post_{$this->post_type}", array( $this, 'flush' ), 10, 2 );

		// add meta boxes
		add_action( 'equip/register', array( $this, 'add_meta_boxes' ) );

		// Display Featured Image in entries list
		add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'additional_posts_screen_columns' ) );
		add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'additional_posts_screen_content' ), 10, 2 );
	}

	public function register() {
		$labels = array(
			'name'               => _x( 'Scroll Slideshow', 'post type general name', 'nucleus' ),
			'singular_name'      => _x( 'Scroll Slideshow', 'post type singular name', 'nucleus' ),
			'menu_name'          => __( 'Scroll Slideshow', 'nucleus' ),
			'all_items'          => __( 'All Items', 'nucleus' ),
			'view_item'          => __( 'View', 'nucleus' ),
			'add_new_item'       => __( 'Add New Slideshow', 'nucleus' ),
			'add_new'            => __( 'Add New', 'nucleus' ),
			'edit_item'          => __( 'Edit', 'nucleus' ),
			'update_item'        => __( 'Update', 'nucleus' ),
			'search_items'       => __( 'Search', 'nucleus' ),
			'not_found'          => __( 'Not found', 'nucleus' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'nucleus' )
		);

		$rewrite = array(
			'slug'       => 'slideshow-item',
			'with_front' => false,
			'feeds'      => false,
			'pages'      => false,
		);

		$args = array(
			'label'               => __( 'Scroll Slideshow', 'nucleus' ),
			'labels'              => $labels,
			'description'         => __( 'A fullscreen scroll slideshow with effects', 'nucleus' ),
			'public'              => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => '48.2',
			'menu_icon'           => 'dashicons-slides',
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'rewrite'             => $rewrite,
			'query_var'           => true,
			'can_export'          => true,
		);

		register_post_type( $this->post_type, $args );
	}

	/**
	 * Flush cache for Scroll Slideshow CPT
	 *
	 * @see nucleus_the_slideshow()
	 * @see single-nucleus_slideshow.php
	 *
	 * @param int     $post_id Post ID
	 * @param WP_Post $post    Post object
	 */
	public function flush( $post_id, $post ) {
		// @see nucleus_the_slideshow
		$cache_key = nucleus_get_unique_key( $this->post_type, $post_id );
		delete_transient( $cache_key );
		unset( $cache_key );
	}

	public function add_meta_boxes() {
		try {
			$layout = equip_create_meta_box_layout();
			$layout
				->add_field( 'gallery', 'media', array(
					'label'       => __( 'Gallery', 'nucleus' ),
					'description' => sprintf( '<p class="description">%s</p>',
						__( 'You can choose multiple images by holding the CTRL key in media library. Also you can sort chosen images simply with drag and drop to change the order.', 'nucleus' )
					),
					'multiple'    => true,
					'sortable'    => true,
					'media'       => array( 'title' => __( 'Gallery', 'nucleus' ) ),
				) )
				->add_field( 'animation', 'select', array(
					'label'       => __( 'Animation', 'nucleus' ),
					'description' => __( 'Choose the animation effect for the gallery', 'nucleus' ),
					'default'     => 'none',
					'options'     => array(
						'none'      => __( 'None', 'nucleus' ),
						'scaleDown' => __( 'Scale', 'nucleus' ),
						'rotate'    => __( 'Rotate', 'nucleus' ),
						'gallery'   => __( 'Gallery', 'nucleus' ),
						'opacity'   => __( 'Opacity', 'nucleus' ),
						'parallax'  => __( 'Parallax', 'nucleus' ),
					),
				) );

			equip_add_meta_box( $this->meta_box, $layout, array(
				'id'       => 'nucleus-slideshow-settings',
				'title'    => __( 'Slideshow Settings', 'nucleus' ),
				'screen'   => $this->post_type,
				'context'  => 'normal',
				'priority' => 'core',
			) );
		} catch ( Exception $e ) {
			trigger_error( $e->getMessage() );
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
			'cb'        => '<input type="checkbox" />',
			'preview'   => __( 'Preview', 'nucleus' ),
			'title'     => __( 'Title', 'nucleus' ),
			'animation' => __( 'Animation', 'nucleus' ),
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
			case 'preview':
				$images = nucleus_get_meta( $post_id, $this->meta_box, 'gallery' );
				if ( empty( $images ) ) {
					break;
				}

				$images  = wp_parse_id_list( $images );
				$preview = array_shift( $images );

				// show the preview
				echo '<div class="nucleus-slideshow-preview">';
				echo wp_get_attachment_image( $preview, 'thumbnail' );

				// Show other images, if exists
				if ( count( $images ) > 0 ) {
					echo '<ul>';
					foreach ( $images as $image ) {
						printf( '<li>%s</li>',
							wp_get_attachment_image( $image, array( 45, 45 ) )
						);
					}
					echo '</ul>';
				}
				echo '</div>';

				break;

			case 'animation':
				echo nucleus_get_meta( $post_id, $this->meta_box, 'animation', 'none' );
				break;
		}
	}
}