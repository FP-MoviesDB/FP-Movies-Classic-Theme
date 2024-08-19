<?php

if (!defined('ABSPATH')) exit;

function fp_create_trending_searches_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'fp_trending_searches';

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                search_term varchar(255) NOT NULL,
                search_count bigint(20) NOT NULL DEFAULT 1,
                last_searched datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY search_term (search_term)
            ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function fp_reset_search_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'fp_trending_searches';

    // SQL to delete all rows
    $transaction = $wpdb->query("TRUNCATE TABLE `$table_name`");

    // clear its cache from transient
    delete_transient(FP_T_CK['ts']['key']);

    fp_log('Search Table Transaction: ' . ($transaction ? 'Success' : 'Failed') . ' | ORG RESPONSE: ' . $transaction);

    return true;
}


function fp_track_search_query($sQuery)
{
    // fp_log('fp_track_search_query()');
    // if (is_search() && get_search_query()) {
    fp_log('Search Query: ' . get_search_query());
    global $wpdb;
    // $term = get_search_query();
    $term = $sQuery;
    $table_name = $wpdb->prefix . 'fp_trending_searches';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        fp_create_trending_searches_table();
    }

    $stop_words = array('the', 'a', 'an', 'and', 'but', 'or', 'on', 'in', 'with', 'at', 'from', 'by', 'to', 'of', 'for', 'as', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'can', 'could', 'shall', 'should', 'will', 'would', 'may', 'might', 'must', 'am', 'is', 'are', 'was', 'were', 'be', 'being', 'been', 'have', 'has', 'had', 'do', 'does');
    
    // $stop_words = array('the');

    // Remove stop words from the search term
    $filtered_term = preg_replace('/\b(' . implode('|', $stop_words) . ')\b/i', '', $term);
    $filtered_term = trim(preg_replace('/\s+/', ' ', $filtered_term)); // Remove extra spaces

    fp_log('Filtered Term: ' . $filtered_term);

    // Proceed only if the filtered term is not empty
    if (!empty($filtered_term)) {

        $visitor_id = $_COOKIE[FP_T_COOK['v_id']['k']] ?? null;
        // check if its admin
        if (current_user_can('manage_options')) {
            $visitor_id = 'admin';
        }

        fp_log('Visitor ID: ' . $visitor_id);
        if ($visitor_id === null) return;

        $cookie_name = 'fp_s_' . md5($filtered_term . $visitor_id);
        fp_log('Cookie Name: ' . $cookie_name);

        $search_data = $_COOKIE[$cookie_name] ?? null;
        fp_log('Search Data: ' . $search_data);

        // if ($search_data === null) return;


        if ($search_data !== null) {
            $last_searched = unserialize($search_data);
            if (time() - $last_searched <= 12 * HOUR_IN_SECONDS) return;
        }

        fp_log('Search term not found in cookie or last searched more than 12 hours ago');

        if ($visitor_id === 'admin') {
            fp_log('Admin user, SKIPPED : search term to database');
            return;
        }


        $query = $wpdb->prepare("SELECT id FROM $table_name WHERE search_term = %s", $filtered_term);
        $exists = $wpdb->get_var($query);

        if ($exists) {
            $wpdb->query($wpdb->prepare("UPDATE $table_name SET search_count = search_count + 1, last_searched = NOW() WHERE id = %d", $exists));
            fp_log('Search term already exists, updated search count');
        } else {
            $wpdb->insert(
                $table_name,
                array(
                    'search_term' => $filtered_term,
                    'search_count' => 1,
                    'last_searched' => current_time('mysql')
                ),
                array('%s', '%d', '%s')
            );
            fp_log('Search term added to database');
        }

        setcookie($cookie_name, serialize(time()), time() + (12 * HOUR_IN_SECONDS), '/');
        fp_log('Cookie set for 12 hours');
    }
    // }
}
