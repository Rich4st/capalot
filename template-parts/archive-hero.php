<?php

$cat_id = get_queried_object_id();
$meta_bg = get_term_meta($cat_id, 'bg-image', true);
$bg_img = (!empty($meta_bg)) ? $meta_bg : capalot_get_thumbnail_url();

?>

<div class=" relative overflow-hidden">
    <div class=" absolute left-0 top-0 right-0 w-full h-full z-[-1] lazy" data-bg="<?php echo esc_url($bg_img); ?>" style="background-position:50%;background-size:100%;"></div>
	<div class=" absolute  backdrop-blur-lg h-full w-full bg-black/30"></div>
    <div class=" max-w-[80rem] m-auto lg:px-0 px-4 lg:py-12 py-8 text-center relative z-50">
        <?php
        the_archive_title('<h1 class=" text-white text-xl font-bold">', '</h1>');
        the_archive_description('<div class=" text-white/80 text-sm mt-2">', '</div>');
        ?>
    </div>
</div>

