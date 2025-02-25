<?php
/**
 * 追格商城 - 首页设置管理页面
 */
if (!current_user_can('manage_options')) {
    wp_die(__('你没有权限访问此页面。'));
}

// 实例化设置控制器
$setting_controller = new Zhuige_Shop_Setting_Controller();

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('zhuige_shop_home_settings')) {
    $slider = isset($_POST['zhuige_shop_slider']) ? sanitize_text_field($_POST['zhuige_shop_slider']) : '';
    $banner = isset($_POST['zhuige_shop_banner']) ? sanitize_text_field($_POST['zhuige_shop_banner']) : '';
    $res1 = $setting_controller->update_setting('zhuige_shop_slider', $slider);
    $res2 = $setting_controller->update_setting('zhuige_shop_banner', $banner);
    if ($res1 && $res2) {
        echo '<div class="updated"><p>设置保存成功。</p></div>';
    } else {
        echo '<div class="error"><p>保存设置失败，请重试。</p></div>';
    }
}

// 获取当前设置
$current_slider = $setting_controller->get_setting('zhuige_shop_slider');
$current_banner = $setting_controller->get_setting('zhuige_shop_banner');
?>
<div class="wrap">
    <h1>首页设置</h1>
    <form method="post" action="">
        <?php wp_nonce_field('zhuige_shop_home_settings'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="zhuige_shop_slider">幻灯片图片</label></th>
                <td>
                    <input type="text" id="zhuige_shop_slider" name="zhuige_shop_slider" value="<?php echo esc_attr($current_slider); ?>" class="regular-text" />
                    <p class="description">请输入幻灯片图片 URL，多张用逗号分隔。</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="zhuige_shop_banner">首页横幅</label></th>
                <td>
                    <input type="text" id="zhuige_shop_banner" name="zhuige_shop_banner" value="<?php echo esc_attr($current_banner); ?>" class="regular-text" />
                    <p class="description">请输入首页横幅图片 URL。</p>
                </td>
            </tr>
        </table>
        <?php submit_button('保存设置'); ?>
    </form>
</div>
