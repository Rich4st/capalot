<?php

defined('ABSPATH') || exit;


if (is_user_logged_in()) {
	wp_safe_redirect(get_uc_menu_link());
	exit;
}


$is_login_action   = (get_query_var('uc-login-page') == 1) ? true : false;
$is_reg_action     = (get_query_var('uc-register-page') == 1) ? true : false;
$is_lostpwd_action = (get_query_var('uc-lostpwd-page') == 1) ? true : false;

if ($is_login_action && !is_site_user_login()) {
	capalot_wp_die('本站登录功能暂时关闭', '本站登录功能暂时关闭', home_url(), '返回首页');
} elseif ($is_reg_action && !is_site_user_register()) {
	capalot_wp_die('本站注册功能暂时关闭', '本站注册功能暂时关闭', home_url(), '返回首页');
}

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> data-bs-theme="<?php echo get_site_default_color_style(); ?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body>


	<main>


		<?php

		$args = [
			'bg_type' => _capalot('site_loginpage_bg_type', 'img'),
			'bg_img' => _capalot('site_loginpage_bg_img', get_template_directory_uri() . '/assets/img/bg.jpg'),
			'color' => _capalot('site_loginpage_color', array('bgcolor' => '#005588', 'color' => '#ededed')),
			'birds_color' => _capalot('birds_color'),
			'fog_color' => _capalot('fog_color'),
			'waves_color' => _capalot('waves_color'),
			'net_color' => _capalot('net_color'),
		];

		if ($args['bg_type'] == 'img') {
			$classex = 'bg-type-' . $args['bg_type'] . ' lazy';
		} else {
			$classex = 'bg-type-' . $args['bg_type'];

			$base_config = [
				'el' => '.login-and-register',
				'mouseControls' => true,
				'touchControls' => true,
				'gyroControls' => false,
				'minHeight' => 200.00,
				'minWidth' => 200.00,
				'scale' => 1.00,
				'scaleMobile' => 1.00,
			];

			$vanta_configs = array(

				'birds' => [
					'name' => 'BIRDS',
					'config' => array_merge($base_config, [
						'backgroundColor' => $args['birds_color']['bgcolor'],
						'color1' => $args['birds_color']['color1'],
						'color2' => $args['birds_color']['color2'],
					])
				],

				'fog' => [
					'name' => 'FOG',
					'config' => array_merge($base_config, [
						'highlightColor' => $args['fog_color']['highlight_color'],
						'midtoneColor' => $args['fog_color']['midtone_color'],
						'lowlightColor' => $args['fog_color']['lowlight_color'],
						'baseColor' => $args['fog_color']['base_color']
					])
				],

				'waves' => [
					'name' => 'WAVES',
					'config' => array_merge($base_config, [
						'color' => $args['waves_color']['color'],
					])
				],

				'net' => [
					'name' => 'NET',
					'config' => array_merge($base_config, [
						'color' => $args['net_color']['color'],
						'backgroundColor' => $args['net_color']['bgcolor'],
					])
				]

			);

			$vanta = $vanta_configs[$args['bg_type']];
		}

		?>

		<div class="login-and-register h-screen flex justify-center items-center bg-cover object-center bg-center bg-no-repeat <?php echo $classex; ?>"
		style="background-image: url(<?php echo esc_url($args['bg_img']); ?>);">
			<div class="bg-white shadow rounded-md overflow-hidden py-16 px-10 text-center">
				<!-- Logo -->
				<a class="flex justify-center items-center mb-3" href="<?php echo esc_url(home_url()); ?>">
					<img class="logo regular mb-2 w-10 h-10" src="<?php echo esc_url(_capalot('site_logo', '')); ?>" alt="<?php echo get_bloginfo('name'); ?>">
				</a>

				<form id="account-from" class="text-start account-from space-y-4">

					<?php if ($is_login_action) : ?>
						<!-- 登录表单 -->
						<div class="flex flex-col">
							<label class="form-label mb-2 text-gray-500">邮箱或用户名</label>
							<input type="text" class="form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="user_name">
						</div>
						<div class="flex flex-col">
							<label class="form-label text-gray-500 mb-2"><?php _e('密码', 'ripro'); ?><a class="text-gray-500 text-sm hover:underline ml-2" href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php _e('忘记密码？', 'ripro'); ?></a></label>
							<input class="form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" type="password" autocomplete="TRUE" name="user_password">
						</div>

						<?php if (is_site_img_captcha()) : ?>
							<div class="mb-3 flex justify-between items-center">
								<input type="text" class="form-control mr-10 focus:invalid:border-pink-500 focus:invalid:ring-pink-500 p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="captcha_code" placeholder="验证码">
								<img id="captcha-img" class="rounded-2 lazy" role="button" data-src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/captcha.png'); ?>" title="<?php _e('点击刷新验证码', 'ripro'); ?>" />
							</div>
						<?php endif; ?>

						<div class="mb-3 flex items-center">
							<input id="rememberCheck" type="checkbox" class="form-check-input" name="remember" checked>
							<label class="form-check-label text-gray-500 text-sm ml-2" for="rememberCheck">记住登录状态？</label>
						</div>
						<input type="hidden" name="action" value="capalot_user_login">

						<?php if (is_site_user_register()) : ?>
							<p class="mb-3 text-gray-500">新用户？
								<a class="hover:underline text-blue-500" href="<?php echo esc_url(wp_registration_url()); ?>">注册账号</a>
							</p>
						<?php endif; ?>

						<div>
							<button type="submit" id="click-submit" class="p-2 bg-sky-600 w-full rounded-sm text-white">立即登录</button>
						</div>

					<?php elseif ($is_reg_action) : ?>
						<!-- 注册表表单 -->
						<div class="flex flex-col">
							<label class="form-label mb-2 text-gray-500"><?php _e('用户名*', 'ripro'); ?></label>
							<input type="text" class="form-control focus:invalid:border-pink-500 focus:invalid:ring-pink-500 p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="user_name" placeholder="<?php _e('英文名称', 'ripro'); ?>">
						</div>
						<div class="flex flex-col">
							<label class="form-label mb-2 text-gray-500"><?php _e('邮箱*', 'ripro'); ?></label>
							<input type="email" class="form-control focus:invalid:border-pink-500 focus:invalid:ring-pink-500 p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="user_email" placeholder="<?php _e('邮箱地址', 'ripro'); ?>">
						</div>
						<div class="flex flex-col">
							<label class="form-label mb-2 text-gray-500"><?php _e('密码*', 'ripro'); ?></label>
							<input class="form-control focus:invalid:border-pink-500 focus:invalid:ring-pink-500 p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" type="password" autocomplete="TRUE" name="user_password" placeholder="<?php _e('密码', 'ripro'); ?>">
						</div>
						<div class="flex flex-col">
							<input class="form-control focus:invalid:border-pink-500 focus:invalid:ring-pink-500 p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" type="password" name="user_password_ok" placeholder="<?php _e('确认输入密码', 'ripro'); ?>">
						</div>

						<?php if (is_site_invitecode_register()) : ?>
							<div class="mb-3">
								<label class="form-label"><?php _e('邀请码* ', 'ripro'); ?><a target="_blank" class="ms-2 small text-danger" href="<?php echo _capalot('site_invitecode_get_url'); ?>"><?php _e('获取邀请码', 'ripro'); ?></a></label>
								<input type="text" class="form-control" name="invite_code" placeholder="必填">
							</div>
						<?php endif; ?>

						<?php if (is_site_img_captcha()) : ?>
							<div class="mb-3 flex justify-between items-center">
								<input type="text" class="form-control mr-10 focus:invalid:border-pink-500 focus:invalid:ring-pink-500 p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="captcha_code" placeholder="验证码">
								<img id="captcha-img" class="rounded-2 lazy" role="button" data-src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/captcha.png'); ?>" title="<?php _e('点击刷新验证码', 'ripro'); ?>" />
							</div>
						<?php endif; ?>

						<input type="hidden" name="action" value="capalot_user_register">

						<?php if (is_site_user_login()) : ?>
							<p class="mb-3 text-gray-500">
								已有账号？
								<a class="hover:underline text-blue-500" href="<?php echo esc_url(wp_login_url()); ?>">登录账号</a>
							</p>
						<?php endif; ?>

						<!-- Button -->
						<button type="submit" id="click-submit" class="p-2 bg-sky-600 w-full rounded-sm text-white"><?php _e('立即注册', 'ripro'); ?>
						</button>

					<?php elseif ($is_lostpwd_action) : ?>

						<?php
						$riresetpass  = wp_unslash(get_response_param('riresetpass', false, 'get'));
						$rifrp_action = wp_unslash(get_response_param('rifrp_action', false, 'get'));
						$key          = wp_unslash(get_response_param('key', false, 'get'));
						$uid          = wp_unslash(get_response_param('uid', false, 'get'));
						$DataArr      = compact('riresetpass', 'rifrp_action', 'key', 'uid');

						foreach ($DataArr as $key => $value) {
							$is_riresetpass_from = (!empty($value)) ? true : false;
						}

						if (!empty($is_riresetpass_from)) :
							foreach ($DataArr as $key => $value) {
								echo '<input type="hidden" name="' . $key . '" value="' . $value . '">';
							} ?>

							<p class="mb-3 small text-danger text-center"><?php _e('您正在重新设置账号密码', 'ripro'); ?></p>
							<div class="mb-3">
								<label class="form-label"><?php _e('新密码', 'ripro'); ?></label>
								<input class="form-control" type="password" autocomplete="TRUE" name="user_password" placeholder="<?php _e('密码', 'ripro'); ?>">
							</div>
							<div class="mb-3">
								<label class="form-label"><?php _e('确认新密码', 'ripro'); ?></label>
								<input class="form-control" type="password" name="user_password_ok" placeholder="<?php _e('确认输入密码', 'ripro'); ?>">
								<input type="hidden" name="action" value="zb_user_restpwd">
							</div>
							<div class="mb-3 flex justify-between items-center">
								<input type="text" class="form-control mr-10 focus:invalid:border-pink-500 focus:invalid:ring-pink-500 p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="captcha_code" placeholder="验证码">
								<img id="captcha-img" class="rounded-2 lazy" role="button" data-src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/captcha.png'); ?>" title="<?php _e('点击刷新验证码', 'ripro'); ?>" />
							</div>

							<?php if (is_site_user_login()) : ?>
								<p class="mb-3"><?php _e('想起密码？', 'ripro'); ?><a class="btn-link text-primary" href="<?php echo esc_url(wp_login_url()); ?>"><?php _e('登录账号', 'ripro'); ?></a></p>
							<?php endif; ?>

							<div><button type="submit" id="click-submit" class="btn btn-danger w-100 mb-3"><?php _e('立即重置密码', 'ripro'); ?></button></div>

						<?php else : ?>
							<!-- 找回密码表单 -->
							<div class="flex flex-col">
								<label class="form-label mb-2 text-gray-500"><?php _e('账号绑定的邮箱*', 'ripro'); ?></label>
								<input type="email" class="form-control focus:invalid:border-pink-500 focus:invalid:ring-pink-500 p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="user_email" placeholder="<?php _e('邮箱地址', 'ripro'); ?>">
								<input type="hidden" name="action focus:invalid:border-pink-500 focus:invalid:ring-pink-500 p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" value="zb_user_lostpwd">
							</div>

							<div class="mb-3 flex justify-between items-center">
								<input type="text" class="form-control mr-10 focus:invalid:border-pink-500 focus:invalid:ring-pink-500 p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="captcha_code" placeholder="验证码">
								<img id="captcha-img" class="rounded-2 lazy" role="button" data-src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/captcha.png'); ?>" title="<?php _e('点击刷新验证码', 'ripro'); ?>" />
							</div>

							<p class="mb-3 text-sm text-red-600"><?php _e('重置密码链接会发送到您的邮箱，请通过重置链接修改新密码。', 'ripro'); ?></p>
							<p class="mb-3 text-gray-500"><?php _e('想起密码？', 'ripro'); ?><a class="text-blue-500 hover:underline" href="<?php echo esc_url(wp_login_url()); ?>"><?php _e('登录账号', 'ripro'); ?></a></p>

							<button type="submit" id="click-submit" class="p-2 bg-sky-600 w-full rounded-sm text-white"><?php _e('找回密码', 'ripro'); ?></button>
						<?php endif; ?>

					<?php endif; ?>


					<?php do_action('login_footer'); ?>

					<!-- oauth mode -->
					<?php if (($is_login_action || $is_reg_action)) : ?>
						<?php if (_capalot('is_sns_qq', false) || _capalot('is_sns_weixin', false)) : ?>
							<div class="relative">
								<hr>
								<p class="text-sm w-32 text-gray-500 bg-white text-center absolute -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 -right-1/2"><?php _e('快捷登录/注册', 'ripro'); ?></p>
							</div>
							<div class="grid gap-2 md:block py-4  text-white text-center">
								<?php if (_capalot('is_sns_qq', false)) : ?>
									<a href="<?php echo get_oauth_permalink('qq'); ?>" class="btn bg-info hover:bg-[#4386d3]  cursor-pointer rounded py-2 px-4 mx-2"><i class="fab fa-qq me-1"></i><?php _e('QQ登录', 'ripro'); ?></a>
								<?php endif; ?>
								<?php if (_capalot('is_sns_weixin', false)) : ?>
									<a href="<?php echo get_oauth_permalink('weixin'); ?>" class="btn bg-success hover:bg-[#0aa073]  cursor-pointer rounded py-2 px-4 mx-2"><i class="fab fa-weixin me-1"></i><?php _e('微信登录', 'ripro'); ?></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<!-- Copyright -->
					<p class="text-center text-sm text-gray-400">
						<small class="text-muted">
							<?php _e('注册&登录即表示同意本站', 'ripro'); ?>
							<a target="_blank" class="hover:underline" href="<?php echo _capalot('site_user_agreement_href', '#'); ?>"><?php _e('用户协议', 'ripro'); ?></a>、<a target="_blank" class="hover:underline" href="<?php echo _capalot('site_privacy_href', '#'); ?>"><?php _e('隐私政策', 'ripro'); ?></a>
						</small>
						<br>
						<?php printf('<small class="text-muted">©%s <a target="_blank" href="%s">%s</a> All rights reserved</small>', wp_date('Y', time()), esc_url(home_url()), get_bloginfo('name')); ?>
					</p>
				</form>
			</div>
		</div>
	</main>

	<?php if ($args['bg_type'] != 'img') : ?>

		<script src="<?php echo get_template_directory_uri() . '/assets/js/vantajs/three.min.js'; ?>" defer></script>
		<script src="<?php echo get_template_directory_uri() . '/assets/js/vantajs/vanta.' . $args['bg_type'] . '.min.js'; ?>" defer></script>
		<script>
			$(document).ready(function() {
				const v_config = <?php echo json_encode($vanta); ?>;
				console.log(v_config.config);
				VANTA[v_config.name](v_config.config)
			})
		</script>
	<?php endif; ?>

	<?php wp_footer(); ?>

</body>

</html>
