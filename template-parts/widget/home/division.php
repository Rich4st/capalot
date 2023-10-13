<?php

if (empty($args)) {
  return;
}
$container = _capalot('site_container_width', '1400');
?>


<section class=" dark:bg-dark py-3">
  <div class="mx-auto  grid grid-cols-2 md:grid-cols-4 gap-4" style="max-width: <?php
                                                                                    if ($container === '') {
                                                                                      echo '1280';
                                                                                    } else {
                                                                                      echo $container;
                                                                                    }
                                                                                    ?>px;">

    <?php if ($args['div_data']) :
      foreach ($args['div_data'] as $key => $item) : ?>

        <div class="flex dark:bg-dark-card bg-white flex-col text-center md:flex-row md:text-left items-center justify-center
      p-1 shadow-[0_3px_10px_rgb(0,0,0,0.2)] rounded-sm">

          <!-- <lord-icon src="<?php echo esc_attr($item['icon']); ?>" trigger="hover" colors="primary:#4be1ec,secondary:#cb5eee" style="width:75px;height:75px">
        </lord-icon> -->
          <div class="<?php echo esc_attr($args['icon_style']); ?>" style="width:5rem;height:5rem;background-color:<?php echo $item['color']; ?>">
            <i class="<?php echo esc_attr($item['icon']); ?> w-full h-full"></i>
          </div>
          <div class="md:ml-2 ml-0 md:w-[70%] w-full">
            <h4 class="font-semibold dark:text-gray-50 truncate">
              <?php if (!empty($item['link'])) :
                if (strpos($item['link'], 'http') === false) :
                  $item['link'] = 'https://' . $item['link'];
                endif; ?>
                <a href="<?php echo $item['link']; ?>">
                  <?php echo $item['title']; ?></a>
              <?php else : ?>
                <?php echo $item['title']; ?>
              <?php endif; ?>
            </h4>
            <p class="text-sm text-gray-700 font-medium dark:text-gray-400 truncate"><?php echo $item['desc']; ?></p>
          </div>

        </div>

    <?php endforeach;
    endif; ?>

  </div>
</section>