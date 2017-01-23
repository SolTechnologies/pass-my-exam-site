<?php
/**
 * Pricing Table | nucleus_pricing_table
 *
 * @var array $atts    Shortcode attributes
 * @var mixed $content Shortcode content
 *
 * @author 8guild
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Filter the default shortcode attributes
 *
 * @param array $atts Pairs of default attributes
 */
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_pricing_table_atts', array(
	'is_title'           => 'no',
	'title_title'        => '',
	'title_subtitle'     => '',
	'title_tag'          => 'h2',
	'title_alignment'    => 'left',
	'title_class'        => '',
	'is_switch'          => 'no',
	'switch_label'       => '',
	'switch_type'        => 'text', // text | image
	'switch_text_left'   => '',
	'switch_text_right'  => '',
	'switch_image_left'  => '',
	'switch_image_right' => '',
	'is_animation'       => 'disable',
	'animation_type'     => 'top',
	'animation_delay'    => 0,
	'animation_easing'   => 'none',
	'class'              => '',
) ), $atts );


$is_title  = ( 'yes' === $a['is_title'] );
$is_switch = ( 'yes' === $a['is_switch'] );

$title  = '';
$switch = '';

$animation = nucleus_parse_array( $a, 'animation_' );
$class     = esc_attr( nucleus_get_class_set( array(
	'pricing-table-wrapper',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) ) );

$types = get_terms( array(
	'taxonomy'     => 'nucleus_pricing_type',
	'hide_empty'   => false,
	'hierarchical' => false,
	'number'       => 2,
	'orderby'      => 'term_id',
	'order'        => 'ASC',
) );

$properties = get_terms( array(
	'taxonomy'     => 'nucleus_pricing_properties',
	'hide_empty'   => false,
	'hierarchical' => false,
	'orderby'      => 'term_id',
	'order'        => 'ASC',
) );

// block title shortcode
if ( $is_title ) {
	$t_atts = nucleus_parse_array( $a, 'title_' );
	$t_sh   = nucleus_shortcode_build( 'nucleus_block_title', $t_atts );
	$title  = nucleus_do_shortcode( $t_sh );
	unset( $t_atts, $t_sh );
}

// switcher
if ( $is_switch ) {

	$type  = $a['switch_type'];
	$label = esc_html( $a['switch_label'] );

	if ( 'image' === $type ) {
		$left  = wp_get_attachment_image( (int) $a['switch_image_left'] );
		$right = wp_get_attachment_image( (int) $a['switch_image_right'] );
	} else {
		$left  = nucleus_get_text( esc_html( $a['switch_text_left'] ), '<span>', '</span>' );
		$right = nucleus_get_text( esc_html( $a['switch_text_right'] ), '<span>', '</span>' );
	}

	$template = <<<'TEMPLATE'
<div class="switch-wrap text-right mobile-center space-bottom">
    {label}
    <div class="switch-inner">
        {left}
        <div class="switch">
            <div><span class="knob"></span></div>
            <input type="hidden" value="off">
        </div>
        {right}
    </div>
</div>
TEMPLATE;

	$r = array(
		'{label}' => nucleus_get_text( $label, '<span class="label hidden-xs">', '</span>' ),
		'{left}'  => $left,
		'{right}' => $right,
	);
	
	$switch = str_replace( array_keys( $r ), array_values( $r ), $template );
}

// get posts
/**
 * Filter the args for {@see get_posts()}
 *
 * @param array $args Arguments
 */
$posts = get_posts( apply_filters( 'nucleus_shortcode_pricing_table_posts_args', array(
	'post_type'      => 'nucleus_pricing',
	'posts_per_page' => - 1,
	'orderby'        => 'ID',
	'order'          => 'ASC',
) ) );

// set up metadata
foreach ( $posts as &$post ) {
	$post->properties = get_post_meta( $post->ID, '_nucleus_plan_properties', true );
	$post->settings   = wp_parse_args( (array) get_post_meta( $post->ID, '_nucleus_plan_settings', true ), array(
		'url'         => '',
		'text'        => __( 'Buy now', 'nucleus' ),
		'icon'        => '',
		'is_featured' => 0,
	) );
}
unset( $post );

// set up table
$table = new Nucleus_Pricing_Table();
$table->set( 'properties', $properties );
$table->set( 'types', $types );
$table->set( 'posts', $posts );

// start output
if ( $is_title || $is_switch ) : ?>

	<div class="row">

		<?php if ( $is_title ) : ?>
		<div class="col-lg-4 col-md-5 col-sm-6 mobile-center">
			<?php echo $title; ?>
		</div>
		<?php endif; ?>

		<?php if ( $is_switch ) : ?>
		<div class="col-lg-8 col-md-7 col-sm-6">
			<div class="padding-top hidden-xs"></div>
			<?php echo $switch; ?>
		</div>
		<?php endif; ?>
		
	</div>
	
	<div class="pricing-table space-top">
		<?php $table->render(); ?>
	</div>

<?php endif;