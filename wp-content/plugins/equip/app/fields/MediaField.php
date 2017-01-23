<?php
namespace Equip\Field;

/**
 * Single image field
 *
 * @author  8guild
 * @package Equip\Field
 */
class MediaField extends Field {

	public function render( $slug, $settings, $value ) {
		$is_multiple = $settings['multiple'];
		if ( $is_multiple ) {
			// convert to comma-separated string if multiple is enabled
			$value = esc_attr( implode( ',', (array) $value ) );
		}

		// normally, HTML attributes should be applied to the field itself,
		// but in cases, when we haven't got a field itself, attributes will
		// be used for a wrapper.

		// open .equip-media-wrap
		echo '<div ', $this->get_attr(), '>';
		equip_the_tag( 'input', [
			'type'  => 'hidden',
			'class' => 'equip-media-value',
			'name'  => esc_attr( $this->get_name() ),
			'value' => $value,
		] );

		echo '<ul class="equip-media-items">';
		if ( $is_multiple ) {
			$this->attachments();
		} else {
			$this->attachment();
		}

		$this->control();
		echo '</ul>';
		
		// close .equip-media-wrap
		echo '</div>';
	}

	/**
	 * Render single attachment
	 */
	private function attachment() {
		$value = (int) $this->get_value();
		if ( empty( $value ) ) {
			return;
		}

		ob_start();
		?>
		<li class="equip-media-item" data-id="{id}">
			<a href="#" class="equip-media-remove">&times;</a>
			{thumb}
		</li>
		<?php
		$tpl  = ob_get_clean();
		$data = [
			'{id}'    => $value,
			'{thumb}' => wp_get_attachment_image( $value, 'thumbnail' ),
		];

		echo str_replace( array_keys( $data ), array_values( $data ), $tpl );
	}

	/**
	 * Render multiple attachments
	 */
	private function attachments() {
		$value = (array) $this->get_value();
		if ( empty( $value ) ) {
			return;
		}

		ob_start();
		?>
		<li class="equip-media-item" data-id="{id}" style="{image}">
			<a href="#" class="equip-media-remove">&times;</a>
		</li>
		<?php
		$tpl = ob_get_clean();

		// value should be an array, @see escape()
		foreach ( $value as $attachmentID ) {
			$data = [
				'{id}'    => (int) $attachmentID,
				'{image}' => equip_css_background_image( (int) $attachmentID, 'medium' ),
			];

			echo str_replace( array_keys( $data ), array_values( $data ), $tpl );
			unset( $data );
		}
		unset( $attachmentID );
	}

	/**
	 * Render "Add" control
	 *
	 * Acts differently, according to "multiple" key.
	 * If multiple is enabled will add a new images in the end of a set
	 * If multiple is disabled will replace the existing image and value
	 */
	private function control() {
		// allowed attributes
		$allowed = [ 'title', 'button' ];

		// get media attr, remove the restricted and merge with defaults
		$media = $this->get_setting( 'media', [] );
		$media = array_intersect_key( $media, array_flip( $allowed ) );
		$media = wp_parse_args( $media, $this->get_media_attr() );

		// override multiple
		$media['multiple'] = (int) $this->get_setting( 'multiple' );

		// convert all keys to data-*
		// and build a new array with media attributes
		$keys  = equip_datify( array_keys( $media ) );
		$media = array_combine( $keys, array_values( $media ) );
		unset( $keys );

		// user should not be able to change this attributes
		$media = array_merge( $media, [
			'href'  => '#',
			'class' => 'equip-media-add',
		] );

		krsort( $media );

		$tpl = '<li class="equip-media-control">{a}</li>';
		echo str_replace( '{a}', equip_get_tag( 'a', $media, '&#43;', 'paired' ), $tpl );
	}

	/**
	 * If "multiple" key is enabled the value will be converted from
	 * comma-separated string into the array of integers. If disabled,
	 * the value will be a single integer
	 *
	 * @param mixed  $value
	 * @param array  $settings
	 * @param string $slug
	 *
	 * @return array|int
	 */
	public function sanitize( $value, $settings, $slug ) {
		if ( array_key_exists('multiple', $settings )
		     && true === $settings['multiple']
		) {
			$attachments = explode( ',', $value );
			$attachments = array_filter( $attachments, 'is_numeric' );
			$attachments = array_map( 'intval', $attachments );

			$value = $attachments;
		} else {
			// for single image
			$value = (int) $value;
		}

		return $value;
	}

	/**
	 * If "multiple" key is enabled the returned value will be an array,
	 * else value will be a single integer
	 *
	 * @param mixed  $value
	 * @param array  $settings
	 * @param string $slug
	 *
	 * @return array|int
	 */
	public function escape( $value, $settings, $slug ) {
		if ( array_key_exists( 'multiple', $settings )
		     && true === $settings['multiple']
		) {
			// NOTE: before inserting into the control field value
			// should be converted into the comma-separated string
			$value = (array) $value;
			$value = array_filter( $value, 'is_numeric' );
			$value = array_map( 'intval', $value );
		} else {
			$value = (int) $value;
		}

		return $value;
	}

	public function get_defaults() {
		return [
			'multiple' => false,
			'sortable' => false,
			'media'    => [],
		];
	}

	public function get_default_attr() {
		$is_multiple = ( true == $this->get_setting( 'multiple' ) );

		return [
			'class' => $is_multiple ? 'equip-media-wrap equip-multiple' : 'equip-media-wrap equip-single',
		];
	}

	public function get_media_attr() {
		return [
			'title'  => esc_html__( 'Media Library', 'equip' ),
			'button' => esc_html__( 'Select', 'equip' ),
		];
	}
}