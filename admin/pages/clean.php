<?php

defined('WPINC') || exit;

// 缓存/临时/垃圾数据清理


$page_action = get_response_param('action', '', 'get');

$message = '';

global $wpdb;

// clear_hook
if (isset($_GET['clear_hook'])) {

  switch (trim($_GET['clear_hook'])) {
    case 'order':
      $table_name = $wpdb->prefix . 'capalot_order';
      $query   = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE pay_status = %d AND create_time < %s", 0, strtotime("-1 day")));
      $message = sprintf('一天前所有未支付订单清理成功 (%s)个记录', number_format_i18n($query));
      break;
    case 'down':
      $table_name = $wpdb->prefix . 'capalot_download';
      $query   = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE create_time < %s", strtotime("-7 day")));
      $message = sprintf('成功清理7天前下载记录 (%s)个记录', number_format_i18n($query));
      break;
    case 'optimize':
      $query = $wpdb->get_col('SHOW TABLES');
      if ($query) {
        $tables = implode(',', $query);
        $wpdb->query("OPTIMIZE TABLE $tables");
        $message = sprintf('已成功优化数据表 %s ', number_format_i18n(count($query)));
      }
      break;
    case 'migrate_old_order_data':
      $ZB_SetupDb = new ZB_SetupDb;
      $count = $ZB_SetupDb->migrate_old_order_data();
      $message = sprintf('已成迁移数据记录 %s ', number_format_i18n($count));
      break;
    case 'migrate_old_post_meta':
      $ZB_SetupDb = new ZB_SetupDb;
      $count = $ZB_SetupDb->migrate_old_post_meta();
      $message = sprintf('已成迁移数据记录 %s ', number_format_i18n($count));
      break;
    case 'migrate_old_filter_meta':
      $ZB_SetupDb = new ZB_SetupDb;
      $count = $ZB_SetupDb->migrate_old_filter_meta();
      $message = sprintf('已成迁移数据记录 %s ', number_format_i18n($count));
  }
}

?>
<!-- 主页面 -->
<div class="wrap zb-admin-page">

  <div class="health-check-header">
    <h1 class="m-0">数据清理优化</h1>
    <p>缓存/临时/历史数据清理，有效提升数据库负载和速度，孤立数据表示关了用户被删除，这些数据无意义</p>
    <p class="attention">清理数据记录会影响商城总览中统计数量减少和占比变化，不方便观察运营，请您谨慎操作</p>
  </div>

  <?php if (!empty($message)) {
    echo '<div class="notice notice-zbinfo is-dismissible" id="message"><p>' . $message . '</p></div>';
  } ?>

  <hr class="wp-header-end">

  <div class="health-check-body">

    <?php

    $tables = [];
    $order_table = $wpdb->prefix . 'capalot_order';

    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $order_table WHERE 1=%d", 1));
    $count2 = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $order_table WHERE pay_status=%d", 0));

    $sum = $count + $count2;

    $ratio = ($sum > 0) ? round(($count2 / $sum * 100)) : 0;
    $d_link = ($ratio > 0) ? 'admin.php?page=zb-admin-page-clear&clear_hook=order&day=1' : '';
    $tables['order'] = [
      'title' => '订单清理',
      'desc' => '总计 <strong class="attention"><span>' . $count . '</span></strong> 个订单记录 <strong class="attention"><span>' . $count2 . '</span> 个未支付订单</strong>。',
      'tr' => [
        [
          '未支付订单（全部）',
          $count2,
          $ratio . '%',
          $d_link,
        ]
      ],
    ];

    $download_table = $wpdb->prefix . 'capalot_download';
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $download_table WHERE 1=%d", 1));
    $count2 = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $download_table WHERE create_time<%s", strtotime("-7 day")));
    $sum = $count + $count2;
    $ratio = ($sum > 0) ? round(($count2 / $sum * 100)) : 0;
    $d_link = ($ratio > 0) ? 'admin.php?page=zb-admin-page-clear&clear_hook=down&day=7' : '';
    $tables['down'] = [
      'title' => '下载记录清理',
      'desc' => '总计 <strong class="attention"><span>' . $count . '</span></strong> 个下载记录 <strong class="attention"> 七天前下载记录<span>' . $count2 . '</span> </strong>',
      'tr' => [
        [
          '七天前的下载记录',
          $count2,
          $ratio . '%',
          $d_link,
        ]
      ],
    ];

    $count = count($wpdb->get_col('SHOW TABLES'));
    $tables['optimize'] = [
      'title' => '数据库优化',
      'desc' => '总计 <strong class="attention"><span>' . $count . '</span></strong> 个数据表，当数据很多时，每周一次或每月一次',
      'tr' => [
        [
          '数据表',
          $count,
          '100%',
          'admin.php?page=zb-admin-page-clear&clear_hook=optimize&day=1',
        ]
      ],
    ];

    foreach ($tables as $key => $item) {
      echo '<div class="clear-itme-warp">';
      echo sprintf('<h3>%s</h3>', $item['title']);
      echo sprintf('<p>%s</p>', $item['desc']);
      echo '<table class="widefat table-sweep">';
      echo '<thead><tr><th>详情</th><th>统计</th><th>占比</th><th>操作</th></tr></thead><tbody>';

      foreach ($item['tr'] as $iex => $tr) {
        $class = ($iex % 2 != 0) ? 'class="alternate"' : 'class=""';
        echo '<tr ' . $class . '>';
        foreach ($tr as $index => $td) {
          if ($index == 0) {
            echo sprintf('<td><strong>%s</strong></td>', $td);
          } elseif ($index == 3) {
            if (empty($td)) {
              echo '<td>无需清理</td>';
            } else {
              echo sprintf('<td><a class="button button-primary" href="%s">处理数据</a></td>', $td);
            }
          } else {
            echo sprintf('<td><span>%s</span></td>', $td);
          }
        }
        echo '</tr>';
      }
      echo '</tbody></table>';
      echo '</div>';
    }

    ?>



  </div>
  <br class="clear">
</div>
<script type="text/javascript">
  jQuery(document).ready(function($) {
    jQuery('a.button-primary').click(function(e) {
      return confirm('确实要对所选条目执行数据处理吗?');
    });

  });
</script>
<!-- 主页面END -->
