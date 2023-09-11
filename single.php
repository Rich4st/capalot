<?php get_header() ?>

<div>
  <div>
    <?php the_content(); ?>
  </div>
  <div class="sidebar">
    <?php dynamic_sidebar('single-sidebar'); ?>
  </div>
</div>

<?php
if (comments_open() || get_comments_number()) :
  comments_template();
endif;
