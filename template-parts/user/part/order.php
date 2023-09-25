<?php 

global $current_user;

?>

<div class="mb-4 bg-white dark:bg-dark-card p-4 mx-2 rounded">
	<div class="mb-3"><h5 class="font-bold"><?php _e('订单记录','ripro');?></h5></div>

	<div class="card-body">
		<div class=" mb-2 text-sm"><?php _e('最近20条','ripro');?></div>
		<?php 

		global $wpdb;
        $table_name = $wpdb->prefix . 'capalot_order';
		$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE user_id = %d AND order_type=1 AND pay_status=1 ORDER BY create_time DESC LIMIT 20", $current_user->ID));

		if (empty($data)) {
			echo '<p class="p-4 text-center">' . __('暂无记录','ripro' ) . '</p>';
		}else{

			echo '<div class="bg-[#ededed] rounded border dark:bg-dark dark:border-transparent border-[#dadada] ">';
			foreach ($data as $item) : ?>
				<a target="_blank" href="<?php echo $retVal = (get_permalink($item->post_id)) ? get_permalink($item->post_id) : '';?>" class="px-4 my-2 block ">
					<div class=" flex justify-between w-full">
						<h6 class="mb-1 "><?php echo $retVal = (get_permalink($item->post_id)) ? get_the_title($item->post_id) : 'Null';?></h6>
						<small class="text-muted"><?php echo wp_date('Y-m-d H:i', $item->create_time);?></small>
					</div>
					<small class="text-muted block md:inline-block"><?php _e('订单号：','ripro');?><?php echo $item->order_trade_no;?></small>
					<small class="text-muted"><?php _e('支付金额：','ripro');?><?php echo $item->order_price;?></small>
					<small class="text-muted"><?php _e('支付方式：','ripro');?><?php echo Capalot_Shop::get_pay_type($item->pay_type);?></small>
				</a>
			<?php endforeach;
			echo '</div>';
		}
		?>
	</div>
	
</div>
