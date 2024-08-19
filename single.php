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

get_header();

include_once(FP_T_PATH . '/include/templates/single.php');

if (function_exists('rank_math_the_breadcrumbs')) {
    echo '<div style="padding: 15px 30px;">';
    rank_math_the_breadcrumbs();
    echo '</div>';
} elseif (function_exists('yoast_breadcrumb')) {
    echo '<div style="padding: 15px 30px;">';
    yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
    echo '</div>';
}


if (comments_open() && !post_password_required()) {
    comments_template();
}

get_footer();
