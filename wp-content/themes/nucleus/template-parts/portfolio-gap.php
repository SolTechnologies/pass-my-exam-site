<?php
/**
 * A portfolio tile for grids with type=gap
 *
 * @author 8guild
 */

?>
<div id="portfolio-item-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a href="<?php nucleus_portfolio_permalink(); ?>" class="portfolio-item">
		<div class="thumbnail waves-effect waves-light">
			<?php the_post_thumbnail(); ?>
			<?php the_title( '<h3 class="portfolio-title">', '</h3>' ); ?>
		</div>
	</a>
</div>
