<?php

/**
 * @description: 数据库表初始化
 */
class SetupDB
{

  private $db_prefix;
  private $db_tables;

  public function __construct()
  {
    $this->db_prefix = 'capalot_';
    $this->db_tables = array(
      'order', //订单表
      'cdk', //优惠码表
      'download', //下载记录表
      'aff', //佣金记录表
      'ticket', //工单记录表
    );

    $this->define_tables();
  }

  /**
   * @description: 定义数据库表
   */
  private function define_tables()
  {
    global $wpdb;
    $_db_prefix = $this->db_prefix;
    $_db_tables = $this->db_tables;

    foreach ($_db_tables as $name) {
      $table_name = $wpdb->prefix . $_db_prefix . $name;
      $backward_key = $_db_prefix . $name;
      $wpdb->{$backward_key} = $table_name;
    }
  }

  /**
   * @description: 安装数据库表
   */
  public function create_db()
  {

    global $wpdb;

    $collate = '';
    if ($wpdb->has_cap('collation')) {
      if (!empty($wpdb->charset)) {
        $collate .= 'DEFAULT CHARACTER SET ' . $wpdb->charset;
      }

      if (!empty($wpdb->collate)) {
        $collate .= ' COLLATE ' . $wpdb->collate;
      }
    }

    // 订单表
    $execute = $wpdb->query(
      "
      CREATE TABLE IF NOT EXISTS $wpdb->capalot_order(
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        post_id bigint(20) unsigned NOT NULL,
        user_id bigint(20) unsigned NOT NULL,
        order_type int(3) NOT NULL DEFAULT 0,
        order_trade_no varchar(50) DEFAULT NULL,
        order_price double(10,2) DEFAULT NULL,
        create_time int(11) DEFAULT 0,
        pay_type tinyint(3) DEFAULT 0,
        pay_time int(11) DEFAULT 0,
        pay_price double(10,2) DEFAULT NULL,
        pay_trade_no varchar(50) DEFAULT NULL,
        order_info longtext,
        pay_status tinyint(3) DEFAULT 0,
        PRIMARY KEY (id),
        KEY order_trade_no (order_trade_no)
        ) $collate
      "
    );

    //优惠码表
    $execute = $wpdb->query(
      "
      CREATE TABLE IF NOT EXISTS $wpdb->capalot_cdk(
          id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          order_id bigint(20) unsigned NOT NULL,
          type tinyint(3) NOT NULL DEFAULT 0,
          amount double(10,2) NOT NULL DEFAULT 0,
          create_time int(11) DEFAULT 0,
          expiry_time int(11) DEFAULT 0,
          code varchar(50) DEFAULT NULL,
          info longtext,
          status tinyint(3) NOT NULL DEFAULT 0,
          PRIMARY KEY (id),
          KEY code (code)
      ) $collate
      "
    );

    //下载记录表
    $execute = $wpdb->query(
      "
      CREATE TABLE IF NOT EXISTS $wpdb->capalot_download(
          id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          user_id bigint(20) unsigned NOT NULL,
          post_id bigint(20) unsigned NOT NULL,
          create_time int(11) DEFAULT 0,
          ip varchar(255) DEFAULT NULL,
          note varchar(255) DEFAULT NULL,
          PRIMARY KEY (id)
      ) $collate
      "
    );

    //推广记录表
    $execute = $wpdb->query(
      "
      CREATE TABLE IF NOT EXISTS $wpdb->capalot_aff(
          id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          order_id bigint(20) unsigned NOT NULL,
          aff_uid bigint(20) unsigned NOT NULL,
          aff_rate double(10,2) NOT NULL DEFAULT 0,
          create_time int(11) DEFAULT 0,
          apply_time int(11) DEFAULT 0,
          comple_time int(11) DEFAULT 0,
          note varchar(255) DEFAULT NULL,
          status tinyint(3) NOT NULL DEFAULT 0,
          PRIMARY KEY (id)
      ) $collate
      "
  );

    //工单表
    $execute = $wpdb->query(
      "
      CREATE TABLE IF NOT EXISTS $wpdb->capalot_ticket(
          id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          type tinyint(3) NOT NULL DEFAULT 0 COMMENT '工单类型',
          title varchar(255) NOT NULL COMMENT '工单标题',
          content text DEFAULT NULL COMMENT '工单内容',
          reply_content text DEFAULT NULL COMMENT '工单回复内容',
          file varchar(255) DEFAULT NULL COMMENT '附件',
          reply_file varchar(255) DEFAULT NULL COMMENT '回复附件',
          creator_id int(11) unsigned NOT NULL COMMENT '工单创建者ID',
          assignee_id int(11) unsigned DEFAULT NULL COMMENT '工单处理人员ID',
          create_time int(11) DEFAULT 0 COMMENT '修改时间',
          updated_time int(11) DEFAULT 0 COMMENT '最近更新时间',
          reply_time int(11) DEFAULT 0 COMMENT '回复时间',
          status tinyint(3) NOT NULL DEFAULT 0 COMMENT '工单状态，0：新建，1：处理中，2：已解决，3：已关闭',
          PRIMARY KEY (id)
      ) $collate
      "
    );

    if ($wpdb->last_error) {
      throw new Exception($wpdb->last_error);
  }

    return $execute;
  }
}
