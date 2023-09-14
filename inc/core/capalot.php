<?php

new Capalot_Shop();

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

    if ($order == null) {
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

    return true;
  }

  /**
   * 支付回调
   * @param string $order_trade_no 订单交易号
   * @param string $trade_no 交易号
   */
  public static function pay_notify_callback($order_trade_no, $trade_no)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_order';

    $time = time('mysql');

    $update = $wpdb->update(
      $table_name,
      [
        'pay_status' => 1,
        'pay_order_no' => $order_trade_no,
        'pay_trade_no' => $trade_no,
        'pay_time' => $time,
      ]
    );

    return $update ? true : false;
  }
}

class Capalot_Pay
{
}
