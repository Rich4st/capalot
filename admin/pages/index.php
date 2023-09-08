<?php

defined('ABSPATH') || exit;

$Capalot_UI = new Capalot_UI();
$screen = $Capalot_UI->screen;

?>

<div>
  <h1>商城总览</h1>
  <div>
    <?php do_meta_boxes($screen->id, 'column1', null); ?>
  </div>
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
    add_meta_box(
      'widget-1',
      '今日订单统计',
      array($this, 'today_order_quantity'),
      $this->screen->id,
      'column1',
    );

  }

  /**
   * 今日订单统计
   */
  public function today_order_quantity()
  {
    echo '
    <div>
    <h2>今日订单统计:</h2>
    <div>
      <span>总数:</span>10
    </div>
  </div>
    ';
  }
}
