<?php

defined('ABSPATH') || exit;

/**
 * 资源信息购买组件
 *
 * @param string widget回调函数
 * @param array widget参数
 */
CSF::createWidget('capalot_post_pay_widget', array(
  'title' => '【边栏】1.资源购买信息组件',
  'className' => 'post-pay-widget',
  'desc' => '付费文章必备侧边栏下载按钮',
  'fields' => array(

    array(
      'id' => 'title',
      'type' => 'text',
      'title' => '标题',
      'default' => '资源购买',
    ),

  )
));

/**
 * 资源信息购买组件回调函数
 * @param array widget参数
 * @param array widget实例
 */
function capalot_post_pay_widget($args, $instance)
{

  $instance = array_merge(
    array(
      'is_download_count' => true,
      'is_modified_date' => true,
      'is_sales_count' => true,
      'resize_position' => 'bottom',
      'footer_text' => '下载遇到问题？可联系客服或反馈'
    ),
    $instance
  );

  echo $args['before_widget'];

  get_template_part('template-parts/widget/post-pay', '', $instance);

  echo $args['after_widget'];
}
