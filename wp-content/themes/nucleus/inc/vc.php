<?php
/**
 * Visual Composer actions & filters
 *
 * @author 8guild
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! defined( 'WPB_VC_VERSION' ) ) {
	return;
}

/**
 * Setup Visual Composer for theme.
 *
 * Also, this function could be defined in theme "core" plugin.
 */
function nucleus_vc_before_init() {
	vc_disable_frontend();
	vc_set_as_theme();

	/**
	 * Filter the default post types for Visual Composer
	 * 
	 * This means VC should be enabled for this post types by default
	 * 
	 * @param array $post_types Post types list
	 */
	$post_types = apply_filters( 'nucleus_vc_default_editor_post_types', array(
		'page',
		'post',
		'nucleus_portfolio',
	) );
	
	vc_set_default_editor_post_types( $post_types );


	/**
	 * Filter the path to overwritten Visual Composer templates
	 *
	 * This filter allows easily change the path to modified VC templates
	 * without adding another action. In case if you want overwrite our
	 * overwritten templates.
	 *
	 * @param string $path Default path to our templates
	 */
	$templates_path = apply_filters( 'nucleus_vc_templates_path', NUCLEUS_TEMPLATE_DIR . '/vc-templates' );

	// Set path to directory where Visual Composer
	// should look for template files for content elements.
	vc_set_shortcodes_templates_dir( $templates_path );
}

add_action( 'vc_before_init', 'nucleus_vc_before_init' );

/**
 * Customize some Visual Composer default shortcodes
 *
 * Also, this function could be defined in theme "core" plugin.
 */
