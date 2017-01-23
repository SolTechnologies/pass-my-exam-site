<?php
/**
 * Footer layout with four columns
 *
 * @package Nucleus
 */
?>
<div class="col-md-3 col-sm-6">
	<?php
	if ( is_active_sidebar( 'footer-sidebar-1' ) ) :
		dynamic_sidebar( 'footer-sidebar-1' );
	endif;
	?>
</div>
<div class="col-md-3 col-sm-6">
	<?php
	if ( is_active_sidebar( 'footer-sidebar-2' ) ) :
		dynamic_sidebar( 'footer-sidebar-2' );
	endif;
	?>
</div>
<div class="clearfix visible-sm"></div>
<div class="col-md-3 col-sm-6">
	<?php
	if ( is_active_sidebar( 'footer-sidebar-3' ) ) :
		dynamic_sidebar( 'footer-sidebar-3' );
	endif;
	?>
</div>
<div class="col-md-3 col-sm-6">
	<?php
	if ( is_active_sidebar( 'footer-sidebar-4' ) ) :
		dynamic_sidebar( 'footer-sidebar-4' );
	endif;
	?>
</div>
