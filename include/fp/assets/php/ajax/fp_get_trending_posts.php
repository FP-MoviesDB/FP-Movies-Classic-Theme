<?php

if (!defined('ABSPATH')) exit;

function fp_get_trending_posts_main($post_type = 'post', $limit = 5)
{

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
    );
    $args['meta_query'][] = array(
        'key' => 'mtg_post_views_count',
        'compare' => 'EXISTS'
    );
    $args['orderby'] = array(
        'meta_value_num' => 'DESC',
        'date' => 'DESC',
    );
    $args['meta_key'] = 'mtg_post_views_count';

    $query = new WP_Query($args);
    $results = [];
    if ($query->have_posts()) {
        update_post_caches($query->posts, 'post', true, true);
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            // $fallback_sizes = ['fp_tp', 'thumbnail', 'medium', 'large', 'full'];
            $results[] = [
                'id' => $post_id,
                'title' => get_the_title(),
                'p_link' => get_the_permalink(),
                'post_type' => get_post_meta($post_id, 'mtg_post_type', true),
                'r_date' => get_post_meta($post_id, 'mtg_release_date', true),
                'vote' => get_post_meta($post_id, 'mtg_vote_average', true),
                't_cover' => get_post_meta($post_id, 'mtg_backdrop_path', true),
                't_img' => get_post_meta($post_id, 'mtg_poster_path', true),
                // get post content as overview
                't_overview' => get_the_content(),
                'genres' => wp_get_post_terms($post_id, 'mtg_genre', ['fields' => 'names']),
                'audio' => wp_get_post_terms($post_id, 'mtg_audio', ['fields' => 'names']),
                // 'thumb' => get_the_post_thumbnail_url($post_id, 'fp_tp')
                'thumb' => get_the_post_thumbnail_url($post_id, 'fp_tp')


            ];
        }
    }
    wp_reset_postdata();
    fp_log('Search results: ' . wp_json_encode($results));
    wp_send_json_success($results, 200);
}
