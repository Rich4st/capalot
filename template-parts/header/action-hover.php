<?php
global $current_user;
$uc_menus = get_uc_menus();
$site_color = get_site_default_color_style();

$lang_options = [
    'zh_CN' => '简体中文',
    'en_US' => 'English',
    'vt_VT' => 'Tiếng Việt',
    'th_TH' => 'ไทย',
    'pg_PG' => 'Português',
];

$current_lang = array_filter($lang_options, function ($key) {
    return $key === Capalot_Cookie::get('lang');
}, ARRAY_FILTER_USE_KEY);
$current_lang_key = array_key_first($current_lang);
$current_lang_value = array_shift($current_lang);

$lang_to_remove = Capalot_Cookie::get('lang') ?? 'zh_CN';
if (isset($lang_options[$lang_to_remove])) {
    unset($lang_options[$lang_to_remove]);
}

?>


<?php if (!empty(_capalot('is_site_dark_toggle', true))) : ?>
    <div class="h-full mr-4 bottom-auto relative mb-6">
        <?php if (_capalot('is_site_dark_toggle', true)) : ?>
            <span id="light-mode" class="absolute cursor-pointer
            <?php if (get_site_default_color_style() === 'dark') echo ' hidden'; ?>">
                <i class="fa-solid fa-sun"></i>
            </span>
            <span id="dark-mode" class="absolute cursor-pointer dark:text-gray-400
            <?php if (get_site_default_color_style() === 'light') echo ' hidden'; ?>">
                <i class="fa-solid fa-cloud-moon"></i>
            </span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (empty(_capalot('remove_site_search', false))) : ?>
    <span class="action-btn cursor-pointer toggle-search hover:opacity-70" id="search_on" rel="nofollow noopener noreferrer" title="<?php _e('站内搜索', 'ripro'); ?>"><i class="fas fa-search"></i></span>
<?php endif; ?>


<?php if (is_site_notify()) : ?>
    <span class="action-btn cursor-pointer toggle-notify" rel="nofollow noopener noreferrer" title='<?php _e('网站公告', 'ripro'); ?>'>
        <i class="fa-regular fa-bell"></i>
    </span>
<?php endif; ?>

