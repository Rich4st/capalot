<?php

global $current_user;

$site_vip_options = get_site_vip_options();
$site_vip_buy_options = get_site_vip_buy_options();
$uc_vip_info = get_user_vip_data($current_user->ID);

//颜色配置
$vip_colors = [
    'no'      => 'secondary',
    'vip'     => 'success',
    'boosvip' => 'warning',
];

$price_shape = get_template_directory_uri().'/assets/img/price_shape.png';

?>


<div class="mb-4 bg-white   dark:bg-dark-card mx-2 rounded p-4">
	<div class="mb-3">
		<h5 class="font-bold"><?php _e('会员中心', 'ripro'); ?></h5>
	</div>
	
	<div class="flex justify-between items-start  mb-4">
		<div class="flex items-center justify-center mb-3">
			<div class="me-2">
				<div class=" mb-2">
					<img class="avatar-img rounded-full border border-white  shadow" src="<?php echo get_avatar_url($current_user->ID); ?>" alt="user">
				</div>
			</div>
			<div class="ms-2 ">
				<h5 class="flex items-center mb-1">
				<?php echo $current_user->display_name; ?><?php echo capalot_get_user_badge($current_user->ID,'span','mb-0 ms-2'); ?>
				</h5>
				<div class="mb-1">
					<span><?php echo $current_user->user_login; ?></span>
					<?php
					
					if ($uc_vip_info['type'] != 'no') {
						printf('<span>%s%s</span>', $uc_vip_info['end_date'],__('到期', 'ripro') );
					} else {
						printf('<span>%s%s</span>', $current_user->user_registered,__('加入', 'ripro') );
					}
					?>
				</div>
			</div>
		</div>
		
		<div class="row row-cols-2 row-cols-md-4 g-2 g-md-4">
			<?php
			$item = [
				'total' => __('每天可下载数', 'ripro'),
	            'used' => __('今日已用次数', 'ripro'),
	            'not' => __('今日剩余次数', 'ripro'),
	            'rate' => __('下载使用率', 'ripro'),
	        ];
	        $uc_vip_info['downnums']['rate'] = ($uc_vip_info['downnums']['used'] / $uc_vip_info['downnums']['total'] * 100).'%';

	        $color_key = 1;
			foreach ($item as $key => $name) : $value = $uc_vip_info['downnums'][$key];$color_key++;?>
			<div class="col">
				<div class="card bg-<?php echo zb_get_color_class($color_key);?> bg-opacity-25 p-4 h-100 rounded-2">
					<h4 class="fw-bold text-<?php echo zb_get_color_class($color_key);?>"><?php echo $value;?></h4>
					<span class="h6 mb-0 text-muted"><?php echo $name;?></span>
				</div>
			</div>
			<?php endforeach;?>

		</div>
	</div>



	<div class="card-body mb-4">
		<div class="card-header mb-3">
			<h5 class="fw-bold mb-0"><?php _e('会员开通', 'ripro'); ?></h5>
		</div>
		<ol class="list-group list-group-numbered">
		<?php foreach (_capalot('site_buyvip_desc',array()) as $text) {
			echo '<li class="list-group-item list-group-item-light text-muted">'.$text['content'].'</li>';
		}?>
		</ol>
	</div>

	<div class="card-body">

    	<div class="row row-cols-1 row-cols-md-3 g-3 justify-content-center">
			<?php foreach ($site_vip_buy_options as $day => $item) :
				if ($item['day_num']==9999) {
					$day_title = __('永久', 'ripro');
				}else{
					$day_title = sprintf(__('%s天', 'ripro'),$item['day_num']);
				}
				$rate_day_coin = round($item['coin_price'] / $item['day_num'], 0);
			?>

			<div class="col">
				<div class="price-card text-center">
				    <div class="price-header bg-<?php echo $vip_colors[$item['type']];?> bg-opacity-10">

				        <span class="price-plan"><?php echo $item['buy_title'];?></span>

				        <h3 class="price-ammount"><?php echo $item['coin_price'];?><sup><?php echo get_site_coin_name();?></sup></h3>

				        <span class="price-sub text-<?php echo $vip_colors[$item['type']];?>"><i class="far fa-gem me-1"></i><?php echo $item['name'];?><sup><?php echo $day_title;?></sup></span>

				    </div>
				    <div class="price-body">
				        <ul class="price-desc">
				        	<li><?php printf(__('尊享%s特权%s', 'ripro'),$item['name'],$day_title);?></li>
				        	<?php foreach ($item['desc'] as $text) :?>
				        	<li><?php echo $text;?></li>
				        	<?php endforeach;?>
				        </ul>
				    </div>
				    <div class="price-footer">
				    	<?php
				    	$btn_text = __('立即开通', 'ripro');
				    	$disabled = '';
				    	if ($uc_vip_info['type'] == 'boosvip') {
			    			$btn_text = __('已获得权限', 'ripro');
			    			$disabled = 'disabled';
			    		}elseif ($uc_vip_info['type'] == 'vip' && $item['type']=='vip') {
			    			$btn_text = __('立即续费', 'ripro');
			    		}elseif ($uc_vip_info['type'] == 'vip' && $item['type']=='boosvip') {
			    			$btn_text = __('立即升级', 'ripro');
			    		}
				    	?>
				    	<button class="btn btn-<?php echo $vip_colors[$item['type']];?>-soft js-pay-action" data-id="0" data-type="3" data-info="<?php echo $item['day_num'];?>" <?php echo $disabled;?>><i class="far fa-gem me-1"></i><?php echo $btn_text;?></button>
				    </div>
				</div>
			</div>
			<?php endforeach;?>

		</div>



	</div>

