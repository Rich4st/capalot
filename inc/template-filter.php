<?php

/**
 * 内页描述优化
 */
function capalot_archive_description($description)
{
    if (is_search()) {
        global $wp_query;
        $search_num  = $wp_query->found_posts;
        $description = sprintf(__('搜索到 %1$s 个与 "%2$s" 相关的结果', 'ripro'), $search_num, get_search_query());
    }
    return $description;
}
add_filter('get_the_archive_description', 'capalot_archive_description');

// 重写登录页面url
function capalot_login_url($url, $redirect)
{
    $url = home_url('/login');
    if (!empty($redirect)) {
        $url = add_query_arg('redirect_to', urlencode($redirect), $url);
    }
    return esc_url($url);
}
add_filter('login_url', 'capalot_login_url', 20, 2);

// 重写注册页面url
function capalot_register_url($url)
{
    $url = home_url('/register');
    return esc_url($url);
}
add_filter('register_url', 'capalot_register_url', 20);

// 重写忘记密码页面url
function capalot_lostpassword_url($url, $redirect)
{
    $url = home_url('/lostpwd');
    if (!empty($redirect)) {
        $url = add_query_arg('redirect_to', urlencode($redirect), $url);
    }
    return esc_url($url);
}
add_filter('lostpassword_url', 'capalot_lostpassword_url', 20, 2);
