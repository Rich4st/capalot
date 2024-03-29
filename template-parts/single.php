<?php

$post_id = get_the_ID();

$bg_image = capalot_get_thumbnail_url($post_id);
$single_style = _capalot('single_style', 'hero'); //general hero

$container = _capalot('site_container_width', '1400')
?>

<?php if (!empty(_capalot('single_top_breadcrumb', false))) : ?>
  <!-- 面包屑 -->
  <div class="container-full  dark:bg-dark   dark:text-gray-400">
    <div class="mx-auto" style="max-width: <?php
                                            if ($container === '') {
                                              echo '1280';
                                            } else {
                                              echo $container;
                                            }
                                            ?>px;">
      <nav class="container hidden md:flex flex-col  p-2 " aria-label="breadcrumb">
        <?php capalot_get_breadcrumb('breadcrumb mb-0 flex'); ?>
      </nav>
    </div>
    <?php
    if ($single_style === 'hero') {
      get_template_part('template-parts/single/hero');
    }
    ?>
  </div>
  </div>
<?php endif; ?>


<!--  TODO:视频模块 -->

<div class="dark:bg-dark md:px-8 p-0">
  <div style="max-width: <?php
                          if ($container === '') {
                            echo '1280';
                          } else {
                            echo $container;
                          }
                          ?>px;" class="mx-auto flex lg:flex-row flex-col justify-center p-2 md:px-0 md:py-4 dark:bg-dark w-full">
    <div class=" lg:w-[75%] w-full  text-slate-700 dark:text-gray-400">

      <div class="bg-white  prose-sm dark:bg-dark-card p-2 lg:p-4 rounded my-3 relative" style="padding-bottom:4rem;">
        <?php
        if ($single_style === 'general') {

          the_title('<h1 class="post-title mb-2 lg:mb-3">', '</h1>');
          get_template_part('template-parts/single/meta');
        }
        ?>
        <?php get_template_part('template-parts/single/content') ?>
      </div>

      <?php get_template_part('template-parts/single/entry-navigation');?>

      <?php get_template_part('template-parts/single/entry-related-posts');?>



      <!-- 评论 -->
      <?php
        if (comments_open() || get_comments_number()) :
          comments_template();
        endif;
      ?>
    </div>

    <div class=" sidebar lg:w-[25%] w-full lg:mx-4  mb-[1.5rem] ">
      <?php dynamic_sidebar('single-sidebar'); ?>
    </div>
  </div>
</div>
