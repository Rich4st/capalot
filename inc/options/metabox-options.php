<?php
defined('ABSPATH') || exit;

if (!class_exists('CSF')) {
  return;
}

$prefix = _OPTIONS_PREFIX . '-post';

/**
 * 文章高级设置
 */
CSF::createMetabox($prefix, array(
  'title'     => '文章高级配置(capalot)',
  'nav'       => 'inline',
  'post_type' => array('post'),
  'data_type' => 'unserialize',
  'context'   => 'normal', //`normal`, `side`, `advanced`
));

if (_capalot('site_shop_mode', 'all') !== 'close') {

  $__vip_options = _capalot('site_vip_options');

  if (empty($__vip_options)) {
    $__vip_options = array('no_name' => '普通用户', 'vip_name' => '包月VIP', 'boosvip_name' => '永久VIP');
  }

  // 文章高级设置 - 文章价格设置
  CSF::createSection($prefix, array(
    'title'  => '文章价格设置',
    'fields' => array(

      array(
        'id'          => 'capalot_price',
        'type'        => 'number',
        'title'       => '价格：*',
        'desc'        => '免费请填写：0',
        'unit'        => esc_html(_capalot('site_currency_name', '金币')),
        'output'      => '.heading',
        'output_mode' => 'width',
        'default'     => _capalot('capalot_price', 0),
      ),

      array(
        'id'          => 'capalot_vip_rate',
        'type'        => 'number',
        'title'       => sprintf('%s购买折扣：*', esc_html(@$__vip_options['vip_name'])),
        'desc'        => '0.N 等于N折;1 等于不打折;0 等于会员免费',
        'unit'        => '.N折',
        'output'      => '.heading',
        'output_mode' => 'width',
        'default'     => _capalot('capalot_vip_rate', 0),
      ),

      array(
        'id'      => 'capalot_is_boosvip',
        'type'    => 'switcher',
        'title'   => sprintf('%s免费购买', esc_html(@$__vip_options['boosvip_name'])),
        'desc'   => '开启后永久会员用户免费，其他会员按折扣或者原价购买',
        'default' => _capalot('capalot_is_boosvip', false),
      ),

      array(
        'id'      => 'capalot_close_novip_pay',
        'type'    => 'switcher',
        'title'   => '关闭非会员购买',
        'default' => _capalot('capalot_close_novip_pay'),
        'desc'   => '开启后，非会员无法购买资源',
      ),

      array(
        'id'          => 'capalot_paynum',
        'type'        => 'number',
        'title'       => '已售数量',
        'desc'        => '可自定义修改数字',
        'unit'        => '个',
        'output'      => '.heading',
        'output_mode' => 'width',
        'default'     => _capalot('capalot_paynum', 0)
      ),

    ),
  ));

  // 文章高级设置 - 文章下载设置
  CSF::createSection($prefix, array(
    'title' => '文章下载设置',
    'fields' => array(

      array(
        'id'      => 'capalot_status',
        'type'    => 'switcher',
        'title'   => '启用付费下载模块',
        'label'   => '开启后可设置付费下载专有内容',
        'default' => _capalot('capalot_status'),
      ),

      array(
        'id'                     => 'capalot_downurl_new',
        'type'                   => 'group',
        'title'                  => '资源下载地址',
        'subtitle'               => '支持多个下载地址，支持https:,thunder:,magnet:,ed2k开头地址',
        'accordion_title_number' => true,
        'fields'                 => array(
          array(
            'id'      => 'name',
            'type'    => 'text',
            'title'   => '资源名称',
            'default' => '资源名称',
          ),
          array(
            'id'       => 'url',
            'type'     => 'upload',
            'title'    => '下载地址',
            'sanitize' => false,
          ),
          array(
            'id'    => 'pwd',
            'type'  => 'text',
            'title' => '下载密码',
          ),
        ),
        'default' => _capalot('capalot_downurl_new', array()),
      ),

      array(
        'id'      => 'capalot_info',
        'type'    => 'repeater',
        'title'   => '资源其他信息',
        'desc'    => '例如：格式 / 大小 / 类型',
        'fields'  => array(
          array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '标题',
            'default' => '格式',
          ),
          array(
            'id'      => 'desc',
            'type'    => 'text',
            'title'   => '描述',
            'default' => 'ZIP',
          ),
        ),
        'default' => _capalot('capalot_info', array()),
      ),

      array(
        'id'      => 'capalot_demourl',
        'type'    => 'text',
        'title'   => '资源预览地址',
        'label'   => '为空则不显示',
        'default' => _capalot('capalot_demourl'),
      ),

      array(
        'id'       => 'capalot_diy_btn',
        'type'     => 'text',
        'title'    => '自定义按钮展示',
        'subtitle' => '为空则不显示，用 | 隔开',
        'desc'     => '格式： 下载免费版|https://www.baidu.com/',
        'default'  => _capalot('capalot_diy_btn'),
      ),

    )
  ));

  // 文章高级设置 - 文章音视频设置
  CSF::createSection($prefix, array(
    'title' => '文章音视频设置',
    'fields' => array(

      array(
        'id'    => 'capalot_video',
        'type'  => 'switcher',
        'title' => '启用音视频模块',
        'default' => false
      ),

      array(
        'id'         => 'capalot_is_video_free',
        'type'       => 'switcher',
        'title'      => '免费播放',
        'label'      => '勾选后该视频不参与任何付费逻辑，可直接展示播放',
        'default'    => false,
      ),

      array(
        'id'                     => 'video_url_new',
        'type'                   => 'group',
        'title'                  => '媒体播放地址',
        'subtitle'               => '支持多集，支持mp4、mp3、m3u8等常见格式，不支持第三方平台解析',
        'accordion_title_number' => true,
        'fields'                 => array(
          array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '媒体名称',
            'default' => '',
          ),
          array(
            'id'       => 'src',
            'type'     => 'upload',
            'title'    => '播放地址',
            'sanitize' => false,
            'default'  => '',
          ),
          array(
            'id'       => 'img',
            'type'     => 'upload',
            'title'    => '封面海报',
            'sanitize' => false,
            'default'  => '',
          ),
        ),
      ),

    )
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
