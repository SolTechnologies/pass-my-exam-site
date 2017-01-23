<?php

/**
 * Widget "Nucleus Custom Menu"
 *
 * @see WP_Widget
 */
class Nucleus_Widget_Menu extends WP_Widget {

	/**
	 * Widget id_base
	 *
	 * @var string
	 */
	private $widget_id = 'nucleus_custom_menu';

	public function __construct() {
		$widget_opts = array(
			'description'                 => esc_html__( 'Add a custom menu to your sidebar.', 'nucleus' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( $this->widget_id, esc_html__( 'Nucleus Custom Menu', 'nucleus' ), $widget_opts );
	}

	/**
	 * Outputs the settings form for the Custom Menu widget.
	 *
	 * @param array $instance Current settings.
	 *
	 * @return bool
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title'    => '',
			'icon'     => '',
			'nav_menu' => 0,
		) );

		$title    = $instance['title'];
		$title_id = $this->get_field_id( 'title' );

		$icon    = $instance['icon'];
		$icon_id = $this->get_field_id( 'icon' );

		$nav_menu    = (int) $instance['nav_menu'];
		$nav_menu_id = $this->get_field_id( 'nav_menu' );

		// get menus
		$menus = wp_get_nav_menus();

		// If no menus exists, direct the user to go and create some.
		?>
		<p class="nav-menu-widget-no-menus-message" <?php if ( ! empty( $menus ) ) { echo ' style="display:none" '; } ?>>
			<?php
			if ( isset( $GLOBALS['wp_customize'] ) && $GLOBALS['wp_customize'] instanceof WP_Customize_Manager ) {
				$url = 'javascript: wp.customize.panel( "nav_menus" ).focus();';
			} else {
				$url = admin_url( 'nav-menus.php' );
			}

			echo sprintf( '%1$s <a href="%2$s">%3$s</a>.',
				esc_html__( 'No menus have been created yet.', 'nucleus' ),
				esc_attr( $url ),
				esc_html__( ' Create some', 'nucleus' )
			);
			?>
		</p>
		<div class="nav-menu-widget-form-controls" <?php if ( empty( $menus ) ) { echo ' style="display:none" '; } ?>>
			<p>
				<label for="<?php echo esc_attr( $title_id ); ?>">
					<?php echo esc_html_x( 'Title', 'widget title', 'nucleus' ); ?>
				</label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $title_id ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				       value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr( $icon_id ); ?>">
					<?php echo esc_html_x( 'Icon', 'widget icon', 'nucleus' ); ?>
				</label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $icon_id ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( 'icon' ) ); ?>"
				       value="<?php echo esc_attr( $icon ); ?>">
				<span class="description">
					<?php echo wp_kses( __( 'Add an icon class in a field above. You can find all icons <a href="http://docs.8guild.com/feather-icons/" target="_blank">here</a>.', 'nucleus' ), array(
						'a' => array( 'href' => true, 'target' => true ),
					) ); ?>
				</span>
			</p>
			<p>
				<label for="<?php echo esc_attr( $nav_menu_id ); ?>">
					<?php echo esc_html_x( 'Select Menu', 'widget', 'nucleus' ); ?>
				</label>
				<select id="<?php echo esc_attr( $nav_menu_id ); ?>"
				        name="<?php echo esc_attr( $this->get_field_name( 'nav_menu' ) ); ?>">
					<option value="0"><?php esc_html_e( '&mdash; Select &mdash;', 'nucleus' ); ?></option>
					<?php foreach ( $menus as $menu ) : ?>
						<option value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $nav_menu, $menu->term_id ); ?>>
							<?php echo esc_html( $menu->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>
		<?php
	}

	/**
	 * Handles updating settings for the current Custom Menu widget instance.
	 *
	 * @param array $new_instance New settings
	 * @param array $old_instance Old settings
	 *
	 * @return array Updated settings to save
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']    = sanitize_text_field( trim( $new_instance['title'] ) );
		$instance['icon']     = esc_attr( trim( $new_instance['icon'] ) );
		$instance['nav_menu'] = (int) $new_instance['nav_menu'];

		return $instance;
	}

	/**
	 * Outputs the content for the current Custom Menu widget instance.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Custom Menu widget instance.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title'    => '',
			'icon'     => '',
			'nav_menu' => 0,
		) );


		if ( empty( $instance['nav_menu'] ) ) {
			return;
		}

		// get menu
		$nav_menu = wp_get_nav_menu_object( $instance['nav_menu'] );
		$title    = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		if ( $title ) {
			$icon = esc_attr( trim( $instance['icon'], " \t\n\r\0\x0B." ) );
			$icon = empty( $icon ) ? '' : nucleus_get_tag( 'i', array( 'class' => $icon ), '' ) . '&nbsp;';
			echo $args['before_title'], $icon, $title, $args['after_title'];
		}

		$nav_menu_args = array(
			'fallback_cb' => '',
			'menu'        => $nav_menu
		);

		/**
		 * Filter the arguments for the Custom Menu widget.
		 *
		 * @param array    $nav_menu_args An array of arguments passed to wp_nav_menu() to retrieve a custom menu.
		 * @param stdClass $nav_menu      Nav menu object for the current menu.
		 * @param array    $args          Display arguments for the current widget.
		 * @param array    $instance      Array of settings for the current widget.
		 */
		wp_nav_menu( apply_filters( 'nucleus_widget_menu_args', $nav_menu_args, $nav_menu, $args, $instance ) );

		echo $args['after_widget'];
	}
}
