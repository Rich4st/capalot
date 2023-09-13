<?php

$content = $args;
$post_id = get_the_ID();
$user_id = get_current_user_id();

// 用户是否已购买或可以免费获取
$user_pay_post_status = get_user_pay_post_status($user_id, $post_id);
// 是否免费资源并且需要登录后查看
$is_user_login_get_status = $user_pay_post_status === '0' && empty($user_id);

?>

<div class="ri-hide-warp">
  <?php if ($user_pay_post_status && !$is_user_login_get_status) : ?>
    <span class="hide-msg"><i class="fas fa-unlock me-1"></i><?php echo '已获得查看权限'; ?></span>
    <?php echo $content; ?>
  <?php else : ?>
    <span class="hide-msg"><i class="fas fa-lock me-1"></i><?php echo '隐藏内容'; ?></span>
    <div class="hide-buy-warp">
      <?php if ($is_user_login_get_status) : ?>
        <div class="buy-title"><i class="fas fa-lock me-1"></i><?php _e('本内容登录后免费查看', 'capalot'); ?></div>
        <div class="buy-btns">
          <a rel="nofollow noopener noreferrer" href="<?php echo esc_url(wp_login_url(get_current_url())); ?>" class="btn btn-info px-4 rounded-pill"><i class="far fa-user me-1"></i><?php _e('登录后查看', 'capalot'); ?></a>
        </div>

      <?php else : ?>
        <div class="buy-title"><i class="fas fa-lock me-1"></i><?php _e('本内容需权限查看', 'capalot'); ?></div>
        <div class="buy-btns">
          <button class="btn btn-danger px-4 rounded-pill js-pay-action" data-id="<?php echo $post_id; ?>" data-type="1" data-info="">
            <i class="fab fa-shopify me-1"></i>
            <?php _e('购买查看权限', 'capalot'); ?>
          </button>
        </div>

        <div class="buy-desc">

          <ul class="prices-info">
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
                $__price_span = '<span><i class="fas ' . '金币' . ' me-1"></i>' . $coin_price . '金币' . '<sup class="ms-1">' . sprintf(__('%s折', 'capalot'), $__rate) . '<sup></span>';
              } else {
                $__price_span = '<span><i class="fas ' . '金币' . ' me-1"></i>' . $coin_price . '金币' . '</span>';
              }

              echo '<li class="price-item ' . $type . '">' . $price_names[$type] . ': ' . $__price_span . '</li>';
            } ?>
          </ul>

        </div>

        <?php
        $sales_count = absint(get_post_meta($post_id, 'cao_paynum', true));
        if ($sales_count > 0) {
          echo '<div class="buy-count"><i class="fab fa-hotjar me-1"></i>' . sprintf(__('已有<span>%d</span>人解锁查看', 'capalot'), $sales_count) . '</div>';
        }
        ?>
      <?php endif; ?>

    </div>

  <?php endif; ?>
</div>
