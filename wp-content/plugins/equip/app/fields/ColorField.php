<?php
namespace Equip\Field;

/**
 * Color picker field
 *
 * @author  8guild
 * @package Equip\Field
 */
class ColorField extends Field {

	public function render( $slug, $settings, $value ) {
		// TODO: may be add attributes to a wrapper?
		ob_start(); ?>
		<div class="equip-color-field">
			<span class="equip-color-result">{value}</span>
			<input type="text" name="{name}" id="{id}" value="{value}" {attr}>
		</div>
		<?php
		$tpl  = ob_get_clean();
		$data = [
			'{name}'  => esc_attr( $this->get_name() ),
			'{id}'    => esc_attr( $this->get_id() ),
			'{value}' => $value,
			'{attr}'  => $this->get_attr(),
		];

		echo str_replace( array_keys( $data ), array_values( $data ), $tpl );
	}

	public function get_default_attr() {
		return [
			'class' => 'equip-color'
		];
	}
}