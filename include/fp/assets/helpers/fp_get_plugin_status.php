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

function isPluginActive()
{
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        // fp_log('Plugin file included');
    }

    // Check if the plugin is active
    if (is_plugin_active('fp-moviesdb/fp_movies.php')) {
        // fp_log('Plugin is active');
        return true;
    }
    // fp_log('Plugin is not active');
    return false;
}
