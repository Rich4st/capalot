<?php

if (empty($args))
  exit;

$title = $args['title'];
$desc = $args['desc'];

$query_args = array(
  'paged' => get_query_var('paged', 1),
  'ignore_sticky_posts' => false,
  'post_status' => 'publish',
);

$post_data = new WP_Query($query_args);

?>


<div class="max-w-7xl mx-auto">
  <h2 class="text-3xl font-bold text-center"><?php echo $title; ?></h2>
  <p class="text-center text-gray-500"><?php echo $desc; ?></p>
  <ul class="post-wrap grid grid-cols-1 md:grid-cols-4 md:gap-8 my-8">
    <?php if ($post_data->have_posts()) :
      while ($post_data->have_posts()) : $post_data->the_post(); ?>
        <li class="p-10 shadow-[0_10px_20px_rgba(240,_46,_170,_0.7)]">
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </li>
      <?php endwhile; ?>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
  </ul>

  <?php if (!empty($args['is_pagination'])) : ?>
    <?php Capalot_Pagination(); ?>
  <?php endif; ?>
</div>
