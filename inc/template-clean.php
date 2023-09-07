<?php

/**
 * 主题性能优化，取出无用功能
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
        // Remove the 'Welcome' panel
        remove_action('welcome_panel', 'wp_welcome_panel');

        // Remove 'Site health' metabox
        // remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

        // Remove the 'At a Glance' metabox
        // remove_meta_box('dashboard_right_now', 'dashboard', 'normal');

        // Remove the 'Activity' metabox
        remove_meta_box('dashboard_activity', 'dashboard', 'normal');

        // Remove the 'WordPress News' metabox
        remove_meta_box('dashboard_primary', 'dashboard', 'side');

        // Remove the 'Quick Draft' metabox
        // remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
      }
    );
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
