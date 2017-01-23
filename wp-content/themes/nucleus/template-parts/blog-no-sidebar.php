<?php
/**
 * Template part for displaying the blog without sidebar
 *
 * @package Nucleus
 */

/**
 * Fires right before the blog loop starts
 */
do_action( 'nucleus_loop_before' );

?>
<section class="grid isotope-grid col-2">
	<div class="gutter-sizer"></div>
	<div class="grid-sizer"></div>

	<?php
	while ( have_posts() ):
		the_post();
		get_template_part( 'template-parts/tile', 'isotope' );
	endwhile;
	?>

</section>

<?php

/**
 * Fires after the blog loop
 *
 * @see nucleus_posts_pagination()
 */
do_action( 'nucleus_loop_after' );
