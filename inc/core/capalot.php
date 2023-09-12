<?php

new Capalot_Shop();

class Capalot_Shop
{

  public function __construct()
  {
  }

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
}
