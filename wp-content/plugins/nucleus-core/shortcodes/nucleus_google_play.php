<?php
/**
 * Google Play | nucleus_google_play
 *
 * @var array $atts    Shortcode attributes
 * @var mixed $content Shortcode content
 *
 * @author 8guild
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Filter the "nucleus_google_play" default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_google_play_atts', array(
	'link'             => '',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

/**
 * Filter the path to Google Play background
 *
 * @param string $path URI to background image
 */
$image     = apply_filters( 'nucleus_shortcode_google_play_bg', NUCLEUS_CORE_URI . '/img/google-play.png' );
$link      = nucleus_build_link( $a['link'] );
$animation = nucleus_parse_array( $a, 'animation_' );
$class     = nucleus_get_class_set( array(
	'btn-google-play',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

$attributes           = array();
$attributes['href']   = empty( $link['url'] ) ? '#' : esc_url( trim( $link['url'] ) );
$attributes['target'] = empty( $link['target'] ) ? '' : esc_attr( trim( $link['target'] ) );
$attributes['title']  = empty( $link['title'] ) ? '' : esc_attr( trim( $link['title'] ) );
$attributes['class']  = $class;
$attributes['style']  = nucleus_css_background_image( $image );

echo nucleus_get_tag( 'a', $attributes, '' );