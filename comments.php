<?php

if (post_password_required() || !comments_open() || !is_site_comments()) return;

?>

<div id="comments" class="entry-comments my-3 bg-white dark:bg-dark-card rounded p-2 lg:p-4">
	<?php $fields =  array(
		'author' => '<div class="comment-form-author"><input id="author" class="w-full mb-4  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="author" type="text" placeholder="' . __('*昵称:', 'ripro') . '" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . ($req ? ' class="required "' : '') . '></div>',
		'email'  => '<div class="comment-form-email"><input id="email" class="w-full mb-4 dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="email" type="text" placeholder="' . __('*邮箱:', 'ripro') . '" value="' . esc_attr($commenter['comment_author_email']) . '"' . ($req ? ' class="required"' : '') . '></div>',
		'url'    => '<div class="comment-form-url"><input class="w-full mb-4 dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" id="url" name="url" type="text" placeholder="' . __('网址:', 'ripro') . '" value="' . esc_attr($commenter['comment_author_url']) . '" size="30"></div>',
		'cookies' => '<input type="hidden" name="wp-comment-cookies-consent" value="yes">'
	); ?>


	<h2 class="comments-title my-4 relative"><i class="fas fa-comment-dots me-1"></i><?= sprintf(__('评论(%s)', 'ripro'), number_format_i18n(get_comments_number())); ?></h2>

	<?php comment_form(array(
		'title_reply'        => __('提示：请文明发言', 'ripro'),
		'title_reply_to'     => '',
		'fields'             => $fields,
		'comment_field'      => '<div class="comment-form-comment "><textarea class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" id="comment" name="comment" rows="3" placeholder="' . __('请输入评论内容...', 'ripro') . '"></textarea></div>',
		'must_log_in'        => '<div class="flex justify-center "><a rel="nofollow noopener noreferrer" href="' . esc_url(wp_login_url(get_current_url())) . '" class="btn bg-black px-4 py-1 rounded-lg hover:bg-[#3c3c41]"><i class="far fa-user me-1"></i>' . __('登录后评论', 'ripro') . '</a></div>', //登录提示
		'logged_in_as'       => '', //已经登录提示
		'label_submit'       => __('提交评论', 'ripro'),
		'format'             => 'html5'
	)); ?>



	<?php if (have_comments()) : ?>
		<ul class="comments-list">
			<?= wp_list_comments(array(
				'walker'      => new ZB_Walker_Comment,
				'style'       => 'ul',
				'short_ping'  => true,
				'type'        => 'comment',
				'avatar_size' => '40',
				'format'      => 'html5'
			));
			?>
		</ul>
		
		<?php if ($the_paginate = paginate_comments_links(array('echo' => false))) : ?>
			<!-- 评论翻页按钮 -->
			<div class="comments-nav infinite-scroll flex justify-center">
				<div class="comments-pagination">
					<?= $the_paginate; ?>
				</div>
			</div>
		<?php endif; ?>


	<?php endif; ?>


</div>