<?php
/**
 * 追格商城模块主文件
 * 本模块作为追格小程序 WP 系统插件的一个功能模块加载，负责初始化数据表、
 * 注册后台菜单、加载 API 控制器及后台页面。
 */
if (!defined('ABSPATH')) exit;

require_once dirname(__FILE__) . '/config.php';

// 加载 API 控制器类
require_once dirname(__FILE__) . '/api/class-zhuige-shop-base-controller.php';
require_once dirname(__FILE__) . '/api/class-zhuige-shop-goods-controller.php';
require_once dirname(__FILE__) . '/api/class-zhuige-shop-order-controller.php';
require_once dirname(__FILE__) . '/api/class-zhuige-shop-setting-controller.php';

/**
 * 初始化商城模块：自动检测创建数据表
 */
function zhuige_shop_module_setup() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // 定义表名
    $table_order = $wpdb->prefix . "zhuige_shop_order";
    $table_goods = $wpdb->prefix . "zhuige_shop_goods";
    $table_category = $wpdb->prefix . "zhuige_shop_category";

    // 订单表：增加订单状态、支付方式、更新时间字段
    $sql_order = "CREATE TABLE IF NOT EXISTS $table_order (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        order_number varchar(50) NOT NULL,
        user_id bigint(20) NOT NULL,
        total decimal(10,2) NOT NULL,
        order_status varchar(20) NOT NULL DEFAULT 'pending',
        payment_method varchar(50) DEFAULT '',
        create_time datetime DEFAULT CURRENT_TIMESTAMP,
        update_time datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY order_number (order_number)
    ) $charset_collate;";

    // 商品表：包含描述、库存、图片、角标及更新时间字段
    $sql_goods = "CREATE TABLE IF NOT EXISTS $table_goods (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        description text,
        sale_price decimal(10,2) NOT NULL,
        original_price decimal(10,2) DEFAULT 0,
        stock int(11) DEFAULT 0,
        header_image varchar(255),
        badge varchar(50),
        create_time datetime DEFAULT CURRENT_TIMESTAMP,
        update_time datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY title (title)
    ) $charset_collate;";

    // 分类表：用于商品分类管理
    $sql_category = "CREATE TABLE IF NOT EXISTS $table_category (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        description text,
        create_time datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY name (name)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_order);
    dbDelta($sql_goods);
    dbDelta($sql_category);
}
add_action('init', 'zhuige_shop_module_setup');

/**
 * 注册后台管理菜单
 */
function zhuige_shop_register_menus() {
    // 一级菜单“追格商城”，默认指向首页设置页面
    add_menu_page('追格商城', '追格商城', 'manage_options', 'zhuige-shop', 'zhuige_shop_render_home_page', 'dashicons-store', 5);
    add_submenu_page('zhuige-shop', '首页设置', '首页设置', 'manage_options', 'zhuige-shop-home', 'zhuige_shop_render_home_page');
    add_submenu_page('zhuige-shop', '商品管理', '商品管理', 'manage_options', 'zhuige-shop-goods', 'zhuige_shop_render_goods_page');
    add_submenu_page('zhuige-shop', '发布商品', '发布商品', 'manage_options', 'zhuige-shop-add-goods', 'zhuige_shop_render_add_goods_page');
    add_submenu_page('zhuige-shop', '商品分类', '商品分类', 'manage_options', 'zhuige-shop-category', 'zhuige_shop_render_category_page');
    add_submenu_page('zhuige-shop', '订单管理', '订单管理', 'manage_options', 'zhuige-shop-orders', 'zhuige_shop_render_orders_page');
}
add_action('admin_menu', 'zhuige_shop_register_menus');

/**
 * 后台页面回调函数：加载对应管理页面
 */
function zhuige_shop_render_home_page() {
    include dirname(__FILE__) . '/admin/home.php';
}
function zhuige_shop_render_goods_page() {
    include dirname(__FILE__) . '/admin/goods.php';
}
function zhuige_shop_render_add_goods_page() {
    include dirname(__FILE__) . '/admin/add_goods.php';
}
function zhuige_shop_render_category_page() {
    include dirname(__FILE__) . '/admin/category.php';
}
function zhuige_shop_render_orders_page() {
    include dirname(__FILE__) . '/admin/orders.php';
}
?>
