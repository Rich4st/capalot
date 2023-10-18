<?php

/**
 * 翻页导航
 */
function Capalot_Pagination($args = array())
{

  $default = array(
    'range' => 4,
    'custom_query' => false,
    'previous_string' => '«',
    'next_string' => '»',
    'nav_type' => _capalot('site_page_nav_type', 'click'),
    'nav_class' => 'pagination justify-content-center',
  );

  $args = wp_parse_args($args, $default);

  $args['range'] = (int) $args['range'] - 1;

  if (!$args['custom_query'])
    $args['custom_query'] = @$GLOBALS['wp_query'];

  $count = (int) $args['custom_query']->max_num_pages;
  $page = intval(get_query_var('paged'));
  $ceil = ceil($args['range'] / 2);

  if ($count <= 1)
    return false;

  if (!$page)
    $page = 1;

  if ($count > $args['range']) {
    if ($page <= $args['range']) {
      $min = 1;
      $max = $args['range'] + 1;
    } elseif ($page >= ($count - $ceil)) {
      $min = $count - $args['range'];
      $max = $count;
    } elseif ($page >= $args['range'] && $page < ($count - $ceil)) {
      $min = $page - $ceil;
      $max = $page + $ceil;
    }
  } else {
    $min = 1;
    $max = $count;
  }

  $echo = '<ul class="pagination flex items-center gap-4">';

  //页码
  $echo .= '<li class="ml-4"><span class="page-link">' . $page . '/' . $count . '</span></li>';

  //最前一页
  $firstpage = esc_attr(get_pagenum_link(1));
  if ($firstpage && (1 != $page)) {
    $echo .= '<li class="page-item page-first"><a class="page-link" href="' . $firstpage . '"><span title="' . __('最新一页', 'ripro') . '" aria-hidden="true">&laquo;</span></a></li>';
  }

  //上一页
  $previous = intval($page) - 1;
  $previous = esc_attr(get_pagenum_link($previous));
  if ($previous && (1 != $page)) {
    $echo .= '<li class="page-item"><a class="page-link page-previous" href="' . $previous . '">' . $args['previous_string'] . '</a></li>';
  }


  //数字页
  if (!empty($min) && !empty($max)) {
    for ($i = $min; $i <= $max; $i++) {
      if ($page == $i) {
        $echo .= '<li class="page-item active"><span class="page-link">' . (int) $i . '</span></li>';
      } else {
        $echo .= sprintf('<li class="page-item"><a class="page-link" href="%s">%d</a></li>', esc_attr(get_pagenum_link($i)), $i);
      }
    }
  }

  //下一页
  $next = intval($page) + 1;
  $next = esc_attr(get_pagenum_link($next));
  if ($next && ($count != $page)) {
    $echo .= '<li class="page-item"><a class="page-link page-next" href="' . $next . '">' . $args['next_string'] . '</a></li>';
  }

  //最后一页
  $lastpage = esc_attr(get_pagenum_link($count));
  if ($lastpage) {
    $echo .= '<li class="page-item page-last"><a class="page-link" href="' . $lastpage . '"><span title="' . __('最后一页', 'ripro') . '" aria-hidden="true">&raquo;</span></a></li>';
  }

  $echo .= '</ul>';

  if ($args['nav_type'] == 'click' || $args['nav_type'] == 'pull') {
    $echo = '';
    $args['nav_class'] = $args['nav_class'] . ' infinite-scroll';
    $echo .= infinite_scroll_button($args['nav_type']);
  }

  if (isset($echo)) {
    echo '<nav class="' . $args['nav_class'] . '">' . $echo . '</nav>';
  }
}

// 无限滚动加载按钮
function infinite_scroll_button($type = 'click')
{
  return '<div class="btn__wrapper text-center py-6">
  <button id="load-more" type="button" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-full text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"><i class="fa-solid fa-spinner fa-spin hidden more_icon"></i>' . __('加载更多', 'ripro') . '</button>
  <p id="no-more-button" style="display: none;" class="text-[#b9b2b2] text-[0.9rem]">' . __('已全部加载完毕', 'ripro') . '</p>
</div>';
}

/**
 * 链接是否新窗口打开
 */
function get_target_blank()
{
  return empty(_capalot('site_link_blank')) ? '_self' : '_blank';
}




/**
 * 默认缩略图
 * @Author Dadong2g
 * @date   2022-01-22
 * @return [type]
 */
function get_default_thumbnail_src()
{
  return _capalot('default_thumb') ? _capalot('default_thumb') : get_template_directory_uri() . '/assets/img/thumb.jpg';
}


