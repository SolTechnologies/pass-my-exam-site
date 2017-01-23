<?php
namespace Equip\Field;

/**
 * Textarea field
 *
 * @author  8guild
 * @package Equip\Field
 */
class TextareaField extends Field {

	public function render( $slug, $settings, $value ) {
		/*
		 * 1 - name
		 * 2 - id
		 * 3 - value
		 * 4 - HTML attributes
		 */
		printf( '<textarea name="%1$s" id="%2$s" %4$s>%3$s</textarea>',
			esc_attr( $this->get_name() ),
			esc_attr( $this->get_id() ),
			$value,
			$this->get_attr()
		);
	}

	public function sanitize( $value, $settings, $slug ) {
		// Get allowed HTML tags for wp_kses()
		$allowed_tags = wp_kses_allowed_html( 'data' );
		$value        = stripslashes( trim( $value ) );
		
		return wp_kses( $value, $allowed_tags );
	}

	public function escape( $value, $settings, $slug ) {
		// Get allowed HTML tags for wp_kses()
		$allowed_tags = wp_kses_allowed_html( 'data' );
		$value        = stripslashes( trim( $value ) );

		return wp_kses( $value, $allowed_tags );
	}

	public function get_default_attr() {
		return [
			'rows' => 6,
		];
	}
}