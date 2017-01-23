<?php
/**
 * Download Counter | nucleus_download_counter
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_download_counter_atts', array(
	'number'           => '',
	'label'            => '',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$number    = absint( $a['number'] );
$label     = esc_html( $a['label'] );
$animation = nucleus_parse_array( $a, 'animation_' );
$class     = esc_attr( nucleus_get_class_set( array(
	'download-counter-wrapper',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) ) );

$template = <<<'TEMPLATE'
<div class="{class}">
  <div class="download-counter">
    <div class="inner">
      {number}
      {label}
    </div>
  </div>
</div>
TEMPLATE;

$r = array(
	'{class}' => $class,
	'{number}' => $number,
	'{label}' => nucleus_get_text( $label, '<small>', '</small>' ),
);

echo str_replace( array_keys( $r ), array_values( $r ), $template );
