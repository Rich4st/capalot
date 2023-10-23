<?php


if (!is_user_logged_in()) {
    wp_safe_redirect(home_url('/login'));
    exit;
}


global $current_user;

$user_id = $current_user->ID;
$post_id = intval(get_response_param('post_id', 0, 'get'));
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : $post_id;


// 检查是否为编辑现有文章
$is_editing = $post_id > 0;

// 判断编辑权限
$post_author_id = get_post_field('post_author', $post_id);
// 判断文章的作者ID是否与当前用户ID相同
if ($is_editing && $post_author_id != $user_id) {
    capalot_wp_die(
        __('无权限编辑', 'ripro'),
        __('仅限编辑作者本人文章', 'ripro'),
        get_uc_menu_link('tougao')
    );
    exit;
}

$site_vip_options = _capalot('site_vip_options');

// 初始化文章信息变量
$post_title = '';
$post_content = '';
$post_status = 'publish';
// $post_format = '';
$post_category = 0;
$post_tags = '';
$thumbnail_id = -1;
// 添加或更新自定义字段值
$post_meta_fields = array(
    'capalot_status' => 0,
    'capalot_video' => 0,
    'capalot_is_boosvip' => 0,
    'capalot_close_novip_pay' => 0,
    'capalot_price' => '',
    'capalot_vip_rate' => '',
    'capalot_demourl' => '',
    'capalot_diy_btn' => '',
    'capalot_downurl_new' => [['name' => '', 'pwd' => '', 'url' => '']],
    'capalot_is_video_free' => 0,
    'video_url_new' => [['title' => '', 'img' => '', 'src' => '']],
);

// 获取现有文章内容（如果有）
if ($is_editing) {
    $post = get_post($post_id);
    $post_title = $post->post_title;
    $post_content = $post->post_content;
    $post_status = $post->post_status;
    // $post_format = get_post_format($post_id); // 需要主题支持文章形式才有效
    $post_category = wp_get_post_categories($post_id)[0];
    $post_tags = implode(', ', wp_get_post_tags($post_id, array('fields' => 'names')));
    $thumbnail_id = get_post_thumbnail_id($post_id);

    foreach ($post_meta_fields as $key => $default_value) {
        $post_meta_fields[$key] = get_post_meta($post_id, $key, true) === ''
            ? $default_value
            : get_post_meta($post_id, $key, true);
    }
}



$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 获取表单数据
    $post_title = $_POST['post_title'];
    $post_content = $_POST['post_content'];
    $post_status = $_POST['post_status'];
    // $post_format = $_POST['post_format'];
    $post_category = intval($_POST['post_category']);
    $post_tags = $_POST['post_tags'];
    $thumbnail_id = $_POST['_thumbnail_id'];

    $bool_fields = array(
        'capalot_status',
        'capalot_video',
        'capalot_is_boosvip',
        'capalot_close_novip_pay',
        'capalot_is_video_free',
    );
    foreach ($post_meta_fields as $key => $default_value) {

        if (in_array($key, $bool_fields)) {
            $retVal = (isset($_POST['post_meta'][$key])) ? 1 : 0;
        } elseif (isset($_POST['post_meta'][$key])) {
            $retVal = $_POST['post_meta'][$key];
        } else {
            $retVal = $default_value;
        }

        $post_meta_fields[$key] = $retVal;
    }

    if (!current_user_can('publish_posts') && $post_status == 'publish') {
        $post_status == 'pending';
    }

    $post = array(
        'ID' => $post_id,
        'post_title' => $post_title,
        'post_content' => wp_kses_post($post_content),
        'post_category' => array($post_category),
        'post_status' => $post_status,
        'tags_input' => $post_tags,
        'meta_input' => $post_meta_fields // 包含自定义字段
    );


    // 验证Nonce字段
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update_post_nonce')) {
        $message = '<div class="alert alert-danger bg-[#cef2e7]" role="alert"><i class="fas fa-info-circle me-1"></i>' . __('非法请求', 'ripro') . '</div>';
    }

    if (empty($message)) {
        // 创建新文章或更新现有文章
        if ($is_editing) {
            wp_update_post($post);
            $message = '<div class="alert alert-success mb-2 bg-[#cef2e7] text-[#0a966c] md:p-4 p-2 rounded" role="alert"><i class="fas fa-check-circle me-1"></i>' . __('更新成功', 'ripro') . ' <a class="text-gray-500 w-full line-clamp-1" target="_blank" href="' . esc_url(get_permalink($post_id)) . '">' . esc_url(get_permalink($post_id)) . '</a></div>';
        } else {
            $post_id = wp_insert_post($post);
            wp_redirect(esc_url(add_query_arg(array('post_id' => $post_id), home_url('/tougao'))));
            exit;
        }

        // 更新缩略图
        if ($thumbnail_id > 0) {
            set_post_thumbnail($post_id, $thumbnail_id);
        }
    }
}

