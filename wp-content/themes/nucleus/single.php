<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Nucleus
 */

get_header(); ?>

<div class="container">
	<div class="row">

	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/single', nucleus_single_layout() );
	endwhile;
	?>

	</div>
</div>

<?php
get_footer();
