<?php

if (empty($args)) {
  return;
}

$config = [
  'lazy'=> true,
  'loop'     => true,
  'navigation' => [
    'nextEl' => '.swiper-button-next',
    'prevEl' => '.swiper-button-prev'
  ]
];

foreach ($args['config'] as $key) {
  $config[$key] = true;
}

$config['items'] = absint($args['items']);

$container = _capalot('site_container_width', '1400');


?>

<section class="dark:bg-dark">
<div class="swiper mySwiper  <?php echo $args['container']; ?> mx-auto" data-config='<?php echo json_encode($config); ?>' style="max-width: <?php
                                                                                                if ($container === '') {
                                                                                                  echo '1280';
                                                                                                } else {
                                                                                                  echo $container;
                                                                                                }
                                                                                                ?>px;">
  <div class="swiper-wrapper " >

    <?php if($args['data']):
    foreach ($args['data'] as $item) : ?>

      <div class="swiper-slide text-white ">
        <div class=" relative h-44 md:h-80">
          <img data-src="<?php echo $item['_img']; ?>" src="<?php echo $item['_img']; ?>" class="w-full h-full object-cover">
          <?php echo $args['container']; ?>
          <div class="absolute bottom-1/2 space-y-2 text-center w-full translate-y-1/2 px-10" >
            <?php echo $item['_desc']; ?>
          </div>
          <?php if (!empty($item['_href'])) : ?>
            <a target="<?php echo $item['_target']; ?>" class="u-permalink " href="<?php echo $item['_href']; ?>"></a>
          <?php endif; ?>
        </div>
      </div>

    <?php endforeach;
    endif; ?>

  </div>
  <div class="swiper-button-next after:text-white"></div>
  <div class="swiper-button-prev after:text-white"></div>
</div>
</section>
