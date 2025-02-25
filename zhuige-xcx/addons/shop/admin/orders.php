<?php
/**
 * 追格商城 - 订单管理页面
 */
if (!current_user_can('manage_options')) {
    wp_die(__('你没有权限访问此页面。'));
}

$order_controller = new Zhuige_Shop_Order_Controller();
$orders = $order_controller->get_order_list();

// 处理订单状态更新操作（通过 GET 参数触发）
if (isset($_GET['action']) && $_GET['action'] === 'update_status' && isset($_GET['order_id']) && isset($_GET['status'])) {
    $order_id = intval($_GET['order_id']);
    $new_status = sanitize_text_field($_GET['status']);
    $update_result = $order_controller->update_order_status($order_id, $new_status);
    if ($update_result) {
        echo '<div class="updated"><p>订单状态已更新。</p></div>';
    } else {
        echo '<div class="error"><p>更新订单状态失败。</p></div>';
    }
    // 刷新页面防止重复提交
    echo '<meta http-equiv="refresh" content="2;url=' . esc_url(admin_url('admin.php?page=zhuige-shop-orders')) . '">';
}
?>
<div class="wrap">
    <h1>订单管理</h1>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>订单号</th>
                <th>用户ID</th>
                <th>总额</th>
                <th>状态</th>
                <th>支付方式</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)) : ?>
                <?php foreach ($orders as $order) : ?>
                    <tr>
                        <td><?php echo esc_html($order['order_number']); ?></td>
                        <td><?php echo esc_html($order['user_id']); ?></td>
                        <td><?php echo esc_html($order['total']); ?></td>
                        <td><?php echo esc_html($order['order_status']); ?></td>
                        <td><?php echo esc_html($order['payment_method']); ?></td>
                        <td><?php echo esc_html($order['create_time']); ?></td>
                        <td>
                            <?php if ($order['order_status'] !== 'completed') : ?>
                                <a href="<?php echo admin_url('admin.php?page=zhuige-shop-orders&action=update_status&order_id=' . $order['id'] . '&status=completed'); ?>">标记完成</a>
                            <?php else: ?>
                                <span>已完成</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">暂无订单记录</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
