<?php

if(empty($args))
  return;

$query_params = array(
  'cat'                 => intval($args['category']),
  'ignore_sticky_posts' => false,
  'post_status'         => 'publish',
  'posts_per_page'      => (int) $args['count'],
  'orderby'             => $args['orderby'],
);

$posts = new WP_Query($query_params);

