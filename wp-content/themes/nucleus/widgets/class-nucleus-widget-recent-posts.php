<?php

/**
 * Widget "Nucleus Recent Posts"
 *
 * Shows the latest posts in another manner
 *
 * @uses WP_Widget
 */
class Nucleus_Widget_Recent_Posts extends WP_Widget {

	/**
	 * Widget id_base
	 *
	 * @var string
	 */
	private $widget_id = 'nucleus_recent_posts';

	private $cache_key = 'nucleus_recent_posts';
	private $cache_group = 'nucleus_widgets';

	public function __construct() {
		$widget_opts = array( 'description' => esc_html__( 'Your latest posts in Nucleus style', 'nucleus' ) );
		parent::__construct( $this->widget_id, esc_html__( 'Nucleus Recent Posts', 'nucleus' ), $widget_opts );

		add_action( 'save_post', array( $this, 'flush_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_cache' ) );
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
			'title'      => '',
			'icon'       => '',
			'count'      => 5,
			'is_preview' => 1,
		) );

		$title    = $instance['title'];
		$title_id = $this->get_field_id( 'title' );

		$icon    = $instance['icon'];
		$icon_id = $this->get_field_id( 'icon' );

		$count    = $instance['count'];
		$count_id = $this->get_field_id( 'count' );

		$is_preview = (bool) $instance['is_preview'];
		$preview_id = $this->get_field_id( 'preview_id' );
		
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
			<label for="<?php echo esc_attr( $count_id ); ?>">
				<?php esc_html_e( 'Number of posts', 'nucleus' ); ?>
			</label>
			<input type="number" size="3" id="<?php echo esc_attr( $count_id ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>"
			       value="<?php echo esc_attr( $count ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $preview_id ); ?>">
				<?php esc_html_e( 'Show Featured Image?', 'nucleus' ); ?>
			</label>
			<select class="widefat" id="<?php echo esc_attr( $preview_id ); ?>"
			        name="<?php echo esc_attr( $this->get_field_name( 'is_preview' ) ); ?>">
				<option value="1" <?php selected( true, $is_preview ); ?>><?php esc_html_e( 'Enable', 'nucleus' ); ?></option>
				<option value="0" <?php selected( false, $is_preview ); ?>><?php esc_html_e( 'Disable', 'nucleus' ); ?></option>
			</select>
			<span class="description"><?php esc_html_e( 'Note: this option affects each post in widget.', 'nucleus' ); ?></span>
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

		$instance['title']      = sanitize_text_field( trim( $new_instance['title'] ) );
		$instance['icon']       = esc_attr( trim( $new_instance['icon'] ) );
		$instance['count']      = absint( $new_instance['count'] );
		$instance['is_preview'] = (int) $new_instance['is_preview'];

		// flush cache on update settings
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
			'title'      => '',
			'icon'       => '',
			'count'      => 5,
			'is_preview' => 1,
		) );

		$title      = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$count      = false === (bool) $instance['count'] ? 5 : (int) $instance['count'];
		$is_preview = (bool) $instance['is_preview'];

		/**
		 * Filter the argument for querying Recent Posts widget
		 *
		 * @since 1.0.0
		 *
		 * @param array $args An array of arguments for WP_Query
		 */
		$query = new WP_Query( apply_filters( 'nucleus_widget_recent_posts_args', array(
			'post_status'         => 'publish',
			'no_found_rows'       => true,
			'posts_per_page'      => $count,
			'ignore_sticky_posts' => true,
		) ) );

		ob_start();

		if ( $query->have_posts() ) :

			echo $args['before_widget'];

			if ( $title ) {
				$icon = esc_attr( trim( $instance['icon'], " \t\n\r\0\x0B." ) );
				$icon = empty( $icon ) ? '' : nucleus_get_tag( 'i', array( 'class' => $icon ), '' ) . '&nbsp;';
				echo $args['before_title'], $icon, $title, $args['after_title'];
			}

			while ( $query->have_posts() ) : $query->the_post(); ?>
				<div class="item">

					<?php if ( $is_preview && has_post_thumbnail() ) : ?>
						<div class="thumb">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'thumbnail' ); ?>
							</a>
						</div>
					<?php endif; ?>

					<div class="info">
						<?php the_title(
							sprintf( '<h4><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
							'</a></h4>'
						); ?>
					</div>
				</div>
				<?php
			endwhile;

			echo $args['after_widget'];

		endif;
		wp_reset_postdata();

		if ( ! $this->is_preview() ) {
			$cached[ $this->id ] = nucleus_content_encode( ob_get_flush() );
			wp_cache_set( $this->cache_key, $cached, $this->cache_group );
		} else {
			ob_end_flush();
		}
	}
}