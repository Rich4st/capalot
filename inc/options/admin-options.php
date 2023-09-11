<?php

defined('ABSPATH') || exit;

// CSF框架未加载，退出程序
if (!class_exists('CSF')) {
  return;
}

$prefix = _OPTIONS_PREFIX;
$template_dir = get_template_directory_uri();

/** 主题设置 */
CSF::createOptions($prefix, array(
  'menu_title' => '主题设置',
  'menu_slug' => 'capalot',
));

/**
 * 基本设置
 */
CSF::createSection($prefix, array(
  'title'  => '基本设置',
  'icon' => 'dashicons dashicons-admin-generic',
  'fields' => array(

    array(
      'id'      => 'site_logo',
      'type'    => 'upload',
      'title'   => '网站LOGO',
      'default' => _capalot('site_logo', get_template_directory_uri() . '/assets/img/logo.png'),
    ),

    array(
      'id'      => 'site_favicon',
      'type'    => 'upload',
      'title'   => '网站favicon图标',
      'default' => _capalot('site_favicon', get_template_directory_uri() . '/assets/img/favicon.png'),
    ),


    array(
      'id'      => 'is_site_notify',
      'type'    => 'switcher',
      'title'   => '全站弹窗公告',
      'desc'   => '开启网站顶部显示公告图标，点击弹出公告',
      'default' => true,
    ),

    array(
      'id'         => 'site_notify_title',
      'type'       => 'text',
      'title'      => '全站弹窗公告-标题',
      'desc'       => '纯文本',
      'attributes' => array(
        'style' => 'width: 100%;',
      ),
      'default'    => '全站弹窗公告标题',
      'dependency' => array('is_site_notify', '==', 'true'),
    ),
    array(
      'id'         => 'site_notify_desc',
      'type'       => 'textarea',
      'title'      => '全站弹窗公告-内容',
      'desc'       => '全站弹窗公告，通知，纯文本通知弹窗,支持html代码',
      'attributes' => array(
        'style' => 'width: 100%;',
      ),
      'sanitize' => false,
      'default'    => '这是一条网站公告，可在后台开启或关闭，可自定义背景颜色，标题，内容，此处可使用html标签...',
      'dependency' => array('is_site_notify', '==', 'true'),
    ),


    array(
      'id'      => 'is_site_tougao',
      'type'    => 'switcher',
      'title'   => '网站投稿功能',
      'label'   => '控制网站投稿功能开关，启用后前端用户可以投稿管理编辑自己的文章,发布权限跟随WP自身，订阅者、贡献者需要审核、作者权限不需要审核，使用此功能请自行对自己的用户恶意上传投稿附件图片等审核负责。嫌麻烦就关闭',
      'default' => true,
    ),

    array(
      'id'      => 'is_site_comments',
      'type'    => 'switcher',
      'title'   => '网站评论功能',
      'label'   => '控制网站评论功能开关，如果用不到，推荐关闭',
      'default' => true,
    ),
    array(
      'id'      => 'is_site_tickets',
      'type'    => 'switcher',
      'title'   => '网站工单功能',
      'label'   => '控制网站工单功能开关，启用后用户可在前端个人中心提交问题反馈咨询等，管理员可在后台回复管理',
      'default' => true,
    ),

    array(
      'id'      => 'is_site_tags_page',
      'type'    => 'switcher',
      'title'   => '网站标签云功能',
      'label'   => '开启后可打开访问，功能页面地址：' . esc_url(home_url('/tags')),
      'default' => true,
    ),
    array(
      'id'      => 'is_site_link_manager_page',
      'type'    => 'switcher',
      'title'   => '网站网址导航功能',
      'label'   => '开启后可打开访问，功能页面地址：' . esc_url(home_url('/links')),
      'default' => true,
    ),
    array(
      'id'      => 'is_site_vip_price_page',
      'type'    => 'switcher',
      'title'   => '网站VIP介绍页面',
      'label'   => '开启后可在前台独立页面展示网站VIP套餐和介绍，功能页面地址：' . esc_url(home_url('/vip-prices')),
      'default' => true,
    ),


    array(
      'id'      => 'site_main_target_blank',
      'type'    => 'switcher',
      'title'   => '网站主链接新窗口打开文章',
      'desc'    => '主要链接包括列表展示盒网格展示一些文章都新窗口打开',
      'default' => false,
    ),


    array(
      'id'       => 'site_web_css',
      'type'     => 'textarea',
      'title'    => '自定义CSS样式代码',
      'before'   => '<p class="csf-text-muted"><strong>位于顶部，自定义修改CSS</strong>不用添加<strong>&lt;style></strong>标签</p>',
      'sanitize' => false,
      'default'  => '',
    ),

    array(
      'id'       => 'site_web_js',
      'type'     => 'textarea',
      'title'    => '网站底部自定义JS代码',
      'desc'     => '位于底部，用于添加第三方流量数据统计代码，如：Google analytics、百度统计、CNZZ,例如：' . esc_attr('<script>统计代码</script>'),
      'sanitize' => false,
      'default'  => '',
    ),

  ),
));

