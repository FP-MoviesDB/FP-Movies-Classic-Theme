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

$b_settings = get_option('fp_theme_all_settings')['basic_settings'] ?? [];

$t_logo = isset($b_settings['logo']) ? $b_settings['logo'] : 'https://dummyimage.com/200x60/000/fff';
$t_favicon = isset($b_settings['favicon']) ? $b_settings['favicon'] : 'https://dummyimage.com/32x32/000/fff';
$t_max_width = ($b_settings['max_width'] ?? '1200') . 'px';
$t_show_social = isset($b_settings['show_social']) ? $b_settings['show_social'] : 'false';
$t_social_base_icon = isset($b_settings['social_base_icon']) ? $b_settings['social_base_icon'] : 'bi bi-emoji-sunglasses-fill';
$t_social_items = [];
if ($t_show_social) $t_social_items = $b_settings['social_list'] ?? [];

if (empty($t_logo)) $t_logo = 'https://dummyimage.com/200x60/000/fff';
if (empty($t_favicon)) $t_favicon = 'https://dummyimage.com/32x32/000/fff';
if (empty($t_max_width)) $t_max_width = '1200px';
if (empty($t_show_social)) $t_show_social = 'false';
if (empty($t_social_base_icon)) $t_social_base_icon = 'bi bi-emoji-sunglasses-fill';


?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="mobile-web-app-capable" content="yes">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <link rel="preconnect" href="https://www.google-analytics.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="icon" href="<?php echo $t_favicon; ?>" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo $t_favicon; ?>">

    <?php wp_head(); ?>
</head>

