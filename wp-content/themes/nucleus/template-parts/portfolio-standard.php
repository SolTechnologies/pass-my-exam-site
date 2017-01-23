<?php
/**
 * A portfolio tile for grids with type=standard
 *
 * @author 8guild
 */

?>
<div id="portfolio-item-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a href="<?php nucleus_portfolio_permalink(); ?>" class="portfolio-item">
		<?php if ( has_post_thumbnail() ) : ?>
		<div class="thumbnail waves-effect waves-light">
			<?php the_post_thumbnail(); ?>
		</div>
		<?php endif; ?>
		
		<?php the_title( '<h3 class="portfolio-title">', '</h3>' ); ?>
	</a>
</div>