$bg_image = get_template_directory_uri() . '/assets/img/bg.jpg';
$container = _capalot('site_container_width', '1400');

get_header();


?>

<section class="tougao dark:bg-dark  p-2 dark:text-gray-400">
    <div class="lg:my-6 my-2 mx-auto" style="max-width: <?php
                                                        if ($container === '') {
                                                            echo '1280';
                                                        } else {
                                                            echo $container;
                                                        }
                                                        ?>px;">
        <form id="post-form" action="" method="post">
            <div class="flex md:flex-row flex-col md:space-x-2 md:space-y-0 spce-x-0 space-y-2 justify-center ">
                <div class="tougao-wrapper  md:w-[75%] w-full  bg-white dark:bg-dark-card p-4 rounded">
                    <!-- 新增编辑文章 -->
                    <div class="card overflow-visible">
                        <?php echo $message; ?>
                        <h5 class="font-bold mb-2"><?php echo $is_editing ? __('编辑文章', 'ripro') : __('发布文章', 'ripro'); ?></h5>
                        <div class="text-sm small mb-3">
                            <p class="mb-1"><?php _e('如遇无法添加附件到本文章提示，请先将文章发布文后添加。', 'ripro'); ?></p>
                            <?php if (is_site_author_aff()) : ?>
                                <p class="text-[#7eb0fa]"><?php printf(__('本站已开启作者分成奖励，您发布的资源有人购买时，成交价的(%s%%)收入将发放至您的佣金。', 'ripro'), get_site_author_aff_rate() * 100); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php wp_nonce_field('update_post_nonce'); ?>

                            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                            <div class="mb-3">
                                <label class="form-label pb-2 block text-gray-500"><?php _e('文章标题', 'ripro'); ?></label>
                                <input type="text" class="form-control w-full  dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="post_title" value="<?php echo $post_title; ?>" placeholder="<?php _e('请输入文章标题', 'ripro'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label pb-2 block text-gray-500"><?php _e('文章内容', 'ripro'); ?></label>
                                <?php wp_editor(wp_kses_post($post_content), 'post_content', array(
                                    'media_buttons' => current_user_can('upload_files'),
                                    '_content_editor_dfw' => true,
                                    'editor_height'       => 350,
                                    'tinymce'             => array(
                                        'resize'                  => false,
                                        'wp_autoresize_on'        => false,
                                        'add_unload_trigger'      => false,
                                    ),
                                )); ?>

                            </div>


                            <?php if (is_site_shop()) : ?>
                                <div class="mb-3 flex flex-row space-x-6">
                                    <div class="form-check form-switch form-check-inline">
                                        <input class="form-check-input" type="checkbox" role="switch" id="capalot_status_switch" name="post_meta[capalot_status]" <?php if ($post_meta_fields['capalot_status'] == 1) echo 'checked'; ?>>
                                        <label class="form-check-label  text-gray-500" for="capalot_status_switch"><?php _e('启用付费下载', 'ripro'); ?></label>
                                    </div>

                                    <div class="form-check form-switch form-check-inline">
                                        <input class="form-check-input" type="checkbox" role="switch" id="capalot_video_switch" name="post_meta[capalot_video]" <?php if ($post_meta_fields['capalot_video'] == 1) echo 'checked'; ?>>
                                        <label class="form-check-label  text-gray-500" for="capalot_video_switch"><?php _e('启用付费音视频', 'ripro'); ?></label>
                                    </div>
                                </div>

                                <!-- price-input-warp -->
                                <div id="price-input-warp" class="mb-3 rounded border-2 border-dotted border-[#ffa521] w-full p-2 text-gray-500" style="display: none;">
                                    <div class=" mb-2 ">
                                        <span class="form-label"><?php _e('价格信息', 'ripro'); ?></span>
                                    </div>
                                    <div class="mb-3 flex flex-row space-x-6">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" value="" id="capalot_is_boosvip" name="post_meta[capalot_is_boosvip]" <?php if ($post_meta_fields['capalot_is_boosvip'] == 1) echo 'checked'; ?>>
                                            <label class="form-check-label" for="capalot_is_boosvip"><?php echo $site_vip_options['boosvip_name']; ?><?php _e('免费购买', 'ripro'); ?></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" value="" id="capalot_close_novip_pay" name="post_meta[capalot_close_novip_pay]" <?php if ($post_meta_fields['capalot_close_novip_pay'] == 1) echo 'checked'; ?>>
                                            <label class="form-check-label" for="capalot_close_novip_pay"><?php echo $site_vip_options['no_name']; ?><?php _e('禁止购买', 'ripro'); ?></label>
                                        </div>
                                    </div>
                                    <div class="flex md:flex-row flex-col md:space-x-4 md:space-y-0 spce-x-0 space-y-4">
                                        <div class="input-group input-group-sm mb-3 md:w-[50%] w-full justify-between ">
                                            <span class="input-group-text mb-2 block"><?php _e('价格', 'ripro'); ?></span>
                                            <div class="flex flex-row items-center">
                                                <input type="text" class="form-control w-full   dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="post_meta[capalot_price]" value="<?php echo $post_meta_fields['capalot_price']; ?>">
                                                <span class="input-group-text w-24 text-center"><i class="<?php echo get_site_coin_icon(); ?> me-1"></i><?php echo get_site_coin_name(); ?></span>
                                            </div>
                                        </div>

                                        <div class="input-group input-group-sm mb-3 md:w-[50%] w-full justify-between">
                                            <span class="input-group-text mb-2 block"><?php echo $site_vip_options['vip_name']; ?><?php _e('优惠折扣', 'ripro'); ?></span>
                                            <div class="flex flex-row items-center">
                                                <input type="text" class="form-control w-full  dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="post_meta[capalot_vip_rate]" value="<?php echo $post_meta_fields['capalot_vip_rate']; ?>">
                                                <span class="input-group-text w-24 text-center"><?php _e('0.N折', 'ripro'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- down-input-warp -->
                                <div id="down-input-warp" class="meta-input-warp mb-3  rounded border-2 border-dotted  w-full p-2 text-gray-500" style="display: none;">

                                    <div class="mb-3">
                                        <span class="form-label"><i class="fas fa-cloud-download-alt me-1"></i><?php _e('下载信息', 'ripro'); ?></span>
                                    </div>


                                    <div class="meta-input-group">

                                        <?php
                                        foreach ($post_meta_fields['capalot_downurl_new'] as $key => $item) : ?>

                                            <?php
                                            $_name = 'post_meta[capalot_downurl_new][' . $key . ']';
                                            ?>
                                            <div class="meta-input-item grid grid-cols-12 gap-2 mb-2 items-center">
                                                <div class="col-span-1  cursor-pointer py-1 dark:bg-dark text-gray-400">
                                                    <div class="meta-input-item-remove form-control form-control-sm text-center  "><i class="far fa-trash-alt"></i></div>
                                                </div>
                                                <div class="md:col-span-2 col-span-5 col-start-2">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <input type="text" class="form-control w-full   dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" placeholder="<?php _e('资源名称', 'ripro'); ?>" name="<?php echo $_name; ?>[name]" value="<?php echo esc_html($item['name']); ?>">
                                                    </div>
                                                </div>
                                                <div class="md:col-span-3 md:col-start-4 col-span-6 col-start-7">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <input type="text" class="form-control w-full   dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" placeholder="<?php _e('下载密码', 'ripro'); ?>" name="<?php echo $_name; ?>[pwd]" value="<?php echo $item['pwd']; ?>">
                                                    </div>
                                                </div>
                                                <div class="md:col-span-6 md:col-start-7 col-span-12 col-start-1 ">
                                                    <div class="input-group input-group-sm mb-1 flex flex-row">
                                                        <input type="text" class="input-file-url form-control w-full   dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" placeholder="<?php _e('下载地址', 'ripro'); ?>" name="<?php echo $_name; ?>[url]" value="<?php echo $item['url']; ?>">
                                                        <?php if (current_user_can('upload_files')) : ?>
                                                            <button class="add-input-file btn btn-outline-secondary p-2 " type="button"><i class="fas fa-upload"></i></button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>

                                    <div class="mb-3">
                                        <div class="meta-input-item-add form-control form-control-sm text-center border-2  w-full  dark:bg-dark  p-2 rounded  cursor-pointer"><i class="fas fa-plus-circle"></i></div>
                                    </div>

                                    <div class="flex md:flex-row flex-col md:space-x-2 md:space-y-0 spce-x-0 space-y-2">

                                        <div class="md:w-[50%] w-full">
                                            <div class="input-group input-group-sm mb-1">
                                                <span class="input-group-text pb-2"><?php _e('预览地址', 'ripro'); ?></span>
                                                <input type="text" class="form-control w-full   dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" placeholder="<?php _e('选填,格式:按钮名称|Url地址', 'ripro'); ?>" name="post_meta[capalot_demourl]" value="<?php echo $post_meta_fields['capalot_demourl']; ?>">
                                            </div>
                                        </div>
                                        <div class="md:w-[50%] w-full">
                                            <div class="input-group input-group-sm mb-1">
                                                <span class="input-group-text pb-2"><?php _e('自定义按钮', 'ripro'); ?></span>
                                                <input type="text" class="form-control w-full   dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" placeholder="<?php _e('选填,格式:按钮名称|Url地址', 'ripro'); ?>" name="post_meta[capalot_diy_btn]" value=<?php echo $post_meta_fields['capalot_diy_btn']; ?>>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                                <!-- video-input-warp -->
                                <div id="video-input-warp" class="meta-input-warp mb-3 rounded border-2 border-dotted  w-full p-2 text-gray-500" style="display: none;">

                                    <div class="mb-1">
                                        <span class="form-label mb-2"><i class="fas fa-play-circle me-1"></i><?php _e('音视频信息', 'ripro'); ?></span>
                                    </div>
                                    <div class="mb-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" value="" id="capalot_is_video_free" name="post_meta[capalot_is_video_free]" <?php if ($post_meta_fields['capalot_is_video_free'] == 1) echo 'checked'; ?>>
                                            <label class="form-check-label" for="capalot_is_video_free"><?php _e('免费播放', 'ripro'); ?></span></label>
                                        </div>
                                    </div>
                                    <div class="meta-input-group">

                                        <?php
                                        foreach ($post_meta_fields['video_url_new'] as $key => $item) : ?>

                                            <?php
                                            $_name = 'post_meta[video_url_new][' . $key . ']';
                                            ?>
                                            <div class="meta-input-item  grid grid-cols-12 gap-2 mb-2 items-center">
                                                <div class="col-span-1   cursor-pointer py-1 dark:bg-dark text-gray-400">
                                                    <div class="meta-input-item-remove form-control form-control-sm text-center"><i class="far fa-trash-alt"></i></div>
                                                </div>
                                                <div class="md:col-span-2 col-span-5 col-start-2">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <input type="text" class="form-control w-full   dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" placeholder="<?php _e('媒体名称', 'ripro'); ?>" name="<?php echo $_name; ?>[title]" value="<?php echo esc_html($item['title']); ?>">
                                                    </div>
                                                </div>
                                                <div class="md:col-span-3 md:col-start-4 col-span-6 col-start-7">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <input type="text" class="form-control w-full   dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" placeholder="<?php _e('封面地址', 'ripro'); ?>" name="<?php echo $_name; ?>[img]" value="<?php echo esc_html($item['img']); ?>">
                                                    </div>
                                                </div>
                                                <div class="md:col-span-6 md:col-start-7 col-span-12 col-start-1">
                                                    <div class="input-group input-group-sm mb-1 flex flex-row">
                                                        <input type="text" class="input-file-url form-control w-full   dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" placeholder="<?php _e('播放地址', 'ripro'); ?>" name="<?php echo $_name; ?>[src]" value="<?php echo esc_html($item['src']); ?>">
                                                        <?php if (current_user_can('upload_files')) : ?>
                                                            <button class="add-input-file btn btn-outline-secondary p-2" type="button"><i class="fas fa-upload"></i></button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>
                                    <div class="mb-3">
                                        <div class="meta-input-item-add form-control form-control-sm text-center border-2  w-full  dark:bg-dark  p-2 rounded  cursor-pointer"><i class="fas fa-plus-circle"></i></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>


                    </div>
                </div>

                <div class="tougao-sidebar h-full  md:w-[25%] w-full bg-white dark:bg-dark-card p-4  rounded dark:text-gray-400 text-gray-600">
                    <div class="sidebar">
                        <div class="widget">
                            <h5 class="widget-title font-bold"><?php _e('发布', 'ripro'); ?></h5>

                            <div class="widget-body ">
                                <div class="mb-2">
                                    <label class="form-label pb-2 block"><?php _e('状态', 'ripro'); ?></label>
                                    <select name="post_status" class="form-select w-full  dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                                        <?php if (current_user_can('publish_posts')) : ?>
                                            <option value="publish" <?php selected($post_status, 'publish'); ?>><?php _e('已发布', 'ripro'); ?></option>
                                        <?php endif; ?>
                                        <option value="pending" <?php selected($post_status, 'pending'); ?>><?php _e('等待审核', 'ripro'); ?></option>
                                        <option value="draft" <?php selected($post_status, 'draft'); ?>><?php _e('草稿', 'ripro'); ?></option>
                                    </select>
                                </div>
                                <!-- <div class="mb-2">
                                    <label class="form-label pb-2 block"><?php _e('文章形式', 'ripro'); ?></label>
                                    <select name="post_format" class="form-select w-full  dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                                        <option value="" <?php selected($post_format, ''); ?>><?php _e('标准', 'ripro'); ?></option>
                                        <option value="image" <?php selected($post_format, 'image'); ?>><?php _e('图片', 'ripro'); ?></option>
                                        <option value="video" <?php selected($post_format, 'video'); ?>><?php _e('视频', 'ripro'); ?></option>
                                        <option value="audio" <?php selected($post_format, 'audio'); ?>><?php _e('音频', 'ripro'); ?></option>
                                    </select>
                                </div> -->
                                <div class="mb-2">
                                    <label class="form-label pb-2 block"><?php _e('分类栏目', 'ripro'); ?></label>
                                    <?php wp_dropdown_categories(array(
                                        'hide_empty'       => 0,
                                        'orderby'          => 'name',
                                        'name'          => 'post_category',
                                        'hierarchical' => true,
                                        'id'     => 'post_category',
                                        'class'     => 'form-select w-full  dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500',
                                        'show_option_none' => __('选择分类', 'ripro'),
                                        'selected'         => $post_category,
                                    )); ?>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label pb-2 block"><?php _e('文章标签', 'ripro'); ?></label>
                                    <input type="text" class="form-control w-full  dark:border-gray-500 dark:bg-dark  p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="post_tags" value="<?php echo $post_tags; ?>" placeholder="<?php _e('多个标签用,隔开', 'ripro'); ?>">
                                </div>

                                <?php if (current_user_can('upload_files')) : ?>
                                    <div class="mb-2">
                                        <label class="form-label pb-2 block"><?php _e('缩略图', 'ripro'); ?></label>
                                        <input type="hidden" id="_thumbnail_id" name="_thumbnail_id" value="<?php echo $thumbnail_id; ?>">
                                        <div class="tougao_thumbnail border-2 border-dotted w-full  dark:bg-dark text-center p-2 rounded  cursor-pointer">
                                            <?php
                                            $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'thumbnail');
                                            if (empty($thumbnail_url[0])) {
                                                echo '<i class="fas fa-upload"></i>';
                                            } else {
                                                echo '<img src="' . $thumbnail_url[0] . '">';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-4">
                                    <button type="submit" class="btn bg-success w-full text-center py-2 text-white rounded my-2"><?php echo $is_editing ? __('更新文章', 'ripro') : __('发布文章', 'ripro'); ?></button>
                                    <div class="text-center w-100 mt-2"><a href="<?php echo get_uc_menu_link('tougao'); ?>" class=""><?php _e('返回投稿管理', 'ripro'); ?></a></div>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </form>



    </div>
</section>





<!-- // 添加媒体库支持 -->
<?php wp_enqueue_media(); ?>

<script type="text/javascript">
    jQuery(function($) {
        // 监听表单提交事件
        $('#post-form').submit(function(e) {

        });

        const els = document.querySelectorAll('.csf-shortcode-button');
        els.forEach((el) => {
            el.classList.add('hidden')
        })

    });
</script>

<?php get_footer(); ?>
