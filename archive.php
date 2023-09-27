<?php

get_header();

$cat_id = get_queried_object_id();

$archive_item_config = get_posts_style_config();

?>

<?php get_template_part('template-parts/archive-hero'); ?>

<section class="py-16 px-4 dark:bg-dark">
  <?php do_action('ripro_ads', 'ad_archive_top'); ?>

  <ul class="posts-wrap grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 grid-cols-1 md:gap-8 gap-2 <?php echo esc_attr($archive_item_config['row_cols_class']); ?>">
    <?php if (have_posts()) :
      while (have_posts()) : the_post();
        get_template_part('template-parts/loop/item', get_post_format(), $archive_item_config);
      endwhile;
    else :
      get_template_part('template-parts/loop/item', 'none');
    endif;
    ?>
  </ul>

  <?php Capalot_Pagination(); ?>
</section>

<?php
get_footer();
