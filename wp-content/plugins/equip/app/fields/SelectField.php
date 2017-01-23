<?php
namespace Equip\Field;

/**
 * <select> field
 *
 * @author  8guild
 * @package Equip\Field
 */
class SelectField extends Field {

	public function render( $slug, $settings, $value ) {
		// init the attributes array
		$attr = [];

		$attr['name'] = esc_attr( $this->get_name() );
		$attr['id']   = esc_attr( $this->get_id() );

		if ( true === $this->get_setting( 'searchable' ) ) {
			$attr['data-searchable'] = 'true';
		}

		if ( '' !== $this->get_setting( 'placeholder' ) ) {
			$attr['data-placeholder'] = $this->get_setting( 'placeholder' );
		}

		// merge with defaults and ones defined by user
		$attr = array_merge( $attr, $this->get_attr_array() );

		echo '<select ', equip_get_html_attr( $attr ), '>';
		echo $this->get_options();
		echo '</select>';
	}

	public function sanitize( $value, $settings, $slug ) {
		return esc_attr( $value );
	}

	public function escape( $value, $settings, $slug ) {
		return esc_attr( $value );
	}

	/**
	 * Return the options for select field
	 * 
	 * @return string
	 */
	private function get_options() {
		$options = '';
		foreach ( (array) $this->get_setting( 'options', [] ) as $val => $name ) {
			$options .= sprintf( '<option value="%1$s" %3$s>%2$s</option>',
				$val,
				$name,
				selected( $this->get_value(), $val, false )
			);
		}

		return $options;
	}

	public function get_defaults() {
		return [
			'options'     => [],
			'placeholder' => '',
			'searchable'  => false,
		];
	}
}