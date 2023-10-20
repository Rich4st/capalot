<?php

global $current_user, $wpdb;

$page_action = get_response_param('action', '', 'get');
$ticket_link = get_uc_menu_link('ticket');
$list_link = add_query_arg(array('action' => 'list'), $ticket_link);
$new_link = add_query_arg(array('action' => 'new'), $ticket_link);
$view_id = get_response_param('id', 0, 'get');
$is_delete = get_response_param('delete', 0, 'get');

?>

<?php if ($page_action == 'new') : ?>
	<!-- 新建工单 -->
	<div class="mb-4 bg-white dark:bg-dark-card p-4 mx-2 rounded">
		<div class="mb-3">
			<h5 class="font-bold pb-4"><?php _e('新建工单', 'ripro'); ?>
				<a href="<?php echo esc_url($list_link); ?>" class="px-2 py-1 text-sm rounded bg-dark bg-opacity-75 ms-2 text-white"><?php _e('返回工单列表', 'ripro'); ?></a>
			</h5>
			<hr>
		</div>
		<div class="card-body">
			<form class="gap-4 grid lg:grid-cols-4 grid-cols-1" id="ticket-form">
				<!-- Input item -->
				<div class="lg:col-span-2 col-span-1 col-start-1 pb-2">
					<label class="pb-2 block text-gray-500"><?php _e('创建人', 'ripro'); ?></label>
					<input type="text" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" value="<?php echo $current_user->display_name; ?> (<?php echo $current_user->user_login; ?>)" disabled>
				</div>
				<!-- Input item -->
				<div class="lg:col-span-2 col-span-1 col-start-1 pb-2">
					<label class="pb-2 block text-gray-500"><?php _e('工单类型', 'ripro'); ?></label>
					<select name="type" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
						<?php $option = [1, 2, 3, 4];
						foreach ($option as $value) {
							echo '<option value="' . $value . '">' . Capalot_Ticket::get_type($value) . '</option>';
						} ?>
					</select>

				</div>
				<div class="lg:col-span-4 col-span-1 col-start-1">
					<label class="pb-2 block text-gray-500"><?php _e('工单标题', 'ripro'); ?></label>
					<input type="text" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="title" placeholder="" value="">
				</div>
				<!-- Textarea item -->
				<div class="lg:col-span-4 col-span-1 col-start-1">
					<label class="pb-2 block text-gray-500"><?php _e('描述', 'ripro'); ?></label>
					<textarea class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" rows="8" name="content"></textarea>
				</div>

				<!-- Save button -->
				<div class="flex lg:col-start-4 justify-end text-white">
					<input type="hidden" name="action" value="capalot_user_save_ticket">
					<button type="submit" id="save-ticket" class="bg-black py-1 px-2 rounded hover:bg-[#3c3c41]"><?php _e('提交工单', 'ripro'); ?></button>
				</div>
			</form>
		</div>
	</div>


<?php elseif ($page_action == 'view') : ?>
	<!-- 查看工单详情 -->
	<div class="mb-4 bg-white dark:bg-dark-card p-4 mx-2 rounded">
		<div class="mb-2">
			<h5 class="font-bold mb-4"><?php _e('工单详情', 'ripro'); ?>
				<a href="<?php echo $list_link; ?>" class="p-1 rounded bg-dark text-sm text-white bg-opacity-75 ms-2"><?php _e('返回工单列表', 'ripro'); ?></a>
			</h5>
			<hr>
		</div>
		<div class="card-body">
			<?php

			$data = Capalot_Ticket::get($view_id);

			if (empty($data)) : ?>
				<p class="p-4 text-center fs-4 text-muted"><?php _e('获取工单信息失败', 'ripro'); ?></p>
			<?php else : ?>

				<?php if ($is_delete == 1) : Capalot_Ticket::delete($data->id); ?>
					<script type="text/javascript">
						var url = window.location.href;
						url = url.split('?')[0]; // 截取问号及其后面的字符串
						window.location.replace(url);
					</script>
				<?php exit;
				endif; ?>

				<?php if ($data->status == 2) {
					// 更新查看状态关闭工单
					Capalot_Ticket::update(
						['status' => 3, 'updated_time' => time()],
						['id' => $data->id],
						['%d', '%s'],
						['%d']
					);
				} ?>
				<div class="mb-3">
					<div class="mb-3 text-muted text-[#9497a4] flex items-center ">
						<span class=" w-12 h-12 mr-2">
							<img class="rounded-full lazy" data-src="<?php echo get_avatar_url($data->creator_id); ?>" alt="avatar">
						</span>
						<span class="ms-1"><?php echo $current_user->display_name; ?> <?php echo wp_date('Y-m-d H:i', $data->create_time); ?><?php _e('提交','ripro');?></span>
						<span class="ms-2">【<?php echo Capalot_Ticket::get_type($data->type); ?>】</span>
						<span class="ms-2"><?php _e('状态','ripro')?>：(<?php echo Capalot_Ticket::get_status($data->status); ?>)</span>
					</div>
					<h5 class="font-bold mb-2"><i class="fas fa-question-circle me-1"></i><?php echo esc_html($data->title); ?></h5>
					<div class="p-2 lg:p-3 bg-info bg-opacity-25 rounded-2">
						<?php echo $data->content; ?>
						<?php if (!empty($data->file)) : ?>
							<div class="mt-2"><a class="btn-link text-muted" href="<?php echo esc_url($data->file); ?>" onclick="event.preventDefault(); document.getElementById('flieImage').src=this.href"><?php _e('查看附件', 'ripro'); ?></a><img class="border border-white border-3 shadow" id="flieImage" src=""></div>
						<?php endif; ?>
					</div>

				</div>

				<?php if (!empty($data->reply_content)) : ?>
					<hr>
					<div class="mb-3 my-4">
						<div class="mb-3 small text-muted flex items-center text-[#9497a4]">
							<span class="w-12 h-12 mr-2">
								<img class="rounded-full lazy" data-src="<?php echo get_avatar_url($data->assignee_id); ?>">
							</span>
							<span class="ms-1"><?php echo get_userdata($data->assignee_id)->display_name; ?> <?php echo wp_date('Y-m-d H:i', $data->reply_time); ?> <?php _e('工单回复内容：', 'ripro'); ?></span>
						</div>
						<div class="p-2 lg:p-3 bg-success bg-opacity-25 rounded-2">
							<?php echo $data->reply_content; ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="flex justify-end  mt-3">
					<a href="<?php echo esc_url(add_query_arg(array('action' => 'view', 'id' => $data->id, 'delete' => 1), $ticket_link)); ?>" class="bg-[#d6293e] text-white px-2 py-1 rounded"><?php _e('删除工单', 'ripro'); ?></a>
				</div>

			<?php endif; ?>


		</div>
	</div>


