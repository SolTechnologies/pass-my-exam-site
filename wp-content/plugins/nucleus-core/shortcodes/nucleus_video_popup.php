<?php
/**
 * Video Popup | nucleus_video_popup
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
 * Filter the "nucleus_video_popup" default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_video_popup_atts', array(
	'video'            => '',
	'title'            => '',
	'size'             => 'normal',
	'alignment'        => 'center',
	'skin'             => 'dark',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

if ( empty( $a['video'] ) ) {
	return;
}

$video     = esc_url( trim( $a['video'] ) );
$title     = esc_html( trim( $a['title'] ) );
$size      = sanitize_key( $a['size'] );
$alignment = sanitize_key( $a['alignment'] );
$skin      = sanitize_key( $a['skin'] );
$animation = nucleus_parse_array( $a, 'animation_' );
$class     = nucleus_get_class_set( array(
	'video-popup-btn',
	$size . '-btn',
	'text-' . $skin,
	'text-' . $alignment,
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

$video_attr = array(
	'href'  => $video,
	'class' => 'play-btn waves-effect waves-light',
);

echo '<div class="', esc_attr( $class ), '">';
echo nucleus_get_tag( 'a', $video_attr, '<i class="icon-play"></i>' );
echo nucleus_get_text( $title, '<h4>', '</h4>' );
echo '</div>';
