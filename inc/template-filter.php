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

//替换默认头像
function _get_avatar_url($url, $id_or_email, $args) {
    $user_id = 0;
    if (is_numeric($id_or_email)) {
        $user_id = absint($id_or_email);
    } elseif (is_string($id_or_email) && is_email($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        if (isset($user->ID) && $user->ID) {
            $user_id = $user->ID;
        }
    } elseif ($id_or_email instanceof WP_User) {
        $user_id = $id_or_email->ID;
    } elseif ($id_or_email instanceof WP_Post) {
        $user_id = $id_or_email->post_author;
    } elseif ($id_or_email instanceof WP_Comment) {
        $user_id = $id_or_email->user_id;
        if (!$user_id) {
            $user = get_user_by('email', $id_or_email->comment_author_email);
            if (isset($user->ID) && $user->ID) {
                $user_id = $user->ID;
            }

        }
    }

    $avatar_url = get_default_avatar_src(); //默认头像

    $avatar_type = get_user_meta($user_id, 'user_avatar_type', true); // null | custom | qq | weixin | weibo |

    if ($avatar_type == 'custom' || $avatar_type == 'gravatar') {

        $custom_avatar = get_user_meta($user_id, 'user_custom_avatar', true);
        if (!empty($custom_avatar)) {

            if (strpos($custom_avatar, '/') === 0) {
                // 相对路径，添加网站目录前缀
                //兼容老款相对地址
                $uploads = wp_upload_dir();
                if (file_exists(WP_CONTENT_DIR . '/uploads' . $custom_avatar)) {
                    $custom_avatar = WP_CONTENT_URL . '/uploads' . $custom_avatar;
                }
            } else {
                // 绝对路径，直接输出
                $avatar_url = $custom_avatar;
            }

            $avatar_url = set_url_scheme($custom_avatar); //头像存在
        }

    } elseif (in_array($avatar_type, ['qq', 'weixin', 'weibo'])) {
        $avatar_url = set_url_scheme(get_user_meta($user_id, 'open_' . $avatar_type . '_avatar', true)); //开发平台
    }

    return preg_replace('/^(http|https):/i', '', $avatar_url);

}
add_filter('get_avatar_url', '_get_avatar_url', 10, 3);