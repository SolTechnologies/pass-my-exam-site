<?php
/**
 * Gadgets Slideshow | nucleus_gadgets_slideshow
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
$a = shortcode_atts( apply_filters( 'nucleus_shortcode_gadgets_slideshow_atts', array(
	'title_title'      => '',
	'title_subtitle'   => '',
	'title_tag'        => 'h2',
	'slides'           => '', // param group
	'is_autoplay'      => 'no',
	'delay'            => 3000,
	'is_animation'     => 'disable',
	'animation_type'   => 'top',
	'animation_delay'  => 0,
	'animation_easing' => 'none',
	'class'            => '',
) ), $atts );

$delay       = absint( $a['delay'] );
$slides      = json_decode( urldecode( $a['slides'] ), true );
$is_autoplay = ( 'yes' === $a['is_autoplay'] );
$autoplay    = $is_autoplay ? 'true' : 'false';
$animation   = nucleus_parse_array( $a, 'animation_' );
$unique      = nucleus_get_unique_id( 'gs-' );

// prepare title
$t_atts = nucleus_parse_array( $a, 'title_' );

// visible-when-stack
$t_v_sh  = nucleus_shortcode_build( 'nucleus_block_title', array_merge( $t_atts, array( 'class' => 'visible-when-stack' ) ) );
$visible = nucleus_do_shortcode( $t_v_sh );

// hidden-when-stack
$t_h_sh = nucleus_shortcode_build( 'nucleus_block_title', array_merge( $t_atts, array( 'class' => 'hidden-when-stack' ) ) );
$hidden = nucleus_do_shortcode( $t_h_sh );
unset( $t_atts, $t_v_sh, $t_h_sh );

$class = nucleus_get_class_set( array(
	'feature-tabs',
	nucleus_get_animation_class( $a['is_animation'], $animation ),
	$a['class'],
) );

// prepare the contents
$phones   = array();
$tablets  = array();
$contents = array();
foreach ( $slides as $slide ) {
	$phones[]   = (int) $slide['phone'];
	$tablets[]  = (int) $slide['tablet'];
	$contents[] = array(
		'id'          => $unique . '-' . esc_attr( $slide['id'] ),
		'icon'        => (int) $slide['icon'],
		'title'       => esc_html( $slide['title'] ),
		'description' => wp_kses( $slide['description'], wp_kses_allowed_html( 'data' ) ),
	);
}
unset( $slide );

?>
<div class="<?php echo esc_attr( $class ); ?>">
	
	<?php
	// visible-when-stack
	echo $visible;
	?>
	
	<div class="clearfix">
		<div class="devices">
			<div class="tablet">
				<?php
				/**
				 * Filter the path to tablet mask
				 *
				 * @param string $tablet URI to the tablet mask
				 */
				$tablet_mask = apply_filters( 'nucleus_gadgets_slideshow_tablet_mask', NUCLEUS_CORE_URI . '/img/ipad.png' );
				nucleus_the_tag( 'img', array( 'src' => $tablet_mask, 'alt' => __( 'Tablet', 'nucleus' ) ) );
				?>
				<div class="mask">
					<ul class="screens">
						<?php
						// iterate through the tablets
						$first = reset( $tablets );
						foreach ( $tablets as $k => $tablet ) {
							$tablet_id = $unique . '-ts-' . $k;
							printf( '<li id="%1$s" class="%3$s">%2$s</li>',
								$tablet_id,
								wp_get_attachment_image( $tablet, 'full' ),
								( $first === $tablet ) ? 'active in' : ''
							);

							$contents[ $k ]['tablet'] = $tablet_id;
							unset( $tablet_id );
						}
						unset( $first, $k, $tablet );
						?>
					</ul>
				</div>
			</div>
			<div class="phone">
				<?php
				/**
				 * Filter the path to phone mask
				 *
				 * @param string $phone URI to the phone mask
				 */
				$phone_mask = apply_filters( 'nucleus_gadgets_slideshow_phone_mask', NUCLEUS_CORE_URI . '/img/iphone.png' );
				nucleus_the_tag( 'img', array( 'src' => $phone_mask, 'alt' => __( 'Phone', 'nucleus' ) ) );
				?>
				<div class="mask">
					<ul class="screens">
						<?php
						// iterate through the tablets
						$first = reset( $phones );
						foreach ( $phones as $k => $phone ) :
							$phone_id = $unique . '-ps-' . $k;
							printf( '<li id="%1$s" class="%3$s">%2$s</li>',
								$phone_id,
								wp_get_attachment_image( $phone, 'full' ),
								( $first === $phone ) ? 'active in' : ''
							);

							$contents[ $k ]['phone'] = $phone_id;
							unset( $phone_id );
						endforeach;
						unset( $first, $k, $phone );
						?>
					</ul>
				</div>
			</div>
		</div>
		<div class="tabs text-center">

			<?php
			// hidden-when-stack
			echo $hidden;
			?>

			<ul class="nav-tabs"
			    data-autoswitch="<?php echo esc_attr( $autoplay ); ?>"
			    data-interval="<?php echo esc_attr( $delay ); ?>">
				<?php
				// iterate through the tabs
				$first = reset( $contents );
				foreach ( $contents as $item ) :
					printf( '<li class="%5$s"><a href="#%1$s" data-toggle="tab" data-tablet="#%2$s" data-phone="#%3$s">%4$s</a></li>',
						$item['id'], $item['tablet'], $item['phone'],
						wp_get_attachment_image( $item['icon'] ),
						( $first === $item ) ? 'active' : ''
					);
				endforeach;
				unset( $first, $item );
				?>
			</ul>
			<div class="tab-content">
				<?php
				$template = '<div id="{id}" class="{class}">{title}{desc}</div>';
				$first    = reset( $contents );
				foreach ( $contents as $item ) :
					$r = array(
						'{id}'    => $item['id'],
						'{class}' => ( $first === $item ) ? 'tab-pane transition scale fade in active' : 'tab-pane transition scale fade',
						'{title}' => nucleus_get_text( $item['title'], '<h4>', '</h4>' ),
						'{desc}'  => nucleus_get_text( $item['description'], '<p class="text-gray text-lg">', '</p>' ),
					);

					echo str_replace( array_keys( $r ), array_values( $r ), $template );
				unset( $r );
				endforeach;
				unset( $first, $item, $template );
				?>
			</div>
		</div>
	</div>
</div>
