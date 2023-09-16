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

?>

<section class=" dark:bg-[#222529]">
  <div class="max-w-7xl mx-auto ">
    <?php
    $section_title = $args['title'];
    $section_desc = $args['desc'];
    ?>
    <?php if ($section_title) : ?>
      <div class="section-title text-center mb-4 text-[#595d69]  dark:text-white  ">
        <h3 class="text-[1.640625rem]  hover:text-black dark:hover:text-white transition-all hover:ease-in-out cursor-pointer mb-[0.5rem]"><?php echo $section_title ?></h3>
        <?php if (!empty($section_desc)) : ?>
          <p class="text-muted mb-0 "><?php echo $section_desc ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <ul class="posts-wrap bg-[#ffffff]  dark:bg-[#222529] list-none grid p-[1rem] <?php echo esc_attr($item_config['row_cols_class']); ?>">
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