<body <?php body_class(''); ?> style="max-width: <?php echo $t_max_width; ?>; margin: 0 auto;">
    <div id="fp-main-container" class="text-white relative">
        <?php wp_body_open(); ?>
        <header id="fp-site-header" class="">
            <div class="hbox bg-gray-800">
                <div class="flex justify-between items-center p-1 min-h-[70px] max-h-[70px]">
                    <div id="mobile-primary-icon" class="flex justify-center items-center md:hidden text-white">
                        <i id="mobile-menu-toggle" class="bi bi-list px-3 hover:text-gray-400 cursor-pointer text-2xl"></i>
                    </div>

                    <div id="menu-secondary-main" class="hidden md:inline-flex justify-center items-center text-white relative">
                        <i id="secondary-menu-toggle" class="bi bi-list px-3 hover:text-gray-400 cursor-pointer text-4xl font-semibold"></i>
                        <div id="menu-secondary-content" class="hidden">
                            <?php wp_nav_menu(array(
                                'theme_location' => 'lg-secondary-menu',
                                'menu_class' => 'header-menu-2',
                                'menu_id' => 'header-menu-2',
                                'fallback_cb' => false,
                            ));
                            ?>
                            <?php if ($t_show_social === 'true') : ?>
                                <?php if (!empty($t_social_items)) : ?>
                                    <div class="social-icons flex justify-center items-center gap-5 mt-2 py-2">
                                        <?php foreach ($t_social_items as $social) : ?>
                                            <a href="<?php echo esc_url($social['link']); ?>" target="_blank" rel="noopener noreferrer">
                                                <i class="<?php echo esc_attr($social['icon']); ?> text-3xl" style="background-color: <?php echo esc_attr($social['color']); ?>"></i>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="logo min-w-[180px] p-2 max-h-[60px] aspect-video overflow-clip">
                        <a href="<?php echo esc_url(home_url()); ?>">
                            <img src="<?php echo $t_logo; ?>" width="150" height="auto" alt="Logo" class="fp-site-logo" />
                        </a>
                    </div>

                    <div class="head-main-nav hidden md:flex flex-1 min-h-[70px] max-w-[80%]">
                        <?php wp_nav_menu(array(
                            'theme_location' => 'header-menu',
                            'menu_class' => 'header-menu',
                            'menu_id' => 'header-menu',
                            'fallback_cb' => false,
                            'depth' => 2

                        ));
                        ?>
                        <div id="pc-search-wrapper" class="rounded-lg p-1 flex justify-center items-center bg-gray-700 text-white relative" tabindex="0">
                            <input type="text" placeholder="Search" class="transition-all duration-300 bg-transparent px-3 py-2 outline-none focus:ring-0 min-w-0 w-32" />
                            <div class="flex justify-center items-center gap-2">
                                <!-- search page -->
                                <a href="<?php echo esc_url(home_url('/search')); ?>">
                                    <i id="header-filter-icon" class="hidden bi bi-filter hover:text-gray-400 cursor-pointer text-lg"></i>
                                </a>

                                <div id="search-s-icon" class="">
                                    <img src=<?php echo FP_T_URI . '/include/fp/assets/img/icon-s.png'; ?> alt="S Icon" class="w-5 h-5 text-white opacity-70" />
                                </div>

                                <i id="search-pc-btn" class="bi bi-search hover:text-gray-400 cursor-pointer text-lg pe-2"></i>
                            </div>
                            <!-- Search Result absolute under search -->
                            <div id="pc-search-result" class="rounded-lg hidden absolute top-[50px] left-0 w-full bg-gray-800 text-white z-10 min-w-full min-h-22">
                                <div class="flex justify-start items-start p-2 flex-col">
                                    <div id="results-head" class="hidden min-w-full border-b-[1px] border-white first-letter:font-semibold">Results: </div>
                                    <div id="p-results" class="results flex-1 w-full">
                                        <p style="text-align: center;"><i class="bi bi-info-circle"></i> Enter at least 2 characters.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center items-center md:hidden text-white overflow-hidden">
                        <i class="bi bi-search px-3 hover:text-gray-400 cursor-pointer text-lg" id="search-icon"></i>
                    </div>
                </div>
            </div>
            <div id="menu-mobile-primary-content" class="hidden md:hidden">
                <div class="max-h-[450px] overflow-y-auto">

                    <?php wp_nav_menu(array(
                        'theme_location' => 'mobile-primary-menu',
                        'menu_class' => 'header-menu-2',
                        'menu_id' => 'header-menu-2',
                        'fallback_cb' => false,
                        'depth' => 2
                    ));
                    ?>
                </div>

                <?php if ($t_show_social === 'true') : ?>
                    <?php if (!empty($t_social_items)) : ?>
                        <div class="social-icons flex justify-center items-center gap-5 mt-2 py-2">
                            <?php foreach ($t_social_items as $social) : ?>
                                <a href="<?php echo esc_url($social['link']); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="<?php echo esc_attr($social['icon']); ?> text-3xl" style="background-color: <?php echo esc_attr($social['color']); ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
            <div class="bg-opacity-90 hidden md:hidden p-1 justify-center items-center flex-col" id="mobile-search-container">
                <div class="bg-gray-700 flex justify-center items-center h-full z-10 border-[1px] border-gray-900 p-1 m-2">
                    <input type="text" placeholder="Search" class="bg-transparent text-white px-3 py-2 outline-none focus:ring-0" autofocus>
                    <a href="<?php echo esc_url(home_url('/search')); ?>">
                        <i id="header-filter-icon" class="bi bi-filter hover:text-gray-400 cursor-pointer text-2xl"></i>
                    </a>
                    <i class="bi bi-search px-3 hover:text-gray-400 cursor-pointer text-lg"></i>
                </div>
                <div id="m-search-result" class="w-full max-w-[95%] rounded-lg md:hidden left-0 bg-gray-800 text-white z-10 min-h-22 flex justify-center items-center flex-col">
                    <div class="flex justify-start items-start p-2 flex-col w-[95%]">
                        <div id="results-head" class="hidden min-w-full border-b-[1px] border-white first-letter:font-semibold">Results: </div>
                        <div id="m-results" class="results flex-1 w-full">
                            <p style="text-align: center;"><i class="bi bi-info-circle"></i> Enter at least 2 characters.</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <?php if (!empty($t_social_items)) : ?>
            <div class="fixed bottom-2 right-2 z-50">
                <div class="share_toggle_button">
                    <ul>
                        <li class="bg-gray-900">
                            <a class="share_btn">
                                <span class="text">JOIN</span>
                                <span class="icon"><i class="<?php echo esc_attr($t_social_base_icon); ?>"></i></span>
                            </a>
                        </li>
                        <?php foreach ($t_social_items as $social) : ?>
                            <li class="socialShareItem_bottom bg-gray-800">
                                <a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($social['link']); ?>" class="sm">
                                    <span class="icon" style="background-color: <?php echo esc_attr($social['color']); ?>">
                                        <i class="<?php echo esc_attr($social['icon']); ?>"></i>
                                    </span>
                                    <span class="sStext">
                                        <?php echo esc_html($social['title']); ?>
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>