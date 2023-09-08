<?php

/**
 * 网站静态资源加载
 */
function enqueue_custom_assets()
{

  // 移除无用
  wp_deregister_style('global-styles');
  wp_dequeue_style('global-styles');
  // wp_dequeue_style('wp-block-library');
  wp_dequeue_style('wp-block-library-theme');
  wp_dequeue_style('wc-block-style');

  remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
  remove_action('wp_footer', 'wp_enqueue_global_styles', 1);


  // tailwindcss
  wp_enqueue_style('tailwind', get_template_directory_uri() . '/assets/css/tailwind.css', array(), '0.1.0');
}

// 队列加载JS和CSS文件
add_action('wp_enqueue_scripts', 'enqueue_custom_assets');

/**
 * 后台静态资源加载
 */
function enqueue_admin_custom_assets($hook)
{
  //商城管理页面加载
  if (strpos($hook, 'capalot-admin') !== false) {
    wp_enqueue_script('apexcharts', get_template_directory_uri() . '/admin/js/apexcharts.min.js', array(), '3.35.3', true);
  }
}

add_action('admin_enqueue_scripts', 'enqueue_admin_custom_assets');