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

wp_enqueue_style('fp-adv-search', FP_T_ASSETS_URI . 'css/adv_search.css', array(), FP_T_VERSION, 'all');

?>

<div class="search-results-container">
    <?php

    $search_query = get_search_query();

    if (!function_exists('fp_get_option_values')) {
        require_once(FP_T_ASSETS_PATH . 'php/ajax/fp_get_option_values.php');
    }

    $options = [
        'category' => fp_get_option_values('category'),
        'mtg_genre'   => fp_get_option_values('mtg_genre'),
        'mtg_audio'    => fp_get_option_values('mtg_audio'),
        'mtg_year'    => fp_get_option_values('mtg_year'),
        'mtg_network'    => fp_get_option_values('mtg_network'),
        'sort_by'    => fp_get_option_values('sort_by'),
        // 'mtg_cast'    => fp_get_option_values('mtg_cast'),
        // 'mtg_crew'    => fp_get_option_values('mtg_crew'),
        // 'mtg_collection'    => fp_get_option_values('mtg_collection'),
    ];

    if (!function_exists('fp_get_trending_searches_main')) {
        require_once(FP_T_ASSETS_PATH . 'php/ajax/fp_get_trending_searches_cb.php');
    }

    $ts_data = fp_get_trending_searches_main();

    // include\fp\assets\js\adv_search.js
    wp_enqueue_script('fp-search-input-js', FP_T_ASSETS_URI . 'js/adv_search.js', ['jquery'], FP_T_VERSION, true);
    $local_data = [
        'ajaxurl' => FP_T_AJAX_URL,
        'nonce'   => wp_create_nonce('fp_search_nonce'),
        'options' => $options,
    ];
    wp_localize_script('fp-search-input-js', 'fp_asData', $local_data);

    ?>

    <div class="search-results-wrapper flex flex-col justify-center items-center p-2 md:p-5">


        <?php if (is_array($ts_data) && count($ts_data) > 0) : ?>
            <div class="trending-searches-wrapper">
                <span class="trending-searches-span">Top Searches:</span>
                <?php
                $ts_data = array_slice($ts_data, 0, 100);
                // fp_log('Type of $ts_data: ' . gettype($ts_data));


                try {
                    foreach ($ts_data as $ts_item) :
                        $search_term = $ts_item->search_term ?? '';
                        $search_url = home_url('/search/' . urlencode($search_term));
                ?>
                        <a class="trending-searches-items" href="#">
                            <span class="text-sm inline-block leading-5 text-font-2">
                                <?php echo $search_term; ?>
                            </span>
                        </a>
                <?php endforeach;
                } catch (Exception $e) {
                    error_log('Trending Searches Error: ' . $e->getMessage());
                    echo '<span style="color: red;">Error displaying trending searches.</span>';
                }
                ?>
            </div>
        <?php endif; ?>




        <div class="adv-search-input-bar bg-gray-800 flex justify-center items-center w-full p-1 mx-2 mt-2">
            <i class="bi bi-search px-3"></i>
            <input id="adv-search-input" type="text" placeholder="Search" class="bg-transparent text-white px-3 py-2 outline-none focus:ring-0 w-full" value="<?php echo $search_query; ?>" autofocus>
            <div id="adv-search-btn" class="
                    w-24
                    adv-search-btn 
                    bg-gray-900 
                    text-white 
                    px-3
                    py-2 
                    font-semibold 
                    flex 
                    justify-center 
                    items-center
                    cursor-pointer
                    hover:bg-gray-400
                    hover:text-gray-900
                    transition-all
                    duration-100
                    me-1
                    rounded-lg
                    ">
                Search
            </div>

        </div>

        <div id="filterOptions" class="px-1 py-2 bg-gray-800 w-full flex-1 mt-0 rounded-b-lg">
            <?php foreach ($options as $key => $values) : ?>
                <!-- if sort_by then skip -->
                <?php if ($key === 'sort_by' || $key === 'category') continue; ?>
                <div class="custom-multiselect bg-gray-900 rounded-lg md:mx-2 w-[150px] max-w-[150px]">
                    <div class="select-box" style="color: white; padding: 0.5rem; border-radius: 0.3rem; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="font-weight: 600;" data-tagid="<?php echo esc_attr($key); ?>">
                            <?php
                            $updated_key = str_replace('mtg_', '', $key);
                            echo ucfirst($updated_key);
                            ?></span>
                        <span><i class="bi bi-caret-down-fill"></i></span>
                    </div>
                    <div class="options-list min-w-[150px] max-w-[150px] md:max-w-auto w-auto" style="
                            display: none; 
                            position: absolute; 
                            background: rgb(31, 41, 55); 
                            border-radius: 0.3rem;
                            max-height: 200px; overflow-y: auto; z-index: 10; 
                            margin-top: 0.2rem;
                            padding: 0.5rem; color: white;">
                        <?php foreach ($values as $value) : ?>
                            <label class="flex pr-2 flex-nowrap mb-2 text-nowrap whitespace-nowrap overflow-x-auto no-scrollbar">
                                <input type="checkbox" autocomplete="off" value="<?php echo esc_attr($value['value']); ?>">
                                <?php echo esc_html($value['label']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- category -->
            <div class="custom-multiselect bg-gray-900 rounded-lg md:mx-2 w-[150px] max-w-[150px]">
                <div class="select-box" style="color: white; padding: 0.5rem; border-radius: 0.3rem; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                    <span style="font-weight: 600;" data-tagid="category">Category</span>
                    <span><i class="bi bi-caret-down-fill"></i></span>
                </div>
                <div class="options-list min-w-[150px] max-w-[150px] md:max-w-auto w-auto" style="
                        display: none; 
                        position: absolute; 
                        background: rgb(31, 41, 55); 
                        border-radius: 0.3rem; width: 100%; 
                        max-height: 150px; overflow-y: auto; z-index: 10; 
                        margin-top: 0.2rem;
                        padding: 0.5rem; color: white;">
                    <?php foreach ($options['category'] as $value) : ?>
                        <label class="flex pr-2 flex-nowrap mb-2 text-nowrap whitespace-nowrap overflow-x-auto no-scrollbar">
                            <input type="checkbox" autocomplete="off" value="<?php echo esc_attr($value['value']); ?>">
                            <?php echo esc_html($value['label']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="custom-multiselect bg-gray-900 rounded-lg md:mx-2 w-[150px] max-w-[150px]">
                <div class="select-box" style="color: white; padding: 0.5rem; border-radius: 0.3rem; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                    <span style="font-weight: 600;" data-tagid="sort_by">Sort By</span>
                    <span><i class="bi bi-caret-down-fill"></i></span>
                </div>
                <div class="options-list min-w-[150px] max-w-[150px] md:max-w-auto w-auto" style="
                        display: none; 
                        position: absolute; 
                        background: rgb(31, 41, 55); 
                        border-radius: 0.3rem; 
                        max-height: 150px; overflow-y: auto; z-index: 10; 
                        margin-top: 0.2rem;
                        padding: 0.5rem; color: white;">
                    <?php foreach ($options['sort_by'] as $value) : ?>
                        <label class="flex pr-2 flex-nowrap mb-2 text-nowrap whitespace-nowrap overflow-x-auto no-scrollbar">
                            <!-- Default selected as latest -->
                            <input
                                type="radio"
                                name="sort_by"
                                autocomplete="off"
                                value="<?php echo esc_attr($value['value']); ?>"
                                <?php echo $value['value'] === 'latest' ? 'checked' : ''; ?>>
                            <?php echo esc_html($value['label']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="search-results" class="search-results">
        <div id="active-filters" class="my-3 px-3 gap-3 relative">

        </div>
        <div class="search-results-content px-5">
            <div class="search-adv-loader w-full"></div>
            <div class="search-results-list flex flex-wrap gap-3 justify-center min-h-[500px] md:min-h-[710px]">
            </div>
        </div>
    </div>
    <div class="search-result-pagination my-7">
        <div class="search-result-pagination-wrapper flex flex-wrap justify-center items-center gap-3">
        </div>
    </div>
</div>