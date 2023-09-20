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
  public static function pay_notify_callback($order_data)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_order';

    // 更新$order_trade_no对应的订单状态
    $update = $wpdb->update(
      $table_name,
      [
        'pay_time' => time(),
        'pay_price' => $order_data['pay_price'],
        'pay_trade_no' => '999-' . time(),
        'order_info' => $order_data['order_info'],
        'pay_status' => 1,
      ],
      [
        'order_trade_no' => $order_data['order_trade_no'],
      ]
    );

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
}

/**
 * 推广
 */
class Capalot_Aff
{
  public function __construct()
  {
  }

  /**
   * 获取推广状态
   */
  public static function get_aff_status($param)
  {
    echo $param . '11111111';
  }

  /**
   * 更新推广记录
   */
  public static function update_aff_log($data, $where, $data_format, $where_format)
  {
    global $wpdb;

    // 检查 $wpdb 是否已经初始化，如果没有，可以根据您的需求进行初始化

    // 确保 $data 和 $where 数组的键值对数量匹配
    if (count($data) !== count($data_format) || count($where) !== count($where_format)) {
      return false; // 键值对数量不匹配，操作失败
    }

    // 构建 SQL 更新语句
    $table_name = $wpdb->prefix . 'aff'; // 表名
    $sql = "UPDATE $table_name SET ";

    // 构建 SET 子句
    $set_clause = array();
    foreach ($data as $key => $value) {
      $set_clause[] = "$key = %{$data_format[$key]}";
    }
    $sql .= implode(', ', $set_clause);

    // 构建 WHERE 子句
    $where_clause = array();
    foreach ($where as $key => $value) {
      $where_clause[] = "$key = %{$where_format[$key]}";
    }
    $sql .= " WHERE " . implode(' AND ', $where_clause);

    // 执行 SQL 更新语句
    $result = $wpdb->query($wpdb->prepare($sql, $data, $data_format));

    if ($result === false) {
      return false; // 更新失败
    } else {
      return true; // 更新成功
    }
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
}

/**
 * 缓存
 */
class Capalot_Cookie
{
  public function __construct()
  {
  }

  public static function set($key, $value, $expire = 0)
  {
    if ($expire == 0) {
      $expire = time() + 3600 * 24 * 30;
    }

    setcookie($key, $value, $expire, '/');
  }

  public static function get($key)
  {
    if (!isset($_COOKIE[$key]))
      return false;

    return $_COOKIE[$key];
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
}