/**
 * 安全设置
 */
CSF::createSection($prefix, array(
  'title'  => '安全设置',
  'icon' => 'dashicons dashicons-shield',
  'fields' => array(

    array(
      'id'      => 'is_site_img_captcha',
      'type'    => 'switcher',
      'title'   => '网站注册登录验证码',
      'label'   => '开启后，注册登录需要填写验证码，防止恶意注册和扫描',
      'default' => true,
    ),

    array(
      'id'      => 'site_security_key',
      'type'    => 'text',
      'title'   => '网站安全秘钥',
      'desc'    => '用于网站免登录购买游客识别等敏感信息加密等重要参数，可定期更换，中途更换后，如果刚好有游客购买资源，则购买权限缓存将失效，切勿在高峰期频繁更换或泄露给他人',
      'default' => md5(home_url() . time()),
    ),

    array(
      'id'      => 'is_site_down_basename',
      'type'    => 'switcher',
      'title'   => '下载时显示真实文件名',
      'label'   => '用户下载站内文件时，文件名显示真实文件名，如无特殊需求，不建议开启，容易被人恶意抓取嗅探文件地址',
      'default' => false,
    ),

    array(
      'id'      => 'site_login_security_param',
      'type'    => 'text',
      'title'   => 'WP自带登录地址访问密码',
      'desc'    => '留空则不开启后台登录地址（' . esc_url(home_url('/wp-login.php')) . '）保护，<br>开启后您的真实登录地址是<b style="color:red;">' . esc_url(home_url('/wp-login.php')) . '?security=你设置得密码</b><br>设置后请牢记自己的真实登录地址，此功能不影响网站前台登录界面，只是为了保护wp本身自带得登录地址，防止他人恶意扫描攻击爆破，可有效保护',
      'default' => '',
    ),

  ),
));

/**
 * SEO设置
 */
CSF::createSection($prefix, array(
  'title' => 'SEO设置',
  'icon'   => 'dashicons dashicons-admin-site-alt3',
  'fields' => array(

    array(
      'type'    => 'content',
      'content' => '主题自带SEO优化功能说明：
            <br>1，自带SEO功能包含了自定义文章，首页，页面的TDK功能，自动抓取网站摘要，关键词，自动添加OG协议描述信息等
            <br>2，支持自定义替换wordpress默认的标题链接符号',
    ),

    array(
      'id'      => 'site_no_category',
      'type'    => 'switcher',
      'title'   => '分类别名category精简',
      'desc'   => '网站文章分类链接去除category/前缀，非特殊需求不必开启，尽量保持WP原有规则',
      'default' => _capalot('no_category', false),
    ),

    array(
      'id'      => 'is_theme_seo',
      'type'    => 'switcher',
      'title'   => '主题内置的SEO功能',
      'label'   => '有部分用户在用插件做SEO，可以在此关闭主题自带SEO功能',
      'default' => true,
    ),

    array(
      'id'         => 'site_seo',
      'type'       => 'fieldset',
      'title'      => '内置SEO设置',
      'fields'     => array(
        array(
          'id'      => 'separator',
          'type'    => 'text',
          'title'   => '全站链接符',
          'desc'    => '一经选择，切勿中途更改，对SEO不友好，一般为“-”或“_”',
          'default' => _capalot('site_seo:separator', '-'),
        ),
        array(
          'id'         => 'keywords',
          'type'       => 'text',
          'title'      => '网站关键词',
          'desc'       => '3-5个关键词，用英文逗号隔开',
          'attributes' => array(
            'style' => 'width: 100%;',
          ),
          'default'    => _capalot('site_seo:keywords', ''),
        ),
        array(
          'id'       => 'description',
          'type'     => 'textarea',
          'sanitize' => false,
          'title'    => '网站描述',
          'default'  => _capalot('site_seo:description', ''),
        ),

      ),
      'dependency' => array('is_theme_seo', '==', 'true'),
    ),

  ),
));

