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

<div class="my-3 bg-white  rounded-md p-4 dark:bg-dark-card">
  <?php if (!empty($args['title'])) : ?>
    <h5 class="widget-title mb-4 dark:text-gray-50"><?php echo $args['title']; ?></h5>
  <?php endif; ?>



  <div class="author-header">
    <div class="row align-items-center flex flex-row">
      <div class=" w-1/4  flex items-center">
        <!-- Avatar -->
        <div class=" ">
          <img class="lazy avatar-img rounded-circle   w-12 h-12 shadow rounded-full" src="<?php echo $author_img; ?>">
        </div>

      </div>
      <div class=" w-3/4">
        <span class="  mb-1 block dark:text-gray-50"><?php echo $author_name; ?></span>
        <small class="d-block text-muted text-gray-600 dark:text-gray-400 "><?php _e('等级', 'ripro'); ?><?php echo capalot_get_user_badge($author_id, 'span', ' !bg-gray-200 rounded-md ml-2 px-2 text-[12px] py-1 !text-gray-600 dark:!bg-dark dark:!text-gray-400'); ?></small>
      </div>
    </div>
  </div>

  <div class="author-body my-4">
    <div class="row grid grid-cols-3 gap-4 ">
      <div class="col-4 text-center">
        <span class="h5 mb-0 block dark:text-gray-50"><?php echo $post_count; ?></span>
        <span class="d-block text-sm block dark:text-gray-400"><?php _e('文章', 'ripro'); ?></span>
      </div>
      <div class="col-4 text-center dark:text-gray-50">
        <span class="h5 mb-0 block"><?php echo $comment_count; ?></span>
        <span class="d-block text-sm block dark:text-gray-400"></i><?php _e('评论', 'ripro'); ?></span>
      </div>
      <div class="col-4 text-center dark:text-gray-50">
        <span class="h5 mb-0 block"><?php echo $fav_count; ?></span>
        <span class="d-block text-sm block dark:text-gray-400"><?php _e('收藏', 'ripro'); ?></span>
      </div>
    </div>
  </div>

  <div class="author-footer">
    <div class="text-center text-[12px] text-neutral-500 bg-[#eee] dark:bg-dark p-2 rounded-md">
      <a href="<?php echo $author_link; ?>" class="btn btn-sm px-0 btn-link hover:text-neutral-900 dark:text-gray-400 dark:hover:text-white"><?php _e('查看作者其他文章', 'ripro'); ?></a>
    </div>
  </div>

</div>
