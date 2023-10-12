<?php

if (empty($args)) {
  return;
}

if (!_capalot('remove_site_search', true) && !empty(trim($args['search_hot']))) {
  $search_hot_exp = explode(",", trim($args['search_hot']));
} else {
  $search_hot_exp = array();
}
$type = $args['bg_type'];
$vanta_uniqid = uniqid('vanta-bg-');
$base_config = [
  'el' => '#' . $vanta_uniqid,
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

switch ($type) {
  case 'img':
    $v_class = 'bg-type-' . $type . ' jarallax';
    $v_data = 'data-jarallax data-speed="0.2"';
    break;
  case 'video':
    $v_class = 'bg-type-' . $type . ' jarallax';
    $v_data = 'data-jarallax data-video-src="mp4:' . $args['bg_video'] . '"';
    break;
  default:
    $v_class = 'bg-type-' . $type;
    $vanta = $vanta_configs[$type];
    break;
}


// var_dump($args);
?>


<div id="<?php echo $vanta_uniqid; ?>" class="vanta-bg <?php echo $v_class; ?> relative  text-center h-96">

  <div class="search-wrap absolute w-full top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10 space-y-4">
    <h2 class="text-3xl font-semibold text-white"><?php echo $args['title']; ?></h2>
    <p class=" text-white"><?php echo $args['desc']; ?></p>



    <?php if (empty(_capalot('remove_site_search', false))) : ?>
    <div class=" max-w-[60rem] m-auto lg:px-0 px-4">
      <div class="search-form   bg-white border-2 rounded-md   dark:bg-dark-card dark:border-[#252529]">
        <div class=" h-full grid content-center">
          <div class=" px-2">
            <form method="get" action="<?php echo esc_url(home_url('/')); ?>">
              <div class=" flex flex-row   ">
                <?php if (_capalot('is_site_pro_search', true)) : ?>
                  <div class="search-select w-2/6 lg:w-1/6  self-center">
                    <div class=" flex justify-center ">
                      <?php
                      wp_dropdown_categories(array(
                        'hide_empty'       => false,
                        'show_option_none' => __('全站', 'ripro'),
                        'option_none_value' => '',
                        'order'          => 'DESC',
                        'orderby'          => _capalot('pro_search_select_order', 'id'),
                        'hierarchical'     => true,
                        'depth'     => intval(_capalot('pro_search_select_depth', 1)),
                        'id'     => 'cat-search-select',
                        'class'     => 'form-select dark:bg-dark-card dark:text-gray-400 outline-none w-full',
                      )); ?>
                    </div>
                  </div>
                <?php endif; ?>

                <div class="search-fields  w-4/6 lg:w-5/6">
                  <div class=" flex flex-row px-2">
                    <div class=" w-5/6"><input type="text" class=" text-lg py-2 px-2 w-full outline-none dark:bg-dark-card dark:text-gray-400" placeholder="<?php _e('输入关键词 回车...', 'ripro'); ?>" autocomplete="off" value="<?php echo esc_attr(get_search_query()) ?>" name="s" required="required"></div>
                    <div class=" w-1/6 self-center ">
                      <div class=" flex justify-end hover:opacity-70 dark:text-gray-400"><button title="点击搜索" type="submit"><i class="fas  fa-search "></i></button></div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($search_hot_exp)) : ?>
      <ul class="space-x-1 flex justify-center items-center text-sm text-gray-300">
        <li class="mr-1">搜索热词:</li>
        <?php foreach ($search_hot_exp as $exp) {
          if (!empty($exp)) {
            echo '<li class="hover:text-gray-100 hover:underline"><a href="' . esc_url(home_url('/')). '?s=' . $exp . '">' . $exp . '</a></li>';
          }
        } ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="media-container -z-10 h-full w-full">
    <?php if ($type === 'img') : ?>
      <img class="h-full w-full object-cover" src="<?php echo esc_url($args['bg_img']); ?>" alt="<?php echo esc_attr($args['title']); ?>">
    <?php elseif ($type === 'video') : ?>
      <video class="h-full w-full object-cover" src="<?php echo esc_url($args['bg_video']); ?>" autoplay muted loop></video>
    <?php endif; ?>
  </div>

</div>


<?php if (!in_array($type, ['img', 'video'])) : ?>
  <script src="<?php echo get_template_directory_uri() . '/assets/js/vantajs/three.min.js'; ?>" defer></script>
  <script src="<?php echo get_template_directory_uri() . '/assets/js/vantajs/vanta.' . $type . '.min.js'; ?>" defer></script>
  <script>
    $(document).ready(function() {
      const v_config = <?php echo json_encode($vanta); ?>;
      VANTA[v_config.name](v_config.config)
    })
  </script>
<?php endif; ?>
