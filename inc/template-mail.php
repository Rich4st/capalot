<?php

class Capalot_Notification
{
  public static $max_num = 12; //最大缓存动态数量
  public static $expire_time = 5 * 24 * 3600; // 缓存一天
  public static $transient_key = 'capalot_site_notification'; // data key

  public static function get()
  {
    $data = get_transient(self::$transient_key);

    $arr = maybe_unserialize($data); //序列数组

    if ($arr === false || empty($arr) || !is_array($arr)) {
      return array();
    } else {
      // rsort($arr); //序列数组 sort
      return $arr;
    }
  }
}
