<?php
/**
 * Video Popup Tile | nucleus_video_popup_tile
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
 * Filter the "nucleus_video_popup_tile" default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_video_popup_tile_atts', array(
	'cover'            => '',
	'video'            => '',
	'title'            => '',
	'description'      => '',
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

if ( empty( $a['video'] ) ) {
	return;
}

$cover_id    = (int) $a['cover'];
$video       = esc_url( trim( $a['video'] ) );
$title       = esc_html( trim( $a['title'] ) );
$description = wp_kses( trim( $a['description'] ), wp_kses_allowed_html( 'data' ) );
$animation   = nucleus_parse_array( $a, 'animation_' );
$class       = nucleus_get_class_set( array(
	'video-popup-tile',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

$video_attr = array(
	'href'  => $video,
	'class' => 'play-btn waves-effect waves-light',
);

?>
<div class="<?php echo esc_attr( $class ); ?>">
	<div class="thumb">
		<?php echo wp_get_attachment_image( $cover_id, 'medium' ); ?>
		<div class="inner">
			<?php nucleus_the_tag( 'a', $video_attr, '<i class="icon-play"></i>' ); ?>
		</div>
	</div>
	<?php
	nucleus_the_text( $title, '<h3 class="video-popup-tile-title">', '</h3>' );
	nucleus_the_text( $description, '<p>', '</p>' );
	?>
</div>
