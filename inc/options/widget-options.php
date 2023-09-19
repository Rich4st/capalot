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
 * 边栏 - 排行榜展示
 */
CSF::createWidget('capalot_sidebar_ranking_widget', array(
  'title' => '【边栏】4.排行榜展示',
  'className' => 'sidebar-ranking-widget',
  'desc' => '排行榜展示',
  'fields'      => array(

    array(
      'id'      => 'title',
      'type'    => 'text',
      'title'   => '标题',
      'default' => '排行榜展示',
    ),

    array(
      'id'      => 'orderby',
      'type'    => 'select',
      'title'   => '排行方式',
      'options' => array(
        'views_num' => '阅读量排行',
        'likes_num' => '点赞量排行',
        'fav_num'   => '收藏量排行',
        'down_num'  => '下载量排行',
        'pay_num'   => '购买量排行',
      ),
      'default' => 'views_num',
    ),

    array(
      'id'      => 'count',
      'type'    => 'text',
      'title'   => '显示数量',
      'default' => 6,
    ),

  ),
));
function capalot_sidebar_ranking_widget($args, $instance)
{

  $instance = array_merge(
    array(
      'orderby' => 'views_num',
      'count' => 6,
    ),
    $instance
  );

  echo $args['before_widget'];

  get_template_part('template-parts/widget/sidebar/ranking', '', $instance);

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

/**
 * 首页 - 幻灯片模块
 */
CSF::createWidget('capalot_home_slider_widget', array(
  'title' => '【首页】4.幻灯片模块',
  'className' => 'home-slider-widget',
  'desc' => '首页幻灯片模块',
  'fields'      => array(

    array(
      'id'      => 'container',
      'type'    => 'radio',
      'title'   => '布局宽度',
      'inline'  => true,
      'options' => array(
        'container-full' => '全宽',
        'container'      => '普通',
      ),
      'default' => 'container-full',
    ),

    array(
      'id'      => 'config',
      'type'    => 'checkbox',
      'title'   => '幻灯片配置',
      'options' => array(
        'autoplay' => '自动播放',
        'loop'     => '循环播放',
        'nav'      => '切换按钮',
        'dots'     => '导航圆点',
      ),
      'inline'  => true,
      'default' => array('autoplay'),
    ),

    array(
      'id'          => 'items',
      'type'        => 'number',
      'title'       => '幻灯片列数',
      'unit'        => '列',
      'output'      => '.heading',
      'output_mode' => 'width',
      'default'     => '1',
    ),

    array(
      'id'     => 'data',
      'type'   => 'group',
      'title'  => '幻灯片内容配置',
      'fields' => array(
        array(
          'id'      => '_img',
          'type'    => 'upload',
          'title'   => '上传幻灯片',
          'default' => get_template_directory_uri() . '/assets/img/slider.jpg',
        ),
        array(
          'id'       => '_desc',
          'type'     => 'textarea',
          'title'    => '描述内容，支持html代码',
          'sanitize' => false,
          'default'  => '<h3 class="text-2xl font-bold">Hello, RiPro Theme</h3><p class="hidden md:block">这是一个简单的内容展示，您可以随意插入HTML代码任意组合显示.</p>',
        ),
        array(
          'id'      => '_href',
          'type'    => 'text',
          'title'   => '链接地址',
          'default' => '',
        ),
        array(
          'id'      => '_target',
          'type'    => 'radio',
          'title'   => '链接打开方式',
          'inline'  => true,
          'options' => array(
            '_self'  => '默认',
            '_blank' => '新窗口打开',
          ),
          'default' => '_self',
        ),

      ),

    ),

  ),
));
function capalot_home_slider_widget($args, $instance)
{

  $instance = array_merge(

    array(
      'container' => 'container-full',
      'config' => array('autoplay'),
      'items' => 1,
      'data' => [],
    ),
    $instance
  );

  echo $args['before_widget'];

  get_template_part('template-parts/widget/home/slider', '', $instance);

  echo $args['after_widget'];
}

/**
 * 首页 - CMS文章模块
 */
CSF::createWidget('capalot_home_cmspost_widget', array(
  'title' => '【首页】5.CMS文章模块',
  'className' => 'home-cmspost-widget',
  'desc' => '首页CMS文章模块',
  'fields'      => array(

    array(
      'id'      => 'title',
      'type'    => 'text',
      'title'   => '标题',
      'default' => 'CMS文章',
    ),

    array(
      'id'      => 'desc',
      'type'    => 'text',
      'title'   => '描述介绍',
      'default' => '当前热门分类文章展示',
    ),

    array(
      'id'      => 'style',
      'type'    => 'select',
      'title'   => 'CMS布局风格',
      'options' => array(
        'list' => '左大图-右列表',
        'grid-overlay' => '左大图-右网格',
      ),
      'default' => 'grid-overlay',
    ),
    array(
      'id'      => 'is_box_right',
      'type'    => 'switcher',
      'title'   => '大图右侧显示',
      'default' => false,
    ),

    array(
      'id'          => 'category',
      'type'        => 'select',
      'title'       => '要展示得分类文章',
      'placeholder' => '选择分类',
      'desc' => '不设置则展示最新文章',
      'options'     => 'categories',
    ),

    array(
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
    ),

  ),
));
function capalot_home_cmspost_widget($args, $instance)
{

  $instance = array_merge(
    array(
      'title' => 'CMS文章',
      'desc' => '当前热门分类文章展示',
      'style' => 'grid-overlay',
      'is_box_right' => false,
      'category' => 0,
      'orderby' => 'date',
    ),
    $instance
  );

  echo $args['before_widget'];

  get_template_part('template-parts/widget/home/cmspost', '', $instance);

  echo $args['after_widget'];
}

/**
 * 首页 - 横条图标模块
 */
CSF::createWidget('capalot_home_division_widget', array(
  'title' => '【首页】6.横条图标模块',
  'className' => 'home-division-widget',
  'desc' => '首页横条图标模块',
  'fields'      => array(

    array(
      'id'          => 'icon_style',
      'type'        => 'radio',
      'inline'      => true,
      'title'       => '图标风格',
      'placeholder' => '',
      'options'     => array(
        'rounded-2'    => '方形',
        'rounded-circle'   => '圆形',
      ),
      'default'     => 'rounded-2',
    ),

    array(
      'id'         => 'div_data',
      'type'       => 'group',
      'title'      => '新建',
      'fields'     => array(
        array(
          'id'      => 'title',
          'type'    => 'text',
          'title'   => '标题文字',
          'default' => '标题文字',
        ),
        array(
          'id'         => 'icon',
          'type'       => 'icon',
          'title'      => '图标',
          'desc'       => '设置站内币图标，部分页面展示需要',
          'default'    => 'fab fa-buffer',
        ),
        array(
          'id'      => 'color',
          'type'    => 'color',
          'title'   => '图标颜色',
          'default' => '#1e73be'
        ),
        array(
          'id'      => 'desc',
          'type'    => 'text',
          'title'   => '描述内容',
          'default' => '这里是描述内容介绍',
        ),
        array(
          'id'      => 'link',
          'type'    => 'text',
          'title'   => '链接',
          'desc'   => '不填写则不启用链接',
          'default' => '',
        ),

      ),
      'default' => array(
        array(
          'title' => '模块化首页',
          'icon'  => 'https://cdn.lordicon.com/jlvsilmg.json',
          'color'  => '#8399ff',
          'desc'  => 'WP原生可视化模块定制',
          'link'  => '',
        ),
        array(
          'title' => '商城支持',
          'icon'  => 'https://cdn.lordicon.com/lpddubrl.json',
          'color'  => '#FF9800',
          'desc'  => '付费下载、查看、音视频播放',
          'link'  => '',
        ),
        array(
          'title' => '多级菜单',
          'icon'  => 'https://cdn.lordicon.com/tsnvgrkp.json',
          'color'  => '#4c4c4c',
          'desc'  => '自定义菜单图标，三级菜单',
          'link'  => '',
        ),
        array(
          'title' => '会员系统',
          'icon'  => 'https://cdn.lordicon.com/ljvjsnvh.json',
          'color'  => '#ff75a4',
          'desc'  => '内置VIP和用户中心系统',
          'link'  => '',
        ),
      ),
    ),

  ),
));
function capalot_home_division_widget($args, $instance)
{

  $instance = array_merge(array(
    'div_data' => array(),
    'icon_style' => 'cube',
  ), $instance);

  echo $args['before_widget'];

  get_template_part('template-parts/widget/home/division', '', $instance);

  echo $args['after_widget'];
}

/**
 * 首页 - 图片背景按钮
 */
CSF::createWidget('capalot_home_bg_btn_widget', array(
  'title' => '【首页】7.图片背景按钮',
  'className' => 'home-bg-btn-widget',
  'desc' => '首页图片背景按钮',
  'fields'      => array(


    array(
      'id'      => 'title',
      'type'    => 'text',
      'title'   => '模块主标题',
      'default' => '这是图片背景主标题',
    ),
    array(
      'id'      => 'desc',
      'type'    => 'text',
      'title'   => '模块介绍文字',
      'default' => '这里是模块介绍文字，不填写则不显示，并可以添加不同颜色按钮',
    ),

    array(
      'id'      => 'bg_img',
      'type'    => 'upload',
      'title'   => '背景图片',
      'default' => get_template_directory_uri() . '/assets/img/bg.jpg',
    ),

    array(
      'id'          => 'bg_style',
      'type'        => 'radio',
      'inline'      => true,
      'title'       => '背景图片风格',
      'placeholder' => '',
      'options'     => array(
        'bg-fixed'    => '固定',
        'bg-scroll'   => '跟随',
      ),
      'default'     => 'bg-fixed',
    ),

    array(
      'id'         => 'btn_data',
      'type'       => 'group',
      'title'      => '新建',
      'fields'     => array(
        array(
          'id'      => 'title',
          'type'    => 'text',
          'title'   => '按钮名称',
          'default' => '标题文字',
        ),
        array(
          'id'         => 'icon',
          'type'       => 'icon',
          'title'      => '按钮图标',
          'desc'       => '设置站内币图标，部分页面展示需要',
          'default'    => 'fab fa-buffer',
        ),
        array(
          'id'      => 'link',
          'type'    => 'text',
          'title'   => '链接',
          'desc'   => '不填写则不启用链接',
          'default' => '',
        ),
        array(
          'id'          => 'color',
          'type'        => 'radio',
          'inline'      => true,
          'title'       => '按钮颜色',
          'placeholder' => '',
          'options'     => array(
            'primary' => 'primary',
            'secondary' => 'secondary',
            'success' => 'success',
            'info' => 'info',
            'error' => 'error',
          ),
          'default'     => 'primary',
        ),

      ),
      'default' => array(
        array(
          'title' => '按钮名称1',
          'icon'  => 'fab fa-buffer',
          'color'  => 'info',
          'link'  => '#',
        ),
        array(
          'title' => '按钮名称2',
          'icon'  => 'fab fa-buffer',
          'color'  => 'success',
          'link'  => '#',
        ),
      ),
    ),

  ),
));
function capalot_home_bg_btn_widget($args, $instance)
{

  $instance = array_merge(array(
    'title' => '这是图片背景主标题',
    'desc' => '这里是模块介绍文字，不填写则不显示，并可以添加不同颜色按钮',
    'bg_img' => get_template_directory_uri() . '/assets/img/bg2.jpg',
    'bg_style' => '',
    'btn_data' => array(),
  ), $instance);

  echo $args['before_widget'];

  get_template_part('template-parts/widget/home/bg-btn', '', $instance);

  echo $args['after_widget'];
}

/**
 * 首页 - 网站动态展示
 */
CSF::createWidget('capalot_home_notification_widget', array(
  'title' => '【首页】8.网站动态展示',
  'className' => 'home-notification-widget',
  'desc' => '首页网站动态展示',
  'fields'      => array(

    array(
      'id'    => 'title',
      'type'  => 'Text',
      'title' => '标题',
      'default' => '网站动态',
    ),

    array(
      'id'      => 'bg_color',
      'type'    => 'select',
      'title'   => '背景颜色',
      'options' => array(
        'primary'   => '蓝色',
        'success'   => '绿色',
        'danger'    => '红色',
        'warning'   => '黄色',
        'secondary' => '灰色',
        'dark'      => '黑色',
      ),
      'default' => 'primary',
    ),

    array(
      'id'      => 'is_autoplay',
      'type'    => 'switcher',
      'title'   => '自动播放',
      'default' => true,
    ),

  ),
));
function capalot_home_notification_widget($args, $instance)
{

  $instance = array_merge(array(
    'title' => '网站动态',
    'bg_color' => 'primary',
    'is_autoplay' => true,
  ), $instance);

  echo $args['before_widget'];

  get_template_part('template-parts/widget/home/notification', '', $instance);

  echo $args['after_widget'];
}
