<?php

global $current_user;

$site_vip_options = get_site_vip_options();
$site_vip_buy_options = get_site_vip_buy_options();
$uc_vip_info = get_user_vip_data($current_user->ID);

//颜色配置
$vip_colors = [
	'no'      => 'secondary',
	'vip'     => 'success',
	'boosvip' => '[#f7c32e]',
];

$price_shape = get_template_directory_uri() . '/assets/img/price_shape.png';

?>


<div class="mb-4 bg-white   dark:bg-dark-card mx-2 rounded p-4">
	<div class="mb-3">
		<h5 class="font-bold"><?php _e('会员中心', 'ripro'); ?></h5>
	</div>

	<div class="flex flex-col justify-between items-start  mb-4">
		<div class="flex items-center justify-center mb-3">
			<div class="me-2">
				<div class=" mb-2">
					<img class="avatar-img rounded-full border border-white  shadow" src="<?php echo get_avatar_url($current_user->ID); ?>" alt="user">
				</div>
			</div>
			<div class="ms-2 ">
				<h5 class="flex items-center mb-1 font-bold dark:text-white">
					<?php echo $current_user->display_name; ?><?php echo capalot_get_user_badge($current_user->ID, 'span', 'mb-0 ms-2 rounded px-1'); ?>
				</h5>
				<div class="mb-1 text-sm">
					<span><?php echo $current_user->user_login; ?></span>
					<?php

					if ($uc_vip_info['type'] != 'no') {
						printf('<span>%s%s</span>', $uc_vip_info['end_date'], __('到期', 'ripro'));
					} else {
						printf('<span>%s%s</span>', $current_user->user_registered, __('加入', 'ripro'));
					}
					?>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
			<?php
			$item = [
				'total' => __('每天可下载数', 'ripro'),
				'used' => __('今日已用次数', 'ripro'),
				'not' => __('今日剩余次数', 'ripro'),
				'rate' => __('下载使用率', 'ripro'),
			];
			$uc_vip_info['downnums']['rate'] = ($uc_vip_info['downnums']['used'] / $uc_vip_info['downnums']['total'] * 100) . '%';

			$color_key = 1;
			foreach ($item as $key => $name) : $value = $uc_vip_info['downnums'][$key];
				$color_key++; ?>
				<div class="">
					<div class="card rounded  bg-<?php echo capalot_get_color_class($color_key); ?> bg-opacity-25 p-4  rounded-2">
						<h4 class="font-bold text-<?php echo capalot_get_color_class($color_key); ?>"><?php echo $value; ?></h4>
						<span class="mb-0 text-[#779099]"><?php echo $name; ?></span>
					</div>
				</div>
			<?php endforeach; ?>

		</div>
	</div>



	<div class="mb-4">
		<div class="mb-3">
			<h5 class="font-bold"><?php _e('会员开通', 'ripro'); ?></h5>
		</div>
		<ol class="list-decimal  ml-6">
			<?php foreach (_capalot('site_buyvip_desc', array()) as $text) {
				echo '<li class="py-2 border-b text-muted ">' . $text['content'] . '</li>';
			} ?>
		</ol>
	</div>

	<div class="mb-4">

		<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
			<?php foreach ($site_vip_buy_options as $day => $item) :
				if ($item['day_num'] == 9999) {
					$day_title = __('永久', 'ripro');
				} else {
					$day_title = sprintf(__('%s天', 'ripro'), $item['day_num']);
				}
				$rate_day_coin = round($item['coin_price'] / $item['day_num'], 0);
			?>

				<div class="col">
					<div class="price-card text-center rounded overflow-hidden border border-[#eee] dark:bg-dark cursor-pointer 
					shadow-[rgba(0,_0,_0,_0.1)_0px_1px_1px] transition-all duration-300
					 hover:shadow-[0px_4px_16px_rgba(17,17,26,0.1),_0px_8px_24px_rgba(17,17,26,0.1),_0px_16px_56px_rgba(17,17,26,0.1)]">
						<div class="py-4 bg-no-repeat bg-cover bg-center bg-<?php echo $vip_colors[$item['type']]; ?> bg-opacity-10">

							<span class=" text-xl font-bold mb-2"><?php echo $item['buy_title']; ?></span>

							<h3 class="text-[#fb2971] text-2xl font-bold mb-2"><?php echo $item['coin_price']; ?><sup class="text-base font-semibold "><?php echo get_site_coin_name(); ?></sup></h3>

							<span class="price-sub text-<?php echo $vip_colors[$item['type']]; ?>"><i class="far fa-gem me-1"></i><?php echo $item['name']; ?><sup><?php echo $day_title; ?></sup></span>

						</div>
						<div class="py-4">
							<ul class="price-desc">
								<li class="pb-2 text-[#666]"><?php printf(__('尊享%s特权%s', 'ripro'), $item['name'], $day_title); ?></li>
								<?php foreach ($item['desc'] as $text) : ?>
									<li class="pb-2 text-[#666]"><?php echo $text; ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
						<div class="pb-2  mb-2">
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
							<button class="btn bg-<?php echo $vip_colors[$item['type']]; ?> text-<?php echo $vip_colors[$item['type']]; ?> hover:text-white hover:bg-opacity-100   bg-opacity-10 rounded p-2 js-pay-action" data-id="0" data-type="3" data-info="<?php echo $item['day_num']; ?>" <?php echo $disabled; ?>><i class="far fa-gem me-1"></i><?php echo $btn_text; ?></button>
						</div>
					</div>
				</div>
			<?php endforeach; ?>

		</div>



	</div>

