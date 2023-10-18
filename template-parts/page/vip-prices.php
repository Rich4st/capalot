<?php

get_header();

global $current_user;
$site_vip_options = get_site_vip_options();
$site_vip_buy_options = get_site_vip_buy_options();
$uc_vip_info = get_user_vip_data($current_user->ID);

//颜色配置
$vip_colors = [
	'no' => 'secondary',
	'vip' => 'success',
	'boosvip' => 'warning',
];

$bg_image = get_template_directory_uri() . '/assets/img/bg.jpg';
$price_shape = get_template_directory_uri() . '/assets/img/price_shape.png';

?>

<div class=" relative overflow-hidden">
	<div class="absolute left-0 top-0 right-0 w-full h-full z-[-1] " style="background-image: url(<?php echo $bg_image; ?>); background-position:50%;background-size:100%; "></div>
	<div class=" absolute  backdrop-blur-lg h-full w-full bg-black/30"></div>
	<div class=" relative z-50 py-12 text-center text-white ">
		<h1 class="  text-xl font-bold "><i class="far fa-gem me-1 "></i><?php _e('本站VIP', 'ripro'); ?></h1>
		<div class=" text-sm text-white/80 mt-2 ">
			<p><?php _e('加入本站VIP，畅享海量资源', 'ripro'); ?></p>
		</div>
	</div>
</div>

<section class=" bg-[#eee] dark:bg-dark ">
	<div class="max-w-7xl m-auto lg:px-0 px-4 py-8">

		<div class="grid sm:grid-cols-2 md:grid-cols-3 grid-cols-1 gap-4">
			<?php foreach ($site_vip_buy_options as $day => $item) :
				if ($item['day_num'] == 9999) {
					$day_title = __('永久', 'ripro');
				} else {
					$day_title = sprintf(__('%s天', 'ripro'), $item['day_num']);
				}
				$rate_day_coin = round($item['coin_price'] / $item['day_num'], 0);
			?>
				<div class="text-center bg-white dark:bg-dark-card shadow-xl dark:shadow-none shadow-gray-600/10 relative">
					<div class=" py-8 px-4  bg-sky-100 bg-opacity-70 dark:bg-dark-card">
						<span class=" text-xl font-bold text-sky-800 dark:text-gray-50 "><?php echo $item['buy_title']; ?></span>

						<h3 class=" text-2xl font-bold text-red-500 my-4"><?php echo $item['coin_price']; ?><sup><?php echo get_site_coin_name(); ?></sup></h3>

						<span class=" text-lg text-teal-500 text-<?php echo $vip_colors[$item['type']]; ?>"><i class="far fa-gem me-1"></i><?php echo $item['name']; ?><sup><?php echo $day_title; ?></sup></span>
					</div>
					<div class=" p-4 text-md text-gray-600 dark:text-gray-400 ">
						<ul class="">
							<li><?php printf(__('尊享%s特权%s', 'ripro'), $item['name'], $day_title); ?></li>
							<!-- <?php foreach ($item['desc'] as $text) : ?> -->
							<li><?php echo $text; ?></li>
							<!-- <?php endforeach; ?> -->

						</ul>
					</div>
					<div class=" pb-8 pt-4 px-4 ">
						<?php if (is_user_logged_in()) : ?>
							<?php
							$btn_text = __('立即开通', 'ripro');
							$disabled = '';
							if ($uc_vip_info['type'] == 'boosvip') {
								$btn_text = __('已获得权限', 'ripro');
								$disabled = 'disabled';
							} elseif ($uc_vip_info['type'] == 'vip' && $item['type'] == 'vip') {
								$btn_text = __('立即续费', 'ripro');
							} elseif ($uc_vip_info['type'] == 'vip' && $item['type'] == 'boosvip') {
								$btn_text = __('立即升级', 'ripro');
							}
							?>
							<button class=" border border-teal-500 text-teal-500 text-md p-2 hover:bg-teal-500 hover:text-white rounded-full btn btn-outline-<?php echo $vip_colors[$item['type']]; ?> js-pay-action px-4 rounded-pill" data-id="0" data-type="3" data-info="<?php echo $item['day_num']; ?>" <?php echo $disabled; ?>><i class="far fa-gem me-1"></i><?php echo $btn_text; ?></button>

						<?php else : ?>
							<a class="border border-teal-500 text-teal-500 text-md p-2 hover:bg-teal-500 hover:text-white rounded-full btn" rel="nofollow noopener noreferrer" href="<?php echo esc_url(wp_login_url(get_current_url())); ?>"><i class="far fa-user me-1">
								</i><?php _e('登录后升级','ripro');?>
							</a>
						<?php endif; ?>

					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class=" pt-16">
			<div class=" text-center mb-8 ">
				<h3 class=" text-2xl text-gray-600 dark:text-gray-50 "><?php _e('VIP会员说明', 'ripro'); ?></h3>
				<p class=" text-md text-gray-400 dark:text-gray-400"><?php _e('开通会员常见问题说明及介绍', 'ripro'); ?></p>
			</div>
			<div class=" grid md:grid-cols-2 grid-cols-1 gap-4">
				<?php foreach (_capalot('site_buyvip_desc', array()) as $text) {
					echo '<div class=" text-sm text-gray-500 dark:text-gray-400"><div class="p-4 bg-info bg-opacity-10 rounded-2 dark:bg-dark-card "><i class="fas fa-info-circle me-1"></i>' . $text['content'] . '</div></div>';
				} ?>
			</div>
		</div>

	</div>

</section>

<?php get_footer(); ?>
