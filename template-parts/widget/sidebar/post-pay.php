<?php

if (empty($args))
  exit;

$footer_text = $args['footer_text'];

$post_id = get_the_ID();
$user_id = get_current_user_id();

// 用户是否已购买或者可免费获取
$user_pay_post_status = get_user_pay_post_status($user_id, $post_id);
//是否免费资源并且需要登录后查看
$is_user_login_get_status = $user_pay_post_status === true && empty($user_id);

//下载地址格式化
$capalot_downurl_new = get_post_meta($post_id, 'capalot_downurl_new', true);
if (!empty($capalot_downurl_new) && is_array($capalot_downurl_new)) {
  foreach ($capalot_downurl_new as $key => $item) {
    $capalot_downurl_new[$key]['name'] = (!empty($item['name'])) ? trim($item['name']) : '下载地址' . ($key + 1);
    $capalot_downurl_new[$key]['pwd']  = (!empty($item['pwd'])) ? $item['pwd'] : '';
    $capalot_downurl_new[$key]['url']  = get_post_endown_url($post_id, $key);
  }
} else {
  $capalot_downurl_new = array();
}

?>
<div class=" bg-white rounded-md p-4 my-3  relative overflow-hidden dark:bg-dark-card">
  <div class="ri-down-warp" data-resize="<?php echo esc_attr($args['resize_position']); ?>">
    <div class=" bg-teal-500 text-white text-sm text-center leading-6 rotate-45 w-60 absolute right-[-6rem] top-3"><span class="down-msg"><?php _e('下载', 'ripro'); ?></span></div>
    <?php if ($user_pay_post_status && !$is_user_login_get_status) : ?>

      <div class="down-buy-warp pt-4">
        <div class="buy-title text-center text-teal-500"><i class="fa-solid fa-lock-open mr-2"></i><?php echo $text = ($user_pay_post_status === true) ? __('免费下载', 'ripro') : __('已获得下载权限', 'ripro'); ?></div>
        <?php if (!empty($capalot_downurl_new)) : ?>
          <div class="d-grid gap-2 mt-3 ">
            <?php foreach ($capalot_downurl_new as $item) : ?>
              <a target="_blank" href="<?php echo esc_attr($item['url']); ?>" class="btn btn-lg btn-success rounded-2 text-xl text-center rounded-md py-3 px-2 bg-teal-500 text-white block hover:bg-teal-600" rel="nofollow noopener noreferrer"><i class="fas fa-cloud-download-alt me-1"></i><?php echo $item['name']; ?></a>
              <?php if (!empty($item['pwd'])) : ?>
                <div class=" py-2 text-center text-gray-400">
                  密码:
                  <span id="copy_span" class="copy-pwd copy_p cursor-pointer" data-pwd="<?php echo esc_attr($item['pwd']); ?>">
                    <?php echo esc_attr($item['pwd']); ?>
                  </span>
                  <i id="copy_btn" class="fa-regular fa-copy cursor-pointer"></i>
                </div>
              <?php endif; ?>
            <?php endforeach ?>
          </div>
        <?php endif; ?>
      </div>

    <?php else : ?>
      <div class="down-buy-warp">
        <?php if ($is_user_login_get_status) : ?>
          <div class="text-center text-teal-500">
            <i class="fa-solid fa-lock"></i>
            <?php _e('本资源登录后免费下载', 'ripro'); ?>
          </div>
          <div class="buy-btns  text-center text-sm">
            <a rel="nofollow noopener noreferrer" href="<?php echo esc_url(wp_login_url(get_current_url())); ?>" class="btn btn-info m-auto my-3 inline-block bg-sky-500 text-white px-6 py-2 rounded-full hover:bg-sky-600"><i class="fa-solid fa-user mr-1"></i><?php _e('登录后下载', 'ripro'); ?></a>
          </div>

        <?php else : ?>
          <div class="buy-title text-center text-teal-500 ">
            <i class="fa-solid fa-lock"></i><?php _e('本资源需权限下载', 'ripro'); ?>
          </div>
          <div class=" text-center mt-4">
            <button class=" hover:bg-[#b62335] text-white py-2 px-4 bg-[#d6293e] rounded-full js-pay-action" data-id="<?php echo $post_id; ?>" data-type="1" data-info=""><i class="fa-brands fa-shopify"></i><?php _e('购买下载权限', 'ripro'); ?></button>
          </div>


          <div class=" mt-2 mb-4">
            <div class=" translate-y-1 opacity-70">
              <svg t="1695893791005" class="icon m-auto " viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4005" width="20" height="20">
                <path d="M27.273 753.613l485.222-484.233 484.233 485.222z" fill="#a2e9f5" p-id="4006"></path>
              </svg>
            </div>
            <ul class=" leading-10 p-4 bg-sky-100 text-sm  divide-gray-200 divide-y dark:bg-dark dark:divide-[#333]  ">
              <?php
              $site_vip_options = get_site_vip_options();
              $price_names = [
                'default' => __('原价', 'ripro'),
                'no' => $site_vip_options['no']['name'],
                'vip' => $site_vip_options['vip']['name'],
                'boosvip' => $site_vip_options['boosvip']['name'],
              ];
              //价格组
              $post_price_data = get_post_price_data($post_id);

              $default_price = $post_price_data['default'];

              foreach ($post_price_data as $type => $coin_price) {
                // zb_dump($type,$coin_price);
                if ($type == 'default') {
                  continue;
                }

                if ($coin_price === false) {
                  $__price_span = '<span>' . __('不可购买', 'ripro') . '</span>';
                } elseif ($coin_price == 0) {
                  $__price_span =   __('免费', 'ripro');
                } elseif ($coin_price < $default_price) {
                  $__rate = $coin_price / $default_price * 10;
                  $__price_span = $coin_price . get_site_coin_name() . '<sup class="ms-1">' . sprintf(__('%s折', 'ripro'), $__rate) . '<sup></span>';
                } else {
                  $__price_span = $coin_price . get_site_coin_name() . '</span>';
                }

                echo '<li class=" flex justify-between text-gray-600 dark:text-gray-400  ' . $type . '">' .  $price_names[$type] .  ': ' . '<span>' .  $__price_span .  '</span>' . '</li>';
              } ?>
            </ul>

          </div>

          <?php
          $sales_count = absint(get_post_meta($post_id, 'capalot_paynum', true));
          if (!empty($args['is_sales_count']) && $sales_count > 0) {
            echo '<div class=" text-gray-400 text-sm text-center"><i class="fab fa-hotjar me-1"></i>' . sprintf(__('已有<span class=" text-sky-400">%d</span>人解锁下载', 'ripro'), $sales_count) . '</div>';
          }
          ?>
        <?php endif; ?>

      </div>

    <?php endif; ?>



    <div class="down-buy-info">

      <?php
      $capalot_info = get_post_meta($post_id, 'capalot_info', true);
      $capalot_demourl = trim(get_post_meta($post_id, 'capalot_demourl', true));
      $capalot_diy_btn = array_filter(explode('|', get_post_meta($post_id, 'capalot_diy_btn', true)));
      $sales_count = absint(get_post_meta($post_id, 'capalot_paynum', true));

      $btns = []; //DIY按钮
      if (!empty($capalot_demourl)) {
        $btns[] = array('name' => __('查看预览', 'ripro'), 'url' => $capalot_demourl);
      }
      if (!empty($capalot_diy_btn)) {
        $btns[] = ['name' => $capalot_diy_btn[0], 'url' => $capalot_diy_btn[1]];
      }
      if (empty($capalot_info)) {
        $capalot_info = array();
      }

      if (!empty($args['is_sales_count']) && $sales_count > 0) {
        array_unshift($capalot_info, array('title' => __('累计销量', 'ripro'), 'desc' => $sales_count));
      }
      if (!empty($args['is_modified_date'])) {
        array_unshift($capalot_info, array('title' => __('最近更新', 'ripro'), 'desc' => get_the_modified_time('Y-m-d')));
      }
      if (!empty($args['is_downurl_count']) && !empty($capalot_downurl_new) && count($capalot_downurl_new)) {
        array_unshift($capalot_info, array('title' => __('包含资源', 'ripro'), 'desc' => sprintf(__('(%d个)', 'ripro'), count($capalot_downurl_new))));
      }
      ?>

      <?php if (!empty($btns)) : ?>
        <div class="d-grid gap-4 mt-3 grid text-center">
          <?php foreach ($btns as $item) : ?>
            <a target="_blank" href="<?php echo esc_attr($item['url']); ?>" class="btn btn-secondary-soft rounded-pill bg-gray-200 rounded-full block p-2 hover:bg-gray-700 hover:text-white dark:bg-dark dark:text-gray-400 hover:dark:bg-gray-700 dark:hover:text-gray-50" rel="nofollow noopener noreferrer"><i class="fas fa-link me-1"></i><?php echo esc_attr($item['name']); ?></a>
          <?php endforeach ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($capalot_info)) : ?>
        <ul class="list-group list-group-flush mt-3 text-sm text-gray-500 divide-y leading-10 dark:divide-neutral-700">
          <?php foreach ($capalot_info as $item) : ?>
            <li class="small text-muted list-group-item "><span><?php echo $item['title']; ?>: </span> <span><?php echo $item['desc']; ?></span></li>
          <?php endforeach ?>
        </ul>
      <?php endif; ?>

      <?php if (!empty($args['footer_text'])) : ?>
        <p class="text-muted mb-0 mt-3 small text-sm
        text-gray-500"><?php echo $args['footer_text']; ?></p>
      <?php endif; ?>
    </div>

  </div>
</div>
