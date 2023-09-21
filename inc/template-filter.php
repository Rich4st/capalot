<?php

/**
 * 内页描述优化
 */
function capalot_archive_description($description) {
    if (is_search()) {
        global $wp_query;
        $search_num  = $wp_query->found_posts;
        $description = sprintf(__('搜索到 %1$s 个与 "%2$s" 相关的结果', 'ripro'), $search_num, get_search_query());
    }
    return $description;
}
add_filter('get_the_archive_description', 'capalot_archive_description');