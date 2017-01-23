<?php
/**
 * Subscribe | nucleus_subscribe
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_subscribe_atts', array(
	'url'              => '',
	'placeholder'      => '',
	'button_text'      => __( 'Subscribe', 'nucleus' ),
	'button_color'     => 'default',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

if ( empty( $a['url'] ) ) {
	return;
}

$url          = esc_url( $a['url'] );
$placeholder  = esc_html( $a['placeholder'] );
$animation    = nucleus_parse_array( $a, 'animation_' );
$button_text  = esc_html( $a['button_text'] );
$button_color = esc_attr( $a['button_color'] );

$antispam = '';
$button   = '';
$input    = '';

// Build MC AntiSPAM
$request_uri = parse_url( htmlspecialchars_decode( $url ), PHP_URL_QUERY );
parse_str( $request_uri, $c );
if ( array_key_exists( 'u', $c ) && array_key_exists( 'id', $c ) ) {
	$antispam_name = sprintf( 'b_%1$s_%2$s', $c['u'], $c['id'] );
	$antispam .= '<div style="position: absolute; left: -5000px;">';
	$antispam .= sprintf( '<input type="text" name="%s" tabindex="-1" value="">', $antispam_name );
	$antispam .= '</div>';
	unset( $antispam_name );
}
unset( $request_uri, $c );

// form class
$class = esc_attr( nucleus_get_class_set( array(
	'get-app-form',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) ) );

// form attributes
$form = array(
	'method'       => 'post',
	'action'       => $url,
	'class'        => $class,
	'target'       => '_blank',
	'novalidate'   => true,
	'autocomplete' => 'off',
);

// <input>
$input = nucleus_get_tag( 'input', array(
	'type'        => 'email',
	'name'        => 'EMAIL',
	'class'       => 'form-control',
	'placeholder' => $placeholder,
) );

// <button>
$button = nucleus_get_tag( 'button', array(
	'type'  => 'submit',
	'class' => 'btn btn-sm waves-effect waves-light btn-' . $button_color,
), $button_text );

// output
echo nucleus_get_tag( 'form', $form, $input . $antispam . $button );
