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

//是否开工单
function is_site_tags_page()
{
    return !empty(_capalot('is_site_tags_page', 1));
}

//是否开工单
function is_site_link_manager_page()
{
    return !empty(_capalot('is_site_link_manager_page', 1));
}

//独立VIP介绍页面
function is_site_vip_price_page()
{
    return !empty(_capalot('is_site_vip_price_page', 1));
}

//是否开启投稿
function is_site_tougao()
{
    return !empty(_capalot('is_site_tougao', 1));
}


//是否开启签到功能
function is_site_qiandao() {
    return !empty(_capalot('is_site_qiandao', 1));
}

//今日是否已签到
function is_user_today_qiandao($user_id) {

    // 会员当前签到时间
    $qiandao_time = get_user_meta($user_id, 'cao_qiandao_time', true);

    if (empty($qiandao_time)) {
        $qiandao_time = 0;
    }

    $today_time = get_today_time_range(); //今天时间戳信息 $today_time['start'],$today_time['end']

    if ($today_time['start'] < $qiandao_time && $today_time['end'] > $qiandao_time) {
        return true;
    }
    return false;
}

//获取文章加密下载地址
function get_post_endown_url($post_id, $down_key)
{
    $nonce     = wp_create_nonce('zb_down');
    $down_str  = $post_id . '-' . $down_key . '-' . $nonce;
    return home_url('/goto?down=' . $down_str);
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
function get_user_vip_end_date($user_id)
{
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
    $downnum_used = Capalot_Download::get_user_today_download_num($user_id);
    $downnum_not  = $downnum_total - 0;
    $downnum_not = ($downnum_not >= 0) ? $downnum_not : 0;

    $data         = [
        'name'     => $vip_options[$user_type]['name'],
        'type'     => $vip_options[$user_type]['key'],
        'end_date' => get_user_vip_end_date($user_id),
        'downnums' => ['total' => $downnum_total, 0, 'used' => $downnum_used, 'not' => $downnum_not],
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
//是否开启作者佣金
function is_site_author_aff() {
    return (bool) _capalot('is_site_author_aff', true);
}
//获取网站作者佣金比例
function get_site_author_aff_rate() {
    $ratio = (float) _capalot('site_author_aff_ratio', 0);
    if ($ratio >= 1) {
        $ratio = 0;
    }
    if ($ratio <= 0) {
        $ratio = 0;
    }
    return $ratio;
}
//是否开启邀请码注册
function is_site_invitecode_register()
{
    return (bool) _capalot('is_site_invitecode_register', true);
}

//获取用户VIP类型标志
function capalot_get_user_badge($user_id = null, $tag = 'a', $class = '')
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
function get_uc_menu_link($menu_action = '')
{
    $prefix = '/user/';
    if ($menu_action == 'logout') {
        return esc_url(wp_logout_url(get_current_url()));
    }
    return esc_url(home_url($prefix . $menu_action));
}

//是否开启注册
function is_site_user_register()
{
    return (bool) _capalot('is_site_user_register', true);
}

//是否开启商城功能
function is_site_shop()
{
    return _capalot('site_shop_mode', 'all') !== 'close';
}

//是否开启登录
function is_site_user_login()
{
    return (bool) _capalot('is_site_user_login', true);
}

//是否开启网站公告
function is_site_notify()
{
    return !empty(_capalot('is_site_notify', 1));
}

//是否开工单
function is_site_tickets()
{
    return !empty(_capalot('is_site_tickets', 1));
}

//是否开启推广
function is_site_user_aff()
{
    return (bool) _capalot('is_site_aff', true);
}

/**
 * 文章是否有付费资源
 */
function post_has_pay($post_id)
{
    if (post_is_download_pay($post_id) || post_has_hide_pay($post_id) || post_has_video_pay($post_id)) {
        return true;
    }
    return false;
}

// 文章是否下载资源文章
function post_is_download_pay($post_id)
{
    $price = get_post_meta($post_id, 'capalot_price', true);
    $status = get_post_meta($post_id, 'capalot_status', true);

    if (is_numeric($price) && !empty($status)) {
        return true;
    }
    return false;
}

//文章是否有付费查看内容
function post_has_hide_pay($post_id)
{

    $price = get_post_meta($post_id, 'capalot_price', true);

    $content = get_post_field('post_content', $post_id);

    if (is_numeric($price) && has_shortcode($content, 'capalot-hide')) {
        return true;
    }
    return false;
}

//文章是否有付费播放视频内容
function post_has_video_pay($post_id)
{

    $price = get_post_meta($post_id, 'capalot_price', true);
    $status = get_post_meta($post_id, 'capalot_video', true);

    if (is_numeric($price) && !empty($status)) {
        return true;
    }
    return false;
}

//获取商城模式 [close , all , user_mod]
function get_site_shop_mod()
{
    return _capalot('site_shop_mode', 'all');
}

//站内币图标
function get_site_coin_icon()
{
    return esc_html(_capalot('site_coin_icon', 'fas fa-coins'));
}

//是否开启免登录购买功能
function is_site_not_user_pay()
{
    return get_site_shop_mod() === 'all';
}

//获取文章价格权限信息
function get_post_pay_data($post_id)
{
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
    } elseif ($vip_rate < 0) {
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
function get_post_price_data($post_id)
{
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

//站内币兑RMB汇率
function get_site_coin_rate()
{
    return absint(_capalot('site_coin_rate', '10'));
}

//币种金额换算
function site_convert_amount($amount = 0, $type = 'coin')
{
    // RMB汇率
    $coin_rate = get_site_coin_rate();
    switch ($type) {
        case 'coin':
            $amount = $amount * $coin_rate;
            break;
        case 'rmb':
            $amount = $amount / $coin_rate;
            break;
        default:
            $amount = $amount;
            break;
    }
    return (float) $amount;
}

//根据用户id获取用户购买文章实际价格
function get_user_pay_post_price($user_id, $post_id)
{
    $post_prices = get_post_price_data($post_id);
    $user_type   = get_user_vip_type($user_id);
    return $post_prices[$user_type];
}

//获取用户支付状态
function get_user_pay_post_status($user_id, $post_id)
{

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
    } elseif ($price == 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取支付方式选项
 * @param int $id 选项ID
 *
 * @return array 选项列表
 */
function capalot_get_pay_options($id = null)
{
    $config = [
        1  => ['id' => 'alipay', 'name' => '官方-支付宝', 'is' => (bool) _capalot('is_alipay')],
        2  => ['id' => 'weixinpay', 'name' => '官方-微信', 'is' => (bool) _capalot('is_weixinpay')],

        11 => ['id' => 'hupijiao_alipay', 'name' => '虎皮椒-支付宝', 'is' => (bool) _capalot('is_hupijiao_alipay')],
        12 => ['id' => 'hupijiao_weixin', 'name' => '虎皮椒-微信', 'is' => (bool) _capalot('is_hupijiao_weixin')],

        21  => ['id' => 'xunhu_alipay', 'name' => '讯虎-支付宝', 'is' => (bool) _capalot('is_xunhupay_alipay')],
        22  => ['id' => 'xunhu_weixin', 'name' => '讯虎-微信', 'is' => (bool) _capalot('is_xunhupay_weixin')],

        31  => ['id' => 'payjs_alipay', 'name' => 'PAYJS-支付宝', 'is' => (bool) _capalot('is_payjs_alipay')],
        32  => ['id' => 'payjs_weixin', 'name' => 'PAYJS-微信', 'is' => (bool) _capalot('is_payjs_weixin')],

        41  => ['id' => 'epay_alipay', 'name' => '易支付-支付宝', 'is' => (bool) _capalot('is_epay_alipay')],
        42 =>  ['id' => 'epay_weixin', 'name' => '易支付-微信', 'is' => (bool) _capalot('is_epay_weixin')],

        55  => ['id' => 'paypal', 'name' => 'PayPal', 'is' => (bool) _capalot('is_paypal')],

        66  => ['id' => 'manualpay', 'name' => '手工支付', 'is' => (bool) _capalot('is_manualpay')],
        77  => ['id' => 'site_admin_charge', 'name' => '后台充值', 'is' => false],
        88  => ['id' => 'site_cdk_pay', 'name' => '卡密支付', 'is' => false],
        99  => ['id' => 'site_coin_pay', 'name' => '余额支付', 'is' => (bool) _capalot('is_site_coin_pay')],
    ];

    $options = apply_filters('capalot_pay_options', $config);

    if ($id !== null && isset($options[$id])) {
        return $options[$id];
    }

    return $options;
}

// 获取支付方式选项模板
function capalot_get_pay_select_html($order_type = 0)
{
    $data = capalot_get_pay_options();
    $html = '<ul class="">';
    $str = array('虎皮椒-', '讯虎-', 'PayJS-', '易支付-', '码支付-', '官方-');

    // 充值订单或是未登录用户，去掉余额支付
    if ($order_type === 2 || !is_user_logged_in())
        unset($data[99]);

    // VIP订单 关闭余额支付
    if ($order_type === 3 && !empty(_capalot('is_pay_vip_allow_online', false)))
        unset($data[99]);

    foreach ($data as $key => $item) {
        if (empty($item['is'])) {
            continue;
        }

        $name = str_replace($str, '', $item['name']);
        if ($name === '微信') {
            $icon = '<i class="fa-brands fa-weixin" style="color: #00c900; width:30px;height:30px; margin-right:8px;"></i>';
        } elseif ($name === '支付宝') {
            $icon = '<i class="fa-brands fa-alipay" style="color: #00a0e6; width:30px;height:30px;margin-right:8px;"></i>';
        } else {
            $icon = '<i class="fa-solid fa-coins" style="color: #e9b116; width:30px;height:30px; margin-right:8px;"></i>';
        }

        $html .= '<li class="pay-item cursor-pointer w-[180px] mx-auto  border-2 border-[#dfdfe3] p-[0.4rem] text-[1rem] rounded-lg m-2 flex items-center justify-center hover:text-[#0cbc87] hover:border-[#0cbc87]" data-id="' . $key . '">
        ' . $icon . $name . '</li>';
    }

    $html .= '</ul>';

    return apply_filters('capalot_pay_select_html', $html);
}

//获取用户的推广链接
function get_user_aff_permalink($link_url, $user_id = null)
{
    if (empty($user_id)) {
        global $current_user;
        $user_id = $current_user->ID;
    }
    if (empty($user_id)) {
        return $link_url;
    }

    $url = esc_url_raw(
        add_query_arg(
            array(
                'aff'  => $user_id,
            ),
            $link_url
        )
    );
    return $url;
}


// 获取网站当前推荐人信息
function capalot_get_current_aff_id($user_id = 0)
{
    // TODO:
}

/**
 * 发起支付请求
 * @param array $order_data 订单数据
 */
function capalot_get_request_pay($order_data)
{
    $result = [
        'status' => 0, //状态
        'method' => 'popup', // popup|弹窗  url|跳转 jsapi|js方法
        'num' => $order_data['order_trade_no'], // 订单号
        'msg' => '支付接口未配置', //消息
    ];

    $pay_option = capalot_get_pay_options($order_data['pay_type']);

    if (
        empty($pay_option['is'])
        || ($order_data['order_type'] === 2)
        && $pay_option['id'] === 'site_coin_pay'
    ) {
        $result['msg'] = $pay_option;
        return $result;
    }

    if (
        $order_data['order_type'] === 3
        && $pay_option['id'] === 'site_coin_pay'
        && !empty(_capalot('is_pay_vip_allow_online', false))
    ) {
        $result['msg'] = '支付接口暂未开启';
        return $result;
    }

    $order_info = maybe_unserialize($order_data['order_info']);
    $order_data['ip'] = $order_info['ip'];

    $CapalotPay = new Capalot_Pay();

    switch ($pay_option['id']) {
        case 'site_coin_pay':
            // 余额支付
            $user_id = get_current_user_id();
            $coin_amount = site_convert_amount($order_data['pay_price'], 'coin');
            $user_balance = get_user_coin_balance($user_id);

            usleep(500000);

            if ($user_balance < $coin_amount) {
                $result['msg'] = '余额不足';
                return $result;
            }

            if (!change_user_coin_balance($user_id, $coin_amount, '-')) {
                $result['msg'] = '余额支付失败';
                return $result;
            }

            // 处理支付回调
            $update_order = Capalot_Shop::pay_notify_callback($order_data);

            if (!$update_order) {
                $result['msg'] = '订单状态处理异常';
                return $result;
            } else {
                return [
                    'status' => 1, //状态
                    'method' => 'reload', // popup|弹窗  url|跳转 reload|刷新 jsapi|js方法
                    'num'    => $order_data['order_trade_no'], //订单号
                    'msg'    => '支付成功'
                ];
            }

            break;
        case 'alipay':
            // TODO: 支付宝支付
            $config = _capalot('alipay');

            return [
                'foo' => $config
            ];

            break;
        default:
            break;
    }

    // TODO:设置当前订单号缓存

    return apply_filters('capalot_get_request_pay', $result, $order_data);
}


/**
 * 用户是否第三方注册未设置密码
 * @param  [type]     $user_id [description]
 * @return [type]
 */
function user_is_oauth_password($user_id)
{
    $config = array('qq', 'weixin');
    $user = get_user_by('id', $user_id);
    foreach ($config as $type) {
        $meta_key   = 'open_' . $type . '_openid';
        $p2 = get_user_meta($user_id, $meta_key, true);
        if (!empty($p2) && wp_check_password(md5($p2), $user->user_pass, $user->ID)) {
            return true;
        }
    }
}


/**
 * 获取个人中心菜单
 * @param  [type]     $menu_part [description]
 * @return [type]
 */
function get_uc_menus($menu_part = null)
{

    $default = 'profile';

    $part_tpl = array(
        'profile' => ['title' => __('基本信息', 'ripro'), 'desc' => '', 'icon' => 'far fa-user'],
        'coin'    => ['title' => __('我的余额', 'ripro'), 'desc' => '', 'icon' => get_site_coin_icon()],
        'vip'     => ['title' => __('我的会员', 'ripro'), 'desc' => '', 'icon' => 'fas fa-gem'],
        'order'   => ['title' => __('我的订单', 'ripro'), 'desc' => '', 'icon' => 'fab fa-shopify'],
        'down'    => ['title' => __('下载记录', 'ripro'), 'desc' => '', 'icon' => 'fas fa-cloud-download-alt'],
        'fav'     => ['title' => __('我的收藏', 'ripro'), 'desc' => '', 'icon' => 'fas fa-star'],
        'aff'     => ['title' => __('我的推广', 'ripro'), 'desc' => '', 'icon' => 'fas fa-hand-holding-usd'],
        'ticket'  => ['title' => __('我的工单', 'ripro'), 'desc' => '', 'icon' => 'fas fa-question-circle'],
        'tougao'  => ['title' => __('我的投稿', 'ripro'), 'desc' => '', 'icon' => 'fas fa-edit'],
        'logout'  => ['title' => __('退出登录', 'ripro'), 'desc' => '', 'icon' => 'fas fa-sign-out-alt'],
    );

    if (!is_site_shop()) {
        unset($part_tpl['coin']);
        unset($part_tpl['vip']);
        unset($part_tpl['order']);
        unset($part_tpl['down']);
        unset($part_tpl['aff']);
    }

    if (!is_site_user_aff()) {
        unset($part_tpl['aff']);
    }

    if (!is_site_tickets()) {
        unset($part_tpl['ticket']);
    }

    if (!is_site_tougao()) {
        unset($part_tpl['tougao']);
    }

    if ($menu_part !== null) {
        $menu_part = (array_key_exists($menu_part, $part_tpl)) ? $menu_part : $default;
        return $part_tpl[$menu_part];
    }

    return $part_tpl;
}
