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

//获取网站会员开通套餐
function get_site_vip_buy_options()
{
    $options = _capalot('site_vip_buy_options', array());
    $buy_options = array();

    if (empty($options) && !is_array($buy_options)) {
        return $buy_options;
    }

    $site_vip_options = get_site_vip_options();

    foreach ($options as $item) {

        $title = (!empty($item['title'])) ? esc_html($item['title']) : false;
        $day_num = (!empty($item['daynum'])) ? absint($item['daynum']) : false;
        $coin_price = (!empty($item['price'])) ? abs(floatval($item['price'])) : false;
        $type = $site_vip_options['vip']['key'];
        $name = $site_vip_options['vip']['name'];
        $desc = $site_vip_options['vip']['desc'];
        $downnum = $site_vip_options['vip']['downnum'];

        //永久套餐
        if ($day_num == 9999) {
            $type = $site_vip_options['boosvip']['key'];
            $name = $site_vip_options['boosvip']['name'];
            $desc = $site_vip_options['boosvip']['desc'];
            $downnum = $site_vip_options['boosvip']['downnum'];
        }

        if ($title && $day_num && $coin_price) {
            $buy_options[$day_num] = [
                'type'           => $type,
                'name'           => $name,
                'desc'           => $desc,
                'downnum'        => $downnum,
                'buy_title'      => $title,
                'day_num'        => $day_num,
                'coin_price'     => $coin_price,
            ];
        }
    }

    return $buy_options;
}

//更新用户VIP数据信息
function update_user_vip_data($user_id, $new_day = '0')
{

    $user_id = intval($user_id);
    $vip_buy_options = get_site_vip_buy_options();
    $new_day = absint($new_day);

    if (empty($new_day)) {
        $new_type = 'no';
        $new_day = 0;
    } else {
        $new_type = $vip_buy_options[$new_day]['type'];
        $new_day =  $vip_buy_options[$new_day]['day_num'];
    }


    // 获取用户当前VIP信息
    $current_vip_data = get_user_vip_data($user_id);
    $current_vip_type = $current_vip_data['type']; //当前类型
    $current_end_date = $current_vip_data['end_date']; //

    //降级vip
    if ($current_end_date == '9999-09-09' && $new_type = 'vip') {
        $current_end_date = wp_date('Y-m-d');
    }

    if ($current_vip_type == 'no') {
        $current_end_date = wp_date('Y-m-d');
    }

    //计算时差秒数
    $diff_seconds     = time() - current_time('timestamp');
    $current_end_time = strtotime($current_end_date) + $diff_seconds;
    $new_end_time     = $current_end_time + ($new_day * 24 * 60 * 60);
    $new_vip_type     = $new_type;

    if ($new_type == 'boosvip') {
        $new_vip_type = 'vip';
        $new_end_date = "9999-09-09"; //永久
    } elseif ($new_type == 'no') {
        $new_vip_type = 'no';
        $new_end_date = wp_date('Y-m-d');
    } else {
        $new_vip_type = 'vip';
        $new_end_date = wp_date('Y-m-d', $new_end_time); //新到期时间
    }

    // 更新数据
    $update_type = update_user_meta($user_id, 'capalot_user_type', $new_vip_type);
    $update_endtime = update_user_meta($user_id, 'capalot_vip_end_time', $new_end_date);

    $status = ($update_type || $update_endtime) ? true : false;
    return $status;
}

//获取当前用户VIP类型
function get_user_vip_type($user_id)
{
    $vip_options = get_site_vip_options();
    $user_type   = get_user_meta($user_id, 'capalot_user_type', true);
    if (empty($user_type) || !isset($vip_options[$user_type])) {
        return $vip_options['no']['key'];
    }
    $current_date = wp_date('Y-m-d');
    $vip_end_date = get_user_vip_end_date($user_id);
    $end_time     = strtotime($vip_end_date);
    $current_time = strtotime($current_date);

    if (!$end_time) {
        $end_time = $current_time;
    }

    if ($user_type === 'vip' && $vip_end_date === '9999-09-09') {
        return $vip_options['boosvip']['key'];
    }

    if ($user_type === 'vip' && $end_time > $current_time) {
        return $vip_options['vip']['key'];
    }

    return $vip_options['no']['key'];
}

