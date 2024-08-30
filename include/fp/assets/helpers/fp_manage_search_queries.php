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

function fp_t_clear_search_query_cache_folder() {
    if (!function_exists('fp_t_clear_all_cache')) {
        require_once FP_T_ASSETS_PATH .'helpers/fp_global_theme_cache.php';
    }

    fp_t_clear_all_cache('/search_queries');

}