function _capalot_get_default_lazy_img_src()
{
  return _capalot('default_lazy_thumb') ? _capalot('default_lazy_thumb') : 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
}



function _capalot_get_thumbnail_size_type()
{
  $options = array(
    'bg-cover',
    'bg-auto',
    'bg-contain',
  );
  $opt = _capalot('site_thumb_size_type', 'bg-cover');

  if (!in_array($opt, $options)) {
    $opt = $options[0];
  }
  return $opt;
}

function _capalot_get_thumbnail_fit_type()
{
  $options = array(
    'bg-left-top',
    'bg-right-top',
    'bg-center-top',
    'bg-center',
    'bg-center-bottom',
    'bg-left-bottom',
    'bg-right-bottom',
  );
  $opt = _capalot('site_thumb_fit_type', 'bg-center');

  if (!in_array($opt, $options)) {
    $opt = $options[0];
  }
  return $opt;
}

/**
 * 获取缩略图地址
 * @Author Dadong2g
 * @date   2023-04-19
 * @param  [type]     $post [description]
 * @param  string     $size [description]
 * @return [type]
 */
function capalot_get_thumbnail_url($post = null, $size = 'thumbnail')
{

  if (empty($post)) {
    global $post;
  } else {
    $post = get_post($post);
  }

  if (!$post instanceof WP_Post) {
    return get_default_thumbnail_src();
  }

  if (has_post_thumbnail($post)) {
    return get_the_post_thumbnail_url($post, $size);
  } elseif (_capalot('is_post_one_thumbnail', true) && !empty($post->post_content)) {
    // Automatically get the first image in the post content
    ob_start();
    ob_end_clean();
    preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    if (!empty($matches[1][0])) {
      return $matches[1][0];
    }
  }

  return get_default_thumbnail_src();
}

/**
 * 获取缩略图
 * @Author Dadong2g
 * @date   2022-01-06
 * @param  string     $class [description]
 * @return [type]
 */
function capalot_the_thumbnail($post = null, $class = 'thumb lazy', $size = 'thumbnail')
{
  if (empty($post)) {
    global $post;
  }

  if (is_numeric($post)) {
    $post = get_post($post);
  }

  echo get_the_post_thumbnail($post->ID, $size, array('class' => $class, 'alt' => the_title_attribute(array('echo' => false))));
}


/**
 * 获取面包屑导航
 */
function capalot_get_breadcrumb($class = 'breadcrumb')
{
  global $post, $wp_query;
  echo '<ol class="' . $class . '"><li class=""><a href="' . home_url() . '">' . __('首页', 'ripro') . '</a></li>';

  if (is_category()) {
    $cat_obj   = $wp_query->get_queried_object();
    $thisCat   = $cat_obj->term_id;
    $thisCat   = get_category($thisCat);
    $parentCat = get_category($thisCat->parent);

    if ($thisCat->parent != 0) {
      // echo capalot_get_category_parents_link($parentCat, 'category');
      echo $parentCat . '111';
    }

    echo '<li class="active">';
    single_cat_title();
    echo '</li>';
  } elseif (is_day()) {
    echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> </li>';
    echo '<li><a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> </li>';
    echo '<li class="active">' . get_the_time('d') . '</li>';
  } elseif (is_month()) {
    echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> </li>';
    echo '<li class="active">' . get_the_time('F') . '</li>';
  } elseif (is_year()) {
    echo '<li class="active">' . get_the_time('Y') . '</li>';
  } elseif (is_attachment()) {
    echo '<li class="active">' . get_the_title() . '</li>';
  } elseif (is_single()) {
    $post_type = get_post_type();
    if ($post_type == 'post') {
      $cat = get_the_category();
      $cat = isset($cat[0]) ? $cat[0] : 0;
      echo capalot_get_term_parents_link($cat, 'category');
      echo '<li class="active">' . __('正文', 'ripro') . '</li>';
    } else {
      $obj = get_post_type_object($post_type);
      echo '<li class="active">';
      echo $obj->labels->singular_name;
      echo '</li>';
    }
  } elseif (is_page() && !$post->post_parent) {
    echo '<li class="active">' . get_the_title() . '</li>';
  } elseif (is_page() && $post->post_parent) {
    $parent_id   = $post->post_parent;
    $breadcrumbs = array();
    while ($parent_id) {
      $page          = get_post($parent_id);
      $breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
      $parent_id     = $page->post_parent;
    }
    $breadcrumbs = array_reverse($breadcrumbs);
    foreach ($breadcrumbs as $crumb) {
      echo $crumb;
    }

    echo '<li class="active">' . get_the_title() . '</li>';
  } elseif (is_search()) {
    $kw = get_search_query();
    $kw = !empty($kw) ? $kw : '无';
    echo '<li class="active">' . sprintf(__('搜索: %s', 'ripro'), $kw) . '</li>';
  } elseif (is_tag()) {
    echo '<li class="active">';
    single_tag_title();
    echo '</li>';
  } elseif (is_author()) {
    global $author;
    $userdata = get_userdata($author);
    echo '<li class="active">' . $userdata->display_name . '</li>';
  } elseif (is_404()) {
    echo '<li class="active">404</li>';
  }

  if (get_query_var('paged')) {
    echo '<li class="active">' . sprintf(__('第%s页', 'ripro'), get_query_var('paged')) . '</li>';
  }

  echo '</ol>';
}

