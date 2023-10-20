<?php

if (empty($args)) {
  return;
}


$cat_id = intval($args['category']);

// 查询
$query_args = array(
  'cat'                 => $cat_id,
  'ignore_sticky_posts' => true,
  'post_status'         => 'publish',
  'posts_per_page'      => (int) $args['count'],
  'orderby'             => $args['orderby'],
);

$PostData = new WP_Query($query_args);

?>
<div class=" bg-white p-4 rounded-md my-3 dark:bg-dark-card">

  <p class="widget-title mb-4 dark:text-gray-50"><?php echo $args['title']; ?></p>

  <div class="row g-3 row-cols-1 grid gap-2">
    <?php if ($PostData->have_posts()) :
      while ($PostData->have_posts()) : $PostData->the_post(); ?>

        <div class="col ">
          <div class=" grid grid-cols-6 gap-4 items-center">

            <div class=" col-span-2 sm:col-span-1 lg:col-span-2">
              <a target="<?php echo get_target_blank(); ?>" class="media-img lazy" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                <img class=" w-full h-14 object-cover rounded-md lazy" data-src="<?php echo capalot_get_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
              </a>
            </div>

            <div class=" col-span-4 sm:col-span-5 lg:col-span-4">
              <div class="">
                <h2 class="">
                  <a class=" text-sm line-clamp-2  text-neutral-500 hover:text-neutral-900 dark:text-gray-400 dark:hover:text-white" target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                </h2>
              </div>
            </div>

          </div>
        </div>

    <?php endwhile;
    endif; ?>
  </div>
</div>

<?php wp_reset_postdata(); ?>