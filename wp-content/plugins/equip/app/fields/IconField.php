<?php
namespace Equip\Field;

/**
 * Icon picker
 *
 * @author  8guild
 * @package Equip\Field
 */
class IconField extends Field {

	public function render( $slug, $settings, $value ) {
		// picker settings
		$picker = wp_parse_args( (array) $settings['settings'], $this->get_picker_settings() );
		if ( is_array( $settings['source'] ) ) {
			$picker['source'] = $settings['source'];
		} elseif ( is_string( $settings['source'] ) ) {
			$picker['source'] = equip_get_icons( $settings['source'] );
		}

		// merge input attributes with user-defined ones
		$control = array_merge( [
			'type'          => 'text',
			'name'          => esc_attr( $this->get_name() ),
			'id'            => esc_attr( $this->get_id() ),
			'value'         => $value,
			'data-settings' => $picker,
		], $this->get_attr_array() );

		echo equip_get_tag( 'input', $control );
	}

	public function sanitize( $value, $settings, $slug ) {
		return sanitize_text_field( $value );
	}

	public function escape( $value, $settings, $slug ) {
		return esc_attr( $value );
	}

	public function get_defaults() {
		return [
			'source'   => '',
			'settings' => [],
		];
	}

	public function get_default_attr() {
		return [
			'class' => 'equip-iconpicker',
		];
	}
	
	private function get_picker_settings() {
		return [
			'iconsPerPage' => 20,
			'hasSearch'    => true,
			'emptyIcon'    => true,
		];
	}
}