<?php
/*
* -------------------------------------------------------------------------------------
* @author: FP Movies Classic Theme
* @author URI: https://fpmoviesdb.xyz/
* @copyright: (c) | All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 1.0.0
*
*/

if (!defined('ABSPATH')) exit;

if (post_password_required()) {
	return;
}


wp_enqueue_script('fp-ajax-comments', FP_T_ASSETS_URI . '/js/fp_comment.js', array('jquery'), FP_T_VERSION, true);
wp_localize_script('fp-ajax-comments', 'fp_ajax_comments', array(
	'ajax_url' => admin_url('admin-ajax.php'),
	'nonce'    => wp_create_nonce('ajax-comment-nonce')
));
wp_enqueue_style('fp-comments', FP_T_ASSETS_URI . '/css/fp_comments.css', array(), FP_T_VERSION, 'all');

include_once(FP_T_PATH . '/include/templates/fp_comments.php');
add_filter('comment_form_logged_in', function ($logged_in_as) {
	$current_user = wp_get_current_user();
	$logged_in_as = sprintf(
		'<div class="logged-in-wrapper" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.7rem;">
			<div class="logged-in-as" style="margin: 0;">%s</div>
			<div class="logged-in-links" style="display: flex; justify-content: space-between; align-items: center; gap: 1rem">
				<a href="%s" style="color: #f5f5f5; text-decoration: none;">%s</a>
				<a href="%s" style="color: #f5f5f5; text-decoration: none;">%s</a>
			</div>
		</div>',
		sprintf(__('Logged in as %1$s.', FP_T_TEXT_DOMAIN), esc_html($current_user->display_name)),
		esc_url(admin_url('profile.php')),
		__('Edit your profile.', FP_T_TEXT_DOMAIN),
		esc_url(wp_logout_url(apply_filters('the_permalink', get_permalink()))),
		__('Log out?', FP_T_TEXT_DOMAIN)
	);
	return $logged_in_as;
});

?>

<div id="comments" class="comments-area">
	<?php if (have_comments()) : ?>
		<h2 class="comments-title" style="color: #f5f5f5;">
			<?php
			printf(
				_nx('One Comment', '%1$s Comments', get_comments_number(), 'comments title', FP_T_TEXT_DOMAIN),
				number_format_i18n(get_comments_number())
			);
			?>
		</h2>

		<ol class="comment-list" style="list-style: none; padding: 0;">
			<?php
			wp_list_comments(array(
				'style' => 'ol',
				'short_ping' => true,
				'avatar_size' => 50,
				'callback' => 'fp_comments_template',
			));
			?>
		</ol>

		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php
	// If comments are closed and there are comments, leave a note
	if (!comments_open() && get_comments_number()) :
	?>
		<p class="no-comments"><?php _e('Comments are closed.', FP_T_TEXT_DOMAIN); ?></p>
	<?php endif; ?>
	<div id="new-comment-area" style="padding: 15px 30px; background-color: #1a1a1a; ">
		<?php
		comment_form(array(

			'title_reply' => '<h3><span style="color: #f5f5f5; font-size: 1.5em;">Leave a Comment</span></h3>',
			'title_reply_to' => '<span style="color: #f5f5f5;">Reply to %s</span>',
			'comment_notes_before' => '',
			'comment_notes_after' => '',
			'fields' => array(
				'author' => '<p class="comment-form-author" style="margin-bottom: 20px;"><label for="author" style="color: #f5f5f5;">' . __('Name', FP_T_TEXT_DOMAIN) . '</label> ' .
					'<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" style="width: 100%; padding: 10px; background-color: #333; color: #f5f5f5; border: 1px solid #555; border-radius: 5px;" /></p>',
				'email' => '<p class="comment-form-email" style="margin-bottom: 20px;"><label for="email" style="color: #f5f5f5;">' . __('Email', FP_T_TEXT_DOMAIN) . '</label> ' .
					'<input id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" style="width: 100%; padding: 10px; background-color: #333; color: #f5f5f5; border: 1px solid #555; border-radius: 5px;" /></p>',
			),
			'comment_field' => '<p class="comment-form-comment" style="margin-bottom: 20px;"><label for="comment" style="color: #f5f5f5;">' . _x('Comment', 'noun', FP_T_TEXT_DOMAIN) . '</label> ' .
				'<textarea id="comment" name="comment" cols="45" rows="8" style="width: 100%; padding: 10px; background-color: #333; color: #f5f5f5; border: 1px solid #555; border-radius: 5px;"></textarea></p>',
			'submit_button' => '<button type="submit" class="submit add_new_comment" style="">' . __('Submit Comment', FP_T_TEXT_DOMAIN) . '</button>',
		));
		?>
	</div>
</div>