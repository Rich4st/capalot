<?php
$menu_class = 'mobile-menu d-block d-lg-none';
?>

<div class="off-canvas fixed right-0 top-0 w-3/4 bg-white h-full z-[995580] p-4 hidden dark:bg-dark-card dark:text-gray-400" id="navBg">
  <div class="canvas-close flex justify-end " id="closeNav"><i class="fas fa-times"></i></div>

  <div class="logo-wrapper flex items-center justify-center mt-2">
    <?php $logo_img = _capalot('site_logo', '');
    if (!empty($logo_img)) {
      echo '<a href="' . esc_url(home_url('/')) . '"><img class="logo regular h-16" src="' . esc_url($logo_img) . '" alt="' . esc_attr(get_bloginfo('name')) . '"></a>';
    } else {
      echo '<a class="logo text" href="' . esc_url(home_url('/')) . '">' . esc_html(get_bloginfo('name')) . '</a>';
    } ?>
  </div>


  <div class="<?php echo esc_attr($menu_class); ?>"></div>


</div>