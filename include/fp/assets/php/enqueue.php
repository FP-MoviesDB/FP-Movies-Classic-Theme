<?php

if (!defined('ABSPATH')) exit;

function fp_t_enqueue()
{
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', array(), '1.11.3', 'all');
    wp_enqueue_style('fp-google-fonts', 'https://fonts.googleapis.com/css2?family=Nunito:wght@500;600;700;800&family=Poppins:wght@500;600;700;800&family=Roboto:wght@500;600;700;800&display=swap', array(), null, 'all');
    wp_enqueue_style('fp-t_tw-style', FP_T_URI . '/include/fp/assets/css/global.css', array(), FP_T_VERSION, 'all');
    wp_enqueue_style('fp-t_global-style', FP_T_URI . '/include/fp/assets/css/base.css', array(), FP_T_VERSION, 'all');
    wp_enqueue_script('gp-t_global-script', FP_T_URI . '/include/fp/assets/js/global.js', array('jquery'), FP_T_VERSION, true);
    $local_data = [
        'ajaxurl' => FP_T_AJAX_URL,
        'home_url' => FP_T_HOME_URL,
        'nonce'   => wp_create_nonce('fp_search_nonce'),
        'isLiveSearch' => true,
        'img_path' => FP_T_IMG_URI,
        'icon_film' => FP_T_IMG_URI . 'icon-film.png',
        'icon_calendar' => FP_T_IMG_URI . 'icon-calendar.png',
    ];
    wp_localize_script('gp-t_global-script', 'fp_sData', $local_data);
}


function fp_admin_enqueue_everywhere()
{
    if (is_user_logged_in() && current_user_can('manage_options')) {
        wp_enqueue_script('fp_theme_admin_js', FP_T_URI . '/include/fp/assets/js/admin_enqueue.js', array('jquery'), FP_T_VERSION, true);
    }
}
