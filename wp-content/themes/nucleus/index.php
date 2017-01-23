<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Nucleus
 */

get_header();
?>

<div class="container">

	<?php
	if ( have_posts() ) :
		get_template_part( 'template-parts/blog', nucleus_blog_layout() );
	else :
		get_template_part( 'template-parts/none' );
	endif;
	?>

</div>

<?php
get_footer();
