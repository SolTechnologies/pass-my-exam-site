<?php
/**
 * Mapping all custom shortcodes in Visual Composer interface
 *
 * @author 8guild
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! is_admin() ) {
	return;
}

/**
 * Returns the list of networks for nucleus_socials shortcode
 *
 * @uses nucleus_get_networks()
 *
 * @return array
 */
function nucleus_core_socials() {
	$networks = nucleus_get_networks();
	if ( empty( $networks ) ) {
		return array();
	}

	$_networks = array();
	foreach ( $networks as $network => $data ) {
		$name               = $data['name'];
		$_networks[ $name ] = $network;
		unset( $name );
	}
	unset( $network, $data );

	return $_networks;
}

/**
 * Fetch all portfolio posts for autocomplete field.
 *
 * It is safe to use IDs for import, because
 * WP Importer does not change IDs for posts.
 *
 * @return array
 */
function nucleus_core_portfolio_posts() {
	$cache_key   = 'nucleus_portfolio_posts';
	$cache_group = 'nucleus_vc';

	$posts = wp_cache_get( $cache_key, $cache_group );
	if ( false === $posts ) {
		$posts = array();
		$query = new WP_Query( array(
			'post_type'      => 'nucleus_portfolio',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'posts_per_page' => - 1,
		) );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) :
				$query->the_post();
				$posts[] = array(
					'value' => get_the_ID(),
					'label' => get_the_title(),
				);
			endwhile;

			// cache for 1 day
			wp_cache_set( $cache_key, $posts, $cache_group, 86400 );
		}
		wp_reset_postdata();
	}

	return $posts;
}

/**
 * Fetch the portfolio categories for autocomplete field
 *
 * The taxonomy slug used as autocomplete value because
 * of export/import issues. WP Importer creates new
 * categories, tags, taxonomies based on import information
 * with NEW IDs!
 *
 * @return array
 */
function nucleus_core_portfolio_categories() {
	$cache_key   = 'nucleus_portfolio_cats';
	$cache_group = 'nucleus_vc';

	$data = wp_cache_get( $cache_key, $cache_group );
	if ( false === $data ) {
		$categories = get_terms( array(
			'taxonomy'     => 'nucleus_portfolio_category',
			'hierarchical' => false,
		) );
		if ( is_wp_error( $categories ) || empty( $categories ) ) {
			return array();
		}

		$data = array();
		foreach ( $categories as $category ) {
			$data[] = array(
				'value' => $category->slug,
				'label' => $category->name,
			);
		}

		// cache for 1 day
		wp_cache_set( $cache_key, $data, $cache_group, 86400 );
	}

	return $data;
}

/**
 * Fetch all Blog posts for autocomplete field.
 *
 * It is safe to use IDs for import, because
 * WP Importer does not change IDs for posts.
 *
 * @return array
 */
function nucleus_core_blog_posts() {
	$cache_key   = 'nucleus_blog_posts';
	$cache_group = 'nucleus_vc';

	$posts = wp_cache_get( $cache_key, $cache_group );
	if ( false === $posts ) {
		$posts = array();
		$query = new WP_Query( array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => - 1,
			'no_found_rows'       => true,
		) );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) :
				$query->the_post();
				$posts[] = array(
					'value' => (int) get_the_ID(),
					'label' => esc_html( get_the_title() ),
				);
			endwhile;

			// cache for 1 day
			wp_cache_set( $cache_key, $posts, $cache_group, 86400 );
		}
		wp_reset_postdata();
	}

	return $posts;
}

/**
 * Returns taxonomies like tags, categories
 * and custom taxonomies assigned to "post" post type.
 *
 * @return array
 */
function nucleus_core_blog_taxonomies() {
	$taxonomies = get_taxonomies( array(
		'public'      => true,
		'object_type' => array( 'post' )
	), 'objects' );

	// Exclude post_formats
	if ( array_key_exists( 'post_format', $taxonomies ) ) {
		unset( $taxonomies['post_format'] );
	}

	return $taxonomies;
}

/**
 * Fetch all public taxonomies of blog posts for autocomplete field
 *
 * @return array
 */
function nucleus_core_blog_terms() {
	$taxonomies = nucleus_core_blog_taxonomies();
	$terms      = get_terms( array(
		'taxonomy'     => array_keys( $taxonomies ),
		'hierarchical' => false,
	) );

	if ( ! is_array( $terms ) || empty( $terms ) ) {
		return array();
	}

	$group_default = __( 'Taxonomies', 'nucleus' );

	$data = array();
	foreach ( (array) $terms as $term ) {
		if ( isset( $taxonomies[ $term->taxonomy ] )
		     && isset( $taxonomies[ $term->taxonomy ]->labels )
		     && isset( $taxonomies[ $term->taxonomy ]->labels->name )
		) {
			$group = $taxonomies[ $term->taxonomy ]->labels->name;
		} else {
			$group = $group_default;
		}

		$data[] = array(
			'label'    => $term->name,
			'value'    => $term->slug,
			'group_id' => $term->taxonomy,
			'group'    => $group,
		);
	}

	usort( $data, function( $i, $j ) {
		$a = strtolower( trim( $i['group'] ) );
		$b = strtolower( trim( $j['group'] ) );;

		if ( $a == $b ) {
			return 0;
		} elseif ( $a > $b ) {
			return 1;
		} else {
			return - 1;
		}
	} );

	return $data;
}

/**
 * Get the CF7 Form posts
 *
 * @return array
 */
function nucleus_core_cf7_posts() {
	$cf7 = get_posts( array(
		'post_type'      => 'wpcf7_contact_form',
		'posts_per_page' => - 1,
	) );

	$forms = array();

	$forms[ __( 'Choose the Contact Form', 'nucleus' ) ] = 0;
	if ( ! empty( $cf7 ) ) {
		foreach ( $cf7 as $item ) {
			$forms[ $item->post_title ] = $item->ID;
		}
	}

	return $forms;
}

/**
 * Fetch all Products for autocomplete field.
 *
 * It is safe to use IDs for import, because
 * WP Importer does not change IDs for posts.
 *
 * @return array
 */
function nucleus_core_products() {
	$cache_key   = 'nucleus_products';
	$cache_group = 'nucleus_vc';

	$posts = wp_cache_get( $cache_key, $cache_group );
	if ( false === $posts ) {
		$posts = array();
		$query = new WP_Query( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'posts_per_page' => -1,
		) );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$posts[] = array(
					'value' => get_the_ID(),
					'label' => get_the_title(),
				);
			}

			// cache for 1 day
			wp_cache_set( $cache_key, $posts, $cache_group, 86400 );
		}
		wp_reset_postdata();
	}

	return $posts;
}

/**
 * Fetch the product categories for autocomplete field
 *
 * The taxonomy slug used as autocomplete value because
 * of export/import issues. WP Importer creates new
 * categories, tags, taxonomies based on import information
 * with NEW IDs!
 *
 * @return array
 */
function nucleus_core_product_cats() {
	$cache_key   = 'nucleus_product_cats';
	$cache_group = 'nucleus_vc';

	$data = wp_cache_get( $cache_key, $cache_group );
	if ( false === $data ) {
		$categories = get_terms( array(
			'taxonomy'     => 'product_cat',
			'hierarchical' => false,
		) );

		if ( is_wp_error( $categories ) || empty( $categories ) ) {
			return array();
		}

		$data = array();
		foreach ( $categories as $category ) {
			$data[] = array(
				'value' => $category->slug,
				'label' => $category->name,
			);
		}

		// cache for 1 day
		wp_cache_set( $cache_key, $data, $cache_group, 86400 );
	}

	return $data;
}

/**
 * Integrate custom shortcodes into the VC
 *
 * @uses vc_map
 */
