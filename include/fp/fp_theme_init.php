<?php

if (!defined('ABSPATH')) exit;


// Register Navigation Menus
function fp_theme_register_menus()
{
    register_nav_menus(
        array(
            'header-menu' => __('Header Menu', FP_T_TEXT_DOMAIN),
            'lg-secondary-menu' => __('Secondary Menu', FP_T_TEXT_DOMAIN),
            'mobile-primary-menu' => __('Mobile Primary Menu', FP_T_TEXT_DOMAIN),
            'footer-menu' => __('Footer Menu', FP_T_TEXT_DOMAIN)
        )
    );
}

function fp_register_ajax_global()
{
    require_once FP_T_PATH . '/include/fp/assets/php/ajax/fp_get_search_results.php';
    require_once FP_T_PATH . '/include/fp/assets/php/ajax/fp_get_trending_searches_cb.php';
    require_once FP_T_PATH . '/include/fp/assets/php/ajax/fp_comment_reply.php';
    require_once FP_T_PATH . '/include/fp/assets/php/ajax/fp_get_taxonomy_list.php';
    require_once FP_T_PATH . '/include/fp/assets/php/ajax/fp_theme_manage_settings.php';

    add_action('wp_ajax_fp_perform_search',                 'fp_perform_search_callback');
    add_action('wp_ajax_fp_get_trending_searches',          'fp_get_trending_searches');
    add_action('wp_ajax_fp_get_taxonomy_data',              'fp_ajax_get_taxonomy_list');
    add_action('wp_ajax_fp_save_theme_settings',            'fp_ajax_manage_theme_settings');

    add_action('wp_ajax_nopriv_fp_perform_search',          'fp_perform_search_callback');
    add_action('wp_ajax_nopriv_fp_get_trending_searches',   'fp_get_trending_searches');

    // ┌──────────────────┐
    // │ COMMENTS AJAX  |
    // └──────────────────┘
    add_action('wp_ajax_fp_ajax_submit_comment', 'fp_movies_ajax_submit_comment');
    add_action('wp_ajax_nopriv_fp_ajax_submit_comment', 'fp_movies_ajax_submit_comment');
}


function add_custom_search_rewrite_rule()
{
    add_rewrite_rule(
        '^search/?$',
        'index.php?s=',
        'top'
    );
}

function fp_init_calls()
{

    fp_set_visitor_cookie();
    fp_theme_register_menus();
    fp_register_ajax_global();
    add_custom_search_rewrite_rule();
}

function theme_setup()
{
    add_theme_support('title-tag');
    add_image_size('fp_tp', 250, 375, true);
    add_image_size('fp_tp_medium', 500, 750, true);
    if (is_admin()) if (!(isPluginActive())) fp_notice('error', 'FP MoviesDB Plugin is not active. Please activate the plugin to use this theme. If Activated then please refresh the Page.');
}

function admin_init_calls()
{
    fp_handle_t_menu_actions();
    fp_theme_register_settings();
}



// Includes
include_once(FP_T_PATH . '/include/fp/assets/helpers/fp_global_logs.php');

if (is_admin()) {
    include_once(FP_T_PATH . '/include/fp/assets/helpers/fp_theme_updates.php');
    include_once(FP_T_PATH . '/include/fp/assets/helpers/fp_get_plugin_status.php');
}

include_once(FP_T_PATH . '/include/fp/assets/helpers/fp_admin_notices.php');
include_once(FP_T_PATH . '/include/fp/assets/helpers/fp_menu_options.php');

include_once(FP_T_PATH . '/include/fp/assets/helpers/fp_add_theme_menu.php');


include_once(FP_T_PATH . '/include/fp/assets/helpers/fp_register_theme_settings.php');

include_once(FP_T_PATH . '/include/fp/assets/keys/cache_keys.php');
include_once(FP_T_PATH . '/include/fp/assets/keys/cookies.php');

include_once(FP_T_PATH . '/include/fp/assets/helpers/fp_set_visitor_identity.php');
include_once(FP_T_PATH . '/include/fp/assets/php/enqueue.php');







// Hooks
add_theme_support('menus');
add_theme_support('widgets');

remove_theme_support('widgets-block-editor');

add_action('init', 'fp_init_calls');
add_action('admin_init', 'admin_init_calls');

add_action('wp_enqueue_scripts', 'fp_t_enqueue');
add_action('wp_footer', 'add_customizer_css_last', 100);

// ENQUEUE SCRIPTS EVERYWHERE [ADMIN LOGIN + DASHBOARD / FRONTEND]
add_action('wp_enqueue_scripts', 'fp_admin_enqueue_everywhere');
add_action('admin_enqueue_scripts', 'fp_admin_enqueue_everywhere');

// ENQUEUE SCRIPTS + CSS ONLY THEME PAGE
add_action('admin_enqueue_scripts', 'fp_enqueue_theme_options_page');



add_action('after_setup_theme', 'theme_setup');


add_action('admin_notices', 'fp_show_transient_notice');

// add_action( 'save_post', 'changePostLastModifiedTime', 10, 2 );

add_action('admin_bar_menu', 'fp_movie_t_admin_bar', 100, 1);
add_action('admin_menu', 'fp_theme_options_menu');
