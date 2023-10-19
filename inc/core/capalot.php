<?php


/**
 * 商城
 */
class Capalot_Shop
{

  public function __construct()
  {
  }

  /**
   * 获取支付状态
   * @param int $user_id 用户ID
   * @param int $post_id 文章ID
   */
  public static function get_pay_post_status($user_id, $post_id)
  {
    // 查询wp_capalot_order表中是否有该用户的订单
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_order';

    $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND post_id = %d", $user_id, $post_id));

    if ($order == null || $order->pay_status == 0) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * 订单入库
   * @param array $order_data 订单数据
   */
  public static function add_order($order_data)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_order';

    $insert = $wpdb->insert(
      $table_name,
      [
        'user_id' => $order_data['user_id'],
        'post_id' => $order_data['post_id'],
        'order_trade_no' => $order_data['order_trade_no'],
        'order_type' => $order_data['order_type'],
        'order_price' => $order_data['order_price'],
        'create_time' => time(),
        'pay_type' => $order_data['pay_type'],
      ]
    );

    return $insert ? true : false;
  }

  /**
   * 支付回调
   * @param string $order_data 订单数据
   */
  public static function pay_notify_callback($order_data, $cdk_code = null)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_order';

    $order_data['pay_time'] = time();
    $order_data['pay_trade_no'] = '2023-' . time();

    // 更新$order_trade_no对应的订单状态
    $update = $wpdb->update(
      $table_name,
      [
        'pay_time' => $order_data['pay_time'],
        'pay_price' => $order_data['pay_price'] ?? '0',
        'pay_trade_no' => $order_data['pay_trade_no'],
        'order_info' => $order_data['order_info'] ?? $cdk_code,
        'pay_status' => 1,
      ],
      [
        'order_trade_no' => $order_data['order_trade_no'],
      ]
    );

    if ($update) {
      $order_data['pay_status'] = 1;

      $order_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE order_trade_no = %s", $order_data['order_trade_no']));
      $order_data['order_id'] = $order_id;

      $post = get_post($order_data['post_id']);
      $order_data['author_id'] = $post->post_author;

      do_action('capalot_pay_success', $order_data);
    }

    return $update ? true : false;
  }

  /**
   * 获取订单类型
   */
  public static function get_order_type($type)
  {
    switch ($type) {
      case '1':
        return '文章订单';
        break;
      case '2':
        return '充值订单';
        break;
      case '3':
        return 'VIP订单';
        break;
      default:
        return '其他订单';
        break;
    }
  }

  /**
   * 获取支付类型
   */
  public static function get_pay_type($pay_type)
  {
    switch ($pay_type) {
      case '1':
        echo '官方-支付宝';
        break;
      case '2':
        echo '官方-微信';
        break;
      case '11':
        echo '虎皮椒-支付宝';
        break;
      case '12':
        echo '虎皮椒-微信';
        break;
      case '21':
        echo '迅虎-支付宝';
        break;
      case '22':
        echo '迅虎-微信';
        break;
      case '31':
        echo 'PATJS-支付宝';
        break;
      case '32':
        echo 'PATJS-微信';
        break;
      case '41':
        echo '易支付-支付宝';
        break;
      case '42':
        echo '易支付-微信';
        break;
      case '55':
        echo 'PayPal';
        break;
      case '66':
        echo '手工支付';
        break;
      case '77':
        echo '后台支付';
        break;
      case '88':
        echo '卡密支付';
        break;
      case '99':
        echo '余额支付';
        break;
    }
  }

  /**
   * 获取支付状态
   */
  public static function get_pay_status($status)
  {
    switch ($status) {
      case '0':
        return '未支付';
        break;
      case '1':
        return '支付成功';
        break;
    }
  }
}

/**
 * 支付
 */
class Capalot_Pay
{

  private $alipay_params;
  private $alipay_config;

  private $wx_params;
  private $wx_config;

