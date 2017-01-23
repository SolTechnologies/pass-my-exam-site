<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Nucleus
 */

get_header();
?>

	<div class="container padding-bottom-3x">

		<?php
		if ( have_posts() ) :
			get_template_part( 'template-parts/blog', nucleus_archive_layout() );
		else :
			get_template_part( 'template-parts/none' );
		endif;
		?>

	</div>

<?php
get_footer();
