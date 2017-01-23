<?php

/**
 * Render the Timetable
 *
 * @see    shortcodes/nucleus_timetable.php
 *
 * @author 8guild
 */
class Nucleus_Time_Table {

	private $clean = array();
	private $raw = array();
	private $rows = array();

	public function __construct() {}

	/**
	 * Set up columns
	 *
	 * @param string|int $key    Column key, may be numeric or string
	 * @param array      $column Column values, according to shortcode attributes
	 */
	public function setColumn( $key, $column ) {
		$this->raw[ $key ] = $column;
	}

	public function render() {
		$this->cleanUp();
		$this->convertColsToRows();

		echo '<table>';
		$this->doColgroup();
		$this->doHeader();
		$this->doRows();
		echo '</table>';
	}

	/**
	 * Remove empty columns
	 */
	private function cleanUp() {
		foreach( $this->raw as $k => $c ) {
			$col = array_filter( $c, function( $v ) {
				if ( empty( $v ) || 'no' === $v || '%5B%5D' === $v ) {
					return false;
				} else {
					return true;
				}
			} );

			if ( empty( $col ) ) {
				continue;
			}

			// keep the original array
			$this->clean[ $k ] = $this->raw[ $k ];
		}
	}

	/**
	 * Convert to more appropriate format
	 */
	private function convertColsToRows() {
		// count the max number of options per column
		$_options = array();
		foreach ( $this->clean as $k => $c ) {
			$_options[ $k ] = json_decode( urldecode( $c['options'] ), true );
		}
		unset( $k, $c );

		$c = array_reduce( $_options, function( $c, $options ) {
			$count = count( $options );

			return $c > $count ? $c : $count;
		} );

		foreach ( $this->clean as $k => $col ) {
			// count current number of options and
			// increase to the largest column
			$options = array_pad( $_options[ $k ], $c, 0 );
			foreach ( (array) $options as $i => $option ) {
				$this->rows[ $i ][ $k ] = $option['item'];
			}
		}
	}

	/**
	 * Render the <colgroup>
	 */
	private function doColgroup() {
		$cols = array();
		foreach ( $this->clean as $k => $c ) {
			if ( 'yes' === $c['is_featured'] ) {
				$col = '<col class="featured">';
			} else {
				$col = '<col>';
			}

			$cols[] = $col;
		}

		echo '<colgroup>', implode( '', $cols ), '</colgroup>';
	}

	/**
	 * Show the Timetable header
	 */
	private function doHeader() {
		$default = array(
			'icon'  => '',
			'title' => '',
		);

		$tds = array();
		foreach ( $this->clean as $k => $c ) {
			// make sure all required keys exists
			$c = wp_parse_args( $c, $default );

			$td = '<td>';
			if ( ! empty( $c['icon'] ) ) {
				$td .= nucleus_get_text( wp_get_attachment_image( (int) $c['icon'], 'full' ),
					'<span class="img-icon">', '</span>'
				);
			}

			$td .= nucleus_get_text( esc_html( $c['title'] ), '<span class="text-bold">', '</span>' );
			$td .= '</td>';

			$tds[] = $td;
		}

		echo '<tr>', implode( '', $tds ), '</tr>';
	}

	/**
	 * Show the options rows
	 */
	private function doRows() {
		foreach ( $this->rows as $row ) {
			$f = reset( $row );
			echo '<tr>';
			foreach ( $row as $col => $item ) {
				if ( empty( $item ) ) {
					echo '<td>', '</td>';
					continue;
				}

				if ( $f === $item ) {
					echo '<td class="text-bold">', esc_html( $item ), '</td>';
				} else {
					echo '<td>', esc_html( $item ), '</td>';
				}
			}
			echo '</tr>';
		}
	}
}