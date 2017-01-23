<?php
/**
 * Split Contacts | nucleus_split_contacts
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_split_contacts_atts', array(
	'map_location'     => '',
	'map_height'       => 500,
	'map_zoom'         => 14,
	'map_is_zoom'      => 'disable',
	'map_is_scroll'    => 'disable',
	'map_is_marker'    => 'disable',
	'map_marker_title' => '',
	'map_marker'       => '', // attachment ID
	'map_style'        => '', // custom base64 encoded styles
	'form_id'          => 0, // cf7 post id
	'form_bg'          => '',
	'form_title'       => '',
	'is_contact'       => 'yes',
	'contact_image'    => '',
	'contact_title'    => '',
	'contact_subtitle' => '',
	'contact_info'     => '', // param group
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$is_map     = ( ! empty( $a['map_location'] ) );
$is_form    = ( ! empty( $a['form_id'] ) );
$is_contact = ( 'yes' === $a['is_contact'] );
$animation  = nucleus_parse_array( $a, 'animation_' );

$map     = '';
$form    = '';
$contact = '';

// build a map
if ( $is_map ) {
	$map_atts = nucleus_parse_array( $a, 'map_' );
	$map_sh   = nucleus_shortcode_build( 'nucleus_map', $map_atts );
	$map      = nucleus_do_shortcode( $map_sh );
	unset( $map_atts, $map_sh );
}

// get the contacts
if ( $is_contact ) {
	$c = nucleus_parse_array( $a, 'contact_' );

	$info  = json_decode( urldecode( $c['info'] ), true );
	$image = '';
	if ( ! empty( $c['image'] ) ) {
		$image = wp_get_attachment_image( (int) $c['image'], 'full' );
		$image = '<div class="cd-icon">' . $image . '</div>';
	}

	$title    = nucleus_get_text( esc_html( $c['title'] ), '<div class="cd-title"><h4>', '</h4></div>' );
	$subtitle = nucleus_get_text( esc_html( $c['subtitle'] ), '<h6>', '</h6>' );
	$list     = '';

	if ( ! empty( $info ) ) {
		$icons = array(
			'addr'  => 'fa fa-map-marker',
			'tel'   => 'fa fa-phone',
			'email' => 'fa fa-envelope',
			'skype' => 'fa fa-skype',
		);

		$list .= '<ul class="list-icon">';
		foreach ( $info as $item ) {
			$list .= sprintf( '<li><i class="%2$s"></i>%1$s</li>',
				wp_kses( $item['value'], wp_kses_allowed_html( 'data' ) ),
				$icons[ $item['type'] ]
			);
		}
		$list .= '</ul>';
	}

	$contact .= '<div class="contact-details text-light">';
	$contact .= '<div class="cd-head">';
	$contact .= $image;
	$contact .= $title;
	$contact .= '</div>';
	$contact .= $subtitle;
	$contact .= $list;
	$contact .= '</div>';

	unset( $c, $image, $title, $subtitle, $list, $info );
}

if ( $is_form ) {
	$cf7_sh = nucleus_shortcode_build( 'contact-form-7', array(
		'id'    => (int) $a['form_id'],
		'title' => esc_html( $a['form_title'] )
	) );

	$form .= nucleus_get_text( esc_html( $a['form_title'] ), '<h4>', '</h4>' );
	$form .= nucleus_do_shortcode( $cf7_sh );
	unset( $cf7_sh );
}

$class = nucleus_get_class_set( array(
	'split-contacts',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

?>
<div class="<?php echo esc_attr( $class ); ?>">
	<div class="column">
		<?php echo $map; ?>
		<?php echo $contact; ?>
	</div>
	<div class="column"
	     style="<?php echo nucleus_css_background_image( (int) $a['form_bg'] ); ?>">
		<?php echo $form; ?>
	</div>
</div>
