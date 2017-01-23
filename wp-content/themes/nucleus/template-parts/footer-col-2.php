<?php
/**
 * Footer layout with two columns
 *
 * @package Nucleus
 */
?>
<div class="col-sm-6">
	<?php
	if ( is_active_sidebar( 'footer-sidebar-1' ) ) :
		dynamic_sidebar( 'footer-sidebar-1' );
	endif;
	?>
</div>
<div class="col-sm-6">
	<?php
	if ( is_active_sidebar( 'footer-sidebar-2' ) ) :
		dynamic_sidebar( 'footer-sidebar-2' );
	endif;
	?>
</div>