function nucleus_core_vc_init() {
	/**
	 * @var string Translated name for _g's shortcodes category
	 */
	$category = __( 'Nucleus', 'nucleus' );

	/**#@+
	 * Translated strings for "heading" parameters, which used more than once
	 */
	$heading_title              = __( 'Title', 'nucleus' );
	$heading_icon               = __( 'Icon', 'nucleus' );
	$heading_url                = __( 'URL', 'nucleus' );
	$heading_description        = __( 'Description', 'nucleus' );
	$heading_column_icon        = __( 'Column Icon', 'nucleus' );
	$heading_column_title       = __( 'Column Title', 'nucleus' );
	$heading_column_options     = __( 'Column Options', 'nucleus' );
	$heading_column_is_featured = __( 'Featured Column', 'nucleus' );
	/**#@-*/


	/**#@+
	 * Translated strings for "groups" parameters, which used more than once
	 */
	$group_button   = __( 'Button', 'nucleus' );
	$group_icon     = __( 'Icon', 'nucleus' );
	$group_filter   = __( 'Filter', 'nucleus' );
	$group_more     = __( 'Load More', 'nucleus' );
	$group_switcher = __( 'Switcher', 'nucleus' );
	$group_column1  = __( 'Column 1', 'nucleus' );
	$group_column2  = __( 'Column 2', 'nucleus' );
	$group_column3  = __( 'Column 3', 'nucleus' );
	$group_column4  = __( 'Column 4', 'nucleus' );
	$group_form     = __( 'Form', 'nucleus' );
	$group_contact  = __( 'Contact Details', 'nucleus' );
	/**#@-*/


	/**#@+
	 * Shortcode attribute values mostly for "dropdown" type which used more than once
	 */
	$value_no_yes = array(
		__( 'No', 'nucleus' )  => 'no',
		__( 'Yes', 'nucleus' ) => 'yes',
	);

	$value_colors = array(
		__( 'Default', 'nucleus' ) => 'default',
		__( 'Primary', 'nucleus' ) => 'primary',
		__( 'Success', 'nucleus' ) => 'success',
		__( 'Info', 'nucleus' )    => 'info',
		__( 'Warning', 'nucleus' ) => 'warning',
		__( 'Danger', 'nucleus' )  => 'danger',
	);

	$value_lcr = array(
		__( 'Left', 'nucleus' )   => 'left',
		__( 'Center', 'nucleus' ) => 'center',
		__( 'Right', 'nucleus' )  => 'right',
	);

	$value_colors_wgc = array_merge( $value_colors, array(
		__( 'White', 'nucleus' )  => 'white',
		__( 'Gray', 'nucleus' )   => 'gray',
		__( 'Custom', 'nucleus' ) => 'custom',
	) );

	$value_colors_light = array_merge( $value_colors, array(
		__( 'Light', 'nucleus' ) => 'light',
	) );

	$value_enable_disable = array(
		__( 'Enable', 'nucleus' )  => 'enable',
		__( 'Disable', 'nucleus' ) => 'disable',
	);

	$value_dark_light = array(
		__( 'Dark', 'nucleus' )  => 'dark',
		__( 'Light', 'nucleus' ) => 'light',
	);
	/**#@-*/


	/**#@+
	 * Shortcode attributes which used more than once
	 */

	$field_alignment = array(
		'param_name' => 'alignment',
		'type'       => 'dropdown',
		'weight'     => 10,
		'heading'    => __( 'Alignment', 'nucleus' ),
		'std'        => 'center',
		'value'      => $value_lcr,
	);

	$field_is_loop = array(
		'param_name' => 'is_loop',
		'type'       => 'dropdown',
		'weight'     => 10,
		'heading'    => __( 'Loop slider?', 'nucleus' ),
		'std'        => 'no',
		'value'      => $value_no_yes,
	);

	$field_is_autoplay = array(
		'param_name' => 'is_autoplay',
		'type'       => 'dropdown',
		'weight'     => 10,
		'heading'    => __( 'Enable autoplay?', 'nucleus' ),
		'std'        => 'no',
		'value'      => $value_no_yes,
	);

	$field_delay = array(
		'param_name' => 'delay',
		'type'       => 'textfield',
		'weight'     => 10,
		'heading'    => __( 'Autoplay delay, ms', 'nucleus' ),
		'dependency' => array( 'element' => 'is_autoplay', 'value' => 'yes' ),
		'value'      => '3000',
	);

	$field_icon_library = array(
		'param_name' => 'icon_library',
		'type'       => 'dropdown',
		'weight'     => 10,
		'group'      => $group_icon,
		'heading'    => __( 'Icon library', 'nucleus' ),
		'std'        => 'fontawesome',
		'value'      => array(
			__( 'Font Awesome', 'nucleus' ) => 'fontawesome',
			__( 'Open Iconic', 'nucleus' )  => 'openiconic',
			__( 'Typicons', 'nucleus' )     => 'typicons',
			__( 'Entypo', 'nucleus' )       => 'entypo',
			__( 'Linecons', 'nucleus' )     => 'linecons',
			__( 'Feather', 'nucleus' )      => 'feather',
			__( 'Flaticon', 'nucleus' )     => 'flaticon',
		),
	);

	$field_icon_fontawesome = array(
		'param_name' => 'icon_fontawesome',
		'type'       => 'iconpicker',
		'weight'     => 10,
		'group'      => $group_icon,
		'heading'    => $heading_icon,
		'settings'   => array( 'type' => 'fontawesome', 'iconsPerPage' => 4000 ),
		'dependency' => array( 'element' => 'icon_library', 'value' => 'fontawesome' ),
	);

	$field_icon_openiconic = array(
		'param_name' => 'icon_openiconic',
		'type'       => 'iconpicker',
		'weight'     => 10,
		'group'      => $group_icon,
		'heading'    => $heading_icon,
		'settings'   => array( 'type' => 'openiconic', 'iconsPerPage' => 4000 ),
		'dependency' => array( 'element' => 'icon_library', 'value' => 'openiconic' ),
	);

	$field_icon_typicons = array(
		'param_name' => 'icon_typicons',
		'type'       => 'iconpicker',
		'weight'     => 10,
		'group'      => $group_icon,
		'heading'    => $heading_icon,
		'settings'   => array( 'type' => 'typicons', 'iconsPerPage' => 4000 ),
		'dependency' => array( 'element' => 'icon_library', 'value' => 'typicons' ),
	);

	$field_icon_entypo = array(
		'param_name' => 'icon_entypo',
		'type'       => 'iconpicker',
		'weight'     => 10,
		'group'      => $group_icon,
		'heading'    => $heading_icon,
		'settings'   => array( 'type' => 'entypo', 'iconsPerPage' => 4000 ),
		'dependency' => array( 'element' => 'icon_library', 'value' => 'entypo' ),
	);

	$field_icon_linecons = array(
		'param_name' => 'icon_linecons',
		'type'       => 'iconpicker',
		'weight'     => 10,
		'group'      => $group_icon,
		'heading'    => $heading_icon,
		'settings'   => array( 'type' => 'linecons', 'iconsPerPage' => 4000 ),
		'dependency' => array( 'element' => 'icon_library', 'value' => 'linecons' ),
	);

	$field_icon_flaticon = array(
		'param_name' => 'icon_flaticon',
		'type'       => 'iconpicker',
		'weight'     => 10,
		'group'      => $group_icon,
		'heading'    => $heading_icon,
		'settings'   => array( 'type' => 'flaticon', 'iconsPerPage' => 4000 ),
		'dependency' => array( 'element' => 'icon_library', 'value' => 'flaticon' ),
	);

	$field_icon_feather = array(
		'param_name' => 'icon_feather',
		'type'       => 'iconpicker',
		'weight'     => 10,
		'group'      => $group_icon,
		'heading'    => $heading_icon,
		'settings'   => array( 'type' => 'feather', 'iconsPerPage' => 4000 ),
		'dependency' => array( 'element' => 'icon_library', 'value' => 'feather' ),
	);

	$field_icon_position = array(
		'param_name' => 'icon_position',
		'type'       => 'dropdown',
		'weight'     => 10,
		'heading'    => __( 'Position', 'nucleus' ),
		'group'      => $group_icon,
		'std'        => 'left',
		'value'      => array_diff( $value_lcr, array( 'center' ) ),
		'dependency' => array( 'element' => 'is_icon', 'value' => 'yes' )
	);

	$field_skin = array(
		'param_name' => 'skin',
		'type'       => 'dropdown',
		'weight'     => 10,
		'heading'    => __( 'Choose color skin', 'nucleus' ),
		'std'        => 'dark',
		'value'      => array(
			__( 'Dark', 'nucleus' )  => 'dark',
			__( 'Light', 'nucleus' ) => 'light',
		),
	);

	$field_query_posts_per_page = array(
		'param_name'  => 'query_posts_per_page',
		'type'        => 'textfield',
		'weight'      => 10,
		'heading'     => __( 'Number of posts', 'nucleus' ),
		'description' => __( 'Any number or "all" for displaying all posts.', 'nucleus' ),
		'value'       => 'all',
	);

	$field_query_order_by = array(
		'param_name'       => 'query_orderby',
		'type'             => 'dropdown',
		'weight'           => 10,
		'heading'          => __( 'Order by', 'nucleus' ),
		'edit_field_class' => 'vc_column vc_col-sm-6',
		'std'              => 'date',
		'value'            => array(
			__( 'Post ID', 'nucleus' )            => 'ID',
			__( 'Author', 'nucleus' )             => 'author',
			__( 'Post name (slug)', 'nucleus' )   => 'name',
			__( 'Date', 'nucleus' )               => 'date',
			__( 'Last Modified Date', 'nucleus' ) => 'modified',
			__( 'Number of comments', 'nucleus' ) => 'comment_count',
			__( 'Random', 'nucleus' )             => 'rand',
		),
	);

	$field_query_order = array(
		'param_name'       => 'query_order',
		'type'             => 'dropdown',
		'weight'           => 10,
		'heading'          => __( 'Sorting', 'nucleus' ),
		'edit_field_class' => 'vc_column vc_col-sm-6',
		'std'              => 'DESC',
		'value'            => array(
			__( 'Descending', 'nucleus' ) => 'DESC',
			__( 'Ascending', 'nucleus' )  => 'ASC',
		),
	);
	/**#@-*/

	/**
	 * Button | nucleus_button
	 */
	vc_map( array(
		'base'     => 'nucleus_button',
		'name'     => __( 'Button', 'nucleus' ),
		'category' => $category,
		'icon'     => 'icon-wpb-ui-button',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name'  => 'text',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Text', 'nucleus' ),
				'admin_label' => true,
			),
			array(
				'param_name' => 'link',
				'type'       => 'vc_link',
				'weight'     => 10,
			),
			array(
				'param_name'         => 'color',
				'type'               => 'dropdown',
				'weight'             => 10,
				'heading'            => __( 'Color', 'nucleus' ),
				'param_holder_class' => 'nucleus-colored',
				'edit_field_class'   => 'vc_column vc_col-sm-4',
				'std'                => 'default',
				'value'              => $value_colors_light,
			),
			array(
				'param_name'       => 'type',
				'type'             => 'dropdown',
				'weight'           => 10,
				'heading'          => __( 'Type', 'nucleus' ),
				'edit_field_class' => 'vc_column vc_col-sm-4',
				'value'            => array(
					__( 'Standard', 'nucleus' ) => 'standard',
					__( 'Ghost', 'nucleus' )    => 'ghost', // btn-ghost
					__( '3D', 'nucleus' )       => '3d', // btn-3d
				),
			),
			array(
				'param_name'       => 'size',
				'type'             => 'dropdown',
				'weight'           => 10,
				'heading'          => __( 'Size', 'nucleus' ),
				'edit_field_class' => 'vc_column vc_col-sm-4',
				'value'            => array(
					__( 'Normal', 'nucleus' ) => 'nl',
					__( 'Small', 'nucleus' )  => 'sm',
				),
			),
			array(
				'param_name'       => 'alignment',
				'type'             => 'dropdown',
				'weight'           => 10,
				'heading'          => __( 'Alignment', 'nucleus' ),
				'edit_field_class' => 'vc_column vc_col-sm-4',
				'std'              => 'inline',
				'value'            => array_merge( array( __( 'Inline', 'nucleus' ) => 'inline' ), $value_lcr ),
			),
			array(
				'param_name'       => 'is_full',
				'type'             => 'dropdown',
				'weight'           => 10,
				'heading'          => __( 'Make button full-width?', 'nucleus' ),
				'edit_field_class' => 'vc_column vc_col-sm-4',
				'std'              => 'no',
				'value'            => $value_no_yes,
			),
			array(
				'param_name'       => 'is_icon',
				'type'             => 'dropdown',
				'weight'           => 10,
				'heading'          => __( 'Use icon?', 'nucleus' ),
				'edit_field_class' => 'vc_column vc_col-sm-4',
				'std'              => 'no',
				'value'            => $value_no_yes,
			),
			array_merge( $field_icon_library, array(
				'dependency' => array( 'element' => 'is_icon', 'value' => 'yes' ),
			) ),
			$field_icon_fontawesome,
			$field_icon_openiconic,
			$field_icon_typicons,
			$field_icon_entypo,
			$field_icon_linecons,
			$field_icon_feather,
			$field_icon_flaticon,
			$field_icon_position,
			array(
				'param_name' => 'is_waves',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Waves', 'nucleus' ),
				'std'        => 'disable',
				'value'      => $value_enable_disable,
			),
			array(
				'param_name' => 'waves_skin',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Waves Skin', 'nucleus' ),
				'value'      => $value_dark_light,
				'dependency' => array( 'element' => 'is_waves', 'value' => 'enable' ),
			),
		) ),
	) );

	/**
	 * Block Title | nucleus_block_title
	 */
	vc_map( array(
		'base'     => 'nucleus_block_title',
		'name'     => __( 'Block Title', 'nucleus' ),
		'category' => $category,
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name'  => 'title',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => $heading_title,
				'admin_label' => true,
			),
			array(
				'param_name'  => 'subtitle',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Subtitle', 'nucleus' ),
				'admin_label' => true,
			),
			array(
				'param_name' => 'tag',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Select the heading tag', 'nucleus' ),
				'std'        => 'h2',
				'value'      => array(
					'H1' => 'h1',
					'H2' => 'h2',
					'H3' => 'h3',
					'H4' => 'h6',
					'H5' => 'h5',
					'H6' => 'h6',
				),
			),
			$field_alignment,
		) ),
	) );

	/**
	 * Socials | nucleus_socials
	 */
	vc_map( array(
		'base'     => 'nucleus_socials',
		'name'     => __( 'Socials', 'nucleus' ),
		'icon'     => '',
		'category' => $category,
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name'  => 'socials',
				'type'        => 'param_group',
				'heading'     => __( 'Socials', 'nucleus' ),
				'description' => __( 'Choose your social networks', 'nucleus' ),
				'value'       => urlencode( json_encode( array(
					array(
						'network' => 'twitter',
						'url'     => 'https://twitter.com/8guild',
					),
					array(
						'network' => 'facebook',
						'url'     => '#',
					),
				) ) ),
				'params'      => array(
					array(
						'param_name'  => 'network',
						'type'        => 'dropdown',
						'weight'      => 10,
						'heading'     => __( 'Network', 'nucleus' ),
						'description' => __( 'Choose the network from the given list.', 'nucleus' ),
						'value'       => call_user_func( 'nucleus_core_socials' ),
						'admin_label' => true,
					),
					array(
						'param_name'  => 'url',
						'type'        => 'textfield',
						'weight'      => 10,
						'heading'     => $heading_url,
						'description' => __( 'Enter the link to your social networks', 'nucleus' ),
						'admin_label' => true,
					),
				),
			),
			array(
				'param_name' => 'is_tooltip',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Tooltips', 'nucleus' ),
				'std'        => 'disable',
				'value'      => $value_enable_disable,
			),
		) ),
	) );

	/**
	 * App Store | nucleus_app_store
	 */
	vc_map( array(
		'base'     => 'nucleus_app_store',
		'name'     => __( 'App Store', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'link',
				'type'       => 'vc_link',
				'weight'     => 10,
			),
		) ),
	) );

	/**
	 * Google Play | nucleus_google_play
	 */
	vc_map( array(
		'base'     => 'nucleus_google_play',
		'name'     => __( 'Google Play', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'link',
				'type'       => 'vc_link',
				'weight'     => 10,
			),
		) ),
	) );

	/**
	 * Windows Store | nucleus_windows_store
	 */
	vc_map( array(
		'base'     => 'nucleus_windows_store',
		'name'     => __( 'Windows Store', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'link',
				'type'       => 'vc_link',
				'weight'     => 10,
			),
		) ),
	) );

	/**
	 * Amazon | nucleus_amazon
	 */
	vc_map( array(
		'base'     => 'nucleus_amazon',
		'name'     => __( 'Amazon', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'link',
				'type'       => 'vc_link',
				'weight'     => 10,
			),
		) ),
	) );

	/**
	 * Logo Carousel | nucleus_logo_carousel
	 */
	vc_map( array(
		'base'     => 'nucleus_logo_carousel',
		'name'     => __( 'Logo Carousel', 'nucleus' ),
		'icon'     => '',
		'category' => $category,
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name'  => 'carousel',
				'type'        => 'param_group',
				'heading'     => __( 'Carousel', 'nucleus' ),
				'description' => __( 'Add data for carousel', 'nucleus' ),
				'value'       => urlencode( json_encode( array(
					array(
						'logo' => 0,
						'url'  => 'http://8guild.com',
					),
				) ) ),
				'params'      => array(
					array(
						'param_name' => 'logo',
						'type'       => 'attach_image',
						'weight'     => 10,
						'heading'    => __( 'Logo', 'nucleus' ),
					),
					array(
						'param_name'  => 'url',
						'type'        => 'textfield',
						'weight'      => 10,
						'heading'     => $heading_url,
						'admin_label' => true,
					),
				),
			),
			array_merge( $field_is_loop, array( 'edit_field_class' => 'vc_column vc_col-sm-6' ) ),
			array_merge( $field_is_autoplay, array( 'edit_field_class' => 'vc_column vc_col-sm-6' ) ),
			$field_delay,
		) ),
	) );

	/**
	 * Image Carousel | nucleus_image_carousel
	 */
	vc_map( array(
		'base'     => 'nucleus_image_carousel',
		'name'     => __( 'Image Carousel', 'nucleus' ),
		'category' => $category,
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'images',
				'type'       => 'attach_images',
				'weight'     => 10,
				'heading'    => __( 'Images', 'nucleus' ),
			),
			array(
				'param_name'  => 'is_autoheight',
				'type'        => 'dropdown',
				'weight'      => 10,
				'heading'     => __( 'Enable auto height?', 'nucleus' ),
				'description' => __( 'This option especially good for cases when slides in the carousel have different height. It enables smooth height transition.', 'nucleus' ),
				'value'       => $value_no_yes,
			),
			array_merge( $field_is_loop, array( 'edit_field_class' => 'vc_column vc_col-sm-6' ) ),
			array_merge( $field_is_autoplay, array( 'edit_field_class' => 'vc_column vc_col-sm-6' ) ),
			$field_delay,
		) ),
	) );

	/**
	 * Quotation | nucleus_quotation
	 */
	vc_map( array(
		'name'     => __( 'Quotation', 'nucleus' ),
		'base'     => 'nucleus_quotation',
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'quotation',
				'type'       => 'textarea',
				'weight'     => 10,
				'heading'    => __( 'Quotation', 'nucleus' ),
			),
			array(
				'param_name' => 'author',
				'type'       => 'textfield',
				'weight'     => 10,
				'heading'    => __( 'Author', 'nucleus' ),
			),
			$field_skin,
			array(
				'param_name' => 'is_shareable',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Shareable with Twitter?', 'nucleus' ),
				'value'      => $value_no_yes,
			),
		) ),
	) );

	/**
	 * Portfolio | nucleus_portfolio
	 */
	vc_map( array(
		'name'     => __( 'Portfolio', 'nucleus' ),
		'base'     => 'nucleus_portfolio',
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'type',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Type', 'nucleus' ),
				'value'      => array(
					__( 'Standard', 'nucleus' ) => 'standard',
					__( 'With gap', 'nucleus' ) => 'gap',
					__( 'No gap', 'nucleus' )   => 'no-gap',
				),
			),
			array(
				'param_name' => 'source',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Data source', 'nucleus' ),
				'value'      => array(
					__( 'Categories', 'nucleus' ) => 'categories',
					__( 'IDs', 'nucleus' )        => 'ids',
				),
			),
			array(
				'param_name'  => 'query_post__in',
				'type'        => 'autocomplete',
				'weight'      => 10,
				'heading'     => __( 'Specify posts to retrieve', 'nucleus' ),
				'description' => __( 'Specify portfolio items you want to retrieve, by title', 'nucleus' ),
				'dependency'  => array( 'element' => 'source', 'value' => 'ids' ),
				'settings'    => array(
					'multiple'   => true,
					'min_length' => 2,
					'values'     => call_user_func( 'nucleus_core_portfolio_posts' ),
				),
			),
			array(
				'param_name' => 'query_categories',
				'type'       => 'autocomplete',
				'weight'     => 10,
				'heading'    => __( 'Categories', 'nucleus' ),
				'dependency' => array( 'element' => 'source', 'value' => 'categories' ),
				'settings'   => array(
					'multiple'       => true,
					'min_length'     => 2,
					'unique_values'  => true,
					'display_inline' => true,
					'values'         => call_user_func( 'nucleus_core_portfolio_categories' ),
				),
			),
			array(
				'param_name'  => 'query_post__not_in',
				'type'        => 'autocomplete',
				'weight'      => 10,
				'heading'     => __( 'Exclude posts', 'nucleus' ),
				'description' => __( 'Exclude some posts from results, by title.', 'nucleus' ),
				'dependency'  => array( 'element' => 'source', 'value' => 'categories' ),
				'settings'    => array(
					'multiple'   => true,
					'min_length' => 2,
					'values'     => call_user_func( 'nucleus_core_portfolio_posts' ),
				),
			),
			array_merge( $field_query_posts_per_page, array(
				'dependency' => array( 'element' => 'source', 'value_not_equal_to' => 'ids' ),
			) ),
			$field_query_order_by,
			$field_query_order,
			array(
				'param_name'       => 'is_more',
				'type'             => 'dropdown',
				'weight'           => 10,
				'heading'          => __( 'Enable Load More button?', 'nucleus' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'value'            => $value_no_yes,
			),
			array(
				'param_name' => 'more_pos',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'More position', 'nucleus' ),
				'group'      => $group_more,
				'std'        => 'center',
				'value'      => $value_lcr,
				'dependency' => array( 'element' => 'is_more', 'value' => 'yes' ),
			),
			array(
				'param_name'  => 'more_text',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'More text', 'nucleus' ),
				'description' => __( 'This text will be displayed on the Load More button. Also you can use a font icon here.', 'nucleus' ),
				'group'       => $group_more,
				'value'       => __( 'Load More', 'nucleus' ),
				'dependency'  => array( 'element' => 'is_more', 'value' => 'yes' ),
			),
			array(
				'param_name'       => 'is_filters',
				'type'             => 'dropdown',
				'weight'           => 10,
				'heading'          => __( 'Enable filters?', 'nucleus' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'value'            => $value_no_yes,
			),
			array(
				'param_name' => 'filters_pos',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Filters position', 'nucleus' ),
				'group'      => $group_filter,
				'std'        => 'center',
				'value'      => $value_lcr,
				'dependency' => array( 'element' => 'is_filters', 'value' => 'yes' ),
			),
			array(
				'param_name'  => 'filters_exclude',
				'type'        => 'autocomplete',
				'weight'      => 10,
				'heading'     => __( 'Exclude from filter list', 'nucleus' ),
				'description' => __( 'Enter categories won\'t be shown in the filters list. This option is useful if you specify some categories in General tab.', 'nucleus' ),
				'group'       => $group_filter,
				'dependency'  => array( 'element' => 'is_filters', 'value' => 'yes' ),
				'settings'    => array(
					'multiple'       => true,
					'min_length'     => 2,
					'unique_values'  => true,
					'display_inline' => true,
					'values'         => call_user_func( 'nucleus_core_portfolio_categories' ),
				),
			),
		) ),
	) );

	/**
	 * Image Box | nucleus_image_box
	 */
	vc_map( array(
		'base'     => 'nucleus_image_box',
		'name'     => __( 'Image Box', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'image',
				'type'       => 'attach_image',
				'weight'     => 10,
				'heading'    => __( 'Image', 'nucleus' ),
			),
			array(
				'param_name' => 'link',
				'type'       => 'vc_link',
				'weight'     => 10,
			),
			array(
				'param_name'  => 'title',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => $heading_title,
				'admin_label' => true,
			),
		) ),
	) );

	/**
	 * Testimonial | nucleus_testimonial
	 */
	vc_map( array(
		'base'     => 'nucleus_testimonial',
		'name'     => __( 'Testimonial', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'image',
				'type'       => 'attach_image',
				'weight'     => 10,
				'heading'    => __( 'Featured Image', 'nucleus' ),
			),
			array(
				'param_name'  => 'name',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Author Name', 'nucleus' ),
				'admin_label' => true,
			),
			array(
				'param_name' => 'position',
				'type'       => 'textfield',
				'weight'     => 10,
				'heading'    => __( 'Author Position', 'nucleus' ),
			),
			array(
				'param_name'  => 'quotation',
				'type'        => 'textarea',
				'weight'      => 10,
				'heading'     => __( 'Quotation', 'nucleus' ),
				'admin_label' => true,
			),
			array(
				'param_name' => 'link',
				'type'       => 'vc_link',
				'weight'     => 10,
			),
		) ),
	) );

	/**
	 * Video Popup | nucleus_video_popup
	 */
	vc_map( array(
		'base'     => 'nucleus_video_popup',
		'name'     => __( 'Video Popup', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name'  => 'video',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Link to video', 'nucleus' ),
				'description' => __( 'Paste a link to video, for example https://vimeo.com/33984473 or https://www.youtube.com/watch?v=DqO90q0WZ0M', 'nucleus' ),
				'admin_label' => true,
			),
			array(
				'param_name'  => 'title',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => $heading_title,
				'admin_label' => true,
			),
			array(
				'param_name' => 'size',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Size', 'nucleus' ),
				'std'        => 'normal',
				'value'      => array(
					__( 'Normal', 'nucleus' ) => 'normal',
					__( 'Small', 'nucleus' )  => 'small',
				),
			),
			$field_alignment,
			$field_skin,
		) ),
	) );

	/**
	 * Video Popup Tile | nucleus_video_popup_tile
	 */
	vc_map( array(
		'base'     => 'nucleus_video_popup_tile',
		'name'     => __( 'Video Popup Tile', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'cover',
				'type'       => 'attach_image',
				'weight'     => 10,
				'heading'    => __( 'Cover Image', 'nucleus' ),
			),
			array(
				'param_name'  => 'video',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Link to video', 'nucleus' ),
				'description' => __( 'Paste a link to video, for example https://vimeo.com/33984473 or https://www.youtube.com/watch?v=DqO90q0WZ0M', 'nucleus' ),
			),
			array(
				'param_name'  => 'title',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => $heading_title,
				'admin_label' => true,
			),
			array(
				'param_name'  => 'description',
				'type'        => 'textarea',
				'weight'      => 10,
				'heading'     => $heading_description,
				'description' => __( 'HTML is allowed', 'nucleus' ),
			),
		) ),
	) );

	/**
	 * Animated Digits | nucleus_animated_digits
	 */
	vc_map( array(
		'base'     => 'nucleus_animated_digits',
		'name'     => __( 'Animated Digits', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'image',
				'type'       => 'attach_image',
				'weight'     => 10,
				'heading'    => __( 'Featured Image', 'nucleus' ),
			),
			array(
				'param_name'  => 'digit',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Digit', 'nucleus' ),
				'description' => __( 'Accepts positive integer numbers', 'nucleus' ),
				'admin_label' => true,
			),
			array(
				'param_name'  => 'description',
				'type'        => 'textarea',
				'weight'      => 10,
				'heading'     => $heading_description,
				'description' => __( 'HTML is allowed', 'nucleus' ),
			),
			array(
				'param_name' => 'is_featured',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Make featured?', 'nucleus' ),
				'std'        => 'no',
				'value'      => $value_no_yes,
			),
			array(
				'param_name'  => 'duration',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Duration', 'nucleus' ),
				'description' => __( 'Set the increment duration from 0 to provided number. Accepts positive integer numbers in ms.', 'nucleus' ),
				'value'       => 1500,
			),
		) ),
	) );

	/**
	 * Team | nucleus_team
	 */
	vc_map( array(
		'base'     => 'nucleus_team',
		'name'     => __( 'Team', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array_merge(
			array(
				array(
					'param_name' => 'type',
					'type'       => 'dropdown',
					'weight'     => 10,
					'heading'    => __( 'Teammate hover effect', 'nucleus' ),
					'std'        => 'flip',
					'value'      => array(
						__( 'Flip', 'nucleus' )     => 1,
						__( 'Morphing', 'nucleus' ) => 2,
						__( 'Static', 'nucleus' )   => 3,
					),
				),
				array(
					'param_name' => 'image',
					'type'       => 'attach_image',
					'weight'     => 10,
					'heading'    => __( 'Featured Image', 'nucleus' ),
				),
				array(
					'param_name'  => 'name',
					'type'        => 'textfield',
					'weight'      => 10,
					'heading'     => __( 'Name', 'nucleus' ),
					'admin_label' => true,
				),
				array(
					'param_name' => 'position',
					'type'       => 'textfield',
					'weight'     => 10,
					'heading'    => __( 'Position', 'nucleus' ),
				),
				array(
					'param_name' => 'about',
					'type'       => 'textarea',
					'weight'     => 10,
					'heading'    => __( 'About', 'nucleus' ),
					'dependency' => array( 'element' => 'type', 'value' => array( '1', '2' ) ),
				),
			),
			(array) vc_map_integrate_shortcode( 'nucleus_socials', 'socials_', '',
				array( 'include_only' => array( 'socials', 'is_tooltip' ) ),
				array( 'element' => 'type', 'value' => array( '1', '2' ) )
			)
		) ),
	) );

	/**
	 * Pricing | nucleus_pricing
	 */
	vc_map( array(
		'base'     => 'nucleus_pricing',
		'name'     => __( 'Pricing', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array_merge(
			array(
				array(
					'param_name' => 'image',
					'type'       => 'attach_image',
					'weight'     => 10,
					'heading'    => __( 'Featured Image', 'nucleus' ),
				),
				array(
					'param_name'  => 'name',
					'type'        => 'textfield',
					'weight'      => 10,
					'heading'     => __( 'Name', 'nucleus' ),
					'admin_label' => true,
				),
				array(
					'param_name' => 'description',
					'type'       => 'textarea',
					'weight'     => 10,
					'heading'    => __( 'Description', 'nucleus' ),
				),
				array(
					'param_name'  => 'is_button',
					'type'        => 'dropdown',
					'weight'      => 10,
					'heading'     => __( 'Add button?', 'nucleus' ),
					'description' => __( 'You can set the price in button\'s text field', 'nucleus' ),
					'std'         => 'no',
					'value'       => $value_no_yes,
				),
				array(
					'param_name' => 'options',
					'type'       => 'param_group',
					'heading'    => __( 'Options', 'nucleus' ),
					'params'     => array(
						array(
							'param_name'  => 'property',
							'type'        => 'textfield',
							'weight'      => 10,
							'heading'     => __( 'Property', 'nucleus' ),
							'admin_label' => true,
						),
						array(
							'param_name'  => 'value',
							'type'        => 'textfield',
							'weight'      => 10,
							'heading'     => __( 'Value', 'nucleus' ),
							'admin_label' => true,
						),
					),
				),
			),
			(array) vc_map_integrate_shortcode( 'nucleus_button', 'button_', __( 'Button', 'nucleus' ),
				array( 'exclude' => array( 'is_animation', 'animation_type', 'animation_delay', 'animation_easing' ) ),
				array( 'element' => 'is_button', 'value' => 'yes' )
			)
		) ),
	) );

	/**
	 * Pricing Table | nucleus_pricing_table
	 */
	vc_map( array(
		'base'     => 'nucleus_pricing_table',
		'name'     => __( 'Pricing Table', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array_merge(
			array(
				array(
					'param_name' => 'is_title',
					'type'       => 'dropdown',
					'weight'     => 10,
					'heading'    => __( 'Add title?', 'nucleus' ),
					'std'        => 'no',
					'value'      => $value_no_yes,
				),
			),
			(array) vc_map_integrate_shortcode( 'nucleus_block_title', 'title_', $heading_title,
				array( 'exclude' => array( 'is_animation', 'animation_type', 'animation_delay', 'animation_easing', 'alignment' ) ),
				array( 'element' => 'is_title', 'value' => 'yes' )
			),
			array(
				array(
					'param_name'  => 'is_switch',
					'type'        => 'dropdown',
					'weight'      => 10,
					'heading'     => __( 'Add switch?', 'nucleus' ),
					'description' => __( 'NOTE: switcher is based on Pricing Table > Types. So you should have at least 2 types.', 'nucleus' ),
					'std'         => 'no',
					'value'       => $value_no_yes,
				),
				array(
					'param_name' => 'switch_label',
					'type'       => 'textfield',
					'weight'     => 10,
					'heading'    => __( 'Switch Label', 'nucleus' ),
					'group'      => $group_switcher,
					'dependency' => array( 'element' => 'is_switch', 'value' => 'yes' ),
				),
				array(
					'param_name'  => 'switch_type',
					'type'        => 'dropdown',
					'weight'      => 10,
					'heading'     => __( 'Switch Type', 'nucleus' ),
					'description' => __( 'This option lets you choose what to use as switcher "labels": images or simple text.', 'nucleus' ),
					'group'       => $group_switcher,
					'dependency'  => array( 'element' => 'is_switch', 'value' => 'yes' ),
					'std'         => 'text',
					'value'       => array(
						__( 'Text', 'nucleus' )  => 'text',
						__( 'Image', 'nucleus' ) => 'image',
					),
				),
				array(
					'param_name' => 'switch_text_left',
					'type'       => 'textfield',
					'weight'     => 10,
					'heading'    => __( 'Left', 'nucleus' ),
					'group'      => $group_switcher,
					'dependency' => array( 'element' => 'switch_type', 'value' => 'text' ),
				),
				array(
					'param_name' => 'switch_text_right',
					'type'       => 'textfield',
					'weight'     => 10,
					'heading'    => __( 'Right', 'nucleus' ),
					'group'      => $group_switcher,
					'dependency' => array( 'element' => 'switch_type', 'value' => 'text' ),
				),
				array(
					'param_name' => 'switch_image_left',
					'type'       => 'attach_image',
					'weight'     => 10,
					'heading'    => __( 'Left', 'nucleus' ),
					'group'      => $group_switcher,
					'dependency' => array( 'element' => 'switch_type', 'value' => 'image' ),
				),
				array(
					'param_name' => 'switch_image_right',
					'type'       => 'attach_image',
					'weight'     => 10,
					'heading'    => __( 'Right', 'nucleus' ),
					'group'      => $group_switcher,
					'dependency' => array( 'element' => 'switch_type', 'value' => 'image' ),
				),
			)
		) ),
	) );

	/**
	 * Download Counter | nucleus_download_counter
	 */
	vc_map( array(
		'base'     => 'nucleus_download_counter',
		'name'     => __( 'Download Counter', 'nucleus' ),
		'category' => $category,
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name'  => 'number',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Number', 'nucleus' ),
				'admin_label' => true,
			),
			array(
				'param_name'  => 'label',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Label', 'nucleus' ),
				'admin_label' => true,
			),
		) ),
	) );

	/**
	 * Countdown | nucleus_countdown
	 */
	vc_map( array(
		'base'     => 'nucleus_countdown',
		'name'     => __( 'Countdown', 'nucleus' ),
		'category' => $category,
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name'  => 'date',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Date', 'nucleus' ),
				'description' => __( 'Add a due date in format "Month/Day/Year Hour:Minute:Second", for example 12/15/2016 12:00:00. You can skip the date or time parts.', 'nucleus' ),
				'admin_label' => true,
			),
		) ),
	) );

	/**
	 * Progress Bars | nucleus_progress_bars
	 */
	vc_map( array(
		'base'     => 'nucleus_progress_bars',
		'name'     => __( 'Progress Bars', 'nucleus' ),
		'category' => $category,
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'bars',
				'type'       => 'param_group',
				'heading'    => __( 'Bars', 'nucleus' ),
				'params'     => array(
					array(
						'param_name'  => 'value',
						'type'        => 'textfield',
						'weight'      => 10,
						'heading'     => __( 'Progress value', 'nucleus' ),
						'description' => __( 'Positive integer number from 0 till 100', 'nucleus' ),
						'admin_label' => true,
					),
					array(
						'param_name'  => 'label',
						'type'        => 'textfield',
						'weight'      => 10,
						'heading'     => __( 'Label', 'nucleus' ),
						'admin_label' => true,
					),
					array(
						'param_name' => 'is_units',
						'type'       => 'dropdown',
						'weight'     => 10,
						'heading'    => __( 'Show units?', 'nucleus' ),
						'std'        => 'yes',
						'value'      => $value_no_yes,
					),
					array(
						'param_name' => 'color',
						'type'       => 'dropdown',
						'weight'     => 10,
						'heading'    => __( 'Color', 'nucleus' ),
						'value'      => $value_colors,
					),
					array(
						'param_name' => 'is_animated',
						'type'       => 'dropdown',
						'weight'     => 10,
						'std'        => 'yes',
						'heading'    => __( 'Animated?', 'nucleus' ),
						'value'      => $value_no_yes,
					),
				),
			),
		) ),
	) );

	/**
	 * Gallery | nucleus_gallery
	 */
	vc_map( array(
		'base'     => 'nucleus_gallery',
		'name'     => __( 'Gallery', 'nucleus' ),
		'category' => $category,
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'images',
				'type'       => 'attach_images',
				'weight'     => 10,
				'heading'    => __( 'Gallery', 'nucleus' ),
			),
			array(
				'param_name'  => 'is_title',
				'type'        => 'dropdown',
				'weight'      => 10,
				'heading'     => __( 'Show titles?', 'nucleus' ),
				'description' => __( 'Titles from image metadata will be used', 'nucleus' ),
				'value'       => $value_no_yes,
			),
		) ),
	) );

	/**
	 * Icon Box | nucleus_icon_box
	 */
	vc_map( array(
		'base'     => 'nucleus_icon_box',
		'name'     => __( 'Icon Box', 'nucleus' ),
		'category' => $category,
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'type',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Visual Type', 'nucleus' ),
				'value'      => array(
					__( 'Image', 'nucleus' ) => 'image',
					__( 'Icon', 'nucleus' )  => 'icon',
				),
			),
			array(
				'param_name' => 'image',
				'type'       => 'attach_image',
				'weight'     => 10,
				'heading'    => __( 'Image', 'nucleus' ),
				'dependency' => array( 'element' => 'type', 'value' => 'image' ),
			),
			array_merge( $field_icon_library, array(
				'dependency' => array( 'element' => 'type', 'value' => 'icon' ),
			) ),
			$field_icon_fontawesome,
			$field_icon_openiconic,
			$field_icon_typicons,
			$field_icon_entypo,
			$field_icon_linecons,
			$field_icon_feather,
			$field_icon_flaticon,
			array(
				'param_name'  => 'title',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => $heading_title,
				'admin_label' => true,
			),
			array(
				'param_name' => 'description',
				'type'       => 'textarea',
				'weight'     => 10,
				'heading'    => __( 'Description', 'nucleus' ),
			),
			$field_alignment,
			array(
				'param_name'  => 'layout',
				'type'        => 'dropdown',
				'weight'      => 10,
				'heading'     => __( 'Layout', 'nucleus' ),
				'description' => __( 'This option allow you to control the icon position', 'nucleus' ),
				'value'       => array(
					__( 'Vertical', 'nucleus' )   => 'vertical',
					__( 'Horizontal', 'nucleus' ) => 'horizontal',
				),
			),
			array(
				'param_name'  => 'is_expandable',
				'type'        => 'dropdown',
				'weight'      => 10,
				'heading'     => __( 'Expandable?', 'nucleus' ),
				'description' => __( 'Enables the "expand" effect', 'nucleus' ),
				'value'       => $value_no_yes,
				'dependency'  => array( 'element' => 'layout', 'value' => 'vertical' ),
			),
		) ),
	) );

	/**
	 * Google Maps | nucleus_map
	 */
	vc_map( array(
		'base'     => 'nucleus_map',
		'name'     => __( 'Google Maps', 'nucleus' ),
		'category' => $category,
		'icon'     => 'icon-wpb-map-pin',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name'  => 'location',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Location', 'nucleus' ),
				'description' => __( 'Enter any search query, which you can find on Google Maps, e.g. "New York, USA".', 'nucleus' ),
			),
			array(
				'param_name'       => 'height',
				'type'             => 'textfield',
				'weight'           => 10,
				'heading'          => __( 'Map height', 'nucleus' ),
				'description'      => __( 'Height of the map in pixels.', 'nucleus' ),
				'value'            => 500,
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),
			array(
				'param_name'       => 'zoom',
				'type'             => 'textfield',
				'weight'           => 10,
				'heading'          => __( 'Zoom', 'nucleus' ),
				'description'      => __( 'The initial Map zoom level', 'nucleus' ),
				'value'            => 14,
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),
			array(
				'param_name'       => 'is_zoom',
				'type'             => 'dropdown',
				'weight'           => 10,
				'heading'          => __( 'Zoom Controls', 'nucleus' ),
				'description'      => __( 'Enable or disable map controls like pan, zoom, etc.', 'nucleus' ),
				'std'              => 'disable',
				'value'            => $value_enable_disable,
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),
			array(
				'param_name'       => 'is_scroll',
				'type'             => 'dropdown',
				'weight'           => 10,
				'heading'          => __( 'ScrollWheel', 'nucleus' ),
				'description'      => __( 'Enable or disable scrollwheel zooming on the map.', 'nucleus' ),
				'std'              => 'disable',
				'value'            => $value_enable_disable,
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),
			array(
				'param_name'  => 'is_marker',
				'type'        => 'dropdown',
				'weight'      => 10,
				'heading'     => __( 'Custom marker', 'nucleus' ),
				'description' => __( 'Enable or disable custom marker on the map.', 'nucleus' ),
				'std'         => 'disable',
				'value'       => $value_enable_disable,
			),
			array(
				'param_name'  => 'marker_title',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Marker Title', 'nucleus' ),
				'description' => __( 'Optional title appears on marker hover.', 'nucleus' ),
				'dependency'  => array( 'element' => 'is_marker', 'value' => 'enable' ),
			),
			array(
				'param_name' => 'marker',
				'type'       => 'attach_image',
				'weight'     => 10,
				'heading'    => __( 'Custom marker', 'nucleus' ),
				'dependency' => array( 'element' => 'is_marker', 'value' => 'enable' ),
			),
			array(
				'param_name'  => 'style',
				'type'        => 'textarea_raw_html',
				'weight'      => 10,
				'heading'     => __( 'Maps custom styling', 'nucleus' ),
				'group'       => __( 'Styling', 'nucleus' ),
				'description' => wp_kses( __( 'Generate your styles in <a href="https://snazzymaps.com/editor" target="_blank">Snazzymaps Editor</a> and paste JavaScript Style Array in field above', 'nucleus' ), array(
					'a' => array( 'href' => true, 'target' => true ),
				) ),
			),
		) ),
	) );

	/**
	 * Subscribe | nucleus_subscribe
	 */
	vc_map( array(
		'base'     => 'nucleus_subscribe',
		'name'     => __( 'Subscribe', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name'  => 'url',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'MailChimp URL', 'nucleus' ),
				'description' => __( 'This URL can be retrieved from your MailChimp dashboard > Lists > your desired list > list settings > forms. in your form creation page you will need to click on "share it" tab then find "Your subscribe form lives at this URL:". Its a short URL so you will need to visit this link. Once you get into the your created form page, then copy the full address and paste it here in this form. URL look like http://YOUR_USER_NAME.us6.list-manage.com/subscribe?u=d5f4e5e82a59166b0cfbc716f&id=4db82d169b', 'nucleus' ),
			),
			array(
				'param_name' => 'placeholder',
				'type'       => 'textfield',
				'weight'     => 10,
				'heading'    => __( 'Placeholder', 'nucleus' ),
			),
			array(
				'param_name'  => 'button_text',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'Button Text', 'nucleus' ),
				'description' => __( 'Text on the button', 'nucleus' ),
				'value'       => __( 'Subscribe', 'nucleus' ),
			),
			array(
				'param_name'         => 'button_color',
				'type'               => 'dropdown',
				'weight'             => 10,
				'heading'            => __( 'Button Color', 'nucleus' ),
				'param_holder_class' => 'nucleus-colored',
				'std'                => 'default',
				'value'              => $value_colors_light,
			),
		) ),
	) );

	/**
	 * Timetable | nucleus_timetable
	 */
	vc_map( array(
		'base'     => 'nucleus_timetable',
		'name'     => __( 'Timetable', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'col1_icon',
				'type'       => 'attach_image',
				'weight'     => 10,
				'heading'    => $heading_column_icon,
				'group'      => $group_column1,
			),
			array(
				'param_name' => 'col1_title',
				'type'       => 'textfield',
				'weight'     => 10,
				'heading'    => $heading_column_title,
				'group'      => $group_column1,
			),
			array(
				'param_name' => 'col1_options',
				'type'       => 'param_group',
				'weight'     => 10,
				'heading'    => $heading_column_options,
				'group'      => $group_column1,
				'params'     => array(
					array(
						'param_name'  => 'item',
						'type'        => 'textfield',
						'weight'      => 10,
						'heading'     => __( 'Item', 'nucleus' ),
						'admin_label' => true,
					),
				),
			),
			array(
				'param_name' => 'col1_is_featured',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => $heading_column_is_featured,
				'group'      => $group_column1,
				'value'      => $value_no_yes,
			),
			array(
				'param_name' => 'col2_icon',
				'type'       => 'attach_image',
				'weight'     => 10,
				'heading'    => $heading_column_icon,
				'group'      => $group_column2,
			),
			array(
				'param_name' => 'col2_title',
				'type'       => 'textfield',
				'weight'     => 10,
				'heading'    => $heading_column_title,
				'group'      => $group_column2,
			),
			array(
				'param_name' => 'col2_options',
				'type'       => 'param_group',
				'weight'     => 10,
				'heading'    => $heading_column_options,
				'group'      => $group_column2,
				'params'     => array(
					array(
						'param_name'  => 'item',
						'type'        => 'textfield',
						'weight'      => 10,
						'heading'     => __( 'Item', 'nucleus' ),
						'admin_label' => true,
					),
				),
			),
			array(
				'param_name' => 'col2_is_featured',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => $heading_column_is_featured,
				'group'      => $group_column2,
				'value'      => $value_no_yes,
			),
			array(
				'param_name' => 'col3_icon',
				'type'       => 'attach_image',
				'weight'     => 10,
				'heading'    => $heading_column_icon,
				'group'      => $group_column3,
			),
			array(
				'param_name' => 'col3_title',
				'type'       => 'textfield',
				'weight'     => 10,
				'heading'    => $heading_column_title,
				'group'      => $group_column3,
			),
			array(
				'param_name' => 'col3_options',
				'type'       => 'param_group',
				'weight'     => 10,
				'heading'    => $heading_column_options,
				'group'      => $group_column3,
				'params'     => array(
					array(
						'param_name'  => 'item',
						'type'        => 'textfield',
						'weight'      => 10,
						'heading'     => __( 'Item', 'nucleus' ),
						'admin_label' => true,
					),
				),
			),
			array(
				'param_name' => 'col3_is_featured',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => $heading_column_is_featured,
				'group'      => $group_column3,
				'value'      => $value_no_yes,
			),
			array(
				'param_name' => 'col4_icon',
				'type'       => 'attach_image',
				'weight'     => 10,
				'heading'    => $heading_column_icon,
				'group'      => $group_column4,
			),
			array(
				'param_name' => 'col4_title',
				'type'       => 'textfield',
				'weight'     => 10,
				'heading'    => $heading_column_title,
				'group'      => $group_column4,
			),
			array(
				'param_name' => 'col4_options',
				'type'       => 'param_group',
				'weight'     => 10,
				'heading'    => $heading_column_options,
				'group'      => $group_column4,
				'params'     => array(
					array(
						'param_name'  => 'item',
						'type'        => 'textfield',
						'weight'      => 10,
						'heading'     => __( 'Item', 'nucleus' ),
						'admin_label' => true,
					),
				),
			),
			array(
				'param_name' => 'col4_is_featured',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => $heading_column_is_featured,
				'group'      => $group_column4,
				'value'      => $value_no_yes,
			),
		) ),
	) );

	/**
	 * Blog | nucleus_blog
	 */
	vc_map( array(
		'base'     => 'nucleus_blog',
		'name'     => __( 'Blog', 'nucleus' ),
		'category' => $category,
		'icon'     => 'vc_icon-vc-masonry-grid',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name'  => 'source',
				'type'        => 'dropdown',
				'weight'      => 10,
				'heading'     => __( 'Data source', 'nucleus' ),
				'description' => __( 'Choose the "List of IDs" if you want to retrieve some specific posts. If you choose the "Posts" further you can clarify the request.', 'nucleus' ),
				'value'       => array(
					__( 'Posts', 'nucleus' ) => 'posts',
					__( 'IDs', 'nucleus' )   => 'ids',
				),
			),
			array(
				'param_name'  => 'query_post__in',
				'type'        => 'autocomplete',
				'weight'      => 10,
				'heading'     => __( 'Posts to retrieve', 'nucleus' ),
				'description' => __( 'Specify items you want to retrieve, by title', 'nucleus' ),
				'dependency'  => array( 'element' => 'source', 'value' => 'ids' ),
				'settings'    => array(
					'multiple'      => true,
					'min_length'    => 2,
					'unique_values' => true,
					'values'        => call_user_func( 'nucleus_core_blog_posts' ),
				),
			),
			array(
				'param_name'  => 'query_taxonomies',
				'type'        => 'autocomplete',
				'weight'      => 10,
				'heading'     => __( 'Specify the source', 'nucleus' ),
				'description' => __( 'You can specify post categories, tags or custom taxonomies. NOTE: do not use tags and categories with the same slug!', 'nucleus' ),
				'dependency'  => array( 'element' => 'source', 'value' => 'posts' ),
				'settings'    => array(
					'multiple'       => true,
					'min_length'     => 2,
					'sortable'       => true,
					'unique_values'  => true,
					'groups'         => true,
					'display_inline' => true,
					'values'         => call_user_func( 'nucleus_core_blog_terms' ),
				),
			),
			array(
				'param_name'  => 'query_post__not_in',
				'type'        => 'autocomplete',
				'weight'      => 10,
				'heading'     => __( 'Exclude posts', 'nucleus' ),
				'description' => __( 'Exclude some posts from results, by title.', 'nucleus' ),
				'dependency'  => array( 'element' => 'source', 'value' => 'posts' ),
				'settings'    => array(
					'multiple'      => true,
					'min_length'    => 2,
					'unique_values' => true,
					'values'        => call_user_func( 'nucleus_core_blog_posts' ),
				),
			),
			array_merge( $field_query_posts_per_page, array(
				'dependency' => array( 'element' => 'source', 'value_not_equal_to' => 'ids' ),
			) ),
			$field_query_order_by,
			$field_query_order,
			array(
				'param_name' => 'is_more',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Enable Load More?', 'nucleus' ),
				'value'      => $value_no_yes,
			),
			array(
				'param_name' => 'more_position',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'More position', 'nucleus' ),
				'group'      => $group_more,
				'std'        => 'center',
				'value'      => $value_lcr,
				'dependency' => array( 'element' => 'is_more', 'value' => 'yes' ),
			),
			array(
				'param_name'  => 'more_text',
				'type'        => 'textfield',
				'weight'      => 10,
				'heading'     => __( 'More text', 'nucleus' ),
				'description' => __( 'This text will be displayed on the Load More button. Also you can use a font icon here.', 'nucleus' ),
				'group'       => $group_more,
				'value'       => __( 'Load More', 'nucleus' ),
				'dependency'  => array( 'element' => 'is_more', 'value' => 'yes' ),
			),
		) ),
	) );

	/**
	 * Posts | nucleus_posts
	 */
	vc_map( array(
		'base'     => 'nucleus_posts',
		'name'     => __( 'Posts', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name'  => 'source',
				'type'        => 'dropdown',
				'weight'      => 10,
				'heading'     => __( 'Data source', 'nucleus' ),
				'description' => __( 'Choose the "List of IDs" if you want to retrieve some specific posts. If you choose the "Posts" further you can clarify the request.', 'nucleus' ),
				'value'       => array(
					__( 'Posts', 'nucleus' ) => 'posts',
					__( 'IDs', 'nucleus' )   => 'ids',
				),
			),
			array(
				'param_name'  => 'query_post__in',
				'type'        => 'autocomplete',
				'weight'      => 10,
				'heading'     => __( 'Specify posts to retrieve', 'nucleus' ),
				'description' => __( 'Specify portfolio items you want to retrieve, by title', 'nucleus' ),
				'dependency'  => array( 'element' => 'source', 'value' => 'ids' ),
				'settings'    => array(
					'multiple'      => true,
					'min_length'    => 2,
					'unique_values' => true,
					'values'        => call_user_func( 'nucleus_core_blog_posts' ),
				),
			),
			array(
				'param_name'  => 'query_taxonomies',
				'type'        => 'autocomplete',
				'weight'      => 10,
				'heading'     => __( 'Specify the source', 'nucleus' ),
				'description' => __( 'You can specify post categories, tags or custom taxonomies. NOTE: do not use tags and categories with the same slug!', 'nucleus' ),
				'dependency'  => array( 'element' => 'source', 'value' => 'posts' ),
				'settings'    => array(
					'multiple'       => true,
					'min_length'     => 2,
					'sortable'       => true,
					'unique_values'  => true,
					'groups'         => true,
					'display_inline' => true,
					'values'         => call_user_func( 'nucleus_core_blog_terms' ),
				),
			),
			array(
				'param_name'  => 'query_post__not_in',
				'type'        => 'autocomplete',
				'weight'      => 10,
				'heading'     => __( 'Exclude posts', 'nucleus' ),
				'description' => __( 'Exclude some posts from results, by title.', 'nucleus' ),
				'dependency'  => array( 'element' => 'source', 'value' => 'posts' ),
				'settings'    => array(
					'multiple'      => true,
					'min_length'    => 2,
					'unique_values' => true,
					'values'        => call_user_func( 'nucleus_core_blog_posts' ),
				),
			),
			array_merge( $field_query_posts_per_page, array(
				'dependency' => array( 'element' => 'source', 'value_not_equal_to' => 'ids' ),
			) ),
			$field_query_order_by,
			$field_query_order,
		) ),
	) );

	/**
	 * Split Contacts | nucleus_split_contacts
	 */
	vc_map( array(
		'base'     => 'nucleus_split_contacts',
		'name'     => __( 'Split Contacts', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array_merge(
			array(
				array(
					'param_name' => 'is_contact',
					'type'       => 'dropdown',
					'weight'     => 10,
					'heading'    => __( 'Enable Contacts Widget?', 'nucleus' ),
					'std'        => 'yes',
					'value'      => $value_no_yes,
				),
				array(
					'param_name' => 'contact_image',
					'type'       => 'attach_image',
					'weight'     => 10,
					'heading'    => __( 'Featured Image', 'nucleus' ),
					'group'      => $group_contact,
					'dependency' => array( 'element' => 'is_contact', 'value' => 'yes' ),
				),
				array(
					'param_name' => 'contact_title',
					'type'       => 'textfield',
					'weight'     => 10,
					'heading'    => __( 'Title', 'nucleus' ),
					'group'      => $group_contact,
					'dependency' => array( 'element' => 'is_contact', 'value' => 'yes' ),
				),
				array(
					'param_name' => 'contact_subtitle',
					'type'       => 'textfield',
					'weight'     => 10,
					'heading'    => __( 'Subtitle', 'nucleus' ),
					'group'      => $group_contact,
					'dependency' => array( 'element' => 'is_contact', 'value' => 'yes' ),
				),
				array(
					'param_name' => 'contact_info',
					'type'       => 'param_group',
					'weight'     => 10,
					'heading'    => __( 'Info', 'nucleus' ),
					'group'      => $group_contact,
					'dependency' => array( 'element' => 'is_contact', 'value' => 'yes' ),
					'params'     => array(
						array(
							'param_name' => 'type',
							'type'       => 'dropdown',
							'weight'     => 10,
							'heading'    => __( 'Type', 'nucleus' ),
							'value'      => array(
								__( 'Addr', 'nucleus' )  => 'addr',
								__( 'Tel', 'nucleus' )   => 'tel',
								__( 'Email', 'nucleus' ) => 'email',
								__( 'Skype', 'nucleus' ) => 'skype',
							),
						),
						array(
							'param_name'  => 'value',
							'type'        => 'textfield',
							'weight'      => 10,
							'heading'     => __( 'Value', 'nucleus' ),
							'admin_label' => true,
						),
					),
				),
			),
			(array) vc_map_integrate_shortcode( 'nucleus_map', 'map_', __( 'Map', 'nucleus' ),
				array( 'exclude' => array( 'is_animation', 'animation_type', 'animation_delay', 'animation_easing' ) )
			),
			array(
				array(
					'param_name' => 'form_id',
					'type'       => 'dropdown',
					'weight'     => 10,
					'heading'    => __( 'Contact Form 7', 'nucleus' ),
					'group'      => $group_form,
					'value'      => call_user_func( 'nucleus_core_cf7_posts' ),
				),
				array(
					'param_name' => 'form_title',
					'type'       => 'textfield',
					'weight'     => 10,
					'heading'    => __( 'Form Title', 'nucleus' ),
					'group'      => $group_form,
				),
				array(
					'param_name' => 'form_bg',
					'type'       => 'attach_image',
					'weight'     => 10,
					'heading'    => __( 'Background', 'nucleus' ),
					'group'      => $group_form,
				),
			)
		) ),
	) );

	/**
	 * Gadgets Slideshow | nucleus_gadgets_slideshow
	 */
	vc_map( array(
		'base'     => 'nucleus_gadgets_slideshow',
		'name'     => __( 'Gadgets Slideshow', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array_merge(
			(array) vc_map_integrate_shortcode( 'nucleus_block_title', 'title_', '',
				array(
					'exclude' => array(
						'is_animation',
						'animation_type',
						'animation_delay',
						'animation_easing',
						'alignment',
						'class',
					),
				)
			),
			array(
				array(
					'param_name' => 'slides',
					'type'       => 'param_group',
					'weight'     => 10,
					'heading'    => __( 'Slides', 'nucleus' ),
					'group'      => __( 'Slides', 'nucleus' ),
					'params'     => array(
						array(
							'param_name'  => 'id',
							'type'        => 'textfield',
							'weight'      => 10,
							'heading'     => __( 'Unique ID', 'nucleus' ),
							'description' => __( 'This ID should be unique per slide', 'nucleus' ),
							'admin_label' => true,
						),
						array(
							'param_name' => 'icon',
							'type'       => 'attach_image',
							'weight'     => 10,
							'heading'    => __( 'Featured Image', 'nucleus' ),
						),
						array(
							'param_name'  => 'title',
							'type'        => 'textfield',
							'weight'      => 10,
							'heading'     => $heading_title,
							'admin_label' => true,
						),
						array(
							'param_name'  => 'description',
							'type'        => 'textarea',
							'weight'      => 10,
							'heading'     => $heading_description,
							'description' => __( 'HTML is allowed', 'nucleus' ),
						),
						array(
							'param_name' => 'phone',
							'type'       => 'attach_image',
							'weight'     => 10,
							'heading'    => __( 'Phone screen', 'nucleus' ),
						),
						array(
							'param_name' => 'tablet',
							'type'       => 'attach_image',
							'weight'     => 10,
							'heading'    => __( 'Tablet screen', 'nucleus' ),
						),
					),
				),
				$field_is_autoplay,
				$field_delay,
			)
		) ),
	) );

	/**
	 * Products | nucleus_products
	 */
	vc_map( array(
		'base'     => 'nucleus_products',
		'name'     => __( 'Products', 'nucleus' ),
		'category' => $category,
		'icon'     => '',
		'params'   => nucleus_vc_map_params( array(
			array(
				'param_name' => 'source',
				'type'       => 'dropdown',
				'weight'     => 10,
				'heading'    => __( 'Data source', 'nucleus' ),
				'value'      => array(
					__( 'Categories', 'nucleus' ) => 'categories',
					__( 'IDs', 'nucleus' )        => 'ids',
				),
			),
			array(
				'param_name'  => 'query_post__in',
				'type'        => 'autocomplete',
				'weight'      => 10,
				'heading'     => __( 'Posts to retrieve', 'nucleus' ),
				'description' => __( 'Specify items you want to retrieve, by title', 'nucleus' ),
				'dependency'  => array( 'element' => 'source', 'value' => 'ids' ),
				'settings'    => array(
					'multiple'   => true,
					'min_length' => 2,
					'values'     => call_user_func( 'nucleus_core_products' ),
				),
			),
			array(
				'param_name' => 'query_categories',
				'type'       => 'autocomplete',
				'weight'     => 10,
				'heading'    => __( 'Categories', 'nucleus' ),
				'dependency' => array( 'element' => 'source', 'value' => 'categories' ),
				'settings'   => array(
					'multiple'       => true,
					'min_length'     => 2,
					'unique_values'  => true,
					'display_inline' => true,
					'values'         => call_user_func( 'nucleus_core_product_cats' ),
				),
			),
			array(
				'param_name'  => 'query_post__not_in',
				'type'        => 'autocomplete',
				'weight'      => 10,
				'heading'     => __( 'Exclude posts', 'nucleus' ),
				'description' => __( 'Exclude some posts from results, by title.', 'nucleus' ),
				'dependency'  => array( 'element' => 'source', 'value' => 'categories' ),
				'settings'    => array(
					'multiple'   => true,
					'min_length' => 2,
					'values'     => call_user_func( 'nucleus_core_products' ),
				),
			),
			array_merge( $field_query_posts_per_page, array(
				'dependency' => array( 'element' => 'source', 'value_not_equal_to' => 'ids' ),
			) ),
			$field_query_order_by,
			$field_query_order,
		) ),
	) );
}

