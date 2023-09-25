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
    $this->add_action('update_profile', 1); //保存个人信息
    $this->add_action('update_new_email', 1); //保存个人信息
    $this->add_action('update_password', 1); //修改密码
    $this->add_action('update_avatar', 1); //上传头像
    $this->add_action('get_captcha_img'); //验证码
    $this->add_action('user_register', 0); //注册
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

  // 用户注册
  public function user_register()
  {

    $this->valid_nonce_ajax(); #安全验证

    if (!is_site_user_register()) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => '本站未开启注册功能',
      ));
    }

    $user_name        = wp_unslash(get_response_param('user_name'), true);
    $user_email       = sanitize_email(get_response_param('user_email'));
    $user_password    = wp_unslash(get_response_param('user_password'));
    $user_password_ok = wp_unslash(get_response_param('user_password_ok'));
    $invite_code      = esc_sql(trim(get_response_param('invite_code')));
    $captcha_code     = wp_unslash(trim(get_response_param('captcha_code')));

    if (!validate_username($user_name)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('用户名格式错误', 'ripro'),
      ));
    }

    if (!is_email($user_email)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('邮箱地址格式错误', 'ripro'),
      ));
    }

    if (!$user_name || !$user_email || !$user_password || !$user_password_ok) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('请输入完整注册信息', 'ripro'),
      ));
    }

    if ($user_password !== $user_password_ok) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('两次密码输入不一致', 'ripro'),
      ));
    }

    if (is_site_img_captcha() && !is_img_captcha(strtolower($captcha_code))) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('验证码错误，请刷新验证码', 'ripro'),
      ));
    }

    if (username_exists($user_name)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('此用户名已被注册', 'ripro'),
      ));
    }

    if (email_exists($user_email)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('此邮箱已被注册', 'ripro'),
      ));
    }


    //邀请码注册
    if (is_site_invitecode_register()) {

      if (empty($invite_code)) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('请输入邀请码', 'ripro'),
        ));
      }

      //验证邀请码
      $cdk_data = Capalot_Cdk::get_cdk($invite_code);

      if (empty($cdk_data)) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('邀请码错误', 'ripro'),
        ));
      }

      if ($cdk_data->type != 3 || $cdk_data->status != 0) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('邀请码已失效', 'ripro'),
        ));
      }

      if (time() > $cdk_data->expiry_time) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('邀请码已到期', 'ripro'),
        ));
      }

      // 处理优惠码状态
      $update_cdk = Capalot_Cdk::update_cdk(
        array('status' => 1),
        array('id' => $cdk_data->id),
        array('%d'),
        array('%d')
      );

      if (!$update_cdk) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('邀请码状态异常，请刷新重试', 'ripro'),
        ));
      }
    }


    $user_id = wp_create_user($user_name, $user_password, $user_email);

    if (is_wp_error($user_id)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => $user_id->get_error_message(),
      ));
    }

    $UserLogin = wp_signon(array(
      'user_login' => $user_name,
      'user_password' => $user_password,
      'remember' => true
    ), false);

    if (is_wp_error($UserLogin)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => $UserLogin->get_error_message(),
      ));
    }

    wp_set_current_user($UserLogin->ID, $UserLogin->user_login);
    wp_set_auth_cookie($UserLogin->ID, true);

    wp_send_json(array(
      'status'   => 1,
      'msg'      => __('注册成功，即将自动登录', 'ripro'),
      'back_url' => get_uc_menu_link('profile'),
    ));
  }
  //验证码
  public function get_captcha_img()
  {
    $this->valid_nonce_ajax(); #安全验证
    wp_send_json(array(
      'status' => 1,
      'msg'    => get_img_captcha(),
    ));
  }
  //上传头像
  public function update_avatar()
  {
    $this->valid_nonce_ajax(); #安全验证
    $user_id = get_current_user_id();
    $file = !empty($_FILES['file']) ? $_FILES['file'] : null;

    if (empty($file)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('请选择头像上传', 'ripro'),
      ));
    }


    //图片上传 没有则不处理
    if ($file["size"] > 500000) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('图片大小超出500KB限制', 'ripro'),
      ));
    }

    if (!in_array($file["type"], ['image/jpg', 'image/gif', 'image/png', 'image/jpeg'])) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('仅支持上传图片', 'ripro'),
      ));
    }

    // 检测文件是否为真实的图片
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('图片格式错误', 'ripro'),
      ));
    }


    // 上传文件
    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    $extension = pathinfo($file["name"], PATHINFO_EXTENSION);

    // 检查上传文件的扩展名是否在允许的范围内
    if (!in_array(strtolower($extension), $allowedExtensions)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('只允许上传图片文件', 'ripro'),
      ));
    }

    // 根据上传文件的类型创建相应的图像
    switch (strtolower($extension)) {
      case "jpg":
      case "jpeg":
        $source = imagecreatefromjpeg($file["tmp_name"]);
        break;
      case "png":
        $source = imagecreatefrompng($file["tmp_name"]);
        break;
      case "gif":
        $source = imagecreatefromgif($file["tmp_name"]);
        break;
      default:
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('未知的文件类型', 'ripro'),
        ));
    }


    // 缩放和裁剪图像到200x200大小
    $newWidth = 100;
    $newHeight = 100;
    $canvas = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($source), imagesy($source));

    // 将裁剪后的图像保存到字节数组
    ob_start();
    switch (strtolower($extension)) {
      case "jpg":
      case "jpeg":
        imagejpeg($canvas, null, 90);
        break;
      case "png":
        imagepng($canvas, null, 9);
        break;
      case "gif":
        imagegif($canvas, null);
        break;
    }
    $imageData = ob_get_contents();
    ob_end_clean();


    // 移动上传的文件到指定目录并重命名
    $newFilename = 'avatar-' . Capalot_Code::encid($user_id) . '.' . $extension;


    add_filter('upload_dir', function ($dirs) {
      $dirs['baseurl'] = WP_CONTENT_URL . '/uploads';
      $dirs['basedir'] = WP_CONTENT_DIR . '/uploads';
      $dirs['path'] = $dirs['basedir'] . $dirs['subdir'];
      $dirs['url'] = $dirs['baseurl'] . $dirs['subdir'];
      return $dirs;
    });

    $wp_upload_dir = wp_upload_dir();

    $file_path = $wp_upload_dir['basedir'] . '/1234/01/' . $newFilename;
    // 如果文件存在，则删除它
    if (file_exists($file_path)) {
      @unlink($file_path);
    }

    $upload = wp_upload_bits($newFilename, null, $imageData, '1234/01');

    if ($upload['error']) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('上传失败请重试', 'ripro'),
      ));
    }

    update_user_meta($user_id, 'user_custom_avatar', $upload['url']);
    update_user_meta($user_id, 'user_avatar_type', 'custom');

    wp_send_json(array(
      'status' => 1,
      'msg'    => __('头像上传成功', 'ripro'),
    ));
  }
  //保存个人信息
  public function update_profile()
  {

    $this->valid_nonce_ajax(); #安全验证

    $user_id      = get_current_user_id();
    $display_name = sanitize_text_field(get_response_param('display_name'));
    $description  = sanitize_text_field(get_response_param('description'));
    $uc_lxqq      = (!empty(absint(get_response_param('uc_lxqq')))) ? absint(get_response_param('uc_lxqq')) : '';

    $meta_input = [
      'qq'     => $uc_lxqq,
      'description' => $description,
    ];

    $UserData = wp_update_user([
      'ID'           => $user_id,
      'nickname'     => $display_name,
      'display_name' => $display_name,
      'meta_input'   => $meta_input,
    ]);

    if (is_wp_error($UserData)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('保存失败', 'ripro'),
      ));
    }

    wp_send_json(array(
      'status' => 1,
      'msg'    => __('保存成功', 'ripro'),
    ));
  }

  //修改邮箱
  public function update_new_email()
  {

    $this->valid_nonce_ajax(); #安全验证

    $user_id      = get_current_user_id();
    $new_user_email = sanitize_email(get_response_param('new_user_email'));

    if (!is_email($new_user_email)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('邮箱地址格式错误', 'ripro'),
      ));
    }

    if (email_exists($new_user_email)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('此邮箱已被使用', 'ripro'),
      ));
    }

    $UserData = wp_update_user([
      'ID'           => $user_id,
      'user_email'   => $new_user_email,
    ]);

    if (is_wp_error($UserData)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('邮箱修改失败', 'ripro'),
      ));
    }

    wp_send_json(array(
      'status' => 1,
      'msg'    => __('邮箱修改成功', 'ripro'),
    ));
  }



  //修改密码
  public function update_password()
  {
    $this->valid_nonce_ajax(); #安全验证
    global $current_user;
    $old_pwd  = get_response_param('old_password');
    $new_pwd  = get_response_param('new_password');
    $new_pwd2 = get_response_param('new_password2');

    if (empty($old_pwd) || empty($new_pwd) || empty($new_pwd2)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('请输入完整密码修改信息', 'ripro'),
      ));
    }
    if ($old_pwd == $new_pwd) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('新密码不能与旧密码相同', 'ripro'),
      ));
    }
    if ($new_pwd !== $new_pwd2) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('两次输入的密码不一致', 'ripro'),
      ));
    }

    if (!user_is_oauth_password($current_user->ID) && !wp_check_password($old_pwd, $current_user->data->user_pass, $current_user->ID)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('旧密码错误，请输入正确的密码', 'ripro'),
      ));
    }

    wp_set_password($new_pwd2, $current_user->ID);
    wp_logout();
    wp_send_json(array(
      'status' => 1,
      'msg'    => __('密码修改成功，请使用新密码重新登录', 'ripro'),
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
    } elseif ($order_type == 2) {
      // 充值...
      $recharge_amount = absint($order_info_key);
      $_minnum = absint(_capalot('site_coin_pay_minnum'));
      $_maxnum = absint(_capalot('site_coin_pay_maxnum'));

      if (empty($recharge_amount)) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('充值数量不能为0', 'ripro'),
        ));
      }

      if ($recharge_amount < $_minnum) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('最低充值限制', 'ripro') . $_minnum . get_site_coin_name(),
        ));
      }

      if ($recharge_amount > $_maxnum) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('最高充值限制', 'ripro') . $_minnum . get_site_coin_name(),
        ));
      }

      $order_data['post_id'] = 0;
      $order_data['order_price'] = site_convert_amount($recharge_amount, 'rmb');
      $order_data['pay_price'] = site_convert_amount($recharge_amount, 'rmb');
      $order_data['callback_url'] = esc_url(get_uc_menu_link('coin'));
    } elseif ($order_type == 3) {
      // 购买VIP...
      $buy_options = get_site_vip_buy_options();
      $day = absint($order_info_key);
      if (empty($buy_options) || empty($buy_options[$day]['coin_price'])) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('VIP套餐不存在', 'ripro'),
        ));
      }

      $uc_vip_info = get_user_vip_data($user_id);
      if ($uc_vip_info['type'] == 'boosvip') {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('您已获得最高特权，无需重复开通', 'ripro'),
        ));
      }

      $vip_price = $buy_options[$day]['coin_price'];
      $order_data['post_id'] = 0;
      $order_data['order_price'] = site_convert_amount($vip_price, 'rmb');
      $order_data['pay_price'] = site_convert_amount($vip_price, 'rmb');
      $order_data['callback_url'] = esc_url(get_uc_menu_link('vip'));

      //VIP订单其他信息 vip_type 会员类型 vip boosvip
      $order_data['order_info']['vip_type'] = $buy_options[$day]['type'];
      $order_data['order_info']['vip_day'] = $buy_options[$day]['day_num'];
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
