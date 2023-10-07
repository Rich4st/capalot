<?php

/**
 * 后台管理
 */
new Capalot_Admin();

class Capalot_Admin
{

  public function __construct()
  {

    add_action('admin_menu', array($this, 'admin_menu'));
    add_filter('manage_users_columns', array($this, 'custom_user_columns'));
    add_action('manage_users_custom_column', array($this, 'output_user_columns'), 10, 3);

    add_filter('manage_posts_columns', array($this, 'manage_posts_columns'));
    add_action('manage_posts_custom_column', array($this, 'manage_posts_custom_column'), 10, 2);
  }

  /**
   * 自定义用户列表页面显示信息
   */
  public function custom_user_columns($columns)
  {

    unset($columns['posts']);
    unset($columns['role']);

    $columns['uid']             = 'UID';
    $columns['vip_type']        = '会员类型';
    $columns['capalot_balance'] = '余额';
    $columns['registered']      = '注册时间';
    $columns['last_login']      = '最近登录';
    $columns['user_status']     = '账号状态';
    $columns['user_bind_ref']   = '推荐人';
    return $columns;
  }

  public function output_user_columns($var, $column_name, $user_id)
  {

    switch ($column_name) {
      case "uid":
        return sprintf('<code>%s</code>', $user_id);
        break;
      case "capalot_balance":
        $balance = (int)get_user_meta($user_id, 'capalot_balance', true);
        return sprintf('<b>%s</b>', $balance);
        break;
      case "vip_type":
        $vip_data = get_user_vip_data($user_id);
        return sprintf('<code class="vip_badge %s">%s</code>', $vip_data['type'], $vip_data['name']);
        break;
      case "registered":
        $user = get_userdata($user_id);
        return sprintf('%s<br><small style="display:block;color: green;">IP：%s</small>', $user->user_registered, $user->user_ip);
        break;
      case "last_login":
        $session = get_user_meta($user_id, 'session_tokens', true);
        if (!empty($session)) {
          $session = end($session);
          return sprintf('%s<br><small style="display:block;color: green;">IP：%s</small>', get_date_from_gmt($session['login']), $session['ip']);
        } else {
          return '';
        }
        break;
      case "user_status":
        $retVal = (empty(get_user_meta($user_id, 'capalot_banned', true))) ? '<span style="color: green;">正常</span>' : '<span style="color: red;">封禁</span>';
        return $retVal;
        break;
        // TODO: 推荐人
    }
  }

  /**
   * 自定义文章列表显示信息
   */
  public function manage_posts_columns($columns)
  {

    $post_type = get_post_type();
    if ($post_type == 'post') {
      return array_merge(
        $columns,
        array(
          'capalot_price' => '售价',
          'capalot_vip_rate' => '会员折扣',
          'pay_info' => '销售数据'
        )
      );
    } else {
      return $columns;
    }
  }

  public function manage_posts_custom_column($column, $post_id)
  {
    switch ($column) {
      case 'capalot_price':
        $meta = get_post_meta($post_id, 'cao_price', true);
        if ($meta == '0') {
          echo sprintf('<b class="vip_badge">%s</b>', '免费');
        } else {
          $meta = ($meta == '') ? '无' : (float) $meta . get_site_coin_name();
          echo sprintf('<b class="vip_badge">%s</b>', $meta);
        }

        break;
      case 'capalot_vip_rate':
        $meta = get_post_meta($post_id, 'capalot_vip_rate', true);
        $meta = ($meta == '' || $meta == '1') ? '无' : ($meta * 10) . '折';
        echo sprintf('<b class="vip_badge">%s</b>', $meta);
        break;
      case 'pay_info':
        global $wpdb;
        $table_name = $wpdb->prefix . 'capalot_order';

        $data = $wpdb->get_row(
          $wpdb->prepare("SELECT COUNT(post_id) as count,SUM(pay_price) as sum_price FROM $table_name WHERE post_id = %d AND pay_status = 1", $post_id)
        );
        echo sprintf('销量：<b>%s</b><small style="display:block;color: green;">销售额：￥%s</small>', $data->count, sprintf('%0.2f', $data->sum_price));
        break;
    }
  }


  /**
   * 添加商城管理菜单
   */
  public function admin_menu()
  {

    $menu_role = 'manage_options';
    $menu_slug = 'capalot-admin';
    $menu_icon = 'dashicons-hammer';
    $menu_title = '商城管理';

    add_menu_page(
      $menu_title,
      $menu_title,
      $menu_role,
      $menu_slug,
      array($this, 'admin_page_index'),
      $menu_icon,
      88
    );

    add_submenu_page(
      $menu_slug,
      $menu_title . '-商城总览',
      '商城总览',
      $menu_role,
      $menu_slug,
      array($this, 'admin_page_index')
    );

    add_submenu_page(
      $menu_slug,
      $menu_title . '-订单管理',
      '订单管理',
      $menu_role,
      $menu_slug . '-order',
      array($this, 'admin_page_order')
    );

    add_submenu_page(
      $menu_slug,
      $menu_title . '-卡券管理',
      '卡券管理',
      $menu_role,
      $menu_slug . '-cdk',
      array($this, 'admin_page_cdk')
    );

    add_submenu_page(
      $menu_slug,
      $menu_title . '-推广中心',
      '推广中心',
      $menu_role,
      $menu_slug . '-affiliate',
      array($this, 'admin_page_aff')
    );

    add_submenu_page(
      $menu_slug,
      $menu_title . '-下载日志',
      '下载日志',
      $menu_role,
      $menu_slug . '-download',
      array($this, 'admin_page_download')
    );

    add_submenu_page(
      $menu_slug,
      $menu_title . '-工单管理',
      '工单管理',
      $menu_role,
      $menu_slug . '-ticket',
      array($this, 'admin_page_ticket')
    );

    add_submenu_page(
      $menu_slug,
      $menu_title . '-后台充值',
      '后台充值',
      $menu_role,
      $menu_slug . '-pay',
      array($this, 'admin_page_pay')
    );

    add_submenu_page(
      $menu_slug,
      $menu_title . '-批量修改',
      '批量修改',
      $menu_role,
      $menu_slug . '-modify',
      array($this, 'admin_page_modify')
    );

    add_submenu_page(
      $menu_slug,
      $menu_title . '-数据清理',
      '数据清理',
      $menu_role,
      $menu_slug . '-clean',
      array($this, 'admin_page_clean')
    );
  }

  /**
   * 加载页面
   * @param string $name 页面名称
   */
  public function load_page($name)
  {
    $pages_dir = get_template_directory() . '/admin/pages';

    if (file_exists($pages_dir . '/' . $name . '.php')) {
      require_once $pages_dir . '/' . $name . '.php';
      return true;
    }

    return false;
  }

  // 商城管理首页
  public function admin_page_index()
  {
    $this->load_page('index');
  }

  //订单管理页面
  public function admin_page_order()
  {
    $this->load_page('order');
  }

  //卡券管理页面
  public function admin_page_cdk()
  {
    $this->load_page('cdk');
  }

  //推广中心页面
  public function admin_page_aff()
  {
    $this->load_page('aff');
  }

  //下载日志页面
  public function admin_page_download()
  {
    $this->load_page('download');
  }

  //工单管理页面
  public function admin_page_ticket()
  {
    $this->load_page('ticket');
  }

  //后台充值页面
  public function admin_page_pay()
  {
    $this->load_page('pay');
  }

  //批量修改页面
  public function admin_page_modify()
  {
    $this->load_page('modify');
  }

  //数据清理页面
  public function admin_page_clean()
  {
    $this->load_page('clean');
  }
}