/**
 * 网站优化
 */
CSF::createSection($prefix, array(
  'title'  => '网站优化',
  'icon' => 'dashicons dashicons-update',
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
  'icon' => 'dashicons dashicons-cart',
  'title'  => '商城设置',
));

// 商城设置 - 基本设置
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

// 商城设置 - 默认发布字段
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

// 商城设置 - VIP会员设置
// CSF::createSection($prefix, array(
//   'parent' => 'shop_options',
//   'title' => 'VIP会员配置',
//   'fields' => array(

//     array(
//       'type'    => 'submessage',
//       'style'   => 'danger',
//       'content' => '<b>请注意：</b><p>1，本会员组涉及逻辑为。只有普通用户，vip用户，永久用户三种权限，对应资源权限有普通原价购买，vip用户折扣或者免费，永久vip免费，其中可以自定义会员开通套餐时长</p><p>2，注意开通套餐仅仅是购买会员开通，不涉及权限控制，ripro会员组一直都是会员非会员两种，如果要多会员组而不是多套餐开通，请用riplus或者rimini能弄几十万个会员组，根据自己网站运营思路设计网站VIP会员售价</p><p>3，会员组主要控制你网站用户不同级别的叫法名称白标识和权限次数</p><p>4，会员开通套餐配置用于前台个人中心和vip开通页面购买套餐选择，不涉及上面的会员组配置任何权限控制。请不要混淆，更不要当成自定义权限，这只是套餐，套餐，套餐，用于前台开通购买显示。</p>',
//     ),

//     array(
//       'id'      => 'is_pay_vip_allow_oline',
//       'type'    => 'switcher',
//       'title'   => '仅限在线支付购买开通VIP',
//       'desc'   => '开启后，站内币不允许支付购买vip，必须在线支付',
//       'default' => false,
//     ),

//     array(
//       'id'    => 'site_vip_options',
//       'type'  => 'tabbed',
//       'title' => '会员组设置',
//       'desc'   => '会员组主要控制你网站用户不同级别的叫法名称白标识和权限次数',
//       'tabs'  => array(
//         array(
//           'id'     => 'no',
//           'title'  => '默认普通用户',
//           'icon'   => 'fa fa-circle',
//           'fields' => array(

//             array(
//               'id'      => 'no_name',
//               'type'    => 'text',
//               'title'   => '名称',
//               'default' => '普通用户',
//             ),

//             array(
//               'id'      => 'no_downnum',
//               'type'    => 'text',
//               'title'   => '每日可下载次数',
//               'default' => '5',
//             ),

//             array(
//               'id'      => 'no_desc',
//               'type'    => 'textarea',
//               'title'   => '特权介绍',
//               'desc'   => '每行一个，用于前台展示',
//               'default' => '下载本站免费资源' . PHP_EOL . '每日可下载5个免费资源' . PHP_EOL .
//                 '5×8小时在线人工客服' . PHP_EOL . '全站无限制收藏次数',
//             ),

//           ),
//         ),
//         array(
//           'id'     => 'vip',
//           'title'  => '会员用户',
//           'icon'   => 'fa fa-circle',
//           'fields' => array(
//             array(
//               'id'      => 'vip_name',
//               'type'    => 'text',
//               'title'   => '名称',
//               'default' => 'VIP会员',
//             ),
//             array(
//               'id'      => 'vip_downnum',
//               'type'    => 'text',
//               'title'   => '每日可下载次数',
//               'default' => '10',
//             ),
//             array(
//               'id'      => 'vip_desc',
//               'type'    => 'textarea',
//               'title'   => '特权介绍',
//               'desc'   => '每行一个，用于前台展示',
//               'default' => '可获取专属免费资源' . PHP_EOL . '每日可下载10个免费资源' . PHP_EOL . '5×8小时在线人工客服' .
//                 PHP_EOL . '全站无限制收藏次数',
//             ),
//           ),
//         ),
//         array(
//           'id'     => 'boosvip',
//           'title'  => '永久会员用户',
//           'icon'   => 'fa fa-circle',
//           'fields' => array(
//             array(
//               'id'      => 'boosvip_name',
//               'type'    => 'text',
//               'title'   => '名称',
//               'default' => '永久会员',
//             ),
//             array(
//               'id'      => 'boosvip_downnum',
//               'type'    => 'text',
//               'title'   => '每日可下载次数',
//               'default' => '99',
//             ),
//             array(
//               'id'      => 'boosvip_desc',
//               'type'    => 'textarea',
//               'title'   => '特权介绍',
//               'desc'   => '每行一个，用于前台展示',
//               'default' => '可获取专属免费资源' . PHP_EOL . '每日可下载99个免费资源' . PHP_EOL .
//                 '5×8小时在线人工客服' . PHP_EOL . '全站无限制收藏次数',
//             ),

