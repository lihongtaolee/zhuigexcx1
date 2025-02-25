<?php
/**
 * 追格商城 - 发布/编辑商品页面
 */
if (!current_user_can('manage_options')) {
    wp_die(__('你没有权限访问此页面。'));
}

$goods_controller = new Zhuige_Shop_Goods_Controller();
$editing = false;
$product = array(
    'title'           => '',
    'description'     => '',
    'sale_price'      => '',
    'original_price'  => '',
    'stock'           => '',
    'header_image'    => '',
    'badge'           => ''
);

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editing = true;
    $product_id = intval($_GET['edit']);
    $product_data = $goods_controller->get_goods_by_id($product_id);
    if ($product_data) {
        $product = (array)$product_data;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('zhuige_shop_save_product', 'zhuige_shop_nonce')) {
    $data = array(
        'title' => sanitize_text_field($_POST['product_title']),
        'description' => sanitize_textarea_field($_POST['product_description']),
        'sale_price' => floatval($_POST['product_sale_price']),
        'original_price' => floatval($_POST['product_original_price']),
        'stock' => intval($_POST['product_stock']),
        'header_image' => sanitize_text_field($_POST['product_image']),
        'badge' => sanitize_text_field($_POST['product_badge']),
        'create_time' => current_time('mysql')
    );
    if ($editing) {
        $result = $goods_controller->save_goods($data, $product_id);
        if ($result !== false) {
            echo '<div class="updated"><p>商品更新成功。</p></div>';
        } else {
            echo '<div class="error"><p>更新失败，请重试。</p></div>';
        }
    } else {
        $result = $goods_controller->save_goods($data);
        if ($result) {
            echo '<div class="updated"><p>商品发布成功。</p></div>';
        } else {
            echo '<div class="error"><p>发布失败，请重试。</p></div>';
        }
    }
    // 如果是编辑，刷新数据
    if ($editing) {
        $product_data = $goods_controller->get_goods_by_id($product_id);
        if ($product_data) {
            $product = (array)$product_data;
        }
    }
}
?>
<div class="wrap">
    <h1><?php echo $editing ? '编辑商品' : '发布商品'; ?></h1>
    <form method="post" action="">
        <?php wp_nonce_field('zhuige_shop_save_product', 'zhuige_shop_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="product_title">商品名称</label></th>
                <td><input type="text" name="product_title" id="product_title" value="<?php echo esc_attr($product['title']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="product_description">商品描述</label></th>
                <td>
                    <textarea name="product_description" id="product_description" rows="5" class="large-text"><?php echo esc_textarea($product['description']); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="product_sale_price">售价</label></th>
                <td><input type="number" step="0.01" name="product_sale_price" id="product_sale_price" value="<?php echo esc_attr($product['sale_price']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="product_original_price">原价</label></th>
                <td><input type="number" step="0.01" name="product_original_price" id="product_original_price" value="<?php echo esc_attr($product['original_price']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="product_stock">库存</label></th>
                <td><input type="number" name="product_stock" id="product_stock" value="<?php echo esc_attr($product['stock']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="product_image">头图</label></th>
                <td>
                    <input type="text" name="product_image" id="product_image" value="<?php echo esc_attr($product['header_image']); ?>" class="regular-text">
                    <p class="description">请输入图片 URL。</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="product_badge">角标</label></th>
                <td><input type="text" name="product_badge" id="product_badge" value="<?php echo esc_attr($product['badge']); ?>" class="regular-text"></td>
            </tr>
        </table>
        <?php submit_button($editing ? '更新商品' : '发布商品'); ?>
    </form>
</div>
