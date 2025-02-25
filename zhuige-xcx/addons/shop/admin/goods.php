<?php
/**
 * 追格商城 - 商品管理页面
 */
if (!current_user_can('manage_options')) {
    wp_die(__('你没有权限访问此页面。'));
}

$goods_controller = new Zhuige_Shop_Goods_Controller();
$goods_list = $goods_controller->get_goods_list();
?>
<div class="wrap">
    <h1>商品管理</h1>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>商品名称</th>
                <th>售价</th>
                <th>库存</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($goods_list)) : ?>
                <?php foreach ($goods_list as $item) : ?>
                    <tr>
                        <td><?php echo esc_html($item['id']); ?></td>
                        <td><?php echo esc_html($item['title']); ?></td>
                        <td><?php echo esc_html($item['sale_price']); ?></td>
                        <td><?php echo esc_html($item['stock']); ?></td>
                        <td><?php echo esc_html($item['create_time']); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=zhuige-shop-add-goods&edit=' . $item['id']); ?>">编辑</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6">暂无商品</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
