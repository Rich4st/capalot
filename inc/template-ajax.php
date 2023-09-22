<?php

/**
 * 主题AJAX接口
 * 地址：domain/wp-admin/admin-ajax.php
 * 参数：action 接口
 * 参数：nonce 安全验证参数 使用 wp_create_nonce("zb_ajax") 方法生成
 * $this->add_action('test_api'); //全部用户可用
 * $this->add_action('test_api',0); //未登录用户可用
 * $this->add_action('test_api',1); //登录用户可用
 * trim(sanitize_text_field($data))
 */

new Capalot_Ajax();

class Capalot_Ajax
{

  // 请求前缀
  private $__ajax_prefix = 'wp_ajax_capalot_';
  // 未登录用户请求前缀
  private $__ajax_nopriv_prefix = 'wp_ajax_nopriv_capalot_';

  public function __construct()
  {
    $this->init();
  }

  /**
   * 添加接口
   * @param string $hook_name 接口名称
   * @param int $type -1:全部用户可用 0:未登录用户可用 1:登录用户可用
   */
  private function add_action($hook_name, $type = null)
  {
    if ($type === null || $type === 1)
      add_action($this->__ajax_prefix . $hook_name, [$this, $hook_name]);

    if ($type === null || $type === 0)
      add_action($this->__ajax_nopriv_prefix . $hook_name, [$this, $hook_name]);
  }

  /**
   * 初始化所有接口请求
   */
  private function init()
  {
    $this->add_action('get_pay_select_html'); //获取支付方式
    $this->add_action('get_pay_action'); //下单
    $this->add_action('user_login', 0); //登录
  }

  /**
   * 接口安全验证
   */
  private function valid_nonce_ajax()
  {
    if (!check_ajax_referer('capalot_ajax', 'nonce', false)) {
      wp_send_json([
        'status' => 0,
        'msg' => '非法请求',
      ]);
    }
  }

