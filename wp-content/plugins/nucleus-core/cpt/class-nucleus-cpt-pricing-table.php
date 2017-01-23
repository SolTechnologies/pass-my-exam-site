<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * CPT "Pricing Table"
 *
 * @author 8guild
 */
class Nucleus_CPT_Pricing_Table extends Nucleus_CPT {

	/**
	 * Custom Post Type
	 *
	 * @var string
	 */
	protected $post_type = 'nucleus_pricing';

	/**
	 * Taxonomy: Type
	 *
	 * @var string
	 */
	protected $tax_type = 'nucleus_pricing_type';

	/**
	 * Taxonomy: Properties
	 *
	 * @var string
	 */
	protected $tax_properties = 'nucleus_pricing_properties';

	/**
	 * Meta box: Plan Properties
	 *
	 * @var string
	 */
	protected $mb_properties = '_nucleus_plan_properties';

	/**
	 * Meta box: Plan Settings
	 *
	 * @var string
	 */
	protected $mb_settings = '_nucleus_plan_settings';

	private $nonce = 'nucleus_pricing_props';
	private $nonce_field = 'nucleus_pricing_props_nonce';

	/**
	 * Constructor
	 */
	public function __construct() {}

	public function init() {
		add_action( 'init', array( $this, 'register' ), 0 );

		// meta boxes
		add_action( 'equip/register', array( $this, 'add_meta_boxes') );
		add_action( "add_meta_boxes_{$this->post_type}", array( $this, 'custom_meta_boxes' ) );
		add_action( "save_post_{$this->post_type}", array( $this, 'save_meta_boxes' ), 10, 2 );
	}

	public function register() {
		$this->register_post_type();
		$this->register_tax_type();
		$this->register_tax_properties();
	}

