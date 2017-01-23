<?php
/**
 * Button | nucleus_button
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
 * Filter the "nucleus_button" default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_button_atts', array(
	'text'             => '',
	'link'             => '',
	'color'            => 'default', // default | primary | success | ... | light
	'type'             => 'standard', // standard | ghost | 3d
	'size'             => 'nl', // small (sm) | normal (nl)
	'alignment'        => 'inline', // inline | left | center | right
	'is_full'          => 'no', // yes | no
	'is_icon'          => 'no', // yes | no
	'icon_library'     => 'fontawesome', // fontawesome | openiconic | typicons | entypo | linecons | pixeden
	'icon_fontawesome' => '',
	'icon_openiconic'  => '',
	'icon_typicons'    => '',
	'icon_entypo'      => '',
	'icon_linecons'    => '',
	'icon_feather'     => '',
	'icon_flaticon'    => '',
	'icon_position'    => 'left', // left | right
	'is_waves'         => 'disable',
	'waves_skin'       => 'dark', // dark | light
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$attributes = array();
$classes    = array();

$color     = esc_attr( $a['color'] );
$type      = esc_attr( $a['type'] );
$size      = esc_attr( $a['size'] );
$alignment = esc_attr( $a['alignment'] );
$animation = nucleus_parse_array( $a, 'animation_' );
$w_skin    = esc_attr( $a['waves_skin'] );

$is_full   = ( 'yes' === $a['is_full'] );
$is_icon   = ( 'yes' === $a['is_icon'] );
$is_waves  = ( 'enable' === $a['is_waves'] );
$is_inline = ( 'inline' === $a['alignment'] );
$is_right  = ( 'right' === $a['icon_position'] );

$icon = '';
$text = esc_html( $a['text'] );
$link = nucleus_build_link( $a['link'] );

$attributes['href']   = empty( $link['url'] ) ? '#' : esc_url( trim( $link['url'] ) );
$attributes['target'] = empty( $link['target'] ) ? '' : esc_attr( trim( $link['target'] ) );
$attributes['title']  = empty( $link['title'] ) ? '' : esc_attr( trim( $link['title'] ) );

// default button classes
$classes[] = 'btn';
$classes[] = 'btn-' . $color;
$classes[] = 'btn-' . $size;
$classes[] = 'btn-' . $type;
$classes[] = $is_full ? 'btn-block' : '';
$classes[] = $is_waves ? 'waves-effect' : '';
$classes[] = $is_waves ? 'waves-' . $w_skin : '';
$classes[] = nucleus_get_animation_class( $a['is_animation'], $animation );
$classes[] = $a['class'];

// build an icon
if ( $is_icon ) {
	// icon position class
	$classes[] = $is_right ? 'btn-icon-right' : 'btn-icon-left';
	$library   = $a['icon_library'];
	$icon      = sprintf( '<i class="%s"></i>', esc_attr( $a["icon_{$library}"] ) );
}

$attributes['class'] = nucleus_get_class_set( $classes );
$attributes          = nucleus_get_html_attr( $attributes );

// template, according to icon position
// 1 - attributes, 2 - text, 3 - icon
$tpl = ( $is_right ) ? '<a %1$s>%2$s%3$s</a>' : '<a %1$s>%3$s%2$s</a>';
if ( $is_inline ) {
	printf( $tpl, $attributes, $text, $icon );
} else {
	$btn = sprintf( $tpl, $attributes, $text, $icon );
	printf( '<div class="text-%1$s">%2$s</div>', $alignment, $btn );
}