<?php else : ?>
	<!-- 工单列表 -->
	<div class="mb-4 bg-white dark:bg-dark-card p-4 mx-2 rounded">
		<div class="mb-4">
			<h5 class="font-bold"><?php _e('工单列表', 'ripro'); ?>
				<a href="<?php echo esc_url($new_link); ?>" class="p-1 rounded text-white bg-dark text-sm bg-opacity-75 ms-2"><?php _e('新建工单', 'ripro'); ?></a>
			</h5>
		</div>
		<div class="card-body">

			<?php
			global $wpdb;
			$table_name = $wpdb->prefix . 'capalot_ticket';
			$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE creator_id = %d ORDER BY create_time DESC,updated_time DESC LIMIT 20", $current_user->ID));

			$counts = $wpdb->get_results(
				$wpdb->prepare("SELECT status,COUNT(id) as count FROM {$table_name} WHERE creator_id = %d GROUP BY status ORDER BY status ASC", $current_user->ID),
				ARRAY_A
			);

			?>
			<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
				<?php
				$item = [0, 1, 2, 3];
				$color_key = 0;
				foreach ($item as $key => $status) :
					$target_count = 0;
					foreach ($counts as $count) {
						if (isset($count["status"]) && $count["status"] == $status) {
							$target_count = $count["count"];
							break;
						}
					}
					$color_key++;
				?>
					<div class="col">
						<div class="card text-center bg-<?php echo capalot_get_color_class($color_key); ?> bg-opacity-25 p-4 h-full rounded">
							<h4 class="font-bold text-<?php echo capalot_get_color_class($color_key); ?>"><?php echo $target_count; ?></h4>
							<span class="h6 text-muted"><?php echo Capalot_Ticket::get_status($key); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div class=" mb-2"><?php _e('最近20条', 'ripro'); ?></div>

			<?php
			if (empty($data)) {
				echo '<p class="p-4 text-center">' . __('暂无记录', 'ripro') . '</p>';
			} else {

				echo '<div class="bg-[#ededed] rounded border dark:bg-dark dark:border-transparent border-[#dadada]">';
				foreach ($data as $item) : ?>

					<?php switch ($item->status) {
						case '2':
							$color = 'success';
							break;
						case '3':
							$color = 'dark';
							break;
						default:
							$color = 'danger';
							break;
					} ?>

					<a href="<?php echo esc_url(add_query_arg(array('action' => 'view', 'id' => $item->id), $ticket_link)); ?>" class="px-4 my-2 block ">
						<div class="flex items-center">
							<div class="w-12 h-12">
								<img class="avatar-img rounded-full lazy" data-src="<?php echo get_avatar_url($item->creator_id); ?>" alt="avatar">
							</div>
							<div class="w-full pl-2">
								<div class="flex w-full justify-between ">
									<h6 class="font-bold mb-1"><?php echo esc_html($item->title); ?></h6>
									<small class="text-muted"><?php echo wp_date('Y-m-d H:i', $item->create_time); ?></small>
								</div>
								<span class="p-1 rounded text-sm bg-opacity-10 text-<?php echo esc_attr($color); ?> bg-<?php echo esc_attr($color); ?>"><?php echo Capalot_Ticket::get_status($item->status); ?></span>
								<span class="badge text-sm"><?php echo Capalot_Ticket::get_type($item->type); ?></span>
							</div>
						</div>
					</a>

			<?php endforeach;
				echo '</div>';
			}
			?>

		</div>
	</div>

<?php endif; ?>



<script type="text/javascript">
	jQuery(function($) {

		$('.delete-ticket').click(function(e) {
			return confirm(capalot.gettext.__is_delete_n);
		});

		//提交工单
		$("#save-ticket").on("click", function(e) {
			e.preventDefault();
			var _this = $(this);
			var formData = $("#ticket-form").serializeArray();

			var data = {
				nonce: capalot.ajax_nonce,
			};

			formData.forEach(({
				name,
				value
			}) => {
				data[name] = value;
			});


			if (!data.title) {
				$('input[name="title"]').focus();
				return;
			}
			if (!data.content) {
				$('textarea[name="content"]').focus();
				return;
			}

			var url = window.location.href;
			url = url.split('?')[0]; // 截取问号及其后面的字符串

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
					status == 1 ? ca.notice({
						title: msg,
						icon: 'success'
					}) : ca.notice({
						title: msg,
						icon: 'error'
					});
					if (status == 1) {
						setTimeout(function() {
							window.location.replace(url);
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