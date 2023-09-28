<?php

$btnOption = _capalot('single_bottom_action_btn', array('share', 'fav', 'like'));
if (empty($btnOption)) {
  return;
}

$author_id = get_the_author_meta('ID');

$author_name = get_the_author_meta('display_name', $author_id);

?>

<div class="entry-social">

  <div class="flex items-center justify-between">

    <div class="col">
      <?php if (_capalot('single_bottom_author', true)) : ?>
        <a class="flex items-center" href="<?php echo esc_url(get_author_posts_url($author_id, $author_name)); ?>">
          <img class="w-8 h-8 rounded-full mr-2" src="<?php echo get_avatar_url($author_id); ?>" alt="">
          <?php echo $author_name; ?>
        </a>
      <?php endif; ?>
    </div>

    <div class="space-x-2">

      <?php if (in_array('share', $btnOption)) : ?>
        <button class="post-share-btn px-4 py-2 rounded-full transition-all duration-300
          hover:shadow-[0_5px_20px_rgba(240,_46,_170,_0.4)] dark:bg-dark
          shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]" title='分享'>
          <i class="share fa-solid fa-share"></i>
        </button>
      <?php endif; ?>


      <?php if (in_array('fav', $btnOption)) :
        $is_fav = (capalot_is_post_fav()) ? 0 : 1;
        $fav_text = ($is_fav == '0') ? '取消收藏' : '收藏';
      ?>
        <button class="post-fav-btn px-4 py-2 rounded-full transition-all duration-300
          hover:shadow-[0_5px_20px_rgba(240,_46,_170,_0.4)] dark:bg-dark
          shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]" title='<?php echo $fav_text; ?>' data-is="<?php echo $is_fav; ?>">
          <i class="<?php if($is_fav == '0') echo 'hidden'; ?> unfav fa-regular fa-star"></i>
          <i class="<?php if($is_fav == '1') echo 'hidden'; ?> fav fa-solid fa-star" style="color: #ea64d9;"></i>
        </button>
      <?php endif; ?>

      <?php if (in_array('like', $btnOption)) :
          $is_like = (capalot_is_post_like()) ? 0 : 1;
          $like_title = ($is_like == '0') ? '取消点赞' : '点赞';
        ?>
        <button class="post-like-btn relative px-4 py-2 rounded-full transition-all duration-300
          hover:shadow-[0_5px_20px_rgba(240,_46,_170,_0.4)] dark:bg-dark
          shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]" title="<?php echo $like_title; ?>" data-is='<?php echo $is_like; ?>'>
          <i class="<?php if($is_like == '0') echo 'hidden'; ?> unlike fa-regular fa-heart"></i>
          <i class="<?php if($is_like == '1') echo 'hidden'; ?> liked fa-solid fa-heart" style="color: #ea64d9;"></i>
          <span class="like-count text-sm text-gray-400 bg-white  absolute -top-1 -right-1 px-1 rounded-full border">
            <?php echo capalot_get_post_likes(); ?>
          </span>
        </button>
      <?php endif; ?>

    </div>
  </div>

</div>
