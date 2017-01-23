<?php
/**
 * Gallery | nucleus_gallery
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_gallery_atts', array(
	'images'           => '',
	'is_title'         => 'no',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$is_title  = ( 'yes' === $a['is_title'] );
$images    = wp_parse_id_list( $a['images'] );
$animation = nucleus_parse_array( $a, 'animation_' );
$wrapper   = esc_attr( nucleus_get_class_set( array(
	'gallery-grid',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) ) );

$template = <<<'TEMPLATE'
<a href="{href}" class="gallery-item waves-effect waves-light" {title}>
    <div class="thumbnail" style="{bg}"></div>
    <i class="icon-circle-plus"></i>
</a>
TEMPLATE;

// output
echo '<div class="', $wrapper, '">';
foreach ( (array) $images as $image ) {
	$image = (int) $image;
	$title = '';

	if ( $is_title ) {
		$attachment = nucleus_get_attachment( $image );
		$title      = sprintf( 'data-title="%s"', esc_attr( $attachment['title'] ) );
		unset( $attachment );
	}

	$r = array(
		'{href}'  => esc_url( nucleus_get_image_src( $image ) ),
		'{bg}'    => nucleus_css_background_image( $image, 'large' ),
		'{title}' => $title,
	);

	echo str_replace( array_keys( $r ), array_values( $r ), $template );
}
echo '</div>';
