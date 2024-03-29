<?php

// 自定义顶部css
function custom_head_css()
{
    $css = '';

    //背景颜色配置
    $container_width = (int) _capalot('site_container_width', '');

    if ($container_width > 0) {
        $css .= "@media (min-width: 1200px){ .container-xl, .container-lg, .container-md, .container-sm, .container { max-width: {$container_width}px; } }";
    }

    //背景颜色配置
    $body_background = _capalot('site_background', array());
    $__css           = '';
    if (is_array($body_background) || is_object($body_background)) {
        foreach ($body_background as $property => $value) {
            if (!empty($value)) {
                if (is_array($value)) {
                    $url = isset($value['url']) && !empty($value['url']) ? $value['url'] : null;
                    if ($url !== null) {
                        $__css .= "$property: url('$url');";
                    }
                } else {
                    $__css .= "$property: $value;";
                }
            }
        }
    }

    if (!empty($__css)) {
        $css .= "body{{$__css}}\n";
    }

    //顶部菜单配置
    $header_color = _capalot('site_header_color', array());
    $__css        = '';

    if (!empty($header_color['bg-color'])) {
        $__css .= ".site-header{background-color:{$header_color['bg-color']};}\n";
        $__css .= ".navbar .nav-list .sub-menu:before{border-bottom-color:{$header_color['sub-bg-color']};}\n";
    }
    if (!empty($header_color['sub-bg-color'])) {
        $__css .= ".navbar .nav-list .sub-menu{background-color:{$header_color['sub-bg-color']};}\n";
    }
    if (!empty($header_color['color'])) {
        $__css .= ".site-header,.navbar .nav-list a,.navbar .actions .action-btn{color:{$header_color['color']};}\n";
    }
    if (!empty($header_color['hover-color'])) {
        $__css .= ".navbar .nav-list a:hover,.navbar .nav-list > .menu-item.current-menu-item > a {color:{$header_color['hover-color']};}\n";
    }

    if (!empty($__css)) {
        $css .= "$__css";
    }

    //自定义CSS
    $custom_web_css = _capalot('site_web_css');
    if ($custom_web_css) {
        $css .= $custom_web_css;
    }
    //打包输出
    if (!empty($css)) {
        echo "<style type=\"text/css\">\n" . $css . "\n</style>";
    }
}
add_action('wp_head', 'custom_head_css');

// 为body添加自定义样式
function custom_body_classes($classes)
{

    $classes[] = 'dark:bg-dark';

    if (get_query_var('uc-page')) {
        $classes[] = 'uc-page';
    }

    if (get_query_var('pay-vip-page')) {
        $classes[] = 'vip-prices-page';
    }

    //顶部透明菜单
    $is_home_header_transparent = (bool) _capalot('is_site_home_header_transparent', false);
    if (is_home() && $is_home_header_transparent) {
        $classes[] = 'header-transparent';
    }

    return $classes;
}
add_filter('body_class', 'custom_body_classes');

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

