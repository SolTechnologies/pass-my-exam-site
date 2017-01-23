<?php
namespace Equip\Engine;

use Equip\Factory;

/**
 * Engine for rendering the "anchor" layout
 *
 * @see \Equip\Layout\AnchorLayout
 *
 * @author  8guild
 * @package Equip\Engine
 */
class AnchorEngine extends Engine {

	/**
	 * Open section.equip-anchor
	 *
	 * @param string               $slug
	 * @param \Equip\Layout\Layout $layout
	 */
	public function before_elements( $slug, $layout ) {
		$settings = $layout->get_settings();
		$section  = [
			'id'           => sanitize_key( $settings['id'] ),
			'class'        => 'equip-anchor scrollspy',
			'data-element' => 'anchor',
		];

		echo '<section ', equip_get_html_attr( $section ), '>';
		echo '<h3>', esc_html( $settings['title'] ), '</h3>';
	}

	public function before_element( $slug, $settings, $layout ) {
		return;
	}

	public function do_element( $slug, $settings, $values, $layout ) {
		$engine = Factory::engine( $layout );
		$engine->render( $slug, $layout, $values );
	}

	public function after_element( $slug, $settings, $layout ) {
		return;
	}

	/**
	 * Close section.equip-anchor
	 *
	 * @param string               $slug
	 * @param \Equip\Layout\Layout $layout
	 */
	public function after_elements( $slug, $layout ) {
		echo '</section>';
	}

}
