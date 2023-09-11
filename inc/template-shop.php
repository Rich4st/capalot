<?php

/**
 * 判断用户是否评论文章
 *
 * @param int $user_id 用户ID
 * @param int $post_id 文章ID
 *
 */
function is_user_commented($user_id, $post_id)
{
    $comments = get_comments(array(
        'post_id' => $post_id,
        'user_id' => $user_id,
    ));
    return count($comments) > 0;
}

// 获取网站商城会员组配置
function get_site_vip_options()
{
    $options = _capalot('site_vip_options');
    $vip_options = array();

    if (empty($options))
        return $vip_options;

    $vip_group = ['no', 'vip', 'boosvip'];

    foreach ($vip_group as $key) {
        $name = (isset($options[$key . '_name'])) ? $options[$key . '_name'] : '';
        $downnum = (isset($options[$key . '_downnum'])) ? $options[$key . '_downnum'] : 0;
        $desc = (isset($options[$key . '_desc'])) ? $options[$key . '_desc'] : '';
        $desc = empty($desc) ? [] : explode("\n", $desc);
        $vip_options[$key] = [
            'key'     => $key, //标识
            'name'    => esc_html($name), //名称
            'desc'    => $desc, //介绍
            'downnum' => absint($downnum), //下载次数
        ];
    }

    return $vip_options;
}

// 站内币名称
function get_site_coin_name()
{
    return esc_html(_capalot('site_coin_name', '金币'));
}
