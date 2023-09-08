<?php

defined('ABSPATH') || exit;

/**
 * 资源信息购买组件
 *
 * @param string widget回调函数
 * @param array widget参数
 */
CSF::createWidget('capalot_sidebar_post_pay_widget', array(
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
function capalot_sidebar_post_pay_widget($args, $instance)
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

  get_template_part('template-parts/widget/sidebar/post-pay', '', $instance);

  echo $args['after_widget'];
}

/**
 * 最新文章组件
 */
CSF::createWidget('capalot_home_latest_posts_widget', array(
  'title' => '【首页】1.最新文章组件',
  'className' => 'home-latest-posts-widget',
  'desc' => '首页最新文章组件',
  'fields' => array(

    array(
      'id' => 'title',
      'type' => 'text',
      'title' => '标题',
      'default' => '最新文章',
    ),

    array(
      'id' => 'desc',
      'type' => 'text',
      'title' => '描述介绍',
      'default' => '当前最新发布更新的热门资源，我们将会持续保持更新',
    ),

    array(
      'id' => 'is_pagination',
      'type' => 'switcher',
      'title' => '是否开启分页',
      'default' => true,
    ),

    array(
      'type'    => 'subheading',
      'content' => '文章数请在 WP后台->设置->阅读->博客页面至多显示 调整',
    ),

  )
));

function capalot_home_latest_posts_widget($args, $instance)
{

  $instance = array_merge(
    array(
      'title' => '最新推荐',
      'desc' => '当前最新发布更新的热门资源，我们将会持续保持更新'
    ),
    $instance
  );

  echo $args['before_widget'];

  get_template_part('template-parts/widget/home/latest_posts', '', $instance);

  echo $args['after_widget'];
}
