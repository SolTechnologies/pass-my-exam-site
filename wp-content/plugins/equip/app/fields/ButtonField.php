<?php
namespace Equip\Field;

/**
 * Button field
 *
 * @author  8guild
 * @package Equip\Field
 */
class ButtonField extends Field {

	public function render( $slug, $settings, $value ) {
		$color        = esc_attr( $settings['color'] );
		$text         = esc_html( $settings['text'] );
		$icon         = '';
		$is_icon      = ( ! empty( $settings['icon'] ) );
		$is_right     = ( 'right' === $settings['icon_position'] );
		$is_fullwidth = ( true === $settings['fullwidth'] );

		$class = [];

		$class[] = 'equip-btn';
		$class[] = 'waves-effect';
		$class[] = "btn-{$color}";
		$class[] = ( $is_fullwidth ) ? 'btn-block' : '';
		$class[] = array_key_exists( 'class', $settings['attr'] ) ? $settings['attr']['class'] : '';

		// build an icon
		if ( $is_icon ) {
			// icon position class
			$class[] = $is_right ? 'btn-icon-right' : 'btn-icon-left';
			$i_class = esc_attr( $settings['icon'] );
			$icon    = equip_get_tag( 'i', [ 'class' => $i_class ], '', 'paired' );
			unset( $i_class );
		}

		// button specific attributes
		$btn = [
			'href'   => empty( $settings['url'] ) ? '#' : esc_url( $settings['url'] ),
			'target' => esc_attr( $settings['target'] ),
			'class'  => equip_get_class_set( $class ),
		];

		// get attributes, but remove class, href and target we use it above
		$raw_attr = $this->get_attr_array();
		$raw_attr = array_diff_key( $raw_attr, array_flip( [ 'class', 'href', 'target' ] ) );
		$attr     = array_merge( $btn, $raw_attr );

		// prepare the content, according to the icon position
		$contents = $is_right ? $text . $icon : $icon . $text;

		echo equip_get_tag( 'a', $attr, $contents, 'paired' );
	}

	public function get_defaults() {
		return [
			'text'          => '',
			'url'           => '',
			'target'        => '_self',
			'color'         => 'default', // default | primary | info | success | warning | danger
			'fullwidth'     => false,
			'icon'          => '',
			'icon_position' => 'left', // left | right
		];
	}
}