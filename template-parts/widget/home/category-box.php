<?php

if (empty($args)) {
  return;
}

$config = [
  'slidesPerView' => capalot_is_mobile() ? 2 : 4,
  'spaceBetween' => 30,
  'lazy' => true,
  'autoplay' => true,
  'loop'     => true,
  'navigation' => [
    'nextEl' => ".swiper-button-next",
    'prevEl' => ".swiper-button-prev",
  ],
];

$cat_ids = (array) $args['category'];

$taxonomy_name = 'category';

$__args = array(
  'taxonomy'   => $taxonomy_name,
  'hide_empty' => false,
  'parent'     => 0,
  'include'    => $cat_ids,
  'orderby'    => 'include',
);

$terms = get_terms($__args);

if (is_wp_error($terms) || empty($terms)) {
  return;
}

$def_bg_img = get_template_directory_uri() . '/assets/img/bg.jpg'; //默认缩略图
$container = _capalot('site_container_width', '1400');
?>

<section class="dark:bg-dark py-3 md:px-8 px-0">
  <div class="swiper mySwiper category-swiper mx-auto" data-config='<?php echo json_encode($config); ?>' style="max-width: <?php
                                                                                                              if ($container === '') {
                                                                                                                echo '1280';
                                                                                                              } else {
                                                                                                                echo $container;
                                                                                                              }
                                                                                                              ?>px;">
    <div class="swiper-wrapper">

      <?php foreach ($terms as $key => $item) :

        $meta_bg = get_term_meta($item->term_id, 'bg-image', true);

        $bg_img = (!empty($meta_bg)) ? $meta_bg : $def_bg_img;

        $color = capalot_get_color_class($key);
      ?>

        <div class="swiper-slide py-8 cursor-pointer text-white">
          <div class="rounded h-40 w-full bg-black bg-opacity-30 relative overflow-hidden ">
            <div class="h-full w-full flex justify-center items-center  text-center bg-cover bg-center z-[-1] absolute hover:ease-in-out hover:duration-700 " style="background-image: url(<?php echo $bg_img; ?>);">
              <a href="<?php echo get_term_link($item->term_id, $taxonomy_name); ?>">
                <?php if (!empty($args['is_num'])) : ?>
                  <span class="bg-<?php echo $color; ?> bg-accent  text-sm p-1 rounded-lg"><?php echo $item->count; ?>+</span>
                <?php endif; ?>
                <h3 class="font-semibold mt-1 text-xl truncate md:w-full w-20"><?php echo $item->name; ?></h3>
              </a>
            </div>
          </div>
        </div>

      <?php endforeach; ?>
    </div>
    <div class="swiper-button-next after:text-white"></div>
    <div class="swiper-button-prev after:text-white"></div>
  </div>

</section>