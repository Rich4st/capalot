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
    'nav_type' => _capalot('site_pagination_type', 'click'),
    'nav_class' => 'pagination justify-content-center',
  );

  $args = wp_parse_args(
    $args,
    $default
  );

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

function infinite_scroll_button($type = 'click')
{
  return '<div class="btn__wrapper text-center">
  <a href="#!" class="btn btn__primary" id="load-more">加载更多</a>
  <p id="no-more-button" style="display: none;">没有更多了</p>
</div>';
}

function capalot_load_more()
{
  $ajaxposts = new WP_Query([
    'ignore_sticky_posts' => false,
    'post_status' => 'publish',
    'paged' => $_POST['paged'],
  ]);

  $response = '';
  $max_pages = $ajaxposts->max_num_pages;

  if ($ajaxposts->have_posts()) {
    while ($ajaxposts->have_posts()) : $ajaxposts->the_post();
      $response .= '<li class="p-10 shadow-[0_10px_20px_rgba(240,_46,_170,_0.7)]">';
      $response .= '<a href="' . get_the_permalink() . '">' . get_the_title() . '</a>';
      $response .= '</li>';
    endwhile;
  } else {
    $response = '';
  }

  $result = [
    'max' => $max_pages,
    'html' => $response,
  ];

  echo json_encode($result);
  exit;
}
add_action('wp_ajax_capalot_load_more', 'capalot_load_more');

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
 * @Author Dadong2g
 * @date   2022-11-27
 * @return [type]
 */
if (!function_exists('get_current_url')) {
  function get_current_url() {
      $current_url = home_url(add_query_arg(array()));
      return esc_url($current_url);
  }
}

//获取用户客户端IP get_ip_address()
function get_ip_address($ignore_private_and_reserved = false) {
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
