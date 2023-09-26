<?php

global $current_user;

$user_id = $current_user->ID;
$curr_page_action = sanitize_text_field(get_response_param('action', 'publish', 'get'));
$curr_post_id = intval(get_response_param('post_id', '', 'get'));
$site_vip_options = _capalot('site_vip_options');

if ($curr_page_action == 'trash_post' && $curr_post_id) {
	// 获取文章的作者ID
	$post_author_id = get_post_field('post_author', $curr_post_id);

	// 判断文章的作者ID是否与当前用户ID相同
	if ($post_author_id == $user_id) {
		// 将文章移至回收站
		wp_trash_post($curr_post_id);
		$curr_page_action = 'trash';
	}
} elseif ($curr_page_action == 'delete_post' && $curr_post_id) {
	// 获取文章的作者ID
	$post_author_id = get_post_field('post_author', $curr_post_id);

	// 判断文章的作者ID是否与当前用户ID相同
	if ($post_author_id == $user_id || current_user_can('delete_others_posts')) {
		// 删除文章...
		wp_delete_post($curr_post_id, true);
		$curr_page_action = 'trash';
	}
}

$page_link = get_uc_menu_link('tougao');


?>

<!-- 投稿列表 -->
<div class="mb-4 bg-white dark:bg-dark-card p-4 mx-2 rounded user-tougao-warp">
	<div class="mb-4">
		<h5 class="font-bold flex justify-between  items-center"><?php _e('投稿管理', 'ripro'); ?>
			<a class="bg-[#0cbc87] px-2 py-1 text-white rounded  " href="<?php echo esc_url(home_url('/tougao')); ?>"><i class="fa-solid fa-pen-to-square"></i><?php _e('新建投稿', 'ripro'); ?></a>
		</h5>
		<div class="mt-2">
			<?php
			$menus = [
				['title' => __('已发布', 'ripro'), 'icon' => 'far fa-check-circle', 'key' => 'publish',],
				['title' => __('草稿', 'ripro'), 'icon' => 'fas fa-circle-notch', 'key' => 'draft'],
				['title' => __('待审核', 'ripro'), 'icon' => 'far fa-circle', 'key' => 'pending'],
				['title' => __('回收站', 'ripro'), 'icon' => 'far fa-trash-alt', 'key' => 'trash'],
			];
			foreach ($menus as $menu) {
				$_link = esc_url(add_query_arg(array('action' => $menu['key']), $page_link));
				$classes = ($curr_page_action == $menu['key']) ? ' text-red-500' : ' text-muted';
				printf('<a class="me-2%s" href="%s"><i class="%s me-1"></i>%s</a>', $classes, $_link, $menu['icon'], $menu['title']);
			}

			?>
		</div>
	</div>
	<div class="card-body">

		<?php

		$page = isset($_GET['page']) ? absint($_GET['page']) : 1; //第几页
		$limit = 10; //每页显示数量

		$post_filter = (in_array($curr_page_action, array('publish', 'draft', 'pending', 'trash'))) ? $curr_page_action : 'publish';
		$args = array(
			'post_type' => 'post',
			'post_status' => $post_filter,
			'posts_per_page' => $limit,
			'paged' => $page,
			'ignore_sticky_posts' => 1,
			'author' => $current_user->ID,
			'orderby' => 'date',
			'order' => 'DESC'
		);
		$PostData = new WP_Query($args);

		?>

		<?php if ($PostData->have_posts()) : ?>
			<ul class="grid grid-cols-1 gap-4  bg-white dark:bg-dark-card list-none">
				<?php while ($PostData->have_posts()) : $PostData->the_post(); ?>

					<?php

					$post_id = get_the_id();

					$post_prices = get_post_price_data($post_id);
					$post_price = $post_prices['default'];

					?>

					<li class="  rounded cursor-pointer shadow-[rgba(0,_0,_0,_0.24)_0px_3px_8px] transition-all duration-300 hover:shadow-[0px_4px_16px_rgba(17,17,26,0.1),_0px_8px_24px_rgba(17,17,26,0.1),_0px_16px_56px_rgba(17,17,26,0.1)]  ">
						<article class="border flex dark:bg-dark relative rounded dark:border-0 p-2">

							<div class="tips-badge absolute w-10 text-center top-0 start-0 z-[999] m-2 bg-[#b0adac]  rounded-xl">
								<?php if (is_sticky()) : ?>
									<div class="text-[0.5rem] bg-opacity-25 text-white"><?php _e('置顶', 'ripro'); ?></div>
								<?php endif; ?>
							</div>

							<div class="max-w-[8rem] ratio ratio-16x9 col-auto mr-2">
								<a target="<?php echo get_target_blank(); ?>" class="block  bg-no-repeat " style="background-image: url(<?php echo capalot_get_thumbnail_url(); ?>);" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" data-bg="<?php echo capalot_get_thumbnail_url(); ?>"></a>
							</div>
							<div class="entry-wrapper  w-full text-gray-400 text-[0.75rem]">
								<div class="entry-body  h-3/4  ">
									<div class="mb-1 whitespace-nowrap text-ellipsis overflow-hidde"><i class="fa-solid fa-tag pr-2" style="color: #82a6f0;"></i><?php capalot_meta_category(2); ?></div>
									<h2 class="text-black text-base dark:text-gray-50  font-bold  whitespace-nowrap text-ellipsis overflow-hidden">
										<a target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
									</h2>
								</div>
								<div class="entry-meta flex justify-between">
									<div class="entry-meta flex flex-row">
										<span class="meta-date mr-1"><i class="fa-regular fa-clock pr-1"></i><?php capalot_meta_datetime(); ?></span>
										<span class="meta-likes  md:inline-block hidden mr-1"><i class="far fa-heart me-1"></i><?php echo capalot_get_post_likes(); ?></span>
										<span class="meta-fav md:inline-block hidden mr-1"><i class="far fa-star me-1"></i><?php echo capalot_get_post_favorites(); ?></span>
										<span class="meta-views mr-1"><i class="fa-regular fa-eye pr-1"></i><?php echo capalot_get_post_views(); ?></span>
										<?php if (is_site_shop() && post_is_pay($post_id)) : ?>
											<span class="meta-price whitespace-nowrap flex flex-row"><i class="<?php echo get_site_coin_icon(); ?> me-1"></i><?php echo $post_price; ?></span>
										<?php endif; ?>


									</div>
									<div>
										<span class="meta-action">

											<a target="_blank" href="<?php echo esc_url(add_query_arg(array('post_id' => $post_id), home_url('/tougao'))); ?>" class="text-[#509ff8] md:mr-4 mr-0"><i class="bi bi-pencil-square fa-fw me-1"></i><?php _e('编辑', 'ripro'); ?></a>

											<?php $retVal = ($curr_page_action == 'trash') ? 'delete_post' : 'trash_post'; ?>
											<a href="<?php echo esc_url(add_query_arg(array('action' => $retVal, 'post_id' => $post_id), $page_link)); ?>" class="text-red-500 md:mr-2"><i class="bi bi-trash3 fa-fw me-1"></i><?php _e('删除', 'ripro'); ?></a>
										</span>
									</div>
								</div>

							</div>
						</article>
					</li>

				<?php endwhile; ?>
			</ul>
		<?php
			capalot_custom_pagination($page, $PostData->max_num_pages);
			wp_reset_postdata();
		else :

			echo '<p class="w-100 p-4 text-center">' . __('暂无内容', 'ripro') . '</p>';

		endif;
		?>
	</div>


</div>