add_action( 'vc_mapper_init_after', 'nucleus_core_vc_init' );

/**
 * Returns the shortcode params for {@see vc_map()}
 * with nucleus mandatory fields
 *
 * @param array $params Shortcode params
 *
 * @return array
 */
function nucleus_vc_map_params( $params ) {
	$group_animation = __( 'Animation', 'nucleus' );

	return array_merge( $params, array(
		array(
			'param_name' => 'is_animation',
			'type'       => 'dropdown',
			'heading'    => __( 'Animation', 'nucleus' ),
			'weight'     => 10,
			'value'      => array(
				__( 'Disable', 'nucleus' ) => 'disable',
				__( 'Enable', 'nucleus' )  => 'enable',
			),
		),
		array(
			'param_name' => 'animation_type',
			'type'       => 'dropdown',
			'heading'    => __( 'Type', 'nucleus' ),
			'weight'     => 10,
			'group'      => $group_animation,
			'dependency' => array( 'element' => 'is_animation', 'value' => 'enable' ),
			'value'      => array(
				__( 'Top', 'nucleus' )        => 'top',
				__( 'Bottom', 'nucleus' )     => 'bottom',
				__( 'Left', 'nucleus' )       => 'left',
				__( 'Right', 'nucleus' )      => 'right',
				__( 'Scale up', 'nucleus' )   => 'scaleUp',
				__( 'Scale down', 'nucleus' ) => 'scaleDown',
			),
		),
		array(
			'param_name' => 'animation_delay',
			'type'       => 'dropdown',
			'heading'    => __( 'Delay', 'nucleus' ),
			'weight'     => 10,
			'group'      => $group_animation,
			'dependency' => array( 'element' => 'is_animation', 'value' => 'enable' ),
			'value'      => array(
				__( 'None', 'nucleus' ) => 0,
				'1'                     => 1,
				'2'                     => 2,
				'3'                     => 3,
				'4'                     => 4,
				'5'                     => 5,
				'6'                     => 6,
				'7'                     => 7,
				'8'                     => 8,
			),
		),
		array(
			'param_name' => 'animation_easing',
			'type'       => 'dropdown',
			'heading'    => __( 'Easing', 'nucleus' ),
			'weight'     => 10,
			'group'      => $group_animation,
			'dependency' => array( 'element' => 'is_animation', 'value' => 'enable' ),
			'value'      => array(
				__( 'None', 'nucleus' )  => 'none',
				__( 'Quad', 'nucleus' )  => 'quad',
				__( 'Cubic', 'nucleus' ) => 'cubic',
				__( 'Quart', 'nucleus' ) => 'quart',
				__( 'Quint', 'nucleus' ) => 'quint',
				__( 'Sine', 'nucleus' )  => 'sine',
				__( 'Expo', 'nucleus' )  => 'expo',
				__( 'Circ', 'nucleus' )  => 'circ',
				__( 'Back', 'nucleus' )  => 'back',
			),
		),
		array(
			'param_name'  => 'class',
			'type'        => 'textfield',
			'weight'      => - 1,
			'heading'     => __( 'Extra class name', 'nucleus' ),
			'description' => __( 'Add extra classes, divided by whitespace, if you wish to style particular content element differently', 'nucleus' ),
		),
	) );
}