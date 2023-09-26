<?php 

global $current_user;

?>

<div class="mb-4 bg-white dark:bg-dark-card p-4 mx-2 rounded">
	<div class="mb-3"><h5 class="font-bold"><?php _e('下载记录','ripro');?></h5></div>

	<div class="card-body">
		<div class="mb-2 text-sm"><?php _e('最近20条','ripro');?></div>
		<?php 

		global $wpdb;
         $table_name = $wpdb->prefix . 'capalot_download';
		$data = $wpdb->get_results($wpdb->prepare(
			"SELECT *,count(post_id) as down_num FROM {$table_name} WHERE user_id = %d GROUP BY post_id ORDER BY create_time DESC LIMIT 20",
			$current_user->ID
		));

		if (empty($data)) {
			echo '<p class="p-4 text-center">'.__('暂无记录','ripro').'</p>';
		}else{

			echo '<div class="bg-[#ededed] rounded border  dark:bg-dark dark:border-transparent border-[#dadada]">';
			foreach ($data as $item) : ?>
				<a target="_blank" href="<?php echo get_permalink($item->post_id);?>" class="px-4 my-2 block ">
					<div class="flex justify-between w-full">
						<h6 class="mb-1"><?php echo get_the_title($item->post_id);?></h6>
						<small class="text-muted"><?php echo wp_date('Y-m-d H:i', $item->create_time);?></small>
					</div>
					<small class="text-muted block md:inline-block"><?php _e('下载IP：','ripro');?><?php echo $item->ip;?></small>
					<small class="text-muted"><?php _e('下载次数：','ripro');?><?php echo $item->down_num;?></small>
				</a>
			<?php endforeach;
			echo '</div>';
		}
		?>
	</div>
	
</div>
