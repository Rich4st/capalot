<?php

if (empty($args)) {
  return;
}

$config = [
  'lazy' => true,
  'autoplay' => false,
  'loop'     => false,
  'nav'      => false,
  'dots'     => false,
];
if ($args['config']) {
  foreach ($args['config'] as $key) {
    $config[$key] = true;
  }
}
$config['items'] = absint($args['items']);

if ($config['nav'] === true) {
  $config['navigation'] = [
    'nextEl' => '.swiper-button-next',
    'prevEl' => '.swiper-button-prev'
  ];
}

if ($config['dots'] === true) {
  $config['pagination'] = [
    'el' => '.swiper-pagination', 'clickable' => true
  ];
}
$container = _capalot('site_container_width', '1400');

?>

<section class="dark:bg-dark py-3">
  <div class="swiper mySwiper mx-auto" style="max-width:
   <?php if ($container === '') {
      echo ($args['container'] == 'container-normal') ?   '1280px' : '100%';
    } else {
      echo ($args['container'] == 'container-normal') ?   $container . 'px' : '100%';
    } ?>" data-config='<?php echo json_encode($config); ?>'>
    <div class="swiper-wrapper  ">

      <?php if ($args['data']) :
        foreach ($args['data'] as $item) : ?>

          <div class="swiper-slide text-white ">
            <div class=" relative h-44 md:h-80">
              <img data-src="<?php echo $item['_img']; ?>" src="<?php echo $item['_img']; ?>" class="w-full h-full object-cover">
              <?php echo $args['container']; ?>
              <div class="absolute bottom-1/2 space-y-2 text-center w-full translate-y-1/2 px-10">
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
    <?php if ($config['nav']) : ?>
      <div class="swiper-button-next after:text-white"></div>
      <div class="swiper-button-prev after:text-white"></div>
    <?php endif ?>
    <?php if ($config['dots']) : ?>
      <div class="swiper-pagination"></div>
    <?php endif; ?>
  </div>
</section>