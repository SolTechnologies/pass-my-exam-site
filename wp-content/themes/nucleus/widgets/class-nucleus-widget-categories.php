<?php

/**
 * Widget "Nucleus Categories"
 *
 * Display the categories
 *
 * @uses WP_Widget
 */
class Nucleus_Widget_Categories extends WP_Widget {

	/**
	 * Widget id_base
	 *
	 * @var string
	 */
	private $widget_id = 'nucleus_categories';

	private $cache_key = 'nucleus_categories';
	private $cache_group = 'nucleus_widgets';

	public function __construct() {
		$widget_opts = array( 'description' => esc_html__( 'A list of categories in Nucleus style', 'nucleus' ) );
		parent::__construct( $this->widget_id, esc_html__( 'Nucleus Categories', 'nucleus' ), $widget_opts );

		add_action( 'save_post', array( $this, 'flush_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_cache' ) );
		add_action( 'create_category', array( $this, 'flush_cache' ) );
		add_action( 'delete_category', array( $this, 'flush_cache' ) );
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
			'title'           => '',
			'icon'            => '',
			'limit'           => 5,
			'order_by'        => 'name',
			'order'           => 'ASC',
			'is_counter'      => 1,
			'is_hierarchical' => 0,
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

		$is_counter = (bool) $instance['is_counter'];
		$counter_id = $this->get_field_id( 'is_counter' );

		$is_hierarchical = (bool) $instance['is_hierarchical'];
		$hierarchical_id = $this->get_field_id( 'is_hierarchical' );

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
				<?php esc_html_e( 'Limit categories number to:', 'nucleus' ); ?>
			</label>
			<input type="text" size="3" id="<?php echo esc_attr( $limit_id ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>"
			       value="<?php echo (int) $limit; ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $order_by_id ); ?>">
				<?php esc_html_e( 'Order categories by', 'nucleus' ); ?>
			</label>
			<select class="widefat" id="<?php echo esc_attr( $order_by_id ); ?>"
			        name="<?php echo esc_attr( $this->get_field_name( 'order_by' ) ); ?>">
				<option value="ID" <?php selected( 'ID', $order_by ); ?>><?php esc_html_e( 'ID', 'nucleus' ); ?></option>
				<option value="name" <?php selected( 'name', $order_by ); ?>><?php esc_html_e( 'Name', 'nucleus' ); ?></option>
				<option value="slug" <?php selected( 'slug', $order_by ); ?>><?php esc_html_e( 'Slug', 'nucleus' ); ?></option>
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
		<p>
			<input type="checkbox" class="checkbox" <?php checked( true, $is_counter ); ?>
			       id="<?php echo esc_attr( $counter_id ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'is_counter' ) ); ?>">
			<label for="<?php echo esc_attr( $counter_id ); ?>"><?php esc_html_e( 'Show post counts', 'nucleus' ); ?></label>
			<br>

			<input type="checkbox" class="checkbox" <?php checked( true, $is_hierarchical ); ?>
			       id="<?php echo esc_attr( $hierarchical_id ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'is_hierarchical' ) ); ?>">
			<label for="<?php echo esc_attr( $hierarchical_id ); ?>"><?php esc_html_e( 'Show hierarchy', 'nucleus' ); ?></label>
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

		$instance['title']           = sanitize_text_field( trim( $new_instance['title'] ) );
		$instance['icon']            = esc_attr( trim( $new_instance['icon'] ) );
		$instance['limit']           = (int) $new_instance['limit'];
		$instance['order_by']        = sanitize_text_field( $new_instance['order_by'] );
		$instance['order']           = sanitize_text_field( $new_instance['order'] );
		$instance['is_counter']      = isset( $new_instance['is_counter'] ) ? 1 : 0;
		$instance['is_hierarchical'] = isset( $new_instance['is_hierarchical'] ) ? 1 : 0;

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
			'title'           => '',
			'icon'            => '',
			'limit'           => 5,
			'order_by'        => 'name',
			'order'           => 'ASC',
			'is_counter'      => 1,
			'is_hierarchical' => 0,
		) );

		$title           = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$limit           = ( -1 === $instance['limit'] ) ? '' : absint( $instance['limit'] );
		$order_by        = esc_attr( $instance['order_by'] );
		$order           = esc_attr( $instance['order'] );
		$is_counter      = (bool) $instance['is_counter'];
		$is_hierarchical = (bool) $instance['is_hierarchical'];

		ob_start();

		echo $args['before_widget'];

		if ( $title ) {
			$icon = esc_attr( trim( $instance['icon'], " \t\n\r\0\x0B." ) );
			$icon = empty( $icon ) ? '' : nucleus_get_tag( 'i', array( 'class' => $icon ), '' ) . '&nbsp;';
			echo $args['before_title'], $icon, $title, $args['after_title'];
		}

		echo '<ul>';

		/**
		 * Filter the arguments for building the categories list
		 * in Nucleus Categories widget
		 *
		 * @since 1.0.0
		 *
		 * @param array $categories An array of arguments for {@see wp_list_categories}
		 */
		wp_list_categories( apply_filters( 'nucleus_widget_categories_args', array(
			'orderby'            => $order_by,
			'order'              => $order,
			'show_count'         => $is_counter,
			'use_desc_for_title' => true,
			'hierarchical'       => $is_hierarchical,
			'number'             => $limit,
			'title_li'           => '',
			'walker'             => new Nucleus_Category_Walker(),
		) ) );

		echo '</ul>';
		echo $args['after_widget'];

		if ( ! $this->is_preview() ) {
			$cached[ $this->id ] = nucleus_content_encode( ob_get_flush() );
			wp_cache_set( $this->cache_key, $cached, $this->cache_group );
		} else {
			ob_end_flush();
		}
	}
}
