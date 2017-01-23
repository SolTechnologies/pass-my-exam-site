<?php
/**
 * Template part for displaying post tiles in Home
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Nucleus
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-item' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="post-thumb waves-effect">
			<?php the_post_thumbnail(); ?>
		</a>
	<?php endif; ?>
	<div class="post-body">
		<?php
		// display meta information
		nucleus_entry_meta();

		// display post title
		the_title(
			sprintf( '<a href="%s" class="post-title"><h3>', esc_url( get_permalink() ) ),
			'</h3></a>'
		);

		the_excerpt();

		// read more button, etc
		nucleus_tile_footer();
		?>
	</div>
</article>