  public function __construct()
  {
    $this->alipay_params = new \Yurun\PaySDK\Alipay\Params\PublicParams;
    $this->alipay_config = _capalot('alipay');
    $this->alipay_params->appID = $this->alipay_config['appid'];

    $this->wx_params = new \Yurun\PaySDK\Weixin\Params\PublicParams;
    $this->wx_config = _capalot('weixinpay');
    $this->wx_params->appID = $this->wx_config['appid'];
    $this->wx_params->mch_id = $this->wx_config['mch_id'];
    $this->wx_params->key = $this->wx_config['key'];
  }

  // 支付宝手机支付
  public function alipay_app_wap_pay($order_data = null)
  {
    $pay = new \Yurun\PaySDK\Alipay\SDK($this->alipay_params);

    $request = new \Yurun\PaySDK\Alipay\Params\WapPay\Request;
    $request->notify_url = get_template_directory_uri() . '/inc/shop/alipay/notify.php';
    $request->return_url = get_template_directory_uri() . '/inc/shop/alipay/return.php';
    $request->businessParams->seller_id = $this->alipay_params->appID;
    $request->businessParams->out_trade_no = $order_data['order_trade_no'];
    $request->businessParams->total_fee = $order_data['order_price'];
    $request->businessParams->subject = $order_data['order_name'];
    $request->businessParams->body = $order_data['order_name'];
    $request->businessParams->show_url = get_permalink($order_data['post_id']);

    $pay->prepareExecute($request, $url);

    return esc_url($url);
  }

  // 支付宝网页支付
  public function alipay_app_web_pay($order_data = null)
  {
    $pay = new \Yurun\PaySDK\Alipay\SDK($this->alipay_params);

    $request = new \Yurun\PaySDK\Alipay\Params\Pay\Request;
    $request->notify_url = get_template_directory_uri() . '/inc/shop/alipay/notify.php';
    $request->return_url = get_template_directory_uri() . '/inc/shop/alipay/return.php';
    $request->businessParams->seller_id = $this->alipay_params->appID;
    $request->businessParams->out_trade_no = $order_data['order_trade_no'];
    $request->businessParams->total_fee = $order_data['order_price'];
    $request->businessParams->subject = $order_data['order_name'];
    $request->businessParams->body = $order_data['order_name'];

    $pay->prepareExecute($request, $url);

    return $url;
  }

  // 支付宝当面付
  public function alipay_app_qr_pay($order_data = null)
  {
    $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams;
    $params->appID = $this->alipay_config['appid'];
    $params->appPrivateKey = $this->alipay_config['private_key'];
    $params->appPublicKey = $this->alipay_config['public_key'];

    $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);

    $request = new \Yurun\PaySDK\AlipayApp\FTF\Params\QR\Request;
    $request->notify_url = get_template_directory_uri() . '/inc/shop/alipay/notify.php';
    $request->businessParams->out_trade_no = $order_data['order_trade_no'];
    $request->businessParams->total_amount = $order_data['order_price'];
    $request->businessParams->subject = $order_data['order_name'];
    $request->businessParams->body = $order_data['order_name'];

    // 调用接口
    try {
      $data = $pay->execute($request);
      // var_dump('result:', $data);
      // var_dump('success:', $pay->checkResult());
      // var_dump('error:', $pay->getError(), 'error_code:', $pay->getErrorCode());
    } catch (Exception $e) {
      // var_dump($pay->response->body());
    }

