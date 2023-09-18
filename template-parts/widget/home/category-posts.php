<?php

if (empty($args))
  return;

$cat_id = intval($args['category']);

$query_params = array(
  'cat'                 => $cat_id,
  'ignore_sticky_posts' => false,
  'post_status'         => 'publish',
  'posts_per_page'      => (int) $args['count'],
  'orderby'             => $args['orderby'],
);

$PostData = new WP_Query($query_params);

$config = get_posts_style_config();

$container = _capalot('site_container_width', '1400')
?>
<!-- 文章展示页 -->
<section class=" dark:bg-dark">
  <div class="mx-auto" style="max-width: <?php
                                          if ($container === '') {
                                            echo '1280';
                                          } else {
                                            echo $container;
                                          }
                                          ?>px;">
    <?php
    $section_title = get_cat_name($cat_id);
    $section_desc  = category_description($cat_id);
    ?>
    <?php if ($section_title) : ?>
      <div class="section-title text-center mb-4   dark:text-white  ">
        <h3 class="text-2xl text-black dark:text-gray-50 transition-all hover:ease-in-out cursor-pointer mb-2"><a href="<?php echo get_category_link($cat_id) ?>"><?php echo $section_title ?></a></h3>
        <?php if (!empty($section_desc)) : ?>
          <p class="text-muted mb-0 text-gray-400"><?php echo $section_desc ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <ul class="row bg-white dark:bg-dark list-none grid p-4 <?php echo esc_attr($config['row_cols_class']); ?>">
      <?php if ($PostData->have_posts()) :
        while ($PostData->have_posts()) : $PostData->the_post();
          get_template_part('template-parts/loop/item', '', $config);
        endwhile;
      else :
        get_template_part('template-parts/loop/item', 'none');
      endif; ?>
    </ul>

  </div>
</section>

<?php wp_reset_postdata(); ?>
