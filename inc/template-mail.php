<?php

/**
 * 网站动态
 */
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

  public static function add($param = [])
  {
    $arr = self::get();

    $data = array_merge(array(
      'info' => '',
      'uid' => '',
      'href' => '',
      'time' => time(),
    ), $param);

    array_push($arr, $data);

    array_multisort(array_column($arr, 'time'), SORT_DESC, $arr);

    if (count($arr) > self::$max_num) {
      array_pop($arr);
    }

    $data_arr = maybe_serialize($arr); //格式化数据

    return set_transient(self::$transient_key, $data_arr, self::$expire_time);
  }
}

new Capalot_Mail();

class Capalot_Mail
{

  public function __construct()
  {
    //埋点钩子
    add_action('capalot_send_mail_msg', function ($param = array()) {
      $this->send_msg_mail($param);
    }, 10, 1);

    add_action('phpmailer_init', array($this, 'smtp_mail'));
    add_filter('wp_mail', array($this, 'mail_templates'), 10, 1);
  }

  public function smtp_mail($phpmailer)
  {
    $phpmailer->FromName   = _capalot('smtp_mail_nickname'); // 发件人昵称
    $phpmailer->Host       = _capalot('smtp_mail_host'); // 邮箱SMTP服务器
    $phpmailer->Port       = (int) _capalot('smtp_mail_port'); // SMTP端口，不需要改
    $phpmailer->Username   = _capalot('smtp_mail_name'); // 邮箱账户
    $phpmailer->Password   = _capalot('smtp_mail_passwd'); // 此处填写邮箱生成的授权码，不是邮箱登录密码
    $phpmailer->From       = _capalot('smtp_mail_name'); // 邮箱账户同上
    $phpmailer->SMTPAuth   = !empty(_capalot('smtp_mail_smtpauth'));
    $phpmailer->SMTPSecure = _capalot('smtp_mail_smtpsecure'); // 端口25时 留空，465时 ssl，不需要改
    $phpmailer->IsSMTP();
  }

  public function send_msg_mail($param = array())
  {
    if (empty($param)) {
      return false;
    }
    return wp_mail(
      $param['email'],
      $param['title'],
      $param['msg'],
      array('Content-Type: text/html; charset=UTF-8')
    );
  }


  public function mail_templates($mail)
  {
    return $mail;
  }
}
