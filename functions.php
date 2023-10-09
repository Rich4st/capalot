<?php
defined('ABSPATH') || exit;

/**
 * 主题初始化
 */
function capalot_setup()
{
    if(is_admin() && !function_exists('domain_check')) {
        exit;
    }

    add_theme_support('post-thumbnails'); // 添加缩略图功能

    register_nav_menus(
        array(
            'main-menu' => '全站顶部菜单',
        )
    );

    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // 开启友情链接功能
    add_filter('pre_option_link_manager_enabled', '__return_true');

    // 小工具选择性刷新
    add_theme_support('customize-selective-refresh-widgets');

    // 删除render_block 过滤器
    remove_filter('render_block', 'wp_render_duotone_support');
    remove_filter('render_block', 'wp_restore_group_inner_container');
    remove_filter('render_block', 'wp_render_layout_support_flag');


    // 第一次启用主题时，创建数据库表
    if (get_option('theme_setup') != 'done') {
        require get_template_directory() . '/inc/setup-db.php';

        $setup_db = new SetupDB();
        $setup_db->create_db();

        // 创建成功后，将主题设置为已启用
        update_option('theme_setup', 'done');

        // 重写固定链接规则
        flush_rewrite_rules(false);
    }
}

add_action('after_setup_theme', 'capalot_setup');

/**
 * 注册菜单
 */
function capalot_widget_init()
{

    register_sidebar(array(
        'name'          => '首页模块',
        'id'            => 'home-module',
        'description'   => '首页模块主内容区域',
        'before_widget' => '<div id="%1$s" class="home-widget %2$s">',
        'after_widget'  => '</div>',
    ));

    register_sidebar(array(
        'name'          => '文章侧边栏',
        'id'            => 'single-sidebar',
        'description'   => '文章模块侧边栏区域',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
    ));
}

add_action('widgets_init', 'capalot_widget_init');

require_once get_template_directory() . '/inc/core/capalot.php';

// 加载CSF框架配置
require_once get_template_directory() . '/inc/template-csf.php';

// 加载静态资源
require_once get_template_directory() . '/inc/template-assets.php';

// 主题基本优化
require_once get_template_directory() . '/inc/template-clean.php';

// 主题后台设置
require_once get_template_directory() . '/inc/template-admin.php';

// 商城公共方法
require_once get_template_directory() . '/inc/template-shop.php';

// 主题功能标签
require_once get_template_directory() . '/inc/template-tags.php';

// 主题功能标签
require_once get_template_directory() . '/inc/template-mail.php';

// //伪静态路由
require_once get_template_directory() . '/inc/template-rewrite.php';

// ajax 请求
require_once get_template_directory() . '/inc/template-ajax.php';

// filters
require_once get_template_directory() . '/inc/template-filter.php';

require_once get_template_directory() . '/inc/template-walker.php';

// seo设置
require_once get_template_directory() . '/inc/template-seo.php';

// domain-check
require_once get_template_directory() . '/domain-check.php';

