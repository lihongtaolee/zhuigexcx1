<?php
/**
 * 追格商城商品控制器
 */
if (!defined('ABSPATH')) exit;

class Zhuige_Shop_Goods_Controller extends Zhuige_Shop_Base_Controller {
    public function __construct() {
        parent::__construct();
    }

    /**
     * 获取商品列表
     *
     * @return array
     */
    public function get_goods_list() {
        global $wpdb;
        $table_goods = $wpdb->prefix . "zhuige_shop_goods";
        $goods = $wpdb->get_results("SELECT * FROM $table_goods ORDER BY update_time DESC", ARRAY_A);
        return $goods;
    }
    
    /**
     * 根据 ID 获取单个商品详情
     *
     * @param int $id
     * @return object|null
     */
    public function get_goods_by_id($id) {
        global $wpdb;
        $table_goods = $wpdb->prefix . "zhuige_shop_goods";
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_goods WHERE id = %d", $id));
    }
    
    /**
     * 添加或更新商品
     *
     * @param array $data
     * @param int|null $id
     * @return int|false 返回商品 ID 或 false
     */
    public function save_goods($data, $id = null) {
        global $wpdb;
        $table_goods = $wpdb->prefix . "zhuige_shop_goods";
        if ($id) {
            $result = $wpdb->update($table_goods, $data, array('id' => $id));
            return $result !== false ? $id : false;
        } else {
            $result = $wpdb->insert($table_goods, $data);
            return $result ? $wpdb->insert_id : false;
        }
    }
}
?>
