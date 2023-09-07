<?php
defined('ABSPATH') || exit;

if (!class_exists('CSF')) {
  return;
}


$prefix = _OPTIONS_PREFIX . '-post';

CSF::createMetabox($prefix, array(
  'title'     => '文章高级配置(capalot)',
  'nav'       => 'inline',
  'post_type' => array('post'),
  'data_type' => 'unserialize',
  'context'   => 'normal', //`normal`, `side`, `advanced`
));

if (_capalot('site_shop_mode', '') !== 'close') {

  CSF::createSection($prefix, array(
    'title'  => '文章价格设置',
    'fields' => array(

      array(
        'id'          => 'capalot_price',
        'type'        => 'number',
        'title'       => '价格：*',
        'desc'        => '免费请填写：0',
        'unit'        => _capalot('site_currency_name', '金币'),
        'output'      => '.heading',
        'output_mode' => 'width',
        'default'     => 0,
      ),

      array(
        'id'          => 'capalot_vip_rate',
        'type'        => 'number',
        'title'       => '折扣:',
        'desc'        => '0.N 等于N折;1 等于不打折;0 等于会员免费',
        'unit'        => '.N折',
        'output'      => '.heading',
        'output_mode' => 'width',
        'default'     => 0,
      ),

      array(
        'id'          => 'capalot_sold_quantity',
        'type'        => 'number',
        'title'       => '已售数量',
        'desc'        => '可自定义修改数字',
        'unit'        => '个',
        'output'      => '.heading',
        'output_mode' => 'width',
        'default'     => _capalot('site_default_sold_quantity', 0)
      ),

    ),
  ));

}


CSF::createSection($prefix, array(
  'title'  => '自定义SEO信息',
  'fields' => array(
    array(
      'id'       => 'post_title',
      'type'     => 'text',
      'title'    => '自定义SEO标题',
      'subtitle' => '留空则不设置',
    ),

    array(
      'id'       => 'post_description',
      'type'     => 'textarea',
      'title'    => '自定义SEO描述',
      'subtitle' => '字数控制到80-180最佳,留空则不设置',
    ),

    array(
      'id'       => 'post_keywords',
      'type'     => 'text',
      'title'    => '自定义SEO关键词',
      'subtitle' => '关键词用英文逗号,隔开,留空则不设置',
    ),

  ),
));
