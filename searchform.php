<div class="search-form absolute inset-0 bg-white border-2 rounded-md  z-50 dark:bg-dark-card dark:border-[#252529]">
    <div class=" h-full grid content-center">
        <div class=" px-2">
            <form method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <div class=" flex flex-row   ">
                    <?php if (_capalot('is_site_pro_search', true)) : ?>
                        <div class="search-select w-2/6 lg:w-1/6 self-center">
                            <div class=" flex justify-center ">
                                <?php
                                wp_dropdown_categories(array(
                                    'hide_empty'       => false,
                                    'show_option_none' => __('全站', 'ripro'),
                                    'option_none_value' => '',
                                    'order'          => 'DESC',
                                    'orderby'          => _capalot('pro_search_select_order', 'id'),
                                    'hierarchical'     => true,
                                    'depth'     => intval(_capalot('pro_search_select_depth', 1)),
                                    'id'     => 'cat-search-select',
                                    'class'     => 'form-select dark:bg-dark-card dark:text-gray-400 outline-none w-full',
                                )); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="search-fields  lg:w-5/6 w-4/6">
                        <div class=" flex flex-row px-2">
                            <div class=" w-5/6"><input type="text" class=" text-lg py-2 px-2 w-full outline-none dark:bg-dark-card dark:text-gray-400" placeholder="<?php _e('输入关键词 回车...', 'ripro'); ?>" autocomplete="off" value="<?php echo esc_attr(get_search_query()) ?>" name="s" required="required"></div>
                            <div class=" w-1/6 self-center ">
                                <div class=" flex justify-end hover:opacity-70"><button title="点击搜索" type="submit"><i class="fas  fa-search "></i></button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>