//获取用户到期时间
function get_user_vip_end_date($user_id) {
    $vip_options  = get_site_vip_options();
    $user_type = get_user_meta($user_id, 'capalot_user_type', true);
    $current_date = wp_date('Y-m-d');
    if (empty($user_type) || !isset($vip_options[$user_type])) {
        return $current_date;
    }
    $vip_end_date = get_user_meta($user_id, 'capalot_vip_end_time', true);
    if (strtotime($vip_end_date)) {
        return $vip_end_date;
    }
    return $current_date;
}


//获取用户VIP数据
function get_user_vip_data($user_id)
{
    $vip_options = get_site_vip_options();
    $user_type   = get_user_vip_type($user_id);
    //今日可下载次数
    $downnum_total = $vip_options[$user_type]['downnum'];
    //TODO:今日已下载次数
    // $downnum_used = ZB_Down::get_user_today_down_num($user_id);
    $downnum_not  = $downnum_total - 0;
    $downnum_not = ($downnum_not >= 0) ? $downnum_not : 0;

    $data         = [
        'name'     => $vip_options[$user_type]['name'],
        'type'     => $vip_options[$user_type]['key'],
        'end_date' => get_user_vip_end_date($user_id),
        'downnums' => ['total' => $downnum_total, 0, 'not' => $downnum_not],
    ];
    return $data;
}

//获取用户余额
function get_user_coin_balance($user_id)
{
    $current_balance = get_user_meta($user_id, 'capalot_balance', true);

    // 如果当前余额未设置，默认为0
    if (empty($current_balance)) {
        $current_balance = 0;
    }

    return abs($current_balance);
}

//更新用户余额 balance [+ 充值 - 消费扣减]
function change_user_coin_balance($user_id, $amount, $change_type)
{
    // 检查变更类型是否有效
    if (!in_array($change_type, array('+', '-'))) {
        return false;
    }
    $amount = abs($amount);
    // 获取当前余额和余额变更记录
    $current_balance = get_user_coin_balance($user_id);
    $balance_before = $current_balance;

    $balance_log = get_user_meta($user_id, 'balance_log', true);

    if (empty($balance_log) || !is_array($balance_log)) {
        $balance_log = array();
    }
    // 根据变更类型更新余额和余额变更记录
    if ($change_type == '+') {
        $current_balance += $amount;
        $event = '+';
    } else {
        if ($current_balance < $amount) {
            // 余额不足
            return false;
        }

        $current_balance -= $amount;
        $event = '-';
    }
    $new_log_item = array(
        'date' => wp_date('Y-m-d H:i:s'),
        'event' => $event,
        'amount' => $amount,
        'balance_before' => $balance_before,
        'balance_after' => $current_balance
    );

    array_unshift($balance_log, $new_log_item);
    // 更新用户余额和余额变更记录
    update_user_meta($user_id, 'capalot_balance', $current_balance);
    update_user_meta($user_id, 'balance_log', $balance_log);
    return true;
}

//是否开启评论
function is_site_comments()
{
    return !empty(_capalot('is_site_comments', 1));
}

