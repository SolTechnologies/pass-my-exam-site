<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nucleus
 */

if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
	return;
}
?>

<div class="space-top-2x visible-sm visible-xs"></div>
<aside class="sidebar">
	<?php dynamic_sidebar( 'blog-sidebar' ); ?>
</aside>