/**
 * 根据分类id获取分类父集返回链接
 * @param  [type]     $id       [description]
 * @param  [type]     $taxonomy [description]
 * @param  array      $visited  [description]
 * @return [type]
 */
function capalot_get_term_parents_link($id, $taxonomy, $visited = array())
{
  if (!$id) {
    return '';
  }
  $chain  = '';
  $parent = get_term($id, $taxonomy);
  if (is_wp_error($parent)) {
    return '';
  }
  $name = $parent->name;
  if ($parent->parent && ($parent->parent != $parent->term_id) && !in_array($parent->parent, $visited)) {
    $visited[] = $parent->parent;
    $chain .= capalot_get_term_parents_link($parent->parent, $taxonomy, $visited);
  }
  $chain .= '<li><a href="' . esc_url(get_category_link($parent->term_id)) . '">' . $name . '</a></li>';
  return $chain;
}

//  获取分类ID的顶级分类id

function capalot_get_term_top_id($term_id, $taxonomy = 'category')
{
  $ancestors = get_ancestors($term_id, $taxonomy);
  if ($ancestors) {
    $top_level_id = end($ancestors);
  } else {
    $top_level_id = $term_id;
  }
  return $top_level_id;
}



/**
 * 分页
 * @param  integer    $pagenum       [当前页面]
 * @param  integer    $max_num_pages [MAX数量]
 * @return [type]
 */
function capalot_custom_pagination($pagenum = 0, $max_num_pages = 0)
{

  $page_links = paginate_links(array(
    'base'      => add_query_arg('page', '%#%'),
    'format'    => '?page=%#%',
    'total'     => intval($max_num_pages),
    'current'   => intval($pagenum),
    'show_all'   => false,
  ));

  if ($page_links) {
    echo '<nav class="fav-pagination mt-3 md:mt-4 text-center">
      ' . $page_links . '
      </nav>';
  }
}

function is_weixin_visit()
{
  if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
    return true;
  } else {
    return false;
  }
}

// 获取分类前缀
function capalot_get_category_base()
{
  if (_capalot('site_no_category', false)) {
    $category_base = '';
  } else {
    // 获取用户在固定链接中设置的分类前缀
    $category_base = get_option('category_base');

    if (empty($category_base)) {
      $category_base = 'category';
    }
  }

  return $category_base;
}

/**
 * 获取分类信息
 */
function capalot_meta_category($num = 2)
{
  $categories = get_the_category();
  $separator = ' ';
  $output = '';
  if ($categories) {
    foreach ($categories as $key => $category) {
      if ($key == $num) {
        break;
      }
      $output .= '<a class="hover:text-red-500"' . 'title="' . $category->name . '"' . 'href="' . esc_url(get_category_link($category->term_id)) . '">
          <i class="fa-solid fa-tag pr-2"></i>' . esc_html($category->name) .
        '</a>' . $separator;
    }
    echo trim($output, $separator);
  }
}

/**
 * 获取文章描述
 */
function capalot_get_post_excerpt($limit = '48')
{
  $excerpt = get_the_excerpt();

  if (empty($excerpt)) {
    $excerpt = get_the_content();
  }

  echo wp_trim_words(strip_shortcodes($excerpt), $limit, '...');
}

/**
 * 获取文章点赞数
 */
function capalot_get_post_likes($post_id = null)
{
  if (empty($post_id)) {
    global $post;
    $post_id = $post->ID;
  }
  $meta_key = 'likes';
  $num      = absint(get_post_meta($post_id, $meta_key, true));
  if (1000 <= $num) {
    $num = sprintf('%0.1f', $num / 1000) . 'K';
  }
  return $num;
}

/**
 * 获取文章收藏数
 */