  // 用户登录
  public function user_login()
  {

    $this->valid_nonce_ajax(); #安全验证

    if (!is_site_user_login()) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => '本站未开启登录功能',
      ));
    }

    $user_name     = sanitize_user(get_response_param('user_name'), true);
    $user_password = wp_unslash(get_response_param('user_password'));
    $captcha_code  = wp_unslash(trim(get_response_param('captcha_code')));
    $remember      = (empty(get_response_param('remember'))) ? false : true;

    if (!$user_name || !$user_password) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => '请输入账号或密码',
      ));
    }

    if (is_site_img_captcha() && !is_img_captcha(strtolower($captcha_code))) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => '验证码错误，请刷新验证码',
      ));
    }

    $UserData = [
      'user_login'    => $user_name,
      'user_password' => $user_password,
      'remember'      => $remember,
    ];

    $UserLogin = wp_signon($UserData, false);
    if (is_wp_error($UserLogin)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => '用户名或密码不正确',
      ));
    }

    if (!empty(get_user_meta($UserLogin->ID, 'cao_banned', true))) {
      wp_logout();
      wp_send_json(array(
        'status' => 0,
        'msg'    => sprintf('此账号已被封禁（ %s ）', get_user_meta($UserLogin->ID, 'cao_banned_reason', true)),
      ));
    }

    wp_set_current_user($UserLogin->ID, $UserLogin->user_login);
    wp_set_auth_cookie($UserLogin->ID, true);

    wp_send_json(array(
      'status'   => 1,
      'msg'      => __('登录成功', 'ripro'),
      'back_url' => get_uc_menu_link(),
    ));
  }

  // 获取支付方式HTML
  public function get_pay_select_html()
  {
    $this->valid_nonce_ajax();

    $user_id = get_current_user_id();
    $post_id = absint(get_response_param('post_id'));
    $order_type = absint(get_response_param('order_type', 0));
    $body = capalot_get_pay_select_html($order_type);

    if (!is_site_shop())
      wp_send_json([
        'status' => 0,
        'msg' => '商城功能未开启',
      ]);

    if (
      in_array($order_type, array(2, 3)) && empty($user_id)
      || (empty($user_id) && !is_site_not_user_pay())
    )
      wp_send_json([
        'status' => 0,
        'msg' => '请登录后购买',
      ]);

    $post_price = get_user_pay_post_price($user_id, $post_id);

    if ($post_price === false)
      wp_send_json([
        'status' => 0,
        'msg' => '暂无购买权限',
      ]);

    wp_send_json([
      'status' => 1,
      'msg' => '获取成功',
      'data' => $body
    ]);
  }

  // 购买支付接口
  public function get_pay_action()
  {
    $this->valid_nonce_ajax();

    $user_id = get_current_user_id();
    $post_id = absint(get_response_param('post_id'));
    $order_type = absint(get_response_param('order_type', 0));
    $order_info_key = trim(get_response_param('order_info'));
    $pay_type_id = absint(get_response_param('pay_type_id', 0));

    if (!is_site_shop())
      wp_send_json([
        'status' => 0,
        'msg' => '商城功能未开启',
      ]);

    if (
      in_array($order_type, array(2, 3)) && empty($user_id)
      || (empty($user_id) && !is_site_not_user_pay())
    )
      wp_send_json([
        'status' => 0,
        'msg' => '请登录后购买',
      ]);

    if (!in_array($order_type, array(1, 2, 3, 4)))
      wp_send_json([
        'status' => 0,
        'msg' => '订单类型错误',
      ]);

    // 构建订单数据
    $order_data = [
      'user_id' => $user_id,
      'post_id' => $post_id,
      'order_price' => 0,
      'order_trade_no' => wp_date('YmdHis') . mt_rand(100, 999)
        . mt_rand(100, 999) . mt_rand(100, 999), // 本地订单号
      'order_type' => $order_type, // 订单类型 1:'Post' 2:'Charge' 3:'VIP'
      'pay_type' => $pay_type_id, // 支付方式
      'pay_price' => 0,
      'order_name' => '商城自助购买',
      'callback_url' => esc_url(home_url()),
      'order_info' => [
        'aff_id' => 1, // TODO: 获取推荐人信息
        'ip' => get_ip_address(), // ip
      ],
    ];

    if ($order_type === 1) {
      // 购买文章
      if (empty(get_permalink($post_id)))
        wp_send_json([
          'status' => 0,
          'msg' => '文章不存在',
        ]);

      $price_data = get_post_price_data($post_id);
      $post_price = get_user_pay_post_price($user_id, $post_id);

      if ($post_price === false)
        wp_send_json([
          'status' => 0,
          'msg' => '暂无购买权限',
        ]);

      if ($post_price > 0) {
        $order_data['order_price'] = site_convert_amount($price_data['default'], 'rmb');
        $order_data['pay_price'] = site_convert_amount($post_price, 'rmb');
        $order_data['callback_url'] = esc_url(get_permalink($post_id));
      }

      $post_pay_data = get_post_pay_data($post_id);
      $order_data['order_info']['vip_rate'] = $post_pay_data['vip_rate'];
    }

    // 序列化订单信息
    $order_data['order_info'] = maybe_serialize($order_data['order_info']);

    // 订单验证
    if (empty($order_data['order_price']))
      wp_send_json([
        'status' => 0,
        'msg' => '订单金额错误',
      ]);

    // 订单入库
    if (!Capalot_Shop::add_order($order_data))
      wp_send_json([
        'status' => 0,
        'msg' => '订单创建失败',
      ]);



    // // 请求支付接口
    $response = capalot_get_request_pay($order_data);

    wp_send_json($response);
  }

  //公告
  public function get_site_notify()
  {
    $this->valid_nonce_ajax(); #安全验证

    if (!is_site_notify()) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('暂无公告', 'ripro'),
      ));
    }

    $title = _capalot('site_notify_title');
    $desc = _capalot('site_notify_desc');
    $html = '<div class="site-notify-body"><h1 class="notify-title"><i class="fa fa-bell-o me-1"></i>' . $title . '</h1><div class="notify-desc">' . $desc . '</div></div>';
    wp_send_json(array(
      'status' => 1,
      'msg'    => $html,
    ));
  }

  //签到
  public function user_qiandao()
  {
    $this->valid_nonce_ajax(); #安全验证
    $user_id  = get_current_user_id();

    if (!is_site_qiandao()) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('签到功能暂未开启', 'ripro'),
      ));
    }

    if (is_user_today_qiandao($user_id)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('今日已签到，请明日再来', 'ripro'),
      ));
    }

    $site_qiandao_coin_num = sprintf('%0.1f', abs(_capalot('site_qiandao_coin_num', '0.5')));

    if (!update_user_meta($user_id, 'cao_qiandao_time', time()) || !change_user_coin_balance($user_id, $site_qiandao_coin_num, '+')) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('签到失败', 'ripro'),
      ));
    }

    wp_send_json(array(
      'status' => 1,
      'msg'    => sprintf(__('签到成功，领取(%s)%s', 'ripro'), $site_qiandao_coin_num, get_site_coin_name()),
    ));
  }
}
