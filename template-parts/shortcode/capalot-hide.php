<?php

$content = $args;
$post_id = get_the_ID();
$user_id = get_current_user_id();

// 用户是否已购买或可以免费获取
$user_pay_post_status = get_user_pay_post_status($user_id, $post_id);
// 是否免费资源并且需要登录后查看
$is_user_login_get_status = $user_pay_post_status === '0' && empty($user_id);

?>

<div class=" p-4 border-dashed border-2 border-rose-200 rounded-md my-4 bg-gray-100 overflow-hidden relative dark:bg-dark dark:border-gray-600">
  <?php if ($user_pay_post_status && !$is_user_login_get_status) : ?>
    <div class="absolute right-0 top-0 px-2 bg-rose-300 text-white z-40 rounded-tr-md rounded-bl-md"><span class=" text-sm  "><?php echo '已获得查看权限'; ?></span></div>
    <?php echo $content; ?>
  <?php else : ?>
    <span class="font-bold text-[18px] leading-6 relative pl-[18px] border-l-[#3370e9] border-l-2"><?php echo '隐藏内容'; ?></span>
    <div class="my-2 border-2 border-dashed border-[#ffb1cb] rounded-[0.5rem] py-[1.5rem] px-[1rem] text-center block">
      <?php if ($is_user_login_get_status) : ?>
        <div class="text-[#ff5722] text-[1.25rem] mb-[1rem] flex justify-center items-center"><i class="fas fa-lock me-1"></i><?php _e('本内容登录后免费查看', 'capalot'); ?></div>
        <div class="text-[#ff5722] text-[1.25rem] mb-[1rem] flex justify-center items-center leading-[1.5rem]">
          <a rel="nofollow noopener noreferrer" href="<?php echo esc_url(wp_login_url(get_current_url())); ?>" class="btn btn-info px-4 rounded-pill"><i class="far fa-user me-1"></i><?php _e('登录后查看', 'capalot'); ?></a>
        </div>

      <?php else : ?>
        <div class="text-[#ff5722] text-[1.25rem] mb-[1rem] flex justify-center items-center leading-[1.5rem]"><i class="fas fa-lock me-1"></i><?php _e('本内容需权限查看', 'capalot'); ?></div>
        <div class="mb-[1rem] text-white text-center">
          <button
          class="js-pay-action cursor-pointer bg-[#d6293e] px-[1.5rem] rounded-full hover:bg-[#b62335] p-2 block my-0 mx-[auto]" data-id="<?php echo $post_id; ?>" data-type="1" data-info="">
            <i class="fab fa-shopify me-1"></i>
            <?php _e('购买查看权限', 'capalot'); ?>
          </button>
        </div>

        <div class="text-[0.95rem] p-[0.5rem] leading-6 md:w-[550px]  w-full bg-[#eaf6ff] text-center block my-0 mx-[auto]">

          <ul class="">
            <?php
            $site_vip_options = get_site_vip_options();
            $price_names = [
              'default' => __('原价', 'capalot'),
              'no' => $site_vip_options['no']['name'],
              'vip' => $site_vip_options['vip']['name'],
              'boosvip' => $site_vip_options['boosvip']['name'],
            ];
            //价格组
            $post_price_data = get_post_price_data($post_id);

            $default_price = $post_price_data['default'];

            foreach ($post_price_data as $type => $coin_price) {
              if ($type == 'default') {
                continue;
              }

              if ($coin_price === false) {
                $__price_span = '<span>' . __('不可购买', 'capalot') . '</span>';
              } elseif ($coin_price == 0) {
                $__price_span = '<span>' . __('免费', 'capalot') . '</span>';
              } elseif ($coin_price < $default_price) {
                $__rate = $coin_price / $default_price * 10;
                $__price_span = '<span class="text-[#f7c32e]"><i class="fas ' . '金币' . ' me-1"></i>' . $coin_price . '金币' . '<sup class="ms-1">' . sprintf(__('%s折', 'capalot'), $__rate) . '<sup></span>';
              } else {
                $__price_span = '<span><span class="dashicons dashicons-database text-[1rem]"></span><i class="fas ' . '金币' . ' me-1"></i>' . $coin_price . '金币' . '</span>';
              }

              echo '<li class="inline-block px-2  ' . $type . '">' . $price_names[$type] . ': ' . $__price_span . '</li>';
            } ?>
          </ul>

        </div>

        <?php
        $sales_count = absint(get_post_meta($post_id, 'capalot_paynum', true));
        if ($sales_count > 0) {
          echo '<div class="text-[#8d9da9] text-[0.9rem] mt-[1rem]"><i class="fab fa-hotjar me-1"></i>' . sprintf(__('已有<span class="text-[#ff5722] mx-[2px] text-[0.875rem]">%d</span>人解锁查看', 'capalot'), $sales_count) . '</div>';
        }
        ?>
      <?php endif; ?>

    </div>

  <?php endif; ?>
</div>
