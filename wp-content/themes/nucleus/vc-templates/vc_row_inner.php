<?php
/**
 * Inner Row | vc_row_inner
 *
 * @var array                          $atts    Shortcode attributes
 * @var mixed                          $content Shortcode content
 * @var WPBakeryShortCode_VC_Row_Inner $this
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Filter the "nucleus_testimonial" default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_vc_inner_row_atts', array(
	'id'    => '',
	'class' => '',
	'css'   => '',
) ), $atts );

$classes = nucleus_get_class_set( array(
	'row',
	trim( vc_shortcode_custom_css_class( $a['css'] ) ),
	$a['class'],
) );

$attributes = array(
	'id'    => esc_attr( $a['id'] ),
	'class' => esc_attr( $classes ),
);

echo '<div ', nucleus_get_html_attr( $attributes ), '>';
echo nucleus_do_shortcode( $content );
echo '</div>';
