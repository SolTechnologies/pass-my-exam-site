<?php
/**
 * Image Carousel | nucleus_image_carousel
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
 * Filter the "nucleus_logo_carousel" default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_image_carousel_atts', array(
	'images'           => '',
	'is_autoheight'    => 'no',
	'is_loop'          => 'no',
	'is_autoplay'      => 'no',
	'delay'            => 3000,
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$images        = wp_parse_id_list( $a['images'] );
$looped        = ( count( $images ) >= 2 );
$is_autoheight = ( 'yes' === $a['is_autoheight'] );
$is_loop       = ( 'yes' === $a['is_loop'] );
$is_autoplay   = ( 'yes' === $a['is_autoplay'] );
$delay         = (int) $a['delay'];
$animation     = nucleus_parse_array( $a, 'animation_' );
$class         = nucleus_get_class_set( array(
	'image-carousel',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

$attr = array();

$attr['class']           = $class;
$attr['data-autoheight'] = $is_autoheight ? 'true' : 'false';
$attr['data-loop']       = $is_loop ? 'true' : 'false';
$attr['data-autoplay']   = $is_autoplay ? 'true' : 'false';
$attr['data-interval']   = $delay;

// start output
echo '<div ', nucleus_get_html_attr( $attr ), '>';
if ( $looped ) {
	echo '<div class="inner">';
}

foreach ( (array) $images as $image_id ) {
	echo wp_get_attachment_image( $image_id, 'full' );
}

if ( $looped ) {
	echo '</div>';
}
echo '</div>';
