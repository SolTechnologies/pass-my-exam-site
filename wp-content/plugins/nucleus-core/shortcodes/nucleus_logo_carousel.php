<?php
/**
 * Logo Carousel | nucleus_logo_carousel
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_socials_atts', array(
	'carousel'         => '',
	'is_loop'          => 'no',
	'is_autoplay'      => 'no',
	'delay'            => 3000,
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$carousel    = json_decode( urldecode( $a['carousel'] ), true );
$looped      = ( count( $carousel ) >= 2 );
$is_loop     = ( 'yes' === $a['is_loop'] );
$is_autoplay = ( 'yes' === $a['is_autoplay'] );
$delay       = (int) $a['delay'];
$animation   = nucleus_parse_array( $a, 'animation_' );
$class       = nucleus_get_class_set( array(
	'logo-carousel',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

// carousel wrapper attributes
$attr = array();

$attr['class']         = $class;
$attr['data-autoplay'] = $is_autoplay ? 'true' : 'false';
$attr['data-interval'] = $delay;
$attr['data-loop']     = $is_loop ? 'true' : 'false';

// start output
echo '<div ', nucleus_get_html_attr( $attr ), '>';
if ( $looped ) {
	echo '<div class="inner">';
}
foreach ( $carousel as $item ) {
	printf( '<a href="%1$s">%2$s</a>',
		esc_url( $item['url'] ),
		wp_get_attachment_image( (int) $item['logo'], 'full' )
	);
}
if ( $looped ) {
	echo '</div>';
}
echo '</div>';
