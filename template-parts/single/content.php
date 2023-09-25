<article <?php post_class('post-content'); ?>>
  <?php
  the_content(
    sprintf(
      wp_kses(
        '继续阅读<span class="screen-reader-text"> "%s"</span>',
        array(
          'span' => array(
            'class' => array(),
          ),
        )
      ),
      wp_kses_post(get_the_title())
    )
  );

  wp_link_pages(
    array(
      'before' => '<div class="custom-nav mb-3"><ul class="pagination d-inline-block d-md-flex justify-content-center"><span class="disabled">' . __('内容分页', 'ripro') . '</span>',
      'after'  => '</ul></div>',
    )
  );
  ?>

  <?php get_template_part('template-parts/single/entry-copyright'); ?>

</article>

<?php get_template_part('template-parts/single/tags') ?>

<?php get_template_part('template-parts/single/social'); ?>
