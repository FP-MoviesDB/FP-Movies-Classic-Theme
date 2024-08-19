<?php

if (!defined('ABSPATH')) exit;

// v = visitor, t = time
$FP_T_COOK = [
    'v_id' => [
        'k' => 'fp_theme_visitor_id',
        't' => DAY_IN_SECONDS * 30
    ],
    'v_ip' => [
        'k' => 'fp_theme_visitor_ip',
        't' => DAY_IN_SECONDS * 30
    ],
    'v_id_ip_v' => [
        'k' => 'fp_theme_visitor_id_ip_v',
        't' => DAY_IN_SECONDS * 30
    ],
];

if (!defined('FP_T_COOK')) define('FP_T_COOK', $FP_T_COOK);