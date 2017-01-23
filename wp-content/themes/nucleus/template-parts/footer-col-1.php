<?php
/**
 * Footer layout with one column
 *
 * @package Nucleus
 */
?>
<div class="col-sm-12">
	<?php
	if ( is_active_sidebar( 'footer-sidebar-1' ) ) :
		dynamic_sidebar( 'footer-sidebar-1' );
	endif;
	?>
</div>
