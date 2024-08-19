<?php

if (!defined('ABSPATH')) exit;



function fp_set_visitor_cookie()
{

    if (is_admin()) return;

    if (headers_sent()) {
        fp_log('Headers already sent, cannot set cookies.');
        return;
    }


    if (current_user_can('manage_options')) return;

    require_once(FP_T_ASSETS_PATH . '/helpers/fp_manage_encryption.php');
    require_once(FP_T_ASSETS_PATH . '/helpers/fp_get_user_ip.php');

    $encryptionHandler = new FP_EncryptionHandler();
    $user_request_url = $_SERVER['REQUEST_URI'];

    if (isset($_COOKIE[FP_T_COOK['v_id']['k']], $_COOKIE[FP_T_COOK['v_ip']['k']], $_COOKIE[FP_T_COOK['v_id_ip_v']['k']])) {
        if ($encryptionHandler->verifyHash($_COOKIE[FP_T_COOK['v_ip']['k']], $_COOKIE[FP_T_COOK['v_id_ip_v']['k']])) {
            fp_log('OLD Visitor, URL Request: ' . $user_request_url);
            return;
        }
    }

    // if its wordpress request, then return
    if (strpos($user_request_url, 'wp-admin') !== false) {
        fp_log('WP Admin Request IGNORED Cookies: ' . $user_request_url);

        return;
    }


    $visitor_id = md5(uniqid(rand(), true));
    $visitor_ip = get_the_user_ip();
    $encrypted_ip = $encryptionHandler->encryptData($visitor_ip);
    $ip_hash = hash('sha256', $encrypted_ip . $encryptionHandler->getEncryptionKey());

    

    fp_log('Visitor ID: ' . $visitor_id . ' - Visitor IP: ' . $visitor_ip . ' - Encrypted IP: ' . $encrypted_ip . ' - IP Hash: ' . $ip_hash . ' - Request URL: ' . $user_request_url);

    setcookie(FP_T_COOK['v_id']['k'], $visitor_id, time() + FP_T_COOK['v_id']['t'], '/');
    setcookie(FP_T_COOK['v_ip']['k'], $encrypted_ip, time() + FP_T_COOK['v_ip']['t'], '/');
    setcookie(FP_T_COOK['v_id_ip_v']['k'], $ip_hash, time() + FP_T_COOK['v_id_ip_v']['t'], '/');
}