    return $pay->response->body();
  }

  // 微信网页支付
  public function weixin_h5_pay($order_data = null)
  {
    $pay = new \Yurun\PaySDK\Weixin\SDK($this->wx_params);

    $request = new \Yurun\PaySDK\Weixin\H5\Params\Pay\Request;
    $request->notify_url = get_template_directory_uri() . '/inc/shop/weixinpay/notify.php';
    $request->body = $order_data['order_name'];
    $request->out_trade_no = $order_data['order_trade_no'];
    $request->total_fee = $order_data['order_price'];
    $request->spbill_create_ip = $_SERVER['REMOTE_ADDR'];

    $result = $pay->execute($request);

    return $result;
  }

  // 微信当面付
  public function weixin_qr_pay($order_data  = null)
  {
    $pay = new \Yurun\PaySDK\Weixin\SDK($this->wx_params);

    $request = new \Yurun\PaySDK\Weixin\H5\Params\Pay\Request;
    $request->body = $order_data['order_name'];
    $request->out_trade_no = $order_data['order_trade_no'];
    $request->total_fee = $order_data['order_price'];
    $request->spbill_create_ip = $_SERVER['REMOTE_ADDR'];
    $request->notify_url = get_template_directory_uri() . '/inc/shop/weixinpay/notify.php';

    $result = $pay->execute($request);

    return $result['code_url'];
  }
}

/**
 * 推广
 */
class Capalot_Aff
{
  public function __construct()
  {
  }

  // 获取推广状态
  public static function get_aff_status($param)
  {
    switch ($param) {
      case '0':
        return '可提现';
        break;
      case '1':
        return '提现中';
        break;
      case '2':
        return '已提现';
        break;
    }
  }

  // 添加推广记录
  public static function add_aff_log($param)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_aff';

    $param['create_time'] = time();

    $insert = $wpdb->insert(
      $table_name,
      $param
    );

    return $insert ? true : false;
  }

  // 更新推广记录
  public static function update_aff_log($data, $where, $data_format, $where_format = ['id' => '%d'])
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_aff';

    $sql = "UPDATE $table_name SET ";

    $set_clause = array();
    foreach ($data as $key => $_value) {
      $set_clause[] = "$key = $data_format[$key]";
    }
    $sql .= implode(', ', $set_clause);

    $where_clause = [];
    foreach ($where as $key => $_valua) {
      $where_clause[] = "$key = $where_format[$key]";
    }
    $sql .= ' WHERE ' . implode(' AND ', $where_clause);

    $data_values = array_values($data);
    $where_value = array_values($where);

    $result = $wpdb->query($wpdb->prepare($sql, array_merge($data_values, $where_value)));

    return $result;
  }

  // 获取用户推广信息
  public static function get_user_aff_info($user_id)
  {

    $user_aff_info = [
      'total' => self::can_be_withdraw($user_id) + self::withdrawing($user_id) + self::withdrawed($user_id),
      'can_be_withdraw' => self::can_be_withdraw($user_id),
      'withdrawing' => self::withdrawing($user_id),
      'withdrawed' => self::withdrawed($user_id),
      'ref_uids' => self::get_ref_ids($user_id),
    ];

    return $user_aff_info;
  }

  // 可提现金额
  public static function can_be_withdraw($user_id)
  {
    global $wpdb;
    $aff_table = $wpdb->prefix . 'capalot_aff';
    $order_table = $wpdb->prefix . 'capalot_order';

    $sql = "SELECT SUM(b.pay_price)
    FROM $aff_table AS a
    LEFT JOIN $order_table AS b ON a.order_id = b.id
    WHERE a.aff_uid = %d AND a.status = 0";

    $sum = $wpdb->get_var($wpdb->prepare($sql, $user_id));

    return $sum ? (float) $sum : 0;
  }

  // 提现中金额
  public static function withdrawing($user_id)
  {
    global $wpdb;
    $aff_table = $wpdb->prefix . 'capalot_aff';
    $order_table = $wpdb->prefix . 'capalot_order';

    $sql = "SELECT SUM(b.pay_price)
    FROM $aff_table AS a
    LEFT JOIN $order_table AS b ON a.order_id = b.id
    WHERE a.aff_uid = %d AND a.status = 1";

    $sum = $wpdb->get_var($wpdb->prepare($sql, $user_id));

    return $sum ? (float) $sum : 0;
  }

  // 已提现金额
  public static function withdrawed($user_id)
  {
    global $wpdb;
    $aff_table = $wpdb->prefix . 'capalot_aff';
    $order_table = $wpdb->prefix . 'capalot_order';

    $sql = "SELECT SUM(b.pay_price)
    FROM $aff_table AS a
    LEFT JOIN $order_table AS b ON a.order_id = b.id
    WHERE a.aff_uid = %d AND a.status = 2";

    $sum = $wpdb->get_var($wpdb->prepare($sql, $user_id));

    return $sum ? (float) $sum : 0;
  }

  // 已推广的人的id
  public static function get_ref_ids($user_id)
  {
    global $wpdb;

    $ref_ids = $wpdb->get_col(
      $wpdb->prepare("SELECT user_id
      FROM $wpdb->usermeta
      WHERE meta_key = %s AND meta_value = %d", 'capalot_ref_from', $user_id)
    );

    return array_unique($ref_ids);
  }
}

