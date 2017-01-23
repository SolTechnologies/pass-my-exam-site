<?php
/**
 * The header for our theme
 *
 * @package Nucleus
 */
?><!DOCTYPE html>
<html itemscope itemtype="http://schema.org/WebPage" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php nucleus_body_attributes(); ?>>

	<?php
	/** Service action */
	do_action( 'guild_body_before' );
	?>

	<?php
	/**
	 * Fires right before the <header>
	 * 
	 * @see nucleus_the_preloader()
	 * @see nucleus_the_social_bar()
	 * @see nucleus_page_wrapper_before()
	 */
	do_action( 'nucleus_header_before' );
	?>

	<header class="<?php nucleus_navbar_class(); ?>">
		<!--<div class="topbar">-->
		<!--	<div class="container">-->
		<!--		<?php nucleus_the_logo(); ?>-->
		<!--		<div class="nav-toggle"><span></span></div>-->
		<!--		<?php nucleus_the_toolbar(); ?>-->
		<!--	</div>-->
		<!--</div>-->
		
		<div class="nav-toggle"><span></span></div>
		<?php nucleus_the_menu(); ?>
		<?php nucleus_mobile_socials(); ?>
	</header>

	<?php
	/**
	 * Fires right after the <header>
	 *
	 * @see nucleus_page_title()
	 */
	do_action( 'nucleus_header_after' );
	?>
