<?php
/**
 * Pricing | nucleus_pricing
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_pricing_atts', array(
	'image'                   => '',
	'name'                    => '',
	'description'             => '',
	'is_button'               => 'no',
	'button_text'             => '',
	'button_link'             => '',
	'button_color'            => 'default',
	'button_type'             => 'standard',
	'button_size'             => 'nl',
	'button_alignment'        => 'inline',
	'button_is_full'          => 'no',
	'button_is_icon'          => 'no',
	'button_icon_library'     => 'fontawesome',
	'button_icon_fontawesome' => '',
	'button_icon_openiconic'  => '',
	'button_icon_typicons'    => '',
	'button_icon_entypo'      => '',
	'button_icon_linecons'    => '',
	'button_icon_feather'     => '',
	'button_icon_flaticon'    => '',
	'button_icon_position'    => 'left', // left | right
	'button_is_waves'         => 'disable',
	'button_waves_skin'       => 'dark', // dark | light
	'button_class'            => '',
	'options'                 => '',
	'is_animation'            => 'disable',
	'animation_type'          => 'top',
	'animation_delay'         => 0,
	'animation_easing'        => 'none',
	'class'                   => '',
) ), $atts );

$image_id    = (int) $a['image'];
$name        = esc_html( trim( $a['name'] ) );
$description = wp_kses( trim( $a['description'] ), wp_kses_allowed_html( 'data' ) );
$is_button   = ( 'yes' === $a['is_button'] );
$is_image    = ( ! empty( $image_id ) );
$image       = '';
$button      = '';
$options_m   = '';
$options     = json_decode( urldecode( $a['options'] ), true );
$animation   = nucleus_parse_array( $a, 'animation_' );
$class       = esc_attr( nucleus_get_class_set( array(
	'pricing-plan',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) ) );

// may be add button
if ( $is_button ) {
	$b_atts = nucleus_parse_array( $a, 'button_' );
	$b_sh   = nucleus_shortcode_build( 'nucleus_button', $b_atts );
	$button = nucleus_do_shortcode( $b_sh );
}

// prevent unnecessary markup if image not provided
if ( $is_image ) {
	$img = wp_get_attachment_image( $image_id );
	$image = '<div class="pricing-icon">' . $img . '</div>';
	unset( $img );
}

// get options markup
if ( ! empty( $options ) ) {
	$options_m .= '<ul>';
	foreach ( (array) $options as $option ) {
		$options_m .= sprintf( '<li>%1$s <strong>%2$s</strong></li>',
			esc_html( $option['property'] ),
			esc_html( $option['value'] )
		);
	}
	$options_m .= '</ul>';
	unset( $option );
}

$template = <<<'TEMPLATE'
<div class="{class}">
  <div class="pricing-header">
      {image}
      {name}
      {description}
      {button}
  </div>
  {options}
</div>
TEMPLATE;

$r = array(
	'{class}'       => $class,
	'{image}'       => $image,
	'{name}'        => nucleus_get_text( $name, '<h3 class="pricing-title">', '</h3>' ),
	'{description}' => nucleus_get_text( $description, '<p>', '</p>' ),
	'{button}'      => $button,
	'{options}'     => $options_m,
);

echo str_replace( array_keys( $r ), array_values( $r ), $template );