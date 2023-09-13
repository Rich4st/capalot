<?php

/**
 * 网站静态资源加载
 */
function capalot_assets()
{

  // 移除无用
  wp_deregister_style('global-styles');
  wp_dequeue_style('global-styles');
  // wp_dequeue_style('wp-block-library');
  wp_dequeue_style('wp-block-library-theme');
  wp_dequeue_style('wc-block-style');

  remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
  remove_action('wp_footer', 'wp_enqueue_global_styles', 1);

  // jquery
  wp_deregister_script('jquery');
  wp_enqueue_script('jquery', get_template_directory_uri() . '/assets/js/jquery.min.js', array(), '3.6.0', false);

  // tailwindcss
  wp_enqueue_style('tailwind', get_template_directory_uri() . '/assets/css/tailwind.css', array(), '0.1.0');
  // app.js
  wp_enqueue_script('app', get_template_directory_uri() . '/assets/js/app.js', array(), '0.1.0', true);

  // 文章详情页参数
  $script_params = array(
    'home_url' => esc_url(home_url()),
    'ajax_url' => esc_url(admin_url('admin-ajax.php')),
    'theme_url' => esc_url(get_template_directory_uri()),
    'singular_id' => 0,
    // 'post_content_nav' => intval(_capalot('site_post_content_nav', 0)),
    'current_user_id' => get_current_user_id(),
    'ajax_nonce' => wp_create_nonce("ca_ajax"),
    'get_text' => array(

      '__copied_pwd' => '密码已复制剪贴板',
      '__copt_btn' => '复制',
      '__coppied__success' => '复制成功',
      '__commiting' => '提交中...',
      '__commit_success' => '提交成功',
      '__comment_success' => '评论成功',
      '__refresh_page' => '即将刷新页面',
      '__paying' => '支付中...',
      '__pay_success' => '支付成功',
      '__pay_error' => '支付失败',
      '__pay_cancel' => '支付已取消',
      '__delete_confirm' => '确定删除此纪录？',

    )
  );

  if (is_singular())
    $script_params['singular_id'] = get_the_ID();

  wp_localize_script('app', 'capalot', $script_params);
}
// 队列加载JS和CSS文件
add_action('wp_enqueue_scripts', 'capalot_assets');

/**
 * 后台静态资源加载
 */
function enqueue_admin_custom_assets($hook)
{

  // main.css
  wp_enqueue_style('admin-main-css', get_template_directory_uri() . '/admin/css/main.css', array(), '6.2');

  //商城管理页面加载
  if (strpos($hook, 'capalot-admin') !== false) {
    wp_enqueue_script('apexcharts', get_template_directory_uri() . '/admin/js/apexcharts.min.js', array(), '3.35.3', true);
  }
}

add_action('admin_enqueue_scripts', 'enqueue_admin_custom_assets');
