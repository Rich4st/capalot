<?php

defined('ABSPATH') || exit;
###########################################

/**
 * 用户中心 UC 页面入口模板
 */


if (!is_user_logged_in()) {
    wp_safe_redirect(home_url('/login'));
    exit;
}

get_header();

$menus = get_uc_menus();

$page_action = (array_key_exists(get_query_var('uc-page-action'), $menus)) ? get_query_var('uc-page-action') : 'profile';

$bg_image = get_template_directory_uri() . '/assets/img/bg.png';

$container = _capalot('site_container_width', '1400')
?>

<section class="dark:bg-dark  py-2">
    <div class="lg:my-6 my-2  mx-auto flex dark:text-gray-400 text-[#4d4d4d] " style="max-width: <?php
                                                                if ($container === '') {
                                                                    echo '1280';
                                                                } else {
                                                                    echo $container;
                                                                }
                                                                ?>px;">

        <div class="lg:block hidden h-full w-72 text-center mr-2 " data-sticky>
            <?php get_template_part('/template-parts/user/part/menu'); ?>
        </div>

        <div class="flex-1 " data-sticky-content>
            <?php get_template_part('/template-parts/user/part/' . $page_action); ?>
        </div>
    </div>
</section>
<?php

get_footer();