<div class="group relative">
    <span class="cursor-pointer toggle-language " rel="nofollow noopener noreferrer" title='<?php _e('多语言', 'ripro'); ?>'>
        <i class="fa-solid fa-language text-xl"></i>
    </span>
    <div class="group-hover:block hidden absolute rounded-lg shadow-lg dark:bg-dark-card top-6 right-0 w-40 z-[9999]">
        <ul class="dark:bg-dark rounded-lg shadow-lg text-sm text-left bg-white px-3 space-y-2 py-2">
            <li data-lang="<?php echo $current_lang_key; ?> " class="font-bold rounded-md p-1 pl-2">
                <?php echo $current_lang_value; ?>
            </li>
            <?php foreach ($lang_options as $key => $lang) : ?>
                <li id="lang-option" data-lang="<?php echo $key; ?> " class="hover:text-indigo-400 cursor-pointer hover:bg-gray-100 rounded-md p-1 pl-2">
                    <?php echo $lang; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php if (is_user_logged_in()) : ?>

    <div class="group relative ">
        <a class="flex" href="<?php echo get_uc_menu_link(); ?>" rel="nofollow noopener noreferrer" id="adminT">
            <div class="flex">
                <img class="avatar-img rounded-full " src="<?php echo get_avatar_url($current_user->ID); ?>" width="30" alt="<?php _e('avatar','ripro');?>">
                <span class="ms-2 hidden md:block"><?php echo $current_user->display_name; ?></span>
            </div>
            <?php if (is_site_shop()) : ?>
                <?php echo capalot_get_user_badge($current_user->ID, 'span', 'md:flex items-center hidden rounded px-1  ml-2 dark:bg-dark'); ?>
            <?php endif; ?>
        </a>

        <?php if (is_site_shop()) : ?>
            <div class="text-[#595d69] md:group-hover:block hidden absolute right-0  top-7  w-[400px] z-[9999]" id="adminC">
                <div class="dark:bg-dark   mt-2 rounded-lg  shadow-lg overflow-hidden   text-sm  bg-white w-full ">
                    <div class="flex justify-between bg-[#dfeeff] dark:bg-dark-card">
                        <div class="hover-info p-2">
                            <div class="flex items-center ">
                                <div class="mr-2">
                                    <img class="rounded-full border m-4 h-10 w-10 inline-block border-white border-3 shadow" src="<?php echo get_avatar_url($current_user->ID); ?>" alt="<?php _e('user','ripro');?>">
                                </div>
                                <div class="mr-2 flex flex-col ">
                                    <div class="rounded px-1">
                                        <?php echo capalot_get_user_badge($current_user->ID, 'span', 'flex items-center rounded px-1 dark:bg-dark-card'); ?>
                                    </div>
                                    <b class=" mt-2"><?php echo $current_user->display_name; ?></b>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col m-4 space-y-2">
                            <?php
                            printf('<a href="%s"><i class="%s me-1"></i>%s</a>', get_uc_menu_link('logout'), $uc_menus['logout']['icon'], $uc_menus['logout']['title']);
                            if (in_array('administrator', $current_user->roles)) {
                                printf('<a target="_blank" href="%s"><i class="fab fa-wordpress me-1 fa-lg"></i>%s</a>', esc_url(home_url('/wp-admin/')), __('后台管理', 'ripro'));
                            } else {
                                printf('<a href="%s"><i class="%s me-1"></i>%s</a>', get_uc_menu_link('aff'), $uc_menus['aff']['icon'], $uc_menus['aff']['title']);
                            }

                            ?>
                        </div>
                    </div>

                    <div class=" p-3 pb-1 ">

                        <div class="grid grid-cols-2 gap-2">

                            <div class="text-center bg-info  text-white bg-opacity-75 rounded-lg p-3 ">
                                <div class="mb-2"><?php printf('%s%s', get_site_coin_name(), __('余额', 'ripro')); ?></div>
                                <?php printf('<div class="mb-2"><i class="%s me-1"></i>%s</div>', get_site_coin_icon(), get_user_coin_balance($current_user->ID)); ?>
                                <button class="text-[#7bb6fa] bg-white w-20 h-6 rounded-xl">
                                    <a class="w-full h-full" href="<?php echo get_uc_menu_link('coin'); ?>" rel="nofollow noopener noreferrer"><?php _e('充值', 'ripro'); ?></a>
                                </button>
                            </div>

                            <div class="grid grid-row-3 ">
                                <?php
                                $vip_options = get_site_vip_options();
                                $colors = [
                                    'no'        => 'secondary',
                                    'vip'     => 'success',
                                    'boosvip' => 'accent',
                                ];
                                foreach ($vip_options as $key => $item) {
                                    if ($item['key'] != 'no') {
                                        $color = $colors[$item['key']];
                                        $link  = get_uc_menu_link('vip');
                                        echo '<a class="btn btn-sm md:block bg-' . $color . ' text-white bg-opacity-75 rounded-lg p-2 py-3 mb-2" href="' . $link . '"><i class="far fa-gem me-1 fa-lg"></i>' . __('本站', 'ripro') . $item['name'] . '</a>';
                                    }
                                }
                                ?>
                            </div>

                        </div>

                    </div>

                    <div class="mt-2 p-3 pt-0">
                        <div class="space-x-4 grid grid-cols-5">
                            <?php
                            $menus_item1 = ['profile', 'coin', 'vip', 'fav', 'order'];
                            foreach ($menus_item1 as $key) {
                                printf(
                                    '<a href="%s"><i class="%s bg-[#eeeeee] p-4 rounded-full fa-lg"></i><div>%s</div></a>',
                                    get_uc_menu_link($key),
                                    $uc_menus[$key]['icon'],
                                    $uc_menus[$key]['title']
                                );
                            }
                            ?>
                        </div>
                    </div>

                </div>
            </div>
        <?php endif; ?>
    </div>
<?php else : ?>

    <?php if (is_site_user_login()) : ?>
        <a class="action-btn login-btn md:text-base text-sm btn" rel="nofollow noopener noreferrer" href="<?php echo esc_url(wp_login_url(get_current_url())); ?>"><i class="far fa-user me-1"></i>
            <?php echo __('登录', 'ripro'); ?>
        </a>
    <?php endif; ?>

<?php endif; ?>



<script>
    // 显示/隐藏搜索框
    let search_on = document.getElementById('search_on');
    let search_form = document.getElementById('search_form');
    let hea_bg = document.getElementById('hea_bg');
    let adminT = document.getElementById('adminT');
    let adminC = document.getElementById('adminC');
    search_on.addEventListener('click', function() {
        if (search_form.style.display == 'none' || !search_form.style.display) {
            search_form.style.display = 'block';
        } else {
            search_form.style.display = 'none';
        }
    });
    document.addEventListener('click', function() {
        search_form.style.display = 'none';
    });
    hea_bg.addEventListener('click', function() {
        var e = event || window.event;
        e.stopPropagation();
    });

    adminT.addEventListener('click', function() {
        adminC.style.display = 'none';
    });
</script>