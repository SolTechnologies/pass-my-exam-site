<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Nucleus
 */

get_header();
?>

	<div class="container">

		<?php
		if ( have_posts() ) :
			get_template_part( 'template-parts/blog', nucleus_search_layout() );
		else :
			get_template_part( 'template-parts/none' );
		endif;
		?>

	</div>

<?php
get_footer();