function capalot_get_post_favorites($post_id = null)
{
  if (empty($post_id)) {
    global $post;
    $post_id = $post->ID;
  }
  $meta_key = 'follow_num';
  $num      = absint(get_post_meta($post_id, $meta_key, true));
  if (1000 <= $num) {
    $num = sprintf('%0.1f', $num / 1000) . 'K';
  }
  return $num;
}

/**
 * 是否已经收藏该文章
 */
function capalot_is_post_fav($user_id = null, $post_id = null)
{
  if (empty($post_id)) {
    global $post;
    $post_id = $post->ID;
  }
  if (empty($user_id)) {
    $user_id = get_current_user_id();
  }
  $post_id = absint($post_id);

  if (get_post_status($post_id) === false) {
    return false;
  }

  $meta_key = 'follow_post';

  $data = get_user_meta($user_id, $meta_key, true); # 获取...

  if (empty($data) || !is_array($data)) {
    return false;
  }
  return in_array($post_id, $data);
}

/**
 * 是否已点赞该文章
 */
function capalot_is_post_like($user_id = null, $post_id = null)
{
  if (empty($post_id)) {
    global $post;
    $post_id = $post->ID;
  }
  if (empty($user_id)) {
    $user_id = get_current_user_id();
  }
  $post_id = absint($post_id);

  if (get_post_status($post_id) === false) {
    return false;
  }

  $meta_key = 'like_post';

  $data = get_user_meta($user_id, $meta_key, true); # 获取点赞文章

  if (empty($data) || !is_array($data)) {
    return false;
  }
  return in_array($post_id, $data);
}

/**
 * 收藏文章
 */
function capalot_add_post_fav($user_id = null, $post_id = 0)
{

  $post_id = absint($post_id);
  if (get_post_status($post_id) === false) {
    return false;
  }
  if (empty($user_id)) {
    $user_id = get_current_user_id();
  }

  $meta_key = 'follow_post';
  $post_key = 'follow_num';

  $old_data = get_user_meta($user_id, $meta_key, true); # 获取...

  if (empty($old_data) || !is_array($old_data)) {
    $new_data = [];
  } else {
    $new_data = $old_data;
  }

  if (!in_array($post_id, $new_data)) {
    // 新数据 开始处理
    array_push($new_data, $post_id);
  }
  if (true) {
    $favnum  = absint(get_post_meta($post_id, $post_key, true));
    $new_num = $favnum + 1;
    update_post_meta($post_id, $post_key, $new_num);
  }

  return update_user_meta($user_id, $meta_key, $new_data);
}

/**
 * 取消收藏文章
 */
function capalot_delete_post_fav($user_id = null, $post_id = 0)
{

  $post_id = absint($post_id);
  if (get_post_status($post_id) === false) {
    return false;
  }
  if (empty($user_id)) {
    $user_id = get_current_user_id();
  }

  $meta_key = 'follow_post';
  $post_key = 'follow_num';

  $old_data = get_user_meta($user_id, $meta_key, true); # 获取...

  if (empty($old_data) || !is_array($old_data)) {
    $new_data = [];
  } else {
    $new_data = $old_data;
  }

  if (in_array($post_id, $new_data)) {
    $new_data = array_values(array_diff($new_data, [$post_id]));
  }

  if (true) {
    $favnum  = absint(get_post_meta($post_id, $post_key, true));
    $new_num = $favnum - 1;
    if ($new_num < 0) {
      $new_num = 0;
    }

    update_post_meta($post_id, $post_key, $new_num);
  }

  return update_user_meta($user_id, $meta_key, $new_data);
}

/**
 * 取消点赞文章
 */
function capalot_delete_post_like($user_id = null, $post_id = null)
{
  $post_id = absint($post_id);
  if (get_post_status($post_id) === false) {
    return false;
  }

  if (empty($user_id)) {
    $user_id = get_current_user_id();
  }

  $meta_key = 'like_post';
  $post_key = 'likes';

  $old_data = get_user_meta($user_id, $meta_key, true); # 获取...

  if (empty($old_data) || !is_array($old_data)) {
    $new_data = [];
  } else {
    $new_data = $old_data;
  }

  if (in_array($post_id, $new_data)) {
    $new_data = array_values(array_diff($new_data, [$post_id]));
  }

  if (true) {
    $favnum  = absint(get_post_meta($post_id, $post_key, true));
    $new_num = $favnum - 1;
    if ($new_num < 0) {
      $new_num = 0;
    }

    update_post_meta($post_id, $post_key, $new_num);
  }

  return update_user_meta($user_id, $meta_key, $new_data);
}

