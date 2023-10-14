<?php

if (empty($args)) {
  return;
}

// 查询
$query_args = array(
  'ignore_sticky_posts' => true,
  'post_status'         => 'publish',
  'posts_per_page'      => (int) $args['count'],
);
//字段排序
if (in_array($args['orderby'], array('views_num', 'likes_num', 'fav_num'))) {
  $meta_ranks = [
    'views_num' => 'views',
    'likes_num' => 'likes',
    'fav_num'   => 'follow_num',
  ];
  $query_args['meta_key'] = $meta_ranks[$args['orderby']];
  $query_args['order']    = 'DESC';
  $query_args['orderby']  = 'meta_value_num';
} elseif ($args['orderby'] == 'down_num') {
  // 下载量排行...
  global $wpdb;
  $post_ids = $wpdb->get_col(
    $wpdb->prepare(
      "SELECT post_id
      FROM {$wpdb->capalot_down} GROUP BY post_id
      ORDER BY COUNT(*) DESC LIMIT %d",
      $args['count']
    )
  );

  if (!empty($post_ids)) {
    $query_args['post__in'] = $post_ids;
    $query_args['orderby']  = 'post__in';
  }
} elseif ($args['orderby'] == 'pay_num') {
  // 购买量排行...
  global $wpdb;
  $post_ids = $wpdb->get_col(
    $wpdb->prepare("SELECT post_id FROM {$wpdb->capalot_order} WHERE status = 1 GROUP BY post_id ORDER BY COUNT(*) DESC LIMIT %d", $args['count'])
  );

  if (!empty($post_ids)) {
    $query_args['post__in'] = $post_ids;
    $query_args['orderby']  = 'post__in';
  }
}

//查询排序


$PostData = new WP_Query($query_args);

?>
<div class=" bg-white p-4 rounded-md my-3 dark:bg-dark-card">

  <h5 class="widget-title mb-4 dark:text-gray-50"><?php echo $args['title']; ?></h5>

  <div class="row g-3 row-cols-1 grid gap-4">
    <?php if ($PostData->have_posts()) : $rank_key = 0;
      while ($PostData->have_posts()) : $PostData->the_post();
        $rank_key++; ?>
        <div class="col ">
          <article class="ranking-item flex flex-row gap-2">
            <div class=" ">
              <span class="ranking-num badge   bg-<?php echo capalot_get_color_class($rank_key); ?> bg-opacity-50 bg-teal-700  px-[11.5px] h-8 leading-8  text-center block rounded-full text-white"><?php echo $rank_key; ?></span>
            </div>

            <h3 class="ranking-title">
              <a class=" leading-8 text-sm line-clamp-1 text-neutral-500 hover:text-neutral-900 dark:text-gray-400 dark:hover:text-white" target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?> "><?php the_title(); ?></a>
            </h3>
          </article>
        </div>
      <?php endwhile;
    else : ?>
      <p class="col mb-0"><?php _e('暂无排行', 'ripro'); ?></p>
    <?php endif; ?>
  </div>

</div>
<?php wp_reset_postdata(); ?>
