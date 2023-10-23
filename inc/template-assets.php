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
  // custom css
  wp_enqueue_style('custom', get_template_directory_uri() . '/assets/css/custom.css', array(), '0.1.0');
  // app.js
  wp_enqueue_script('app', get_template_directory_uri() . '/assets/js/app.js', array(), '0.1.0', true);
  // sweetalert2
  wp_enqueue_script('sweetalert2', get_template_directory_uri() . '/assets/js/sweetalert2.min.js', array(), '11.0.18', true);
  // font-awesome
  wp_enqueue_script('solid', get_template_directory_uri() . '/assets/js/solid.min.js', array(), '6.4.2', true);
  wp_enqueue_script('regular', get_template_directory_uri() . '/assets/js/regular.min.js', array(), '6.4.2', true);
  wp_enqueue_script('fontawesome', get_template_directory_uri() . '/assets/js/fontawesome.min.js', array(), '6.4.2', true);
  wp_enqueue_script('brands', get_template_directory_uri() . '/assets/js/brands.min.js', array(), '6.4.2', true);
  // swiper
  wp_enqueue_style('swiper', get_template_directory_uri() . '/assets/css/swiper-bundle.min.css', array(), '10.1.0');
  wp_enqueue_script('swiper', get_template_directory_uri() . '/assets/js/swiper-bundle.min.js', array(), '10.1.0', true);
  // gsap
  wp_enqueue_script('gsap', get_template_directory_uri() . '/assets/js/gsap/gsap.min.js', array(), '3.12.1', true);
  // lazyload
  wp_enqueue_script('lazyload', get_template_directory_uri() . '/assets/js/lazyload.min.js', array(), '17.8.0', true);

  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
  // 全局参数
  $script_params = array(
    'home_url' => esc_url(home_url()),
    'ajax_url' => esc_url(admin_url('admin-ajax.php')),
    'theme_url' => esc_url(get_template_directory_uri()),
    'singular_id' => 0,
    'category_base' => capalot_get_category_base(),
    'current_user_id' => get_current_user_id(),
    'ajax_nonce' => wp_create_nonce("capalot_ajax"),
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

  //fontawesome
  wp_enqueue_style('admin-fontawesome', get_template_directory_uri() . '/admin/css/font-awesome/css/all.css', array(), '6.4.2');
  wp_enqueue_style('admin-fontawesome-shims', get_template_directory_uri() . '/admin/css/font-awesome/css/v4-shims.css', array(), '6.4.2');
  // jquery
  wp_enqueue_script('ca-admin-all', get_template_directory_uri() . '/admin/js/admin-all.js', array('jquery'));
  // app.js
  wp_enqueue_script('app', get_template_directory_uri() . '/assets/js/app.js', array(), '0.1.0', true);

  $script_params = array(
      'home_url'   => esc_url(home_url()),
      'ajax_url'   => esc_url(admin_url('admin-ajax.php')),
      'theme_url'  => esc_url(get_template_directory_uri()),
      'ajax_nonce' => wp_create_nonce("capalot_ajax"),
  );

  wp_localize_script('ca-admin-all', 'capalot', $script_params);

  //商城管理页面加载
  if (strpos($hook, 'capalot-admin') !== false) {
    wp_enqueue_script('apexcharts', get_template_directory_uri() . '/admin/js/apexcharts.min.js', array(), '3.35.3', true);
  }
}

add_action('admin_enqueue_scripts', 'enqueue_admin_custom_assets');
