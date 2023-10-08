<?php

$cat_id = get_queried_object_id();
$meta_bg = get_term_meta($cat_id, 'bg-image', true);
$bg_img = (!empty($meta_bg)) ? $meta_bg : capalot_get_thumbnail_url();

?>

<div class=" relative overflow-hidden">
    <div class=" absolute left-0 top-0 right-0 w-full h-full z-[-1] blur-lg" style="background-image: url(<?php echo esc_url($bg_img); ?>); background-position:50%;background-size:140%;"></div>
    <div class=" absolute w-full h-full bg-black/30 z-10"></div>
    <div class=" max-w-[80rem] m-auto lg:px-0 px-4 py-8 text-center relative z-50">
        <?php
        the_archive_title('<h1 class=" text-white text-xl font-bold">', '</h1>');
        the_archive_description('<div class=" text-white text-md mt-4">', '</div>');
        ?>
    </div>
</div>