</div>

<?php if (!empty(_capalot('is_site_cdk_pay',true))) :?>
<div class="mb-4 bg-white dark:bg-dark-card mx-2 rounded vip-cdk-body p-4">
	<div class="mb-3">
		<h5 class="font-bold"><?php _e('会员兑换','ripro');?></h5>
	</div>
	<div class="card-body">
		<h5 class="text-center mb-4 text-muted"><?php _e('使用CDK码兑换VIP特权','ripro');?></h5>
		<form class="w-64 flex flex-col mx-auto" id="vip-cdk-action">
            <div class="mb-3">
                <input type="text" class="bg-[#ededed] w-full h-8 px-2 rounded dark:bg-dark" name="cdk_code" placeholder="兑换码/CDK卡号" value="">
            </div>
            <div class="flex flex-row space-x-2 mb-3">
	          <input type="text" class="bg-[#ededed] w-full h-8 px-2 rounded dark:bg-dark rounded-2" name="captcha_code" placeholder="验证码">
	          <img id="captcha-img" class="rounded-2 w-full h-8 bg-[#cacaca]  text-white mx-2 rounded" role="button" data-src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/captcha.png');?>" title="<?php _e('点击刷新验证码','ripro');?>" />
	        </div>
            <div class="flex flex-row justify-center space-x-2 mb-3 mt-3 text-center text-sm">
                <input type="hidden" name="action" value="zb_vip_cdk_action">
                <button type="submit" id="vip-cdk-submit" class="btn bg-[#d6293e] text-white px-4 py-1 rounded"><i class="fas fa-gift me-1"></i><?php _e('立即兑换','ripro');?></button>
                <a class="btn bg-[#f7c32e]  px-4 py-1 rounded" target="_blank" href="<?php echo _capalot('site_cdk_pay_link');?>" rel="nofollow noopener noreferrer"><i class="fas fa-external-link-alt me-1"></i><?php _e('购买CDK','ripro');?></a>

            </div>
        </form>
	</div>
</div>
<?php endif;?>

<div class="mb-4 bg-white   dark:bg-dark-card mx-2 rounded p-4">
	<div class="mb-2">
		<caption class="font-bold "><?php _e('VIP获取记录（最近10条）', 'ripro'); ?></caption>
	</div>

	<div class="card-body pay-vip-log">
	    <?php
	    global $wpdb;
	    $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->cao_order_tbl} WHERE user_id = %d AND order_type=3 AND pay_status=1 ORDER BY create_time DESC LIMIT 10", $current_user->ID));

	    if (empty($data)) {
	        echo '<p class="p-4 text-center">' . __('暂无记录', 'ripro') . '</p>';
	    } else {
	        echo '<ul class="list-group mt-2">';
	        foreach ($data as $key => $item) {
	            $info = maybe_unserialize($item->order_info);
	            $vip_info = $site_vip_options[$info['vip_type']];
	            ?>
	            <div class="list-group-item list-group-item-action">
	                <div class="flex w-full justify-between">
	                    <h6 class="mb-1"><?php printf(__('订单类型：%s', 'ripro'), $vip_info['name']);?></h6>
	                    <small class="text-muted"><?php echo wp_date('Y-m-d H:i', $item->create_time);?></small>
	                </div>
	                <small class="text-muted"><?php printf(__('支付金额：￥%1$s（%2$s）', 'ripro'),$item->order_price, site_convert_amount($item->order_price,'coin') . get_site_coin_name() );?></small>

	                <small class="text-muted"><?php printf(__('支付方式：%s', 'ripro'), Capalot_Shop::get_pay_type($item->pay_type));?></small>

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
              nonce: zb.ajax_nonce,
            };

            formData.forEach(({ name, value }) => {
              data[name] = value;
            });

	        ri.ajax({
	            data,
	            before: () => {
	                _this.attr("disabled", "true")
	            },
	            result: ({
	                status,
	                msg
	            }) => {
	                ri.notice(msg);
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
