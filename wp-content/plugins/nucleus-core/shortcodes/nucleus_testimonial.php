<?php
/**
 * Testimonial | nucleus_testimonial
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
 * Filter the "nucleus_testimonial" default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_testimonial_atts', array(
	'image'            => '',
	'name'             => '',
	'position'         => '',
	'quotation'        => '',
	'link'             => '',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$image_id  = (int) $a['image'];
$name      = esc_html( trim( $a['name'] ) );
$position  = esc_html( trim( $a['position'] ) );
$quotation = esc_textarea( stripslashes( trim( $a['quotation'] ) ) );
$link      = nucleus_build_link( $a['link'] );
$is_link   = ( ! empty( $link['url'] ) );
$animation = nucleus_parse_array( $a, 'animation_' );
$class     = nucleus_get_class_set( array(
	'testimonial',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

?>
<div class="<?php echo esc_attr( $class ); ?>">
	<?php if ( ! empty( $image_id ) ) : ?>
		<div class="author-ava">
			<?php echo wp_get_attachment_image( $image_id ); ?>
		</div>
	<?php endif; ?>

	<?php
	nucleus_the_text( $name, '<h3 class="author-name">', '</h3>' );
	nucleus_the_text( $position, '<p class="text-gray">', '</p>' );
	nucleus_the_text( $quotation, '<div class="text">', '</div>' );

	if ( $is_link ) {
		$link_attr = array();

		$link_attr['href']   = esc_url( trim( $link['url'] ) );
		$link_attr['target'] = esc_attr( trim( $link['target'] ) );
		$link_attr['class']  = 'text-sm';

		nucleus_the_tag( 'a', $link_attr, esc_html( trim( $link['title'] ) ) );
	}
	?>
</div>