//           ),
//         ),
//       ),
//     ),



//     array(
//       'id'      => 'site_vip_buy_options',
//       'type'    => 'group',
//       'title'   => '会员开通套餐配置',
//       'desc'   => '会员开通套餐配置用于前台个人中心和vip开通页面购买套餐选择,不涉及上面的会员组配置任何权限控制。
//       请不要混淆，更不要当成自定义权限，这只是套餐，套餐，套餐，用于前台开通购买显示。',
//       'fields'  => array(
//         array(
//           'id'      => 'title',
//           'type'    => 'text',
//           'default' => '会员',
//           'desc'    => '比如包月会员',
//           'title'   => '套餐名称',
//         ),
//         array(
//           'id'      => 'daynum',
//           'type'    => 'text',
//           'default' => '30',
//           'desc'    => '比如你想设置一个套餐是月费,则填写30,如果要设置终身会员套餐,填写: 9999',
//           'title'   => '开通天数',
//         ),
//         array(
//           'id'      => 'price',
//           'type'    => 'text',
//           'default' => '20',
//           'desc'    => '此套餐所需的站内币价格',
//           'title'   => '套餐价格/' . _capalot('site_coin_name', '金币'),
//         ),
//       ),
//       'default' => array(
//         array(
//           'title'  => '体验会员',
//           'daynum' => '1',
//           'price'  => '10',
//         ),
//         array(
//           'title'  => '包月会员',
//           'daynum' => '30',
//           'price'  => '300',
//         ),
//         array(
//           'title'  => '永久会员',
//           'daynum' => '9999',
//           'price'  => '3000',
//         ),
//       ),
//     ),

//     array(
//       'id'      => 'site_buyvip_desc',
//       'type'    => 'repeater',
//       'title'   => '会员开通协议说明',
//       'fields'  => array(
//         array(
//           'id'       => 'content',
//           'type'     => 'text',
//           'default'  => '',
//           'sanitize' => false,
//         ),
//       ),
//       'default' => array(
//         array('content' => '指会员所享有根据选择购买的会员选项所享有的特殊服务，具体以本站公布的服务内容为准。'),
//         array('content' => '在遵守VIP会员协议前提下，VIP会员在会员有效期内可以享受免费或折扣权限购买获取资源。'),
//         array('content' => 'VIP会员属于虚拟服务，购买后不能够申请退款。如付款前有任何疑问，联系站长处理'),
//         array('content' => '本站所有资源，针对不同等级VIP会员可直接下载，特殊资源商品会注明是否免费'),
//       ),
//     ),

//   )
// ));

// 商城设置 - 支付接口配置
// CSF::createSection($prefix, array(
//   'parent' => 'shop_options',
//   'title' => '支付接口配置',
//   'fields' => array(

//     array(
//       'id'      => 'is_site_coin_pay',
//       'type'    => 'switcher',
//       'title'   => '站内币-余额支付购买',
//       'label'   => '开启后网站支持站内币购买文章和会员',
//       'default' => true,
//     ),
//     array(
//       'id'      => 'is_site_cdk_pay',
//       'type'    => 'switcher',
//       'title'   => '卡密CDK-支付兑换',
//       'label'   => '开启后网站支持卡密CDK充值余额和会员',
//       'default' => true,
//     ),
//     array(
//       'id'         => 'site_cdk_pay_link',
//       'type'       => 'text',
//       'title'      => '卡密购买地址',
//       'desc'       => '不想用站自己支付的可以用卡密规避风险，自己生产充值卡密去第三方平台发卡，用户购买卡密后回来充值消费。',
//       'dependency' => array('is_site_cdk_pay', '==', 'true'),
//     ),

