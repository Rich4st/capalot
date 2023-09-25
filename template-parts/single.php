<?php

$post_id = get_the_ID();

$bg_image = capalot_get_thumbnail_url($post_id);
$single_style = _capalot('single_style', 'general'); //general hero

?>

<?php if (!empty(_capalot('single_top_breadcrumb', false))) : ?>
  // TODO: 面包屑
<?php endif; ?>


<?php

// TODO:视频模块
?>
<div class="bg-[#ededed] dark:bg-dark">
  <div class="flex lg:flex-row flex-col bg-[#ededed] justify-center lg:max-w-7xl md:max-w-3xl w-full md:mx-auto py-4 px-2 dark:bg-dark">
    <div class="bg-white dark:bg-dark-card prose-sm lg:w-[75%] w-full lg:mx-4 rounded-md p-[1.25rem] mb-[1.5rem] text-base leading-8 text-slate-700">

      <div>
        <?php
        if ($single_style === 'general') {

          the_title('<h1 class="post-title mb-2 mb-lg-3">', '</h1>');
          get_template_part('template-parts/single/meta');
        }
        ?>
        <?php get_template_part('template-parts/single/content') ?>
      </div>
    </div>
    <div class=" sidebar lg:w-[25%] w-full lg:mx-4  mb-[1.5rem] ">
      <?php dynamic_sidebar('single-sidebar'); ?>
    </div>
  </div>
</div>
