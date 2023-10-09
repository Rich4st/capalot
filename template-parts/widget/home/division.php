<?php

if (empty($args)) {
  return;
}
$container = _capalot('site_container_width', '1400');
?>


<section class=" dark:bg-dark py-4">
  <div class="mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-4" style="max-width: <?php
                                                        if ($container === '') {
                                                            echo '1280';
                                                        } else {
                                                            echo $container;
                                                        }
                                                        ?>px;">

    <?php foreach ($args['div_data'] as $key => $item) : ?>

      <div class="flex dark:bg-dark-card bg-white flex-col text-center md:flex-row md:text-left items-center justify-center
      p-1 shadow-[0_3px_10px_rgb(0,0,0,0.2)] rounded-sm">

        <lord-icon src="<?php echo esc_attr($item['icon']); ?>" trigger="hover" colors="primary:#4be1ec,secondary:#cb5eee"
        style="width:75px;height:75px">
        </lord-icon>
        <!-- <div class="<?php echo esc_attr($args['icon_style']); ?>" style="background-color:<?php echo $item['color']; ?>">
          <i class="<?php echo esc_attr($item['icon']); ?>"></i>
        </div> -->
        <div>
          <h4 class="font-semibold dark:text-gray-50">
            <?php if (!empty($item['link'])) : ?>
              <a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a>
            <?php else : ?>
              <?php echo $item['title']; ?>
            <?php endif; ?>
          </h4>
          <p class="text-sm text-gray-700 font-medium dark:text-gray-400"><?php echo $item['desc']; ?></p>
        </div>

      </div>

    <?php endforeach; ?>

  </div>
</section>