//     // 支付宝配置
//     array(
//       'id'      => 'is_alipay',
//       'type'    => 'switcher',
//       'title'   => '支付宝（官方企业支付-新应用模式）',
//       'label'   => '支付宝商户后台推荐签约电脑网站支付，当面付，手机网站支付，配置教程（https://www.kancloud.cn/rizhuti/ritheme/1961638）',
//       'default' => _capalot('is_alipay', true),
//     ),
//     array(
//       'id'         => 'alipay',
//       'type'       => 'fieldset',
//       'title'      => '配置详情',
//       'fields'     => array(

//         array(
//           'id'         => 'appid',
//           'type'       => 'text',
//           'title'      => '开放平台-应用appid',
//           'attributes' => array(
//             'type' => 'password',
//           ),
//           'default'    => _capalot('alipay:appid', ''),
//         ),
//         array(
//           'id'      => 'privateKey',
//           'type'    => 'textarea',
//           'title'   => '开放平台-应用私钥',
//           'desc'    => '请注意这里是应用的私钥，就是你用工具生成的应用私钥',
//           'default' => _capalot('alipay:privateKey', ''),
//         ),
//         array(
//           'id'      => 'publicKey',
//           'type'    => 'textarea',
//           'title'   => '开放平台-支付宝公钥',
//           'desc'    => '请注意这里是支付宝后台中的公钥，不是你生成的那个应用私钥，如果支付成功后，网站支付状态不刷新或者后台的订单显示未支付，请检查公钥是否支付宝公钥和https证书是否正常，一般更换https证书即可，各大支付平台对ssl证书都有一定的安全性验证，个别有时候无法通知，换一个ssl证书即可',
//           'default' => _capalot('alipay:publicKey', ''),
//         ),

//         array(
//           'id'      => 'api_type',
//           'type'    => 'radio',
//           'title'   => '应用接口模式',
//           'inline'  => true,
//           'options' => array(
//             'qr'  => '当面付(需签约当面付产品)',
//             'web' => '电脑网站支付(需签约电脑网站支付产品)',
//           ),
//           'desc'    => '自2021年初开始，支付宝官方风控系统对异地跨地区进行当面付扫码支付或者信用卡以及分期付款的异常用户，容易被风控商户，建议非必要情况下不要使用当面付模式，关闭此项，如果是个人的商户，没有电脑网站支付产品，只能硬刚当面付，没有其他办法。',
//           'default' => 'web',
//         ),

//         array(
//           'id'      => 'is_mobile',
//           'type'    => 'switcher',
//           'title'   => '手机端自动跳转H5支付',
//           'label'   => '(需签约手机网站支付产品，只支持手机浏览器打开唤醒APP支付，并不能在应用内，如QQ/微信/支付宝内部浏览器无效)',
//           'default' => false,
//         ),

//       ),
//       'dependency' => array('is_alipay', '==', 'true'),
//     ),

//     // 微信支付配置
//     array(
//       'id'      => 'is_weixinpay',
//       'type'    => 'switcher',
//       'title'   => '微信支付（官方企业支付）',
//       'label'   => '微信官方商户后台推荐签约native产品，JSAPI产品，h5支付产品',
//       'default' => _capalot('is_weixinpay', false),
//     ),
//     array(
//       'id'         => 'weixinpay',
//       'type'       => 'fieldset',
//       'title'      => '配置详情',
//       'fields'     => array(
//         array(
//           'id'      => 'mch_id',
//           'type'    => 'text',
//           'title'   => '微信支付商户号',
//           'desc'    => '微信支付商户号 PartnerID 通过微信支付商户资料审核后邮件发送',
//           'default' => _capalot('weixinpay:mch_id', ''),
//         ),
//         array(
//           'id'      => 'appid',
//           'type'    => 'text',
//           'title'   => '公众号或小程序APPID',
//           'desc'    => '公众号APPID 通过微信支付商户资料审核后邮件发送,开通jsapi支付和配置公众号手机内直接登录的用户注意,如果是小程序的appid,请到支付商户绑定公众号appid授权,这里填写为公众号即可',
//           'default' => _capalot('weixinpay:appid', ''),
//         ),
//         array(
//           'id'         => 'key',
//           'type'       => 'text',
//           'title'      => '微信支付API密钥',
//           'desc'       => '帐户设置-安全设置-API安全-API密钥-设置API密钥',
//           'default'    => _capalot('weixinpay:key', ''),
//           'attributes' => array(
//             'type' => 'password',
//           ),
//         ),
//         // array(
//         //     'id'      => 'is_jsapi',
//         //     'type'    => 'switcher',
//         //     'title'   => 'JSAPI支付',
//         //     'label'   => '微信端内打开可以直接发起支付，开启此项需要登录注册里开启公众号登录，开启后网站用户在微信内登录后可以直接支付',
//         //     'default' => false,
//         // ),
//         array(
//           'id'      => 'is_mobile',
//           'type'    => 'switcher',
//           'title'   => '手机跳转H5支付',
//           'label'   => '移动端自动自动切换为跳转支付（需开通H5支付，只支持手机浏览器打开唤醒APP支付，并不能在应用内，如QQ/微信/支付宝内部浏览器无效）',
//           'default' => _capalot('weixinpay:is_mobile', false),
//         ),
//       ),
//       'dependency' => array('is_weixinpay', '==', 'true'),
//     ),

