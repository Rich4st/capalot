<?php

if (empty($args)) {
  return;
}

?>

<div class="<?php echo esc_attr($args['bg_style']); ?> text-white text-center py-4" data-bg="<?php echo esc_url($args['bg_img']); ?>"
  style="background-image: url(<?php echo esc_url($args['bg_img']); ?>);">
  <div class="container py-5">
    <?php if (!empty($args['title'])) : ?>
      <h4 class="text-4xl font-semibold"><?php echo $args['title']; ?></h4>

    <?php endif; ?>
    <?php if (!empty($args['desc'])) : ?>
      <p class="text-gray-400 my-4"><?php echo $args['desc']; ?></p>
    <?php endif; ?>

    <div class="space-x-4">
      <?php foreach ($args['btn_data'] as $key => $item) : ?>
        <a class="bg-<?php echo esc_attr($item['color']); ?> rounded-full hover:opacity-70 p-2 w-fit" href="<?php echo $item['link']; ?>"><i class="<?php echo esc_attr($item['icon']); ?> me-1"></i><?php echo $item['title']; ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
