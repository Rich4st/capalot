<?php get_header(); ?>
<?php

if (is_active_sidebar('home-module')) {
  dynamic_sidebar('home-module');
} else {
  if (have_posts()) {
    while (have_posts()) {
      the_post();
    }
  }
}

get_footer();
