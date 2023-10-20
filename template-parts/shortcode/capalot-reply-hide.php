<?php

$content = $args;
$post_id = get_the_ID();
$user_id = get_current_user_id();

$status = is_user_commented($user_id, $post_id);

?>

<div class="ri-hide-warp p-4 border-dashed border-2 border-gray-200 rounded-md my-4 bg-sky-100 overflow-hidden relative dark:bg-dark-card dark:border-gray-600">
  <?php if ($status) : ?>
   <div class="absolute right-0 top-0 px-2 bg-gray-400 text-white z-40 rounded-tr-md rounded-bl-md"> <span class="hide-msg text-sm  "><i class="fas fa-unlock me-1"></i><?php _e('已获得查看权限', 'ripro'); ?></span></div>
    <?php echo $content; ?>
  <?php else : ?>
    <span class="hide-msg absolute right-0 top-0 px-2 bg-gray-400 text-white z-40 rounded-tr-md rounded-bl-md"><i class="fas fa-lock me-1"></i><?php _e('隐藏内容', 'ripro'); ?></span>
    <div class="hide-buy-warp my-2  py-[1.5rem] px-[1rem] text-center block">
      <div class="buy-title text-gray-400 text-[1.25rem] mb-[1rem] flex justify-center items-center"><i class="fas fa-lock me-1"></i><?php _e('本内容需评论后查看', 'ripro'); ?></div>
      <div class="buy-btns text-white mb-[1rem] flex justify-center items-center leading-[1.5rem]">
        <a rel="nofollow noopener noreferrer" href="#respond" class="btn bg-dark px-4 hover:bg-opacity-70 py-2 rounded-full"><i class="far fa-comments me-1"></i><?php _e('评论后查看', 'ripro'); ?></a>
      </div>
    </div>
  <?php endif; ?>
</div>
