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
update_post_caches($posts, 'post', true, true);
// Get post meta
$postmeta = get_post_meta($post->ID);

// Get post title and link
$post_title = get_the_title();
$post_link = get_permalink();

// Get and set poster image
$tmdb_img = isset($postmeta['mtg_poster_path'][0]) ? $postmeta['mtg_poster_path'][0] : '';
$tmdb_img = $tmdb_img ? 'https://image.tmdb.org/t/p/w300' . $tmdb_img : '';
$poster_url = $tmdb_img ? $tmdb_img : get_the_post_thumbnail_url($post->ID, 'fp_tp');

// Get and set cover image
$tmdb_cover = isset($postmeta['mtg_backdrop_path'][0]) ? $postmeta['mtg_backdrop_path'][0] : '';

// Get and set average vote
$avg_vote = isset($postmeta['mtg_vote_average'][0]) ? $postmeta['mtg_vote_average'][0] : '';

// Get and set storyline
$storyline = isset($postmeta['mtg_storyline'][0]) ? $postmeta['mtg_storyline'][0] : get_the_content();
$storyline = wp_trim_words($storyline, 25, '...');

// Get and set modified date
$modified_date = isset($postmeta['fp_post_modified'][0]) ? $postmeta['fp_post_modified'][0] : (get_the_modified_date() ?? get_the_date());

// Get and set gradient
$gradient = isset($postmeta['mtg_gradient_color'][0]) ? $postmeta['mtg_gradient_color'][0] : 'linear-gradient(#222, #222), linear-gradient(to right, #c49811, #30ab17)';

// Taxonomies -> Get 1st term for network
$network_tax = get_the_terms($post->ID, 'mtg_network');
$network = $network_tax && isset($network_tax[0]) ? $network_tax[0]->name : false;

// Taxonomies -> Get 1st term for quality
$quality_tax = get_the_terms($post->ID, 'mtg_quality');
$quality = isset($quality_tax[0]) ? $quality_tax[0]->name : 'HD';

// Taxonomies -> Get last term for year
$year_tax = get_the_terms($post->ID, 'mtg_year');
$year = $year_tax && isset($year_tax[count($year_tax) - 1]) ? $year_tax[count($year_tax) - 1]->name : false;



?>

<article id="post-<?php the_ID(); ?>" class="item" style="background-image: <?php echo $gradient; ?>;">
    <div class="poster">
        <a href="<?php the_permalink() ?>"><img src="<?php echo $poster_url; ?>" alt="<?php the_title(); ?>" width="200px" height="300px"></a>

        <div class="meta_n">
            <?php echo ($network) ? '<span class="meta_base network">' . $network . '</span>' : ''; ?>
        </div>
        <div class="meta_q">
            <?php echo ($quality) ? '<span class="meta_base quality">' . $quality . '</span>' : ''; ?>
        </div>
        <div class="meta_i">
            <?php if ($avg_vote != 0) {
                echo '<span class="meta_base imdb"><i class="bi bi-star-fill" style=""></i>' . $avg_vote . '</span>';
            } ?>
        </div>

    </div>
    <div class="data">
        <a href="<?php the_permalink() ?>">
            <h5><?php the_title(); ?></h5>
        </a>
    </div>
</article>