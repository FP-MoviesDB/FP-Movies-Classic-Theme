<?php

if (!defined('ABSPATH')) exit;

function fp_get_trending_searches_main($search_limit = 20) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'fp_trending_searches';
    $cache_key = FP_T_CK['ts']['key'];

    $cached_data = get_transient($cache_key);

    if (!empty($cached_data) && is_array($cached_data)) {
        return $cached_data;
    }

    $results = $wpdb->get_results("SELECT search_term, search_count FROM $table_name ORDER BY search_count DESC, last_searched DESC LIMIT $search_limit", OBJECT);
    if (count($results) >= 6) {
        set_transient($cache_key, $results, FP_T_CK['ts']['time']);
    }

    return $results;
}


function fp_get_trending_searches() {
    // verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'wp_fp_trending_searches')) {
        die('Invalid nonce');
    }

    $trending_searches = fp_get_trending_searches_main();

    return wp_send_json_success(array(
        'data' => $trending_searches,
        'cached' => !empty($cached_data)
    ));
}
