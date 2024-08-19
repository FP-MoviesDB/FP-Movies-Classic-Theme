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

function fp_theme_options_menu()
{
    add_theme_page(
        'Theme Options',           // Page title
        'Theme Options',           // Menu title
        'manage_options',          // Capability required to access the page
        'fp-theme-options',        // Menu slug
        'fp_theme_options_page'    // Function to display the page content
    );
}


function fp_enqueue_theme_options_page($hook_suffix)
{
    if ($hook_suffix === 'appearance_page_fp-theme-options') {
        wp_enqueue_style('fp-t_tw-style', FP_T_ASSETS_URI . 'css/global.css', array(), FP_T_VERSION, 'all');
        wp_enqueue_style('fp-t_tw-style_2', FP_T_ASSETS_URI . 'css/theme_option_admin.css', array(), FP_T_VERSION, 'all');
        wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', array(), '1.11.3', 'all');

        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');


        wp_enqueue_script('fp-t_options_scripts', FP_T_ASSETS_URI . 'js/fp_theme_option.js', array('jquery'), FP_T_VERSION, true);

        wp_localize_script('fp-t_options_scripts', 'fp_Data', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'save_nonce' => wp_create_nonce('fp_save_theme_settings'),
            'tax_nonce' => wp_create_nonce('fp_get_taxonomy_list'),
        ]);
        wp_enqueue_media();
    }
}

