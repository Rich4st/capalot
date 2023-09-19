<?php

if (empty($args)) {
	return;
}

$config = [
	'lazy' => true,
	'loop'     => true,
	// 判断后台是否启用自动播放
	'autoplay' => (bool)$args['is_autoplay'],
];

$data = Capalot_Notification::get();

if (empty($data)) {
	$data = [
		[
			'info' => sprintf(__('欢迎访问%s，网址：%s', 'ripro'), get_bloginfo('name'), home_url()),
			'uid' => get_current_user_id(),
			'href' => home_url(),
			'time' => time(),
		],
		[
			'info' => sprintf(__('欢迎访问%s，网址：%s', 'ripro'), get_bloginfo('name'), home_url()),
			'uid' => get_current_user_id(),
			'href' => home_url(),
			'time' => time(),
		],
		[
			'info' => sprintf(__('欢迎访问%s，网址：%s', 'ripro'), get_bloginfo('name'), home_url()),
			'uid' => get_current_user_id(),
			'href' => home_url(),
			'time' => time(),
		]
	];
}
$container = _capalot('site_container_width', '1400');

?>


<section class="dark:bg-dark pt-3 pb-0 ">
	<div class="mx-auto " style="max-width: <?php if ($container === '') {
												echo '1280';
											} else {
												echo $container;
											}
											?>px;">

		<div class="bg-[#d8d8d8] dark:bg-dark p-2 rounded text-sm">
			<div class="flex items-center">
				<div class="w-36  md:w-28 mr-2">
					<span class="bg-dark dark:bg-dark-card px-2 rounded text-white"><i class="fa fa-volume-up me-1"></i><?php echo esc_html($args['title']); ?></span>
				</div>

				<div class="swiper mySwiper w-full text-gray-400 " data-config='<?php echo json_encode($config); ?>'>
					<div class="swiper-wrapper">
						<?php foreach ($data as $key => $item) : ?>
							<div class="swiper-slide">
								<?php
								$times  = sprintf(__('%s前', 'ripro'), human_time_diff($item['time'], time()));
								$u_name = get_user_meta(intval($item['uid']), 'nickname', 1);
								$u_avatar = get_avatar_url($item['uid']);
								$info = (empty($item['href'])) ? $item['info'] : '<a target="_blank" href="' . $item['href'] . '">' . $item['info'] . '</a>';
								if (empty($u_name)) {
									$u_name = '游客';
								} else {
									$u_name = capalot_substr_cut($u_name);
								}
								?>
								<div class="flex flex-row space-x-2 items-center ">
									<div class="h-8 w-8 flex items-center justify-center"><img class="rounded-full  overflow-hidden border-white border-2 shadow" src="<?php echo $u_avatar; ?>"></div>
									<b class="name font-bold"><?php echo $u_name; ?></b>
									<p class="info overflow-hidden text-ellipsis whitespace-nowrap"><?php echo $info; ?><span class="times"><?php echo $times; ?></span></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>


	</div>
</section>