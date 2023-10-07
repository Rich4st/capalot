<?php

$post_id = get_the_ID();
$bg_image = capalot_get_thumbnail_url($post_id);

?>
<div class="archive-hero post-hero text-center  overflow-hidden text-white">
    <div class="archive-hero-bg lazy overflow-hidden w-full h-full bg-no-repeat bg-cover " style="background-image:url(<?php echo $bg_image; ?>);" data-bg="<?php echo $bg_image; ?>">
        <div class="w-full  py-3 md:py-10 bg-black bg-opacity-50">
            <div class="article-header">
                <?php the_title('<h1 class="post-title mb-2 lg:mb-3 text-[1.5rem] font-semibold leading-[1.2] line-clamp-2">', '</h1>'); ?>
                <div class="article-meta text-[0.7rem] md:text-base">
                    <?php get_template_part('template-parts/single/meta'); ?>
                </div>
            </div>
        </div>
    </div>
</div>