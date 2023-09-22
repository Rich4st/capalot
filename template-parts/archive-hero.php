<?php

$cat_id = get_queried_object_id();
$meta_bg = get_term_meta($cat_id, 'bg-image', true);
$bg_img = (!empty($meta_bg)) ? $meta_bg : capalot_get_thumbnail_url();

?>

<div class="archive-hero text-center py-4 bg-gray-200 dark:bg-dark-card">
    <div class="archive-hero-bg lazy" bg="<?php echo esc_url($bg_img); ?>"></div>
    <div class=" py-2 py-md-4 lg:max-w-[80rem] m-auto ">
        <?php
        the_archive_title('<h1 class="archive-title mb-2 text-xl font-bold dark:text-gray-50">', '</h1>');

        the_archive_description('<p class="archive-desc mt-2 mb-0 dark:text-gray-400">', '</p>');
        ?>

    </div>
</div>