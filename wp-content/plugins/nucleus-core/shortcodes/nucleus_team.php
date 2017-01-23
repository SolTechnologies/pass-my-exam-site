<?php
/**
 * Team | nucleus_team
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_team_atts', array(
	'image'              => '',
	'name'               => '',
	'position'           => '',
	'about'              => '',
	'socials_socials'    => '',
	'socials_is_tooltip' => '',
	'type'               => 1, // 1 = flip, 2 = morphing, 3 - static
	'is_animation'       => 'disable',
	'animation_type'     => 'top',
	'animation_delay'    => 0,
	'animation_easing'   => 'none',
	'class'              => '',
) ), $atts );

$image_id   = (int) $a['image'];
$featured 	= '';
$name       = esc_html( trim( $a['name'] ) );
$position   = esc_html( trim( $a['position'] ) );
$about      = wp_kses( trim( $a['about'] ), wp_kses_allowed_html( 'data' ) );
$type       = (int) $a['type'];
$is_flip    = ( 1 === $type );
$is_morph   = ( 2 === $type );
$is_static  = ( 3 === $type );
$animation  = nucleus_parse_array( $a, 'animation_' );
$is_socials = ( ! empty( $a['socials_socials'] ) );
$socials    = ''; // may be a shortcode content here
$class      = esc_attr( nucleus_get_class_set( array(
	'teammate-' . $type,
	$is_flip ? 'text-center' : '',
	$is_morph ? 'mobile-center' : '',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) ) );

if ( empty( $image_id ) ) {
	$featured = nucleus_get_tag( 'img', [
		'src' => NUCLEUS_CORE_URI . '/img/placeholder-team.png',
		'alt' => 'Teammate'
	] );
} else {
	$featured = wp_get_attachment_image( $image_id, 'medium' );
}


// may be add socials
if ( $is_socials && ! $is_static ) {
	$s_atts  = nucleus_parse_array( $a, 'socials_' );
	$s_sh    = nucleus_shortcode_build( 'nucleus_socials', $s_atts );
	$socials = nucleus_do_shortcode( $s_sh );
}

// template for flip type
$t1 = <<<'FLIP'
<div class="{class}">
    <div class="thumbnail">
        <div class="flipper">
            <div class="front">{featured}</div>
            <div class="back">{about}{socials}</div>
        </div>
    </div>
    {name}
    {position}
</div>
FLIP;

// template for morphing type
$t2 = <<<'MORPH'
<div class="{class}">
    <div class="thumbnail">{featured}{socials}</div>
    {name}
    {position}
    {about}
</div>
MORPH;

// template for static type
$t3 = <<<'STATIC'
<div class="{class}">
    <div class="thumbnail">{featured}</div>
    {name}
    {position}
</div>
STATIC;


$r = array(
	'{class}'    => $class,
	'{featured}' => $featured,
	'{about}'    => $is_flip ? nucleus_get_text( $about, '<p class="padding-top">', '</p>' ) : nucleus_get_text( $about, '<p>', '</p>' ),
	'{socials}'  => $socials,
	'{name}'     => nucleus_get_text( $name, '<h3 class="teammate-name">', '</h3>' ),
	'{position}' => nucleus_get_text( $position, '<p class="teammate-position">', '</p>' ),
);

echo str_replace( array_keys( $r ), array_values( $r ), ${'t'. $type} );
