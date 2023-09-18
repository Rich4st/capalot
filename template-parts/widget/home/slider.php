<?php

if (empty($args)) {
  return;
}

$config = [
  'lazyLoad' => true,
  'autoplay' => false,
  'loop'     => false,
  'nav'      => false,
  'dots'     => false,
];

foreach ($args['config'] as $key) {
  $config[$key] = true;
}

$config['items'] = absint($args['items']);

?>


<div class="swiper mySwiper <?php echo $args['container'];?>">
  <div class="swiper-wrapper" data-config='<?php echo json_encode($config); ?>'>

    <?php foreach ($args['data'] as $item) : ?>

      <div class="swiper-slide">
        <div>
          <img data-src="<?php echo $item['_img']; ?>" src="<?php echo $item['_img']; ?>">
          <?php echo $args['container']; ?>
          <div>
            <?php echo $item['_desc']; ?>
          </div>
          <?php if (!empty($item['_href'])) : ?>
            <a target="<?php echo $item['_target']; ?>" class="u-permalink" href="<?php echo $item['_href']; ?>"></a>
          <?php endif; ?>
        </div>
      </div>

    <?php endforeach; ?>

  </div>
</div>
