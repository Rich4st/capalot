<?php

if (empty($args)) {
  return;
}

$author_id = get_the_author_meta('ID');
$author_name = get_the_author_meta('display_name', $author_id);
$author_link = esc_url(get_author_posts_url($author_id, $author_name));
$author_img = get_avatar_url($author_id);
$post_count = count_user_posts($author_id, 'post');
$fav_ids = get_user_meta($author_id, 'follow_post', true);
if (empty($fav_ids) || !is_array($fav_ids)) {
  $fav_ids = array();
}

$fav_count = count($fav_ids);
$comment_count = get_comments(array(
  'user_id' => $author_id,
  'count'   => true,
));
?>

<div class=" mb-8 bg-white dark:bg-black rounded-xl p-4">

  <?php if (!empty($args['title'])) : ?>
    <h5 class="widget-title mb-4"><?php echo $args['title']; ?></h5>
  <?php endif; ?>



  <div class="author-header">
    <div class="row align-items-center flex flex-row">
      <div class="col-auto mr-8">
        <!-- Avatar -->
        <div class="avatar ">
          <img class="lazy avatar-img rounded-circle   w-12 h-12 shadow rounded-full" src="<?php echo $author_img; ?>">
        </div>

      </div>
      <div class="col">
        <span class="d-block h6 fw-bold mb-1"><?php echo $author_name; ?></span>
        <small class="d-block text-muted"><?php _e('等级', 'ripro'); ?><?php echo zb_get_user_badge($author_id, 'span', 'ms-1 mb-0'); ?></small>
      </div>
    </div>
  </div>

  <div class="author-body my-4">
    <div class="row grid grid-cols-3 gap-4 ">
      <div class="col-4 text-center">
        <span class="h5 mb-0 block"><?php echo $post_count; ?></span>
        <span class="d-block text-sm block"><?php _e('文章', 'ripro'); ?></span>
      </div>
      <div class="col-4 text-center">
        <span class="h5 mb-0 block"><?php echo $comment_count; ?></span>
        <span class="d-block text-sm block"></i><?php _e('评论', 'ripro'); ?></span>
      </div>
      <div class="col-4 text-center">
        <span class="h5 mb-0 block"><?php echo $fav_count; ?></span>
        <span class="d-block text-sm block"><?php _e('收藏', 'ripro'); ?></span>
      </div>
    </div>
  </div>

  <div class="author-footer">
    <div class="text-center text-[12px] text-neutral-500 bg-[#eee] p-2 rounded-md">
      <a href="<?php echo $author_link; ?>" class="btn btn-sm px-0 btn-link hover:text-neutral-900"><?php _e('查看作者其他文章', 'ripro'); ?></a>
    </div>
  </div>

</div>