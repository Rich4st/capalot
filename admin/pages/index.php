<?php

defined('ABSPATH') || exit;

$Capalot_UI = new Capalot_UI();
$screen = $Capalot_UI->screen;

?>

<div>
  <h1 class="text-2xl my-4">商城管理总览/仪表盘</h1>
  <ul class="grid grid-col-2 md:grid-cols-2 lg:grid-cols-3 md:gap-4">
    <li>
      <?php do_meta_boxes($screen->id, 'column1', null); ?>
    </li>
    <li>
      <?php do_meta_boxes($screen->id, 'column2', null); ?>
    </li>
    <li>
      <?php do_meta_boxes($screen->id, 'column3', null); ?>
    </li>
    <li>
      <?php do_meta_boxes($screen->id, 'column4', null); ?>
    </li>
  </ul>
</div>


<?php
class Capalot_UI
{

  public $screen;

  public function __construct()
  {
    wp_enqueue_script('dashboard');

    if (wp_is_mobile())
      wp_enqueue_script('jquery-touch-punch');

    $this->screen = get_current_screen();
    $this->reg_box();
  }

  public function reg_box()
  {
    // 今日订单统计
    add_meta_box(
      'widget-1',
      sprintf('<span class="pl-2 dashicons dashicons-chart-bar"></span><span class="text-base font-semibold">今日订单统计(%s)</span>', wp_date('Y-m-d')),
      array($this, 'today_order_quantity'),
      $this->screen->id,
      'column1',
    );

    // 今日下载统计
    add_meta_box(
      'widget-2',
      sprintf('<span class="pl-2 dashicons dashicons-download"></span><span class="text-base font-semibold">今日下载统计(%s)</span>', wp_date('Y-m-d')),
      array($this, 'today_download_quantity'),
      $this->screen->id,
      'column2',
    );

    // 今日推广统计
    add_meta_box(
      'widget-3',
      sprintf('<span class="pl-2 dashicons dashicons-networking"></span><span class="text-base font-semibold">今日推广统计(%s)</span>', wp_date('Y-m-d')),
      array($this, 'today_promote_quantity'),
      $this->screen->id,
      'column3',
    );

    // 今日用户统计
    add_meta_box(
      'widget-4',
      sprintf('<span class="pl-2 dashicons dashicons-groups"></span><span class="text-base font-semibold">今日用户统计(%s)</span>', wp_date('Y-m-d')),
      array($this, 'today_user_quantity'),
      $this->screen->id,
      'column4',
    );
  }

  /**
   * 今日订单统计
   */
  public function today_order_quantity()
  {

    $today_time = get_today_time_range();
    $res = $this->query_pay_info_for_time($today_time['start'], $today_time['end']);
    $price1 = $price2 = $count1 = $count2 = 0;
    foreach ($res as $key => $v) {
      if ($v->pay_status == 0) {
        $price1 = $v->pay_price;
        $count1 = $v->count;
      } elseif ($v->pay_status == 1) {
        $price2 = $v->pay_price;
        $count2 = $v->count;
      }
    }
    $sumcount = $count1 + $count2;
    $ratio = ($sumcount > 0) ? round(($count2 / $sumcount * 100)) : 0;
    $data = [
      ['name' => '总订单数', 'value' => sprintf('%s条', ($count1 + $count2))],
      ['name' => '订单总额', 'value' => sprintf('￥%s', ($price2 + $price1))],
      ['name' => '付款率', 'value' => sprintf('%0.1f%%', $ratio)],
    ];
    ob_start(); ?>
    <div class="text-center">
      <p> 今日已付款 (<?php echo $count2; ?>/条)</p>
      <p class="text-xl font-semibold text-orange-400">￥<?php echo $price2; ?></p>
    </div>
    <div class="flex items-center justify-around mt-4 pt-2 border-t">
      <?php foreach ($data as $item) {
        echo '<div class="text-center">';
        echo '<span class="text-gray-500">' . $item['name'] . '</span>
              <h4 class="font-semibold">' . $item['value'] . '</h4>';
        echo '</div>';
      } ?>
    </div>
  <?php echo ob_get_clean();
  }

