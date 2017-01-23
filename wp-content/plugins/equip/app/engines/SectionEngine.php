<?php
namespace Equip\Engine;

use Equip\Factory;

/**
 * Engine for rendering "section" layout
 * 
 * @see \Equip\Layout\SectionLayout
 *
 * @author  8guild
 * @package Equip\Engine
 */
class SectionEngine extends Engine {

	/**
	 * Open div.tab-pane
	 * 
	 * @param string               $slug
	 * @param \Equip\Layout\Layout $layout
	 */
	public function before_elements( $slug, $layout ) {
		$settings = $layout->get_settings();
		$classes  = [
			'tab-pane',
			'transition',
			'fade',
			esc_attr( $settings['animation'] ),
			( true === $settings['is_active'] ) ? 'active in' : '',
		];

		$section = [
			'role'         => 'tabpanel',
			'id'           => sanitize_key( $settings['id'] ),
			'class'        => esc_attr( equip_get_class_set( $classes ) ),
			'data-element' => 'section',
		];

		// open the div.tab-pane
		echo '<div ', equip_get_html_attr( $section ), '>';

		// show the section title
		echo equip_get_text( esc_html( $settings['title'] ), '<h2>', '</h2>' );
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
	 * Close the div.tab-pane
	 * 
	 * @param string               $slug
	 * @param \Equip\Layout\Layout $layout
	 */
	public function after_elements( $slug, $layout ) {
		echo '</div>';
	}
}