/**
 * CDK
 */
class Capalot_Cdk
{
  public function __construct()
  {
  }

  /**
   * 添加cdk
   *
   * @param array $cdk_data cdk数据
   * @param int $cdk_data['amount'] 数额
   * @param int $cdk_data['type'] 类型
   * @param int $cdk_data['create_time'] 创建时间
   * @param int $cdk_data['expiry_time'] 过期时间
   * @param string $cdk_data['code'] cdk码
   * @param string $cdk_data['info'] cdk信息
   * @param int $cdk_data['status'] cdk状态
   */
  public static function add_cdk($cdk)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_cdk';

    $insert = $wpdb->insert(
      $table_name,
      [
        'amount' => $cdk['amount'],
        'type' => $cdk['type'],
        'create_time' => $cdk['create_time'],
        'expiry_time' => $cdk['expiry_time'],
        'code' => $cdk['code'],
        'info' => $cdk['info'],
        'status' => $cdk['status'],
      ]
    );


    return $insert ? true : false;
  }

  /**
   * 获取cdk类型
   */
  public static function get_cdk_type($type)
  {
    switch ($type) {
      case 1:
      case '1':
        return '余额充值卡';
        break;
      case 2:
      case '2':
        return '会员兑换卡';
        break;
      case 3:
      case '3':
        return '注册邀请码';
        break;
    }
  }

  /**
   * 获取CDK使用状态
   */
  public static function get_cdk_status($status)
  {
    switch ($status) {
      case 0:
      case '0':
        return '未使用';
        break;
      case 1:
      case '1':
        return '已使用';
        break;
    }
  }
  /**
   * 根据code获取CDK
   */
  public static function get_cdk($code)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_cdk';

    $cdk = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE code = %s", $code));

    return $cdk;
  }

  /**
   * 更改邀请码状态
   */
  public static function update_cdk($update, $where)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_cdk';

    $update = $wpdb->update(
      $table_name,
      $update,
      $where
    );

    return $update ? true : false;
  }
}

/**
 * 缓存
 */
class Capalot_Cookie
{
  public function __construct()
  {
  }

  // 设置cookie
  public static function set($key, $value, $expire = 0)
  {
    if ($expire == 0) {
      $expire = time() + 3600 * 24 * 30;
    }

    setcookie($key, $value, $expire, '/');
  }

  // 根据key获取cookie中的值
  public static function get($key)
  {
    if (!isset($_COOKIE[$key]))
      return false;

    return $_COOKIE[$key];
  }

  // 删除cookie
  public static function delete($key)
  {
    setcookie($key, '', time() - 3600, '/');
  }
}

class Capalot_Code
{
  public function __construct()
  {
  }

  public static function destr($url)
  {
    return $url;
  }

  public static function enstr($url)
  {
    echo $url;
  }
  public static function encid($url)
  {
    echo $url;
  }
}

/**
 * 下载
 */
class Capalot_Download
{
  public function __construct()
  {
  }

  // 获取用户今天下载数量
  public static function get_today_post_downnum($user_id, $post_id)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_download';

