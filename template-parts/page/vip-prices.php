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

<div class=" text-center py-8 bg-gray-200 dark:bg-dark-card">
	<div class="archive-hero-bg lazy" data-bg="<?php echo $bg_image; ?>"></div>
	<div class="">
		<h1 class="  text-xl font-bold dark:text-gray-50"><i class="far fa-gem me-1 "></i><?php _e('本站VIP', 'ripro'); ?></h1>
		<div class=" dark:text-gray-400">
			<p><?php _e('加入本站VIP，畅享海量资源', 'ripro'); ?></p>
		</div>
	</div>
</div>


<section class=" bg-[#eee] dark:bg-dark ">
	<div class=" max-w-[80rem] m-auto lg:px-0 px-4 py-8">

		<div class=" grid lg:grid-cols-3 grid-cols-1 gap-4">
			<?php foreach ($site_vip_buy_options as $day => $item) :
				if ($item['day_num'] == 9999) {
					$day_title = __('永久', 'ripro');
				} else {
					$day_title = sprintf(__('%s天', 'ripro'), $item['day_num']);
				}
				$rate_day_coin = round($item['coin_price'] / $item['day_num'], 0);
			?>

				<div class="  text-center bg-white dark:bg-dark-card shadow-xl dark:shadow-none shadow-gray-600/10 relative transition transform   hover:-translate-y-3 duration-300">
					<div class="">
						<div class=" py-8 px-4 bg-<?php echo $vip_colors[$item['type']]; ?> bg-sky-100 bg-opacity-70 dark:bg-dark-card">
							<span class=" text-xl font-bold text-sky-800 dark:text-gray-50 "><?php echo $item['buy_title']; ?></span>

							<h3 class=" text-2xl font-bold text-red-500 my-4"><?php echo $item['coin_price']; ?><sup><?php echo get_site_coin_name(); ?></sup></h3>

							<span class=" text-lg text-teal-500 text-<?php echo $vip_colors[$item['type']]; ?>"><i class="far fa-gem me-1"></i>VIP会员<?php echo $item['name']; ?><sup><?php echo $day_title; ?></sup></span>
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
								<a class="btn btn-dark-soft px-4 rounded-pill" rel="nofollow noopener noreferrer" href="<?php echo esc_url(wp_login_url(get_current_url())); ?>"><i class="far fa-user me-1"></i><?php _e('登录后升级', 'ripro'); ?></a>
							<?php endif; ?>

						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>


		<div class=" pt-16">
			<div class=" text-center mb-8 ">
				<h3 class=" text-2xl text-gray-600 dark:text-gray-50 "><?php _e('VIP会员说明', 'ripro'); ?></h3>
				<p class=" text-md text-gray-400 dark:text-gray-400"><?php _e('开通会员常见问题说明及介绍', 'ripro'); ?></p>
			</div>
			<div class=" grid lg:grid-cols-2 grid-cols-1 gap-4">
				<?php foreach (_capalot('site_buyvip_desc', array()) as $text) {
					echo '<div class=" text-sm text-gray-500 dark:text-gray-400"><div class="p-4 bg-info bg-opacity-10 rounded-2 dark:bg-dark-card "><i class="fas fa-info-circle me-1"></i>' . $text['content'] . '</div></div>';
				} ?>
			</div>
		</div>


	</div>

</section>





<?php

// ri_home_catbox_widget(array(
// 	'id' => 'home-center',
//     'before_widget' => '<div class="home-widget home-cat-box">',
//     'after_widget'  => '</div>',
// ), array());

?>

<?php get_footer(); ?>