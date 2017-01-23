<?php
/**
 * Section | vc_tta_section
 *
 * Supports vc_tta_tabs & vc_tta_accordion
 *
 * @var array                            $atts    Shortcode attributes
 * @var mixed                            $content Shortcode content
 * @var WPBakeryShortCode_VC_Tta_Section $this
 */

$a = shortcode_atts( apply_filters( 'nucleus_shortcode_vc_tta_section_atts', array(
	'title'     => '',
	'tab_id'    => '',
	'animation' => 'fade',
	'el_class'  => '',
) ), $atts );

$this->resetVariables( $a, $content );
WPBakeryShortCode_VC_Tta_Section::$self_count++;
WPBakeryShortCode_VC_Tta_Section::$section_info[] = $a;

// check the parent shortcode
$parent = WPBakeryShortCode_VC_Tta_Section::$tta_base_shortcode;
if ( 'vc_tta_accordion' === $parent->shortcode ) {
	$is_active = ( WPBakeryShortCode_VC_Tta_Section::$self_count <= 1 );

	// accordion ID
	$parent_id = esc_attr( $parent->atts['unique'] );

	// panel ID, also used as a target
	$panel_id = esc_attr( nucleus_get_unique_id( 'panel-' ) );

	// wrapper and heading attributes
	$w_class = nucleus_get_class_set( array( 'panel', $a['el_class'] ) );
	$h_class = nucleus_get_class_set( array( 'panel-title', $is_active ? '' : 'collapsed' ) );
	$heading = array(
		'href'        => '#' . $panel_id,
		'class'       => esc_attr( $h_class ),
		'data-toggle' => 'collapse',
		'data-parent' => '#' . $parent_id,
	);

	// panel class and attributes
	$p_class = nucleus_get_class_set( array( 'panel-collapse', 'collapse', $is_active ? 'in' : '' ) );
	$panel   = array( 'id' => $panel_id, 'class' => esc_attr( $p_class ) );

	$template = <<<'TEMPLATE'
<div class="{wrapper}">
	<div class="panel-heading">{heading}</div>
	<div {panel-attr}>
		<div class="panel-body">{content}</div>
	</div>
</div>
TEMPLATE;

	$r = array(
		'{wrapper}'    => esc_attr( $w_class ),
		'{heading}'    => nucleus_get_tag( 'a', $heading, esc_html( $a['title'] ) ),
		'{panel-attr}' => nucleus_get_html_attr( $panel ),
		'{content}'    => $this->getTemplateVariable( 'content' ),
	);

	echo str_replace( array_keys( $r ), array_values( $r ), $template );

} elseif ( 'vc_tta_tabs' === $parent->shortcode ) {
	$is_active    = ( WPBakeryShortCode_VC_Tta_Section::$self_count <= 1 );
	$is_animation = ( 'fade' !== $a['animation'] );
	$class        = nucleus_get_class_set( array(
		'tab-pane',
		'transition',
		'fade',
		$is_animation ? sanitize_key( $a['animation'] ) : '',
		$is_active ? 'in active' : '',
		$a['el_class'],
	) );

	$tab = array(
		'id'    => esc_attr( $this->getTemplateVariable( 'tab_id' ) ),
		'class' => esc_attr( $class ),
		'role'  => 'tabpanel',
	);
	
	echo nucleus_get_tag( 'div', $tab, $this->getTemplateVariable( 'content' ) );
}