    $today = strtotime(date('Y-m-d', time()));
    $tomorrow = strtotime(date('Y-m-d', strtotime('+1 day')));

    $sql = "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND post_id = %d AND create_time >= %d AND create_time < %d";

    $count = $wpdb->get_var($wpdb->prepare($sql, $user_id, $post_id, $today, $tomorrow));

    return $count;
  }

  // 添加下载记录
  public static function add($item)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_download';

    $insert = $wpdb->insert(
      $table_name,
      [
        'user_id' => $item['user_id'],
        'post_id' => $item['post_id'],
        'create_time' => time(),
        'ip' => $item['ip'],
        'note' => $item['note'],
      ]
    );

    return $insert ? true : false;
  }

  // 根据文件路径下载文件
  public static function local_download_file($file_name)
  {
    if (!file_exists($file_name)) {
      return false;
    }

    $fp = fopen($file_name, "r");
    $file_size = filesize($file_name);

    //下载文件需要用到的头
    Header("Content-type: application/octet-stream");
    Header("Accept-Ranges: bytes");
    Header("Accept-Length:" . $file_size);
    Header("Content-Disposition: attachment; filename=" . $file_name);

    $buffer = 1024;
    $file_count = 0;
    //向浏览器返回数据
    while (!feof($fp) && $file_count < $file_size) {
      $file_con = fread($fp, $buffer);
      $file_count += $buffer;
      echo $file_con;
    }
    fclose($fp);

    return true;
  }

  // 获取用户今日下载量
  public static function get_user_today_download_num($user_id)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_download';

    $today = strtotime(date('Y-m-d', time()));
    $tomorrow = strtotime(date('Y-m-d', strtotime('+1 day')));

    $sql = "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND create_time >= %d AND create_time < %d";

    $count = $wpdb->get_var($wpdb->prepare($sql, $user_id, $today, $tomorrow));

    return $count;
  }
}

/**
 * 工单管理
 */
class Capalot_Ticket
{
  /**
   * 获取工单状态
   */
  public static function get_status($status)
  {
    switch ($status) {
      case '0':
        return _e('待回复', 'ripro');
        break;
      case '1':
        return _e('处理中', 'ripro');
        break;
      case '2':
        return _e('已回复', 'ripro');
        break;
      default:
        return _e('已关闭', 'ripro');
        break;
    }
  }

  /**
   * 获取工单类型
   */
  public static function get_type($type)
  {
    switch ($type) {
      case '1':
        return __('资源问题', 'ripro');
        break;
      case '2':
        return __('会员问题', 'ripro');
        break;
      case '3':
        return __('网站BUG', 'ripro');
        break;
      default:
        return __('其他问题', 'ripro');
        break;
    }
  }

  /**
   * 工单删除
   */
  public static function delete($id)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_ticket';

    $delete = $wpdb->delete(
      $table_name,
      [
        'id' => $id,
      ]
    );

    return $delete ? true : false;
  }

  /**
   * 根据id获取工单
   */
  public static function get($id)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_ticket';

    $ticket = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

    return $ticket;
  }

  /**
   * 工单更新
   */
  public static function update($update, $where)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_ticket';

    $update = $wpdb->update(
      $table_name,
      $update,
      $where
    );

    return $update ? true : false;
  }

  /**
   * 新增工单数据
   */
  public static function add($data)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_ticket';

    $insert = $wpdb->insert(
      $table_name,
      [
        'type' => $data['type'],
        'title' => $data['title'],
        'content' => $data['content'],
        'reply_content' => $data['reply_content'],
        'file' => $data['file'],
        'reply_file' => $data['reply_file'],
        'creator_id' => $data['creator_id'],
        'assignee_id' => $data['assignee_id'],
        'create_time' => time(),
        'updated_time' => time(),
        'reply_time' => time(),
        'status' => $data['status'],
      ]
    );

    return $insert ? true : false;
  }
}