//     //虎皮椒 weixin
//     array(
//       'id'      => 'is_hupijiao_weixin',
//       'type'    => 'switcher',
//       'title'   => '虎皮椒(微信)',
//       'label'   => '无需企业资质，个人用户推荐，微信完美收款，无资质可以用此方法完美替代*_*',
//       'default' => false,
//     ),
//     array(
//       'id'         => 'hupijiao_weixin',
//       'type'       => 'fieldset',
//       'title'      => '配置详情',
//       'fields'     => array(
//         array(
//           'type'    => 'notice',
//           'style'   => 'success',
//           'content' => '虎皮椒V3  <a target="_blank" href="https://admin.xunhupay.com/sign-up/4123.html">注册地址</a>',
//         ),
//         array(
//           'id'      => 'app_id',
//           'type'    => 'text',
//           'title'   => 'APPID',
//           'desc'    => 'APPID',
//           'default' => '',
//         ),
//         array(
//           'id'         => 'app_secret',
//           'type'       => 'text',
//           'title'      => 'APPSECRET',
//           'desc'       => '密钥',
//           'default'    => '',
//           'attributes' => array(
//             'type' => 'password',
//           ),
//         ),
//         array(
//           'id'      => 'api_url',
//           'type'    => 'text',
//           'title'   => '支付网关',
//           'desc'    => '必填',
//           'default' => '',
//         ),

//       ),
//       'dependency' => array('is_hupijiao_weixin', '==', 'true'),
//     ),

//     //虎皮椒 alpay
//     array(
//       'id'      => 'is_hupijiao_alipay',
//       'type'    => 'switcher',
//       'title'   => '虎皮椒(支付宝)',
//       'label'   => '稳定第三方服务商渠道',
//       'default' => false,
//     ),
//     array(
//       'id'         => 'hupijiao_alipay',
//       'type'       => 'fieldset',
//       'title'      => '配置详情',
//       'fields'     => array(
//         array(
//           'type'    => 'notice',
//           'style'   => 'success',
//           'content' => '虎皮椒（讯虎支付）V3  <a target="_blank" href="https://admin.xunhupay.com/sign-up/4123.html">注册地址</a>',
//         ),
//         array(
//           'id'      => 'app_id',
//           'type'    => 'text',
//           'title'   => 'APPID',
//           'desc'    => 'APPID',
//           'default' => '',
//         ),
//         array(
//           'id'         => 'app_secret',
//           'type'       => 'text',
//           'title'      => 'APPSECRET',
//           'desc'       => '密钥',
//           'default'    => '',
//           'attributes' => array(
//             'type' => 'password',
//           ),
//         ),
//         array(
//           'id'      => 'api_url',
//           'type'    => 'text',
//           'title'   => '支付网关',
//           'desc'    => '必填',
//           'default' => '',
//         ),

//       ),
//       'dependency' => array('is_hupijiao_alipay', '==', 'true'),
//     ),

//     //讯虎新支付 微信
//     array(
//       'id'      => 'is_xunhupay_weixin',
//       'type'    => 'switcher',
//       'title'   => '迅虎(微信H5支付)',
//       'label'   => '支持电PC端扫码，移动端H5唤醒支付，微信内JSAPI支付，无资质可以用此方法完美替代*_*',
//       'default' => _capalot('is_xunhupay_weixin', false),
//     ),
//     array(
//       'id'         => 'xunhupay_weixin',
//       'type'       => 'fieldset',
//       'title'      => '配置详情',
//       'fields'     => array(
//         array(
//           'type'    => 'notice',
//           'style'   => 'success',
//           'content' => '讯虎支付 <a target="_blank" href="https://admin.xunhuweb.com/register/15235553019447ebb7e54725220a7cb9">-->>注册地址</a>',
//         ),

