<?php
  $menu_class = 'mobile-menu d-block d-lg-none';
?>

<div class="off-canvas">
  <div class="canvas-close"><i class="fas fa-times"></i></div>
  
  <div class="logo-wrapper">
  	<?php $logo_img = _capalot('site_logo','');
    if ( ! empty( $logo_img ) ) {
    	echo '<a href="'.esc_url( home_url( '/' ) ).'"><img class="logo regular" src="'.esc_url( $logo_img ).'" alt="'.esc_attr( get_bloginfo( 'name' ) ).'"></a>';
    }else{
    	echo '<a class="logo text" href="'.esc_url( home_url( '/' ) ).'">'.esc_html( get_bloginfo( 'name' ) ).'</a>';
    }?>
  </div>

  
  <div class="<?php echo esc_attr( $menu_class ); ?>"></div>

</div>
