<?php
/**
 * Animated Digits | nucleus_animated_digits
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_animated_digits_atts', array(
	'image'            => '',
	'digit'            => '',
	'description'      => '',
	'is_featured'      => 'no',
	'duration'         => 1500,
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$image_id    = (int) $a['image'];
$is_image    = ( ! empty( $image_id ) );
$digit       = absint( $a['digit'] );
$description = wp_kses( trim( $a['description'] ), wp_kses_allowed_html( 'data' ) );
$is_featured = ( 'yes' === $a['is_featured'] );
$duration    = absint( $a['duration'] );
$animation   = nucleus_parse_array( $a, 'animation_' );
$class       = nucleus_get_class_set( array(
	'counter',
	$is_featured ? 'featured' : '',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

$attr = array();

$attr['class']         = esc_attr( $class );
$attr['data-duration'] = $duration;

?>
<div <?php echo nucleus_get_html_attr( $attr ); ?>>
	<?php if ( $is_image ) : ?>
		<header class="counter-icon">
			<?php echo wp_get_attachment_image( $image_id ); ?>
		</header>
	<?php endif; ?>

	<?php
	nucleus_the_text( $digit, '<div class="digits">', '</div>' );
	nucleus_the_text( $description, '<footer class="counter-footer">', '</footer>' );
	?>

</div>