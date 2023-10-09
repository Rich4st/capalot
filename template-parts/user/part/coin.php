<?php

global $current_user;

?>


<div class="mb-4 bg-gradient-to-l from-[#fffdea] to-white dark:from-black dark:to-gray-400 mx-2 rounded  coin-balance-body p-4">
	<?php if (is_site_qiandao()) : ?>
		<div class="balance-qiandao flex justify-end text-sm">
			<?php if (!is_user_today_qiandao($current_user->ID)) : ?>
				<a class="user-qiandao-action btn p-2 rounded text-black dark:text-gray-400 bg-[#fad877]" href="javascript:;"><i class="fa fa-check-square me-1"></i><?php _e('签到领取', 'ripro'); ?><?php echo get_site_coin_name(); ?></a>
			<?php else : ?>
				<a class="btn p-1  text-black dark:text-gray-400" href="javascript:;"><i class="fa fa-check-square me-1"></i><?php _e('今日已签到', 'ripro'); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<div class="balance-info text-center text-xl text-[#ff9800]">
		<div><?php _e('当前账户余额', 'ripro'); ?></div>
		<hr class="border-[#ffe7bf] block overflow-hidden w-36 mx-auto">
		<div><?php printf('<i class="%s me-1"></i>%s%s', get_site_coin_icon(), get_user_coin_balance($current_user->ID), get_site_coin_name()); ?></div>
	</div>
</div>

<div class="mb-4 bg-white   dark:bg-dark-card mx-2 rounded p-4">

	<div class="mb-4">
		<div class="mb-3">
			<h5 class="font-bold"><?php _e('充值余额', 'ripro'); ?></h5>
		</div>
		<?php
		$site_mycoin_pay_arr = _capalot('site_mycoin_pay_arr');
		$site_mycoin_pay_arr = empty($site_mycoin_pay_arr) ? [] : explode(",", $site_mycoin_pay_arr);
		?>


		<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 ">
			<?php foreach ($site_mycoin_pay_arr as $num) : ?>

				<div class="cursor-pointer">
					<div class="coin-pay-card  border  rounded py-2 text-center" data-num="<?php echo absint($num); ?>">
						<h5 class="mb-1 text-[#ffc107]"><?php echo absint($num) . get_site_coin_name(); ?></h6>
							<p class="text-muted">￥<?php echo site_convert_amount(absint($num), 'rmb'); ?></p>
					</div>
				</div>
			<?php endforeach; ?>

		</div>

		<div class="text-center">
			<button class="rounded bg-[#fad877] py-1 px-5 js-pay-action" data-id="0" data-type="2" data-info="0" data-text="<?php _e('充值', 'ripro'); ?>" disabled><i class="fab fa-shopify me-1"></i><span><?php _e('请选择充值数量', 'ripro'); ?></span></button>
		</div>

	</div>

	<div class="mb-4">
		<div class="mb-3">
			<h5 class="font-bold "><?php _e('充值说明', 'ripro'); ?></h5>
		</div>
		<ol class="list-decimal  ml-6">
			<?php
			$mycoin_pay_desc = _capalot('site_mycoin_pay_desc');
			$mycoin_pay_desc = empty($mycoin_pay_desc) ? [] : explode("\n", $mycoin_pay_desc);
			?>
			<?php foreach ($mycoin_pay_desc as $text) {
				echo '<li class="py-2 border-b-2 text-muted ">' . $text . '</li>';
			} ?>
		</ol>
	</div>


</div>


