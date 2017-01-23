<?php
/**
 * The template for displaying "Scroll Slideshow" posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Nucleus
 */

get_header();

while ( have_posts() ) :
	the_post();
	nucleus_the_slideshow();
	nucleus_the_slideshow_nav();
endwhile;

/**
 * Fires right after the closing <footer>
 *
 * @see nucleus_page_wrapper_after()
 * @see nucleus_the_modal()
 */
do_action( 'nucleus_footer_after' );

wp_footer();
?>

</body>
</html>