/**
 * 获取列表显示风格配置
 * @Author Dadong2g
 * @date   2022-12-08
 * @return [type]
 */
function capalot_get_archive_item_config($cat_id = 0)
{

  $item_col   = _capalot('archive_item_col', '4');
  $item_style = _capalot('archive_item_style', 'grid');
  $media_size = _capalot('post_thumbnail_size', 'radio-3x2');

  $media_size_type = capalot_get_thumbnail_size_type();
  $media_fit_type = capalot_get_thumbnail_fit_type();

  $item_entry = _capalot('archive_item_entry', array(
    'category_dot',
    'entry_desc',
    'entry_footer',
    'vip_icon',
  ));

  $term_item_style = _capalot('site_term_item_style', array());



  if (!empty($cat_id) && !empty($term_item_style)) {
    foreach ($term_item_style as $key => $item) {
      if ($cat_id == $item['cat_id']) {
        $item_col   = $item['archive_item_col'];
        $item_style = $item['archive_item_style'];
        $media_size = $item['post_thumbnail_size'];
        $item_entry = $item['archive_item_entry'];
        continue;
      }
    }
  }


  $row_cols = [
    '1' => 'row-cols-1 g-2 g-md-3 g-lg-4',
    '2' => 'row-cols-2 g-2 g-md-3 g-lg-4',
    '3' => 'row-cols-2 row-cols-md-3 g-2 g-md-3 g-lg-4',
    '4' => 'row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 g-md-3 g-lg-4',
    '5' => 'row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-lg-3',
    '6' => 'row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 row-cols-xxl-6 g-2 g-lg-3',
  ];

  if ($item_style == 'list' && $item_col >= 2) {
    // 列表模式自适应...
    $row_cols_class = 'row-cols-1 row-cols-md-2 g-2 g-md-3 g-lg-4';
  } else {
    $row_cols_class = $row_cols[$item_col];
  }

  $config = array(
    'type'            => $item_style, //grid grid-overlay list list-icon
    'row_cols_class'  => $row_cols_class,
    'media_size_type' => $media_size_type,
    'media_fit_type'  => $media_fit_type,
    'media_class'     => $media_size, // media-3x2 media-3x3 media-2x3
    'is_vip_icon'     => @in_array('vip_icon', $item_entry),
    'is_entry_desc'   => @in_array('entry_desc', $item_entry),
    'is_entry_meta' => @in_array('entry_footer', $item_entry),
    'is_entry_cat' => @in_array('category_dot', $item_entry),
  );
  return $config;
}



function capalot_get_thumbnail_size_type()
{
  $options = array(
    'bg-cover',
    'bg-auto',
    'bg-contain',
  );
  $opt = _capalot('site_thumb_size_type', 'bg-cover');

  if (!in_array($opt, $options)) {
    $opt = $options[0];
  }
  return $opt;
}

function capalot_get_thumbnail_fit_type()
{
  $options = array(
    'bg-left-top',
    'bg-right-top',
    'bg-center-top',
    'bg-center',
    'bg-center-bottom',
    'bg-left-bottom',
    'bg-right-bottom',
  );
  $opt = _capalot('site_thumb_fit_type', 'bg-center');

  if (!in_array($opt, $options)) {
    $opt = $options[0];
  }
  return $opt;
}

/**
 * 添加文章阅读数量
 */
function capalot_add_post_views($post_id = null)
{
  if (empty($post_id)) {
    global $post;
    $post_id = $post->ID;
  }
  $meta_key = 'views';
  $this_num = intval(get_post_meta($post_id, $meta_key, true));
  $new_num  = $this_num + 1;
  if ($new_num < 0) {
    $new_num = 1;
  }

  return update_post_meta($post_id, $meta_key, $new_num);
}

/**
 * 获取文章浏览数量
 */
function capalot_get_post_views($post_id = null)
{
  if (empty($post_id)) {
    global $post;
    $post_id = $post->ID;
  }
  $meta_key = 'views';
  $this_num = absint(get_post_meta($post_id, $meta_key, true));
  if (1000 <= $this_num) {
    $this_num = sprintf('%0.1f', $this_num / 1000) . 'K';
  }
  return $this_num;
}

/**
 * 添加文章点赞数
 */
