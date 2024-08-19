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


function fp_movies_ajax_submit_comment()
{
    fp_log('fp_movies_ajax_submit_comment');
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'ajax-comment-nonce')) {
        fp_log('Nonce verification failed.');
        wp_send_json_error(array('message' => 'Nonce verification failed.'));
        return;
    }

    $comment_data = array(
        'comment_post_ID' => intval($_POST['comment_post_ID']),
        'comment_author' => sanitize_text_field($_POST['author']),
        'comment_author_email' => sanitize_email($_POST['email']),
        'comment_content' => sanitize_textarea_field($_POST['comment']),
        'comment_type' => '',
        'comment_parent' => intval($_POST['comment_parent']),
        'user_id' => get_current_user_id(),
    );

    $comment_id = wp_new_comment($comment_data);

    fp_log('Comment data: ' . print_r($comment_data, true));
    fp_log('Comment ID: ' . $comment_id);

    if ($comment_id) {
        fp_log('Comment added successfully.');
        $comment = get_comment($comment_id);

        $response = [
            'comment_id' => $comment_id,
            'comment_parent' => $comment_data['comment_parent'],
            'comment_author' => $comment->comment_author,
            'comment_content' => $comment->comment_content,
            'comment_date' => get_comment_date('', $comment_id),
            'comment_time' => get_comment_time(),
            'avatar' => get_avatar_url($comment->comment_author_email, ['size' => 32])
        ];

        fp_log('RESPONSE: ' . print_r($response, true));

        wp_send_json_success($response);
    } else {
        fp_log('Failed to add comment.');
        wp_send_json_error(array('message' => 'Failed to add comment.'));
    }
}
