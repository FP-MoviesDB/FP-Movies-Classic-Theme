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

function fp_movies_classic_theme_update_check($checked_data)
{
    global $wp_version;

    // Theme information
    
    $theme_data = wp_get_themes();
    $theme_version = '';
    $stylesheet = '';

    // track TIme
    // $time = microtime(true);
    // fp_log("Starting Time: " . $time);
    foreach ($theme_data as $theme) {
        if ($theme->get('Name') === 'FP Movies Classic Theme' || $theme->get('TextDomain') === 'fp-movies-classic-theme') {
            // fp_log("Theme Found");
            // fp_log("Name: " . $theme->get('Name'));
            // fp_log("TextDomain: " . $theme->get('TextDomain'));
            $theme_version = $theme->get('Version');
            $stylesheet = $theme->get_stylesheet();
            break;
        }
    }

    // END track Time
    // $duration = microtime(true) - $time;
    // fp_log("Time: " . $duration);

    if (empty($theme_version) || empty($stylesheet)) {
        return $checked_data;
    }

    // Update details
    $siteURL = get_site_url();
    $update_url = 'https://fp-classic-theme.fpmoviesdb.xyz/?referer=' . $siteURL;
    $changelog_url = 'https://fp-classic-theme.fpmoviesdb.xyz/changelog.html';

    // Only check for update for our theme
    if (isset($checked_data->checked[$stylesheet])) {
        $request = wp_remote_post($update_url, array(
            'body' => array(
                'action' => 'theme_update',
                'version' => $theme_version,
                'wp_version' => $wp_version,
            ),
        ));

        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            $response = json_decode(wp_remote_retrieve_body($request), true);

            if (version_compare($theme_version, $response['new_version'], '<')) {
                $checked_data->response['fp-movies-classic-theme'] = array(
                    'new_version' => $response['new_version'],
                    'package' => $response['package'],
                    'url' => $changelog_url,
                );
            }
        }
    }

    return $checked_data;
}


function fp_movies_classic_theme_update_info($result, $action, $args)
{

    if ($action == 'theme_information' && isset($args->slug) && $args->slug == 'fp-movies-classic-theme') {
        $update_url = 'https://fp-classic-theme.fpmoviesdb.xyz/';

        $request = wp_remote_get($update_url);
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            $response = json_decode(wp_remote_retrieve_body($request), true);
            $result = $response;
        }
    }

    return $result;
}

add_filter('pre_set_site_transient_update_themes', 'fp_movies_classic_theme_update_check');
add_filter('themes_api', 'fp_movies_classic_theme_update_info', 10, 3);
