<?php

global $current_user;

?>

<div class="mb-4 bg-white dark:bg-dark-card p-4 mx-2 rounded uc-aff-page">
	<div class="mb-3">
		<h5 class="font-bold"><?php _e('推广中心', 'ripro'); ?></h5>
	</div>
	<div class="card-body">

		<div class="text-center mb-4">
			<h5 class="h5 text-muted mb-3"><i class="fa-solid fa-link me-1"></i><?php _e('您的推广链接', 'ripro'); ?></h5>
			<div class=" bg-success bg-opacity-10 border-2  border-dashed rounded-full p-2">
				<h5 class="user-select-all  text-[#2e67e8]"><?php echo get_user_aff_permalink(home_url() . '/register\/', $current_user->ID); ?></h5>
			</div>
		</div>

		<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
			<?php

			$user_aff_info = Capalot_Aff::get_user_aff_info($current_user->ID);

			$item = [
				'total' => __('累计佣金', 'ripro'),
				'can_be_withdraw' => __('可提现', 'ripro'),
				'withdrawing' => __('提现中', 'ripro'),
				'withdrawed' => __('已提现', 'ripro'),
			];

			$color_key = 0;

			foreach ($item as $key => $name) : $value = $user_aff_info[$key];
				$color_key++; ?>
				<!-- Counter item -->
				<div class="col">
					<div class="rounded bg-<?php echo capalot_get_color_class($color_key); ?>  bg-opacity-25 p-4">
						<h4 class="font-bold text-<?php echo capalot_get_color_class($color_key); ?>">￥<?php echo $value; ?></h4>
						<span class="text-sm"><?php echo $name; ?></span>
					</div>
				</div>
			<?php endforeach; ?>

		</div>
		<div class="w-full text-center my-2">
			<button id="user-aff-submit" data-action="capalot_user_aff_action" class="bg-black hover:bg-[#3c3c41] text-white py-1  px-5 rounded"><?php _e('申请提现', 'ripro'); ?></button>
		</div>

		<hr class="mb-2">
		<div class="my-3">
			<h6 class="flex  mb-2">
				<?php _e('已成功推广注册', 'ripro'); ?>
				<span class="badge me-1 bg-success px-1">
					<?php echo count($user_aff_info['ref_uids']); ?>
				</span>
				人
			</h6>
			<?php
			if (!empty($user_aff_info['ref_uids'])) {
				$user_i = 0;
				foreach ($user_aff_info['ref_uids'] as $uid) {
					$user_i++;
					if ($user_i <= 20) {
						$s = capalot_substr_cut(get_user_meta(intval($uid), 'nickname', 1));
						printf('<div class="avatar w-10 h-10 inline-block"><img class="avatar-img rounded-full border border-white" src="%s" title="%s"></div>', get_avatar_url($uid), $s);
					}
				}
			} else {
				echo '<p class="text-muted py-2 ">' . __('暂无用户通过您的推广链接注册', 'ripro') . '</p>';
			} ?>
		</div>

		<h6><?php _e('推广说明：', 'ripro'); ?></h6>
		<ol class="list-decimal  ml-6">
			<?php
			$list = _capalot('site_tixian_desc', array());
			foreach ($list as $key => $item) {
				printf('<li class="py-2 border-b text-muted ">%s</li>', $item['content']);
			}
			?>
		</ol>


	</div>
</div>

<div class="mb-4 bg-white dark:bg-dark-card p-4 mx-2 rounded">
	<div class="card-header mb-2">
		<h5 class="fw-bold mb-0"><?php _e('佣金记录', 'ripro'); ?></h5>
	</div>

	<div class="card-body">
		<div class="card-header mb-2"><?php _e('最近20条', 'ripro'); ?></div>
		<?php

		global $wpdb;
		$table_aff = $wpdb->prefix . 'capalot_aff';
		$table_order = $wpdb->prefix . 'capalot_order';

		// 查询语句
		$query = $wpdb->prepare(
			"SELECT a.*, CONVERT(a.aff_rate * b.pay_price, DECIMAL(10,2)) AS aff_money, b.pay_price, b.user_id AS pay_user, b.post_id, b.order_type, b.order_trade_no FROM $table_aff AS a LEFT JOIN $table_order AS b ON a.order_id = b.id WHERE b.id IS NOT NULL AND a.aff_uid = %d ORDER BY a.create_time DESC LIMIT 20",
			$current_user->ID
		);

		$data = $wpdb->get_results($query);

		if (empty($data)) {
			echo '<p class="p-4 text-center">' . __('暂无记录', 'ripro') . '</p>';
		} else {

			echo '<div class="bg-[#ededed] rounded border dark:bg-dark dark:border-transparent border-[#dadada]">';
			foreach ($data as $item) : ?>
				<div class="px-4 my-2 block ">
					<div class="flex justify-between w-ful">
						<h6 class="text-muted block md:inline-block"><?php _e('推广类型', 'ripro'); ?>（<?php echo $item->note; ?>）<?php _e('购买人：', 'ripro'); ?><?php echo capalot_substr_cut(get_user_meta(intval($item->pay_user), 'nickname', 1)); ?></h6>
						<small class="text-muted"><?php echo wp_date('Y-m-d H:i', $item->create_time); ?></small>
					</div>
					<small class="text-muted d-block d-md-inline-block"><?php _e('订单金额：￥', 'ripro'); ?><?php echo $item->pay_price; ?></small>
					<small class="text-muted"><?php _e('佣金收益：', 'ripro'); ?><?php echo ($item->aff_rate * 100); ?>% ~ ￥<?php echo $item->aff_money; ?></small>
					<small class="text-muted"><?php _e('状态：', 'ripro'); ?><?php echo Capalot_Aff::get_aff_status($item->status); ?></small>
				</div>
		<?php endforeach;
			echo '</div>';
		}
		?>
	</div>

</div>

<script type="text/javascript">
	jQuery(function($) {
		// user-aff-submit
		$("#user-aff-submit").on("click", function(e) {
			e.preventDefault();
			var _this = $(this);
			var data = {
				nonce: capalot.ajax_nonce,
				action: _this.data("action")
			};
			ca.ajax({
				data,
				beforeSend: () => {
					_this.attr("disabled", "true")
				},
				success: ({
					status,
					msg
				}) => {
					if (status === 1) {
						ca.notice({
							title: msg,
							icon: 'success',
						});
						setTimeout(function() {
							window.location.reload()
						}, 2000)
					} else {
						ca.notice({
							title: msg,
							icon: 'error',
						});
					}
				},
				complete: () => {
					_this.removeAttr("disabled")
				}
			});
		});
	});
</script>