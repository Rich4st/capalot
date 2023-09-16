<?php

defined('ABSPATH') || exit;

/**
 * 边栏 - 资源信息购买组件
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
 * 边栏 - 作者信息展示
 */
CSF::createWidget('capalot_sidebar_author_widget', array(
  'title' => '【边栏】2.作者信息展示',
  'className' => 'author-widget',
  'desc' => '文章作者信息展示',
  'fields' => array(

    array(
      'id' => 'title',
      'type' => 'text',
      'title' => '标题',
      'default' => '作者信息',
    ),

  )
));
function capalot_sidebar_author_widget($args, $instance)
{

  $instance = array_merge(
    array(
      'is_avatar' => true,
      'is_name' => true,
      'is_desc' => true,
      'is_link' => true,
      'is_social' => true,
    ),
    $instance
  );

  echo $args['before_widget'];

  get_template_part('template-parts/widget/sidebar/author', '', $instance);

  echo $args['after_widget'];
}

/**
 * 边栏 - 文章展示
 */
CSF::createWidget('capalot_sidebar_posts_widget', array(
  'title' => '【边栏】3.文章展示',
  'className' => 'sidebar-posts-widget',
  'desc' => '文章展示',
  'fields'      => array(

    array(
      'id'      => 'title',
      'type'    => 'text',
      'title'   => '标题',
      'default' => '文章展示',
    ),

    array(
      'id'          => 'category',
      'type'        => 'select',
      'title'       => '要展示得分类文章',
      'placeholder' => '选择分类',
      'options'     => 'categories',
    ),

    array(
      'id'      => 'orderby',
      'type'    => 'radio',
      'title'   => '排序方式',
      'inline'  => true,
      'options' => array(
        'date'     => '日期',
        'rand'     => '随机',
        'modified' => '最近编辑时间',
        'title'    => '标题',
        'ID'       => '文章ID',
      ),
      'default' => 'date',
    ),

    array(
      'id'      => 'count',
      'type'    => 'text',
      'title'   => '显示数量',
      'default' => 6,
    ),

  ),
));
function capalot_sidebar_posts_widget($args, $instance)
{

  $instance = array_merge(
    array(
      'category' => 0,
      'orderby' => 'date',
      'count' => 6,
    ),
    $instance
  );

  echo $args['before_widget'];

  get_template_part('template-parts/widget/sidebar/posts', '', $instance);

  echo $args['after_widget'];
}

/**
 * 首页 - 最新文章组件
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
      'id'          => 'no_cat',
      'type'        => 'checkbox',
      'inline'      => true,
      'title'       => '要排除的分类',
      'placeholder' => '选择要排除的分类',
      'options'     => 'categories',
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

  get_template_part('template-parts/widget/home/latest-posts', '', $instance);

  echo $args['after_widget'];
}

/**
 * 首页 - 分类文章模块
 */
CSF::createWidget('capalot_home_category_posts_widget', array(
  'title' => '【首页】2.分类文章模块',
  'className' => 'home-category-posts-widget',
  'desc' => '首页按照分类展示文章',
  'fields' => array(

    [
      'id' => 'category',
      'type' => 'select',
      'title' => '要展示文章的分类',
      'placeholder' => '选择分类',
      'options' => 'categories',
      'default' => 'all',
    ],

    [
      'id'      => 'orderby',
      'type'    => 'radio',
      'title'   => '排序方式',
      'inline'  => true,
      'options' => array(
        'date'          => '日期',
        'rand'          => '随机',
        'comment_count' => '评论数',
        'views'         => '阅读量',
        'modified'      => '最近编辑时间',
        'title'         => '标题',
        'ID'            => '文章ID',
      ),
      'default' => 'date',
    ],

    [
      'id'      => 'count',
      'type'    => 'text',
      'title'   => '显示数量',
      'default' => 8,
    ],

  )
));
function capalot_home_category_posts_widget($args, $instance)
{

  $instance = array_merge(
    [
      'category' => 0,
      'orderby' => 'date',
      'count' => 8,
    ],
    $instance
  );

  echo $args['before_widget'];

  get_template_part('template-parts/widget/home/category-posts', '', $instance);

  echo $args['after_widget'];
}

/**
 * 首页 - 分类BOX模块
 */
CSF::createWidget('capalot_home_category_box_widget', array(
  'title' => '【首页】3.分类BOX模块',
  'className' => 'home-category-box-widget',
  'desc' => '首页分类BOX模块',
  'fields' => [

    [
      'id'          => 'category',
      'type'        => 'select',
      'title'       => '要展示的分类',
      'desc'        => '按顺序选择可以排序',
      'placeholder' => '选择分类',
      'inline'      => true,
      'chosen'      => true,
      'multiple'    => true,
      'options'     => 'categories',
    ],
    [
      'id'      => 'is_num',
      'type'    => 'checkbox',
      'title'   => '显示文章数量',
      'default' => true,
    ]

  ]
));
function capalot_home_category_box_widget($args, $instance)
{

  $instance = array_merge(
    [
      'category' => [],
      'is_num' => true,
    ],
    $instance
  );

  echo $args['before_widget'];

  get_template_part('template-parts/widget/home/category-box', '', $instance);

  echo $args['after_widget'];
}
