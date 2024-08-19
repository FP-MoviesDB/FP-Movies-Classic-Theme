<?php
/*
* ----------------------------------------------------
* @author: FP Movies DB
* @author URI: https://google.com/
* @copyright: (c) 2024 | All rights reserved
* ----------------------------------------------------
* @since 1.0
*/

# The variables
if (!defined('FP_T_PATH')) define('FP_T_PATH', get_theme_file_path());
if (!defined('FP_T_URI')) define('FP_T_URI', get_theme_file_uri());
if (!defined('FP_T_VERSION')) define('FP_T_VERSION', time());
if (!defined('FP_T_REST_URL')) define('FP_T_REST_URL', esc_url_raw(rest_url()));
if (!defined('FP_T_HOME_URL')) define('FP_T_HOME_URL', esc_url_raw(home_url()));
if (!defined('FP_T_AJAX_URL')) define('FP_T_AJAX_URL', admin_url('admin-ajax.php', 'https'));
if (!defined('FP_T_CACHE_DIR')) define('FP_T_CACHE_DIR', WP_CONTENT_DIR . '/cache/fp_movies');
if (!defined('FP_T_ASSETS_URI')) define('FP_T_ASSETS_URI', FP_T_URI . '/include/fp/assets/');
if (!defined('FP_T_ASSETS_PATH')) define('FP_T_ASSETS_PATH', FP_T_PATH . '/include/fp/assets/');
if (!defined('FP_T_IMG_URI')) define('FP_T_IMG_URI', FP_T_ASSETS_URI . 'img/');
if (!defined('FP_T_TEXT_DOMAIN')) define('FP_T_TEXT_DOMAIN', 'fp-movies-theme');
if (!defined('FP_T_LOGS')) define('FP_T_LOGS', false);
if (!defined('FP_MOVIES_THEME_FILE')) define('FP_MOVIES_THEME_FILE', __FILE__);


# The includes
include_once FP_T_PATH . '/include/fp/fp_theme_init.php';