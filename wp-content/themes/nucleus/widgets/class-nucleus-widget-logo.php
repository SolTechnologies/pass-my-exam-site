<?php

/**
 * Widget "Nucleus Logo"
 *
 * Display the logo uploaded through the customizer
 */
class Nucleus_Widget_Logo extends WP_Widget {

	/**
	 * Widget id_base
	 *
	 * @var string
	 */
	private $widget_id = 'nucleus_logo';

	public function __construct() {
		$widget_opts = array( 'description' => esc_html__( 'Display the logo uploaded throught the Customizer in all widgetized areas', 'nucleus' ) );
		parent::__construct( $this->widget_id, esc_html__( 'Nucleus Logo', 'nucleus' ), $widget_opts );
	}

	/**
	 * Display the setting update form
	 *
	 * @param array $instance Widget settings
	 *
	 * @return string
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'icon'  => '',
		) );

		$title    = $instance['title'];
		$title_id = $this->get_field_id( 'title' );

		$icon = $instance['icon'];
		$icon_id = $this->get_field_id( 'icon' );

		?>
		<p>
			<label for="<?php echo esc_attr( $title_id ); ?>">
				<?php echo esc_html_x( 'Title', 'widget title', 'nucleus' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $title_id ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			       value="<?php echo esc_attr( $title ); ?>">
			<span class="description">
				<?php echo wp_kses( __( 'Add an icon class in a field above. You can find all icons <a href="http://docs.8guild.com/feather-icons/" target="_blank">here</a>.', 'nucleus' ), array(
					'a' => array( 'href' => true, 'target' => true )
				) ); ?>
			</span>
		</p>
		<p>
			<label for="<?php echo esc_attr( $icon_id ); ?>">
				<?php echo esc_html_x( 'Icon', 'widget icon', 'nucleus' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $icon_id ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'icon' ) ); ?>"
			       value="<?php echo esc_attr( $icon ); ?>">
		</p>
		<?php

		return '';
	}

	/**
	 * Update widget form
	 *
	 * @param array $new_instance New values
	 * @param array $old_instance Old values
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( trim( $new_instance['title'] ) );
		$instance['icon']  = esc_attr( $new_instance['icon'] );

		return $instance;
	}

	/**
	 * Show widget
	 *
	 * @param array $args     Widget args described in {@see register_sidebar()}
	 * @param array $instance Widget settings
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'icon'  => '',
		) );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( ! has_custom_logo() ) {
			return;
		}

		echo $args['before_widget'];

		if ( $title ) {
			$icon = esc_attr( trim( $instance['icon'], " \t\n\r\0\x0B." ) );
			$icon = empty( $icon ) ? '' : nucleus_get_tag( 'i', array( 'class' => $icon ), '' ) . '&nbsp;';
			echo $args['before_title'], $icon, $title, $args['after_title'];
		}
		
		the_custom_logo();
		
		echo $args['after_widget'];
	}
}