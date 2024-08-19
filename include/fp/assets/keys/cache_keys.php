<?php

if (!defined('ABSPATH')) exit;

$FP_T_CK = [
    'ts' => [
        'key' => 'fp_theme_trending_searches',
        'time' => 60 * 60
    ]
];

if (!defined('FP_T_CK')) define('FP_T_CK', $FP_T_CK);