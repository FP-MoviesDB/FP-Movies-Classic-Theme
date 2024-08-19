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

function fp_ajax_get_taxonomy_list()
{

    // verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'fp_get_taxonomy_list')) {
        die('Invalid nonce');
    }

    // Check if taxonomy is set and sanitize it
    if (!isset($_POST['taxonomy']) || empty($_POST['taxonomy'])) {
        wp_send_json_error('Taxonomy type not specified', 400);
    }

    // Type of Taxonomy
    $taxonomy = sanitize_text_field($_POST['taxonomy']);


    // Get Taxonomy List
    $taxonomies = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ));

    // Check if the taxonomy exists to avoid unnecessary queries
    if (!taxonomy_exists($taxonomy)) {
        wp_send_json_error('Invalid taxonomy type', 404);
    }

    $tax_list = [];
    foreach ($taxonomies as $tax) {
        $tax_list[] = [
            'id' => $tax->term_id,
            'name' => $tax->name,
            'slug' => $tax->slug,
        ];
    }

    wp_send_json_success($tax_list, 200);
}
