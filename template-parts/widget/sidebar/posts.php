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
<div class=" bg-white p-4 rounded-md mb-8 dark:bg-dark-card">

  <h5 class="widget-title mb-4 dark:text-gray-50"><?php echo $args['title']; ?></h5>

  <div class="row g-3 row-cols-1 grid gap-2">
    <?php if ($PostData->have_posts()) :
      while ($PostData->have_posts()) : $PostData->the_post(); ?>

        <div class="col ">
          <article class="post-item item-list flex flex-row gap-4">

            <div class="entry-media ratio ratio-3x2 col-auto w-1/3">
              <a target="<?php echo get_target_blank(); ?>" class="media-img lazy" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" >
                <img class=" w-full h-14 object-cover rounded-md" src="<?php echo capalot_get_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
              </a>
            </div>

            <div class="entry-wrapper w-2/3">
              <div class="entry-body">
                <h2 class="entry-title">
                  <a class=" text-sm line-clamp-2  text-neutral-500 hover:text-neutral-900 dark:text-gray-400 dark:hover:text-white" target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                </h2>
              </div>
            </div>

          </article>
        </div>

    <?php endwhile;
    endif; ?>
  </div>
</div>

<?php wp_reset_postdata(); ?>