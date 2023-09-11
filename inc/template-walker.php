<?php

/**
 * 评论
 */
class ZB_Walker_Comment extends Walker_Comment
{

  public function html5_comment($comment, $depth, $args)
  {
    $GLOBALS['comment'] = $comment;

    if ('div' == $args['style']) {
      $tag = 'div';
      $add_below = 'comment';
    } else {
      $tag = 'li';
      $add_below = 'div-comment';
    }
    $author = get_comment_author();

    if ($comment->user_id) {

      $author = $author . ' ' . zb_get_user_badge($comment->user_id, 'span');
    } else if ($comment->comment_author_url) {
      $author = '<a href="' . esc_url($comment->comment_author_url) . '" target="_blank" rel="nofollow">' . $author . '</a>';
    }

?>
    <<?php echo $tag ?> <?php comment_class(empty($args['has_children']) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">

      <div id="div-comment-<?php comment_ID() ?>" class="comment-inner">
        <div class="comment-author">
          <?php if ($args['avatar_size'] != 0) echo get_avatar($comment, $args['avatar_size']); ?>
        </div>
        <div class="comment-body">
          <div class="nickname"><?php echo $author; ?></div>
          <?php if ($comment->comment_approved == '0') : ?>
            <div class="comment-awaiting-moderation"><?php _e('您的评论正在等待审核。', 'ripro'); ?></div>
          <?php endif; ?>
          <div class="comment-content"><?php comment_text(); ?></div>

          <div class="comment-meta">

            <span class="comment-time"><?php echo sprintf(__('%s前', 'ripro'), human_time_diff(get_comment_time('U'), current_time('timestamp'))); ?></span>
            <?php comment_reply_link(array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
          </div>

        </div>


      </div>
  <?php
  }
}
