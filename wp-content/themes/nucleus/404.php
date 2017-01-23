<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Nucleus
 */

get_header();
?>

<div class="container text-center padding-top-3x padding-bottom-3x">
	<div class="row padding-bottom">
		<div class="col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
			<?php nucleus_the_404(); ?>
		</div>
	</div>
</div>

<?php
get_footer();
