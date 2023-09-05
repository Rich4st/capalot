<?php

defined('ABSPATH') || exit;

// CSF框架未加载，退出程序
if (!class_exists('CSF')) {
  return;
}

$prefix = _OPTIONS_PREFIX;
$template_dir = get_template_directory_uri();

// 主题设置选项
CSF::createOptions($prefix, array(
  'menu_title' => '主题设置',
  'menu_slug' => 'capalot',
));

CSF::createSection( $prefix, array(
  'title'  => '基本设置',
  'fields' => array(

    array(
      'id'    => 'site_logo',
      'type'  => 'upload',
      'title' => '网站LOGO',
      'library' => 'image',
      'placeholder' => 'https://',
      'button_title' => '上传',
      'after' => '<p class="cs-text-muted">建议尺寸：200x50</p>',
      'remove_title' => '删除',
    ),

  )
) );

CSF::createSection( $prefix, array(
  'title'  => '安全设置',
  'fields' => array(

    array(
      'id'      => 'site_captcha',
      'type'    => 'switcher',
      'title'   => '验证码',
      'label'   => '开启后注册登录需要填写验证码',
      'default' => true
    ),

  )
) );
