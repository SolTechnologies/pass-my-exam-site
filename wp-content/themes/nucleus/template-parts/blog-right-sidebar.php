<?php
/**
 * Template part for displaying the blog with the right sidebar
 *
 * @package Nucleus
 */

?>
<div class="row">
	<div class="col-lg-9 col-md-8">

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

	<div class="col-lg-3 col-md-4">
		<?php get_sidebar(); ?>
	</div>
</div>
