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


$homepage_settings = get_option('fp_theme_all_settings')['customize_settings']['homepage_layout'] ?? [];

// fp_log($get_homepage_settings);

$shortcodes = [];

if (!empty($homepage_settings) && is_array($homepage_settings)) {
    foreach ($homepage_settings as $key => $value) {
        
        $shortcode = "";

        $type = $value['type'];
        $content_type = $value['content_type'];
        $limit = $value['limit'];
        $title_background = $value['title_background'];
        $image_source = $value['image_source'];
        $image_size = $value['image_size'];

        $shortcode = "[fp-homepage-view type='{$type}' content_type='{$content_type}' limit='{$limit}' title_background='{$title_background}' image_source='{$image_source}' image_size='{$image_size}'";


        if ($type !== 'featured') {
            $heading = $value['heading'];
            $show_rating = $value['show_ratings'];
            $show_quality = $value['show_quality'];

            $shortcode .= " heading='{$heading}' show_rating='{$show_rating}' show_quality='{$show_quality}'";
        }

        if ($type === 'taxonomy') {
            $taxonomy = $value['taxonomy'];

            $shortcode .= " taxonomy='{$taxonomy}'";
        }

        $shortcode .= "]";

        $shortcodes[] = $shortcode;
    }

    foreach ($shortcodes as $sh) {
        echo do_shortcode($sh);
    }
} else {

    $fallback = [
        "[fp-homepage-view type='featured' content_type='movie' title_background='gradient' image_source='local']",
        "[fp-homepage-view type='meta' content_type='movie' heading='Movies']",
        "[fp-homepage-view type='meta' content_type='series' heading='All Series']",
        "[fp-homepage-view type='featured' content_type='series' title_background='gradient' image_source='local']",
    ];

    foreach ($fallback as $fb) {
        echo do_shortcode($fb);
    }
}
