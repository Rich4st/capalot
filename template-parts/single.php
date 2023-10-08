<?php

$post_id = get_the_ID();

$bg_image = capalot_get_thumbnail_url($post_id);
$single_style = _capalot('single_style', 'general'); //general hero

?>

<?php if (!empty(_capalot('single_top_breadcrumb', false))) : ?>
  // TODO: 面包屑
<?php endif; ?>


<?php

$container = _capalot('site_container_width', '1400')
// TODO:视频模块
?>
<div class="bg-[#ededed] dark:bg-dark">
  <div style="max-width: <?php
                          if ($container === '') {
                            echo '1280';
                          } else {
                            echo $container;
                          }
                          ?>px;" class="mx-auto flex lg:flex-row flex-col justify-center p-2 lg:py-4 dark:bg-dark w-full">
    <div class=" lg:w-[75%] w-full  text-slate-700 dark:text-gray-400">

      <div class="bg-white  prose-sm dark:bg-dark-card p-2 lg:p-4 rounded mb-8 relative" style="padding-bottom:4rem;">
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
