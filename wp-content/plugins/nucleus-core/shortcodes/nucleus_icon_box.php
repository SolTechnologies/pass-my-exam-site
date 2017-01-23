<?php
/**
 * Icon Box | nucleus_icon_box
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_icon_box_atts', array(
	'type'             => 'image',
	'image'            => '',
	'icon_library'     => 'fontawesome',
	'icon_fontawesome' => '',
	'icon_openiconic'  => '',
	'icon_typicons'    => '',
	'icon_entypo'      => '',
	'icon_linecons'    => '',
	'icon_flaticon'    => '',
	'icon_feather'     => '',
	'title'            => '',
	'description'      => '',
	'alignment'        => 'center',
	'layout'           => 'vertical',
	'is_expandable'    => 'no',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$type          = $a['type'];
$title         = esc_html( $a['title'] );
$description   = wp_kses( $a['description'], wp_kses_allowed_html( 'data' ) );
$layout        = $a['layout'];
$alignment     = sanitize_key( $a['alignment'] );
$is_expandable = ( 'vertical' === $layout && 'yes' === $a['is_expandable'] );
$is_horizontal = ( 'horizontal' === $layout );
$animation     = nucleus_parse_array( $a, 'animation_' );

// vertical template
$vertical = <<<'VERTICAL'
<div class="{class}">
    <div class="bwi-inner">
        <div class="bwi-icon">
            {featured}
        </div>
        {title}
        {description}
    </div>
</div>
VERTICAL;

// horizontal icon box template
$horizontal = <<<'HORIZONTAL'
<div class="{class}">
    <div class="bwi-inner">
        <div class="bwi-icon">
            {featured}
        </div>
        <div class="bwi-content">
            {title}
            {description}
        </div>
    </div>
</div>
HORIZONTAL;

// prepare the featured image / icon
$featured = '';
if ( 'icon' === $type ) {
	$library  = $a['icon_library'];
	$key      = 'icon_' . $library;
	$icon     = $a[ $key ];
	$featured = sprintf( '<i class="%s"></i>', esc_attr( $icon ) );
	unset( $library, $key, $icon );
} else {
	$featured = wp_get_attachment_image( (int) $a['image'], 'full' );
}

// icon box class
$class = nucleus_get_class_set( array(
	'box-with-icon',
	$is_horizontal ? 'bwi-horizontal' : '',
	$is_expandable ? 'bwi-expandable' : '',
	'text-' . $alignment,
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

$r = array(
	'{class}'       => esc_attr( $class ),
	'{featured}'    => $featured,
	'{title}'       => nucleus_get_text( $title, '<h3 class="bwi-title">', '</h3>' ),
	'{description}' => nucleus_get_text( $description, '<p class="bwi-text">', '</p>' ),
);

// define the template based on layout
$template = $is_horizontal ? $horizontal : $vertical;

echo str_replace( array_keys( $r ), array_values( $r ), $template );
