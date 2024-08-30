<?php

if (!defined('ABSPATH')) exit;

$FP_T_CK = [
    // ts = trending searches
    'ts' => [
        'key' => 'fp_theme_trending_searches',
        'time' => 60 * 60
    ],
    // sq = search queries
    'sq' => [
        'key' => 'fp_theme_search_queries',
        'time' => 60 * 60 * 24
    ]
];

if (!defined('FP_T_CK')) define('FP_T_CK', $FP_T_CK);