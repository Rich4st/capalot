<?php
if (have_posts()) {

  while (have_posts()) {
    the_post();
?>

    <img src="<?php echo _capalot('site_logo') ?>" alt="">
    <!-- 输出文章链接 -->
    <h1 class=" text-sky-500">
      123
    </h1>
    <a href="<?php the_permalink(); ?>">
      <?php echo the_title(); ?>
    </a>

<?php
  }
}
