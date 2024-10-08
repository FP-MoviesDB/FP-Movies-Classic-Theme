<?php

if (!defined('ABSPATH')) exit;

function fp_get_option_values($option_name)
{

    include_once FP_T_ASSETS_PATH . '/helpers/fp_global_theme_cache.php';

    $cache_key = 'fp_' . $option_name . '_terms_cache';
    $cache_path = '/terms_cache';
    $t_data = fp_t_get_cache($cache_key, $cache_path);

    if ($t_data === false) {
        switch ($option_name) {
            case 'mtg_genre':
            case 'mtg_audio':
            case 'mtg_year':
            case 'mtg_network':
            case 'category':
            case 'mtg_cast':
            case 'mtg_crew':
            case 'mtg_collection':
            case 'post_tag':
                $terms = get_terms([
                    'taxonomy' => $option_name,
                    'hide_empty' => true,
                    'orderby' => 'name',
                    'order' => 'ASC'

                ]);
                if (is_wp_error($terms)) {
                    fp_log('Error fetching terms ' . wp_json_encode($terms->get_error_message()));
                    // wp_send_json_error($terms->get_error_message(), 400);
                    return [];
                }
                // fp_log('Terms ' . wp_json_encode($terms));
                $t_data =  array_map(function ($term) {
                    return ['value' => $term->slug, 'label' => $term->name];
                }, $terms);
                // wp_send_json_success($t_data, 200);
                fp_t_set_cache($cache_key, $t_data, 12 * HOUR_IN_SECONDS, $cache_path);
                break;
                // return $t_data;
            case 'post_type':
                // this get the default categories excluding the uncategorized category
                $categories = get_categories([
                    'taxonomy' => 'category',
                    'hide_empty' => true,
                    'exclude' => [1],
                ]);
                // fp_log('Categories ' . wp_json_encode($categories));
                $t_data = array_map(function ($category) {
                    return ['value' => $category->slug, 'label' => $category->name];
                }, $categories);
                // wp_send_json_success($t_data, 200);
                // return $t_data;
                fp_t_set_cache($cache_key, $t_data, 12 * HOUR_IN_SECONDS, $cache_path);
                break;


            case 'sort_by':
                // trending, latest, topRated, post date, last modified, title, random
                $t_data = [
                    ['value' => 'trending', 'label' => 'Trending'],
                    ['value' => 'latest', 'label' => 'Latest'],
                    ['value' => 'topRated', 'label' => 'Top Rated'],
                    ['value' => 'post_date', 'label' => 'Post Date'],
                    ['value' => 'modified', 'label' => 'Modified'],
                    ['value' => 'title', 'label' => 'Title'],
                    ['value' => 'random', 'label' => 'Random']
                ];
                // wp_send_json_success($sort_by, 200);
                // return $sort_by;
                fp_t_set_cache($cache_key, $t_data, 12 * HOUR_IN_SECONDS, $cache_path);
                break;

            default:
                fp_log('Invalid option name' . $option_name);
                return [];
                // wp_send_json_error('Invalid option name', 400);
        }
    }
    return $t_data;
}
