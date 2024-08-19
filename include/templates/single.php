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

$singlePageSettings = get_option('fp_theme_all_settings')['customize_settings']['single_layout'] ?? [];

$shortcodes = [];

if (!empty($singlePageSettings) && is_array($singlePageSettings)) {
    foreach ($singlePageSettings as $key => $value) {
        $sh = $value['shortcode'];
        fp_log("Shortcode START: {$sh}");

        $shortcode = "[" . $sh;
        if ($sh === 'fp-universal-view'){
            $content_type = $value['content_type'];
            $content = $value['content'];
            // $content = base64_encode($content);
            $shortcode .= " content=" . $content . " type=" . $content_type;
        }

        $shortcode .= "]";

        fp_log("Shortcode ENDDDDD: {$shortcode}");

        $shortcodes[] = $shortcode;
    }
} else {
    $shortcodes[] = '[fp-post-player]';
    $shortcodes[] = '[fp-post-title]';
    $shortcodes[] = '[fp-imdb-box-view]';
    $shortcodes[] = '[fp-synopsis-view]';
    $shortcodes[] = '[fp-post-info]';
    $shortcodes[] = '[fp-screenshot-view]';
    $shortcodes[] = '[fp-post-links]';
}

foreach ($shortcodes as $sh) {
    echo do_shortcode($sh);
}