function capalot_add_post_likes($post_id = null, $num = 1)
{
  if (empty($post_id)) {
    global $post;
    $post_id = $post->ID;
  }
  $user_id = get_current_user_id();

  $meta_key = 'likes';
  $user_meta_key = 'like_post';

  $old_data = get_user_meta($user_id, $user_meta_key, true); # 获取...

  if (empty($old_data) || !is_array($old_data)) {
    $new_data = [];
  } else {
    $new_data = $old_data;
  }

  if (!in_array($post_id, $new_data)) {
    // 新数据 开始处理
    array_push($new_data, $post_id);
  }

  $this_num = intval(get_post_meta($post_id, $meta_key, true));
  $new_num  = $this_num + $num;
  if ($new_num < 0) {
    $new_num = 1;
  }

  update_user_meta($user_id, $user_meta_key, $new_data);

  return update_post_meta($post_id, $meta_key, $new_num);
}

/**
 * 获取文章购买量
 */
function capalot_get_post_paynum($post_id = null)
{
  if (empty($post_id)) {
    global $post;
    $post_id = $post->ID;
  }

  $meta_key = 'capalot_paynum';

  $this_num = absint(get_post_meta($post_id, $meta_key, true));
  if (1000 <= $this_num) {
    $this_num = sprintf('%0.1f', $this_num / 1000) . 'K';
  }
  return $this_num;
}

/**
 * 获取时间戳
 */
function capalot_meta_datetime()
{
  $time = get_the_time('U');

  $time_string = sprintf(
    '<time class="pub-date" datetime="%1$s">%2$s</time>',
    esc_attr(get_the_date(DATE_W3C)),
    esc_html(human_time_diff($time, current_time('timestamp')) . __('前', 'ripro'))
  );

  if (false) {
    // 显示最近修改时间
    $modified_time = get_the_modified_time('U');
    if ($time != $modified_time) {
      $time_string .= sprintf(
        '<time class="mod-date" datetime="%1$s">%2$s</time>',
        esc_attr(get_the_modified_date(DATE_W3C)),
        esc_html(human_time_diff($modified_time, current_time('timestamp')) . __('前', 'ripro'))
      );
    }
  }

  echo $time_string;
}

/**
 * 获取响应参数
 */
function get_response_param($key, $default = '', $method = 'post')
{
  switch ($method) {
    case 'post':
      return (isset($_POST[$key])) ? $_POST[$key] : $default;
      break;
    case 'get':
      return (isset($_GET[$key])) ? $_GET[$key] : $default;
      break;
    case 'request':
      return (isset($_REQUEST[$key])) ? $_REQUEST[$key] : $default;
      break;
    default:
      return null;
      break;
  }
}

/**
 * 获取当前页面URL
 */
if (!function_exists('get_current_url')) {
  function get_current_url()
  {
    $current_url = home_url(add_query_arg(array()));
    return esc_url($current_url);
  }
}

/**
 * 获取默认头像
 */
function get_default_avatar_src()
{
  return get_template_directory_uri() . '/assets/img/avatar.png';
}

//是否开启图片验证码功能
function is_site_img_captcha()
{
  return !empty(_capalot('is_site_img_captcha', 1));
}

//获取用户客户端IP get_ip_address()
function get_ip_address($ignore_private_and_reserved = false)
{
  $flags = $ignore_private_and_reserved ? (FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) : 0;
  foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
    if (array_key_exists($key, $_SERVER) === true) {
      foreach (explode(',', $_SERVER[$key]) as $ip) {
        $ip = trim($ip); // just to be safe

        if (filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false) {
          return $ip;
        }
      }
    }
  }
  return 'unknown';
}

function get_default_lazy_img_src()
{
  return _capalot('default_lazy_thumb') ? _capalot('default_lazy_thumb') : 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
}

/**
 * 判断邮件服务是否开启
 *
 * @param string $role admin | user
 * @param string $key 邮件服务key
 */
function site_push_server($role = 'admin', $key)
{
  switch ($role) {
    case 'admin':
      $opt_key = 'site_admin_push_server';
      break;
    case 'user':
      $opt_key = 'site_user_push_server';
      break;
    default:
      $opt_key = 'null';
      break;
  }
  $data = _capalot($opt_key);
  if (empty($data) || !is_array($data)) {
    return false;
  }
  if (!in_array($key, $data)) {
    return false;
  }
  return true;
}

//全站弹窗报错
function capalot_wp_die($title = '', $msg = '', $back_link = '')
{
  ob_start(); ?>
  <!doctype html>
  <html <?php language_attributes(); ?>>

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <title><?php echo get_bloginfo('name') . ' - ' . $title; ?></title>
    <?php wp_head(); ?>
  </head>

  <body class="wpdie">
    <script type="text/javascript">
      window.onload = function() {
        var html = '<div class="text-center"><h4 class="text-danger"><i class="fas fa-info-circle"></i> <?php echo $title; ?></h4><hr><div class="text-muted py-3"><?php echo $msg; ?></div></div>';
        var back_url = '<?php echo $back_link; ?>';
        ri.popup(html, 400, function() {
          if (back_url === 'close') {
            window.close();
          } else if (back_url !== '') {
            location.href = back_url;
          } else {
            location.href = document.referrer;
          }
        })
      };
    </script>
    <?php wp_footer(); ?>
    <div class="dimmer"></div>
  </body>

  </html>
<?php echo ob_get_clean();
  exit;
}

