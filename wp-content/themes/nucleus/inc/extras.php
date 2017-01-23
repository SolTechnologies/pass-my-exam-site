<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Nucleus
 */


/**
 * Modify TinyMCE. Add "style_formats"
 *
 * @link https://codex.wordpress.org/TinyMCE_Custom_Styles#Using_style_formats
 *
 * @param array $init_array
 *
 * @return mixed
 */
function nucleus_mce_before_init( $init_array ) {
	$style_formats = array(
		array(
			'title'   => esc_html__( 'Text Muted', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-muted'
		),
		array(
			'title'   => esc_html__( 'Text Light', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-light'
		),
		array(
			'title'   => esc_html__( 'Text Default', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-default'
		),
		array(
			'title'   => esc_html__( 'Text Primary', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-primary'
		),
		array(
			'title'   => esc_html__( 'Text Success', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-success'
		),
		array(
			'title'   => esc_html__( 'Text Info', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-info'
		),
		array(
			'title'   => esc_html__( 'Text Warning', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-warning'
		),
		array(
			'title'   => esc_html__( 'Text Danger', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-danger'
		),
		array(
			'title'   => esc_html__( 'Text Gray', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-gray'
		),
		array(
			'title'   => esc_html__( 'Bg Default', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'bg-default'
		),
		array(
			'title'   => esc_html__( 'Bg Primary', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'bg-primary'
		),
		array(
			'title'   => esc_html__( 'Bg Success', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'bg-success'
		),
		array(
			'title'   => esc_html__( 'Bg Info', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'bg-info'
		),
		array(
			'title'   => esc_html__( 'Bg Warning', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'bg-warning'
		),
		array(
			'title'   => esc_html__( 'Bg Danger', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'bg-danger'
		),
		array(
			'title'   => esc_html__( 'UPPERCASE text', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-uppercase'
		),
		array(
			'title'    => esc_html__( 'Lead text', 'nucleus' ),
			'selector' => 'p',
			'classes'  => 'lead'
		),
		array(
			'title'   => esc_html__( 'Large text', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-lg'
		),
		array(
			'title'   => esc_html__( 'Small text', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-sm'
		),
		array(
			'title'   => esc_html__( 'Extra Small text', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-xs'
		),
		array(
			'title'   => esc_html__( 'Thin text', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-thin'
		),
		array(
			'title'   => esc_html__( 'Normal text', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-normal'
		),
		array(
			'title'   => esc_html__( 'Semibold text', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-semibold'
		),
		array(
			'title'   => esc_html__( 'Bold text', 'nucleus' ),
			'inline'  => 'span',
			'classes' => 'text-bold'
		),
		array(
			'title'    => esc_html__( 'Featured List', 'nucleus' ),
			'selector' => 'ul',
			'classes'  => 'list-featured',
		),
		array(
			'title'    => esc_html__( 'Unstyled List', 'nucleus' ),
			'selector' => 'ul',
			'classes'  => 'list-unstyled',
		),
	);

	$init_array['style_formats'] = json_encode( $style_formats );

	return $init_array;
}

add_filter( 'tiny_mce_before_init', 'nucleus_mce_before_init' );

/**
 * Add "styleselect" button to TinyMCE second row
 *
 * @param array $buttons TinyMCE Buttons
 *
 * @return mixed
 */
function nucleus_mce_buttons_2( $buttons ) {
	array_unshift( $buttons, 'styleselect' );

	return $buttons;
}

add_filter( 'mce_buttons_2', 'nucleus_mce_buttons_2' );

/**
 * Add styles to TinyMCE
 */
function nucleus_add_editor_styles() {
	add_editor_style();
}

add_action( 'admin_init', 'nucleus_add_editor_styles' );

/**
 * Some additional mime types
 *
 * @param array $mime_types
 *
 * @return array
 */
function nucleus_extended_mime_types( $mime_types ) {
	$extended = array(
		'svg' => 'image/svg+xml'
	);

	foreach ( $extended as $ext => $mime ) {
		$mime_types[ $ext ] = $mime;
	}

	return $mime_types;
}

add_filter( 'upload_mimes', 'nucleus_extended_mime_types' );

/**
 * Show favicon preview (from .ico format), not an icon
 *
 * @param string $icon    Path to the mime type icon.
 * @param string $mime    Mime type.
 * @param int    $post_id Attachment ID. Will equal 0 if the function passed
 *                        the mime type.
 *
 * @return mixed
 */
function nucleus_mime_type_icon( $icon, $mime, $post_id ) {
	$src   = false;
	$mimes = array(
		'image/x-icon',
		'image/svg+xml',
	);

	if ( in_array( $mime, $mimes, true ) && $post_id > 0 ) {
		$src = wp_get_attachment_image_src( $post_id );
	}

	return is_array( $src ) ? array_shift( $src ) : $icon;
}

add_filter( 'wp_mime_type_icon', 'nucleus_mime_type_icon', 10, 3 );

/**
 * Callback for "tiny_mce_plugins" filter.
 *
 * This function used to remove the emoji plugin from tinymce.
 *
 * @param  array $plugins
 *
 * @return array $plugins Difference between the two arrays
 */
function nucleus_disable_wp_emoji_in_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

/**
 * Disable the emoji's
 */
function nucleus_disable_wp_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	add_filter( 'tiny_mce_plugins', 'nucleus_disable_wp_emoji_in_tinymce' );
}

add_action( 'init', 'nucleus_disable_wp_emoji' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function nucleus_body_classes( $classes ) {
	// adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	if ( nucleus_is_preloader() ) {
		$classes[] = 'is-preloader';
		$classes[] = 'preloading';
	}

	if ( is_singular() && nucleus_is_parallax() ) {
		$classes[] = 'parallax';
	}

	if ( is_single() && nucleus_is_slideshow() ) {
		$classes[] = 'scroll-slideshow';
	}

	return $classes;
}

add_filter( 'body_class', 'nucleus_body_classes' );

/**
 * Custom post classes
 *
 * @param array $classes An array of post classes.
 * @param array $class   An array of additional classes added to the post.
 * @param int   $post_id The post ID.
 *
 * @return array
 */
function nucleus_post_class( $classes, $class, $post_id ) {
	$post = get_post( $post_id );

	if ( is_page()
	     && 'nucleus_portfolio' !== $post->post_type
	     && 'product' !== $post->post_type
	     && false === strpos( $post->post_content, 'vc_row' )
	) {
		$classes[] = 'container';
	}

	// modify the portfolio
	if ( 'nucleus_portfolio' === $post->post_type ) {
		$classes[] = 'grid-item';
		$classes[] = implode( ' ', nucleus_get_post_terms( $post_id, 'nucleus_portfolio_category' ) );
	}

	return $classes;
}

add_filter( 'post_class', 'nucleus_post_class', 10, 3 );

/**
 * Flush out the transients used in nucleus_categorized_blog.
 */
function nucleus_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( NUCLEUS_TRANSIENT_CATEGORIES );
}

add_action( 'edit_category', 'nucleus_category_transient_flusher' );
add_action( 'save_post', 'nucleus_category_transient_flusher' );

/**
 * Change excerpt more string to "..."
 *
 * @return string
 */
function nucleus_excerpt_more() {
	return '...';
}

add_filter( 'excerpt_more', 'nucleus_excerpt_more' );

/**
 * Replace the custom logo class on our own
 *
 * @param string $html Custom logo HTML markup
 *
 * @return string
 */
function nucleus_custom_logo_class( $html ) {
	return str_replace( 'class="custom-logo-link"', 'class="site-logo"', $html );
}

add_filter( 'get_custom_logo', 'nucleus_custom_logo_class' );

/**
 * Returns the Feather Icons pack
 *
 * Slug is "feather"
 *
 * Also, integrated with Equip
 * @see equip_get_icons()
 *
 * @return array
 */
function nucleus_get_feather_icons() {
	return array(
		'icon-eye',
		'icon-paper-clip',
		'icon-mail',
		'icon-mail',
		'icon-toggle',
		'icon-layout',
		'icon-link',
		'icon-bell',
		'icon-lock',
		'icon-unlock',
		'icon-ribbon',
		'icon-image',
		'icon-signal',
		'icon-target',
		'icon-clipboard',
		'icon-clock',
		'icon-watch',
		'icon-air-play',
		'icon-camera',
		'icon-video',
		'icon-disc',
		'icon-printer',
		'icon-monitor',
		'icon-server',
		'icon-cog',
		'icon-heart',
		'icon-paragraph',
		'icon-align-justify',
		'icon-align-left',
		'icon-align-center',
		'icon-align-right',
		'icon-book',
		'icon-layers',
		'icon-stack',
		'icon-stack-2',
		'icon-paper',
		'icon-paper-stack',
		'icon-search',
		'icon-zoom-in',
		'icon-zoom-out',
		'icon-reply',
		'icon-circle-plus',
		'icon-circle-minus',
		'icon-circle-check',
		'icon-circle-cross',
		'icon-square-plus',
		'icon-square-minus',
		'icon-square-check',
		'icon-square-cross',
		'icon-microphone',
		'icon-record',
		'icon-skip-back',
		'icon-rewind',
		'icon-play',
		'icon-pause',
		'icon-stop',
		'icon-fast-forward',
		'icon-skip-forward',
		'icon-shuffle',
		'icon-repeat',
		'icon-folder',
		'icon-umbrella',
		'icon-moon',
		'icon-thermometer',
		'icon-drop',
		'icon-sun',
		'icon-cloud',
		'icon-cloud-upload',
		'icon-cloud-download',
		'icon-upload',
		'icon-download',
		'icon-location',
		'icon-location-2',
		'icon-map',
		'icon-battery',
		'icon-head',
		'icon-briefcase',
		'icon-speech-bubble',
		'icon-anchor',
		'icon-globe',
		'icon-box',
		'icon-reload',
		'icon-share',
		'icon-marquee',
		'icon-marquee-plus',
		'icon-marquee-minus',
		'icon-tag',
		'icon-power',
		'icon-command',
		'icon-alt',
		'icon-esc',
		'icon-bar-graph',
		'icon-bar-graph-2',
		'icon-pie-graph',
		'icon-star',
		'icon-arrow-left',
		'icon-arrow-right',
		'icon-arrow-up',
		'icon-arrow-down',
		'icon-volume',
		'icon-mute',
		'icon-content-right',
		'icon-content-left',
		'icon-grid',
		'icon-grid-2',
		'icon-columns',
		'icon-loader',
		'icon-bag',
		'icon-ban',
		'icon-flag',
		'icon-trash',
		'icon-expand',
		'icon-contract',
		'icon-maximize',
		'icon-minimize',
		'icon-plus',
		'icon-minus',
		'icon-check',
		'icon-cross',
		'icon-move',
		'icon-delete',
		'icon-menu',
		'icon-archive',
		'icon-inbox',
		'icon-outbox',
		'icon-file',
		'icon-file-add',
		'icon-file-subtract',
		'icon-help',
		'icon-open',
		'icon-ellipsis',
	);
}

add_filter( 'equip/icons/feather', 'nucleus_get_feather_icons' );

/**
 * Returns the Flaticon(s) icons pack
 *
 * Slug is "flaticon"
 *
 * Integrated with Equip
 * @see equip_get_icons()
 *
 * @return array
 */
function nucleus_get_flaticons() {
	return array(
		'flaticon-antenna16',
		'flaticon-batteries16',
		'flaticon-battery34',
		'flaticon-calculate12',
		'flaticon-cd-player',
		'flaticon-cellphone101',
		'flaticon-chatting',
		'flaticon-chips1',
		'flaticon-cinema69',
		'flaticon-cinema70',
		'flaticon-cinema71',
		'flaticon-clean12',
		'flaticon-clock243',
		'flaticon-cloud-computing2',
		'flaticon-coffee-shop21',
		'flaticon-communication10',
		'flaticon-computer-monitor3',
		'flaticon-computer-mouse29',
		'flaticon-computers34',
		'flaticon-controllers',
		'flaticon-disc39',
		'flaticon-document239',
		'flaticon-drawing33',
		'flaticon-earphones21',
		'flaticon-earphones22',
		'flaticon-energies',
		'flaticon-energy44',
		'flaticon-eyeglasses28',
		'flaticon-fans9',
		'flaticon-flash6',
		'flaticon-game-controller7',
		'flaticon-game-controller8',
		'flaticon-gamepad21',
		'flaticon-internet78',
		'flaticon-keyboards9',
		'flaticon-laptop-computer17',
		'flaticon-levels4',
		'flaticon-light-bulbs8',
		'flaticon-light128',
		'flaticon-light129',
		'flaticon-mail111',
		'flaticon-map-pointer10',
		'flaticon-microphone121',
		'flaticon-microphone122',
		'flaticon-monitor101',
		'flaticon-monitor102',
		'flaticon-music-player38',
		'flaticon-music-player40',
		'flaticon-musical161',
		'flaticon-musical162',
		'flaticon-networking7',
		'flaticon-nintendo15',
		'flaticon-office-material3',
		'flaticon-office-material4',
		'flaticon-office-material5',
		'flaticon-padlock107',
		'flaticon-phone-call10',
		'flaticon-phone-call12',
		'flaticon-photo-camera19',
		'flaticon-photo-camera21',
		'flaticon-photography46',
		'flaticon-positive',
		'flaticon-printer29',
		'flaticon-printing31',
		'flaticon-projection10',
		'flaticon-radio-box1',
		'flaticon-radio-waves',
		'flaticon-satellites3',
		'flaticon-skating6',
		'flaticon-smartphone160',
		'flaticon-smartphone161',
		'flaticon-sony2',
		'flaticon-space-ship2',
		'flaticon-stars67',
		'flaticon-storage-device1',
		'flaticon-supermarket31',
		'flaticon-tablet29',
		'flaticon-technological',
		'flaticon-technological1',
		'flaticon-technology44',
		'flaticon-technology45',
		'flaticon-technology46',
		'flaticon-technology47',
		'flaticon-telephone167',
		'flaticon-telephone169',
		'flaticon-transport264',
		'flaticon-transport277',
		'flaticon-tv-screen11',
		'flaticon-usb48',
		'flaticon-video-camera18',
		'flaticon-video-camera19',
		'flaticon-video-game15',
		'flaticon-video-player24',
		'flaticon-video-player25',
		'flaticon-video-player26',
		'flaticon-vigilance',
		'flaticon-wireless-connectivity13',
		'flaticon-wireless-internet6',
		'flaticon-wristwatch3',
		'flaticon-writing50',
	);
}

add_filter( 'equip/icons/flaticon', 'nucleus_get_flaticons' );

/**
 * Returns the Font Awesome icons pack
 *
 * Slug is "fontawesome"
 *
 * Integrated with Equip
 * @see equip_get_icons()
 *
 * @return array
 */
function nucleus_get_fontawesome_icons() {
	return array(
		'fa fa-glass',
		'fa fa-music',
		'fa fa-search',
		'fa fa-envelope-o',
		'fa fa-heart',
		'fa fa-star',
		'fa fa-star-o',
		'fa fa-user',
		'fa fa-film',
		'fa fa-th-large',
		'fa fa-th',
		'fa fa-th-list',
		'fa fa-check',
		'fa fa-remove',
		'fa fa-close',
		'fa fa-times',
		'fa fa-search-plus',
		'fa fa-search-minus',
		'fa fa-power-off',
		'fa fa-signal',
		'fa fa-gear',
		'fa fa-cog',
		'fa fa-trash-o',
		'fa fa-home',
		'fa fa-file-o',
		'fa fa-clock-o',
		'fa fa-road',
		'fa fa-download',
		'fa fa-arrow-circle-o-down',
		'fa fa-arrow-circle-o-up',
		'fa fa-inbox',
		'fa fa-play-circle-o',
		'fa fa-rotate-right',
		'fa fa-repeat',
		'fa fa-refresh',
		'fa fa-list-alt',
		'fa fa-lock',
		'fa fa-flag',
		'fa fa-headphones',
		'fa fa-volume-off',
		'fa fa-volume-down',
		'fa fa-volume-up',
		'fa fa-qrcode',
		'fa fa-barcode',
		'fa fa-tag',
		'fa fa-tags',
		'fa fa-book',
		'fa fa-bookmark',
		'fa fa-print',
		'fa fa-camera',
		'fa fa-font',
		'fa fa-bold',
		'fa fa-italic',
		'fa fa-text-height',
		'fa fa-text-width',
		'fa fa-align-left',
		'fa fa-align-center',
		'fa fa-align-right',
		'fa fa-align-justify',
		'fa fa-list',
		'fa fa-dedent',
		'fa fa-outdent',
		'fa fa-indent',
		'fa fa-video-camera',
		'fa fa-photo',
		'fa fa-image',
		'fa fa-picture-o',
		'fa fa-pencil',
		'fa fa-map-marker',
		'fa fa-adjust',
		'fa fa-tint',
		'fa fa-edit',
		'fa fa-pencil-square-o',
		'fa fa-share-square-o',
		'fa fa-check-square-o',
		'fa fa-arrows',
		'fa fa-step-backward',
		'fa fa-fast-backward',
		'fa fa-backward',
		'fa fa-play',
		'fa fa-pause',
		'fa fa-stop',
		'fa fa-forward',
		'fa fa-fast-forward',
		'fa fa-step-forward',
		'fa fa-eject',
		'fa fa-chevron-left',
		'fa fa-chevron-right',
		'fa fa-plus-circle',
		'fa fa-minus-circle',
		'fa fa-times-circle',
		'fa fa-check-circle',
		'fa fa-question-circle',
		'fa fa-info-circle',
		'fa fa-crosshairs',
		'fa fa-times-circle-o',
		'fa fa-check-circle-o',
		'fa fa-ban',
		'fa fa-arrow-left',
		'fa fa-arrow-right',
		'fa fa-arrow-up',
		'fa fa-arrow-down',
		'fa fa-mail-forward',
		'fa fa-share',
		'fa fa-expand',
		'fa fa-compress',
		'fa fa-plus',
		'fa fa-minus',
		'fa fa-asterisk',
		'fa fa-exclamation-circle',
		'fa fa-gift',
		'fa fa-leaf',
		'fa fa-fire',
		'fa fa-eye',
		'fa fa-eye-slash',
		'fa fa-warning',
		'fa fa-exclamation-triangle',
		'fa fa-plane',
		'fa fa-calendar',
		'fa fa-random',
		'fa fa-comment',
		'fa fa-magnet',
		'fa fa-chevron-up',
		'fa fa-chevron-down',
		'fa fa-retweet',
		'fa fa-shopping-cart',
		'fa fa-folder',
		'fa fa-folder-open',
		'fa fa-arrows-v',
		'fa fa-arrows-h',
		'fa fa-bar-chart-o',
		'fa fa-bar-chart',
		'fa fa-twitter-square',
		'fa fa-facebook-square',
		'fa fa-camera-retro',
		'fa fa-key',
		'fa fa-gears',
		'fa fa-cogs',
		'fa fa-comments',
		'fa fa-thumbs-o-up',
		'fa fa-thumbs-o-down',
		'fa fa-star-half',
		'fa fa-heart-o',
		'fa fa-sign-out',
		'fa fa-linkedin-square',
		'fa fa-thumb-tack',
		'fa fa-external-link',
		'fa fa-sign-in',
		'fa fa-trophy',
		'fa fa-github-square',
		'fa fa-upload',
		'fa fa-lemon-o',
		'fa fa-phone',
		'fa fa-square-o',
		'fa fa-bookmark-o',
		'fa fa-phone-square',
		'fa fa-twitter',
		'fa fa-facebook-f',
		'fa fa-facebook',
		'fa fa-github',
		'fa fa-unlock',
		'fa fa-credit-card',
		'fa fa-feed',
		'fa fa-rss',
		'fa fa-hdd-o',
		'fa fa-bullhorn',
		'fa fa-bell',
		'fa fa-certificate',
		'fa fa-hand-o-right',
		'fa fa-hand-o-left',
		'fa fa-hand-o-up',
		'fa fa-hand-o-down',
		'fa fa-arrow-circle-left',
		'fa fa-arrow-circle-right',
		'fa fa-arrow-circle-up',
		'fa fa-arrow-circle-down',
		'fa fa-globe',
		'fa fa-wrench',
		'fa fa-tasks',
		'fa fa-filter',
		'fa fa-briefcase',
		'fa fa-arrows-alt',
		'fa fa-group',
		'fa fa-users',
		'fa fa-chain',
		'fa fa-link',
		'fa fa-cloud',
		'fa fa-flask',
		'fa fa-cut',
		'fa fa-scissors',
		'fa fa-copy',
		'fa fa-files-o',
		'fa fa-paperclip',
		'fa fa-save',
		'fa fa-floppy-o',
		'fa fa-square',
		'fa fa-navicon',
		'fa fa-reorder',
		'fa fa-bars',
		'fa fa-list-ul',
		'fa fa-list-ol',
		'fa fa-strikethrough',
		'fa fa-underline',
		'fa fa-table',
		'fa fa-magic',
		'fa fa-truck',
		'fa fa-pinterest',
		'fa fa-pinterest-square',
		'fa fa-google-plus-square',
		'fa fa-google-plus',
		'fa fa-money',
		'fa fa-caret-down',
		'fa fa-caret-up',
		'fa fa-caret-left',
		'fa fa-caret-right',
		'fa fa-columns',
		'fa fa-unsorted',
		'fa fa-sort',
		'fa fa-sort-down',
		'fa fa-sort-desc',
		'fa fa-sort-up',
		'fa fa-sort-asc',
		'fa fa-envelope',
		'fa fa-linkedin',
		'fa fa-rotate-left',
		'fa fa-undo',
		'fa fa-legal',
		'fa fa-gavel',
		'fa fa-dashboard',
		'fa fa-tachometer',
		'fa fa-comment-o',
		'fa fa-comments-o',
		'fa fa-flash',
		'fa fa-bolt',
		'fa fa-sitemap',
		'fa fa-umbrella',
		'fa fa-paste',
		'fa fa-clipboard',
		'fa fa-lightbulb-o',
		'fa fa-exchange',
		'fa fa-cloud-download',
		'fa fa-cloud-upload',
		'fa fa-user-md',
		'fa fa-stethoscope',
		'fa fa-suitcase',
		'fa fa-bell-o',
		'fa fa-coffee',
		'fa fa-cutlery',
		'fa fa-file-text-o',
		'fa fa-building-o',
		'fa fa-hospital-o',
		'fa fa-ambulance',
		'fa fa-medkit',
		'fa fa-fighter-jet',
		'fa fa-beer',
		'fa fa-h-square',
		'fa fa-plus-square',
		'fa fa-angle-double-left',
		'fa fa-angle-double-right',
		'fa fa-angle-double-up',
		'fa fa-angle-double-down',
		'fa fa-angle-left',
		'fa fa-angle-right',
		'fa fa-angle-up',
		'fa fa-angle-down',
		'fa fa-desktop',
		'fa fa-laptop',
		'fa fa-tablet',
		'fa fa-mobile-phone',
		'fa fa-mobile',
		'fa fa-circle-o',
		'fa fa-quote-left',
		'fa fa-quote-right',
		'fa fa-spinner',
		'fa fa-circle',
		'fa fa-mail-reply',
		'fa fa-reply',
		'fa fa-github-alt',
		'fa fa-folder-o',
		'fa fa-folder-open-o',
		'fa fa-smile-o',
		'fa fa-frown-o',
		'fa fa-meh-o',
		'fa fa-gamepad',
		'fa fa-keyboard-o',
		'fa fa-flag-o',
		'fa fa-flag-checkered',
		'fa fa-terminal',
		'fa fa-code',
		'fa fa-mail-reply-all',
		'fa fa-reply-all',
		'fa fa-star-half-empty',
		'fa fa-star-half-full',
		'fa fa-star-half-o',
		'fa fa-location-arrow',
		'fa fa-crop',
		'fa fa-code-fork',
		'fa fa-unlink',
		'fa fa-chain-broken',
		'fa fa-question',
		'fa fa-info',
		'fa fa-exclamation',
		'fa fa-superscript',
		'fa fa-subscript',
		'fa fa-eraser',
		'fa fa-puzzle-piece',
		'fa fa-microphone',
		'fa fa-microphone-slash',
		'fa fa-shield',
		'fa fa-calendar-o',
		'fa fa-fire-extinguisher',
		'fa fa-rocket',
		'fa fa-maxcdn',
		'fa fa-chevron-circle-left',
		'fa fa-chevron-circle-right',
		'fa fa-chevron-circle-up',
		'fa fa-chevron-circle-down',
		'fa fa-html5',
		'fa fa-css3',
		'fa fa-anchor',
		'fa fa-unlock-alt',
		'fa fa-bullseye',
		'fa fa-ellipsis-h',
		'fa fa-ellipsis-v',
		'fa fa-rss-square',
		'fa fa-play-circle',
		'fa fa-ticket',
		'fa fa-minus-square',
		'fa fa-minus-square-o',
		'fa fa-level-up',
		'fa fa-level-down',
		'fa fa-check-square',
		'fa fa-pencil-square',
		'fa fa-external-link-square',
		'fa fa-share-square',
		'fa fa-compass',
		'fa fa-toggle-down',
		'fa fa-caret-square-o-down',
		'fa fa-toggle-up',
		'fa fa-caret-square-o-up',
		'fa fa-toggle-right',
		'fa fa-caret-square-o-right',
		'fa fa-euro',
		'fa fa-eur',
		'fa fa-gbp',
		'fa fa-dollar',
		'fa fa-usd',
		'fa fa-rupee',
		'fa fa-inr',
		'fa fa-cny',
		'fa fa-rmb',
		'fa fa-yen',
		'fa fa-jpy',
		'fa fa-ruble',
		'fa fa-rouble',
		'fa fa-rub',
		'fa fa-won',
		'fa fa-krw',
		'fa fa-bitcoin',
		'fa fa-btc',
		'fa fa-file',
		'fa fa-file-text',
		'fa fa-sort-alpha-asc',
		'fa fa-sort-alpha-desc',
		'fa fa-sort-amount-asc',
		'fa fa-sort-amount-desc',
		'fa fa-sort-numeric-asc',
		'fa fa-sort-numeric-desc',
		'fa fa-thumbs-up',
		'fa fa-thumbs-down',
		'fa fa-youtube-square',
		'fa fa-youtube',
		'fa fa-xing',
		'fa fa-xing-square',
		'fa fa-youtube-play',
		'fa fa-dropbox',
		'fa fa-stack-overflow',
		'fa fa-instagram',
		'fa fa-flickr',
		'fa fa-adn',
		'fa fa-bitbucket',
		'fa fa-bitbucket-square',
		'fa fa-tumblr',
		'fa fa-tumblr-square',
		'fa fa-long-arrow-down',
		'fa fa-long-arrow-up',
		'fa fa-long-arrow-left',
		'fa fa-long-arrow-right',
		'fa fa-apple',
		'fa fa-windows',
		'fa fa-android',
		'fa fa-linux',
		'fa fa-dribbble',
		'fa fa-skype',
		'fa fa-foursquare',
		'fa fa-trello',
		'fa fa-female',
		'fa fa-male',
		'fa fa-gittip',
		'fa fa-gratipay',
		'fa fa-sun-o',
		'fa fa-moon-o',
		'fa fa-archive',
		'fa fa-bug',
		'fa fa-vk',
		'fa fa-weibo',
		'fa fa-renren',
		'fa fa-pagelines',
		'fa fa-stack-exchange',
		'fa fa-arrow-circle-o-right',
		'fa fa-arrow-circle-o-left',
		'fa fa-toggle-left',
		'fa fa-caret-square-o-left',
		'fa fa-dot-circle-o',
		'fa fa-wheelchair',
		'fa fa-vimeo-square',
		'fa fa-turkish-lira',
		'fa fa-try',
		'fa fa-plus-square-o',
		'fa fa-space-shuttle',
		'fa fa-slack',
		'fa fa-envelope-square',
		'fa fa-wordpress',
		'fa fa-openid',
		'fa fa-institution',
		'fa fa-bank',
		'fa fa-university',
		'fa fa-mortar-board',
		'fa fa-graduation-cap',
		'fa fa-yahoo',
		'fa fa-google',
		'fa fa-reddit',
		'fa fa-reddit-square',
		'fa fa-stumbleupon-circle',
		'fa fa-stumbleupon',
		'fa fa-delicious',
		'fa fa-digg',
		'fa fa-pied-piper',
		'fa fa-pied-piper-alt',
		'fa fa-drupal',
		'fa fa-joomla',
		'fa fa-language',
		'fa fa-fax',
		'fa fa-building',
		'fa fa-child',
		'fa fa-paw',
		'fa fa-spoon',
		'fa fa-cube',
		'fa fa-cubes',
		'fa fa-behance',
		'fa fa-behance-square',
		'fa fa-steam',
		'fa fa-steam-square',
		'fa fa-recycle',
		'fa fa-automobile',
		'fa fa-car',
		'fa fa-cab',
		'fa fa-taxi',
		'fa fa-tree',
		'fa fa-spotify',
		'fa fa-deviantart',
		'fa fa-soundcloud',
		'fa fa-database',
		'fa fa-file-pdf-o',
		'fa fa-file-word-o',
		'fa fa-file-excel-o',
		'fa fa-file-powerpoint-o',
		'fa fa-file-photo-o',
		'fa fa-file-picture-o',
		'fa fa-file-image-o',
		'fa fa-file-zip-o',
		'fa fa-file-archive-o',
		'fa fa-file-sound-o',
		'fa fa-file-audio-o',
		'fa fa-file-movie-o',
		'fa fa-file-video-o',
		'fa fa-file-code-o',
		'fa fa-vine',
		'fa fa-codepen',
		'fa fa-jsfiddle',
		'fa fa-life-bouy',
		'fa fa-life-buoy',
		'fa fa-life-saver',
		'fa fa-support',
		'fa fa-life-ring',
		'fa fa-circle-o-notch',
		'fa fa-ra',
		'fa fa-rebel',
		'fa fa-ge',
		'fa fa-empire',
		'fa fa-git-square',
		'fa fa-git',
		'fa fa-y-combinator-square',
		'fa fa-yc-square',
		'fa fa-hacker-news',
		'fa fa-tencent-weibo',
		'fa fa-qq',
		'fa fa-wechat',
		'fa fa-weixin',
		'fa fa-send',
		'fa fa-paper-plane',
		'fa fa-send-o',
		'fa fa-paper-plane-o',
		'fa fa-history',
		'fa fa-circle-thin',
		'fa fa-header',
		'fa fa-paragraph',
		'fa fa-sliders',
		'fa fa-share-alt',
		'fa fa-share-alt-square',
		'fa fa-bomb',
		'fa fa-soccer-ball-o',
		'fa fa-futbol-o',
		'fa fa-tty',
		'fa fa-binoculars',
		'fa fa-plug',
		'fa fa-slideshare',
		'fa fa-twitch',
		'fa fa-yelp',
		'fa fa-newspaper-o',
		'fa fa-wifi',
		'fa fa-calculator',
		'fa fa-paypal',
		'fa fa-google-wallet',
		'fa fa-cc-visa',
		'fa fa-cc-mastercard',
		'fa fa-cc-discover',
		'fa fa-cc-amex',
		'fa fa-cc-paypal',
		'fa fa-cc-stripe',
		'fa fa-bell-slash',
		'fa fa-bell-slash-o',
		'fa fa-trash',
		'fa fa-copyright',
		'fa fa-at',
		'fa fa-eyedropper',
		'fa fa-paint-brush',
		'fa fa-birthday-cake',
		'fa fa-area-chart',
		'fa fa-pie-chart',
		'fa fa-line-chart',
		'fa fa-lastfm',
		'fa fa-lastfm-square',
		'fa fa-toggle-off',
		'fa fa-toggle-on',
		'fa fa-bicycle',
		'fa fa-bus',
		'fa fa-ioxhost',
		'fa fa-angellist',
		'fa fa-cc',
		'fa fa-shekel',
		'fa fa-sheqel',
		'fa fa-ils',
		'fa fa-meanpath',
		'fa fa-buysellads',
		'fa fa-connectdevelop',
		'fa fa-dashcube',
		'fa fa-forumbee',
		'fa fa-leanpub',
		'fa fa-sellsy',
		'fa fa-shirtsinbulk',
		'fa fa-simplybuilt',
		'fa fa-skyatlas',
		'fa fa-cart-plus',
		'fa fa-cart-arrow-down',
		'fa fa-diamond',
		'fa fa-ship',
		'fa fa-user-secret',
		'fa fa-motorcycle',
		'fa fa-street-view',
		'fa fa-heartbeat',
		'fa fa-venus',
		'fa fa-mars',
		'fa fa-mercury',
		'fa fa-intersex',
		'fa fa-transgender',
		'fa fa-transgender-alt',
		'fa fa-venus-double',
		'fa fa-mars-double',
		'fa fa-venus-mars',
		'fa fa-mars-stroke',
		'fa fa-mars-stroke-v',
		'fa fa-mars-stroke-h',
		'fa fa-neuter',
		'fa fa-genderless',
		'fa fa-facebook-official',
		'fa fa-pinterest-p',
		'fa fa-whatsapp',
		'fa fa-server',
		'fa fa-user-plus',
		'fa fa-user-times',
		'fa fa-hotel',
		'fa fa-bed',
		'fa fa-viacoin',
		'fa fa-train',
		'fa fa-subway',
		'fa fa-medium',
		'fa fa-yc',
		'fa fa-y-combinator',
		'fa fa-optin-monster',
		'fa fa-opencart',
		'fa fa-expeditedssl',
		'fa fa-battery-4',
		'fa fa-battery-full',
		'fa fa-battery-3',
		'fa fa-battery-three-quarters',
		'fa fa-battery-2',
		'fa fa-battery-half',
		'fa fa-battery-1',
		'fa fa-battery-quarter',
		'fa fa-battery-0',
		'fa fa-battery-empty',
		'fa fa-mouse-pointer',
		'fa fa-i-cursor',
		'fa fa-object-group',
		'fa fa-object-ungroup',
		'fa fa-sticky-note',
		'fa fa-sticky-note-o',
		'fa fa-cc-jcb',
		'fa fa-cc-diners-club',
		'fa fa-clone',
		'fa fa-balance-scale',
		'fa fa-hourglass-o',
		'fa fa-hourglass-1',
		'fa fa-hourglass-start',
		'fa fa-hourglass-2',
		'fa fa-hourglass-half',
		'fa fa-hourglass-3',
		'fa fa-hourglass-end',
		'fa fa-hourglass',
		'fa fa-hand-grab-o',
		'fa fa-hand-rock-o',
		'fa fa-hand-stop-o',
		'fa fa-hand-paper-o',
		'fa fa-hand-scissors-o',
		'fa fa-hand-lizard-o',
		'fa fa-hand-spock-o',
		'fa fa-hand-pointer-o',
		'fa fa-hand-peace-o',
		'fa fa-trademark',
		'fa fa-registered',
		'fa fa-creative-commons',
		'fa fa-gg',
		'fa fa-gg-circle',
		'fa fa-tripadvisor',
		'fa fa-odnoklassniki',
		'fa fa-odnoklassniki-square',
		'fa fa-get-pocket',
		'fa fa-wikipedia-w',
		'fa fa-safari',
		'fa fa-chrome',
		'fa fa-firefox',
		'fa fa-opera',
		'fa fa-internet-explorer',
		'fa fa-tv',
		'fa fa-television',
		'fa fa-contao',
		'fa fa-500px',
		'fa fa-amazon',
		'fa fa-calendar-plus-o',
		'fa fa-calendar-minus-o',
		'fa fa-calendar-times-o',
		'fa fa-calendar-check-o',
		'fa fa-industry',
		'fa fa-map-pin',
		'fa fa-map-signs',
		'fa fa-map-o',
		'fa fa-map',
		'fa fa-commenting',
		'fa fa-commenting-o',
		'fa fa-houzz',
		'fa fa-vimeo',
		'fa fa-black-tie',
		'fa fa-fonticons',
	);
}

add_filter( 'equip/icons/fontawesome', 'nucleus_get_fontawesome_icons' );

/**
 * Callback for
 * @see vc_base_register_front_css
 * @see vc_base_register_admin_css
 *
 * Register all the styles for VC Iconpicker to be enqueue later
 */
function nucleus_vc_iconpicker_register_css() {
	wp_register_style( 'feather', NUCLEUS_TEMPLATE_URI . '/css/vendor/feather.min.css', null, null );
	wp_register_style( 'flaticon', NUCLEUS_TEMPLATE_URI . '/css/vendor/flaticon.min.css', null, null );
}

add_action( 'vc_base_register_front_css', 'nucleus_vc_iconpicker_register_css' );
add_action( 'vc_base_register_admin_css', 'nucleus_vc_iconpicker_register_css' );

/**
 * Enqueue the CSS for the Front-end site,
 * when one of the fonts is selected in the shortcode.
 *
 * @see vc_icon_element_fonts_enqueue
 *
 * @param string $font Library name
 */
function nucleus_vc_iconpicker_front_css( $font ) {
	switch ( $font ) {
		case 'feather':
			wp_enqueue_style( 'feather', NUCLEUS_TEMPLATE_URI . '/css/vendor/feather.min.css', null, null );
			break;

		case 'flaticon':
			wp_enqueue_style( 'flaticon', NUCLEUS_TEMPLATE_URI . '/css/vendor/flaticon.min.css', null, null );
			break;
	}
}

add_action( 'vc_enqueue_font_icon_element', 'nucleus_vc_iconpicker_front_css' );

/**
 * Callback for
 * @see vc_backend_editor_enqueue_js_css
 * @see vc_frontend_editor_enqueue_js_css
 *
 * Used to enqueue all needed files when VC editor is rendering
 */
function nucleus_vc_iconpicker_enqueue_css() {
	wp_enqueue_style( 'feather' );
	wp_enqueue_style( 'flaticon' );
}

add_action( 'vc_backend_editor_enqueue_js_css', 'nucleus_vc_iconpicker_enqueue_css' );
add_action( 'vc_frontend_editor_enqueue_js_css', 'nucleus_vc_iconpicker_enqueue_css' );

/**
 * Returns Flaticon for VC icon picker
 *
 * @param array $icons
 *
 * @return array Icons for icon picker, can be categorized, or not.
 */
function nucleus_vc_iconpicker_flaticon( $icons ) {
	$flaticon = array();
	foreach ( (array) nucleus_get_flaticons() as $icon ) {
		$flaticon[] = array( $icon => ".{$icon}" );
	}

	return $flaticon;
}

add_filter( 'vc_iconpicker-type-flaticon', 'nucleus_vc_iconpicker_flaticon' );

/**
 * Feather icons for VC icon picker
 *
 * If array categorized it will auto-enable category dropdown
 *
 * @param array $icons Data for vc_map param field settings['source']
 *
 * @return array Icons for icon picker, can be categorized, or not.
 */
function nucleus_vc_iconpicker_feather( $icons ) {
	$feather = array();
	foreach ( nucleus_get_feather_icons() as $icon ) {
		$feather[] = array( $icon => ".{$icon}" );
	}

	return $feather;
}

add_filter( 'vc_iconpicker-type-feather', 'nucleus_vc_iconpicker_feather' );

/**
 * Return "Page Settings" meta box for current entry.
 *
 * Anyway returns array with default settings if key field is not specified.
 * If $key field given this function may return this field value or $default
 * if given $key not found in settings array.
 *
 * @uses get_queried_object
 * @see  inc/meta-boxes.php
 *
 * @param string $key     Name of required option or "all" for whole bunch of settings
 * @param mixed  $default A fallback if option is failed
 *
 * @return mixed
 */
function nucleus_get_page_setting( $key = 'all', $default = false ) {
	$post     = get_queried_object();
	$settings = false;

	if ( $post instanceof WP_Post ) {
		$settings = nucleus_get_meta( $post->ID, NUCLEUS_PAGE_SETTINGS );
	}

	// do not return empty set
	if ( empty( $settings ) || ! is_array( $settings ) ) {
		$settings = array();
	}

	// posts and pages have different set of settings
	// but I am not worried about it and merge with all
	// possible default values
	$settings = wp_parse_args( $settings, array(
		'header_is_sticky'       => 'default',
		'header_layout'          => 'default',
		'custom_title'           => '',
		'portfolio_permalink'    => '',
		'global_is_page_title'   => 'default',
		'post_layout'            => 'right-sidebar',
		'footer_skin'            => 'default',
		'header_is_search'       => 'default',
		'header_is_signup_login' => 'default',
		'menu_is_icons'          => 1,
	) );

	if ( 'all' === $key ) {
		return $settings;
	}

	$result = $default;
	if ( array_key_exists( $key, $settings ) ) {
		$result = $settings[ $key ];
	}

	return $result;
}

/**
 * Get footer sidebars
 *
 * Based on Theme Options > Footer > Layout
 *
 * @see inc/options.php
 * @see inc/widgets.php
 * @see nucleus_widgets_init()
 *
 * @return int
 */
function nucleus_get_footer_sidebars() {
	$footer_layout = (int) nucleus_get_option( 'footer_layout', 4 );
	if ( $footer_layout <= 0 ) {
		return 0;
	}

	return $footer_layout;
}

/**
 * Returns the path to stylesheet.
 *
 * May return path to compiled CSS (with high priority)
 * or normal css as a fallback.
 *
 * @see functions.php
 * @see inc/options.php
 * @see nucleus_scripts()
 * @see NUCLEUS_COMPILED
 *
 * @return string
 */
function nucleus_stylesheet_uri() {
	// Maybe enqueue compiled css or a fallback
	$c = get_option( NUCLEUS_COMPILED );
	if ( is_array( $c )
	     && array_key_exists( 'path', $c )
	     && is_readable( $c['path'] )
	     && 0 !== filesize( $c['path'] )
	) {
		$stylesheet = esc_url( $c['url'] );
	} else {
		$stylesheet = NUCLEUS_TEMPLATE_URI . '/css/styles.min.css';
	}

	return $stylesheet;
}
