<?php
/**
 * 追格商城 - 商品分类管理页面
 */
if (!current_user_can('manage_options')) {
    wp_die(__('你没有权限访问此页面。'));
}
global $wpdb;
$table_category = $wpdb->prefix . "zhuige_shop_category";

// 处理新分类提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('zhuige_shop_category_settings')) {
    $cat_name = isset($_POST['category_name']) ? sanitize_text_field($_POST['category_name']) : '';
    $cat_desc = isset($_POST['category_description']) ? sanitize_textarea_field($_POST['category_description']) : '';
    if (!empty($cat_name)) {
        $wpdb->insert($table_category, array(
            'name' => $cat_name,
            'description' => $cat_desc,
            'create_time' => current_time('mysql')
        ));
        echo '<div class="updated"><p>分类添加成功。</p></div>';
    } else {
        echo '<div class="error"><p>分类名称不能为空。</p></div>';
    }
}

// 获取所有分类
$categories = $wpdb->get_results("SELECT * FROM $table_category ORDER BY create_time DESC", ARRAY_A);
?>
<div class="wrap">
    <h1>商品分类管理</h1>
    <h2>添加新分类</h2>
    <form method="post" action="">
        <?php wp_nonce_field('zhuige_shop_category_settings'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="category_name">分类名称</label></th>
                <td><input type="text" id="category_name" name="category_name" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="category_description">描述</label></th>
                <td><textarea id="category_description" name="category_description" rows="3" class="large-text"></textarea></td>
            </tr>
        </table>
        <?php submit_button('添加分类'); ?>
    </form>

    <h2>已添加分类</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>分类名称</th>
                <th>描述</th>
                <th>创建时间</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)) : ?>
                <?php foreach ($categories as $cat) : ?>
                    <tr>
                        <td><?php echo esc_html($cat['id']); ?></td>
                        <td><?php echo esc_html($cat['name']); ?></td>
                        <td><?php echo esc_html($cat['description']); ?></td>
                        <td><?php echo esc_html($cat['create_time']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">暂无分类</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