</div>

<?php if (!empty(_capalot('is_site_cdk_pay', true))) : ?>
	<div class="mb-4 bg-white dark:bg-dark-card mx-2 rounded vip-cdk-body p-4">
		<div class="mb-3">
			<h5 class="font-bold"><?php _e('会员兑换', 'ripro'); ?></h5>
		</div>
		<div class="card-body">
			<h5 class="text-center mb-4 text-muted"><?php _e('使用CDK码兑换VIP特权', 'ripro'); ?></h5>
			<form class="w-64 flex flex-col mx-auto" id="vip-cdk-action">
				<div class="mb-3">
					<input type="text" class="bg-[#ededed] h-8 w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="cdk_code" placeholder="兑换码/CDK卡号" value="">
				</div>
				<div class="flex flex-row space-x-2 mb-3">
					<input type="text" class="bg-[#ededed] h-8 w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="captcha_code" placeholder="验证码">
					<img id="captcha-img" class="rounded-2 w-full h-8 bg-[#cacaca]  text-white mx-2 rounded" role="button" src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/captcha.png'); ?>" data-src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/captcha.png'); ?>" title="<?php _e('点击刷新验证码', 'ripro'); ?>" />
				</div>
				<div class="flex flex-row justify-center space-x-2 mb-3 mt-3 text-center text-sm">
					<input type="hidden" name="action" value="capalot_vip_cdk_action">
					<button type="submit" id="vip-cdk-submit" class="btn bg-[#d6293e] text-white px-4 py-1 rounded"><i class="fas fa-gift me-1"></i><?php _e('立即兑换', 'ripro'); ?></button>
					<a class="btn bg-[#f7c32e]  px-4 py-1 rounded" target="_blank" href="<?php echo _capalot('site_cdk_pay_link'); ?>" rel="nofollow noopener noreferrer"><i class="fas fa-external-link-alt me-1"></i><?php _e('购买CDK', 'ripro'); ?></a>

				</div>
			</form>
		</div>
	</div>
<?php endif; ?>

<div class="mb-4 bg-white   dark:bg-dark-card mx-2 rounded p-4">
	<div class="mb-2">
		<caption class="font-bold "><?php _e('VIP获取记录（最近10条）', 'ripro'); ?></caption>
	</div>

	<div class=" pay-vip-log">
		<?php
		global $wpdb;
		$table_name = $wpdb->prefix . 'capalot_order';
		$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE user_id = %d AND order_type=3 AND pay_status=1 ORDER BY create_time DESC LIMIT 10", $current_user->ID));

		if (empty($data)) {
			echo '<p class="p-4 text-center">' . __('暂无记录', 'ripro') . '</p>';
		} else {
			echo '<ul class="bg-[#ededed] rounded border dark:bg-dark dark:border-transparent border-[#dadada]">';
			foreach ($data as $key => $item) {
				$info = maybe_unserialize($item->order_info);
				$vip_info = $site_vip_options[$info['vip_type']];
		?>
				<div class="px-4 my-2 block ">
					<div class="flex justify-between w-full">
						<h6 class="text-muted block md:inline-block "><?php printf(__('订单类型：%s', 'ripro'), $vip_info['name']); ?></h6>
						<small class="text-muted"><?php echo wp_date('Y-m-d H:i', $item->create_time); ?></small>
					</div>
					<small class="text-muted"><?php printf(__('支付金额：￥%1$s（%2$s）', 'ripro'), $item->order_price, site_convert_amount($item->order_price, 'coin') . get_site_coin_name()); ?></small>
					<small class="text-muted"><?php printf(__('支付方式：%s', 'ripro'), Capalot_Shop::get_pay_type($item->pay_type)); ?></small>

				</div>
		<?php }
			echo '</ul>';
		}
		?>
	</div>
</div>


<script type="text/javascript">
	jQuery(function($) {
		// vip-cdk-submit
		$("#vip-cdk-submit").on("click", function(e) {
			e.preventDefault();
			var _this = $(this);
			var formData = $("#vip-cdk-action").serializeArray();

			var data = {
				nonce: capalot.ajax_nonce,
			};

			formData.forEach(({
				name,
				value
			}) => {
				data[name] = value;
			});

			ca.ajax({
				data,
				beforeSend: () => {
					_this.attr("disabled", "true")
				},
				success: ({
					status,
					msg,
					icon
				}) => {
					status == 1 ?
						ca.notice({
							title: msg,
							icon: 'success'
						}) :
						ca.notice({
							title: msg,
							icon: 'error'
						});
					if (status == 1) {
						setTimeout(function() {
							window.location.reload()
						}, 2000)
					}
				},
				complete: () => {
					_this.removeAttr("disabled")
				}
			});
		});
	});
</script>