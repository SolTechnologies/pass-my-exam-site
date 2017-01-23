<?php
/**
 * Nucleus Theme Customizer
 *
 * @package Nucleus
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function nucleus_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}

add_action( 'customize_register', 'nucleus_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function nucleus_customize_preview_js() {
	wp_enqueue_script( 'nucleus_customizer', NUCLEUS_TEMPLATE_URI . '/js/customizer.js', array( 'customize-preview' ), null, true );
}

add_action( 'customize_preview_init', 'nucleus_customize_preview_js' );