function nucleus_vc_after_init() {
	/**#@+
	 * Translated strings for "heading" parameters, which used more than once
	 */
	$heading_icon = esc_html__( 'Icon', 'nucleus' );
	/**#@-*/

	/**#@+
	 * Translated strings for "group" parameters, which used more than once
	 */
	$group_general  = esc_html__( 'General', 'nucleus' );
	$group_overlay  = esc_html__( 'Overlay', 'nucleus' );
	$group_arrow    = esc_html__( 'Arrow', 'nucleus' );
	$group_angle    = esc_html__( 'Angle', 'nucleus' );
	$group_parallax = esc_html__( 'Parallax', 'nucleus' );
	/**#@-*/

	/**
	 * @var array List of theme default colors
	 */
	$value_colors = array(
		esc_html__( 'Default', 'nucleus' )   => 'default',
		esc_html__( 'Default 2', 'nucleus' ) => 'default-2',
		esc_html__( 'Primary', 'nucleus' )   => 'primary',
		esc_html__( 'Success', 'nucleus' )   => 'success',
		esc_html__( 'Info', 'nucleus' )      => 'info',
		esc_html__( 'Warning', 'nucleus' )   => 'warning',
		esc_html__( 'Danger', 'nucleus' )    => 'danger',
	);

	$value_enable_disable = array(
		esc_html__( 'Disable', 'nucleus' ) => 'disable',
		esc_html__( 'Enable', 'nucleus' )  => 'enable',
	);

	$value_no_yes = array(
		esc_html__( 'No', 'nucleus' )  => 'no',
		esc_html__( 'Yes', 'nucleus' ) => 'yes',
	);

	/**
	 * @var array List of Visual Composer's default colors
	 */
	$value_vc_colors = array(
		esc_html__( 'Grey', 'nucleus' )         => 'grey',
		esc_html__( 'White', 'nucleus' )        => 'white',
		esc_html__( 'Custom color', 'nucleus' ) => 'custom'
	);
	
	$description_el_id = wp_kses( __( 'Make sure Row ID is unique and valid according to <a href="http://www.w3schools.com/tags/att_global_id.asp" target="_blank">w3c specification</a>.', 'nucleus' ), array(
		'a' => array( 'href' => true, 'target' => true ),
	) );

	/*
	 * Remove unnecessary shortcodes
	 */
	vc_remove_element( 'vc_toggle' );
	vc_remove_element( 'vc_text_separator' );
	vc_remove_element( 'vc_posts_slider' );
	vc_remove_element( 'vc_gallery' );
	vc_remove_element( 'vc_images_carousel' );
	vc_remove_element( 'vc_basic_grid' );
	vc_remove_element( 'vc_media_grid' );
	vc_remove_element( 'vc_gmaps' );
	vc_remove_element( 'vc_btn' );
	vc_remove_element( 'vc_button' );
	vc_remove_element( 'vc_button2' );
	vc_remove_element( 'vc_cta_button' );
	vc_remove_element( 'vc_cta_button2' );
	vc_remove_element( 'vc_masonry_grid' );
	vc_remove_element( 'vc_masonry_media_grid' );
	vc_remove_element( 'vc_message' );
	vc_remove_element( 'vc_cta' );
	vc_remove_element( 'vc_tta_tour' );
	vc_remove_element( 'vc_tabs' );
	vc_remove_element( 'vc_tour' );
	vc_remove_element( 'vc_accordion' );
	vc_remove_element( 'vc_tta_pageable' );
	vc_remove_element( 'vc_custom_heading' );
	vc_remove_element( 'vc_progress_bar' );
	vc_remove_element( 'vc_pie' );
	vc_remove_element( 'vc_round_chart' );
	vc_remove_element( 'vc_line_chart' );
	vc_remove_element( 'vc_flickr' );

	/**
	 * Row | vc_row
	 */
	nucleus_vc_add_params( 'vc_row', array(
		array(
			'param_name'  => 'id',
			'type'        => 'el_id',
			'weight'      => 10,
			'group'       => $group_general,
			'heading'     => esc_html__( 'Row ID', 'nucleus' ),
			'description' => wp_kses( __( 'Make sure Row ID is unique and valid according to <a href="http://www.w3schools.com/tags/att_global_id.asp" target="_blank">w3c specification</a>.', 'nucleus' ), array(
				'a' => array( 'href' => true, 'target' => true ),
			) ),
		),
		array(
			'param_name'       => 'layout',
			'type'             => 'dropdown',
			'weight'           => 10,
			'heading'          => esc_html__( 'Content Layout', 'nucleus' ),
			'description'      => esc_html__( 'Choose the layout type', 'nucleus' ),
			'group'            => $group_general,
			'edit_field_class' => 'vc_column vc_col-sm-6',
			'value'            => array(
				esc_html__( 'Boxed', 'nucleus' )      => 'boxed',
				esc_html__( 'Full-width', 'nucleus' ) => 'full',
			),
		),
		array(
			'param_name'       => 'skin',
			'type'             => 'dropdown',
			'weight'           => 10,
			'heading'          => esc_html__( 'Predefined Color Skin', 'nucleus' ),
			'description'      => esc_html__( 'Also applies to arrows', 'nucleus' ),
			'group'            => $group_general,
			'edit_field_class' => 'vc_column vc_col-sm-6',
			'value'            => array(
				esc_html__( 'None', 'nucleus' )    => 'none',
				esc_html__( 'Default', 'nucleus' ) => 'default',
				esc_html__( 'Primary', 'nucleus' ) => 'primary',
				esc_html__( 'Gray', 'nucleus' )    => 'gray',
			),
		),
		array(
			'param_name'       => 'is_overlay',
			'type'             => 'dropdown',
			'weight'           => 10,
			'heading'          => esc_html__( 'Overlay', 'nucleus' ),
			'group'            => $group_general,
			'value'            => $value_enable_disable,
			'edit_field_class' => 'vc_column vc_col-sm-6',
		),
		array(
			'param_name'  => 'overlay_opacity',
			'type'        => 'textfield',
			'weight'      => 10,
			'group'       => $group_overlay,
			'heading'     => esc_html__( 'Opacity', 'nucleus' ),
			'description' => esc_html__( 'Enter value from 0 to 100%. Where 0 is fully transparent', 'nucleus' ),
			'value'       => 65,
			'dependency'  => array( 'element' => 'is_overlay', 'value' => 'enable' ),
		),
		array(
			'param_name' => 'overlay_color',
			'type'       => 'colorpicker',
			'weight'     => 10,
			'group'      => $group_overlay,
			'heading'    => esc_html__( 'Color', 'nucleus' ),
			'value'      => '#000000',
			'dependency' => array( 'element' => 'is_overlay', 'value' => 'enable' ),
		),
		array(
			'param_name'       => 'is_arrow',
			'type'             => 'dropdown',
			'heading'          => esc_html__( 'Arrow', 'nucleus' ),
			'weight'           => 10,
			'group'            => $group_general,
			'value'            => $value_enable_disable,
			'edit_field_class' => 'vc_column vc_col-sm-6',
		),
		array(
			'param_name' => 'arrow_position',
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Position', 'nucleus' ),
			'weight'     => 10,
			'group'      => $group_arrow,
			'dependency' => array( 'element' => 'is_arrow', 'value' => 'enable' ),
			'value'      => array(
				esc_html__( 'Bottom', 'nucleus' ) => 'bottom',
				esc_html__( 'Top', 'nucleus' )    => 'top',
			),
		),
		array(
			'param_name'       => 'is_angle',
			'type'             => 'dropdown',
			'heading'          => esc_html__( 'Angle', 'nucleus' ),
			'weight'           => 10,
			'group'            => $group_general,
			'value'            => $value_enable_disable,
			'edit_field_class' => 'vc_column vc_col-sm-6',
		),
		array(
			'param_name' => 'angle_position',
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Position', 'nucleus' ),
			'weight'     => 10,
			'group'      => $group_angle,
			'dependency' => array( 'element' => 'is_angle', 'value' => 'enable' ),
			'value'      => array(
				esc_html__( 'Bottom Right', 'nucleus' ) => 'bottom-right',
				esc_html__( 'Bottom Left', 'nucleus' )  => 'bottom-left',
				esc_html__( 'Top Left', 'nucleus' )     => 'top-left',
				esc_html__( 'Top Right', 'nucleus' )    => 'top-right',
			),
		),
		array(
			'param_name'       => 'is_parallax',
			'type'             => 'dropdown',
			'heading'          => esc_html__( 'Parallax', 'nucleus' ),
			'weight'           => 10,
			'group'            => $group_general,
			'value'            => $value_enable_disable,
			'edit_field_class' => 'vc_column vc_col-sm-6',
		),
		array(
			'param_name'  => 'parallax_speed',
			'type'        => 'textfield',
			'weight'      => 10,
			'group'       => $group_parallax,
			'heading'     => esc_html__( 'Speed', 'nucleus' ),
			'description' => esc_html__( 'Accepts integers or floats from 0 to 1', 'nucleus' ),
			'value'       => '0.65',
			'dependency'  => array( 'element' => 'is_parallax', 'value' => 'enable' ),
		),
		/*array(
			'param_name'  => 'parallax_offset',
			'type'        => 'textfield',
			'weight'      => 10,
			'group'       => $group_parallax,
			'heading'     => esc_html__( 'Offset', 'nucleus' ),
			'description' => esc_html__( 'Accepts only integer numbers, can be negative', 'nucleus' ),
			'dependency'  => array( 'element' => 'is_parallax', 'value' => 'enable' ),
		),*/
		array(
			'param_name' => 'no_padding',
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Remove Padding?', 'nucleus' ),
			'weight'     => 10,
			'group'      => $group_general,
			'value'      => $value_no_yes,
		),
		array(
			'param_name'  => 'class',
			'type'        => 'textfield',
			'weight'      => - 1,
			'group'       => $group_general,
			'heading'     => esc_html__( 'Extra class name', 'nucleus' ),
			'description' => esc_html__( 'Style particular content element differently', 'nucleus' ),
		),
		array(
			'param_name' => 'css',
			'type'       => 'css_editor',
			'group'      => esc_html__( 'Design Options', 'nucleus' ),
			'weight'     => 10,
			'heading'    => esc_html__( 'CSS', 'nucleus' ),
		),
	) );

	/**
	 * Inner Row | vc_row_inner
	 */
	nucleus_vc_add_params( 'vc_row_inner', array(
		array(
			'param_name'  => 'id',
			'type'        => 'el_id',
			'weight'      => 10,
			'group'       => $group_general,
			'heading'     => esc_html__( 'Row ID', 'nucleus' ),
			'description' => $description_el_id,
		),
		array(
			'param_name'  => 'class',
			'type'        => 'textfield',
			'weight'      => -1,
			'group'       => $group_general,
			'heading'     => esc_html__( 'Extra class name', 'nucleus' ),
			'description' => esc_html__( 'Style particular content element differently', 'nucleus' ),
		),
		array(
			'param_name' => 'css',
			'type'       => 'css_editor',
			'group'      => esc_html__( 'Design Options', 'nucleus' ),
			'weight'     => 10,
			'heading'    => esc_html__( 'CSS', 'nucleus' ),
		),
	) );

	/**
	 * Accordion | vc_tta_accordion
	 */
	nucleus_vc_add_params( 'vc_tta_accordion', array(
		array(
			'param_name'  => 'el_class',
			'type'        => 'textfield',
			'weight'      => 10,
			'group'       => $group_general,
			'heading'     => esc_html__( 'Extra class name', 'nucleus' ),
			'description' => esc_html__( 'Style particular content element differently', 'nucleus' ),
		),
		array(
			'param_name' => 'css',
			'type'       => 'css_editor',
			'group'      => esc_html__( 'Design Options', 'nucleus' ),
			'weight'     => 10,
			'heading'    => esc_html__( 'CSS', 'nucleus' ),
		),
	) );

	/**
	 * Tabs | vc_tta_tabs
	 */
	nucleus_vc_add_params( 'vc_tta_tabs', array(
		array(
			'param_name'  => 'alignment',
			'type'        => 'dropdown',
			'weight'      => 10,
			'heading'     => esc_html__( 'Alignment', 'nucleus' ),
			'description' => esc_html__( 'Select tabs section title alignment', 'nucleus' ),
			'value'       => array(
				esc_html__( 'Left', 'nucleus' )   => 'left',
				esc_html__( 'Center', 'nucleus' ) => 'center',
				esc_html__( 'Right', 'nucleus' )  => 'right',
			),
		),
		array(
			'param_name'  => 'el_class',
			'type'        => 'textfield',
			'weight'      => 10,
			'heading'     => esc_html__( 'Extra class name', 'nucleus' ),
			'description' => esc_html__( 'Style particular content element differently', 'nucleus' ),
		),
		array(
			'param_name' => 'css',
			'type'       => 'css_editor',
			'group'      => esc_html__( 'Design Options', 'nucleus' ),
			'weight'     => 10,
			'heading'    => esc_html__( 'CSS', 'nucleus' ),
		),
	) );

	nucleus_vc_add_params( 'vc_tta_section', array(
		array(
			'param_name'  => 'title',
			'type'        => 'textfield',
			'weight'      => 10,
			'heading'     => esc_html__( 'Title', 'nucleus' ),
			'description' => esc_html__( 'Enter section title', 'nucleus' ),
		),
		array(
			'param_name'  => 'tab_id',
			'type'        => 'el_id',
			'weight'      => 10,
			'settings'    => array( 'auto_generate' => true ),
			'heading'     => esc_html__( 'Section ID', 'nucleus' ),
			'description' => $description_el_id,
		),
		array(
			'param_name'  => 'animation',
			'type'        => 'dropdown',
			'weight'      => 10,
			'heading'     => esc_html__( 'Animation', 'nucleus' ),
			'description' => esc_html__( 'Applicable only for tabs', 'nucleus' ),
			'std'         => 'fade',
			'value'       => array(
				esc_html__( 'Fade', 'nucleus' )       => 'fade',
				esc_html__( 'Scale', 'nucleus' )      => 'scale',
				esc_html__( 'Scale Down', 'nucleus' ) => 'scaledown',
				esc_html__( 'Left', 'nucleus' )       => 'left',
				esc_html__( 'Right', 'nucleus' )      => 'right',
				esc_html__( 'Top', 'nucleus' )        => 'top',
				esc_html__( 'Bottom', 'nucleus' )     => 'bottom',
				esc_html__( 'Flip', 'nucleus' )       => 'flip',
			),
		),
		array(
			'param_name'  => 'el_class',
			'type'        => 'textfield',
			'weight'      => 0,
			'heading'     => esc_html__( 'Extra class name', 'nucleus' ),
			'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nucleus' ),
		),
	) );
}

if ( is_admin() ) {
	add_action( 'vc_after_init', 'nucleus_vc_after_init' );
}

/**
 * Remove VC Welcome Page
 */
remove_action( 'vc_activation_hook', 'vc_page_welcome_set_redirect' );
remove_action( 'admin_init', 'vc_page_welcome_redirect' );

/**
 * Wrapper for {@see vc_add_params()}
 *
 * This function will replace default params
 *
 * @param string $tag    Shortcode tag
 * @param array  $params Shortcode params
 */
function nucleus_vc_add_params( $tag, $params ) {
	nucleus_vc_reset_params( $tag );
	vc_add_params( $tag, $params );
}

/**
 * Remove all VC shortcode params
 *
 * @param string $tag Shortcode tag
 */
function nucleus_vc_reset_params( $tag ) {
	$shortcode = vc_get_shortcode( $tag );
	if ( ! is_array( $shortcode ) || ! is_array( $shortcode['params'] ) || empty( $shortcode['params'] ) ) {
		return;
	}

	foreach ( $shortcode['params'] as $param ) {
		if ( ! isset( $param['param_name'] ) ) {
			continue;
		}

		vc_remove_param( $tag, $param['param_name'] );
	}
}