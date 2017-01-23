<?php
/**
 * Template part for displaying the blog with the left sidebar
 *
 * @package Nucleus
 */

?>
<div class="row">
	<div class="col-lg-9 col-md-8 col-lg-push-3 col-md-push-4">

		<?php
		/**
		 * Fires right before the blog loop starts
		 */
		do_action( 'nucleus_loop_before' );

		while ( have_posts() ):
			the_post();
			get_template_part( 'template-parts/tile' );
		endwhile;

		/**
		 * Fires after the blog loop
		 *
		 * @see nucleus_posts_pagination()
		 */
		do_action( 'nucleus_loop_after' );
		?>

	</div>

	<div class="col-lg-3 col-md-4 col-lg-pull-9 col-md-pull-8">
		<?php get_sidebar(); ?>
	</div>
</div>
