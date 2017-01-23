<?php
/**
 * Template part for displaying the single post without the sidebar
 *
 * @author  8guild
 * @package Nucleus
 */
?>
<div class="col-lg-10 col-lg-offset-1">
	<?php
	get_template_part( 'template-parts/content' );

	// If comments are open or we have at least one comment,
	// load up the comment template.
	if ( comments_open() || get_comments_number() ) :
		comments_template();
	endif;
	?>
</div>