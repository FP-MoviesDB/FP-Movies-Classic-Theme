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

function fp_ajax_manage_theme_settings() {
    
        // verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'fp_save_theme_settings')) {
            die('Invalid nonce');
        }

        $tSettings = $_POST['settings'];

        $tSettings['basic_settings']['footer_text'] = wp_kses_post(stripslashes($tSettings['basic_settings']['footer_text']));

        update_option('fp_theme_all_settings', $tSettings);

        set_transient('fp_t_admin_notice', ['success', 'Settings saved successfully'], 30);


        wp_send_json_success(['message' => 'Settings saved successfully'], 200);
        
}