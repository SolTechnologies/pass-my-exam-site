<?php
/**
 * Template part for displaying single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Nucleus
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php

	the_title( '<h1>', '</h1>' );
	nucleus_entry_meta();

	the_content();
	wp_link_pages( array(
		'before'      => '<div class="page-links"><span>' . esc_html__( 'Pages:', 'nucleus' ),
		'after'       => '</span></div>',
		'link_before' => '<i>',
		'link_after'  => '</i>',
	) );

	nucleus_entry_footer();

	?>
</article>