  /**
   * 今日下载统计
   */
  public function today_download_quantity()
  {

    global $wpdb;
    $table_name = $wpdb->prefix . 'capalot_download';
    $today_time = get_today_time_range();

    // 下载总次数
    $res = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT COUNT(id) FROM {$table_name}
          WHERE create_time BETWEEN %s AND %s ",
        $today_time['start'],
        $today_time['end']
      )
    );

    // 下载用户数
    $res2 = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT COUNT(DISTINCT post_id) FROM {$table_name}
          WHERE create_time BETWEEN %s AND %s ",
        $today_time['start'],
        $today_time['end']
      )
    );

    // 重复下载率
    $res3 = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT COUNT(DISTINCT user_id) FROM {$table_name}
          WHERE create_time BETWEEN %s AND %s ",
        $today_time['start'],
        $today_time['end']
      )
    );

    $ratio = ($res > 0) ? round(((1 - $res2 / $res) * 100)) : 0;

    $data = [
      ['name' => '下载总次数', 'value' => sprintf('%d次', $res)],
      ['name' => '下载用户数', 'value' => sprintf('%d位', $res3)],
      ['name' => '重复下载率', 'value' => sprintf('%0.1f%%', $ratio)],
    ];
    ob_start(); ?>
    <div class="text-center">
      <div>
        <p>今日被下载产品数</p>
        <p class="text-xl font-semibold text-green-400"><?php echo $res2; ?> 个</p>
      </div>
      <div class="flex items-center justify-around mt-4 pt-2 border-t">
        <?php foreach ($data as $item) {
          echo '<div class="col text-center">';
          echo '<span class="text-gray-500">' . $item['name'] . '</span>
                <h4 class="font-semibold">' . $item['value'] . '</h4>';
          echo '</div>';
        } ?>
      </div>
    </div>
  <?php echo ob_get_clean();
  }

  /**
   * 今日推广统计
   */
  public function today_promote_quantity()
  {
    global $wpdb;
    $today_time = get_today_time_range(); //今天时间戳信息 $today_time['start'],$today_time['end']
    $table_name = $wpdb->prefix . 'capalot_aff';

    $res = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT status,COUNT(id) as count FROM {$table_name}
        WHERE create_time BETWEEN %s AND %s GROUP BY status ORDER BY status ASC",
        $today_time['start'],
        $today_time['end']
      )
    );

    $count1 = $count2 = $count3 =  0;
    foreach ($res as $key => $v) {
      if ($v->status == 0) {
        $count1 = $v->count; //未提现
      } elseif ($v->status == 1) {
        $count2 = $v->count; //提现中
      } elseif ($v->status == 2) {
        $count3 = $v->count; //已提现
      }
    }

    $sumcount = $count1 + $count2 + $count3;
    $ratio = ($sumcount > 0) ? round(($count2 / $sumcount * 100)) : 0;
    $data = [
      ['name' => '未提现', 'value' => sprintf('%d单', $count1)],
      ['name' => '申请提现', 'value' => sprintf('%d单', $count2)],
      ['name' => '提现率', 'value' => sprintf('%0.1f%%', $ratio)],
    ];
    ob_start(); ?>
    <div class="text-center">
      <div>
        <p>今日推广单数</p>
        <p class="text-xl font-semibold text-pink-500"><?php echo $sumcount; ?> 单</p>
      </div>

      <div class="flex items-center justify-around mt-4 pt-2 border-t">
        <?php foreach ($data as $item) {
          echo '<div class="col text-center">';
          echo '<span class="text-gray-500">' . $item['name'] . '</span>
                <h4 class="font-semibold">' . $item['value'] . '</h4>';
          echo '</div>';
        } ?>
      </div>
    </div>
  <?php echo ob_get_clean();
  }

  /**
   * 今日用户统计
   */
  public function today_user_quantity()
  {
    global $wpdb;
    $count1 = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users");
    $count2 = $wpdb->get_var(
      "SELECT COUNT(ID) FROM $wpdb->users
      WHERE DATE_FORMAT( user_registered,'%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')"
    );
    $count3 = $wpdb->get_var(
      "SELECT count(a.ID) FROM $wpdb->users a INNER JOIN $wpdb->usermeta b ON ( a.ID = b.user_id )
      WHERE (  ( b.meta_key = 'cao_banned' AND b.meta_value = 1 )  )"
    );

    $ratio = ($count1 > 0) ? round(($count3 / $count1 * 100)) : 0;
    $data = [
      ['name' => '全站总用户', 'value' => sprintf('%d位', $count1)],
      ['name' => '已封号用户', 'value' => sprintf('%d位', $count3)],
      ['name' => '封号率', 'value' => sprintf('%0.1f%%', $ratio)],
    ];
    ob_start(); ?>
    <div class="card-body text-center">
      <div>
        <p>今日注册用户数</p>
        <p class="text-xl font-semibold text-sky-500"><?php echo $count2; ?> 位</p>
      </div>

      <div class="flex items-center justify-around mt-4 pt-2 border-t">
        <?php foreach ($data as $item) {
          echo '<div class="col text-center">';
          echo '<span class="text-muted">' . $item['name'] . '</span><h4 class="number-font">' . $item['value'] . '</h4>';
          echo '</div>';
        } ?>
      </div>
    </div>
<?php echo ob_get_clean();
  }

  //查询数据库 不统计站内币支付
  public function query_pay_info_for_time($startime, $endtime)
  {
    global $wpdb, $pay_type_not_in;

    $table = $wpdb->prefix . 'capalot_order';

    $data = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT pay_status,COUNT(id) as count,SUM(pay_price) as pay_price
          FROM {$table} WHERE pay_type NOT IN (%s) AND create_time BETWEEN %s AND %s
          GROUP BY pay_status
          ORDER BY pay_status ASC",
        $pay_type_not_in,
        $startime,
        $endtime
      )
    );
    return $data;
  }
}
