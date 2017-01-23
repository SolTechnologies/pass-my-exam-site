<?php
/**
 * Block Title | nucleus_block_title
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
 * Filter the "nucleus_block_title" default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_block_title_atts', array(
	'title'            => '',
	'subtitle'         => '',
	'tag'              => 'h2',
	'alignment'        => 'center', // left | center | right
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$title    = esc_html( $a['title'] );
$subtitle = nucleus_get_text( esc_html( $a['subtitle'] ), '<small>', '</small>' );

$allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
$tag          = in_array( $a['tag'], $allowed_tags, true ) ? $a['tag'] : 'h2';
$alignment    = sanitize_key( $a['alignment'] );
$animation    = nucleus_parse_array( $a, 'animation_' );
$class        = nucleus_get_class_set( array(
	'block-title',
	'text-' . $alignment,
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

echo nucleus_get_tag( $tag, array( 'class' => $class ), $title . $subtitle );
