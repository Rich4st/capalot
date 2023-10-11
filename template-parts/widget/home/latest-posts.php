<?php

if (empty($args))
  exit;

$query_args = array(
  'paged' => get_query_var('paged', 1),
  'ignore_sticky_posts' => false,
  'post_status' => 'publish',
  // 'category__not_in' => $args['no_cat'] ?? [],
);

$PostData = new WP_Query($query_args);

$item_config = get_posts_style_config();

$container = _capalot('site_container_width', '1400')
?>

<section class=" dark:bg-dark py-3">
  <div class="mx-auto" style="max-width: <?php
                                          if ($container === '') {
                                            echo '1280';
                                          } else {
                                            echo $container;
                                          }
                                          ?>px;">
    <?php
    $section_title = $args['title'];
    $section_desc = $args['desc'];
    ?>
    <?php if ($section_title) : ?>
      <div class="section-title text-center mb-4 dark:text-white  ">
        <h3 class="text-2xl text-black dark:text-gray-50 transition-all hover:ease-in-out cursor-pointer mb-2"><?php echo $section_title ?></h3>
        <?php if (!empty($section_desc)) : ?>
          <p class="text-muted mb-0 text-gray-400 "><?php echo $section_desc ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <ul class="posts-wrap   dark:bg-dark list-none grid p-2 <?php echo esc_attr($item_config['row_cols_class']); ?>">
      <?php if ($PostData->have_posts()) :
        while ($PostData->have_posts()) : $PostData->the_post();
          get_template_part('template-parts/loop/item', '', $item_config);
        endwhile;
      else :
        get_template_part('template-parts/loop/item', 'none');
      endif; ?>
    </ul>

    <?php if (!empty($args['is_pagination'])) : ?>
      <?php Capalot_Pagination(); ?>
    <?php endif; ?>
  </div>

</section>

<?php wp_reset_postdata(); ?>