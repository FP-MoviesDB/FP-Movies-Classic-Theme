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

if (!function_exists('fp_handle_t_menu_actions')) {
    function fp_handle_t_menu_actions()
    {

        $redirect_to = isset($_GET['redirect_to']) ? urldecode($_GET['redirect_to']) : admin_url();

        if (isset($_GET['action']) && $_GET['action'] == 'fp_t_clear_trending_items' && check_admin_referer('fp_t_clear_trending_items_action')) {
            if (!function_exists(('fp_reset_search_table'))) {
                require_once FP_T_ASSETS_PATH . '/helpers/fp_manage_trending_searches.php';
            }
            fp_log('Request to Reset Table');
            fp_reset_search_table();
            // if ($reset_status) {
            fp_log('Table Reset Successfully');
            set_transient('fp_t_admin_notice', array('success', 'Trending Search List Cleared Successfully.'), 30);
            // } else {
            //     fp_log('Table Reset Failed');
            //     set_transient('fp_t_admin_notice', array('error', 'Failed to Clear Trending Search List'), 30);
            // }
            wp_redirect($redirect_to);
            exit();
        }

        fp_log('Admin Menu Action Handler Called Successfully');
    }
}

if (!function_exists('fp_movie_t_admin_bar')) {
    function fp_movie_t_admin_bar($fp_admin_bar)
    {
        // global $post;

        if (!function_exists('is_plugin_active'))   require_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (is_plugin_active('fp-moviesdb/fp-moviesdb.php')) {
            $href_URL = admin_url('admin.php?page=mts_generator');
        } else {
            $href_URL = admin_url('admin.php?page=fp-theme-options');
        }


        if (!$fp_admin_bar->get_node('fp_movies')) {
            $args = array(
                'id'    => 'fp_movies',                 // Node ID
                'title' => 'FP Movies',                 // Node title
                'href'  => $href_URL,                   // URL to visit when the node is clicked
                'meta'  => array(
                    'class' => 'fp-movies-admin-bar'    // CSS class for the node
                )
            );
            $fp_admin_bar->add_node($args); // Add the node to the admin bar
        }

        // admin page or search page opened
        if (is_admin() && current_user_can('manage_options') || is_search()) {
            $args = array(
                'id'    => 'fp_t_clear_trending_items',
                'parent' => 'fp_movies',
                'title' => 'Clear Trending Search List',
                'href'  => wp_nonce_url(admin_url('admin.php?action=fp_t_clear_trending_items' . '&redirect_to=' . urlencode($_SERVER['REQUEST_URI'])), 'fp_t_clear_trending_items_action'),
                'meta'  => array(
                    'class' => 'fp-t-clear-trending-items fp-clear-trending-confirm'
                )
            );
            $fp_admin_bar->add_node($args);
        }
    }
}
