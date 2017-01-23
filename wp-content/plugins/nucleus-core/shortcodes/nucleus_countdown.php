<?php
/**
 * Countdown | nucleus_countdown
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_countdown_atts', array(
	'date'             => '',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

try {
	$datetime = new DateTime( $a['date'] );
	$date = $datetime->format( 'm/d/Y H:i:s' );
} catch ( Exception $e ) {
	trigger_error( $e->getMessage() );

	return;
}

$animation = nucleus_parse_array( $a, 'animation_' );
$class     = esc_attr( nucleus_get_class_set( array(
	'countdown',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) ) );

$attr = array(
	'class'          => $class,
	'data-date-time' => $date,
);

?>
<div <?php echo nucleus_get_html_attr( $attr ); ?>>
	<div class="row">
		<div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
			<div class="row">
				<div class="col-sm-3">
					<div class="item">
						<div class="days">00</div>
						<h4 class="days_ref"><?php _e( 'Days', 'nucleus' ) ?></h4>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="item">
						<div class="hours">00</div>
						<h4 class="hours_ref"><?php _e( 'Hours', 'nucleus' ); ?></h4>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="item">
						<div class="minutes">00</div>
						<h4 class="minutes_ref"><?php _e( 'Minutes', 'nucleus' ); ?></h4>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="item">
						<div class="seconds">00</div>
						<h4 class="seconds_ref"><?php _e( 'Seconds', 'nucleus' ); ?></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
