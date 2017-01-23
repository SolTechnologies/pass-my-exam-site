<?php
/**
 * Actions and filters for comments list and comment form
 *
 * @author 8guild
 */

/**
 * Start the single comment
 *
 * @see wp_list_comments
 *
 * @param object $comment Comment to display.
 * @param array  $args    An array of arguments.
 * @param int    $depth   Depth of comment.
 */
function nucleus_comment( $comment, $args, $depth ) {
	// Extra comment wrap class
	$extra_class = nucleus_get_class_set( $args['has_children'] ? 'parent' : '' );
	?>
	<div id="comment-<?php comment_ID(); ?>" <?php comment_class( $extra_class ); ?>>
		<div class="comment-meta">
			<div class="column">
				<span class="comment-autor"><i class="icon-head"></i><?php comment_author(); ?></span>
				<span class="comment-reply">
					<?php comment_reply_link( array_merge( $args, array(
						'depth'  => $depth,
						'before' => '<i class="icon-reply"></i>&nbsp;',
					) ) ); ?>
				</span>
			</div>
			<div class="column">
				<span class="comment-date"><?php comment_date(); ?></span>
			</div>
		</div>
		<div class="comment-body">
			<?php
			if ( '0' == $comment->comment_approved ) :
				nucleus_the_text( esc_html__( 'Comment is awaiting moderation', 'nucleus' ),
					'<p class="comment-awaiting-moderation">',
					'</p>'
				);
			else:
				comment_text();
			endif;
			?>
		</div>
	<?php
}

/**
 * End of a single comment
 *
 * @see Walker::end_el
 * @see wp_list_comments
 *
 * @param object $comment The comment object. Default current comment.
 * @param array  $args    An array of arguments.
 * @param int    $depth   Depth of comment.
 */
function nucleus_comment_end( $comment, $args, $depth ) {
	echo '</div>'; // close div#comment-%d
}

/**
 * Returns supported level of nesting for comments list.
 * Depends on threaded_comment option and CSS support.
 *
 * @see wp_list_comments
 *
 * @return int|string
 */
function nucleus_comment_max_depth() {
	// Respect the "Enable threaded comments" option in Settings > Discussion
	if ( false === (bool) get_option( 'thread_comments' ) ) {
		return '';
	}

	/**
	 * Filter the comments nesting level for {@see wp_list_comments}
	 *
	 * @param int $level max_depth argument
	 */
	return apply_filters( 'nucleus_comment_max_depth', 2 );
}

/**
 * Don't count pingbacks or trackbacks when determining
 * the number of comments on a post.
 *
 * Comments number is cached for 6 hours!
 *
 * @param string $count Number of comments, pingbacks and trackbacks
 *
 * @return int
 */
function nucleus_comments_number( $count ) {
	global $id;

	/**
	 * Filter for enabling the counting pingbacks and trackbacks when
	 * determining the number of comments on a post. Default is disabled.
	 *
	 * @param bool $is_count Default is false.
	 */
	if ( null === $id || apply_filters( 'nucleus_count_pingbacks_trackbacks', false ) ) {
		return $count;
	}

	$cache_key   = 'comments_number_for_' . $id;
	$cache_group = 'nucleus_comments';

	$comment_count = wp_cache_get( $cache_key, $cache_group );
	if ( false === $comment_count ) {
		$comment_count = 0;
		$comments      = get_approved_comments( $id );
		foreach ( $comments as $comment ) {
			if ( $comment->comment_type === '' ) {
				$comment_count++;
			}
		}

		wp_cache_set( $cache_key, $comment_count, $cache_group, 6 * HOUR_IN_SECONDS );
	}

	return $comment_count;
}

add_filter( 'get_comments_number', 'nucleus_comments_number', 0 );

/**
 * Wrap comment form fields (author, email) with div.row
 *
 * @see comment_form()
 */
function nucleus_comment_form_before_fields() {
	echo '<div class="row">';
}

add_action( 'comment_form_before_fields', 'nucleus_comment_form_before_fields' );

/**
 * Modify the comment form's field like author and email
 *
 * @param array $fields Default fields
 *
 * @return array
 */
function nucleus_comment_form_default_fields( $fields ) {
	// remove URL field
	unset( $fields['url'] );

	$commenter   = wp_get_current_commenter();
	$is_required = (bool) get_option( 'require_name_email' );
	$required    = $is_required ? 'required' : '';

	// html templates for author and email fields
	$author_tpl = '<div class="col-sm-6"><div class="form-group">'
	              . '<label for="cf_name" class="sr-only">%1$s</label>'
	              . '<input type="text" class="form-control input-alt" name="author" id="cf_name" placeholder="%1$s" value="%2$s" %3$s>'
	              . '</div></div>';

	$email_tpl = '<div class="col-sm-6"><div class="form-group">'
	             . '<label for="cf_email" class="sr-only">%1$s</label>'
	             . '<input type="email" class="form-control input-alt" name="email" id="cf_email" placeholder="%1$s" value="%2$s" %3$s>'
	             . '</div></div>';

	// ready fields
	$author = sprintf( $author_tpl,
		esc_html__( 'Name', 'nucleus' ),
		esc_attr( $commenter['comment_author'] ),
		$required
	);

	$email = sprintf( $email_tpl,
		esc_html__( 'Email', 'nucleus' ),
		esc_attr( $commenter['comment_author_email'] ),
		$required
	);

	return array(
		'author' => $author,
		'email'  => $email,
	);
}

add_filter( 'comment_form_default_fields', 'nucleus_comment_form_default_fields' );

/**
 * Close div.row wrapper after comment form fields (author, email)
 *
 * @see comment_form
 */
function nucleus_comment_form_after_fields() {
	echo '</div>'; // close div.row
}

add_action( 'comment_form_after_fields', 'nucleus_comment_form_after_fields' );

/**
 * Modify the comment_form_defaults arguments
 *
 * @see comment_form
 *
 * @param array $args The default comment form arguments.
 *
 * @return array
 */
function nucleus_comment_form_defaults( $args ) {
	// remove comment notes before and after, unnecessary
	$args['comment_notes_before'] = $args['comment_notes_after'] = '';

	// modify the title of the respond form
	$args['title_reply_before'] = '<h4 class="comment-reply-title">';
	$args['title_reply']        = esc_html_x( 'Leave a comment', 'comments title', 'nucleus' );
	$args['title_reply_after']  = '</h4>';

	// textarea html template
	$comment_field_tpl = '<div class="form-group">'
	                     . '<label for="cf_comment" class="sr-only">%1$s</label>'
	                     . '<textarea name="comment" id="cf_comment" class="form-control input-alt" rows="7" placeholder="%2$s" required></textarea>'
	                     . '</div>';

	$args['id_form']       = 'comment-form';
	$args['class_submit']  = 'btn btn-primary btn-block';
	$args['label_submit']  = esc_html_x( 'Post a comment', 'comment form submit', 'nucleus' );
	$args['comment_field'] = sprintf( $comment_field_tpl,
		esc_html_x( 'Comment', 'noun', 'nucleus' ),
		esc_html__( 'Enter your comment', 'nucleus' )
	);

	return $args;
}

add_filter( 'comment_form_defaults', 'nucleus_comment_form_defaults' );
