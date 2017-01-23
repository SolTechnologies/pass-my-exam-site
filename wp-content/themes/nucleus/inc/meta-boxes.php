<?php
/**
 * Theme Meta Boxes
 *
 * @author  8guild
 * @package Nucleus
 */

function nucleus_add_meta_boxes() {
	if ( ! defined( 'EQUIP_VERSION' ) ) {
		return;
	}

	$screen       = get_current_screen();
	$is_post      = ( 'post' === $screen->post_type );
	$is_page      = ( 'page' === $screen->post_type );
	$is_portfolio = ( 'nucleus_portfolio' === $screen->post_type );
	$is_slideshow = ( 'nucleus_slideshow' === $screen->post_type );

	$translated_default    = esc_html__( 'Default', 'nucleus' );
	$layouts_preview       = nucleus_get_asset( 'img/options/layouts' );
	$option_enable_disable = array(
		'default' => $translated_default,
		1         => esc_html__( 'Enable', 'nucleus' ),
		0         => esc_html__( 'Disable', 'nucleus' ),
	);

	try {
		$layout = equip_create_meta_box_layout();
		$layout
			->add_field( 'post_layout', 'image_select', array(
				'label'       => esc_html__( 'Post Layout', 'nucleus' ),
				'description' => esc_html__( 'Choose the current post layout', 'nucleus' ),
				'default'     => 'right-sidebar',
				'hidden'      => ! $is_post, // show only for posts
				'width'       => 200,
				'height'      => 150,
				'options'     => array(
					'right-sidebar' => array(
						'src'   => $layouts_preview . '/layout01.png',
						'label' => esc_html__( 'Right Sidebar', 'nucleus' ),
					),
					'left-sidebar'  => array(
						'src'   => $layouts_preview . '/layout02.png',
						'label' => esc_html__( 'Left Sidebar', 'nucleus' ),
					),
					'no-sidebar'    => array(
						'src'   => $layouts_preview . '/layout03.png',
						'label' => esc_html__( 'No Sidebar', 'nucleus' ),
					),
				),
			) )
			->add_row()
			->add_column( 4 )
			->add_field( 'header_is_sticky', 'select', array(
				'label'       => esc_html__( 'Enable or disable sticky header', 'nucleus' ),
				'description' => esc_html__( 'Default option refers to the Theme Options > Header section', 'nucleus' ),
				'options'     => $option_enable_disable,
			) )
			->add_column( 4 )
			->add_field( 'header_layout', 'select', array(
				'label'   => esc_html__( 'Header Layout', 'nucleus' ),
				'default' => 'default',
				'options' => array(
					'default'   => $translated_default,
					'boxed'     => esc_html__( 'Boxed', 'nucleus' ),
					'fullwidth' => esc_html__( 'Fullwidth', 'nucleus' ),
				),
			) )
			->add_column( 4 )
			->add_field( 'menu_is_icons', 'switch', array(
				'label'       => esc_html__( 'Menu Icons', 'nucleus' ),
				'description' => esc_html__( 'Enable or disable the menu icon on current page', 'nucleus' ),
				'default'     => true,
				'label_on'    => esc_html__( 'On', 'nucleus' ),
				'label_off'   => esc_html__( 'Off', 'nucleus' ),
			) )
			->reset()
			->add_row()
			->add_column( 6 )
			->add_field( 'global_is_page_title', 'select', array(
				'label'       => esc_html__( 'Enable or disable Page Title', 'nucleus' ),
				'description' => esc_html__( 'Note, breadcrumbs will be disabled, too.', 'nucleus' ),
				'default'     => 'default',
				'options'     => $option_enable_disable,
				'hidden'      => $is_slideshow,
			) )
			->add_column( 6 )
			->add_field( 'custom_title', 'text', array(
				'label'       => esc_html__( 'Custom Title', 'nucleus' ),
				'description' => esc_html__( 'An extra title for you single post from our designer!', 'nucleus' ),
				'hidden'      => ! $is_post, // show only for posts
				'required'    => array( 'global_is_page_title', 'in_array', array( 'default', 1 ) ),
			) )
			->add_field( 'portfolio_permalink', 'text', array(
				'label'       => esc_html__( 'Portfolio Custom Link', 'nucleus' ),
				'description' => esc_html__( 'Here you can specify the custom link to redirect to a custom page, not portfolio single. Applicable only for portfolio tile.', 'nucleus' ),
				'hidden'      => ! $is_portfolio, // show only for portfolio posts
			) )
			->reset()
			->add_row()
			->add_column( 4 )
			->add_field( 'header_is_search', 'select', array(
				'label'       => esc_html__( 'Search Form', 'nucleus' ),
				'description' => esc_html__( 'Enable or disable the search form in header for current page.', 'nucleus' ),
				'default'     => 'default',
				'options'     => $option_enable_disable,
			) )
			->add_column( 4 )
			->add_field( 'header_is_signup_login', 'select', array(
				'label'       => esc_html__( 'Sign up / Log in Buttons', 'nucleus' ),
				'description' => esc_html__( 'Enable or disable the sign up / log in button in header for current page.', 'nucleus' ),
				'default'     => 'default',
				'options'     => $option_enable_disable,
			) )
			->add_column( 4 )
			->add_field( 'footer_skin', 'select', array(
				'label'       => esc_html__( 'Footer Skin', 'nucleus' ),
				'description' => esc_html__( 'Choose the footer skin for current page.', 'nucleus' ),
				'default'     => 'default',
				'options'     => array(
					'default' => $translated_default,
					'light'   => esc_html__( 'Light', 'nucleus' ),
					'dark'    => esc_html__( 'Dark', 'nucleus' ),
				),
			) );

		equip_add_meta_box( NUCLEUS_PAGE_SETTINGS, $layout, array(
			'id'     => 'nucleus-page-settings',
			'title'  => esc_html__( 'Page Settings', 'nucleus' ),
			'screen' => array( 'page', 'post', 'nucleus_portfolio', 'nucleus_slideshow' ),
		) );

	} catch ( Exception $e ) {
		trigger_error( $e->getMessage() );
	}
}

add_action( 'current_screen', 'nucleus_add_meta_boxes' );