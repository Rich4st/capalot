<?php

defined('WPINC') || exit;

$Capalot_List_Table = new Capalot_List_Table();
$Capalot_List_Table->prepare_items();
$message = $Capalot_List_Table->message;
?>

<!-- 主页面 -->
<div class="wrap zb-admin-page">

    <h1 class="wp-heading-inline">文章价格批量修改</h1>
    <p>注意事项：修改不可逆转，请您勾选好要修改的文章价格，设置价格和会员折扣</p>
    <p>1、如果只填写价格，其他不选 则只修改价格，其他字段同理</p>
    <p>2、价格单位为站内币，可以设置为0或者其他数字</p>
    <p>3、一键修改全站所有文章价格只会修改已经有价格字段的数据</p>

    <?php if (!empty($message)) {
        echo '<div class="notice notice-zbinfo is-dismissible" id="message"><p>' . $message . '</p></div>';
    } ?>

    <hr class="wp-header-end">

    <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
            <form method="get">
                <?php $Capalot_List_Table->search_box('搜索', 'post_id'); ?>
                <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
                <?php wp_nonce_field('zb-admin-page-nonce', '_nonce'); ?>
                <?php $Capalot_List_Table->display(); ?>
            </form>
        </div>
    </div>
    <br class="clear">
</div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        jQuery('input#doaction').click(function(e) {
            return confirm('确实要对所选条目执行此批量操作吗?');
        });


    });
</script>
<!-- 主页面END -->

<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * Create a new table class that will extend the WP_List_Table
 */
class Capalot_List_Table extends WP_List_Table
{
    public $message = '';
    public function __construct()
    {
        parent::__construct(array(
            'singular' => 'item',
            'plural'   => 'items',
            'ajax'     => false,
        ));
    }

    public function set_message($message)
    {
        $this->message = $message;
    }

    public function prepare_items()
    {
        $columns  = $this->get_columns();
        $sortable = $this->get_sortable_columns();

        $per_page     = 10;
        $current_page = $this->get_pagenum();

        $this->set_pagination_args(array(
            'total_items' => 0,
            'per_page'    => $per_page,
            'total_pages' => 0,
        ));

        $this->_column_headers = array($columns, array(), $sortable);
        $this->process_bulk_action();

        $this->items = $this->table_data($per_page, $current_page);
    }

