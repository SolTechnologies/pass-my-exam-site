<?php
/**
 * Wrapper shortcode for vc_tta_tabs & vc_tta_accordion
 *
 * @var array $atts    Shortcode attributes
 * @var mixed $content Shortcode content
 *
 * @var WPBakeryShortCode_VC_Tta_Accordion|WPBakeryShortCode_VC_Tta_Tabs $this
 */

$a = shortcode_atts( apply_filters( 'nucleus_shortcode_vc_tta_global_atts', array(
	'alignment' => 'left',
	'unique'    => nucleus_get_unique_id( 'tta-' ),
	'el_class'  => '',
	'css'       => '',
) ), $atts );

$this->resetVariables( $a, $content );
$this->setGlobalTtaInfo();

// without this tabs & accordions won't work!
$content = $this->getTemplateVariable( 'content' );

if ( 'vc_tta_accordion' == $this->shortcode ) {
	
	$w_class = esc_attr( nucleus_get_class_set( array(
		'panel-group',
		trim( vc_shortcode_custom_css_class( $a['css'] ) ),
		$a['el_class'],
	) ) );

	echo '<div ', nucleus_get_html_attr( array( 'id' => $a['unique'], 'class' => $w_class ) ), '>';
	echo nucleus_content_encode( $content );
	echo '</div>';

} elseif ( 'vc_tta_tabs' === $this->shortcode ) {
	$w_class = nucleus_get_class_set( array(
		'text-' . sanitize_key( $a['alignment'] ),
		trim( vc_shortcode_custom_css_class( $a['css'] ) ),
		$a['el_class']
	) );
	
	$tabs = array();
	foreach ( WPBakeryShortCode_VC_Tta_Section::$section_info as $nth => $s ) {
		$is_active = ( $nth === 0 );
		$tab       = array(
			'href'        => '#' . esc_attr( $s['tab_id'] ),
			'role'        => 'tab',
			'data-toggle' => 'tab',
		);

		$link   = nucleus_get_tag( 'a', $tab, esc_html( $s['title'] ) );
		$tabs[] = nucleus_get_tag( 'li', array( 'class' => $is_active ? 'active' : '' ), $link );
		unset( $is_active, $tab, $link );
	}
	unset( $nth, $s );

	$template = <<<'TEMPLATE'
<div class="{wrapper}">
	<ul class="nav-tabs" role="tablist">{tabs}</ul>
</div>
<div class="tab-content">
	{content}
</div>
TEMPLATE;

	$r = array(
		'{wrapper}' => esc_attr( $w_class ),
		'{tabs}'    => implode( '', $tabs ),
		'{content}' => $content,
	);

	echo str_replace( array_keys( $r ), array_values( $r ), $template );

} else {
	return;
}

