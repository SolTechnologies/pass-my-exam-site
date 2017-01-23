<?php
/**
 * The template for displaying "Portfolio" posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Nucleus
 */

get_header();

while ( have_posts() ) : the_post();

	get_template_part( 'template-parts/page' );

endwhile;

get_footer();