//         array(
//           'id'      => 'mchid',
//           'type'    => 'text',
//           'title'   => 'MCHID',
//           'desc'    => 'MCHID',
//           'default' => _capalot('xunhupay_weixin:mchid', ''),
//         ),
//         array(
//           'id'         => 'private_key',
//           'type'       => 'text',
//           'title'      => 'Private Key',
//           'desc'       => '密钥',
//           'default'    => _capalot('xunhupay_weixin:private_key', ''),
//           'attributes' => array(
//             'type' => 'password',
//           ),
//         ),
//         array(
//           'id'      => 'url_do',
//           'type'    => 'text',
//           'title'   => '支付网关',
//           'desc'    => '一般不用动，如虎皮椒官方有调整手动更新即可',
//           'default' => _capalot('xunhupay_weixin:url_do', ''),
//         ),

//       ),
//       'dependency' => array('is_xunhupay_weixin', '==', 'true'),
//     ),

//     //讯虎新支付 支付宝
//     array(
//       'id'      => 'is_xunhupay_alipay',
//       'type'    => 'switcher',
//       'title'   => '迅虎(支付宝H5支付)',
//       'label'   => '稳定第三方服务商渠道*_*',
//       'default' => _capalot('is_xunhupay_alipay', false),
//     ),
//     array(
//       'id'         => 'xunhupay_alipay',
//       'type'       => 'fieldset',
//       'title'      => '配置详情',
//       'fields'     => array(
//         array(
//           'type'    => 'notice',
//           'style'   => 'success',
//           'content' => '讯虎支付 <a target="_blank" href="https://admin.xunhuweb.com/register/15235553019447ebb7e54725220a7cb9">-->>注册地址</a>',
//         ),
//         array(
//           'id'      => 'mchid',
//           'type'    => 'text',
//           'title'   => 'MCHID',
//           'desc'    => 'MCHID',
//           'default' => _capalot('xunhupay_alipay:mchid', ''),
//         ),
//         array(
//           'id'         => 'private_key',
//           'type'       => 'text',
//           'title'      => 'Private Key',
//           'desc'       => '密钥',
//           'default'    => _capalot('xunhupay_alipay:private_key', ''),
//           'attributes' => array(
//             'type' => 'password',
//           ),
//         ),
//         array(
//           'id'      => 'url_do',
//           'type'    => 'text',
//           'title'   => '支付网关',
//           'desc'    => '一般不用动，如虎皮椒官方有调整手动更新即可',
//           'default' => _capalot('xunhupay_alipay:url_do', ''),
//         ),

//       ),
//       'dependency' => array('is_xunhupay_alipay', '==', 'true'),
//     ),

//     // 易支付-支付宝
//     array(
//       'id'      => 'is_epay_alipay',
//       'type'    => 'switcher',
//       'title'   => '易支付(支付宝通道)',
//       'label'   => '易支付(支付宝通道)，本API接口为彩虹易支付版本SDK接口',
//       'default' => false,
//     ),

//     array(
//       'id'         => 'epay_alipay',
//       'type'       => 'fieldset',
//       'title'      => '配置详情',
//       'fields'     => array(
//         array(
//           'id'      => 'pid',
//           'type'    => 'text',
//           'title'   => '商户ID',
//           'desc'    => '',
//           'default' => '',
//         ),
//         array(
//           'id'         => 'key',
//           'type'       => 'text',
//           'title'      => '商户KEY',
//           'desc'       => '',
//           'default'    => '',
//           'attributes' => array(
//             'type' => 'password',
//           ),
//         ),
//         array(
//           'id'      => 'apiurl',
//           'type'    => 'text',
//           'title'   => '支付API地址',
//           'desc'    => '请填写你的易支付-接口地址,格式为:http[s]://www.xxxxx.xx/记得协议和最后的/别少',
//           'default' => '',
//         ),

//       ),
//       'dependency' => array('is_epay_alipay', '==', 'true'),
//     ),
//     // 易支付-微信
//     array(
//       'id'      => 'is_epay_weixin',
//       'type'    => 'switcher',
//       'title'   => '易支付(微信通道)',
//       'label'   => '易支付(微信通道)，本API接口为彩虹易支付版本SDK接口',
//       'default' => false,
//     ),

