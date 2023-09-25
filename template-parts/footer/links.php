<?php

$site_footer_link = _capalot('site_footer_links', array());

if (is_home() && !empty($site_footer_link)) : ?>
  <div class=" hidden lg:block">
    <div class=" flex justify-center gap-4 text-gray-600 text-sm">
      <span><?php _e('友情链接：', 'ripro'); ?></span>
      <ul class=" flex gap-4 ">
        <?php foreach ($site_footer_link as $item) : ?>
          <?php printf('<li ><a href="%s" target="_blank" class=" hover:text-gray-950" rel="" title="%s">%s</a></li>', $item['href'], $item['title'], $item['title']); ?>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
<?php endif; ?>