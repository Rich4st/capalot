<?php get_header() ?>
<div class="bg-[#ededed] dark:bg-dark-card">
  <div class="flex lg:flex-row flex-col bg-[#ededed] justify-center lg:max-w-[1280px] md:max-w-[720px] w-full md:mx-auto  p-2">
    <div class="bg-white lg:w-[75%] w-full lg:mx-4 rounded-xl p-[1.25rem] mb-[1.5rem] text-base leading-8 text-slate-700">
      <?php the_content(); ?>
    </div>
    <div class=" sidebar lg:w-[25%] w-full lg:mx-4  mb-[1.5rem] ">
      <?php dynamic_sidebar('single-sidebar'); ?>
    </div>
  </div>
</div>

<?php get_footer() ?>