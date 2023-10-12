<style>
	.header-transparent .site-header:not(.navbar-sticky) {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		background: transparent;
		z-index: 99;
	}
</style>

<header class=" site-header dark:bg-dark dark:text-gray-400 w-full bg-white z-[99] ">

	<div class=" container lg:max-w-[80rem] m-auto lg:px-0 px-2  py-2">
		<div class=" navbar relative px-1 " id="hea_bg"> <!-- container-fluid px-lg-5 -->
			<div class="flex items-center ">
				<div class="mr-4  flex items-center">
					<?php
					$logo_img = _capalot('site_logo', '');
					$blog_name = get_bloginfo('name');
					$home_url = esc_url(home_url('/'));
					if (!empty($logo_img)) {
						$logo_html = sprintf('<img class="logo h-14 regular" src="%s" alt="%s">', esc_url($logo_img), esc_attr($blog_name));
					} else {
						$logo_html = sprintf('<span class="logo text">%s</span>', esc_html($blog_name));
					}
					printf('<a rel="nofollow noopener noreferrer" href="%s">%s</a>', esc_url(home_url('/')), $logo_html);
					?>
				</div>

				<div class="hidden md:block bg-[#ececec] dark:bg-black" style="margin:0 20px;width:1px; height:30px;"></div>

				<nav class="main-menu lg:block hidden">
					<?php
					// 定义缓存的ID和过期时间
					$cache_id = 'main-menu-cache';
					$cache_expiration = 5 * 24 * 3600; // 缓存一天
					// 尝试从缓存获取菜单
					$cached_menu = get_transient($cache_id);
					// 如果没有缓存，重新生成并缓存菜单
					if (false === $cached_menu) {

						$cached_menu = wp_nav_menu(array(
							'container' => true,
							'fallback_cb' => 'Capalot_Walker_Nav_Menu::fallback',
							'menu_id' => 'header-navbar',
							'menu_class' => 'nav-list',
							'theme_location' => 'main-menu',
							'walker' => new Capalot_Walker_Nav_Menu(true),
							'echo' => false, // 返回html内容
						));

						set_transient($cache_id, $cached_menu, $cache_expiration);
					}
					// 输出菜单
					echo $cached_menu;

					?>
				</nav>

				<?php if (empty(_capalot('remove_site_search', false))) : ?>
					<div class=" self-center ">
						<div class=" hidden " id="search_form"><?php get_search_form(); ?></div>
					</div>
				<?php endif; ?>

				<div class="flex items-center justify-center text-center space-x-4 ml-auto">
					<?php get_template_part('template-parts/header/action-hover'); ?>
					<div class="lg:hidden flex cursor-pointer" id="menuA"><i class="fas fa-bars"></i></div>
				</div>



			</div>
		</div>
	</div>

</header>

<div class="header-gap"></div>
