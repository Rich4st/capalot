<?php

defined('ABSPATH') || exit;

if (!class_exists('CSF')) {
  exit;
}

$prefix = _OPTIONS_PREFIX . '-shortcode';

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

function _capalot_login_hide_shortcode($atts, $content = '') {
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

function _capalot_reply_hide_shortcode($attrs, $content = '')
{

  ob_start();
  get_template_part('template-parts/shortcode/capalot-reply-hide', '', $content);
  $html = ob_get_clean();

  return do_shortcode($html);
}
add_shortcode('capalot-reply-hide', '_capalot_reply_hide_shortcode');
