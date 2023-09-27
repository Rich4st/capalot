<?php
$site_footer_rollbar = _capalot('site_footer_rollbar', array());
if (!empty($site_footer_rollbar) && is_array($site_footer_rollbar)) : ?>
    <div class=" hidden lg:block">
        <div class="rollbar fixed right-8 bottom-28 z-[8650]">
            <ul class="actions  bg-white dark:bg-dark-card  dark:border-[#252529] shadow-lg rounded-md border border-gray-200   divide-y dark:divide-[#333]">
                <?php foreach ($site_footer_rollbar as $item) {
                    $target = (empty($item['is_blank'])) ? '' : '_blank';
                    printf('<li class=" hover:opacity-70 p-2 flex items-center justify-center text-center text-[12px] text-gray-500 dark:text-gray-400"><a class=" block max-w-[30px]" target="%s" href="%s" rel="nofollow noopener noreferrer"><i class="%s  text-lg"></i><span class=" block ">%s</span></a></li>', $target, $item['href'], $item['icon'], $item['title']);
                } ?>
            </ul>
        </div>
    </div>
<?php endif; ?>