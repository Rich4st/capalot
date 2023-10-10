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
function is_site_qiandao()
{
    return !empty(_capalot('is_site_qiandao', 1));
}

//今日是否已签到
function is_user_today_qiandao($user_id)
{

    // 会员当前签到时间
    $qiandao_time = get_user_meta($user_id, 'capalot_qiandao_time', true);

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
    $nonce     = wp_create_nonce('capalot_down');
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
    $vip_options = get_site_vip_options();
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
    // 今日已下载次数
    $downnum_used = Capalot_Download::get_user_today_download_num($user_id);
    $downnum_not  = $downnum_total - $downnum_used;
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
function is_site_author_aff()
{
    return (bool) _capalot('is_site_author_aff', true);
}
//获取网站作者佣金比例
function get_site_author_aff_rate()
{
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
    if (_capalot('site_coin_icon', 'fas fa-coins') === '')
        return esc_html("fa-solid fa-coins");
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

// 获取支付弹窗内容
function capalot_get_pay_body_html($id, $price, $qrimg)
{
    //分组
    $alipay_group    = [1, 11, 21, 31, 41];
    $weixinpay_group = [2, 12, 22, 32, 42];

    if (in_array($id, $alipay_group)) {
        # alipay
        $icon_url = get_template_directory_uri() . '/assets/img/alipay.png';
        $title    = sprintf(__('支付宝扫码支付 %s 元', 'ripro'), $price);
    } elseif (in_array($id, $weixinpay_group)) {
        # weixinpay
        $icon_url = get_template_directory_uri() . '/assets/img/weixinpay.png';
        $title    = sprintf(__('微信扫码支付 %s 元', 'ripro'), $price);
    } else {
        $icon_url = '';
        $title    = sprintf(__('扫码支付 %s 元', 'ripro'), $price);
    }

    $desc = __('支付后请等待 5 秒左右，切勿关闭扫码窗口', 'ripro');
    $html = sprintf('<div class="pay-body-html"><img class="pay-icon" src="%s"><div class="title">%s</div><div class="qrcode"><img src="%s"></div><div class="desc">%s</div></div>', $icon_url, $title, $qrimg, $desc);
    return apply_filters('ri_pay_body_html', $html);
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
                $uc_vip_info = get_user_vip_data($order_data['user_id']);

                if ($uc_vip_info['type'] != 'boosvip') {
                    $update = update_user_vip_data($order_data['user_id'], $order_data['order_price']);
                }

                return [
                    'status' => 1, //状态
                    'method' => 'reload', // popup|弹窗  url|跳转 reload|刷新 jsapi|js方法
                    'num'    => $order_data['order_trade_no'], //订单号
                    'msg'    => '支付成功',
                ];
            }

            break;
        case 'alipay':
            // TODO: 支付宝支付
            $config = _capalot('alipay');
            $api_type = (isset($config['api_type'])) ? $config['api_type'] : '';

            if ($api_type == 'web') {
                $result['method'] = 'url';
                $pay_url          = wp_is_mobile() && !empty($config['is_mobile'])
                    ? $CapalotPay->alipay_app_wap_pay($order_data)
                    : $CapalotPay->alipay_app_web_pay($order_data);
            } elseif ($api_type == 'qr') {
                $pay_url = $CapalotPay->alipay_app_qr_pay($order_data);
                $pay_url = capalot_get_pay_body_html($order_data['pay_type'], $order_data['pay_price'], get_qrcode_url($pay_url));
            }

            break;
        default:
            break;
    }

    // 设置当前订单号缓存
    if (!empty($pay_url)) {
        Capalot_Cookie::set('current_order_num', $order_data['order_trade_no'], 300);
        $result['status'] = 1;
        $result['msg']    = $pay_url;
    }

    return apply_filters('capalot_get_request_pay', $result, $order_data);
}

/**
 * 支付成功后处理订单
 *
 * 处理订单业务逻辑 1 => 'Post',2 => 'VIP',3 => 'Other'
 */
