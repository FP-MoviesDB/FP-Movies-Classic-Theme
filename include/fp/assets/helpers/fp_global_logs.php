<?php

if (!defined('ABSPATH')) exit;

if (!defined('FP_T_LOG_FILE')) define('FP_T_LOG_FILE', FP_T_PATH . '/logs/error_logs.txt');

if (!function_exists('fp_log')) {
    function fp_log($message, $context = 'PHP')
    {
        if (FP_T_LOGS === false || !defined('FP_T_LOGS')) return;

        // convert message to string
        if (is_array($message) || is_object($message)) {
            $message = wp_json_encode($message);
        }

        $message = (string) $message;

        if (!file_exists(dirname(FP_T_LOG_FILE))) mkdir(dirname(FP_T_LOG_FILE), 0777, true);
        $logMessage = date('Y-m-d H:i:s') . " - [$context] - $message\n";
        file_put_contents(FP_T_LOG_FILE, $logMessage, FILE_APPEND);
    }
}

if (!function_exists('fp_t_ajax_log_error')) {
    function fp_t_ajax_log_error()
    {
        if (isset($_POST['logMessage']) && isset($_POST['logContext'])) {
            fp_log($_POST['logMessage'], $_POST['logContext']);
        }
        wp_die();
    }
}

add_action('wp_ajax_log_javascript_error', 'fp_t_ajax_log_error');
add_action('wp_ajax_nopriv_log_javascript_error', 'fp_t_ajax_log_error');
