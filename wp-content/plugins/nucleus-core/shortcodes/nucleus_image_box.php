<?php
/**
 * Image Box | nucleus_image_box
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
 * Filter the "nucleus_image_box" default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_image_box_atts', array(
	'image'            => '',
	'link'             => '',
	'title'            => '',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$image_id  = (int) $a['image'];
$link      = nucleus_build_link( $a['link'] );
$is_link   = ( ! empty( $link['url'] ) );
$title     = esc_html( $a['title'] );
$animation = nucleus_parse_array( $a, 'animation_' );
$class     = nucleus_get_class_set( array(
	'image-box',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

$attr = array();

if ( $is_link ) {
	$attr['href']   = esc_url( trim( $link['url'] ) );
	$attr['target'] = esc_attr( trim( $link['target'] ) );
	$attr['title']  = esc_html( trim( $link['title'] ) );
}

$attr['class'] = esc_attr( $class );

$image = wp_get_attachment_image( $image_id, 'medium' );
$title = nucleus_get_text( $title, '<div class="ib-text"><h3 class="ib-title">', '</h3></div>' );
$tag   = $is_link ? 'a' : 'div';

echo nucleus_get_tag( $tag, $attr, $image . $title );