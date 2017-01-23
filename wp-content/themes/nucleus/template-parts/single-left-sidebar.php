<?php
/**
 * Template part for displaying the single post with left sidebar
 *
 * @author  8guild
 * @package Nucleus
 */
?>
<div class="col-lg-9 col-md-8 col-lg-push-3 col-md-push-4">
	<?php
	get_template_part( 'template-parts/content' );

	// If comments are open or we have at least one comment,
	// load up the comment template.
	if ( comments_open() || get_comments_number() ) :
		comments_template();
	endif;
	?>
</div>
<div class="col-lg-3 col-md-4 col-lg-pull-9 col-md-pull-8">
	<div class="space-top-3x visible-sm visible-xs"></div>
	<?php get_sidebar(); ?>
</div>
