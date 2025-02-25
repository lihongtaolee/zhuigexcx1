<?php
/*
 * 追格商城小程序
 * Author: 追格
 * Help document: https://www.zhuige.com/product/sc.html
 * github: https://github.com/zhuige-com/zhuige_shop
 * gitee: https://gitee.com/zhuige_com/zhuige_shop
 * License: GPL-2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 订单管理页面
function zhuige_shop_render_orders_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zhuige_shop_order';

    // 获取订单列表
    $orders = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY create_time DESC" );
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">订单管理</h1>
        <hr class="wp-header-end">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>订单号</th>
                    <th>用户ID</th>
                    <th>商品ID</th>
                    <th>价格</th>
                    <th>状态</th>
                    <th>创建时间</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $orders as $order ) : ?>
                    <tr>
                        <td><?php echo esc_html( $order->order_no ); ?></td>
                        <td><?php echo esc_html( $order->user_id ); ?></td>
                        <td><?php echo esc_html( $order->goods_id ); ?></td>
                        <td><?php echo esc_html( $order->price ); ?></td>
                        <td><?php echo esc_html( $order->status ); ?></td>
                        <td><?php echo esc_html( $order->create_time ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// 输出调试日志（可删除此行）
error_log( "调用 zhuige_shop_render_orders_page()" );
zhuige_shop_render_orders_page();
