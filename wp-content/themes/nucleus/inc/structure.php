<?php
/**
 * Functions that are responsible for the HTML structure
 *
 * @author  8guild
 * @package Nucleus
 */

if ( ! function_exists( 'nucleus_the_preloader' ) ) :
	/**
	 * Optionally display the preloader
	 *
	 * @see header.php
	 */
	function nucleus_the_preloader() {
		if ( ! nucleus_is_preloader() ) {
			return;
		}

		$a = nucleus_get_options_slice( 'global_preloader_' );
		/**
		 * Filter the preloader attributes
		 *
		 * @param array $atts Preloader attributes
		 */
		$a = apply_filters( 'nucleus_the_preloader_atts', wp_parse_args( (array) $a, array(
			'spinner' => 'spinner1',
			'bg'      => '#ffffff',
			'skin'    => 'dark',
		) ) );

		$preloader = array(
			'id'            => 'preloader',
			'class'         => 'preloader-' . sanitize_key( $a['skin'] ),
			'data-spinner'  => esc_attr( $a['spinner'] ),
			'data-screenbg' => esc_attr( $a['bg'] ),
		);

		echo nucleus_get_tag( 'div', $preloader, '' );
	}
endif;

add_action( 'nucleus_header_before', 'nucleus_the_preloader' );

if ( ! function_exists( 'nucleus_the_social_bar' ) ) :
	/**
	 * Display the social bar
	 */
	function nucleus_the_social_bar() {
		if ( ! nucleus_is_social_bar() ) {
			return;
		}

		$socials = nucleus_get_option( 'global_social_bar_socials', array() );
		if ( empty( $socials ) ) {
			return;
		}

		$class = array(
			'social-bar',
			'bar-fixed-' . sanitize_key( nucleus_get_option( 'global_social_bar_pos', 'right' ) ),
		);

		?>
		<div class="<?php echo nucleus_get_class_set( $class ); ?>">
			<?php nucleus_the_socials( $socials ); ?>
		</div>
		<?php
	}
endif;

add_action( 'nucleus_header_before', 'nucleus_the_social_bar' );

if ( ! function_exists( 'nucleus_page_wrapper_before' ) ) :
	/**
	 * Open the div.page-wrapper block.
	 *
	 * Should be opened first right after the <body> tag to wrap the whole page
	 *
	 * @see header.php
	 * @see nucleus_page_wrapper_after
	 */
	function nucleus_page_wrapper_before() {
		echo '<div class="page-wrapper">';
	}
endif;

add_action( 'nucleus_header_before', 'nucleus_page_wrapper_before', 999 );

