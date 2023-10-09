<?php

// 检查当前域名是否匹配
function domain_check()
{

  // 要允许的域名
  $allowed_domain = [
    '127.0.0.23'
  ];

  // 获取当前访问的域名
  $current_domain = $_SERVER['HTTP_HOST'];

  global $pagenow;

  if (is_admin() && $pagenow != 'themes.php' && !in_array($current_domain, $allowed_domain)) {
    // 不匹配则跳转到error
    wp_redirect(home_url('/error'));
    exit;
  }
}

add_action('init', 'domain_check');
