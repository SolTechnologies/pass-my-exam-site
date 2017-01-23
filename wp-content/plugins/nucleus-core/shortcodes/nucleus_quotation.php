<?php
/**
 * Quotation | nucleus_quotation
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
 * Filter the "nucleus_quotation" default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_quotation_atts', array(
	'quotation'        => '',
	'author'           => '',
	'skin'             => 'dark', // dark | light
	'is_shareable'     => 'no',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$author       = esc_html( $a['author'] );
$quotation    = esc_textarea( $a['quotation'] );
$skin         = esc_attr( $a['skin'] );
$is_shareable = ( 'yes' === $a['is_shareable'] );
$animation    = nucleus_parse_array( $a, 'animation_' );
$class        = nucleus_get_class_set( array(
	$is_shareable ? 'quote-shareable' : '',
	$is_shareable ? 'nucleus-share-twitter' : '',
	$skin . '-skin',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

$attr          = array();
$attr['class'] = esc_attr( $class );
if ( $is_shareable ) {
	$attr['data-text'] = $quotation . ' ' . $author;
	$attr['data-url']  = get_permalink();
}

// start output
echo '<blockquote ', nucleus_get_html_attr( $attr ), '>';
if ( $is_shareable ) {
	echo nucleus_get_tag( 'span', array( 'class' => 'share-btn' ), '<i class="fa fa-twitter"></i>' );
}
echo nucleus_get_text( $quotation, '<p>', '</p>' );
echo nucleus_get_text( $author, '<cite>', '</cite>' );
echo '</blockquote>';
