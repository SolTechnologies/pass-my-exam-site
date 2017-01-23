<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Nucleus_Pricing_Table' ) ) :
	/**
	 * Render the Pricing Table
	 *
	 * @see    Nucleus_CPT_Pricing_Table
	 * @see    shortcodes/nucleus_pricing_table.php
	 *
	 * @author 8guild
	 */
	class Nucleus_Pricing_Table {

		private $properties = array();
		private $types = array();
		private $posts = array();

		public function __construct() {
		}

		/**
		 * Set up class properties
		 *
		 * @param string $property Class property name
		 * @param mixed  $value    Class property value
		 */
		public function set( $property, $value ) {
			if ( property_exists( $this, $property ) ) {
				$this->$property = $value;
			}
		}

		public function render() {

			if ( empty( $this->types ) || is_wp_error( $this->types ) || count( $this->types ) < 2 ) {
				return;
			}

			if ( empty( $this->properties ) || is_wp_error( $this->properties ) ) {
				return;
			}

			echo '<table>';
			$this->do_cols();
			$this->do_header();
			$this->do_rows();
			echo '</table>';
		}

		private function do_cols() {
			$columns = array();

			$columns[] = '<col>'; // first column for properties
			foreach ( $this->posts as $post ) {
				if ( ! empty( $post->settings ) && 1 === $post->settings['is_featured'] ) {
					$columns[] = '<col class="featured">';
				} else {
					$columns[] = '<col>';
				}
			}

			echo '<colgroup>', implode( '', $columns ), '</colgroup>';
		}

		/**
		 * Pricing Table header
		 */
		private function do_header() {
			echo '<tr>';
			echo '<td>&nbsp;</td>';

			$cells = array();
			foreach ( $this->posts as $post ) {
				$td = '';

				$title   = esc_html( trim( $post->post_title ) );
				$excerpt = wp_kses( $post->post_excerpt, wp_kses_allowed_html( 'data' ) );

				$button = '';
				if ( ! empty( $post->settings['url'] ) ) {
					$url    = $post->settings['url'];
					$href   = preg_match( '@^https?://@i', $url ) ? esc_url( $url ) : esc_attr( $url );
					$button = nucleus_get_tag( 'a', array(
						'href'  => $href,
						'class' => 'text-sm space-top',
					), esc_html( $post->settings['text'] ) );

					unset( $url, $href );
				}

				$icon = '';
				if ( ! empty( $post->settings['icon'] ) ) {
					$icon = wp_get_attachment_image( (int) $post->settings['icon'] );
				}

				$td .= '<td>';
				$td .= nucleus_get_text( $icon, '<span class="img-icon">', '</span>' );
				$td .= nucleus_get_text( $title, '<span class="text-bold">', '</span>' );
				$td .= nucleus_get_text( $excerpt, '<span class="text-gray space-top">', '</span>' );
				$td .= $button;
				$td .= '</td>';

				$cells[] = $td;
				unset( $td, $title, $excerpt, $button, $icon );
			}

			echo implode( '', $cells );
			echo '</tr>';
		}

		private function do_rows() {


			$rows = array();
			foreach ( $this->properties as $property ) {
				$row = '';

				$row .= '<tr>';
				$row .= sprintf( '<td class="text-gray text-right">%s</td>', esc_html( $property->name ) );

				foreach ( $this->posts as $post ) {
					$props = $this->get_props( $property, $post );

					$row .= nucleus_get_tag( 'td', array(
						'class'      => 'text-bold pp-prop',
						'data-left'  => esc_attr( $props['left'] ),
						'data-right' => esc_attr( $props['right'] ),
					), $this->get_special( $props['left'] ) );
				}

				$row .= '</tr>';

				$rows[] = $row;
				unset( $row, $left, $right );
			}

			echo implode( '', $rows );
		}

		/**
		 * Return props[left, right]
		 *
		 * @param WP_Term $property
		 * @param WP_Post $post
		 *
		 * @return array
		 */
		private function get_props( $property, $post ) {
			// get $types slugs
			$t_left  = $this->types[0]->slug;
			$t_right = $this->types[1]->slug;

			$props = array();

			$props['left'] = '#';
			if ( array_key_exists( $property->slug, $post->properties )
			     && ! empty( $post->properties[ $property->slug ][ $t_left ] )
			) {
				$props['left'] = $post->properties[ $property->slug ][ $t_left ];
			}

			$props['right'] = '#';
			if ( array_key_exists( $property->slug, $post->properties )
			     && ! empty( $post->properties[ $property->slug ][ $t_right ] )
			) {
				$props['right'] = $post->properties[ $property->slug ][ $t_right ];
			}

			return $props;
		}

		private function get_special( $content ) {
			/**
			 * Filter the array of available special keywords
			 *
			 * like "infinity", "available", "not-available"
			 *
			 * @param array $keywords
			 */
			$special = apply_filters( 'nucleus_pricing_table_special_keywords', array(
				'infinity',
				'available',
				'not-available',
			) );

			if ( ! in_array( $content, $special ) ) {
				return $content;
			}

			switch ( $content ) {
				case 'infinity':
					$result = '<span class="infinity"></span>';
					break;

				case 'available':
					$result = '<span class="available"></span>';
					break;

				case 'not-available':
					$result = '<span class="not-available"></span>';
					break;

				default:
					/**
					 * Convert the special keyword
					 *
					 * @param string $content
					 */
					$result = apply_filters( 'nucleus_pricing_table_special', $content );
					break;
			}

			return $result;
		}

	}
endif;