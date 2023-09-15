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

?>

<div class="ri-down-warp" data-resize="<?php echo esc_attr($args['resize_position']); ?>">
  <span class="down-msg"><?php _e('下载', 'ripro'); ?></span>
  <?php if ($user_pay_post_status && !$is_user_login_get_status) : ?>

    <div class="down-buy-warp">
      <div class="buy-title"><i class="fa-solid fa-lock-open"></i><?php echo $text = ($user_pay_post_status === true) ? __('免费下载', 'ripro') : __('已获得下载权限', 'ripro'); ?></div>
      <?php if (!empty($capalot_downurl_new)) : ?>
        <div class="d-grid gap-2 mt-3">
          <?php foreach ($capalot_downurl_new as $item) : ?>
            <a target="_blank" href="<?php echo esc_attr($item['url']); ?>" class="btn btn-lg btn-success rounded-2" rel="nofollow noopener noreferrer"><i class="fas fa-cloud-download-alt me-1"></i><?php echo $item['name']; ?></a>
            <?php if (!empty($item['pwd'])) : ?>
              <span class="text-muted user-select-all copy-pwd" data-pwd="<?php echo esc_attr($item['pwd']); ?>"><?php _e('密码: ', 'ripro'); ?><?php echo esc_attr($item['pwd']); ?><i class="far fa-copy ms-1"></i></span>
            <?php endif; ?>
          <?php endforeach ?>
        </div>
      <?php endif; ?>
    </div>

  <?php else : ?>
    <div class="down-buy-warp">
      <?php if ($is_user_login_get_status) : ?>
        <div class="buy-title"><i class="fas fa-lock me-1"></i><?php _e('本资源登录后免费下载', 'ripro'); ?></div>
        <div class="buy-btns">
          <a rel="nofollow noopener noreferrer" href="<?php echo esc_url(wp_login_url(get_current_url())); ?>" class="btn btn-info px-4 rounded-pill"><i class="far fa-user me-1"></i><?php _e('登录后下载', 'ripro'); ?></a>
        </div>

      <?php else : ?>
        <div class="buy-title"><i class="fas fa-lock me-1"></i></i><?php _e('本资源需权限下载', 'ripro'); ?></div>
        <div class="buy-btns">
          <button class="btn btn-danger px-4 rounded-pill js-pay-action" data-id="<?php echo $post_id; ?>" data-type="1" data-info=""><i class="fab fa-shopify me-1"></i><?php _e('购买下载权限', 'ripro'); ?></button>
        </div>

        <div class="buy-desc">

          <ul class="prices-info">
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
                $__price_span = '<span>' . __('免费', 'ripro') . '</span>';
              } elseif ($coin_price < $default_price) {
                $__rate = $coin_price / $default_price * 10;
                $__price_span = $coin_price . get_site_coin_name() . '<sup class="ms-1">' . sprintf(__('%s折', 'ripro'), $__rate) . '<sup></span>';
              } else {
                $__price_span = $coin_price . get_site_coin_name() . '</span>';
              }

              echo '<li class="price-item ' . $type . '">' . $price_names[$type] . ': ' . $__price_span . '</li>';
            } ?>
          </ul>

        </div>

        <?php
        $sales_count = absint(get_post_meta($post_id, 'capalot_paynum', true));
        if (!empty($args['is_sales_count']) && $sales_count > 0) {
          echo '<div class="buy-count"><i class="fab fa-hotjar me-1"></i>' . sprintf(__('已有<span>%d</span>人解锁下载', 'ripro'), $sales_count) . '</div>';
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
      <div class="d-grid gap-2 mt-3">
        <?php foreach ($btns as $item) : ?>
          <a target="_blank" href="<?php echo esc_attr($item['url']); ?>" class="btn btn-secondary-soft rounded-pill" rel="nofollow noopener noreferrer"><i class="fas fa-link me-1"></i><?php echo esc_attr($item['name']); ?></a>
        <?php endforeach ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($capalot_info)) : ?>
      <ul class="list-group list-group-flush mt-3">
        <?php foreach ($capalot_info as $item) : ?>
          <li class="small text-muted list-group-item bg-white"><span><?php echo $item['title']; ?>: </span> <span><?php echo $item['desc']; ?></span></li>
        <?php endforeach ?>
      </ul>
    <?php endif; ?>

    <?php if (!empty($args['footer_text'])) : ?>
      <p class="text-muted mb-0 mt-3 small"><?php echo $args['footer_text']; ?></p>
    <?php endif; ?>
  </div>

</div>