function capalot_pay_success_callback($order)
{
    if (empty($order) || empty($order->pay_status))
        return false;

    $order_info = maybe_unserialize($order->order_info);

    if ($order->order_type == 1) {
        // TODO: 文章订单
    } elseif ($order->order_type == 2) {
        // TODO: 充值订单
    } elseif ($order->order_type == 3 && isset($order_info['vip_type'])) {
        $uc_vip_info = get_user_vip_data($order->user_id);

        if ($uc_vip_info['type'] != 'boosvip') {

            //更新用户会员状态
            $update = update_user_vip_data($order->user_id, $order_info['vip_day']);
        }

        $site_vip_options = get_site_vip_options();
    }
}
add_action('site_pay_order_success', 'capalot_pay_success_callback', 10, 1);

/**
 * 获取第三方登录地址
 * @param  string     $method   [description]
 * @param  boolean    $callback [description]
 * @return [type]
 */
function get_oauth_permalink($method = 'qq', $callback = false)
{
    if (!in_array($method, array('qq', 'weixin'))) {
        $method = 'qq';
    }
    $callback = (!empty($callback)) ? '/callback' : '';
    return esc_url(home_url('/oauth/' . $method . $callback));
}
/**
 * 第三方登录回调事件处理
 * @param  [type]     $snsInfo [description]
 * @return [type]
 */

function capalot_oauth_callback_event($data)
{
    global $wpdb;

    $current_uid = get_current_user_id(); //当前用户
    // 查询meta关联的用户
    $meta_key   = 'open_' . $data['method'] . '_openid';
    $search_uid = $wpdb->get_var(
        $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key=%s AND meta_value=%s", $meta_key, $data['openid'])
    );

    // 如果当前用户已登录，而$search_user存在，即该开放平台账号连接被其他用户占用了，不能再重复绑定了
    if (!empty($current_uid) && !empty($search_uid) && $current_uid != $search_uid) {
        capalot_wp_die(
            __('绑定失败', 'ripro'),
            __('当前用户之前已有其他账号绑定，请先登录其他账户解绑，或者激活已经使用该方式登录的账号！', 'ripro')
        );
    }

    if (!empty($search_uid) && empty($current_uid)) {
        // 该开放平台账号已连接过WP系统，再次使用它直接登录
        $user = get_user_by('id', $search_uid);
        if ($user) {
            capalot_updete_user_oauth_info($user->ID, $data);

            wp_set_current_user($user->ID, $user->user_login);
            wp_set_auth_cookie($user->ID, true);
            do_action('wp_login', $user->user_login, $user);
            wp_safe_redirect(get_uc_menu_link());
            exit;
        }
    } elseif (!empty($current_uid) && empty($search_uid)) {
        //当前已登录了本地账号, 直接绑定该账号
        capalot_updete_user_oauth_info($current_uid, $data);
        wp_safe_redirect(get_uc_menu_link());
        exit;
    } elseif (empty($search_uid) && empty($current_uid)) {
        //新用户注册
        $new_user_data = array(
            'user_login'   => $data['method'] . mt_rand(1000, 9999) . mt_rand(1000, 9999),
            'user_email'   => "",
            'display_name' => $data['name'],
            'nickname'     => $data['name'],
            'user_pass'    => md5($data['openid']),
            'role'         => get_option('default_role'),
        );

        $new_user = wp_insert_user($new_user_data);

        if (is_wp_error($new_user)) {
            capalot_wp_die(__('新用户注册失败', 'ripro'), $new_user->get_error_message());
        } else {
            //登陆当前用户
            $user = get_user_by('id', $new_user);
            if ($user) {
                capalot_updete_user_oauth_info($user->ID, $data);
                update_user_meta($user->ID, 'user_avatar_type', $data['method']); //更新默认头像

                wp_set_current_user($user->ID, $user->user_login);
                wp_set_auth_cookie($user->ID, true);
                do_action('wp_login', $user->user_login, $user);
                wp_safe_redirect(get_uc_menu_link());
                exit;
            }
        }
    }
}

// 更新用户oauth信息
function capalot_updete_user_oauth_info($user_id, $data)
{
    $meta = 'open_' . $data['method'];
    unset($data['method']);
    foreach ($data as $key => $value) {
        if (!empty($value)) {
            $meta_key = $meta . '_' . $key;
            update_user_meta($user_id, $meta_key, $value);
        }
    }
    return true;
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
