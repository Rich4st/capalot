<?php

if (empty($args)) {
  return;
}


$cat_id = intval($args['category']);

// 查询
$query_args = array(
  'cat'                 => $cat_id,
  'ignore_sticky_posts' => false,
  'post_status'         => 'publish',
  'posts_per_page'      => 5,
  'orderby'             => $args['orderby'],
);

//字段排序
if ($args['orderby'] == 'views') {
  $query_args['meta_key'] = 'views';
  $query_args['orderby'] = 'meta_value_num';
  $query_args['order'] = 'DESC';
}

$PostData = new WP_Query($query_args);

$cms_style = $args['style'];
$cms_box_order = (!empty($args['is_box_right'])) ? 'order-first' : '';

// left
$cms_box_item_config = [
  'type' => 'grid-overlay', // grid  grid-overlay list title
  'media_class' => 'ratio-3x2', // ratio-1x1  3x2 3x4 4x3 16x9
  'size' => 'lg'
];

// right
$cms_list_item_config = [
  'type' => $cms_style,
  'media_class' => 'ratio-3x2',
  'is_entry_desc' => false,
  'size' => 'sm'
];

$cms_list_item_rows = ($cms_style == 'list') ? 'grid-cols-1' : 'grid-cols-2';

$container = _capalot('site_container_width', '1400');

?>

<section class="dark:bg-dark">
  <div class="mx-auto" style="max-width: <?php
                                          if ($container === '') {
                                            echo '1280';
                                          } else {
                                            echo $container;
                                          }
                                          ?>px;">
    <?php
    $section_title = (!empty($args['title'])) ? $args['title'] : get_cat_name($cat_id);
    $section_desc = (!empty($args['desc'])) ? $args['desc'] : category_description($cat_id);
    ?>
    <?php if ($section_title) : ?>
      <div class="section-title text-center mb-4 dark:text-white ">
        <h3 class="text-2xl text-black dark:text-gray-50 transition-all hover:ease-in-out cursor-pointer mb-2"><a href="<?php echo get_category_link($cat_id) ?>"><?php echo $section_title ?></a></h3>
        <?php if (!empty($section_desc)) : ?>
          <p class="text-muted mb-0 text-gray-400"><?php echo $section_desc ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>


    <div class="<?php echo esc_attr($cms_style); ?>  grid lg:grid-cols-2 grid-cols-1 gap-4 p-2">

      <?php if ($PostData->have_posts()) : $counter = 0; ?>

        <ul>
          <?php while ($PostData->have_posts() && $counter == 0) : $PostData->the_post(); ?>
            <?php get_template_part('template-parts/loop/item', '', $cms_box_item_config); ?>
          <?php $counter++;
          endwhile; ?>
        </ul>
        <ul class=" <?php echo esc_attr($cms_box_order); ?>">
          <div class="grid  <?php echo esc_attr($cms_list_item_rows); ?>  gap-4 ">
            <?php while ($PostData->have_posts() && $counter < 5) : $PostData->the_post(); ?>
              <?php if ($counter == 0) : continue;
              endif; ?>
              <?php get_template_part('template-parts/loop/item', '', $cms_list_item_config); ?>
            <?php $counter++;
            endwhile; ?>
          </div>
        </ul>

      <?php else : get_template_part('template-parts/loop/item', 'none');
      endif; ?>

    </div>
  </div>

</section>

<?php wp_reset_postdata(); ?>