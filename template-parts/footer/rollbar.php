<?php
$site_footer_rollbar = _capalot('site_footer_rollbar', array());
if (!empty($site_footer_rollbar) && is_array($site_footer_rollbar)) : ?>
    <div class=" hidden lg:block">
        <div class="rollbar fixed right-8 bottom-20 z-50">
            <ul class="actions  bg-white shadow-2xl rounded-md border divide-y">
                <?php foreach ($site_footer_rollbar as $item) {
                    $target = (empty($item['is_blank'])) ? '' : '_blank';
                    printf('<li class=" hover:opacity-70 p-2 flex items-center justify-center text-center text-[12px] text-gray-600"><a class=" block max-w-[30px]" target="%s" href="%s" rel="nofollow noopener noreferrer"><i class="%s"></i><span class=" block ">%s</span></a></li>', $target, $item['href'], $item['icon'], $item['title']);
                } ?>
            </ul>
        </div>
    </div>
<?php endif; ?>