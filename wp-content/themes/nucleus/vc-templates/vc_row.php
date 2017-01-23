<?php
/**
 * Modification of vc_row shortcode
 *
 * @var array                    $atts    Shortcode attributes
 * @var mixed                    $content Shortcode content
 * @var WPBakeryShortCode_VC_Row $this    Instance of a class
 *
 * @author  8guild
 * @package Nucleus\VC
 */

// no extract(), please
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_vc_row_atts', array(
	'id'              => '',
	'layout'          => 'boxed', // boxed | full
	'skin'            => 'none', // none | default | primary | gray
	'is_overlay'      => 'disable',
	'overlay_opacity' => 65,
	'overlay_color'   => '#000000',
	'is_arrow'        => 'disable',
	'arrow_position'  => 'bottom', // top | bottom
	'is_angle'        => 'disable',
	'angle_position'  => 'bottom-right', // bottom-right, bottom-left, top-left, top-right
	'is_parallax'     => 'disable',
	'parallax_speed'  => 0.65,
	//'parallax_offset' => '',
	'no_padding'      => 'no',
	'class'           => '',
	'css'             => '',
) ), $atts );

$section_class = array();
$section_attr  = array();

$is_full     = ( 'full' === $a['layout'] );
$skin        = sanitize_key( $a['skin'] );
$arrow       = sanitize_key( $a['arrow_position'] );
$is_overlay  = ( 'enable' === $a['is_overlay'] );
$is_arrow    = ( 'enable' === $a['is_arrow'] );
$is_angle    = ( 'enable' === $a['is_angle'] );
$is_parallax = ( 'enable' === $a['is_parallax'] );
$no_padding  = ( 'yes' === $a['no_padding'] );

$section_attr['id'] = esc_attr( $a['id'] );

$section_class[] = 'fw-section';
$section_class[] = 'bg-' . $skin;
$section_class[] = $is_arrow ? 'arrow-' . $arrow : '';
$section_class[] = $no_padding ? 'no-padding' : '';
$section_class[] = trim( vc_shortcode_custom_css_class( $a['css'] ) );
$section_class[] = $a['class'];

// overlay
$overlay_attr = array();
if ( $is_overlay ) {
	$overlay_attr['class'] = 'overlay';
	$overlay_attr['style'] = nucleus_css_declarations( array(
		'opacity'          => nucleus_get_opacity_value( $a['overlay_opacity'] ),
		'background-color' => esc_attr( $a['overlay_color'] ),
	) );
}

// angle
$angle_attr = array();
if ( $is_angle ) {
	$angle_class = nucleus_get_class_set( array(
		'angle',
		'angle-' . esc_attr( $a['angle_position'] ),
	) );

	$angle_attr['class'] = esc_attr( $angle_class );
	$angle_attr['style'] = nucleus_css_background_image( nucleus_get_asset( 'img/angle.png' ) );
}

// parallax
if ( $is_parallax ) {
	$section_class[] = 'bg-parallax';

	$section_attr['data-stellar-background-ratio'] = (float) $a['parallax_speed'];
	//$section_attr['data-stellar-vertical-offset']  = (int) $a['parallax_offset'];
}

$section_attr['class'] = esc_attr( nucleus_get_class_set( $section_class ) );
?>
<section <?php echo nucleus_get_html_attr( $section_attr ); ?>>

	<?php
	// overlay
	if ( $is_overlay ) : nucleus_the_tag( 'span', $overlay_attr, '' ); endif;

	// angle
	if ( $is_angle ) : nucleus_the_tag( 'div', $angle_attr, '' ); endif;
	?>

	<div class="<?php echo esc_attr( $is_full ? 'container-fluid' : 'container' ); ?>">
		<div class="row">
			<?php echo nucleus_do_shortcode( $content ); ?>
		</div>
	</div>
</section>