//     array(
//       'id'         => 'epay_weixin',
//       'type'       => 'fieldset',
//       'title'      => '配置详情',
//       'fields'     => array(
//         array(
//           'id'      => 'pid',
//           'type'    => 'text',
//           'title'   => '商户ID',
//           'desc'    => '',
//           'default' => '',
//         ),
//         array(
//           'id'         => 'key',
//           'type'       => 'text',
//           'title'      => '商户KEY',
//           'desc'       => '',
//           'default'    => '',
//           'attributes' => array(
//             'type' => 'password',
//           ),
//         ),
//         array(
//           'id'      => 'apiurl',
//           'type'    => 'text',
//           'title'   => '支付API地址',
//           'desc'    => '请填写你的易支付-接口地址,格式为:http[s]://www.xxxxx.xx/记得协议和最后的/别少',
//           'default' => '',
//         ),

//       ),
//       'dependency' => array('is_epay_weixin', '==', 'true'),
//     ),

//     //paypal
//     array(
//       'id'      => 'is_paypal',
//       'type'    => 'switcher',
//       'title'   => 'PayPal（贝宝）',
//       'label'   => '贝宝国际支付，需要企业版',
//       'default' => false,
//     ),
//     array(
//       'id'         => 'paypal',
//       'type'       => 'fieldset',
//       'title'      => '配置详情',
//       'fields'     => array(
//         array(
//           'type'    => 'notice',
//           'style'   => 'success',
//           'content' => '查看你的paypal秘钥信息：https://www.paypal.com/businessprofile/mytools/apiaccess/firstparty/signature',
//         ),
//         array(
//           'id'      => 'username',
//           'type'    => 'text',
//           'title'   => 'API用户名',
//           'desc'    => '',
//           'default' => '',
//         ),
//         array(
//           'id'         => 'password',
//           'type'       => 'text',
//           'title'      => 'API密码',
//           'desc'       => '',
//           'default'    => '',
//           'attributes' => array(
//             'type' => 'password',
//           ),
//         ),
//         array(
//           'id'         => 'signature',
//           'type'       => 'text',
//           'title'      => '签名',
//           'desc'       => '',
//           'default'    => '',
//           'attributes' => array(
//             'type' => 'password',
//           ),
//         ),
//         array(
//           'id'      => 'currency',
//           'type'    => 'text',
//           'title'   => '结算货币',
//           'desc'    => '列如(USD：美元、EUR：欧元、GBP：英镑、JPY：日元、CAD：加拿大元、AUD：澳大利亚元、CHF：瑞士法郎、CNY：人民币、SEK：瑞典克朗、NZD：新西兰元)',
//           'default' => 'USD',
//         ),
//         array(
//           'id'      => 'rates',
//           'type'    => 'text',
//           'title'   => '货币汇率',
//           'desc'    => '1元等于多少结算货币,例如你设置结算货币为USD，则1元=0.7美元',
//           'default' => '0.14',
//         ),
//         array(
//           'id'      => 'debug',
//           'type'    => 'switcher',
//           'title'   => '沙盒调试模式',
//           'label'   => '不是测试账户调试时切勿开启！',
//           'default' => false,
//         ),

//       ),
//       'dependency' => array('is_paypal', '==', 'true'),
//     ),

//     // array(
//     //     'id'      => 'is_manualpay',
//     //     'type'    => 'switcher',
//     //     'title'   => '手动静态支付（人工支付）',
//     //     'label'   => '采用微信或支付宝静态收款码提示用户付款对应金额后，引导用户联系网站客服发送订单号核对收取金额由网站管理员后台确认收款',
//     //     'default' => false,
//     // ),

//   )
// ));

/**
 * 布局设置
 */
CSF::createSection($prefix, array(
  'id' => 'layout_options',
  'icon' => 'dashicons dashicons-media-document',
  'title' => '布局设置',
));

// 布局设置 - 文章列表布局
CSF::createSection($prefix, array(
  'parent' => 'layout_options',
  'title' => '文章列表布局',
  'fields' => array(

    array(
      'id' => 'site_pagination_type',
      'type' => 'radio',
      'inline' => true,
      'title' => '分页风格',
      'options' => array(
        'standard' => '传统分页',
        'click' => '点击加载更多',
        'pull' => '下拉加载更多',
      ),
      'default' => 'click'
    )
  )
));
