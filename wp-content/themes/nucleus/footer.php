<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nucleus
 */

/**
 * Fires right before the footer
 *
 * @see nucleus_scroll_to_top()
 */
do_action( 'nucleus_footer_before' );
?>

<footer class="<?php nucleus_footer_class(); ?>">

	<?php if ( nucleus_is_footer_action() || nucleus_is_footer_subscribe() ) : ?>
	<div class="top-footer">
		<div class="container">
			<?php
			nucleus_footer_action();
			nucleus_footer_subscribe();
			?>
		</div>
	</div>
	<?php endif; ?>

	<div class="bottom-footer">
		<div class="container">
			<div class="row">
				<?php get_template_part( 'template-parts/footer', nucleus_footer_layout() ); ?>
			</div>
			
			<?php nucleus_footer_copyright(); ?>
		</div>
	</div>
</footer>

<?php
/**
 * Fires right after the closing <footer>
 *
 * @see nucleus_page_wrapper_after()
 * @see nucleus_the_modal()
 */
do_action( 'nucleus_footer_after' );

wp_footer(); ?>

</body>
</html>
