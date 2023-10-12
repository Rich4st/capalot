<!-- <?php
      $menu_class = 'mobile-menu block  lg:hidden';
      ?> -->

<div class="off-canvas fixed right-0 top-0 w-[16rem] bg-white h-full z-[999] p-4 hidden dark:bg-dark-card dark:text-gray-400 " id="navBg">
  <div class="canvas-close flex justify-end " id="closeNav"><i class="fas fa-times"></i></div>

  <div class="logo-wrapper flex items-center justify-center mt-2  ">
    <?php $logo_img = _capalot('site_logo', '');
    if (!empty($logo_img)) {
      echo '<a href="' . esc_url(home_url('/')) . '"><img class="logo regular h-16" src="' . esc_url($logo_img) . '" alt="' . esc_attr(get_bloginfo('name')) . '"></a>';
    } else {
      echo '<a class="logo text" href="' . esc_url(home_url('/')) . '">' . esc_html(get_bloginfo('name')) . '</a>';
    } ?>
  </div>


  <!-- <div class="<?php echo esc_attr($menu_class); ?>"> -->

  <nav class="sidebar-main-menu">
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
  <div class=" bg-white rounded overflow-hidden text-center text-[#595d69] dark:bg-dark-card mt-4 mb-4 ">
    <?php
    $uc_action = get_query_var('uc-page-action');
    $uc_menus = get_uc_menus();
    $menu_items = '<ul class="sidebar-menu-warp hidden">';
    foreach ($uc_menus as $key => $item) {
      $class = ($uc_action === $key) ? 'menu-item current-menu-item text-red-500 ' : 'menu-item';
      $menu_items .= sprintf(
        '<li class="%s p-2 my-2 dark:bg-dark "><a href="%s "><i class="%s me-1"></i>%s</a></li>',
        esc_attr($class),
        esc_url(get_uc_menu_link($key)),
        esc_attr($item['icon']),
        esc_html($item['title'])
      );
    }
    $menu_items .= '</ul>';
    echo $menu_items;
    ?>
  </div>
</div>
