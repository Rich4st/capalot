<?php

// 定义prefix
if (!defined('_OPTIONS_PREFIX'))
  define('_OPTIONS_PREFIX', '_capalot_options');


if (!function_exists('_capalot')) {
  /**
   * 自定义函数获取设置
   * 
   * @param string $option 设置项
   * @param string $default 默认值
   * @return string
   */
  function _capalot($option = '', $default = null)
  {
    $options_meta = _OPTIONS_PREFIX;
    $options      = get_option($options_meta);
    return (isset($options[$option])) ? $options[$option] : $default;
  }
}


if (!class_exists('CSF')) {

  $options = array(
    '/plugins/codestar-framework/codestar-framework.php', //框架CSF
    '/options/admin-options.php', //后台设置
  );

  foreach ($options as $o) {
    require_once get_template_directory() . '/inc' . $o;
  }
}

/**
 * 初始化主题设置
 * 
 * @param array $params {
 *  @type string $framework_title 框架标题
 *  @type string $menu_title 菜单标题
 *  @type string $theme 主题
 *  @type bool $show_bar_menu 是否显示顶部菜单
 *  @type bool $enqueue_webfont 是否加载字体
 *  @type bool $enqueue 是否加载css和js
 *  @type bool $show_search 是否显示搜索框
 *  @type bool $ajax_save 是否开启ajax保存
 *  @type string $footer_credit 页脚版权
 * }
 * @return array
 */
function theme_options_init($params)
{
  $params['framework_title'] = 'Capalot主题设置 <small>正式版 V' . '0.1.0' . '</small>';
  $params['menu_title']      = '主题设置';
  $params['theme']           = 'light';
  $params['show_bar_menu']   = false;
  $params['enqueue_webfont'] = false;
  $params['enqueue']         = false;
  $params['show_search']     = true;
  $params['ajax_save']       = false;
  $params['footer_credit']   = '';
  $params['footer_text']     = '感谢您使用Capalot主题进行创作运营';
  return $params;
}

add_filter('csf_' . _OPTIONS_PREFIX . '_args', 'theme_options_init');
