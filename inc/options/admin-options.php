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

CSF::createSection($prefix, array(
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
));

CSF::createSection($prefix, array(
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
));


CSF::createSection($prefix, array(
  'title'  => '网站优化',
  'fields' => array(

    array(
      'id'      => 'gutenberg_disable',
      'type'    => 'switcher',
      'title'   => '古藤堡小工具',
      'default' => false
    ),

  )
));

/**
 * 商城设置
 */
CSF::createSection($prefix, array(
  'id'     => 'shop_options',
  'title'  => '商城设置',
));

CSF::createSection($prefix, array(
  'parent' => 'shop_options',
  'title'  => '基本设置',
  'fields' => array(

    array(
      'id' => 'site_shop_mode',
      'type' => 'radio',
      'title' => '商城模式',
      'options'     => array(
        'close'    => '不启用商城功能（网站仅作为博客展示）',
        'all'      => '全能商城（支持游客购买、登录用户购买）',
        'user_mod' => '用户模式（不支持游客购买）',
      ),
      'default'     => 'all',
    ),

    array(
      'id' => 'site_currency_name',
      'type' => 'text',
      'title' => '站内币名称',
      'desc' => '设置站内币名称,例如: 金币、下载币、积分、资源币、BB币、USDT等',
      'default' => '金币',
      'attributes' => array(
        'style' => 'width: 6rem'
      )
    ),

  )
));

CSF::createSection($prefix, array(
  'parent' => 'shop_options',
  'title' => '默认发布字段',
  'fields' => array(

    array(
      'type' => 'heading',
      'content' => '自定义发布文章时的价格等默认字段，可以配置好默认字段，比如你不想每次都填写价格，可以配置默认为多少',
    ),

    array(
      'id' => 'site_default_price',
      'type' => 'number',
      'title' => '默认价格',
      'desc' => '设置默认价格,免费请填写0',
      'output' => '.heading',
      'output_mode' => 'width',
      'default' => 0.1,
    ),

    array(
      'id'          => 'site_default_sold_quantity',
      'type'        => 'number',
      'title'       => '已售数量',
      'desc'        => '可自定义修改数字',
      'unit'        => '个',
      'output'      => '.heading',
      'output_mode' => 'width',
      'default'     => 0,
    ),

  )
));
