<header class=" navbar dark:bg-dark w-full bg-white opacity-1 z-[99] ease-in-out duration-800">

	<div class=" lg:max-w-[80rem] m-auto lg:px-0 px-2 py-2">
		<div class=" text-[#595d69] relative px-1 " id="hea_bg"> <!-- container-fluid px-lg-5 -->
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

				<!-- <div class="actions">
					<?php get_template_part('template-parts/header/action-hover'); ?>
					<div class="burger d-flex d-lg-none"><i class="fas fa-bars"></i></div>
				</div> -->



			</div>
		</div>
	</div>

</header>

<div class="header-gap"></div>
<script>
	var prevScrollpos = window.pageYOffset;


	window.addEventListener('scroll', function() {
		var currentScrollPos = window.pageYOffset;
		var navbar = document.querySelector('.navbar');
		var scrolled = window.scrollY;
		if (scrolled >= 80) {
			if (prevScrollpos > currentScrollPos) {
				navbar.style.opacity = '1';
				navbar.classList.add('fixed');
			} else {
				navbar.style.opacity = '0';
				navbar.classList.add('fixed');
			}
		}else{
			navbar.classList.remove('fixed');
		}

		prevScrollpos = currentScrollPos;
	});
</script>