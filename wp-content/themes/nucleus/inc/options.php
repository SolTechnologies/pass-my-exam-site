<?php
/**
 * Theme Options
 *
 * @author  8guild
 * @package Nucleus
 */

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Theme Options layout
 */
function nucleus_add_theme_options() {

	// check if custom logo exists, WP 4.5+
	$is_logo = function_exists( 'the_custom_logo' );

	$spinner_preview = NUCLEUS_TEMPLATE_URI . '/img/options/spinners/';
	$header_preview  = NUCLEUS_TEMPLATE_URI . '/img/options/headers/';
	$layouts_preview = NUCLEUS_TEMPLATE_URI . '/img/options/layouts/';
	$footer_preview  = NUCLEUS_TEMPLATE_URI . '/img/options/footer/';

	$translated = array(
		'enable'         => esc_html__( 'Enable', 'nucleus' ),
		'disable'        => esc_html__( 'Disable', 'nucleus' ),
		'icon'           => esc_html__( 'Icon', 'nucleus' ),
		'title'          => esc_html__( 'Title', 'nucleus' ),
		'url'            => esc_html__( 'URL', 'nucleus' ),
		'font_size'      => esc_html__( 'Font Size', 'nucleus' ),
		'font_weight'    => esc_html__( 'Font Weight', 'nucleus' ),
		'text_transform' => esc_html__( 'Text Transform', 'nucleus' ),
		'font_style'     => esc_html__( 'Font Style', 'nucleus' ),
		'body'           => esc_html__( 'Body', 'nucleus' ),
		'global'         => esc_html__( 'Global', 'nucleus' ),
	);

	// data used more than once
	$options_sidebars = array(
		'right-sidebar' => array(
			'src'   => $layouts_preview . 'layout01.png',
			'label' => esc_html__( 'Right Sidebar', 'nucleus' ),
		),
		'left-sidebar' => array(
			'src'   => $layouts_preview . 'layout02.png',
			'label' => esc_html__( 'Left Sidebar', 'nucleus' ),
		),
		'no-sidebar' => array(
			'src'   => $layouts_preview . 'layout03.png',
			'label' => esc_html__( 'No Sidebar', 'nucleus' ),
		),
	);

	$options_font_weight = array(
		'lighter' => 'Lighter',
		'normal'  => 'Normal',
		'bold'    => 'Bold',
		'bolder'  => 'Bolder',
		'100'     => '100',
		'200'     => '200',
		'300'     => '300',
		'400'     => '400',
		'500'     => '500',
		'600'     => '600',
		'700'     => '700',
		'800'     => '800',
		'900'     => '900',
	);

	$options_text_transform = array(
		'none'       => 'None',
		'capitalize' => 'Capitalize',
		'lowercase'  => 'Lowercase',
		'uppercase'  => 'Uppercase'
	);

	$options_font_style = array(
		'normal'  => 'Normal',
		'italic'  => 'Italic',
		'oblique' => 'Oblique',
	);

	// start building the layout
	try {
		$layout = equip_create_options_layout();

		//<editor-fold desc="Global Section">
		$layout
			->add_section( 'global', $translated['global'], array(
				'icon'      => 'material-icons public',
				'is_active' => true,
			) )
			->add_row()
			->add_column( 3 )
			->add_field( 'global_custom_logo', 'raw_text', array(
				'label'   => esc_html__( 'Logo', 'nucleus' ),
				'default' => esc_html__( 'Please upload your logo in Appearance > Customize > Site Identity > Logo', 'nucleus' ),
				'hidden'  => ! $is_logo,
			) )
			->add_field( 'global_logo', 'media', array(
				'label'  => esc_html__( 'Logo', 'nucleus' ),
				'hidden' => $is_logo,
			) )
			->add_column( 6 )
			->add_field( 'global_logo_width', 'slider', array(
				'label'       => esc_html__( 'Logo Width', 'nucleus' ),
				'description' => esc_html__( 'Affects both logo in footer and header', 'nucleus' ),
				'min'         => 30,
				'max'         => 200,
				'default'     => 138,
				'sass'        => array( 'var' => 'logo-width', 'append' => 'px' ),
			) )
			->parent( 'section' )
			->add_row()
			->add_column( 3 )
			->add_field( 'global_is_scroll_to_top', 'switch', array(
				'label'       => esc_html__( 'Scroll to Top', 'nucleus' ),
				'description' => esc_html__( 'Enable or Disable the "Scroll to Top" button', 'nucleus' ),
				'default'     => true,
				'label_on'    => $translated['enable'],
				'label_off'   => $translated['disable'],
			) )
			->add_column( 9 )
			->add_field( 'global_is_page_title', 'switch', array(
				'label'       => esc_html__( 'Page Title', 'nucleus' ),
				'description' => esc_html__( 'Enable or Disable the page title globally. Please note, breadcrumbs are disabled, too', 'nucleus' ),
				'default'     => true,
				'label_on'    => $translated['enable'],
				'label_off'   => $translated['disable'],
			) )
			->parent( 'section' )
			->add_row()
			->add_column( 3 )
			->add_field( 'global_is_preloader', 'switch', array(
				'label'       => esc_html__( 'Preloader', 'nucleus' ),
				'description' => esc_html__( 'Enable or Disable the preloader', 'nucleus' ),
				'default'     => true,
				'label_on'    => $translated['enable'],
				'label_off'   => $translated['disable'],
			) )
			->add_column( 3 )
			->add_field( 'global_preloader_bg', 'color', array(
				'label'       => esc_html__( 'Screen Background', 'nucleus' ),
				'description' => esc_html__( 'Screen background color for preloader', 'nucleus' ),
				'default'     => '#ffffff',
				'required'    => array( 'global_is_preloader', '=', 1 ),
			) )
			->add_column( 3 )
			->add_field( 'global_preloader_skin', 'select', array(
				'label'       => esc_html__( 'Skin', 'nucleus' ),
				'description' => esc_html__( 'Choose the preloader skin', 'nucleus' ),
				'default'     => 'dark',
				'required'    => array( 'global_is_preloader', '=', 1 ),
				'options'     => array(
					'dark'  => esc_html__( 'Dark', 'nucleus' ),
					'light' => esc_html__( 'Light', 'nucleus' ),
				),
			) )
			->parent( 'section' )
			->add_field( 'global_preloader_spinner', 'image_select', array(
				'label'       => esc_html__( 'Preloader Spinner', 'nucleus' ),
				'helper' 			=> esc_html__( 'Choose the spinner type', 'nucleus' ),
				'default'     => 'spinner1',
				'width'       => 140,
				'height'      => 105,
				'required'    => array( 'global_is_preloader', '=', 1 ),
				'options'     => array(
					'spinner1' => array(
						'src'   => $spinner_preview . 'spinner01.png',
						'label' => esc_html__( 'Spinner 1', 'nucleus' ),
					),
					'spinner2' => array(
						'src'   => $spinner_preview . 'spinner02.png',
						'label' => esc_html__( 'Spinner 2', 'nucleus' ),
					),
					'spinner3' => array(
						'src'   => $spinner_preview . 'spinner03.png',
						'label' => esc_html__( 'Spinner 3', 'nucleus' ),
					),
					'spinner4' => array(
						'src'   => $spinner_preview . 'spinner04.png',
						'label' => esc_html__( 'Spinner 4', 'nucleus' ),
					),
					'spinner5' => array(
						'src'   => $spinner_preview . 'spinner05.png',
						'label' => esc_html__( 'Spinner 5', 'nucleus' ),
					),
					'spinner6' => array(
						'src'   => $spinner_preview . 'spinner06.png',
						'label' => esc_html__( 'Spinner 6', 'nucleus' ),
					),
					'spinner7' => array(
						'src'   => $spinner_preview . 'spinner07.png',
						'label' => esc_html__( 'Spinner 7', 'nucleus' ),
					),
				),
			) )
			->parent( 'section' )
			->add_row()
			->add_column( 3 )
			->add_field( 'global_is_social_bar', 'switch', array(
				'label'       => esc_html__( 'Social Bar', 'nucleus' ),
				'description' => esc_html__( 'Enable or Disable social bar', 'nucleus' ),
				'default'     => false,
				'label_on'    => $translated['enable'],
				'label_off'   => $translated['disable'],
			) )
			->add_column( 3 )
			->add_field( 'global_social_bar_pos', 'select', array(
				'label'       => esc_html__( 'Position', 'nucleus' ),
				'description' => esc_html__( 'Choose the position of the Social Bar', 'nucleus' ),
				'default'     => 'right',
				'required'    => array( 'global_is_social_bar', '=', 1 ),
				'options'     => array(
					'left'  => esc_html__( 'Left', 'nucleus' ),
					'right' => esc_html__( 'Right', 'nucleus' ),
				),
			) )
			->parent( 'section' )
			->add_field( 'global_social_bar_socials', 'socials', array(
				'label'       => esc_html__( 'Social Networks', 'nucleus' ),
				'description' => esc_html__( 'Choose the socials network for the Social Bar', 'nucleus' ),
				'required'    => array( 'global_is_social_bar', '=', 1 ),
			) );
		//</editor-fold>

		//<editor-fold desc="Typography Section">
		$layout
			->add_section( 'typography', esc_html__( 'Typography', 'nucleus' ), array(
				'icon' => 'material-icons format_size',
			) )
			->add_anchor( 'typography-google-fonts-anchor', esc_html__( 'Google Fonts', 'nucleus' ) )
			->add_row()
			->add_column( 3 )
			->add_field( 'typography_is_google_fonts', 'switch', array(
				'label'     => esc_html__( 'Enable Google Fonts?', 'nucleus' ),
				'default'   => true,
				'label_on'  => $translated['enable'],
				'label_off' => $translated['disable'],
			) )
			->add_column( 5 )
			->add_field( 'typography_font_for_body', 'text', array(
				'label'       => esc_html__( 'Font for Body', 'nucleus' ),
				'description' => wp_kses( 'Go to <a href="https://www.google.com/fonts" target="_blank">google.com/fonts</a>, click "Quick-use" button and follow the instructions. From step 3 copy the "href" value and paste in field above.', array(
					'a' => array( 'href' => true, 'target' => true ),
				) ),
				'default'     => '//fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,600,600italic,700',
				'required'    => array( 'typography_is_google_fonts', '=', true ),
			) )
			->add_column( 4 )
			->add_field( 'typography_font_for_headings', 'text', array(
				'label'       => esc_html__( 'Font for Headings', 'nucleus' ),
				'description' => esc_html__( 'If empty inherits from Body google link.', 'nucleus' ),
				'default'     => '',
				'required'    => array( 'typography_is_google_fonts', '=', true ),
			) )
			->add_anchor( 'typography-font-family-anchor', esc_html__( 'Font Family', 'nucleus' ) )
			->add_row()
			->add_column( 4 )
			->add_field( 'typography_ff_text', 'raw_text', array(
				'default' => esc_html__( 'Put chosen google font (do not forget about quotation marks) along with fallback fonts, separated by comma. ', 'nucleus' ),
			) )
			->add_column( 4 )
			->add_field( 'typography_ff_body', 'text', array(
				'label'   => esc_html__( 'Body', 'nucleus' ),
				'default' => '"Source Sans Pro", Helvetica, Arial, sans-serif',
				'sass'    => 'font-family-base',
			) )
			->add_column( 4 )
			->add_field( 'typography_ff_headings', 'text', array(
				'label'   => esc_html__( 'Headings', 'nucleus' ),
				'default' => '',
				'sass'    => 'headings-font-family',
			) )
			->add_anchor( 'typography-font-size-anchor', esc_html__( 'Font Size', 'nucleus' ) )
			->add_row()
			->add_column( 4 )
			->add_field( 'typography_fs_text', 'raw_text', array(
				'default' => esc_html__( 'Set the global font sizes for body and formats.', 'nucleus' ),
				'attr'    => array(
					'style' => 'padding-top:20px;',
				),
			) )
			->add_column( 4 )
			->add_field( 'typography_fs_body', 'slider', array(
				'label'   => $translated['body'],
				'min'     => 0,
				'max'     => 100,
				'default' => 16,
				'sass'    => array( 'var' => 'font-size-base', 'append' => 'px' ),
			) )
			->add_column( 4 )
			->add_field( 'typography_fs_lead', 'slider', array(
				'label'   => esc_html__( 'Lead', 'nucleus' ),
				'min'     => 0,
				'max'     => 100,
				'default' => 20,
				'sass'    => array( 'var' => 'font-size-lead', 'append' => 'px' ),
			) )
			->add_row()
			->add_column( 4 )
			->add_field( 'typography_fs_lg', 'slider', array(
				'label'   => esc_html__( 'Large', 'nucleus' ),
				'min'     => 0,
				'max'     => 100,
				'default' => 18,
				'sass'    => array( 'var' => 'font-size-large', 'append' => 'px' ),
			) )
			->add_column( 4 )
			->add_field( 'typography_fs_sm', 'slider', array(
				'label'   => esc_html__( 'Small', 'nucleus' ),
				'min'     => 0,
				'max'     => 100,
				'default' => 14,
				'sass'    => array( 'var' => 'font-size-small', 'append' => 'px' ),
			) )
			->add_column( 4 )
			->add_field( 'typography_fs_xs', 'slider', array(
				'label'   => esc_html__( 'Extra Small', 'nucleus' ),
				'min'     => 0,
				'max'     => 100,
				'default' => 12,
				'sass'    => array( 'var' => 'font-size-xs', 'append' => 'px' ),
			) )
			->add_anchor( 'typography-h1-anchor', esc_html__( 'Heading 1 (H1)', 'nucleus' ) )
			->add_row()
			->add_column( 3 )
			->add_field( 'typography_h1_font_size', 'slider', array(
				'label'   => $translated['font_size'],
				'min'     => 0,
				'max'     => 100,
				'default' => 56,
				'sass'    => array( 'var' => 'font-size-h1', 'append' => 'px' ),
			) )
			->add_column( 3 )
			->add_field( 'typography_h1_font_weight', 'select', array(
				'label'   => $translated['font_weight'],
				'default' => '600',
				'sass'    => 'font-weight-h1',
				'options' => $options_font_weight,
			) )
			->add_column( 3 )
			->add_field( 'typography_h1_text_transform', 'select', array(
				'label'   => $translated['text_transform'],
				'default' => 'none',
				'sass'    => 'text-transform-h1',
				'options' => $options_text_transform,
			) )
			->add_column( 3 )
			->add_field( 'typography_h1_font_style', 'select', array(
				'label'   => $translated['font_style'],
				'default' => 'normal',
				'sass'    => 'font-style-h1',
				'options' => $options_font_style,
			) )
			->add_anchor( 'typography-h2-anchor', esc_html__( 'Heading 2 (H2)', 'nucleus' ) )
			->add_row()
			->add_column( 3 )
			->add_field( 'typography_h2_font_size', 'slider', array(
				'label'   => $translated['font_size'],
				'min'     => 0,
				'max'     => 100,
				'default' => 48,
				'sass'    => array( 'var' => 'font-size-h2', 'append' => 'px' ),
			) )
			->add_column( 3 )
			->add_field( 'typography_h2_font_weight', 'select', array(
				'label'   => $translated['font_weight'],
				'default' => '600',
				'sass'    => 'font-weight-h2',
				'options' => $options_font_weight,
			) )
			->add_column( 3 )
			->add_field( 'typography_h2_text_transform', 'select', array(
				'label'   => $translated['text_transform'],
				'default' => 'none',
				'sass'    => 'text-transform-h2',
				'options' => $options_text_transform,
			) )
			->add_column( 3 )
			->add_field( 'typography_h2_font_style', 'select', array(
				'label'   => $translated['font_style'],
				'default' => 'normal',
				'sass'    => 'font-style-h2',
				'options' => $options_font_style,
			) )
			->add_anchor( 'typography-h3-anchor', esc_html__( 'Heading 3 (H3)', 'nucleus' ) )
			->add_row()
			->add_column( 3 )
			->add_field( 'typography_h3_font_size', 'slider', array(
				'label'   => $translated['font_size'],
				'min'     => 0,
				'max'     => 100,
				'default' => 36,
				'sass'    => array( 'var' => 'font-size-h3', 'append' => 'px' ),
			) )
			->add_column( 3 )
			->add_field( 'typography_h3_font_weight', 'select', array(
				'label'   => $translated['font_weight'],
				'default' => '600',
				'sass'    => 'font-weight-h3',
				'options' => $options_font_weight,
			) )
			->add_column( 3 )
			->add_field( 'typography_h3_text_transform', 'select', array(
				'label'   => $translated['text_transform'],
				'default' => 'none',
				'sass'    => 'text-transform-h3',
				'options' => $options_text_transform,
			) )
			->add_column( 3 )
			->add_field( 'typography_h3_font_style', 'select', array(
				'label'   => $translated['font_style'],
				'default' => 'normal',
				'sass'    => 'font-style-h3',
				'options' => $options_font_style,
			) )
			->add_anchor( 'typography-h4-anchor', esc_html__( 'Heading 4 (H4)', 'nucleus' ) )
			->add_row()
			->add_column( 3 )
			->add_field( 'typography_h4_font_size', 'slider', array(
				'label'   => $translated['font_size'],
				'min'     => 0,
				'max'     => 100,
				'default' => 24,
				'sass'    => array( 'var' => 'font-size-h4', 'append' => 'px' ),
			) )
			->add_column( 3 )
			->add_field( 'typography_h4_font_weight', 'select', array(
				'label'   => $translated['font_weight'],
				'default' => '600',
				'sass'    => 'font-weight-h4',
				'options' => $options_font_weight,
			) )
			->add_column( 3 )
			->add_field( 'typography_h4_text_transform', 'select', array(
				'label'   => $translated['text_transform'],
				'default' => 'none',
				'sass'    => 'text-transform-h4',
				'options' => $options_text_transform,
			) )
			->add_column( 3 )
			->add_field( 'typography_h4_font_style', 'select', array(
				'label'   => $translated['font_style'],
				'default' => 'normal',
				'sass'    => 'font-style-h4',
				'options' => $options_font_style,
			) )
			->add_anchor( 'typography-h5-anchor', esc_html__( 'Heading 5 (H5)', 'nucleus' ) )
			->add_row()
			->add_column( 3 )
			->add_field( 'typography_h5_font_size', 'slider', array(
				'label'   => $translated['font_size'],
				'min'     => 0,
				'max'     => 100,
				'default' => 18,
				'sass'    => array( 'var' => 'font-size-h5', 'append' => 'px' ),
			) )
			->add_column( 3 )
			->add_field( 'typography_h5_font_weight', 'select', array(
				'label'   => $translated['font_weight'],
				'default' => '600',
				'sass'    => 'font-weight-h5',
				'options' => $options_font_weight,
			) )
			->add_column( 3 )
			->add_field( 'typography_h5_text_transform', 'select', array(
				'label'   => $translated['text_transform'],
				'default' => 'none',
				'sass'    => 'text-transform-h5',
				'options' => $options_text_transform,
			) )
			->add_column( 3 )
			->add_field( 'typography_h5_font_style', 'select', array(
				'label'   => $translated['font_style'],
				'default' => 'normal',
				'sass'    => 'font-style-h5',
				'options' => $options_font_style,
			) )
			->add_anchor( 'typography-h6-anchor', esc_html__( 'Heading 6 (H6)', 'nucleus' ) )
			->add_row()
			->add_column( 3 )
			->add_field( 'typography_h6_font_size', 'slider', array(
				'label'   => $translated['font_size'],
				'min'     => 0,
				'max'     => 100,
				'default' => 16,
				'sass'    => array( 'var' => 'font-size-h6', 'append' => 'px' ),
			) )
			->add_column( 3 )
			->add_field( 'typography_h6_font_weight', 'select', array(
				'label'   => $translated['font_weight'],
				'default' => '600',
				'sass'    => 'font-weight-h6',
				'options' => $options_font_weight,
			) )
			->add_column( 3 )
			->add_field( 'typography_h6_text_transform', 'select', array(
				'label'   => $translated['text_transform'],
				'default' => 'none',
				'sass'    => 'text-transform-h6',
				'options' => $options_text_transform,
			) )
			->add_column( 3 )
			->add_field( 'typography_h6_font_style', 'select', array(
				'label'   => $translated['font_style'],
				'default' => 'normal',
				'sass'    => 'font-style-h6',
				'options' => $options_font_style,
			) )
			->add_anchor( 'typography-quotation-anchor', esc_html__( 'Quotation', 'nucleus' ) )
			->add_row()
			->add_column( 3 )
			->add_field( 'typography_quote_font_size', 'slider', array(
				'label'   => $translated['font_size'],
				'min'     => 0,
				'max'     => 100,
				'default' => 20,
				'sass'    => array( 'var' => 'quote-font-size', 'append' => 'px' ),
			) )
			->add_column( 3 )
			->add_field( 'typography_quote_font_weight', 'select', array(
				'label'   => $translated['font_weight'],
				'default' => '600',
				'sass'    => 'quote-font-weight',
				'options' => $options_font_weight,
			) )
			->add_column( 3 )
			->add_field( 'typography_quote_text_transform', 'select', array(
				'label'   => $translated['text_transform'],
				'default' => 'none',
				'sass'    => 'quote-text-transform',
				'options' => $options_text_transform,
			) )
			->add_column( 3 )
			->add_field( 'typography_quote_font_style', 'select', array(
				'label'   => $translated['font_style'],
				'default' => 'italic',
				'sass'    => 'quote-font-style',
				'options' => $options_font_style,
			) )
			;
		//</editor-fold>

		//<editor-fold desc="Colors Section">
		$layout
			->add_section( 'color', esc_html__( 'Colors', 'nucleus' ), array(
				'icon' => 'material-icons invert_colors',
			) )
			->add_anchor( 'color-global-anchor', $translated['global'] )
			->add_row()
			->add_column( 3 )
			->add_field( 'color_body_bg', 'color', array(
				'label'   => esc_html__( 'Body Background', 'nucleus' ),
				'default' => '#ffffff',
				'sass'    => 'body-bg',
			) )
			->add_column( 3 )
			->add_field( 'color_body', 'color', array(
				'label'   => esc_html__( 'Body Font', 'nucleus' ),
				'default' => '#000000',
				'sass'    => 'text-color',
			) )
			->add_column( 3 )
			->add_field( 'color_headings', 'color', array(
				'label'   => esc_html__( 'Headings', 'nucleus' ),
				'default' => '#333333',
				'sass'    => 'headings-color',
			) )
			->add_anchor( 'color-grayscale', esc_html__( 'Grayscale', 'nucleus' ) )
			->add_row()
			->add_column( 3 )
			->add_field( 'color_grayscale_text', 'raw_text', array(
				'default' => esc_html__( 'Customize the grayscale', 'nucleus' ),
			) )
			->add_column( 3 )
			->add_field( 'color_gray_darker', 'color', array(
				'label'   => esc_html__( 'Gray Darker', 'nucleus' ),
				'default' => '#000000',
				'sass'    => 'gray-darker',
			) )
			->add_column( 3 )
			->add_field( 'color_gray_dark', 'color', array(
				'label'   => esc_html__( 'Gray Dark', 'nucleus' ),
				'default' => '#333333',
				'sass'    => 'gray-dark',
			) )
			->add_row()
			->add_column( 3 )
			->add_field( 'color_gray', 'color', array(
				'label'   => esc_html__( 'Gray', 'nucleus' ),
				'default' => '#666666',
				'sass'    => 'gray',
			) )
			->add_column( 3 )
			->add_field( 'color_gray_light', 'color', array(
				'label'   => esc_html__( 'Gray Light', 'nucleus' ),
				'default' => '#808080',
				'sass'    => 'gray-light',
			) )
			->add_column( 3 )
			->add_field( 'color_gray_lighter', 'color', array(
				'label'   => esc_html__( 'Gray Lighter', 'nucleus' ),
				'default' => '#d9d9d9',
				'sass'    => 'gray-lighter',
			) )
			->add_anchor( 'color-brand', esc_html__( 'Brand', 'nucleus' ) )
			->add_row()
			->add_column( 3 )
			->add_field( 'color_default', 'color', array(
				'label'   => esc_html__( 'Default', 'nucleus' ),
				'default' => '#2895f1',
				'sass'    => 'brand-default',
			) )
			->add_column( 3 )
			->add_field( 'color_primary', 'color', array(
				'label'   => esc_html__( 'Primary', 'nucleus' ),
				'default' => '#1bdb68',
				'sass'    => 'brand-primary',
			) )
			->add_column( 3 )
			->add_field( 'color_info', 'color', array(
				'label'   => esc_html__( 'Info', 'nucleus' ),
				'default' => '#62b7ff',
				'sass'    => 'brand-info',
			) )
			->add_row()
			->add_column( 3 )
			->add_field( 'color_success', 'color', array(
				'label'   => esc_html__( 'Success', 'nucleus' ),
				'default' => '#19c55e',
				'sass'    => 'brand-success',
			) )
			->add_column( 3 )
			->add_field( 'color_warning', 'color', array(
				'label'   => esc_html__( 'Warning', 'nucleus' ),
				'default' => '#d2a052',
				'sass'    => 'brand-warning',
			) )
			->add_column( 3 )
			->add_field( 'color_danger', 'color', array(
				'label'   => esc_html__( 'Danger', 'nucleus' ),
				'default' => '#e97961',
				'sass'    => 'brand-danger',
			) )
			->add_anchor( 'color-backgrounds', esc_html__( 'Background', 'nucleus' ) )
			->add_row()
			->add_column( 3 )
			->add_field( 'color_bg_default', 'color', array(
				'label'   => esc_html__( 'Default', 'nucleus' ),
				'default' => '#e7f3fd',
				'sass'    => 'default-bg',
			) )
			->add_column( 3 )
			->add_field( 'color_bg_primary', 'color', array(
				'label'   => esc_html__( 'Primary', 'nucleus' ),
				'default' => '#ddfae8',
				'sass'    => 'primary-bg',
			) )
			->add_column( 3 )
			->add_field( 'color_bg_gray', 'color', array(
				'label'   => esc_html__( 'Gray', 'nucleus' ),
				'default' => '#f6f6f6',
				'sass'    => 'gray-bg',
			) );
		//</editor-fold>

		//<editor-fold desc="Header Section">
		$layout
			->add_section( 'header', esc_html__( 'Header', 'nucleus' ), array(
				'icon' => 'material-icons payment',
			) )
			->add_field( 'header_layout', 'image_select', array(
				'label'       => esc_html__( 'Layout', 'nucleus' ),
				'helper' 			=> esc_html__( 'Choose the boxed of fullwidth variant, you can override this option individually for every page', 'nucleus' ),
				'default'     => 'boxed',
				'width'       => 350,
				'height'      => 150,
				'options'     => array(
					'boxed'     => array(
						'src'   => $header_preview . 'header01.png',
						'label' => esc_html__( 'Boxed', 'nucleus' ),
					),
					'fullwidth' => array(
						'src'   => $header_preview . 'header02.png',
						'label' => esc_html__( 'Fullwidth', 'nucleus' ),
					),
				),
			) )
			->add_field( 'header_is_sticky', 'switch', array(
				'label'       => esc_html__( 'Make the header sticky?', 'nucleus' ),
				'description' => esc_html__( 'You can override this option individually for every page', 'nucleus' ),
				'default'     => false,
				'label_on'    => esc_html__( 'Yes', 'nucleus' ),
				'label_off'   => esc_html__( 'No', 'nucleus' ),
			) )
			->add_field( 'header_is_search', 'switch', array(
				'label'       => esc_html__( 'Search', 'nucleus' ),
				'description' => esc_html__( 'Enable or disable the search form in header. You can override this option individually for every page.', 'nucleus' ),
				'default'     => true,
				'label_on'    => $translated['enable'],
				'label_off'   => $translated['disable'],
			) )
			->add_field( 'header_is_signup_login', 'switch', array(
				'label'       => esc_html__( 'Sign up / Log in Buttons', 'nucleus' ),
				'description' => esc_html__( 'Enable or disable the sign up / log in buttons in header. You can override this option individually for every page.', 'nucleus' ),
				'default'     => true,
				'label_on'    => $translated['enable'],
				'label_off'   => $translated['disable'],
			) );
		//</editor-fold>

		//<editor-fold desc="Layouts Section">
		$layout
			->add_section( 'layout', esc_html__( 'Layouts', 'nucleus' ), array(
				'icon' => 'material-icons dashboard',
			) )
			->add_field( 'layout_blog', 'image_select', array(
				'label'   => esc_html__( 'Blog Layout', 'nucleus' ),
				'default' => 'right-sidebar',
				'width'   => 200,
				'height'  => 150,
				'options' => $options_sidebars,
			) )
			->add_field( 'layout_shop', 'image_select', array(
				'label'   => esc_html__( 'Shop Layout', 'nucleus' ),
				'default' => 'no-sidebar',
				'width'   => 200,
				'height'  => 150,
				'options' => $options_sidebars,
			) )
			->add_field( 'layout_search', 'image_select', array(
				'label'   => esc_html__( 'Search Layout', 'nucleus' ),
				'default' => 'no-sidebar',
				'width'   => 200,
				'height'  => 150,
				'options' => $options_sidebars,
			) )
			->add_field( 'layout_archive', 'image_select', array(
				'label'   => esc_html__( 'Archive Layout', 'nucleus' ),
				'default' => 'no-sidebar',
				'width'   => 200,
				'height'  => 150,
				'options' => $options_sidebars,
			) );
		//</editor-fold>

		//<editor-fold desc="Footer Section">
		$layout
			->add_section( 'footer', esc_html__( 'Footer', 'nucleus' ), array(
				'icon' => 'material-icons call_to_action',
			) )
			->add_field( 'footer_layout', 'image_select', array(
				'label'       => esc_html__( 'Layout', 'nucleus' ),
				'helper'			=> esc_html__( 'Choose the number of sidebars in footer', 'nucleus' ),
				'default'     => 4,
				'width'       => 200,
				'height'      => 150,
				'options'     => array(
					1 => array(
						'src'   => $footer_preview . 'footer01.png',
						'label' => esc_html__( 'One Column', 'nucleus' ),
					),
					2 => array(
						'src'   => $footer_preview . 'footer02.png',
						'label' => esc_html__( 'Two Columns', 'nucleus' ),
					),
					3 => array(
						'src'   => $footer_preview . 'footer03.png',
						'label' => esc_html__( 'Three Columns', 'nucleus' ),
					),
					4 => array(
						'src'   => $footer_preview . 'footer04.png',
						'label' => esc_html__( 'Four Columns', 'nucleus' ),
					),
				),
			) )
			->add_field( 'footer_skin', 'select', array(
				'label'       => esc_html__( 'Skin', 'nucleus' ),
				'description' => esc_html__( 'This option let you control your footer skin', 'nucleus' ),
				'default'     => 'light',
				'options'     => array(
					'light' => esc_html__( 'Light', 'nucleus' ),
					'dark'  => esc_html__( 'Dark', 'nucleus' ),
				),
			) )
			->add_field( 'footer_is_copy', 'switch', array(
				'label'       => esc_html__( 'Copyright', 'nucleus' ),
				'description' => esc_html__( 'Enable or Disable the copyright block in footer', 'nucleus' ),
				'default'     => true,
				'label_on'    => $translated['enable'],
				'label_off'   => $translated['disable'],
			) )
			->add_row()
			->add_column( 6 )
			->add_field( 'footer_copy_left', 'editor', array(
				'label'       => esc_html__( 'Copyright Left Column', 'nucleus' ),
				'description' => esc_html__( 'The content for the left column', 'nucleus' ),
				'default'     => '&copy; Nucleus 2016. Made by <a href="http://8guild.com/" target="_blank">8Guild</a> with <i class="fa fa-heart text-danger"></i> love.',
				'wpautop'     => false,
				'teeny'       => true,
				'required'    => array( 'footer_is_copy', '=', 1 ),
			) )
			->add_column( 6 )
			->add_field( 'footer_copy_right', 'editor', array(
				'label'       => esc_html__( 'Copyright Right Column', 'nucleus' ),
				'description' => esc_html__( 'The content for the right column', 'nucleus' ),
				'wpautop'     => false,
				'teeny'       => true,
				'required'    => array( 'footer_is_copy', '=', 1 ),
			) )
			->add_row()
			->add_column( 4 )
			->add_field( 'footer_is_action', 'switch', array(
				'label'       => esc_html__( 'Action Links', 'nucleus' ),
				'description' => esc_html__( 'Enable or disable Action links', 'nucleus' ),
				'default'     => false,
				'label_on'    => $translated['enable'],
				'label_off'   => $translated['disable'],
			) )
			->add_column( 2 )
			->add_field( 'footer_action_1_icon', 'media', array(
				'label'    => $translated['icon'],
				'media'    => array( 'title' => $translated['icon'] ),
				'required' => array( 'footer_is_action', '=', 1 ),
			) )
			->add_column( 3 )
			->add_field( 'footer_action_1_title', 'text', array(
				'label'    => $translated['title'],
				'required' => array( 'footer_is_action', '=', 1 ),
			) )
			->add_column( 3 )
			->add_field( 'footer_action_1_url', 'text', array(
				'label'    => $translated['url'],
				'required' => array( 'footer_is_action', '=', 1 ),
			) )
			->add_row()
			->add_offset( 4 )
			->add_column( 2 )
			->add_field( 'footer_action_2_icon', 'media', array(
				'label'    => $translated['icon'],
				'media'    => array( 'title' => $translated['icon'] ),
				'required' => array( 'footer_is_action', '=', 1 ),
			) )
			->add_column( 3 )
			->add_field( 'footer_action_2_title', 'text', array(
				'label'    => $translated['title'],
				'required' => array( 'footer_is_action', '=', 1 ),
			) )
			->add_column( 3 )
			->add_field( 'footer_action_2_url', 'text', array(
				'label'    => $translated['url'],
				'required' => array( 'footer_is_action', '=', 1 ),
			) )
			->add_row()
			->add_offset( 4 )
			->add_column( 2 )
			->add_field( 'footer_action_3_icon', 'media', array(
				'label'    => $translated['icon'],
				'media'    => array( 'title' => $translated['icon'] ),
				'required' => array( 'footer_is_action', '=', 1 ),
			) )
			->add_column( 3 )
			->add_field( 'footer_action_3_title', 'text', array(
				'label'    => $translated['title'],
				'required' => array( 'footer_is_action', '=', 1 ),
			) )
			->add_column( 3 )
			->add_field( 'footer_action_3_url', 'text', array(
				'label'    => $translated['url'],
				'required' => array( 'footer_is_action', '=', 1 ),
			) )
			->parent( 'section' )
			->add_field( 'footer_is_subscribe', 'switch', array(
				'label'       => esc_html__( 'Subscribe', 'nucleus' ),
				'description' => esc_html__( 'Enable or disable Subscribe Form in Footer', 'nucleus' ),
				'default'     => false,
				'label_on'    => $translated['enable'],
				'label_off'   => $translated['disable'],
			) )
			->add_row()
			->add_column( 2 )
			->add_field( 'footer_subscribe_icon', 'media', array(
				'label'    => $translated['icon'],
				'media'    => array( 'title' => $translated['icon'] ),
				'required' => array( 'footer_is_subscribe', '=', 1 ),
			) )
			->add_column( 5 )
			->add_field( 'footer_subscribe_url', 'text', array(
				'label'       => esc_html__( 'MailChimp Link', 'nucleus' ),
				'description' => esc_html__( 'This URL can be retrieved from your mailchimp dashboard > Lists > your desired list > list settings > forms. in your form creation page you will need to click on "share it" tab then find "Your subscribe form lives at this URL:". Its a short URL so you will need to visit this link. Once you get into the your created form page, then copy the full address and paste it here in this form. URL look like http://YOUR_USER_NAME.us6.list-manage.com/subscribe?u=d5f4e5e82a59166b0cfbc716f&id=4db82d169b', 'nucleus' ),
				'escape'      => 'esc_url_raw',
				'sanitize'    => 'esc_url',
				'required'    => array( 'footer_is_subscribe', '=', 1 ),
			) )
			->add_column( 5 )
			->add_field( 'footer_subscribe_placeholder', 'text', array(
				'label'    => esc_html__( 'Placeholder', 'nucleus' ),
				'default'  => esc_html__( 'Your Email', 'nucleus' ),
				'required' => array( 'footer_is_subscribe', '=', 1 ),
			) );
		//</editor-fold>

		//<editor-fold desc="404 Section">
		$layout
			->add_section( '404', '404', array(
				'icon' => 'material-icons warning',
			) )
			->add_field( '404_title', 'text', array(
				'label'   => esc_html__( 'Title', 'nucleus' ),
				'default' => esc_html__( '404 Page not found', 'nucleus' ),
			) )
			->add_field( '404_description', 'textarea', array(
				'label'       => esc_html__( 'Description', 'nucleus' ),
				'description' => esc_html__( 'HTML is allowed in this field', 'nucleus' ),
				'default'     => esc_html__( 'You may want to head back to the homepage. If you think something is broken, please do not hesitate to report a problem.', 'nucleus' ),
			) )
			->add_field( '404_button_text', 'text', array(
				'label'       => esc_html__( 'Button Text', 'nucleus' ),
				'description' => esc_html__( 'The button leads to the home page. This option lets you customize the text on it.', 'nucleus' ),
				'default'     => esc_html__( 'Back to Homepage', 'nucleus' ),
			) )
			->add_field( '404_featured', 'media', array(
				'label' => esc_html__( 'Featured Image', 'nucleus' ),
				'media' => array( 'title' => esc_html__( '404 Featured Image', 'nucleus' ) )
			) );
		//</editor-fold>

		//<editor-fold desc="Cache Section">
		$layout
			->add_section( 'cache', esc_html__( 'Cache', 'nucleus' ), array(
				'icon' => 'material-icons storage',
			) )
			->add_field( 'cache_is_shortcodes', 'switch', array(
				'label'       => esc_html__( 'Caching in shortcodes', 'nucleus' ),
				'description' => esc_html__( 'NOTE: disabling this option will not flush the cache. Caching will not be used.', 'nucleus' ),
				'default'     => true,
				'label_on'    => $translated['enable'],
				'label_off'   => $translated['disable'],
			) );
		//</editor-fold>

		equip_add_options_page( NUCLEUS_OPTIONS, $layout, array(
			//'parent_slug' => 'themes.php',
			'page_title' => esc_html__( 'Nucleus Options', 'nucleus' ),
			'menu_title' => esc_html__( 'Nucleus', 'nucleus' ),
			'capability' => 'manage_options',
			'menu_slug'  => 'nucleus',
			'icon_url'   => '',
			'position'   => '3.33',
			'sass'       => array(
				'root'      => NUCLEUS_TEMPLATE_DIR . '/sass', // no trailing slash, please
				'subdir'    => 'nucleus',
				'file'      => 'compiled.css',
				'option'    => NUCLEUS_COMPILED,
				'variables' => array(
					'_variables.scss',
					'_bs-variables.scss',
				),
				'files' => array(
					'_mixins.scss',
					'_scaffolding.scss',
					'_typography.scss',
					'_forms.scss',
					'_tables.scss',
					'_buttons.scss',
					'_preloader.scss',
					'_social-buttons.scss',
					'_modals.scss',
					'_navbar.scss',
					'_post.scss',
					'_page-title.scss',
					'_comments.scss',
					'_shortcodes.scss',
					'_shop.scss',
					'_widgets.scss',
					'_grids.scss',
					'_tooltips.scss',
					'_navs.scss',
					'_panels.scss',
					'_carousel.scss',
					'_components.scss',
					'_footer.scss',
					'_scroll-slideshow.scss',
				),
			),
		) );

	} catch ( Exception $e ) {
		trigger_error( $e->getMessage() );
	}
}

add_action( 'equip/register', 'nucleus_add_theme_options' );