//获取用户VIP类型标志
function zb_get_user_badge($user_id = null, $tag = 'a', $class = '')
{
    //颜色配置
    $colors = [
        'no'        => 'secondary',
        'vip'     => 'success',
        'boosvip' => 'warning',
    ];
    $data  = get_user_vip_data($user_id);


    $color = $colors[$data['type']];
    $name  = $data['name'];
    $link  = get_uc_menu_link('vip');
    // 构建HTML代码

    if ($data['type'] != 'no') {
        $badge_title   = $data['end_date'] . '到期';
    } else {
        $badge_title   = '';
    }

    $badge_class   = "badge bg-$color text-$color bg-opacity-10 $class";
    $badge_content = "<i class=\"far fa-gem me-1\"></i>$name";
    if ($tag == 'a') {
        return "<$tag title=\"$badge_title\" class=\"$badge_class\" href=\"$link\">$badge_content</$tag>";
    } else {
        return "<$tag title=\"$badge_title\" class=\"$badge_class\">$badge_content</$tag>";
    }
}

// 菜单获取
function get_uc_menu_link($menu_action = '') {
    $prefix = '/user/';
    if ($menu_action == 'logout') {
        return esc_url(wp_logout_url(get_current_url()));
    }
    return esc_url(home_url($prefix . $menu_action));
}

//是否开启商城功能
function is_site_shop() {
    return _capalot('site_shop_mode', 'all') !== 'close';
}

//获取文章价格权限信息
function get_post_pay_data($post_id) {
    $price = get_post_meta($post_id, 'capalot_price', true);
    $vip_rate = get_post_meta($post_id, 'capalot_vip_rate', true);
    $boosvip_free = get_post_meta($post_id, 'capalot_is_boosvip', true);
    $disable_no_buy = get_post_meta($post_id, 'capalot_close_novip_pay', true);
    $sales_count = get_post_meta($post_id, 'capalot_sold_quantity', true);

    if (!is_numeric($price)) {
        $price = 0;
    }

    if (!is_numeric($vip_rate)) {
        $vip_rate = 1;
    }elseif ($vip_rate < 0) {
        $vip_rate = 0;
    } elseif ($vip_rate > 1) {
        $vip_rate = 1;
    } else {
        $vip_rate = floor($vip_rate * 100) / 100;
    }

    $data = [
        'coin_price' => abs(floatval($price)),
        'vip_rate' => $vip_rate,
        'boosvip_free' => empty($boosvip_free) ? 0 : 1,
        'disable_no_buy' => empty($disable_no_buy) ? 0 : 1,
        'sales_count' => absint($sales_count),
    ];
    return $data;
}

//获取文章价格信息 单位：站内币 0免费 false不可购买
function get_post_price_data($post_id) {
    $data = get_post_pay_data($post_id);

    $coin_price = floatval($data['coin_price']);

    $prices = [
        'default' => $coin_price, //原价
        'no' => $coin_price,
        'vip' => $coin_price,
        'boosvip' => $coin_price,
    ];

    if ($data['disable_no_buy']) {
        $prices['no'] = false;
    }

    if (isset($data['vip_rate'])) {
        $prices['vip'] = $coin_price * $data['vip_rate'];
        $prices['boosvip'] = $coin_price * $data['vip_rate'];
    }

    if ($data['boosvip_free']) {
        $prices['boosvip'] = 0;
    }

    return (array)$prices;
}

//根据用户id获取用户购买文章实际价格
function get_user_pay_post_price($user_id, $post_id) {
    $post_prices = get_post_price_data($post_id);
    $user_type   = get_user_vip_type($user_id);
    return $post_prices[$user_type];
}

//获取用户支付状态
function get_user_pay_post_status($user_id, $post_id) {

    $cache_key = 'pay_post_status_' . $user_id . '_' . $post_id;
    // wp_cache_set($cache_key, 0);
    $pay_status = wp_cache_get($cache_key);

    if (false === $pay_status) {
        //查询订单状态
        $pay_status = Capalot_Shop::get_pay_post_status($user_id, $post_id);
        wp_cache_set($cache_key, $pay_status);
    }

    if ($pay_status == true) {
        return 1;
    }

    //判断价格权限方式
    $price = get_user_pay_post_price($user_id, $post_id);
    if ($price === false) {
        return false;
    }elseif ($price == 0) {
        return true;
    }else{
        return false;
    }

}