if ( ! function_exists( 'nucleus_page_title' ) ) :
	/**
	 * Display the page title
	 *
	 * May be used within or outside the Loop
	 */
	function nucleus_page_title() {
		if ( nucleus_is_slideshow() || ! nucleus_is_page_title() ) {
			return;
		}

		if ( 'posts' == get_option( 'show_on_front') && is_home() ) {
			// for home page without static page
			$title = esc_html__( 'Blog', 'nucleus' );
		} elseif ( is_home() || is_front_page() || is_page() ) {
			// applicable for home with static page, for front page and single page
			$title = single_post_title( '', false );
		} elseif ( is_single() && 'nucleus_portfolio' !== get_post_type() ) {
			// use the "Custom Title" from "Page Settings" meta box
			// for single posts
			$title = nucleus_get_page_setting( 'custom_title', '' );
		} elseif ( is_search() ) {
			// search results
			// NOTE: translators, there is a space after "Results: "
			$title = nucleus_get_text( esc_html( get_search_query() ), esc_html__( 'Results: ', 'nucleus' ) );
		} elseif ( is_archive() ) {
			// archive page
			$title = get_the_archive_title();
		} else {
			$title = get_the_title();
		}

		/**
		 * Filter the Page Title
		 *
		 * @param string $title Page Title
		 */
		$title = apply_filters( 'nucleus_page_title', $title );

		// show nothing if page title not provided
		if ( empty( $title ) ) {
			return;
		}

		// prepare classes
		$classes = array();
		$classes[] = 'page-title';
		$classes[] = nucleus_is_navbar_fullwidth() ? 'pt-fullwidth' : '';

		/**
		 * Filter the page title class
		 *
		 * @param array $classes Page title classes
		 */
		$classes = apply_filters( 'nucleus_page_title_class', $classes );

		$class = nucleus_get_class_set( $classes );
		?>
		<section class="<?php echo esc_attr( $class ); ?>">
			<div class="container">
				<div class="inner">
					<div class="column">
						<?php nucleus_the_text( $title, '<div class="title"><h1>', '</h1></div>' ); ?>
					</div>

					<div class="column">
						<?php nucleus_the_breadcrumbs(); ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
endif;

add_action( 'nucleus_header_after', 'nucleus_page_title' );

if ( ! function_exists( 'nucleus_posts_pagination' ) ) :
	/**
	 * Prints the HTML markup for blog posts pagination.
	 *
	 * @uses nucleus_get_class_set
	 * @uses paginate_links
	 */
	function nucleus_posts_pagination() {
		global $wp_query;

		$total = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
		if ( $total < 2 ) {
			return;
		}

		$classes = array();

		$classes[] = 'pagination';
		$classes[] = 'space-top-2x';

		/**
		 * Filter the classes for posts pagination.
		 *
		 * @param array $classes A list of extra classes
		 */
		$classes = apply_filters( 'nucleus_posts_pagination_class', $classes );
		$classes = nucleus_get_class_set( $classes );

		?>
		<section class="<?php echo esc_attr( $classes ); ?>">
			<div class="nav-links">
				<?php
				/**
				 * Filter the arguments passed to {@see paginate_links}
				 *
				 * @param array $args Arguments for {@see paginate_links}
				 */
				echo paginate_links( apply_filters( 'nucleus_posts_pagination_args', array(
					'type'      => 'plain',
					'mid_size'  => 2,
					'prev_next' => false,
				) ) );
				?>
			</div>
		</section>
		<?php
	}
endif;

add_action( 'nucleus_loop_after', 'nucleus_posts_pagination' );

if ( ! function_exists( 'nucleus_scroll_to_top' ) ) :
	/**
	 * Display scroll to top button
	 *
	 * @see footer.php
	 *
	 * @uses nucleus_is_scroll_to_top()
	 */
	function nucleus_scroll_to_top() {
		if ( ! nucleus_is_scroll_to_top() ) {
			return;
		}

		?>
		<a href="#" class="scroll-to-top-btn">
			<i class="icon-arrow-up"></i>
		</a>
		<?php
	}
endif;

add_action( 'nucleus_footer_before', 'nucleus_scroll_to_top' );

if ( ! function_exists( 'nucleus_the_modal' ) ) :
	/**
	 * Display the log in modal form
	 */
	function nucleus_the_modal() {
		if ( is_user_logged_in() ) {
			return;
		}

		?>
		<div class="modal fade" id="loginModal" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<button type="button" class="close-btn" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="modal-content text-center">
					<h4><?php esc_html_e( 'Login', 'nucleus' ); ?></h4>
					<form method="post" action="<?php echo esc_url( wp_login_url() ); ?>" name="loginform" class="login-form">
						<div class="form-group">
							<input type="text" name="log" class="form-control" placeholder="<?php esc_html_e( 'Username', 'nucleus' ); ?>">
						</div>
						<div class="form-group">
							<input type="password" name="pwd" class="form-control" placeholder="<?php esc_html_e( 'Password', 'nucleus' ); ?>">
						</div>
						<div class="form-footer">
							<p class="forgetmenot">
								<label class="checkbox" for="rememberme">
									<input type="checkbox" id="rememberme">
									<?php esc_html_e( 'Remember me', 'nucleus' ); ?>
								</label>
							</p>
							<p class="submit">
								<button type="submit" name="wp-submit" id="wp-submit"
								        class="btn login-btn btn-default waves-effect waves-light"><?php
									esc_html_e( 'Login into your account', 'nucleus' ); ?><i class="icon-head"></i>
								</button>
								<input type="hidden" name="redirect_to" value="<?php echo admin_url(); ?>">
							</p>
						</div>
						
						<?php if ( ! is_user_logged_in() && get_option( 'users_can_register' ) ) : ?>
						<div class="text-left">
							<span class="text-sm text-semibold">
								<?php esc_html_e( 'New to Nucleus?', 'nucleus' ); ?>
								<?php nucleus_signup(); ?>
							</span>
						</div>
						<?php endif; ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
endif;

add_action( 'nucleus_footer_after', 'nucleus_the_modal' );

if ( ! function_exists( 'nucleus_page_wrapper_after' ) ) :
	/**
	 * Close the opened div.page-wrapper
	 *
	 * Should be closed last, right before the <scripts>
	 *
	 * Executed during the "nucleus_footer_after" hook
	 *
	 * @see footer.php
	 * @see nucleus_page_wrapper_before
	 */
	function nucleus_page_wrapper_after() {
		echo '</div>';
	}
endif;

add_action( 'nucleus_footer_after', 'nucleus_page_wrapper_after', 999 );
