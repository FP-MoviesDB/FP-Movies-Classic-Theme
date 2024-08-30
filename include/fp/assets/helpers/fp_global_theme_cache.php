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

$SEARCH_DIR = FP_T_CACHE_DIR . '/search_queries';

function create_cache_directory($DIR)
{
    if (!is_dir($DIR)) mkdir($DIR, 0755, true);
}


function fp_t_set_cache($key, $data, $expiry = 60 * 60 * 24, $JOIN_PATH = '')
{
    if (empty($key) || empty($data) || empty($JOIN_PATH)) return false;
    $DIR = FP_T_CACHE_DIR . $JOIN_PATH;
    create_cache_directory($DIR);
    $C_FILE = $DIR . '/' . md5($key) . '.cache';
    $data = serialize(['data' => $data, 'expiry' => time() + $expiry]);
    if (!file_put_contents($C_FILE, $data)) return false;
    return true;
}


function fp_t_get_cache($key, $JOIN_PATH = '')
{
    if (empty($key) || empty($JOIN_PATH)) return false;
    $C_FILE = FP_T_CACHE_DIR . $JOIN_PATH . '/' . md5($key) . '.cache';

    if (!file_exists($C_FILE)) return false;

    $FILE_CONTENTS = file_get_contents($C_FILE);
    if ($FILE_CONTENTS === false) return false;

    $FILE_CONTENTS = unserialize($FILE_CONTENTS);
    if ($FILE_CONTENTS === false) return false;

    if (time() > $FILE_CONTENTS['expiry']) {
        wp_delete_file($C_FILE);
        return false;
    }

    return $FILE_CONTENTS['data'];
}

function fp_t_delete_cache($key, $JOIN_PATH = '')
{
    if (empty($key) || empty($JOIN_PATH)) return false;
    $C_FILE = FP_T_CACHE_DIR . $JOIN_PATH . '/' . md5($key) . '.cache';
    unlink($C_FILE);
    return true;
}

function fp_t_clear_all_cache($JOIN_PATH = '')
{
    if (empty($JOIN_PATH)) return false;
    $DIR = FP_T_CACHE_DIR . $JOIN_PATH;
    if (is_dir($DIR)) {
        fp_t_delete_directory($DIR);
        create_cache_directory($DIR);
        return true;
    }
    return false;
}

if (!function_exists('fp_t_delete_directory')) {
    function fp_t_delete_directory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        // $items = new RecursiveIteratorIterator(
        //     new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        //     RecursiveIteratorIterator::CHILD_FIRST
        // );

        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $file) {
            $filePath = "$dir/$file";
            is_dir($filePath) ? fp_t_delete_directory($filePath) : wp_delete_file($filePath);
        }
        rmdir($dir);
    }
}
