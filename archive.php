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
wp_enqueue_style('fp-archive', FP_T_ASSETS_URI . '/css/item_single.css');
global $wp_query;
$total_post_count = $wp_query->found_posts;

$achieve_title = ucwords(single_cat_title('', false));


echo '<div class="row">';

echo '<div class="main_arc_wrapper module"><div class="content full_width_layout">';
echo '<h1 class="achieve_title">' . __($achieve_title) . '</h1>';

echo '<div class="archive_post_head_sub"><h2>' . __('Recently added') . '</h2><span>' . $total_post_count . '</span></div>';
echo '<div class="items-wrapper">';
if (have_posts()) {
    while (have_posts()) {
        the_post();
        // include\templates\item.php
        get_template_part('include/templates/item');
    }
}

?>
</div>
<div class="bw_pagination">
    <div class="nav-next"><?php previous_posts_link('<div class="pg_item"><i class="bi bi-caret-left-fill"></i>  Newer posts</div>'); ?></div>
    <div class="nav-previous"><?php next_posts_link('<div class="pg_item">Older posts <i class="bi bi-caret-right-fill"></i></div>'); ?></div>
</div>

</div>
</div>




<?php
if (function_exists('rank_math_the_breadcrumbs')) {
    echo '<div style="padding: 15px 30px;">';
    rank_math_the_breadcrumbs();
    echo '</div>';
} elseif (function_exists('yoast_breadcrumb')) {
    echo '<div style="padding: 15px 30px;">';
    yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
    echo '</div>';
}
get_footer();