// 在后台更新菜单时删除缓存
function capalot_update_menu_callback()
{
    delete_transient('main-menu-cache');
}
add_action('wp_update_nav_menu', 'capalot_update_menu_callback', 10);


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
function _get_avatar_url($url, $id_or_email, $args)
{
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

// 自定义筛选字段过滤器
function capalot_archive_pre_posts_filter($query)
{

    //后台不过滤
    if (is_admin()) {
        return $query;
    }

    //不是主查询排除过滤器
    if (!$query->is_main_query()) {
        return $query;
    }

    //搜索仅限搜索文章类型
    if (is_search()) {
        $query->set('post_type', 'post');
    }

    //高级筛选过滤器
    if (is_archive()) {

        //价格筛选free
        $price      = sanitize_text_field(get_response_param('price', null, 'get'));
        $price_meta = ['free', 'vip_only', 'vip_free', 'vip_rate', 'boosvip_free'];
        if (in_array($price, $price_meta)) {

            $meta_query       = [];
            $price_key        = 'capalot_price';
            $vip_rate_key     = 'capalot_vip_rate';
            $boosvip_free_key = 'capalot_is_boosvip';
            $vip_only_key     = 'capalot_close_novip_pay';

            switch ($price) {
                case 'free':
                    $meta_query[] = ['key' => $price_key, 'compare' => '=', 'value' => '0'];
                    $meta_query[] = ['key' => $vip_only_key, 'compare' => '!=', 'value' => '1'];
                    break;
                case 'vip_only':
                    $meta_query[] = ['key' => $vip_only_key, 'compare' => '=', 'value' => '1'];
                    break;
                case 'vip_free':
                    $meta_query[] = ['key' => $price_key, 'compare' => '>', 'value' => '0'];
                    $meta_query[] = ['key' => $vip_rate_key, 'compare' => '=', 'value' => '0'];
                    break;
                case 'vip_rate':
                    $meta_query[] = [
                        'relation' => 'AND',
                        ['key' => $price_key, 'compare' => '>', 'value' => '0'],
                        ['key' => $vip_rate_key, 'compare' => '>', 'value' => '0'],
                        ['key' => $vip_rate_key, 'compare' => '<', 'value' => '1'],
                    ];
                    break;
                case 'boosvip_free':
                    $meta_query[] = [
                        'relation'            => 'OR',
                        'boosvip_free_clause' => ['key' => $boosvip_free_key, 'compare' => '=', 'value' => '1'],
                        'vip_rate_clause'     => ['key' => $vip_rate_key, 'compare' => '=', 'value' => '0'],
                    ];
                    break;
            }

            $query->set('meta_query', $meta_query);
        }

        // 排序：
        $orderby     = sanitize_text_field(get_response_param('orderby', null, 'get'));
        $orders_meta = ['views', 'likes', 'follow_num'];
        if (in_array($orderby, $orders_meta)) {
            $query->set('meta_key', $orderby);
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC');
        }
    }

    return $query;
}
add_filter('pre_get_posts', 'capalot_archive_pre_posts_filter', 99);

// 搜素功能过滤器
function site_search_by_title_only($search, $wp_query)
{
    global $wpdb;

    if (empty($search) || empty(_capalot('is_site_pro_search_title', false))) {
        return $search;
    }

    $q      = $wp_query->query_vars;
    $n      = !empty($q['exact']) ? '' : '%';
    $search = $searchand = '';
    foreach ((array) $q['search_terms'] as $term) {
        $term = esc_sql($wpdb->esc_like($term));
        $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
        $searchand = ' AND ';
    }
    if (!empty($search)) {
        $search = " AND ({$search}) ";
        if (!is_user_logged_in()) {
            $search .= " AND ($wpdb->posts.post_password = '') ";
        }
    }
    return $search;
}
add_filter('posts_search', 'site_search_by_title_only', 99, 2);

// 广告代码
function capalot_ads_filter($slug)
{

    if (defined('DOING_AJAX') && DOING_AJAX) {
        return false;
    }

    $position   = (strpos($slug, 'bottum') !== false) ? ' bottum' : ' top';
    $is_ads     = _capalot($slug);
    $ads_pc     = _capalot($slug . '_pc');
    $ads_mobile = _capalot($slug . '_mobile');

    $html = '';
    if (wp_is_mobile() && $is_ads && !empty($ads_mobile)) {
        $html = '<div class="site-addswarp mobile' . $position . '">';
        $html .= $ads_mobile;
        $html .= '</div>';
    } else if ($is_ads && isset($ads_pc)) {
        $html = '<div class="site-addswarp pc' . $position . '">';
        $html .= $ads_pc;
        $html .= '</div>';
    }
    echo $html;
}
add_action('capalot_ads', 'capalot_ads_filter', 10, 1);
