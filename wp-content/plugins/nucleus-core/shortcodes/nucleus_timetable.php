<?php
/**
 * Timetable | nucleus_timetable
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_timetable_atts', array(
	'col1_icon'        => '',
	'col1_title'       => '',
	'col1_options'     => '', // param group
	'col1_is_featured' => 'no',
	'col2_icon'        => '',
	'col2_title'       => '',
	'col2_options'     => '',
	'col2_is_featured' => 'no',
	'col3_icon'        => '',
	'col3_title'       => '',
	'col3_options'     => '',
	'col3_is_featured' => 'no',
	'col4_icon'        => '',
	'col4_title'       => '',
	'col4_options'     => '',
	'col4_is_featured' => 'no',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$first     = nucleus_parse_array( $a, 'col1_' );
$second    = nucleus_parse_array( $a, 'col2_' );
$third     = nucleus_parse_array( $a, 'col3_' );
$fourth    = nucleus_parse_array( $a, 'col4_' );
$animation = nucleus_parse_array( $a, 'animation_' );

$table = new Nucleus_Time_Table();
$table->setColumn( 0, $first );
$table->setColumn( 1, $second );
$table->setColumn( 2, $third );
$table->setColumn( 3, $fourth );

$class = esc_attr( nucleus_get_class_set( array(
	'schedule-table',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) ) );

echo '<div class="', $class, '">';
$table->render();
echo '</div>';
