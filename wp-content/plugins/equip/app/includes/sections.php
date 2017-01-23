<?php
/**
 * Sections wrapper
 *
 * @author  8guild
 * @package Equip
 */

/**
 * Show the sections navigation and sections content wrapper
 *
 * This action will work every time before all section elements
 * on the each section. Another words we should open the wrapper
 * during the rendering of the first element, and close after the
 * last section
 * 
 * @param string $slug
 * @param \Equip\Layout\SectionLayout $layout
 */
function _equip_sections_navi_before( $layout, $slug ) {
	// check if navi already built
	if ( (bool) $layout->parent->get_flag( 'navi' ) ) {
		return;
	}

	// not all parent element are sections, may be rows, fields, etc..
	$sections = $layout->parent->elements;
	$sections = array_filter( $sections, function( $section ) {
		return ( $section instanceof \Equip\Layout\SectionLayout );
	} );

	if ( empty( $sections ) ) {
		return;
	}

	// set the number of sections on the first run
	$layout->parent->set_setting( '_sections_num', count( $sections ) );

	ob_start();
	?>
	<aside class="equip-sidebar">
		{header}
		<nav class="equip-navi">
			<div class="equip-navi-inner">
				<ul class="nav nav-tabs" role="tablist">
					{tabs}
				</ul>
			</div>
		</nav>
	</aside>
	<article class="equip-content">
		<div class="tab-content">
	<?php
	$tpl = ob_get_clean();
	$r   = [
		'{header}' => _equip_sections_navi_header(),
		'{tabs}'   => _equip_sections_navi_tabs( $sections ),
	];

	// set flag navi=true to parent layout
	// to prevent multiple rendering of the navi for all sections
	$layout->parent->set_flag( 'navi', true );
	
	echo str_replace( array_keys( $r ), array_values( $r ), $tpl );
}

add_action( 'equip/engine/elements/section/before', '_equip_sections_navi_before', 10, 2 );

/**
 * Close the sections content wrapper
 *
 * @param string                      $slug
 * @param \Equip\Layout\SectionLayout $layout
 */
function _equip_sections_navi_after( $layout, $slug ) {
	// increase the current section i
	$i = $layout->parent->get_setting( '_section_i', 0 );
	$i++;
	$layout->parent->set_setting( '_section_i', $i );

	// number of all sections
	$n = (int) $layout->parent->get_setting( '_sections_num' );
	if ( $i < $n ) {
		return;
	}

	?>
		</div><!-- .tab-content -->
	</article>
	<?php
}

add_action( 'equip/engine/elements/section/after', '_equip_sections_navi_after', 10, 2 );

/**
 * Get the sections navi header
 * 
 * @return string
 */
function _equip_sections_navi_header() {
	/**
	 * @var \WP_Theme $theme
	 */
	$theme = wp_get_theme();

	// TODO: may be add filter
	return sprintf( '<div class="equip-project-name"><h1>%1$s <small>%2$s</small></h1></div>',
		$theme->get( 'Name' ),
		$theme->get( 'Version' )
	);
}

/**
 * Get the sections navi tabs
 * 
 * @param array $sections
 *
 * @return string
 */
function _equip_sections_navi_tabs( $sections ) {
	ob_start();
	?>
	<li role="presentation" {is_active}>
		<a href="#{id}" aria-controls="{id}" role="tab" data-toggle="tab">
			{icon}
			{title}
		</a>
		{anchors}
	</li>
	<?php
	$tpl  = ob_get_clean();
	$html = '';
	foreach ( $sections as $section ) {
		$r = [
			'{is_active}' => (bool) $section->get_setting( 'is_active' ) ? 'class="active"' : '',
			'{id}'        => sanitize_key( $section->get_setting( 'id' ) ),
			'{icon}'      => equip_get_text( esc_attr( $section->get_setting( 'icon' ) ), '<i class="', '"></i>' ),
			'{title}'     => esc_html( $section->get_setting( 'title' ) ),
			'{anchors}'   => _equip_sections_navi_anchors( $section ),
		];

		$html .= str_replace( array_keys( $r ), array_values( $r ), $tpl );
	}

	return $html;
}

/**
 * Get the anchors
 *
 * @param \Equip\Layout\SectionLayout $section
 *
 * @return string
 */
function _equip_sections_navi_anchors( $section ) {
	$anchors = $section->elements;
	$anchors = array_filter( (array) $anchors, function( $element ) {
		return ( $element instanceof \Equip\Layout\AnchorLayout );
	} );

	if ( empty( $anchors ) ) {
		return '';
	}

	$html = '';
	$html .= '<ul class="sub-navi">';
	/**
	 * @var \Equip\Layout\AnchorLayout $anchor
	 */
	foreach ( $anchors as $i => $anchor ) {
		$is_active = ( $i === 0 );
		$html .= sprintf( '<li %3$s><a href="#%1$s">%2$s</a></li>',
			sanitize_key( $anchor->get_setting( 'id' ) ),
			esc_html( $anchor->get_setting( 'title' ) ),
			$is_active ? 'class="active"' : ''
		);
		unset( $is_active );
	}
	$html .= '</ul>';

	return $html;
}