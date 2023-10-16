<?php

if (empty(_capalot('is_site_footer_widget', true))) {
  return;
}

?>

<div class=" hidden lg:block">

  <div class=" grid grid-cols-6 gap-8 pb-4">
    <div class=" col-span-2">
      <div class="">
        <?php

        $logo_img  = _capalot('site_footer_logo', get_template_directory_uri() . '/assets/img/logo.png');
        $site_dese = _capalot('site_footer_desc', 'RiPro-V5是一款强大的Wordpress资源商城主题，支持付费下载、付费播放音视频、付费查看等众多功能。');
        echo '<a href="' . esc_url(home_url('/')) . '"><img class="logo regular max-h-16" src="' . esc_url($logo_img) . '" alt="' . esc_attr(get_bloginfo('name')) . '"></a>';
        ?>
      </div>
      <p class=" text-sm text-gray-600 dark:text-gray-400"><?php echo $site_dese; ?></p>
    </div>

    <div class=" ">
      <h4 class=" text-lg dark:text-gray-50"><?php _e('快速导航', 'ripro'); ?></h4>
      <ul class=" text-gray-600 text-sm leading-6 dark:text-gray-400">
        <?php foreach (_capalot('site_footer_widget_link1', array()) as $item) {
          $link_item = '<li>
          <a href="' . $item['href'] . '" class=" hover:text-gray-950 hover:dark:text-gray-200 line-clamp-1">
          ' .  $item['title'] . '</a>
          </li>';

          if ($item['title'] === '标签云' &&  _capalot('is_site_tags_page')) {
            echo  $link_item;
          } elseif($item['title'] === '网址导航' && _capalot('is_site_link_manager_page')){
            echo $link_item;
          } elseif($item['title'] === '个人中心'){
            echo $link_item;
          }
        } ?>
      </ul>
    </div>

    <div class="">
      <h4 class=" text-lg dark:text-gray-50"><?php _e('关于本站', 'ripro'); ?></h4>
      <ul class=" text-gray-600 text-sm leading-6 dark:text-gray-400">
        <?php foreach (_capalot('site_footer_widget_link2', array()) as $item) {
          $link_item = '<li>
          <a href="' . $item['href'] . '" class=" hover:text-gray-950 hover:dark:text-gray-200 line-clamp-1">
          ' .  $item['title'] . '</a>
          </li>';
          if($item['title'] === 'VIP介绍' && _capalot('is_site_vip_price_page')){
            echo $link_item;
          }elseif($item['title'] === '客服咨询'){
            echo $link_item;
          }elseif($item['title'] === '推广计划'){
            echo $link_item;
          }
        } ?>
      </ul>
    </div>

    <div class=" col-span-2">
      <h4 class="widget-title dark:text-gray-50"><?php _e('联系我们', 'ripro'); ?></h4>
      <div class=" text-sm text-gray-600 dark:text-gray-400"><?php echo _capalot('site_contact_desc'); ?></div>
    </div>
  </div>
</div>