<?php

defined('ABSPATH') || exit;

if (!class_exists('CSF')) {
  exit;
}



$prefix = _OPTIONS_PREFIX . '-shortcode';

/**
 * 付费查看内容组件
 */
CSF::createShortcoder($prefix . '-pay', array(
  'button_title'   => '添加付费隐藏内容',
  'select_title'   => '选择一个简码组件',
  'insert_title'   => '插入到文章',
  'show_in_editor' => true,
  'gutenberg'      => array(
    'title'       => '添加隐藏内容',
    'description' => '添加隐藏内容',
    'icon'        => 'screenoptions',
    'category'    => 'widgets',
    'keywords'    => array('shortcode', 'capalot', 'insert', 'hide'),
    'placeholder' => '在此处编写简码...',
  ),
));
CSF::createSection($prefix . '-pay', array(
  'title'     => '付费可见内容[capalot-hide]',
  'view'      => 'normal',
  'shortcode' => 'capalot-hide',
  'fields'    => array(
    array(
      'id'    => 'content',
      'type'  => 'wp_editor',
      'title' => '',
      'desc'  => '[capalot-hide]隐藏部分付费内容[/capalot-hide]查看价格和权限设置等和付费下载相同',
    ),

  ),
));
function _capalot_hide($atts, $content = 'foo')
{
  if (!is_site_shop())
    return false;

  // 加载并缓存模板内容
  ob_start();
  get_template_part('template-parts/shortcode/capalot-hide', '', $content);
  $html = ob_get_clean();
  return do_shortcode($html);
}
add_shortcode('capalot-hide', '_capalot_hide');

/**
 * 其他内容组件
 */
CSF::createShortcoder($prefix, array(
  'button_title' => '添加内容组件',
  'select_title' => '选择一个简码组件',
  'insert_title' => '插入到文章',
  'show_in_editor' => true,
  'gutenberg' => array(
    'title' => '添加内容组件',
    'description' => '添加内容组件',
    'icon' => 'screenoptions',
    'category' => 'widgets',
    'keywords' => array('shortcode', 'capalot', 'insert', 'hide'),
    'placeholder' => '在此处编写简码...',
  ),
));


CSF::createSection($prefix, array(
  'title'     => '登录可见内容[capalot-login-hide]',
  'view'      => 'normal',
  'shortcode' => 'capalot-login-hide',
  'fields'    => array(
    array(
      'id'    => 'content',
      'type'  => 'wp_editor',
      'title' => '',
      'desc'  => '[capalot-login-hide]隐藏部分登录后可见内容[/capalot-login-hide]',
    ),

  ),
));

function _capalot_login_hide_shortcode($atts, $content = '')
{
  ob_start();
  get_template_part('template-parts/shortcode/capalot-login-hide', '', $content);
  $html = ob_get_clean();

  return do_shortcode($html);
}
add_shortcode('capalot-login-hide', '_capalot_login_hide_shortcode');

CSF::createSection($prefix, array(
  'title' => '评论可见内容[capalot-reply-hide]',
  'view' => 'normal',
  'shortcode' => 'capalot-reply-hide',
  'fields' => array(
    array(
      'id' => 'content',
      'type' => 'wp_editor',
      'title' => '',
      'desc' => '[capalot-reply-hide]隐藏部分内容[/capalot-reply-hide]查看价格和权限设置等和付费下载相同',
    ),
  ),
));

function _capalot_reply_hide_shortcode($atts, $content = '')
{

  ob_start();
  get_template_part('template-parts/shortcode/capalot-reply-hide', '', $content);
  $html = ob_get_clean();

  return do_shortcode($html);
}
add_shortcode('capalot-reply-hide', '_capalot_reply_hide_shortcode');

/**
 * m3u8视频组件
 */
CSF::createSection($prefix, array(
  'title' => 'm3u8视频组件[capalot-m3u8]',
  'view' => 'normal',
  'shortcode' => 'capalot-m3u8',
  'fields' => array(
    array(
      'id' => 'content',
      'type' => 'text',
      'title' => '',
      'desc' => '请输入视频地址',
    ),
  ),
));
function capalot_m3u8_shortcode($atts, $content = '')
{
  ob_start();
  get_template_part('template-parts/shortcode/capalot-m3u8', '', $content);
  $html = ob_get_clean();

  return do_shortcode($html);
}
add_shortcode('capalot-m3u8', 'capalot_m3u8_shortcode');
