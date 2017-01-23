<?php
/**
 * Google Map | nucleus_map
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
 * Filter the default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_map_atts', array(
	'location'         => '',
	'height'           => 500,
	'zoom'             => 14,
	'is_zoom'          => 'disable',
	'is_scroll'        => 'disable',
	'is_marker'        => 'disable',
	'marker_title'     => '',
	'marker'           => '', // attachment ID
	'style'            => '', // custom base64 encoded styles
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$is_zoom   = ( 'enable' === $a['is_zoom'] );
$is_scroll = ( 'enable' === $a['is_scroll'] );
$is_marker = ( 'enable' === $a['is_marker'] );

$title     = esc_html( $a['marker_title'] );
$location  = empty( $a['location'] ) ? '' : esc_attr( $a['location'] );
$zoom      = is_numeric( $a['zoom'] ) ? absint( $a['zoom'] ) : 14;
$height    = is_numeric( $a['height'] ) ? absint( $a['height'] ) : 500;
$marker    = empty( $a['marker'] ) ? '' : nucleus_get_image_src( (int) $a['marker'] );
$style     = empty( $a['style'] ) ? '' : preg_replace( '/\s+/', '', urldecode( base64_decode( $a['style'] ) ) );
$animation = nucleus_parse_array( $a, 'animation_' );

$class = nucleus_get_class_set( array(
	'google-map',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

$attr = nucleus_get_html_attr( array(
	'class'                 => esc_attr( $class ),
	'data-height'           => $height,
	'data-address'          => $location,
	'data-zoom'             => $zoom,
	'data-disable-controls' => $is_zoom ? 'false' : 'true',
	'data-scrollwheel'      => $is_scroll ? 'true' : 'false',
	'data-marker'           => $is_marker ? esc_url( $marker ) : '',
	'data-marker-title'     => $is_marker ? $title : '',
	'data-styles'           => json_decode( $style, true ),
) );

echo '<div ', $attr, '></div>';