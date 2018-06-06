<?php
/**
 * Theme comment template, contains both current comments and the comment form.
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return without loading the comments.
 */
if(post_password_required()){
	return;
}
?>

	<div class="comments" id="comments"> <?php 
		if(have_comments()){
			$comments_number = get_comments_number();
			$comments_arg	 = array(
				'style' 		=> 'ol',
				'short_ping' 	=> true,
				'avatar_size' 	=> 115,
				'echo'			=> false
			);

			printf(
				'<h2 class="comments-title">%s</h2><ol class="comment-list">%s</ol>',
				sprintf(
					_nx('%s comment', '%s comments', $comments_number, 'comments title', 'fallsky'), 
					number_format_i18n($comments_number)
				),
				wp_list_comments($comments_arg)
			);

			the_comments_navigation();
		} 
		if(comments_open()){
			$fold_reply = fallsky_module_enabled('fallsky_comment_fold_reply_form');
			printf('<div class="click-to-reply%s"><span>%s</span></div>', ($fold_reply ? '' : ' clicked'), esc_html__('Write a response', 'fallsky'));
			comment_form(array(
				'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
				'title_reply_after'  => '</h3>',
			)); 
		}; 
		if(!comments_open() && get_comments_number() && is_singular('post')){
			printf(
				'<p class="comments-closed">%s</p>',
				esc_html__('Comments are closed.', 'fallsky')
			);
		} ?>
	</div>
