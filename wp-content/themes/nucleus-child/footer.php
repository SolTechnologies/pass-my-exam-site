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

<!--Modal For Email Subscription-->
<div class="modal fade" id="subscribeModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
		<div class="modal-header">
		    <a class="close" data-dismiss="modal">×</a>
		</div>
	  	<div class="modal-body">
	  	    <!--sign up form-->
	  	    <!-- Begin MailChimp Signup Form -->
                <link href="//cdn-images.mailchimp.com/embedcode/classic-10_7.css" rel="stylesheet" type="text/css">
                <style type="text/css">
                	#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
                	/* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
                	   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
                </style>
                <div id="mc_embed_signup">
                <form action="//facebook.us14.list-manage.com/subscribe/post?u=98f039ae551b0e91340b04ebd&amp;id=db06950dd7" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                    <div id="mc_embed_signup_scroll">
                	<h2 class="text">Learn more by suscribing to our email list!</h2>
                	<div class="text">Stay connected to learn more about our mission to change the test-taking game for good. 
                	We promise not to spam you, we'll only send you the important stuff.</div>
                <div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
                <div class="mc-field-group">
                	<label for="mce-EMAIL">Email Address  <span class="asterisk">*</span>
                </label>
                	<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
                </div>
                <div class="mc-field-group">
                	<label for="mce-FNAME">First Name </label>
                	<input type="text" value="" name="FNAME" class="" id="mce-FNAME">
                </div>
                <div class="mc-field-group">
                	<label for="mce-LNAME">Last Name </label>
                	<input type="text" value="" name="LNAME" class="" id="mce-LNAME">
                </div>
                	<div id="mce-responses" class="clear">
                		<div class="response" id="mce-error-response" style="display:none"></div>
                		<div class="response" id="mce-success-response" style="display:none"></div>
                	</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_98f039ae551b0e91340b04ebd_db06950dd7" tabindex="-1" value=""></div>
                    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
                    </div>
                </form>
                </div>
                <script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
            <!--End mc_embed_signup-->
	  	    <!--End of sign up form-->
	  	</div>
    </div>
  </div>
</div>


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
