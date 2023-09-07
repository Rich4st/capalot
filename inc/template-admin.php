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
  }

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

}
