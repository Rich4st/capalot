<?php
/**
 * 判断用户是否评论文章
 *
 * @param int $user_id 用户ID
 * @param int $post_id 文章ID
 *
 */
function is_user_commented($user_id, $post_id)
{
    $comments = get_comments(array(
        'post_id' => $post_id,
        'user_id' => $user_id,
    ));
    return count($comments) > 0;
}