function fp_theme_options_page()
{
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Get current settings
    $current_settings = get_option('fp_theme_all_settings');

    // Assign default values if the settings are empty
    $default_settings = [
        'basic_settings' => [
            'logo' => 'https://dummyimage.com/200x60/000/fff',
            'favicon' => 'https://dummyimage.com/50x50/000/fff',
            'max_width' => '1400',
            'show_social' => 0,
            'social_list' => [],
            'footer_text' => '<p class="text-center">&copy; 2024 | All rights reserved.</p>',
        ],
        'customize_settings' => [
            'homepage_layout' => [],
            'single_layout' => [],
        ],
        'other_settings' => [],
    ];

    $current_settings = wp_parse_args($current_settings, $default_settings);
    $basic_settings = $current_settings['basic_settings'];
    $homepage_layout = $current_settings['customize_settings']['homepage_layout'];
    $single_layout = $current_settings['customize_settings']['single_layout'];

    $show_social = ($basic_settings['show_social'] === 'true' || $basic_settings['show_social'] === true);

    $decoded_footer_text = base64_decode($basic_settings['footer_text']) ?? '';

    fp_log("FOOTER TEXT: " . $decoded_footer_text);

    // fp_log("Basic Settings: " . json_encode($basic_settings));
    fp_log("Homepage Layout: " . json_encode($homepage_layout));
    // fp_log("Single Layout: " . json_encode($single_layout));

    $shortcodes = [
        ['shortcode' => 'fp-universal-view', 'title' => 'Universal TextArea'],
        ['shortcode' => 'fp-post-player', 'title' => 'Player'],
        ['shortcode' => 'fp-post-title', 'title' => 'Title'],
        ['shortcode' => 'fp-imdb-box-view', 'title' => 'IMDB BOX'],
        ['shortcode' => 'fp-synopsis-view', 'title' => 'StoryLine'],
        ['shortcode' => 'fp-post-info', 'title' => 'PostInfo'],
        ['shortcode' => 'fp-screenshot-view', 'title' => 'Screenshots'],
        ['shortcode' => 'fp-post-links', 'title' => 'Download Links'],
    ];


?>
    <div class="wrapper flex flex-col bg-gray-900 text-white min-h-screen">
        <h1 class="p-2 text-2xl text-center font-semibold py-3"><?php echo esc_html(get_admin_page_title()); ?></h1>

        <div class="self-center flex justify-center items-center py-3">
            <div id="save-theme-settings" class="px-4 py-2 bg-blue-700 hover:bg-blue-900 text-center cursor-pointer font-semibold">Save Changes</div>
        </div>

        <div class="content-wrapper flex flex-col md:flex-row justify-start items-start bg-gray-900 flex-1">

            <div id="fp_menu_items" class="flex flex-row flex-wrap md:flex-col gap-3 text-gray-400 text-base h-full justify-center items-center md:justify-start md:items-start pt-5 md:pt-0">

                <div id="select-basic" class="p-3 md:min-w-[150px] md:max-w-[150px] hover:cursor-pointer active">
                    <span>Basic</span>
                </div>
                <div id="select-customize" class="p-3 md:min-w-[150px] md:max-w-[150px] hover:cursor-pointer ">
                    <span>Customize</span>
                </div>
                <div id="select-other" class="p-3 md:min-w-[150px] md:max-w-[150px] hover:cursor-pointer ">
                    <span>Other</span>
                </div>
            </div>

            <div class="flex-1 bg-gray-800 w-full h-full text-white p-3">

                <!-- 
                // ┌───────────────────┐
                // │ Basic Settings  │
                // └───────────────────┘
                -->
                <div id="fp-basic-settings" class="flex flex-col">
                    <h2 class="text-center text-xl py-5 md:py-3">Basic Settings</h2>

                    <!-- Site LOGO -->
                    <div id="header-logo" class="flex flex-col md:flex-row p-2 gap-3">
                        <div class="key-themeOptions">Header Logo</div>
                        <div class="value flex flex-col gap-3 bg-gray-700 p-3 flex-1">
                            <div class="relative max-w-40 max-h-20 flex justify-start items-center overflow-hidden">
                                <img id="fp-logo-p" class="max-h-20 aspect-auto" src="<?php echo esc_url($basic_settings['logo']); ?>" title="site-logo" loading="lazy" />
                                <i id="remove-logo" class="bi bi-x-circle-fill absolute top-0 right-1 rounded text-base font-semibold text-red-800 hover:text-red-500 cursor-pointer"></i>
                            </div>
                            <div class="flex gap-2">
                                <input id="logo-url-relative-path" class="min-w-0 flex-1 bg-gray-800" type="text" value="<?php echo esc_attr($basic_settings['logo']); ?>" disabled readonly />
                                <div id="logo-uploader" class="w-20 px-3 py-2 text-center font-semibold rounded bg-gray-900 hover:cursor-pointer">Upload</div>
                            </div>
                        </div>
                    </div>

                    <!-- Site Favicon -->
                    <div id="site-favicon" class="flex flex-col md:flex-row p-2 gap-3">
                        <div class="key-themeOptions">Site Favicon</div>
                        <div class="value flex flex-col gap-3 bg-gray-700 p-3 flex-1">
                            <div class="relative w-20 max-w-40 max-h-20 flex justify-start items-center overflow-hidden">
                                <img id="fp-fav-p" class="max-h-20 aspect-square" src="<?php echo esc_url($basic_settings['favicon']); ?>" title="site-favicon" loading="lazy" />
                                <i id="remove-favicon" class="bi bi-x-circle-fill absolute top-0 right-1 rounded text-base font-semibold text-red-800 hover:text-red-500 cursor-pointer"></i>
                            </div>
                            <div class="flex gap-2">
                                <input id="favicon-url-relative-path" class="bg-gray-800 min-w-0 flex-1" type="text" value="<?php echo esc_attr($basic_settings['favicon']); ?>" disabled readonly />
                                <div id="favicon-uploader" class="w-20 px-3 py-2 text-center font-semibold rounded bg-gray-900 hover:cursor-pointer">Upload</div>
                            </div>
                        </div>
                    </div>

                    <!-- Site Max Width -->
                    <div id="site-max-width" class="flex flex-col md:flex-row p-2 gap-3">
                        <div class="key-themeOptions">Site Max Width</div>
                        <div class="value flex flex-col gap-3 bg-gray-700 p-3 flex-1">
                            <p id="max-width-text" class="text-center text-base font-semibold"><?php echo esc_html($basic_settings['max_width']); ?>px</p>
                            <input id="max-width" class="min-w-0 flex-1" type="range" min="1200" max="2000" step="10" value="<?php echo esc_attr($basic_settings['max_width']); ?>" />
                        </div>
                    </div>

                    <!-- Show Social -->
                    <div id="show-social-wrapper" class="flex flex-col md:flex-row p-2 gap-3">
                        <div class="key-themeOptions">Show Social</div>
                        <div class="value flex justify-start items-center gap-3 bg-gray-700 p-3 flex-1">
                            <input id="show-social" type="checkbox" <?php checked($show_social, true); ?> />
                            <p class="text-base font-semibold">Show Social</p>
                        </div>
                    </div>


                    <div id="show-social-base-wrapper" class="hidden flex-col md:flex-row p-2 gap-3">
                        <div class="key-themeOptions">Social Base Icon</div>
                        <div class="value flex flex-col justify-center items-start gap-3 bg-gray-700 p-3 flex-1">
                            <input
                                id="social-text-icon"
                                class="block bg-gray-800 text-gray-800 min-w-0 py-1 w-full"
                                type="text"
                                value="<?php echo esc_attr($basic_settings['social_base_icon'] ?? 'bi bi-emoji-sunglasses-fill'); ?>"
                                style="padding: 0.7rem 0.5rem;"
                                placeholder="bi bi-emoji-sunglasses-fill" />
                        </div>
                    </div>

                    <!-- Social List -->
                    <div id="social-list" class=" hidden flex-col md:flex-row p-3 gap-2">
                        <div class="key-themeOptions">Social List</div>
                        <div class="value flex justify-start items-center gap-3 bg-gray-700 p-1 flex-1">
                            <div class="flex-1">
                                <?php if (!empty($basic_settings['social_list'])) : ?>
                                    <div id="single-social-container-wrapper" class="flex flex-wrap gap-3 bg-gray-700 px-2 py-1">
                                        <?php foreach ($basic_settings['social_list'] as $social) : ?>
                                            <div class="single-social-item flex flex-col justify-between items-center bg-gray-900 p-2 gap-4 flex-1">
                                                <div class="single-social-container relative flex flex-col gap-3 justify-center items-start p-2 self-start">
                                                    <div class="flex flex-col md:flex-row gap-2">
                                                        <label for="social-icon" class="min-w-[120px] max-w-[120px] overflow-hidden">Bootstrap Icon
                                                            <a href="https://icons.getbootstrap.com/" target="_blank" class="text-base mx-2" style="color: #01fef3;">?</a>
                                                        </label>
                                                        <input class="social-icon min-w-0" type="text" value="<?php echo esc_attr($social['icon']); ?>" placeholder="bi bi-facebook" />
                                                    </div>
                                                    <div class="flex flex-col md:flex-row gap-2">
                                                        <label for="social-title" class="min-w-[120px] max-w-[120px] overflow-hidden">Title</label>
                                                        <input class="social-title min-w-0" type="text" value="<?php echo esc_attr($social['title']); ?>" placeholder="Facebook" />
                                                    </div>

                                                    <div class="flex flex-col md:flex-row gap-2 md:justify-between">
                                                        <label for="social-color" class="min-w-[120px] max-w-[120px] overflow-hidden">Color</label>
                                                        <input class="social-color min-w-0" type="text" value="<?php echo esc_attr($social['color'] ?? '#ffffff'); ?>" />
                                                    </div>

                                                    <div class="flex flex-col md:flex-row gap-2">
                                                        <label for="social-link" class="min-w-[120px] max-w-[120px] overflow-hidden">Link</label>
                                                        <input class="social-link min-w-0" type="text" value="<?php echo esc_attr($social['link']); ?>" placeholder="https://facebook.com" />
                                                    </div>
                                                </div>
                                                <div class="remove-social cursor-pointer flex justify-center items-center gap-2 bg-red-700 px-3 py-2 rounded">
                                                    <i class="bi bi-x-circle-fill rounded text-xs text-gray-200 hover:text-gray-400 cursor-pointer"></i>
                                                    <span class="text-sm text-gray-400 font-semibold">Remove</span>
                                                </div>
                                            </div>

                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <!-- ADD NEW DIV -->
                                <div id="add-new-social-wrapper" class="flex justify-center items-center">
                                    <div id="add-new-social" class="min-w-30 max-w-30 px-3 py-2 text-center font-semibold rounded bg-gray-900 hover:cursor-pointer">
                                        Add More
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Text -->
                    <div id="footer-text-wrapper" class="flex flex-col md:flex-row p-2 gap-3">
                        <div class="key-themeOptions">Footer Text</div>
                        <div class="value flex flex-col gap-3 bg-gray-700 p-3 flex-1">
                            <textarea id="footer-text" class="min-w-0 flex-1 p-2 text-gray-900" rows="5" placeholder="&copy; 2024 | All rights reserved."><?php echo htmlspecialchars($decoded_footer_text, ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <p class="text-sm text-gray-400">You can use HTML tags</p>
                        </div>
                    </div>

                </div>


                <!-- 
                // ┌────────────────────────┐
                // │ Customize Settings  │
                // └────────────────────────┘
                -->
                <div id="fp-customize-settings" class="hidden flex-col">
                    <!-- <h2 class="text-center text-xl py-5 md:py-3">Customize</h2> -->

                    <!-- Builder for HomePage -->
                    <div id="homepage-builder" class="builder-container p-4 mb-4 bg-gray-900">
                        <h3 class="text-lg font-semibold mb-3">HomePage Builder</h3>

                        <!-- Options Form -->
                        <div id="homepage-options" class="flex flex-col gap-3">
                            <div class="flex flex-row gap-3">
                                <label for="hp-type" class="min-w-[150px]">Type</label>
                                <select id="hp-type" class="flex-1">
                                    <option value="featured">Featured</option>
                                    <option value="meta">Meta</option>
                                    <option value="taxonomy">Taxonomy</option>
                                </select>
                            </div>

                            <!-- Taxonomy Section -->
                            <div id="hp-taxonomy-section" class="flex-row gap-3 hidden">
                                <label for="hp-taxonomy" class="min-w-[150px]">Taxonomy</label>
                                <select id="hp-taxonomy" class="flex-1">
                                    <option value="mtg_network">Network</option>
                                    <option value="mtg_quality">Quality</option>
                                    <option value="mtg_resolution">Resolution</option>
                                    <option value="mtg_genre">Genre</option>
                                    <option value="mtg_year">Year</option>
                                </select>
                            </div>

                            <div class="flex flex-row gap-3">
                                <label for="hp-content-type" class="min-w-[150px]">Content Type</label>
                                <select id="hp-content-type" class="flex-1">
                                    <option value="movie">Movie</option>
                                    <option value="series">Series</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>

                            <div id="h-heading-view" class="flex-row gap-3 hidden">
                                <label for="hp-heading" class="min-w-[150px]">Heading</label>
                                <input id="hp-heading" type="text" class="flex-1 p-2 bg-gray-700 text-white min-w-0" />
                            </div>

                            <div class="flex flex-row gap-3">
                                <label for="hp-image-size" class="min-w-[150px]">Image Size</label>
                                <select id="hp-image-size" class="flex-1">
                                    <option value="small">Small</option>
                                    <option value="medium">Medium</option>
                                    <option value="large">Large</option>
                                    <option value="original">Original</option>
                                </select>
                            </div>

                            <div id="h-show-ratings" class="flex-row gap-3 hidden">
                                <label for="hp-show-ratings" class="min-w-[150px]">Show Ratings</label>
                                <input id="hp-show-ratings" type="checkbox" />
                            </div>

                            <div id="h-show-quality" class="flex-row gap-3 hidden">
                                <label for="hp-show-quality" class="min-w-[150px]">Show Quality</label>
                                <input id="hp-show-quality" type="checkbox" />
                            </div>

                            <div class="flex flex-row gap-3">
                                <label for="hp-limit" class="min-w-[150px]">Limit</label>
                                <input id="hp-limit" type="number" class="flex-1 p-2 bg-gray-700 text-white min-w-0" />
                            </div>

                            <div class="flex flex-row gap-3">
                                <label for="hp-title-bg-effect" class="min-w-[150px]">Title Background Effect</label>
                                <select id="hp-title-bg-effect" class="flex-1">
                                    <option value="normal">Normal</option>
                                    <option value="gradient">Gradient</option>
                                </select>
                            </div>

                            <div class="flex flex-row gap-3">
                                <label for="hp-image-source" class="min-w-[150px]">Image Source</label>
                                <select id="hp-image-source" class="flex-1">
                                    <option value="local">Local</option>
                                    <option value="tmdb">TMDB [Saves Bandwidth]</option>
                                </select>
                            </div>


                        </div>

                        <!-- Add Button -->
                        <button id="add-homepage-item" class="w-full mt-3 px-4 py-2 bg-blue-600 text-white">ADD</button>

                        <!-- Sortable List -->
                        <div class=" mt-4 bg-gray-700 p-4 rounded">
                            <!-- <h3 class="text-lg font-semibold mb-3">Item Sequence: </h3> -->
                            <ul id="homepage-items" class="sortable-list">
                                <!-- Dynamic items will be appended here -->
                                <?php foreach ($homepage_layout as $item) : ?>
                                    <div class="sortable-item bg-gray-800 p-3 mb-2 rounded">
                                        <i class="bi bi-arrows-move"></i>
                                        <li>
                                            <div class="i_type"><strong>Type:</strong> <?php echo esc_html($item['type']); ?></div>
                                            <div class="i_content_type"><strong>Content Type:</strong> <?php echo esc_html($item['content_type']); ?></div>
                                            <div class="i_limit"><strong>Limit:</strong> <?php echo esc_html($item['limit']); ?></div>
                                            <div class="i_title_background"><strong>Title Background Effect:</strong> <?php echo esc_html($item['title_background']); ?></div>
                                            <div class="i_image_size"><strong>Image Size:</strong> <?php echo esc_html($item['image_size']); ?></div>
                                            <!-- <div class="i_image_source"><strong>Image Source:</strong> ${item.image_source}</div> -->
                                            <div class="i_image_source"><strong>Image Source:</strong> <?php echo esc_html($item['image_source']); ?></div>
                                            <?php if ($item['type'] === "taxonomy" || $item['type'] === "meta") : ?>
                                                <div class="i_heading"><strong>Heading:</strong> <?php echo esc_html($item['heading']); ?></div>
                                                <div class="i_show_ratings"><strong>Show Ratings:</strong> <?php echo esc_html($item['show_ratings'] === "true" ? "Yes" : "No"); ?></div>
                                                <div class="i_show_quality"><strong>Show Quality:</strong> <?php echo esc_html($item['show_quality'] === "true" ? "Yes" : "No"); ?></div>
                                            <?php endif; ?>

                                            <?php if ($item['type'] === "taxonomy") : ?>
                                                <div class="i_taxonomy"><strong>Taxonomy:</strong> <?php echo esc_html($item['taxonomy']); ?></div>
                                            <?php endif; ?>
                                        </li>
                                        <button class="remove-item remove-btn-base">Remove</button>
                                    </div>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div id="single-builder" class="builder-container p-4 mb-4 bg-gray-900">
                        <h3 class="text-lg font-semibold mb-3">SinglePage Builder</h3>

                        <!-- Options Form -->
                        <div id="singlepage-options" class="flex flex-col gap-3">
                            <div class="flex flex-row gap-3">
                                <label for="sp-shortcode" class="min-w-[150px] max-w-[150px]">Select Shortcode</label>
                                <select id="sp-shortcode" class="flex-1">
                                    <?php foreach ($shortcodes as $shortcode) : ?>
                                        <option value="<?php echo esc_attr($shortcode['shortcode']); ?>">
                                            <?php echo esc_html($shortcode['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div id="universal-content-type-wrapper" class="hidden flex-row gap-3">
                                <label for="universal-content-type" class="min-w-[150px]">Content Type</label>
                                <select id="universal-content-type" class="flex-1">
                                    <option value="text">Text</option>
                                    <option value="html">HTML</option>
                                </select>
                            </div>

                            <div id="universal-view-textarea" class="flex-col gap-3 hidden">
                                <textarea id="universal-content" rows="5" class="p-2 bg-gray-700 text-white" placeholder="Explore {title}, a {genre} film released in {release_year}. This {quality} quality film features {audio} audio and {c_subs} subtitles. Currently, it is the latest in the series from {latest_year}. Stream on {network} or explore more about this {post_type} {separator} | FP Movies Theme."></textarea>
                                <p class="text-xs text-gray-500 px-1 font-semibold">
                                    If using HTML/Scripts tags, make sure to select ContentType to HTML, also theme abbreviation are supported. Abbreviation supported list:
                                </p>
                                <div class="flex flex-wrap justify-start items-start gap-3 px-2">
                                    <span>{title}</span>
                                    <span>{t_title}</span>
                                    <span>{genre}</span>
                                    <span>{release_year}</span>
                                    <span>{latest_year}</span>
                                    <span>{quality}</span>
                                    <span>{audio}</span>
                                    <span>{c_audio}</span>
                                    <span>{c_subs}</span>
                                    <span>{p_type}</span>
                                    <span>{network}</span>
                                    <span>{separator}</span>
                                    <span>{post_type}</span>
                                </div>
                            </div>

                            <!-- Add Button -->
                            <button id="add-singlepage-item" class="mt-3 px-4 py-2 bg-blue-600 text-white">ADD</button>
                        </div>


                        <!-- Sortable List -->
                        <ul id="singlepage-items" class="sortable-list mt-4 bg-gray-700 p-4 rounded">
                            <?php foreach ($single_layout as $item) : ?>
                                <div class="sortable-item bg-gray-800 rounded flex justify-start items-center px-3 py-2 mb-3">
                                    <i class="bi bi-arrows-move"></i>
                                    <li id="single-shortcode-list" class="flex-1 flex gap-5">
                                        <div class="i_shortcode"><span class="inline-block min-w-24 me-1 font-semibold">Shortcode: </span><?php echo esc_html($item['shortcode']); ?></div>
                                        <?php if ($item['shortcode'] === 'fp-universal-view') : ?>
                                            <div class="i_content_type"><span class="inline-block min-w-24 me-1 font-semibold">Type: </span><?php echo $item['content_type']; ?></div>
                                            <div class="i_content"><span class="inline-block min-w-24 me-1 font-semibold">Content: </span>
                                                <?php
                                                $decoded_content = base64_decode($item['content']);
                                                echo htmlspecialchars($decoded_content, ENT_QUOTES, 'UTF-8');
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                    <button class="remove-item remove-btn-base">Remove</button>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    </div>




                </div>



                <!-- 
                // ┌───────────────────┐
                // │ Other Settings  │
                // └───────────────────┘
                -->
                <div id="fp-other-settings" class="hidden flex-col">
                    <h2 class="text-center text-xl py-5 md:py-3">Other</h2>
                </div>
            </div>

        <?php
    }
