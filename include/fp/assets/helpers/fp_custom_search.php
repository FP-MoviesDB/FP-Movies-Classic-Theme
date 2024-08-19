<?php


if (!defined('ABSPATH')) exit;



function fp_custom_search_where($where, $wp_query)
{
    global $wpdb;
    if ($post_search_term = $wp_query->get('search_post_title_only')) {
        $where .= " OR {$wpdb->posts}.post_title LIKE '%" . esc_sql($wpdb->esc_like($post_search_term)) . "%'";
    }
    return $where;
}