	private function register_post_type() {
		$labels = array(
			'name'               => _x( 'Pricing Table', 'post type general name', 'nucleus' ),
			'singular_name'      => _x( 'Pricing Table', 'post type singular name', 'nucleus' ),
			'menu_name'          => __( 'Pricing Table', 'nucleus' ),
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

		$args = array(
			'label'               => __( 'Portfolio', 'nucleus' ),
			'labels'              => $labels,
			'description'         => __( 'A fancy portfolio with filters.', 'nucleus' ),
			'public'              => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => '48.1',
			'menu_icon'           => 'dashicons-media-spreadsheet',
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'excerpt' ),
			'taxonomies'          => array( $this->tax_type, $this->tax_properties ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'can_export'          => true,
		);

		register_post_type( $this->post_type, $args );
	}

	private function register_tax_type() {
		$labels = array(
			'name'                       => _x( 'Types', 'taxonomy general name', 'nucleus' ),
			'singular_name'              => _x( 'Type', 'taxonomy singular name', 'nucleus' ),
			'menu_name'                  => __( 'Types', 'nucleus' ),
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
			'description'        => __( 'Taxonomy used in pricing table switcher', 'nucleus' ),
			'public'             => false,
			'show_ui'            => true,
			'show_in_nav_menus'  => false,
			'show_tagcloud'      => false,
			'show_in_quick_edit' => true,
			'show_admin_column'  => false,
			'meta_box_cb'        => false, // do not show meta box
			'hierarchical'       => true,
			'query_var'          => false,
			'rewrite'            => false,
		);

		register_taxonomy( $this->tax_type, array( $this->post_type ), $args );
	}

	private function register_tax_properties() {
		$labels = array(
			'name'                       => _x( 'Properties', 'taxonomy general name', 'nucleus' ),
			'singular_name'              => _x( 'Property', 'taxonomy singular name', 'nucleus' ),
			'menu_name'                  => __( 'Properties', 'nucleus' ),
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
			'description'        => __( 'Taxonomy used in pricing table for plan properties', 'nucleus' ),
			'public'             => false,
			'show_ui'            => true,
			'show_in_nav_menus'  => false,
			'show_tagcloud'      => false,
			'show_in_quick_edit' => true,
			'show_admin_column'  => false,
			'meta_box_cb'        => false, // do not show meta box
			'hierarchical'       => true,
			'query_var'          => false,
			'rewrite'            => false,
		);

		register_taxonomy( $this->tax_properties, array( $this->post_type ), $args );
	}

	/**
	 * Add meta boxes through Equip
	 */
	public function add_meta_boxes() {
		try {
			$layout = equip_create_meta_box_layout();
			$layout
				->add_field( 'url', 'text', array(
					'label'       => __( 'Button URL', 'nucleus' ),
					'description' => __( 'Leave this field empty if you do not want button', 'nucleus' ),
					'sanitize'    => 'esc_url_raw',
					'escape'      => 'esc_url',
				) )
				->add_field( 'text', 'text', array(
					'label'       => __( 'Button Label', 'nucleus' ),
					'description' => __( 'This text will be displayed on the "Buy now" button', 'nucleus' ),
				) )
				->add_field( 'icon', 'media', array(
					'label' => __( 'Plan Icon', 'nucleus' ),
					'media' => array( 'title' => __( 'Choose the icon', 'nucleus' ) ),
				) )
				->add_field( 'is_featured', 'switch', array(
					'label'       => __( 'Featured', 'nucleus' ),
					'description' => __( 'Make this plan featured. The featured plan will be stand out among other.', 'nucleus' ),
					'label_on'    => __( 'On', 'nucleus' ),
					'label_off'   => __( 'Off', 'nucleus' ),
				) );


			equip_add_meta_box( $this->mb_settings, $layout, array(
				'id'       => 'nucleus-pricing-settings',
				'title'    => __( 'Plan Settings', 'nucleus' ),
				'screen'   => $this->post_type,
				'context'  => 'normal',
				'priority' => 'low',
			) );

		} catch ( Exception $e ) {
			trigger_error( $e->getMessage() );
		}
	}

	/**
	 * Add custom meta box "Plan Properties"
	 *
	 * I can not use Equip because of the complexity
	 *
	 * @param WP_Post $post
	 */
	public function custom_meta_boxes( $post ) {
		add_meta_box(
			'nucleus-plan-properties',
			__( 'Plan Properties', 'nucleus' ),
			array( $this, 'do_properties_meta_box' ),
			$this->post_type,
			'normal',
			'default'
		);
	}

	/**
	 * Show meta box: Plan Properties
	 *
	 * @param WP_Post $post Post object
	 */
	public function do_properties_meta_box( $post ) {
		wp_nonce_field( $this->nonce, $this->nonce_field );

		$values = get_post_meta( $post->ID, $this->mb_properties, true );
		if ( empty( $values ) ) {
			$values = array();
		}

		$types = get_terms( array(
			'taxonomy'     => $this->tax_type,
			'hide_empty'   => false,
			'hierarchical' => false,
			'number'       => 2,
			'orderby'      => 'term_id',
			'order'        => 'ASC',
		) );

		if ( empty( $types ) || is_wp_error( $types ) || count( $types ) < 2 ) {
			_e( 'Please add at least two types in Pricing Table > Types', 'nucleus' );
			return;
		}

		$properties = get_terms( array(
			'taxonomy'     => $this->tax_properties,
			'hide_empty'   => false,
			'hierarchical' => false,
			'orderby'      => 'term_id',
			'order'        => 'ASC',
		) );

		if ( empty( $properties) || is_wp_error( $properties ) ) {
			_e( 'You should add some properties in Pricing Table > Properties', 'nucleus' );
			return;
		}
		?>
		<style media="screen">
			#nucleus-plan-properties table,
			#nucleus-plan-properties table td input {
				width: 100%;
			}
			#nucleus-plan-properties table td input {
				height: 36px;
			}
			#nucleus-plan-properties table tr th {
				text-align: right;
				padding-right: 15px;
			}
			#nucleus-plan-properties table tr:first-child th {
				text-align: center;
				padding-right: 0;
				padding-bottom: 10px;
			}

		</style>
		<?php
		$table = new Nucleus_Table();
		$table->set( 'slug', $this->mb_properties );
		$table->set( 'properties', $properties );
		$table->set( 'types', $types );
		$table->set( 'values', $values );
		$table->render();


		echo '<br>';
		echo '<p class="description">', __( 'If you do not want switch some of the properties just fill in only left column, or use the same values', 'nucleus' ), '</p>';
		echo '<p class="description">', __( 'Also you can use some special keywords, like "infinity", "available", "not-available"', 'nucleus' ), '</p>';

	}

	/**
	 * Save post metadata when a post of {@see $this->post_type} is saved.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id The ID of the post.
	 * @param WP_Post $post    Post object
	 *
	 * @return void
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// No auto-drafts, please
		if ( isset( $post->post_status ) && 'auto-draft' === $post->post_status ) {
			return;
		}

		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		// check the nonce
		if ( ! array_key_exists( $this->nonce_field, $_POST ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST[ $this->nonce_field ], $this->nonce ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check the auto-save and revisions
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( array_key_exists( $this->mb_properties, $_POST ) ) {
			$value = array();
			array_walk( $_POST[ $this->mb_properties ], function( $p, $property ) use ( &$value ) {
				foreach ( $p as $type => $v ) {
					$value[ $property ][ $type ] = esc_attr( $v );
				}
			} );

			update_post_meta( $post_id, $this->mb_properties, $value );
		}
	}

}
