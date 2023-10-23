<?php

/**
 * 主题AJAX接口
 * 地址：domain/wp-admin/admin-ajax.php
 * 参数：action 接口
 * 参数：nonce 安全验证参数 使用 wp_create_nonce("capalot_ajax") 方法生成
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
    $this->add_action('user_save_ticket', 1); //保存工单
    $this->add_action('user_qiandao', 1); //签到
    $this->add_action('add_like_post'); //点赞文章
    $this->add_action('add_fav_post'); //收藏文章
    $this->add_action('load_more'); //加载更多文章
    $this->add_action('get_captcha_code'); //获取验证码
    $this->add_action('add_share_post'); //分享文章
    $this->add_action('add_post_views'); //文章阅读数量+1
    $this->add_action('ajax_comment'); //ajax评论
    $this->add_action('vip_cdk_action', 1); //卡密兑换
    $this->add_action('get_site_notify'); //获取全站公告
    $this->add_action('user_lost_pwd', 0); //找回密码
    $this->add_action('user_reset_pwd', 0); //重置新密码
    $this->add_action('user_aff_action', 1); //提现申请
    $this->add_action('start_collect', 1); // 采集数据
  }

  /**
   * 接口安全验证
   */
  private function valid_nonce_ajax()
  {
    if (!check_ajax_referer('capalot_ajax', 'nonce', false)) {
      wp_send_json([
        'status' => 0,
        'msg' => __('非法请求', 'ripro'),
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
        'msg'    => __('本站未开启登录功能', 'ripro'),
      ));
    }

    $user_name     = sanitize_user(get_response_param('user_name'), true);
    $user_password = wp_unslash(get_response_param('user_password'));
    $captcha_code  = wp_unslash(trim(get_response_param('captcha_code')));
    $remember      = (empty(get_response_param('remember'))) ? false : true;

    if (!$user_name || !$user_password) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('请输入账号或密码', 'ripro'),
      ));
    }

    if (is_site_img_captcha() && !verify_captcha_code(strtolower($captcha_code))) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('验证码错误，请刷新验证码', 'ripro'),
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
        'msg'    => __('用户名或密码不正确', 'ripro'),
      ));
    }

    if (!empty(get_user_meta($UserLogin->ID, 'capalot_banned', true))) {
      wp_logout();
      wp_send_json(array(
        'status' => 0,
        'msg'    => sprintf(__('此账号已被封禁（ %s ）', 'ripro'), get_user_meta($UserLogin->ID, 'capalot_banned_reason', true)),
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
        'msg'    => __('本站未开启注册功能', 'ripro'),
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

    if (is_site_img_captcha() && !verify_captcha_code(strtolower($captcha_code))) {
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

    $user_login = $user_name;
    $user_pass  = $user_password;
    $role       = 'editor';
    $userdata = compact('user_login', 'user_pass', 'user_email', 'role');

    $user_id = wp_insert_user($userdata);

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
  public function get_captcha_code()
  {
    $this->valid_nonce_ajax(); #安全验证

    session_start();

    wp_send_json(array(
      'status' => 1,
      'msg'    => get_captcha_code_img(),
    ));
  }

  // 找回密码邮件发送
  public function user_lost_pwd()
  {
    $this->valid_nonce_ajax(); #安全验证
    $user_email   = sanitize_email(get_response_param('user_email'));
    $captcha_code = wp_unslash(trim(get_response_param('captcha_code')));

    if (!is_email($user_email)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('邮箱地址格式错误', 'ripro'),
      ));
    }

    if (is_site_img_captcha() && !verify_captcha_code(strtolower($captcha_code))) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('验证码错误，请刷新验证码', 'ripro'),
      ));
    }

    if (!email_exists($user_email)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('此邮箱无绑定用户', 'ripro'),
      ));
    }

    $user_data = get_user_by('email', $user_email);

    if (!$user_data) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('用户查询失败', 'ripro'),
      ));
    }

    // Redefining user_login ensures we return the right case in the email.
    $key     = get_password_reset_key($user_data);
    if (is_wp_error($key)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('账号异常，请刷新页面', 'ripro'),
      ));
    }

    do_action('lostpassword_post');

    $reset_url = esc_url_raw(
      add_query_arg(
        array(
          'riresetpass'  => 'true',
          'rifrp_action' => 'rp',
          'key'          => $key,
          'uid'          => $user_data->ID,
        ),
        wp_lostpassword_url()
      )
    );

    $reset_link = '<a href="' . $reset_url . '">' . $reset_url . '</a>';

    $send = do_action('capalot_send_mail_msg', [
      'email' => $user_data->user_email,
      'title' => __('重置密码链接', 'ripro'),
      'msg'   => sprintf(__('请打开此链接重置您的账号密码: %s', 'ripro'), $reset_link),
    ]);

    wp_send_json(array(
      'status'   => 1,
      'msg'      => __('重置密码链接将发送到您的邮箱', 'ripro'),
      'back_url' => esc_url(home_url()),
    ));
  }

  // 重置密码
  public function user_reset_pwd()
  {
    $this->valid_nonce_ajax(); #安全验证

    $uid              = absint(get_response_param('uid', 0));
    $key              = wp_unslash(get_response_param('key'));
    $user_password    = wp_unslash(get_response_param('user_password'));
    $user_password_ok = wp_unslash(get_response_param('user_password_ok'));
    $captcha_code     = wp_unslash(trim(get_response_param('captcha_code')));

    if ($user_password !== $user_password_ok || empty($user_password)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('两次密码输入不一致', 'ripro'),
      ));
    }

    if (empty($uid) || empty($key)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('页面参数错误', 'ripro'),
      ));
    }

    if (is_site_img_captcha() && !verify_captcha_code(strtolower($captcha_code))) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('验证码错误，请刷新验证码', 'ripro'),
      ));
    }

    $user_data = get_user_by('id', $uid);
    if (!$user_data) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('账号信息获取失败', 'ripro'),
      ));
    }

    $user_check = check_password_reset_key($key, $user_data->user_login);

    if (is_wp_error($user_check)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('重置链接无效或已过期', 'ripro'),
      ));
    }

    // 验证通过 处理业务逻辑
    reset_password($user_check, $user_password);

    wp_send_json(array(
      'status'   => 1,
      'msg'      => __('密码重置成功,请使用新密码登录', 'ripro'),
      'back_url' => esc_url(wp_login_url()),
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
    $newFilename = 'avatar-' . $user_id . '.' . $extension;


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

  //提交工单
  public function user_save_ticket()
  {
    $this->valid_nonce_ajax(); #安全验证
    $user_id  = get_current_user_id();
    $file_uri = '';
    // $file     = !empty($_FILES['file']) ? $_FILES['file'] : null;
    $file     = null;
    $type     = absint(get_response_param('type'));
    $title    = sanitize_text_field(trim(get_response_param('title')));
    $content  = wp_kses_post(get_response_param('content'));


    if (!is_site_tickets()) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('网站工单功能暂未开启', 'ripro'),
      ));
    }

    if (empty($title)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('请输入工单标题', 'ripro'),
      ));
    }


    if (empty($title)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('请输入工单标题', 'ripro'),
      ));
    }

    if (empty($content)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('请输入工单内容', 'ripro'),
      ));
    }

    //图片上传 没有则不处理
    if (!empty($file)) {

      if ($file["size"] > 1000000) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('图片大小超出1MB限制', 'ripro'),
        ));
      }

      if (!in_array($file["type"], ['image/jpg', 'image/gif', 'image/png', 'image/jpeg'])) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('仅支持上传图片附件', 'ripro'),
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

      // 获取默认上传目录路径
      $upload_dir = wp_upload_dir();
      $ticket_dir = $upload_dir['basedir'] . '/ticket-file'; // 新建ticket目录
      $ticket_uri = $upload_dir['baseurl'] . '/ticket-file'; // ticket目录的URL地址
      if (!file_exists($ticket_dir)) {
        mkdir($ticket_dir, 0755, true);
      }

      // 上传文件
      $date = wp_date('Ymd_His'); // get the current date and time in "YYYYMMDDHHIISS" format
      $new_file = $date . '_' . basename($file["name"]); // combine the date and original filename
      $target_file = $ticket_dir . '/' . $new_file;
      if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $file_uri = str_replace($ticket_dir, $ticket_uri, $target_file);
      }
    }

    $data = [
      'type'        => $type,
      'title'       => $title,
      'content'     => $content,
      'file'        => $file_uri,
      'creator_id'  => $user_id,
      'create_time' => time(),
      'status'      => 0,
    ];

    if (!Capalot_Ticket::add($data)) {
      wp_send_json(array(
        'status' => 1,
        'msg'    => __('工单提交失败，请重试', 'ripro'),
      ));
    }

    wp_send_json(array(
      'status' => 1,
      'msg'    => __('工单提交成功，客服会尽快处理', 'ripro'),
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
        'msg' => __('商城功能未开启', 'ripro'),
      ]);

    if (
      in_array($order_type, array(2, 3)) && empty($user_id)
      || (empty($user_id) && !is_site_not_user_pay())
    )
      wp_send_json([
        'status' => 0,
        'msg' => __('请登录后购买', 'ripro'),
      ]);

    $post_price = get_user_pay_post_price($user_id, $post_id);

    if ($post_price === false)
      wp_send_json([
        'status' => 0,
        'msg' => __('暂无购买权限', 'ripro'),
      ]);

    wp_send_json([
      'status' => 1,
      'msg' => __('获取成功', 'ripro'),
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
        'msg' => __('商城功能未开启', 'ripro'),
      ]);

    if (
      in_array($order_type, array(2, 3)) && empty($user_id)
      || (empty($user_id) && !is_site_not_user_pay())
    )
      wp_send_json([
        'status' => 0,
        'msg' => __('请登录后购买', 'ripro'),
      ]);

    if (!in_array($order_type, array(1, 2, 3, 4)))
      wp_send_json([
        'status' => 0,
        'msg' => __('订单类型错误', 'ripro'),
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
        'aff_id' => capalot_get_current_aff_id($user_id),
        'ip' => get_ip_address(), // ip
      ],
    ];

    if ($order_type === 1) {
      // 购买文章
      if (empty(get_permalink($post_id)))
        wp_send_json([
          'status' => 0,
          'msg' => __('文章不存在', 'ripro'),
        ]);

      $price_data = get_post_price_data($post_id);
      $post_price = get_user_pay_post_price($user_id, $post_id);

      if ($post_price === false)
        wp_send_json([
          'status' => 0,
          'msg' => __('暂无购买权限', 'ripro'),
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
        'msg' =>  __('订单金额错误', 'ripro'),
      ]);

    // 订单入库
    if (!Capalot_Shop::add_order($order_data))
      wp_send_json([
        'status' => 0,
        'msg' =>  __('订单创建失败', 'ripro'),
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
    wp_send_json(array(
      'status' => 1,
      'title'  => $title,
      'desc'   => $desc
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

    if (!update_user_meta($user_id, 'capalot_qiandao_time', time()) || !change_user_coin_balance($user_id, $site_qiandao_coin_num, '+')) {
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

  // 点赞文章
  public function add_like_post()
  {
    $this->valid_nonce_ajax(); #安全验证

    $post_id = (int) get_response_param('post_id');
    $is_add = (int) get_response_param('is_add');
    $user_id = get_current_user_id();

    $is_like = capalot_is_post_like($user_id, $post_id);

    if ($is_add) {
      if ($is_like) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('您已点赞过', 'ripro'),
        ));
      }

      if ($post_id && capalot_add_post_likes($post_id, 1)) {
        wp_send_json(array(
          'status' => 1,
          'msg'    => __('点赞成功', 'ripro'),
        ));
      } else {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('点赞失败', 'ripro'),
        ));
      }
    } else {
      if ($is_like) capalot_delete_post_like($user_id, $post_id);

      wp_send_json(array(
        'status' => 1,
        'msg'    => __('已取消点赞', 'ripro'),
      ));
    }
  }

  // 收藏文章
  public function add_fav_post()
  {
    $this->valid_nonce_ajax(); #安全验证

    $post_id = (int) get_response_param('post_id');
    $is_add  = (int) get_response_param('is_add');
    $user_id = get_current_user_id();

    if (empty($user_id)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('请登录后收藏', 'ripro'),
      ));
    }

    $is_fav = capalot_is_post_fav($user_id, $post_id);

    if ($is_add) {
      if ($is_fav) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('您已收藏过', 'ripro'),
        ));
      }

      if (capalot_add_post_fav($user_id, $post_id)) {
        wp_send_json(array(
          'status' => 1,
          'msg'    => __('收藏成功', 'ripro'),
        ));
      }
    } else {
      if ($is_fav) {
        capalot_delete_post_fav($user_id, $post_id);
      }
      wp_send_json(array(
        'status' => 1,
        'msg'    => __('已取消收藏', 'ripro'),
      ));
    }
  }

  // 分享文章
  public function add_share_post()
  {
    $this->valid_nonce_ajax(); #安全验证
    $post_id = (int) get_response_param('post_id');
    $user_id = get_current_user_id();
    $share_url = get_user_aff_permalink(get_permalink($post_id), $user_id);

    $body = '<div class="share-body"><img class="share-qrcode" src="' . get_qrcode_url($share_url) . '">';
    $body .= '<div class="share-url user-select-all">' . $share_url . '</div><div class="share-desc">' . _e('手机扫码或复制链接分享', 'ripro') . '</div></div>';

    $post = get_post($post_id);
    $categories = get_the_category($post_id);

    $data = [
      'title'    => get_the_title($post_id),
      'desc'     => wp_trim_words(strip_shortcodes($post->post_content), 92, '...'),
      'img'      => set_url_scheme(capalot_get_thumbnail_url($post, 'thumbnail')),
      'category' => '+ ' . $categories[0]->name . ' by ' . get_the_author_meta('display_name', $post->post_author),
      'date_day' => get_the_date('d', $post_id),
      'date_year' => get_the_date('m / Y', $post_id),
      'qrcode'   => get_qrcode_url($share_url),
      'url'   => get_permalink($post_id),
      'site_logo' => set_url_scheme(_capalot('site_logo', '')),
      'site_name' => get_bloginfo('name'),
      'site_desc' => get_bloginfo('description'),
    ];

    wp_send_json(array(
      'status' => 1,
      'msg'    => array('data' => $data, 'html' => $body),
    ));
  }

  // 文章阅读数量+1
  public function add_post_views()
  {

    $this->valid_nonce_ajax(); #安全验证

    $post_id = (int) get_response_param('post_id');
    if ($post_id && capalot_add_post_views($post_id)) {
      wp_send_json(array(
        'status' => 1,
        'msg'    => sprintf('PID：%s ', $post_id),
      ));
    } else {
      wp_send_json(array(
        'status' => 0,
        'msg'    => sprintf('PID：%s error', $post_id),
      ));
    }
  }

  // 分页加载更多文章
  function load_more()
  {
    $cat = get_response_param('cat');
    $s = get_response_param('s');

    $ajaxposts = new WP_Query([
      'ignore_sticky_posts' => false,
      'post_status' => 'publish',
      'post_type'   => 'post',
      'paged' => $_POST['paged'],
      's' => $s,
      'category_name' => $cat,
    ]);

    $response = '';
    $max_pages = $ajaxposts->max_num_pages;

    if ($ajaxposts->have_posts()) {
      while ($ajaxposts->have_posts()) : $ajaxposts->the_post();
        $item_config = get_posts_style_config();

        ob_start();
        get_template_part('template-parts/loop/item', '', $item_config);

        $response .= ob_get_clean();
      endwhile;
    } else {
      $response = '';
    }

    $result = [
      'max' => $max_pages,
      'html' => $response,
    ];

    echo json_encode($result);
    exit;
  }

  // 评论
  public function ajax_comment()
  {

    $this->valid_nonce_ajax(); #安全验证

    $comment = wp_handle_comment_submission(wp_unslash($_POST));
    if (is_wp_error($comment)) {
      $error_data = intval($comment->get_error_data());
      if (!empty($error_data)) {
        wp_die($comment->get_error_message(), __('Comment Submission Failure'), array('response' => $error_data, 'back_link' => true));
        exit;
      } else {
        wp_die('Unknown error', __('Comment Submission Failure'), array('response' => 500, 'back_link' => true));
        exit;
      }
    }

    $user = wp_get_current_user();
    do_action('set_comment_cookies', $comment, $user);

    echo "success";
    exit;
  }

  //卡密兑换VIP接口
  public function vip_cdk_action()
  {
    $this->valid_nonce_ajax(); #安全验证
    $cdk_code     = esc_sql(trim(get_response_param('cdk_code')));
    $captcha_code = wp_unslash(trim(get_response_param('captcha_code')));
    $user_id      = get_current_user_id();


    if (empty(_capalot('is_site_cdk_pay', true))) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('暂未开启兑换', 'ripro'),
      ));
    }

    if (empty($user_id)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('请登录后兑换', 'ripro'),
      ));
    }

    if (empty($cdk_code)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('请输入兑换码', 'ripro'),
      ));
    }

    if (is_site_img_captcha() && !verify_captcha_code(strtolower($captcha_code))) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('验证码错误，请刷新验证码', 'ripro'),
      ));
    }

    $cdk_data = Capalot_Cdk::get_cdk($cdk_code);

    if (empty($cdk_data)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('兑换码错误', 'ripro'),
      ));
    }

    if ($cdk_data->status != 0) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('兑换码无效', 'ripro'),
      ));
    }

    if (time() > $cdk_data->expiry_time) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('兑换码已到期', 'ripro'),
      ));
    }

    //判断卡密类型
    if ($cdk_data->type == 1) {
      // 余额充值卡...
      if (empty($cdk_data->amount) || $cdk_data->amount < 0) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('兑换金额错误', 'ripro'),
        ));
      }

      $order_price = site_convert_amount($cdk_data->amount, 'rmb');
      $order_type = 2;
      $order_info = array(
        'ip'    => get_ip_address(),
        'code'  => $cdk_data->code,
      );
    } elseif ($cdk_data->type == 2) {
      // 会员兑换卡...
      $vip_day = absint($cdk_data->info);
      $vip_buy_options = get_site_vip_buy_options();

      if (!isset($vip_buy_options[$vip_day])) {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('兑换类型错误', 'ripro'),
        ));
      }

      $vip_options = $vip_buy_options[$vip_day];
      $uc_vip_info = get_user_vip_data($user_id);
      if ($uc_vip_info['type'] == 'boosvip') {
        wp_send_json(array(
          'status' => 0,
          'msg'    => __('您已获得最高特权，无需重复开通', 'ripro'),
        ));
      }

      $order_price = site_convert_amount($vip_options['coin_price'], 'rmb');
      $order_type = 3;
      $order_info = array(
        'ip' => get_ip_address(),
        'vip_type' => $vip_options['type'],
        'vip_day' => $vip_options['day_num'],
      );
    }

    //添加订单入库
    $order_data = [
      'user_id'        => $user_id,
      'post_id'        => 0,
      'order_price'    => $order_price,
      'order_trade_no' => wp_date("YmdHis") . mt_rand(100, 999) . mt_rand(100, 999) . mt_rand(100, 999), //本地订单号
      'order_type'     => $order_type, //订单类型 1=>'Post',2=>'charge',3=>'VIP'
      'pay_type'       => 88, //支付方式ID
      'pay_price'      => $order_price,
      'order_info'     => maybe_serialize($order_info),
    ];

    // 添加订单入库
    if (!Capalot_Shop::add_order($order_data)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('订单创建失败', 'ripro'),
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
        'msg'    => __('兑换失败，请更换重试', 'ripro'),
      ));
    }

    //处理回调
    $update_order = Capalot_Shop::pay_notify_callback($order_data, $cdk_data->code);

    if (!$update_order) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('订单状态处理异常', 'ripro'),
      ));
      return  $cdk_data;
    }

    wp_send_json(array(
      'status' => 1,
      'msg'    => __('兑换成功，即将刷新页面', 'ripro'),
    ));
  }

  //申请提现按钮
  public function user_aff_action()
  {
    $this->valid_nonce_ajax(); #安全验证
    $user_id = get_current_user_id();

    if (empty($user_id)) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('请登录后申请', 'ripro'),
      ));
    }

    if (!is_site_user_aff()) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('网站推广功能暂未开启', 'ripro'),
      ));
    }

    $user_aff_info = Capalot_Aff::get_user_aff_info($user_id);
    $min_price     = absint(_capalot('site_min_tixin_price', 10));

    if ($user_aff_info['can_be_withdraw'] < $min_price) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => sprintf(__('可提现金额须大于 %s 才可申请', 'ripro'), $min_price),
      ));
    }

    $update_aff = Capalot_Aff::update_aff_log(
      array('status' => 1, 'apply_time' => time()),
      array('aff_uid' => $user_id, 'status' => 0),
      ['status' => '%d', 'apply_time' => '%s'],
      ['aff_uid' => '%d', 'status' => '%d']
    );

    if (!$update_aff) {
      wp_send_json(array(
        'status' => 0,
        'msg'    => __('申请提现失败，请联系客服处理', 'ripro'),
      ));
    }
    wp_send_json(array(
      'status' => 1,
      'msg'    => __('申请提现成功，请联系网站客服人工处理', 'ripro'),
    ));
  }

  //数据采集
  public function start_collect()
  {
    $this->valid_nonce_ajax(); #安全验证

    extract($_POST);

    if (empty($url))
      wp_send_json([
        'status' => 0,
        'msg'    => __('请输入采集地址', 'ripro'),
      ]);

    $response = wp_remote_get($url);

    wp_send_json([
      'status' => 1,
      'msg'    => $response,
    ]);
  }
}