// 根据字符串搜索用户id 用于搜索
function get_user_id_from_str($string)
{
  $string = trim($string);

  if (is_email($string) && $user = get_user_by('email', $string)) {
    return $user->ID;
  }
  if (is_numeric($string) && $user = get_user_by('id', absint($string))) {
    return $user->ID;
  }
  if (is_string($string) && $user = get_user_by('login', $string)) {
    return $user->ID;
  }
  return 0;
}

/**
 * 获取网站默认颜色风格
 */
function get_site_default_color_style()
{

  $style = _capalot('site_default_color_mode', 'light');
  //读取用户浏览器缓存模式
  $cookie_style = Capalot_Cookie::get('theme');

  if (!empty($cookie_style)) {
    $style = $cookie_style;
  }

  if ($style == 'auto') {
    $current_hour = wp_date('H'); // 获取当前小时
    // 定义白天和黑夜的起止时间（您可以根据需要进行调整）
    $day_start = 6;   // 白天开始时间
    $night_start = 18;   // 黑夜开始时间
    // 根据当前时间判断风格
    if ($current_hour >= $day_start && $current_hour < $night_start) {
      $style = 'light';   // 白天风格
    } elseif ($current_hour >= $night_start || $current_hour < $day_start) {
      $style = 'dark';   // 黑夜风格
    }
  }

  return $style;
}

/**
 * 获取今天开始结束时间戳
 */
function get_today_time_range()
{
  $timezone_object = wp_timezone();
  // 获取今天开始和结束时间的DateTime对象
  $today_start = new DateTime('today', $timezone_object);
  $today_end = clone $today_start;
  $today_end->modify('+1 day');

  // 转换为时间戳
  $today_start_timestamp = $today_start->getTimestamp();
  $today_end_timestamp = $today_end->getTimestamp();


  return array(
    'start' => $today_start->getTimestamp(),
    'end'   => $today_end->getTimestamp(),
  );
}

/**
 * 获取文章列表显示风格配置
 */
function get_posts_style_config($cat_id = 0)
{
  $item_col = _capalot('archive_item_col', '4');
  $item_style = _capalot('archive_item_style', 'grid');
  $media_size = _capalot('post_thumbnail_size', 'radio-3x2');

  $media_size_type = get_thumbnail_size_type();
  $media_align_type = get_thumbnail_align_type();

  $item_entry = _capalot('archive_item_entry', array(
    'category_dot',
    'entry_desc',
    'entry_footer',
    'vip_icon',
  ));

  $term_item_style = _capalot('site_term_item_style', []);

  if (!empty($cat_id) && !empty($term_item_style)) {
    foreach ($term_item_style as $key => $item) {
      if ($cat_id == $item['cat_id']) {
        $item_col   = $item['archive_item_col'];
        $item_style = $item['archive_item_style'];
        $media_size = $item['post_thumbnail_size'];
        $item_entry = $item['archive_item_entry'];
        continue;
      }
    }
  }


  $row_cols = [
    '1' => 'grid-cols-1 gap-4',
    '2' => 'grid-cols-1 md:grid-cols-2 gap-4',
    '3' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 ',
    '4' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4',
    '5' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xxl:grid-cols-5 gap-4',
    '6' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xxl:grid-cols-5 xxxl:grid-cols-6 gap-4',
  ];

  if ($item_style == 'list' && $item_col >= 2) {
    // 列表模式自适应...
    $row_cols_class = 'grid-cols-1 md:grid-cols-2 gap-4 ';
  } else {
    $row_cols_class = $row_cols[$item_col];
  }

  $config = array(
    'type'            => $item_style, //grid grid-overlay list list-icon
    'row_cols_class'  => $row_cols_class,
    'media_size_type' => $media_size_type,
    'media_fit_type'  => $media_align_type,
    'media_class'     => $media_size, // media-3x2 media-3x3 media-2x3
    'is_vip_icon'     => @in_array('vip_icon', $item_entry),
    'is_entry_desc'   => @in_array('entry_desc', $item_entry),
    'is_entry_meta' => @in_array('entry_footer', $item_entry),
    'is_entry_cat' => @in_array('category_dot', $item_entry),
  );
  return $config;
}

