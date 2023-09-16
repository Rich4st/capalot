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

<h5 class="widget-title"><?php echo $args['title']; ?></h5>

<div class="row g-3 row-cols-1">
  <?php if ($PostData->have_posts()) :
    while ($PostData->have_posts()) : $PostData->the_post(); ?>

      <div class="col">
        <article class="post-item item-list">

          <div class="entry-media ratio ratio-3x2 col-auto">
            <a target="<?php echo get_target_blank(); ?>" class="media-img lazy" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" data-bg="<?php echo capalot_get_thumbnail_url(); ?>"></a>
          </div>

          <div class="entry-wrapper">
            <div class="entry-body">
              <h2 class="entry-title">
                <a target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
              </h2>
            </div>
          </div>

        </article>
      </div>

  <?php endwhile;
  endif; ?>
</div>

<?php wp_reset_postdata(); ?>
