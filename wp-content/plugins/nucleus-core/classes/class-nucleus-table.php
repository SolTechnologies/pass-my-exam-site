<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Nucleus_Table' ) ) :
	/**
	 * Class for building properties table
	 *
	 * @see    Nucleus_CPT_Pricing_Table
	 *
	 * @author 8guild
	 */
	class Nucleus_Table {

		/**
		 * Slug
		 *
		 * @var string
		 */
		private $slug = '';

		/**
		 * Properties
		 *
		 * @var array
		 */
		private $properties = array();

		/**
		 * Types
		 *
		 * @var array
		 */
		private $types = array();

		/**
		 * Values from the database
		 *
		 * @var array
		 */
		private $values = array();

		/**
		 * Constructor
		 */
		public function __construct() {
		}

		/**
		 * Set up the Nucleus_Table properties
		 *
		 * @param string $property Class property
		 * @param mixed  $value    Property value
		 */
		public function set( $property, $value ) {
			if ( property_exists( $this, $property ) ) {
				$this->$property = $value;
			}
		}

		/**
		 * Render
		 */
		public function render() {
			$r = array(
				'{types}' => $this->get_types(),
				'{props}' => $this->get_props(),
			);

			echo str_replace(
				array_keys( $r ),
				array_values( $r ),
				$this->get_template()
			);
		}

		/**
		 * Get HTML template for table
		 *
		 * @return string
		 */
		private function get_template() {
			return '<table>{types}{props}</table>';
		}

		/**
		 * Get HTML markup for Types row
		 *
		 * @return string
		 */
		private function get_types() {
			$html = '';
			$html .= '<tr>';
			$html .= '<td><!-- Empty cell for props --></td>';

			foreach ( (array) $this->types as $type ) {
				$html .= '<th>' . esc_html( $type->name ) . '</th>';
			}

			$html .= '</tr>';

			return $html;
		}

		private function get_props() {
			ob_start();
			?>
			<tr>
				<th>{name}</th>
				<td>{first}</td>
				<td>{second}</td>
			</tr>
			<?php

			$row  = ob_get_clean();
			$rows = array();

			foreach ( (array) $this->properties as $property ) {
				$r = array(
					'{name}'   => esc_html( $property->name ),
					'{first}'  => $this->get_field( $property, $this->types[0] ),
					'{second}' => $this->get_field( $property, $this->types[1] ),
				);

				$rows[] = str_replace( array_keys( $r ), array_values( $r ), $row );
			}

			return implode( '', $rows );
		}

		/**
		 * Get field control for single property
		 *
		 * @param WP_Term $property Property object
		 * @param WP_Term $type     Type object
		 *
		 * @return string
		 */
		private function get_field( $property, $type ) {
			$value = '';
			if ( ! empty( $this->values )
			     && array_key_exists( $property->slug, $this->values )
			     && array_key_exists( $type->slug, $this->values[ $property->slug ] )
			) {
				$value = $this->values[ $property->slug ][ $type->slug ];
				$value = esc_attr( $value );
			}

			return sprintf( '<input type="text" name="%1$s" value="%2$s">',
				$this->get_name( $property->slug, $type->slug ),
				$value
			);
		}

		/**
		 * Get name for the field
		 *
		 * @param string $property Property slug
		 * @param string $type     Type slug
		 *
		 * @return string
		 */
		private function get_name( $property, $type ) {
			return sprintf( '%1$s[%2$s][%3$s]',
				$this->slug,
				$property,
				$type
			);
		}
	}
endif;