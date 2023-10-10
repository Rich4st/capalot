<?php

if (empty($args)) {
  return;
}

?>

<div class="<?php echo esc_attr($args['bg_style']); ?> text-white text-center py-8 relative" data-bg="<?php echo esc_url($args['bg_img']); ?>" style="background-image: url(<?php echo esc_url($args['bg_img']); ?>);">
  <div class=" absolute inset-0 bg-black opacity-50 z-10"></div>
  <div class="container py-5 m-auto relative z-50">
    <div class=" px-4 lg:px-0">
      <?php if (!empty($args['title'])) : ?>
        <h4 class="text-4xl font-semibold"><?php echo $args['title']; ?></h4>

      <?php endif; ?>
      <?php if (!empty($args['desc'])) : ?>
        <p class="text-gray-300 my-4"><?php echo $args['desc']; ?></p>
      <?php endif; ?>

      <div class="space-x-4 flex gap-4  justify-center">
        <?php foreach ($args['btn_data'] as $key => $item) :
          if (strpos($item['link'], 'http') === false) :
            $item['link'] = 'https://' . $item['link'];
          endif; ?>
          <a class="bgBtn bg-<?php echo esc_attr($item['color']); ?> px-6 rounded-full hover:opacity-70 p-2 w-fit !mx-0" href="<?php echo $item['link']; ?>"><i class="<?php echo esc_attr($item['icon']); ?> me-1"></i><?php echo $item['title']; ?></a>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</div>