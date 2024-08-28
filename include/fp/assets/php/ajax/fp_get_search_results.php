<?php


if (!defined('ABSPATH')) exit;


function fp_adv_sortBy($sort_by)
{
    fp_log('Inside fp_adv_sortBy');
    fp_log('Sort by: ' . $sort_by[0]);

    switch ($sort_by[0]) {
        case 'trending':
            return [
                'orderby' => 'meta_value_num',
                'meta_key' => 'mtg_post_views_count',
                'order' => 'DESC'
            ];
        case 'latest':
            return [
                'orderby' => 'post_modified',
                'order' => 'DESC'
            ];
        case 'topRated':
            return [
                'orderby' => 'meta_value_num',
                'meta_key' => 'mtg_vote_average',
                'order' => 'DESC'
            ];
        case 'post_date':
            return [
                'orderby' => 'date',
                'order' => 'DESC'
            ];
        case 'modified':
            return [
                'orderby' => 'post_modified',
                'order' => 'DESC'
            ];
        case 'title':
            return [
                'orderby' => 'title',
                'order' => 'ASC'
            ];
        case 'random':
            return [
                'orderby' => 'rand'
            ];
        default:
            return [
                'orderby' => 'date',
                'order' => 'DESC'
            ];
    }
}

function fp_perform_search_callback()
{

    fp_log('Search request received');

    // verify the nonce "fp_search_nonce"
    if (!wp_verify_nonce($_POST['nonce'], 'fp_search_nonce')) {
        fp_log('Nonce verification failed');
        wp_send_json_error('Nonce verification failed', 401);
        die('Caught You !!');
    }

    fp_log('Nonce verified');


    $search_query = sanitize_text_field($_POST['search']);
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 10,
        'paged'          => $paged,
        'meta_query'     => [
            'relation' => 'OR',  // Ensure meta and tax queries are combined with OR
            [
                'key'     => 'mtg_tmdb_title',
                'value'   => $search_query,
                'compare' => 'LIKE'
            ]
        ]
    ];

    fp_log('Search query: ' . $search_query);

    // try {
    //     fp_log('Filters: ' . wp_json_encode($_POST['filters']));
    // } catch (Exception $e) {
    //     fp_log('Filters: ' . $e->getMessage());
    // }
    // check if the filters is set of not
    if (isset($_POST['filters'])) {
        // fp_log('Filters: ' . wp_json_encode($_POST['filters']));
        $filters = $_POST['filters'];

        $args['tax_query'] = [
            'relation' => 'AND'
        ];

        foreach ($filters as $taxonomy => $terms) {
            if (!empty($terms)) {
                if ($taxonomy === 'sort_by') {
                    $sort_args = fp_adv_sortBy($terms);

                    // Merge meta_query with existing ones
                    if (isset($sort_args['meta_query'])) {
                        $args['meta_query'][] = $sort_args['meta_query'];
                        unset($sort_args['meta_query']);
                    }

                    // Merge remaining sort arguments
                    $args = array_merge($args, $sort_args);
                    continue;

                    // $args = array_merge($args, fp_adv_sortBy($terms));
                    // continue;
                }
                $args['tax_query'][] = [
                    'taxonomy' => sanitize_key($taxonomy),
                    'field'    => 'slug',
                    'terms'    => array_map('sanitize_text_field', $terms),
                    'operator' => 'IN'
                ];
            }
        }
    }

    fp_log('Search args: ' . wp_json_encode($args));



    $query = new WP_Query($args);
    $results = [];
    $pagination = [];
    if ($query->have_posts()) {
        if (!empty($search_query)) {
            if (!function_exists('fp_track_search_query')) {
                require_once FP_T_ASSETS_PATH . '/helpers/fp_manage_trending_searches.php';
            }
            fp_track_search_query($search_query);
        }
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
        $max_num_pages = $query->max_num_pages;
        $pagination = [
            'current_page' => $paged,
            'total_pages'  => $max_num_pages,
            'has_prev_page' => $paged > 1,
            'has_next_page' => $paged < $max_num_pages
        ];
    }
    wp_reset_postdata();
    // fp_log('Search results: ' . wp_json_encode($results));
    wp_send_json_success([
        'results' => $results,
        'pagination' => $pagination
    ], 200);
}


function get_thumbnail_with_fallback($post_id, $sizes = [])
{
    foreach ($sizes as $size) {
        $thumb_url = get_the_post_thumbnail_url($post_id, $size);
        if ($thumb_url) {
            return $thumb_url;  // Return the URL if it exists for the current size
        }
    }
    return false;  // Return false if no thumbnail is found
}
