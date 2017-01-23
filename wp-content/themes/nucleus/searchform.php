<?php
/**
 * Search form
 *
 * @author  8guild
 * @package Nucleus
 */
?>
<form method="get" class="search-box" action="<?php echo esc_url( home_url( '/' ) ); ?>" autocomplete="off">
	<input type="text" name="s" class="form-control"
	       placeholder="<?php echo esc_attr_x( 'Search', 'search form placeholder', 'nucleus' ); ?>"
	       value="<?php the_search_query(); ?>">
	<button type="submit"><i class="icon-search"></i></button>
</form>
