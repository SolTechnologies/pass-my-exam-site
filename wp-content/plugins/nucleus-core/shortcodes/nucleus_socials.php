<?php
/**
 * Socials | nucleus_socials
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
 * Filter the "nucleus_socials" default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_socials_atts', array(
	'socials'          => '',
	'is_tooltip'       => 'disable',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$socials    = json_decode( urldecode( $a['socials'] ), true );
$networks   = nucleus_get_networks();
$is_tooltip = ( 'enable' === $a['is_tooltip'] );
$animation  = nucleus_parse_array( $a, 'animation_' );
if ( empty( $networks ) ) {
	return;
}

// attributes for each social network link
$attributes          = array();
$attributes['href']  = '{url}';
$attributes['class'] = '{helper}';

if ( $is_tooltip ) {
	$attributes['data-toggle']    = 'tooltip';
	$attributes['data-placement'] = 'top';
	$attributes['title']          = '{name}';
}

$tpl   = nucleus_get_tag( 'a', $attributes, '<i class="{icon}"></i>' );
$class = nucleus_get_class_set( array(
	'social-bar',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

echo '<div class="', esc_attr( $class ), '">';
foreach ( $socials as $social ) {
	$network = $social['network'];
	$url     = $social['url'];

	$r = array(
		'{url}'    => preg_match( '@^https?://@i', $url ) ? esc_url( $url ) : esc_attr( $url ),
		'{helper}' => esc_attr( $networks[ $network ]['helper'] ),
		'{icon}'   => esc_attr( $networks[ $network ]['icon'] ),
		'{name}'   => esc_attr( $networks[ $network ]['name'] ),
	);

	echo str_replace( array_keys( $r ), array_values( $r ), $tpl );
}
echo '</div>';