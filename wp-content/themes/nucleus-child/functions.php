<?php
/**
 * Nucleus Child
 *
 * @author  8guild
 * @package Nucleus
 */

/**
 * Enqueue parent and child scripts and styles
 */
function nucleus_child_styles() {
	wp_enqueue_style( 'nucleus-child', get_stylesheet_directory_uri() . '/style.css', array(), null );
}

add_action( 'wp_enqueue_scripts', 'nucleus_child_styles', 11 );

function wpb_adding_scripts() {
wp_register_script('fp-smoothscroll', '/wp-content/themes/nucleus-child/scripts/fp-smoothscroll.js', array('jquery'),'1.0', true);
wp_enqueue_script('fp-smoothscroll');
}

add_action( 'wp_enqueue_scripts', 'wpb_adding_scripts' );  