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
    $theme_data = wp_get_theme('FP Movies Classic Theme');
    $theme_version = $theme_data->get('Version');

    // Update details
    $update_url = 'https://fp-classic-theme.fpmoviesdb.xyz/';
    $changelog_url = 'https://fp-classic-theme.fpmoviesdb.xyz/changelog.html';

    // Only check for update for our theme
    if (isset($checked_data->checked['fp-movies-classic-theme'])) {
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
