<?php

/**
 * Plugin Name:       追格小程序
 * Plugin URI:        https://www.zhuige.com/
 * Description:       追格小程序是一个积木式小程序搭建框架。
 * Version:           2.5.8
 * Author:            追格
 * Author URI:        https://www.zhuige.com/
 * Text Domain:       zhuige-xcx
 */

if (!defined('WPINC')) {
    die;
}

define('ZHUIGE_XCX', 'zhuige-xcx');
define('ZHUIGE_XCX_VERSION', '2.5.8');
define('ZHUIGE_XCX_BASE_NAME', plugin_basename(__FILE__));
define('ZHUIGE_XCX_BASE_DIR', plugin_dir_path(__FILE__));
define('ZHUIGE_XCX_BASE_URL', plugin_dir_url(__FILE__));
define('ZHUIGE_XCX_ADDONS_DIR', ZHUIGE_XCX_BASE_DIR . 'addons/');

function activate_zhuige_xcx()
{
    require_once ZHUIGE_XCX_BASE_DIR . 'includes/class-zhuige-xcx-activator.php';
    ZhuiGe_Xcx_Activator::activate();

    // 在插件激活时创建身高预测表（从sgtest模块移过来的函数调用）
    create_height_predictions_table(); 

    // 调用 sgycai 模块激活逻辑，确保执行 SQL 文件中的建表命令
    require_once ZHUIGE_XCX_ADDONS_DIR . 'sgtool/sgycai/sgycai.php';
    ZhuiGe_Xcx_Sgycai::activate();

    // 调用 sgztmk 模块激活逻辑，创建专题模块配置表
    require_once ZHUIGE_XCX_ADDONS_DIR . 'sgtool/sgztmk/sgztmk.php';
    if (class_exists('ZhuiGe_Xcx_Sgztmk')) {
        ZhuiGe_Xcx_Sgztmk::activate();
    }
}

function deactivate_zhuige_xcx()
{
    require_once ZHUIGE_XCX_BASE_DIR . 'includes/class-zhuige-xcx-deactivator.php';
    ZhuiGe_Xcx_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_zhuige_xcx');
register_deactivation_hook(__FILE__, 'deactivate_zhuige_xcx');

// 插件卸载时执行的操作 (可选，但谨慎使用)
register_uninstall_hook(__FILE__, 'uninstall_zhuige_xcx');
function uninstall_zhuige_xcx() {
    // **请谨慎操作，卸载钩子是不可逆的，数据会丢失！**
    global $wpdb;
    $table_name = $wpdb->prefix . 'height_predictions';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

function zhuige_xcx_action_links($actions)
{
    $actions[] = '<a href="admin.php?page=zhuige-xcx">设置</a>';
    $actions[] = '<a href="https://zhuige.com/bbs/forum/1.html" target="_blank">技术支持</a>';
    return $actions;
}
add_filter('plugin_action_links_' . ZHUIGE_XCX_BASE_NAME, 'zhuige_xcx_action_links');

// 用户登录检查
add_filter('rest_authentication_errors', function ($error) {
    if (!empty($error)) {
        return $error;
    }

    if (in_array($_SERVER['REQUEST_URI'], ZhuiGe_Xcx::$require_login_uris)) {
        if (!is_user_logged_in()) {
            return new WP_Error('user_not_login', '用户未登录', '');
        }
    }

    return true;
});

require ZHUIGE_XCX_BASE_DIR . 'includes/class-zhuige-xcx.php';
require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-market.php';
require ZHUIGE_XCX_BASE_DIR . 'includes/class-zhuige-xcx-addon.php';
ZhuiGe_Xcx_Addon::load();

// 包含 sgtest.php 文件 (身高预测模块)
require_once ZHUIGE_XCX_ADDONS_DIR . 'sgtool/sgtest/sgtest.php'; // 确保包含 sgtest.php

// 包含 sgycai.php 文件 (身高预测AI模块)
require_once ZHUIGE_XCX_ADDONS_DIR . 'sgtool/sgycai/sgycai.php';

require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-xcx-function.php';
require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-xcx-user-column.php';
require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-xcx-dashboard.php';
require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-xcx-plugins.php';

// 引入专题模块配置
require_once ZHUIGE_XCX_ADDONS_DIR . 'sgtool/sgztmk/sgztmk.php';

foreach (ZhuiGe_Xcx_Addon::$post_types as $post_type) {
    $file_path = ZHUIGE_XCX_ADDONS_DIR . $post_type;
    if (file_exists($file_path)) {
        require_once($file_path);
    }
}

foreach (ZhuiGe_Xcx_Addon::$cruds as $crud) {
    $file_path = ZHUIGE_XCX_ADDONS_DIR . $crud;
    if (file_exists($file_path)) {
        require_once($file_path);
    }
}

require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-xcx-user-property.php';
foreach (ZhuiGe_Xcx_Addon::$users as $user) {
    $file_path = ZHUIGE_XCX_ADDONS_DIR . $user;
    if (file_exists($file_path)) {
        require_once($file_path);
    }
}

// *** 新增：加载追格商城模块（shop 模块） ***
require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/main.php';
if (function_exists('zhuige_shop_module_init')) {
    zhuige_shop_module_init();
    error_log("追格商城模块已初始化。");
} else {
    error_log("追格商城模块加载失败：函数 zhuige_shop_module_init 不存在。");
}

function run_zhuige_xcx()
{
    $plugin = new ZhuiGe_Xcx();
    $plugin->run();
}
run_zhuige_xcx();