<?php if (!empty(_capalot('is_site_cdk_pay', true))) : ?>
	<div class="mb-4 bg-white   dark:bg-dark-card mx-2 rounded vip-cdk-body p-4">
		<div class="mb-3">
			<h5 class="font-bold "><?php printf('%s%s', get_site_coin_name(), __('兑换', 'ripro')); ?></h5>
		</div>

		<div class="card-body">
			<h5 class="text-center mb-4 text-muted"><?php _e('使用CDK码兑换站内币', 'ripro'); ?></h5>
			<form class="w-64 flex flex-col mx-auto" id="vip-cdk-action">
				<div class="mb-3">
					<input type="text" class="bg-[#ededed] h-8 w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="cdk_code" placeholder="兑换码/CDK卡号" value="">
				</div>
				<div class="flex flex-row space-x-2 mb-3">
					<input type="text" class=" bg-[#ededed] h-8 w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="captcha_code" placeholder="验证码">
					<!-- <img id="captcha-img" class="rounded-2 w-full h-8 bg-[#cacaca]  text-white mx-2 rounded" role="button" data-src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/captcha.png'); ?>" title="<?php _e('点击刷新验证码', 'ripro'); ?>" /> -->
					<img id="captcha-img" class="rounded-2 w-full h-8 bg-[#cacaca]  text-white mx-2 rounded" role="button" src="<?php echo get_template_directory_uri() ?>/assets/img/captcha.png" title="<?php _e('点击刷新验证码', 'ripro'); ?>" />
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
		<caption class="font-bold"><?php printf('%s%s', get_site_coin_name(), __('获取记录（最近10条）', 'ripro')); ?></caption>
	</div>

	<div class="card-body pay-vip-log">
		<?php
		global $wpdb;
		$table_name = $wpdb->prefix . 'capalot_order';
		$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE user_id = %d AND order_type=2 AND pay_status=1 ORDER BY create_time DESC LIMIT 10", $current_user->ID));

		if (empty($data)) {
			echo '<p class="p-4 text-center">' . __('暂无记录', 'ripro') . '</p>';
		} else {
			echo '<ul class="list-group mt-2">';
			foreach ($data as $key => $item) { ?>
				<div class="list-group-item list-group-item-action">
					<small class="text-muted"><?php echo wp_date('Y-m-d H:i', $item->create_time); ?></small>
					<small class="text-muted"><?php printf(__('充值金额：￥%1$s（%2$s）', 'ripro'), $item->order_price, site_convert_amount($item->order_price, 'coin') . get_site_coin_name()); ?></small>

					<small class="text-muted">
						支付方式：<?php echo Capalot_Shop::get_pay_type($item->pay_type); ?>
					</small>

				</div>
		<?php }
			echo '</ul>';
		}
		?>
	</div>
</div>


<script type="text/javascript">
	jQuery(function($) {

		$(".coin-pay-card").click(function() {
			var amount = $(this).data("num");
			var paybtn = $(".js-pay-action");
			paybtn.data("info", amount).removeAttr("disabled");
			paybtn.find("span").text(paybtn.data("text") + amount);
			$(this).addClass("active").parent().siblings().find(".coin-pay-card").removeClass("active");
		});

		$(".user-qiandao-action").on("click", ca.debounce(function() {
			var _this = $(this);

			var iconEl = _this.find('i');
			var def_icon = iconEl.attr('class');
			var spinner_icon = 'fa-solid fa-spinner fa-spin me-1';

			var data = {
				nonce: capalot.ajax_nonce,
				action: 'capalot_user_qiandao'
			};
			ca.ajax({
				data,
				beforeSend: () => {
					iconEl.removeClass().addClass(spinner_icon);
					// user-qiandao-action增加class disabled
					_this.addClass('pointer-events-none');
				},
				complete: ({
					responseJSON
				}) => {
					const {
						status,
						msg
					} = responseJSON;

					if (status === 1) {
						ca.notice({
							title: msg,
							icon: 'success',
						});
						setTimeout(function() {
							window.location.reload()
						}, 1000)
					} else {
						ca.notice({
							title: msg,
							icon: 'error',
						});
					}
				},
			});
		}, 500));

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
				}) => {
					ca.notice({
						title: msg,
						icon: status == 1 ? 'success' : 'error',
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
