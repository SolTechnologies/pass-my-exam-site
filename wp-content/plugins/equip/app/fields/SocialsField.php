<?php
namespace Equip\Field;

/**
 * Field "socials"
 *
 * Displays the control which allow to choose
 * the social network and associate a link with it.
 *
 * @author  8guild
 * @package Equip\Field
 */
class SocialsField extends Field {

	public function render( $slug, $settings, $value ) {
		$networks = equip_get_networks();
		if ( empty( $networks ) ) {
			return;
		}

		$name = $this->get_name();
		
		// parse social settings
		$s = wp_parse_args( (array) $settings['settings'], $this->get_socials_settings() );
		// we need them in other methods, so
		$this->set_setting( 'settings', $s );

		// prepare the "add more" control
		$more = [
			'href'  => '#',
			'class' => 'equip-socials-add',
		];

		echo '<div ', $this->get_attr(), '>'; // open .equip-socials-wrap

		if ( is_array( $value ) && count( $value ) > 0 ) :
			echo $this->get_list( $name, $networks, $value );
		else:
			echo $this->get_empty( $name, $networks );
		endif;

		echo '</div>'; // close .equip-socials-wrap
		echo '<br>';
		echo equip_get_tag( 'a', $more, esc_html( $s['more_label'] ), 'paired' );
	}

	/**
	 * Returns a non-empty list of networks => urls pairs
	 *
	 * @param string $name     Field name
	 * @param array  $networks A list of networks from networks.ini
	 * @param array  $socials  User defined list of socials
	 *
	 * @return string
	 */
	private function get_list( $name, $networks, $socials ) {
		$select_name = esc_attr( $name . '[networks][]' );
		$input_name  = esc_attr( $name . '[urls][]' );
		$settings    = $this->get_setting( 'settings' );
		$placeholder = esc_html( $settings['placeholder'] );
		$tpl         = $this->get_tpl();

		$result = '';
		foreach ( $socials as $social => $url ) {
			// prepare options dropdown
			$options = '';
			foreach( $networks as $network => $data ) {
				$selected = $social === $network ? 'selected' : '';
				$options .= sprintf( '<option value="%1$s" %3$s>%2$s</option>',
					esc_attr( $network ), esc_html( $data['name'] ), $selected
				);
				unset( $selected );
			}
			unset( $network, $data );

			$r = [
				'{sname}'       => $select_name,
				'{iname}'       => $input_name,
				'{value}'       => esc_url( $url ),
				'{placeholder}' => $placeholder,
				'{options}'     => $options,
			];

			$result .= str_replace( array_keys( $r ), array_values( $r ), $tpl );
			unset( $options );
		}
		unset( $social, $url );

		return $result;
	}

	/**
	 * Engine empty list of social networks (controls for single network)
	 *
	 * @param string $name     Meta box name
	 * @param array  $networks List of networks from social-networks.ini
	 *
	 * @return string
	 */
	private function get_empty( $name, $networks ) {
		$settings = $this->get_setting( 'settings' );
		$tpl      = $this->get_tpl();

		// prepare options dropdown
		$options = '';
		foreach( $networks as $network => $data ) {
			$options .= sprintf( '<option value="%1$s">%2$s</option>',
				esc_attr( $network ), esc_html( $data['name'] )
			);
		}
		unset( $network, $data );

		$r = [
			'{sname}'       => esc_attr( $name . '[networks][]' ),
			'{iname}'       => esc_attr( $name . '[urls][]' ),
			'{placeholder}' => esc_html( $settings['placeholder'] ),
			'{options}'     => $options,
		];

		return str_replace( array_keys( $r ), array_values( $r ), $tpl );
	}

	/**
	 * Convert input array of user social networks to more suitable format.
	 *
	 * @param array  $socials  Expected multidimensional array with two keys
	 *                         [networks] and [urls], both contains equal
	 *                         number of elements.
	 *
	 * <pre>
	 * [
	 *   networks => [ facebook, twitter, ... ],
	 *   urls     => [ url1, url2, ... ],
	 * ];
	 * </pre>
	 *
	 *
	 * @param array  $settings Field settings
	 * @param string $slug     Element slug
	 *
	 * @return array New format of input array
	 *
	 * <pre>
	 * [
	 *   network  => url,
	 *   facebook => url,
	 *   twitter  => url
	 * ];
	 * </pre>
	 */
	public function sanitize( $socials, $settings, $slug ) {
		if ( 0 === count( (array) $socials ) ) {
			return array();
		}

		// Return empty if networks or url not provided.
		if ( empty( $socials['networks'] ) || empty( $socials['urls'] ) ) {
			return array();
		}

		$result = array();
		// $network is a section name from networks.ini
		array_map( function ( $network, $url ) use ( &$result ) {

			// Just skip iteration if network or url not set
			if ( '' === $network || '' === $url ) {
				return;
			}

			switch ( $network ) {
				case 'email':
					// for use in href with another social links 
					$result[ $network ] = 'mailto:' . equip_sanitize_email( $url );
					break;

				default:
					$result[ $network ] = esc_url_raw( $url );
					break;
			}
		}, $socials['networks'], $socials['urls'] );

		return $result;
	}

	/**
	 * Escape social networks urls.
	 *
	 * This function expects that social networks already sanitized.
	 *
	 * @param array  $socials  List of user defined social networks in format
	 *                         [network => url, ... ]
	 *
	 * @param array  $settings Field settings
	 * @param string $slug     Element name
	 *
	 * @return array
	 */
	public function escape( $socials, $settings, $slug ) {
		if ( ! is_array( $socials ) || empty( $socials ) ) {
			return array();
		}

		$result = array();
		foreach ( (array) $socials as $network => $url ) {
			if ( 'email' === $network ) {
				$result[ $network ] = esc_attr( $url );
			} else {
				$result[ $network ] = esc_url( $url );
			}
		}

		return $result;
	}
	
	public function get_defaults() {
		return [
			'settings' => [],
		];
	}

	public function get_default_attr() {
		return [
			'class' => 'equip-socials-wrap',
		];
	}
	
	private function get_socials_settings() {
		return [
			'more_label'  => esc_html__( 'Add one more network', 'equip' ),
			'placeholder' => esc_html__( 'URL', 'equip' ),
		];
	}

	/**
	 * Returns the template for single control
	 *
	 * @return string
	 */
	private function get_tpl() {
		ob_start();
		?>
		<div class="equip-socials-group">
			<select name="{sname}">{options}</select>
			<input type="text" name="{iname}" placeholder="{placeholder}" value="{value}">
			<a href="#" class="equip-socials-group-remove">&times;</a>
		</div>
		<?php

		return ob_get_clean();
	}
}