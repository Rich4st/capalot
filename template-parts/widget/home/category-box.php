<?php

if (empty($args)) {
  return;
}

$config = [
  'slidesPerView' => capalot_is_mobile() ? 2 : 4,
  'spaceBetween' => 30,
  'pagination' => [
    'el' => '.swiper-pagination',
    'clickable' => true
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

?>


<section class="swiper mySwiper max-w-7xl mx-auto dark:bg-dark" data-config='<?php echo json_encode($config); ?>'>
  <div class="swiper-wrapper">

    <?php foreach ($terms as $key => $item) :

      $meta_bg = get_term_meta($item->term_id, 'bg-image', true);

      $bg_img = (!empty($meta_bg)) ? $meta_bg : $def_bg_img;

      $color = capalot_get_color_class($key);
    ?>

      <div class="swiper-slide py-8">
        <div class="h-40 flex justify-center items-center text-white text-center" style="background-image: url(<?php echo $bg_img; ?>);">

          <a href="<?php echo get_term_link($item->term_id, $taxonomy_name); ?>">
            <div>
              <?php if (!empty($args['is_num'])) : ?>
                <span class="bg-<?php echo $color; ?> bg-accent text-sm p-1 rounded-lg"><?php echo $item->count; ?>+</span>
              <?php endif; ?>
              <h3 class="font-semibold mt-1 text-xl"><?php echo $item->name; ?></h3>
            </div>
          </a>

        </div>
      </div>

    <?php endforeach; ?>
  </div>
</section>
