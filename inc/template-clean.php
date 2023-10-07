<?php

/**
 * 主题性能优化，去除无用功能
 */

class capalot_clean
{

  /**
   * wordpress filter AND action load
   * @Author Dadong2g
   * @date   2022-01-21
   */
  public function __construct()
  {
    // 移除WP后台底部版本信息
    add_filter('admin_footer_text', '__return_empty_string');
    add_filter('update_footer', '__return_empty_string', 11);

    add_action(
      'wp_dashboard_setup',
      function () {
        remove_action('welcome_panel', 'wp_welcome_panel');

        // remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

        // remove_meta_box('dashboard_right_now', 'dashboard', 'normal');

        remove_meta_box('dashboard_activity', 'dashboard', 'normal');

        remove_meta_box('dashboard_primary', 'dashboard', 'side');

        // remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
      }
    );


    // 关闭古腾堡编辑器
    if (!_capalot('gutenberg_edit')) {
      add_filter('use_block_editor_for_post', '__return_false');
      remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');
    }

    // 禁用古腾堡小工具
    if (!_capalot('gutenberg_widgets', false)) {
      // Disables the block editor from managing widgets in the Gutenberg plugin.
      add_filter('gutenberg_use_widgets_block_editor', '__return_false');
      // Disables the block editor from managing widgets.
      add_filter('use_widgets_block_editor', '__return_false');
    }

    // 自带顶部导航栏
    if (_capalot('show_admin_bar', true)) {
      add_filter('show_admin_bar', '__return_false');
    }
  }

  public function admin_init()
  {

    //删除仪表盘
    global $pagenow; // Get current page
    $redirect = get_admin_url(null, 'edit.php'); // Where to redirect

    if ($pagenow == 'index.php') {
      wp_redirect($redirect, 301);
      exit;
    }
  }
}

new capalot_clean();
