<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Nucleus
 */

get_header();

while ( have_posts() ) : the_post();

	get_template_part( 'template-parts/page' );

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) :
		echo "<div class='container'>";
		comments_template();
		echo "</div>";
	endif;

endwhile;

get_footer();