    //获取数据库数据
    private function table_data($per_page = 5, $page_number = 1)
    {

        global $wpdb;

        //排序分页
        $orderby   = isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns())) ? $_REQUEST['orderby'] : 'post_date';
        $order     = isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')) ? $_REQUEST['order'] : 'desc';
        $order_str = sanitize_sql_orderby($orderby . ' ' . $order);
        $limit_from = ($page_number - 1) * $per_page;

        $search_term = (!empty($_REQUEST['s'])) ? trim($_REQUEST['s']) : '';
        $search_like  = '%' . $wpdb->esc_like($search_term) . '%';

        // 筛选
        $where = "WHERE post_type = 'post' AND post_status = 'publish'";
        if (!empty($search_term)) {
            $where .= $wpdb->prepare(" AND (post_title LIKE %s)", $search_like);
        }

        $order = "ORDER BY {$order_str} LIMIT {$limit_from}, {$per_page}";

        // 查询文章列表和总数
        $query = "SELECT SQL_CALC_FOUND_ROWS ID, post_title, post_date, post_author FROM {$wpdb->posts} {$where} {$order}";
        $data = $wpdb->get_results($query, ARRAY_A);
        $total_items = $wpdb->get_var("SELECT FOUND_ROWS()");

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
            'orderby'     => $orderby,
            'order'       => $order,
        ));

        return $data;
    }

    // 获取列列表
    public function get_columns()
    {

        $options = get_site_vip_options();
        $columns = [
            'cb'  => '<input type="checkbox" />',
            'post_title'  => '文章标题',
            'ID'  => '文章ID',
            'capalot_price'  => '售价' . get_site_coin_name(),
            'capalot_vip_rate'  => $options['vip']['name'] . '折扣',
            'capalot_is_boosvip'  => $options['boosvip']['name'] . '免费',
            'capalot_close_novip_pay'  => $options['no']['name'] . '禁止购买',
            // 'capalot_paynum'  => '已售数量',
        ];
        return $columns;
    }

    //可排序列字段
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'ID'          => array('ID', false),
            'post_title'  => array('post_title', false),
        );

        return $sortable_columns;
    }

    public function no_items()
    {
        _e('没有找到相关数据');
    }

    //列数据显示
    public function column_default($item, $i)
    {
        switch ($i) {
            case 'post_title':
                if (get_permalink($item['ID'])) {
                    return sprintf('<a target="_blank" href=%s>%s</a>', get_permalink($item['ID']), $item[$i]);
                }
                break;
            case 'capalot_price':
            case 'capalot_vip_rate':
            case 'capalot_is_boosvip':
            case 'capalot_close_novip_pay':
            case 'capalot_paynum':
                $meta = get_post_meta($item['ID'], $i, true);
                $meta = ($meta == '') ? '' : $meta;
                return $meta;
                break;
            default:
                return sprintf('<i>%s</i>', $item[$i]);
        }
    }

    // 显示分页
    public function display_tablenav($which)
    {
        ob_start(); ?>
        <div class="tablenav mb-4 <?php echo esc_attr($which); ?>">
            <?php if ('top' === $which) { ?>
                <div class="alignleft actions">
                    售价：<input type="text" size="5" placeholder="<?php echo get_site_coin_name(); ?>" name="capalot_price" value="">
                    VIP折扣：<input type="text" size="5" placeholder="0/1/0.N" name="capalot_vip_rate" value="">
                    <input type="checkbox" name="capalot_is_boosvip" value="1">永久VIP免费
                    <input type="checkbox" name="capalot_close_novip_pay" value="1">普通用户禁止购买
                    <?php $this->bulk_actions(); ?>
                </div>

            <?php } ?>

            <?php
            $this->extra_tablenav($which);
            $this->pagination($which);
            ?>
            <?php if ($which == 'bottom') {
            } ?>
            <br class="clear" />
        </div>
<?php echo ob_get_clean();
    }

    //在批量操作和分页之间显示的额外控件
    public function extra_tablenav($which)
    {
        // null
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['ID']
        );
    }


    public function current_action()
    {
        if (isset($_REQUEST['filter_action']) && !empty($_REQUEST['filter_action'])) {
            return false;
        }
        if (isset($_REQUEST['action']) && -1 != $_REQUEST['action']) {
            return $_REQUEST['action'];
        }
        if (isset($_REQUEST['action2']) && -1 != $_REQUEST['action2']) {
            return $_REQUEST['action2'];
        }
        return false;
    }

    // 批量操作参数
    public function get_bulk_actions()
    {
        $actions = array(
            'edit_cb' => '修改选中文章',
            'edit_all' => '修改全部文章',
        );
        return $actions;
    }

    //批量操作触发
    public function process_bulk_action()
    {
        global $wpdb;
        $capalot_price = get_response_param('capalot_price', '', 'request');
        $capalot_vip_rate = get_response_param('capalot_vip_rate', '', 'request');
        $capalot_is_boosvip = get_response_param('capalot_is_boosvip', '0', 'request');
        $capalot_close_novip_pay = get_response_param('capalot_close_novip_pay', '0', 'request');

        if ($this->current_action() == 'edit_cb' || $this->current_action() == 'edit_all') {
            $ids    = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            $_nonce = isset($_REQUEST['_nonce']) ? $_REQUEST['_nonce'] : '';

            if (!wp_verify_nonce($_nonce, 'zb-admin-page-nonce')) {
                $this->set_message('nonce验证失败，请返回刷新重试');
                return false;
            }

            if ($this->current_action() == 'edit_cb' && !empty($ids)) {

                $__num = 0;
                foreach ($ids as $post_id) {
                    if ($capalot_price != '') {
                        update_post_meta($post_id, 'capalot_price', floatval(abs($capalot_price)));
                    }
                    if ($capalot_vip_rate != '') {
                        if ($capalot_vip_rate < 0 || $capalot_vip_rate > 1) {
                            $capalot_vip_rate = 1;
                        }
                        update_post_meta($post_id, 'capalot_vip_rate', floatval($capalot_vip_rate));
                    }
                    if ($capalot_is_boosvip != '') {
                        $capalot_is_boosvip = (bool) $capalot_is_boosvip;
                        update_post_meta($post_id, 'capalot_is_boosvip', intval($capalot_is_boosvip));
                    }
                    if ($capalot_close_novip_pay != '') {
                        $capalot_close_novip_pay = (bool) $capalot_close_novip_pay;
                        update_post_meta($post_id, 'capalot_close_novip_pay', intval($capalot_close_novip_pay));
                    }

                    $__num++;
                }
                $this->set_message(sprintf('成功修改 %d 条记录', $__num));
            } elseif ($this->current_action() == 'edit_all') {
                $__num = 0;
                if ($capalot_price != '') {
                    $__num += $wpdb->update($wpdb->postmeta, array('meta_value' => floatval(abs($capalot_price))), array('meta_key' => 'capalot_price'));
                }
                if ($capalot_vip_rate != '') {
                    if ($capalot_vip_rate < 0 || $capalot_vip_rate > 1) {
                        $capalot_vip_rate = 1;
                    }
                    $__num += $wpdb->update($wpdb->postmeta, array('meta_value' => floatval($capalot_vip_rate)), array('meta_key' => 'capalot_vip_rate'));
                }
                if ($capalot_is_boosvip != '') {
                    $capalot_is_boosvip = (bool) $capalot_is_boosvip;
                    $__num += $wpdb->update($wpdb->postmeta, array('meta_value' => intval($capalot_is_boosvip)), array('meta_key' => 'capalot_is_boosvip'));
                }
                if ($capalot_close_novip_pay != '') {
                    $capalot_close_novip_pay = (bool) $capalot_close_novip_pay;
                    $__num += $wpdb->update($wpdb->postmeta, array('meta_value' => intval($capalot_close_novip_pay)), array('meta_key' => 'capalot_close_novip_pay'));
                }

                $this->set_message(sprintf('成功修改 %d 条记录', $__num));
            }
        }
    }
}
