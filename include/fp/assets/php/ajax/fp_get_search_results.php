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

    // Verify the nonce "fp_search_nonce"
    if (!wp_verify_nonce($_POST['nonce'], 'fp_search_nonce')) {
        fp_log('Nonce verification failed');
        wp_send_json_error('Nonce verification failed', 401);
        die('Caught You !!');
    }

    fp_log('Nonce verified');

    $search_query = sanitize_text_field($_POST['search']);
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    include_once FP_T_ASSETS_PATH . '/helpers/fp_global_theme_cache.php';

    $filters = $_POST['filters'] ?? [];
    $filters_key = md5(json_encode($filters));
    $cache_key = md5("{$search_query}_page_{$paged}_filters_{$filters_key}");
    
    $cached_results = fp_t_get_cache($cache_key, '/search_queries');
    if ($cached_results) {
        fp_log('Cache hit, returning cached results');
        wp_send_json_success($cached_results, 200);
        return;
    }

    // First, create the query to get exact matches
    $args_exact = [
        'post_type'      => 'post',
        'posts_per_page' => 10,
        'paged'          => $paged,
        'meta_query'     => [
            [
                'key'     => 'mtg_tmdb_title',
                'value'   => $search_query,
                'compare' => '='
            ]
        ]
    ];

    // Then create the query for partial matches, excluding exact matches
    $args_partial = [
        'post_type'      => 'post',
        'posts_per_page' => 10, // Will adjust after getting exact matches
        'paged'          => $paged,
        'meta_query'     => [
            [
                'key'     => 'mtg_tmdb_title',
                'value'   => $search_query,
                'compare' => 'LIKE'
            ]
        ],
        'post__not_in'   => [], // Placeholder for exclusion of exact matches
    ];

    fp_log('Search query: ' . $search_query);

    // Apply filters if set
    if (!empty($filters)) {
        $args_partial['tax_query'] = $args_exact['tax_query'] = [
            'relation' => 'AND'
        ];

        foreach ($filters as $taxonomy => $terms) {
            if (!empty($terms)) {
                if ($taxonomy === 'sort_by') {
                    $sort_args = fp_adv_sortBy($terms);

                    // Merge meta_query with existing ones
                    if (isset($sort_args['meta_query'])) {
                        $args_exact['meta_query'][] = $sort_args['meta_query'];
                        $args_partial['meta_query'][] = $sort_args['meta_query'];
                        unset($sort_args['meta_query']);
                    }

                    // Merge remaining sort arguments
                    $args_exact = array_merge($args_exact, $sort_args);
                    $args_partial = array_merge($args_partial, $sort_args);
                    continue;
                }
                $tax_query = [
                    'taxonomy' => sanitize_key($taxonomy),
                    'field'    => 'slug',
                    'terms'    => array_map('sanitize_text_field', $terms),
                    'operator' => 'IN'
                ];

                $args_exact['tax_query'][] = $tax_query;
                $args_partial['tax_query'][] = $tax_query;
            }
        }
    }

    // fp_log('Search args (exact): ' . wp_json_encode($args_exact));

    // Execute exact match query
    $query_exact = new WP_Query($args_exact);
    $exact_results = [];

    if ($query_exact->have_posts()) {
        while ($query_exact->have_posts()) {
            $query_exact->the_post();
            $post_id = get_the_ID();
            $exact_results[] = [
                'id' => $post_id,
                'title' => get_the_title(),
                'p_link' => get_the_permalink(),
                'post_type' => get_post_meta($post_id, 'mtg_post_type', true),
                'r_date' => get_post_meta($post_id, 'mtg_release_date', true),
                'vote' => get_post_meta($post_id, 'mtg_vote_average', true),
                't_cover' => get_post_meta($post_id, 'mtg_backdrop_path', true),
                't_img' => get_post_meta($post_id, 'mtg_poster_path', true),
                't_overview' => get_the_content(),
                'genres' => wp_get_post_terms($post_id, 'mtg_genre', ['fields' => 'names']),
                'audio' => wp_get_post_terms($post_id, 'mtg_audio', ['fields' => 'names']),
                'thumb' => get_the_post_thumbnail_url($post_id, 'fp_tp')
            ];
        }

        // Exclude exact matches from the partial match query
        $args_partial['post__not_in'] = wp_list_pluck($exact_results, 'id');
    }

    // Adjust the number of posts per page for the partial match query
    $args_partial['posts_per_page'] = 10 - count($exact_results);

    // fp_log('Search args (partial): ' . wp_json_encode($args_partial));

    // Execute partial match query
    $query_partial = new WP_Query($args_partial);
    $partial_results = [];

    if ($query_partial->have_posts()) {
        while ($query_partial->have_posts()) {
            $query_partial->the_post();
            $post_id = get_the_ID();
            $partial_results[] = [
                'id' => $post_id,
                'title' => get_the_title(),
                'p_link' => get_the_permalink(),
                'post_type' => get_post_meta($post_id, 'mtg_post_type', true),
                'r_date' => get_post_meta($post_id, 'mtg_release_date', true),
                'vote' => get_post_meta($post_id, 'mtg_vote_average', true),
                't_cover' => get_post_meta($post_id, 'mtg_backdrop_path', true),
                't_img' => get_post_meta($post_id, 'mtg_poster_path', true),
                't_overview' => get_the_content(),
                'genres' => wp_get_post_terms($post_id, 'mtg_genre', ['fields' => 'names']),
                'audio' => wp_get_post_terms($post_id, 'mtg_audio', ['fields' => 'names']),
                'thumb' => get_the_post_thumbnail_url($post_id, 'fp_tp')
            ];
        }
    }

    // Combine exact and partial results, with exact matches first
    $results = array_merge($exact_results, $partial_results);

    $max_num_pages = max($query_exact->max_num_pages, $query_partial->max_num_pages);
    $pagination = [
        'current_page' => $paged,
        'total_pages'  => $max_num_pages,
        'has_prev_page' => $paged > 1,
        'has_next_page' => $paged < $max_num_pages
    ];

    wp_reset_postdata();
    $cache_data = [
        'results' => $results,
        'pagination' => $pagination
    ];

    fp_t_set_cache($cache_key, $cache_data, FP_T_CK ['sq']['time'], '/search_queries');
    fp_log('Served fresh results and cached them');

    wp_send_json_success($cache_data, 200);
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