function capalot_get_color_class($key = 0)
{
  $colors  = ['danger', 'primary', 'success', '[#f7c32e]', 'info', 'secondary'];
  $color   = (isset($colors[$key])) ? $colors[$key] : 'secondary';
  return $color;
}

// 获取文章缩略图尺寸
function get_thumbnail_size_type()
{
  $options = ['bg-cover', 'bg-auto', 'bg-contain'];
  $option = _capalot('site_thumb_size_type', 'bg-cover');

  if (!in_array($option, $options))
    $option = $options[0];

  return $option;
}

// 获取图片验证码
function get_captcha_code_img()
{
  // 生成随机验证码
  $captcha_code = substr(md5(mt_rand()), 0, 6);

  // 将验证码保存到Cookie中
  Capalot_Cookie::set('captcha_code', $captcha_code, time() + 60 * 5, '/');

  // 创建图片
  $image = imagecreatetruecolor(100, 30);

  // 定义颜色
  $bg_color = imagecolorallocate($image, 255, 255, 255);
  $text_color = imagecolorallocate($image, 0, 0, 0);

  // 填充背景色
  imagefilledrectangle($image, 0, 0, 100, 30, $bg_color);

  // 添加验证码文本
  imagestring($image, 5, 30, 5, $captcha_code, $text_color);

  // 将图像输出为base64编码的数据
  ob_start();
  imagepng($image);
  $image_data = ob_get_clean();
  $base64_image = 'data:image/png;base64,' . base64_encode($image_data);

  // 输出base64编码的图片数据
  return $base64_image;

  // 销毁图像
  imagedestroy($image);
}

/**
 * 确认验证码是否正确
 *
 * @param $code 验证码
 */
function verify_captcha_code($code)
{
  // 验证$code与cookie中的验证码是否一致
  $captcha_code = Capalot_Cookie::get('captcha_code');
  if (empty($captcha_code) || $captcha_code != $code) {
    return false;
  }

  // 验证成功后，删除cookie中的验证码
  Capalot_Cookie::delete('captcha_code');

  return true;
}

// 获取文章缩略图对齐方式
function get_thumbnail_align_type()
{
  $options = [
    'bg-left-top', 'bg-right-top', 'bg-center-top',
    'bg-left-center', 'bg-right-center', 'bg-center',
    'bg-left-bottom', 'bg-right-bottom', 'bg-center-bottom'
  ];
  $option = _capalot('site_thumb_fit_type', 'bg-center');

  if (!in_array($option, $options))
    $option = $options[0];

  return $option;
}

/**
 * 获取二维码地址
 */
function get_qrcode_url($text)
{
  $api_url = get_template_directory_uri() . '/inc/plugins/qrcode/qrcode.php?data=';
  return $api_url . $text;
}

//只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
function capalot_substr_cut($user_name)
{

  if (empty($user_name)) {
    return '游客';
  }

  $strlen   = mb_strlen($user_name, 'utf-8');
  $firstStr = mb_substr($user_name, 0, 1, 'utf-8');
  $lastStr  = mb_substr($user_name, -1, 1, 'utf-8');
  if ($strlen < 2) {
    return $user_name;
  }
  return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
}

// 判断当前是否是移动端
function capalot_is_mobile()
{
  $userAgent = $_SERVER['HTTP_USER_AGENT'];
  $mobileKeywords = array('Mobile', 'Android', 'iPhone', 'iPad', 'Windows Phone', 'BlackBerry');

  foreach ($mobileKeywords as $keyword) {
    if (stripos($userAgent, $keyword) !== false) {
      return true;
    }
  }

  return false;
}


//  获取自定义分类法筛选配置

function get_site_custom_taxonomy()
{

  $data   = _capalot('site_custom_taxonomy', array());
  $config = array();
  if (empty($data) || !is_array($data)) {
    return array();
  }

  foreach ($data as $key => $value) {

    $custom_taxonomy = trim($value['taxonomy']);

    if (!preg_match("/^[a-zA-Z\s]+$/", $custom_taxonomy)) {
      continue; //不是英文标识
    }

    $config[$custom_taxonomy] = $value;
  }

  return $config;
}

//  根据分类id获取当前分类筛选配置

function get_cat_filter_config($cat_id)
{

  $data   = _capalot('site_term_filter_config', array());

  if (empty($cat_id) || empty($data)) {
    return array();
  }

  $config = array();

  foreach ($data as $item) {
    if ($item['cat_id'] == $cat_id) {
      $config = $item;
    }
  }

  return $config;
}
