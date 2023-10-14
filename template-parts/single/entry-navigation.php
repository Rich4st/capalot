<?php
if (empty(_capalot('is_single_bottom_navigation', true))) {
    return;
}
?>


<div class="py-3">
    <div class=" grid lg:grid-cols-2 gap-4">
        <?php if ($pre = get_previous_post()) : ?>
            <div class=" bg-white dark:bg-dark-card p-4 transition  hover:shadow-2xl hover:-translate-y-1 duration-300">
                <a class=" block" href="<?php echo get_the_permalink($pre->ID); ?>" title="<?php echo esc_attr(get_the_title($pre->ID)); ?>">
                    <div class=" grid grid-cols-5 gap-4 items-center">
                        <div class=""><i class="fas fa-arrow-left"></i></div>
                        <div class=" col-span-4">
                            <span class=" text-gray-400 dark:text-gray-400 text-sm block text-right"><?php echo esc_html('上一篇', 'ripro'); ?></span>
                            <div class=" flex justify-end"><div class=" text-base dark:text-gray-50 line-clamp-1"><?php echo get_the_title($pre); ?></div></div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>
        <?php if ($next = get_next_post()) : ?>
            <div class=" bg-white dark:bg-dark-card p-4 transition hover:shadow-2xl hover:-translate-y-1 duration-300">
                <a class=" block" href="<?php echo get_the_permalink($next->ID); ?>" title="<?php echo esc_attr(get_the_title($next->ID)); ?>">
                    <div class=" grid grid-cols-5 gap-4 items-center">
                        <div class=" col-span-4">
                            <span class=" text-gray-400 dark:text-gray-400 text-sm block text-left"><?php echo esc_html('下一篇', 'ripro'); ?></span>
                            <div class=" flex justify-start"><div class=" text-base line-clamp-1 dark:text-gray-50"><?php echo get_the_title($next); ?></div></div>
                        </div>
                        <div class=" text-right"><i class="fas fa-arrow-right"></i></div>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>