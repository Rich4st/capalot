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

$config = get_posts_style_config()
?>

<section class="container">
  <?php
  $section_title = get_cat_name($cat_id);
  $section_desc  = category_description($cat_id);
  ?>
  <?php if ($section_title) : ?>
    <div class="section-title text-center mb-4">
      <h3><a href="<?php echo get_category_link($cat_id) ?>"><?php echo $section_title ?></a></h3>
      <?php if (!empty($section_desc)) : ?>
        <p class="text-muted mb-0"><?php echo $section_desc ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="row <?php echo esc_attr($config['row_cols_class']); ?>">
    <?php if ($PostData->have_posts()) :
      while ($PostData->have_posts()) : $PostData->the_post();
        get_template_part('template-parts/loop/item', '', $config);
      endwhile;
    else :
      get_template_part('template-parts/loop/item', 'none');
    endif; ?>
  </div>

</section>

<?php wp_reset_postdata(); ?>
