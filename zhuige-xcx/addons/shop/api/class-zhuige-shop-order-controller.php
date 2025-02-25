<?php
/**
 * 追格商城订单控制器
 */
if (!defined('ABSPATH')) exit;

class Zhuige_Shop_Order_Controller extends Zhuige_Shop_Base_Controller {
    public function __construct() {
        parent::__construct();
    }

    /**
     * 获取订单列表
     *
     * @return array
     */
    public function get_order_list() {
        global $wpdb;
        $table_order = $wpdb->prefix . "zhuige_shop_order";
        $orders = $wpdb->get_results("SELECT * FROM $table_order ORDER BY create_time DESC", ARRAY_A);
        return $orders;
    }
    
    /**
     * 根据订单 ID 获取订单详情
     *
     * @param int $id
     * @return object|null
     */
    public function get_order_by_id($id) {
        global $wpdb;
        $table_order = $wpdb->prefix . "zhuige_shop_order";
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_order WHERE id = %d", $id));
    }
    
    /**
     * 更新订单状态
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function update_order_status($id, $status) {
        global $wpdb;
        $table_order = $wpdb->prefix . "zhuige_shop_order";
        $result = $wpdb->update($table_order, array('order_status' => $status, 'update_time' => current_time('mysql')), array('id' => $id));
        return $result !== false;
    }
}
?>
