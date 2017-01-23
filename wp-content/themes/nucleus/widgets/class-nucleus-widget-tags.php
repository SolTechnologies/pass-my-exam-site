<?php

class Nucleus_Widget_Tags extends WP_Widget {

	/**
	 * Widget id_base
	 *
	 * @var string
	 */
	private $widget_id = 'nucleus_tags';

	private $cache_key = 'nucleus_tags';
	private $cache_group = 'nucleus_widgets';

	public function __construct() {
		$widget_opts = array( 'description' => esc_html__( 'A list of tags in Nucleus style', 'nucleus' ) );
		parent::__construct( $this->widget_id, esc_html__( 'Nucleus Tags', 'nucleus' ), $widget_opts );

		// flush on post save and delete
		// because user can attach or detach some tags in posts
		add_action( 'save_post', array( $this, 'flush_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_cache' ) );

		// flush when user add or remove the tag
		add_action( 'create_post_tag', array( $this, 'flush_cache') );
		add_action( 'delete_post_tag', array( $this, 'flush_cache' ) );

		// flush when user change theme
		add_action( 'switch_theme', array( $this, 'flush_cache' ) );
	}

	/**
	 * Delete the widget cache
	 */
	public function flush_cache() {
		wp_cache_delete( $this->cache_key, $this->cache_group );
	}

	/**
	 * Output the settings update form
	 *
	 * @param array $instance Current settings
	 *
	 * @return bool
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title'    => '',
			'icon'     => '',
			'limit'    => 5,
			'order_by' => 'name',
			'order'    => 'ASC',
		) );

		$title    = $instance['title'];
		$title_id = $this->get_field_id( 'title' );

		$icon    = $instance['icon'];
		$icon_id = $this->get_field_id( 'icon' );

		$limit    = $instance['limit'];
		$limit_id = $this->get_field_id( 'limit' );

		$order_by    = esc_attr( $instance['order_by'] );
		$order_by_id = $this->get_field_id( 'order_by' );

		$order    = esc_attr( $instance['order'] );
		$order_id = $this->get_field_id( 'order' );

		?>
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
					'a' => array( 'href' => true, 'target' => true )
				) ); ?>
			</span>
		</p>
		<p>
			<label for="<?php echo esc_attr( $limit_id ); ?>">
				<?php esc_html_e( 'Limit tags number to:', 'nucleus' ); ?>
			</label>
			<input type="text" size="3" id="<?php echo esc_attr( $limit_id ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>"
			       value="<?php echo (int) $limit; ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $order_by_id ); ?>">
				<?php esc_html_e( 'Order tags by', 'nucleus' ); ?>
			</label>
			<select class="widefat" id="<?php echo esc_attr( $order_by_id ); ?>"
			        name="<?php echo esc_attr( $this->get_field_name( 'order_by' ) ); ?>">
				<option value="ID" <?php selected( 'ID', $order_by ); ?>><?php esc_html_e( 'ID', 'nucleus' ); ?></option>
				<option value="name" <?php selected( 'name', $order_by ); ?>><?php esc_html_e( 'Name', 'nucleus' ); ?></option>
				<option value="count" <?php selected( 'count', $order_by ); ?>><?php esc_html_e( 'Popularity', 'nucleus' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $order_id ); ?>">
				<?php esc_html_e( 'Order', 'nucleus' ); ?>
			</label>
			<select class="widefat" id="<?php echo esc_attr( $order_id ); ?>"
			        name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
				<option value="ASC" <?php selected( 'ASC', $order ); ?>><?php esc_html_e( 'Ascending', 'nucleus' ); ?></option>
				<option value="DESC" <?php selected( 'DESC', $order ); ?>><?php esc_html_e( 'Descending', 'nucleus' ); ?></option>
			</select>
		</p>
		<?php

		return true;
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

		$instance['title']    = sanitize_text_field( trim( $new_instance['title'] ) );
		$instance['icon']     = esc_attr( $new_instance['icon'] );
		$instance['limit']    = (int) $new_instance['limit'];
		$instance['order_by'] = sanitize_text_field( $new_instance['order_by'] );
		$instance['order']    = sanitize_text_field( $new_instance['order'] );

		// flush cache on settings update
		$this->flush_cache();

		return $instance;
	}

	/**
	 * Show widget
	 *
	 * @param array $args     Widget args described in {@see register_sidebar()}
	 * @param array $instance Widget settings
	 */
	public function widget( $args, $instance ) {
		$cached = false;
		if ( ! $this->is_preview() ) {
			$cached = wp_cache_get( $this->cache_key, $this->cache_group );
		}

		// show cache and exit, if exists
		if ( is_array( $cached ) && array_key_exists( $this->id, $cached ) ) {
			echo nucleus_content_decode( $cached[ $this->id ] );

			return;
		}

		// if cache missing convert var to empty array for further usage
		if ( ! is_array( $cached ) ) {
			$cached = array();
		}

		$instance = wp_parse_args( (array) $instance, array(
			'title'    => '',
			'icon'     => '',
			'limit'    => 5,
			'order_by' => 'name',
			'order'    => 'ASC',
		) );
		
		$title    = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$limit    = (int) $instance['limit'] <= 0 ? '' : absint( $instance['limit'] );
		$order_by = esc_attr( $instance['order_by'] );
		$order    = esc_attr( $instance['order'] );

		$tags = get_tags( array(
			'orderby'      => $order_by,
			'order'        => $order,
			'hide_empty'   => true,
			'hierarchical' => false,
			'number'       => $limit,
		) );

		if ( empty( $tags ) ) {
			return;
		}

		ob_start();

		echo $args['before_widget'];

		if ( $title ) {
			$icon = esc_attr( trim( $instance['icon'], " \t\n\r\0\x0B." ) );
			$icon = empty( $icon ) ? '' : nucleus_get_tag( 'i', array( 'class' => $icon ), '' ) . '&nbsp;';
			echo $args['before_title'], $icon, $title, $args['after_title'];
		}

		foreach( (array) $tags as $tag ) {
			printf( '<a href="%1$s">%2$s</a>',
				esc_url( get_term_link( $tag, $tag->taxonomy ) ),
				esc_html( $tag->name )
			);
		}
		unset( $tag );

		echo $args['after_widget'];

		if ( ! $this->is_preview() ) {
			$cached[ $this->id ] = nucleus_content_encode( ob_get_flush() );
			wp_cache_set( $this->cache_key, $cached, $this->cache_group );
		} else {
			ob_end_flush();
		}
	}

}