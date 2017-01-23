<?php
/**
 * Progress Bars | nucleus_progress_bars
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_progress_bars_atts', array(
	'bars'             => '', // param group
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$bars      = json_decode( urldecode( $a['bars'] ), true );
$animation = nucleus_parse_array( $a, 'animation_' );
$class     = esc_attr( nucleus_get_class_set( array(
	'progress-bars',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) ) );

$template = <<<'TEMPLATE'
<div class="{wrapper}">
  <div class="label">
      {label}
      <span class="units">{value}{units}</span>
  </div>
  <div class="{class}" data-valuenow="{value}" style="{strip}"></div>
</div>
TEMPLATE;


echo '<div class="', $class, '">';
foreach ( (array) $bars as $bar ) {
	$bar = wp_parse_args( $bar, array(
		'value'       => '',
		'label'       => '',
		'is_units'    => 'yes',
		'color'       => 'default',
		'is_animated' => 'yes',
	) );

	if ( empty( $bar['value'] ) || ! is_numeric( $bar['value'] ) ) {
		continue;
	}

	$value = absint( $bar['value'] );
	$value = ( $value > 100 ) ? 100 : $value;

	$is_units    = ( 'yes' === $bar['is_units'] );
	$is_animated = ( 'yes' === $bar['is_animated'] );

	$b_w_class = nucleus_get_class_set( array(
		'progress',
		$is_animated ? 'progress-animated' : '',
	) );

	$b_class = nucleus_get_class_set( array(
		'progress-bar',
		'progress-bar-' . sanitize_key( $bar['color'] ),
	) );

	$r = array(
		'{wrapper}' => esc_attr( $b_w_class ),
		'{label}'   => nucleus_get_text( esc_html( $bar['label'] ), '<span>', '</span>' ),
		'{value}'   => $value,
		'{units}'   => $is_units ? '%' : '',
		'{class}'   => $b_class,
		'{strip}'   => nucleus_css_background_image( NUCLEUS_CORE_URI . '/img/strip.png' ),
	);

	echo str_replace( array_keys( $r ),  array_values( $r ), $template );
	unset( $value, $is_units, $is_animated, $b_w_class, $b_class, $r );
}
echo '</div>';
