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
}

class Capalot_Pay
{
}
