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
  'title'  => 'Tab Title 1',
  'fields' => array(

    //
    // A text field
    array(
      'id'    => 'opt-text',
      'type'  => 'text',
      'title' => 'Simple Text',
    ),

  )
) );
