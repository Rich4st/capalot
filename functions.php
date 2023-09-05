<?php
defined('ABSPATH') || exit;


define('_THEME_DEBUG', 0); //调试模式控制切勿开启

//调试模式显示错误日志信息
if ((defined('WP_DEBUG') && WP_DEBUG == true) || _THEME_DEBUG == true) {
    error_reporting(E_ALL);
} else {
    error_reporting(0); //关闭报错止乱码
}

function jhh_setup()
{
    add_theme_support('post-thumbnails'); // 添加缩略图功能
    add_theme_support('post-formats', array('image', 'video', 'audio')); // 添加文章格式功能

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

add_action('after_setup_theme', 'jhh_setup');
