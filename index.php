<?php get_header(); ?>
<?php
if (have_posts()) {

  while (have_posts()) {
    the_post();
?>
    <!-- 输出文章链接 -->
    <span class="dashicons dashicons-translation"></span>
    <h1 class="text-red-500">
      123
    </h1>
    <a href="<?php the_permalink(); ?>">
      <?php echo the_title(); ?>
    </a>

<?php
  }
}
?>

<?php
get_footer();
