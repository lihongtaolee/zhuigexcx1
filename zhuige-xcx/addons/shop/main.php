<?php
// 注意：本文件为追格商城功能模块，不包含插件头部信息，
// 需由主插件在适当时机调用本模块的初始化函数。

if ( ! defined( 'ZHUIGE_XCX_ADDONS_DIR' ) ) {
    define( 'ZHUIGE_XCX_ADDONS_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * 初始化追格商城模块
 */
function zhuige_shop_module_init() {

    // 注册 REST API 路由
    add_action( 'rest_api_init', function () {
        register_rest_route( 'zhuige/v1', 'shop/setting/home', [
            'methods'             => 'GET',
            'callback'            => 'zhuige_shop_setting_home',
            'permission_callback' => '__return_true'
        ] );
        register_rest_route( 'zhuige/v1', 'shop/category', [
            'methods'             => 'GET',
            'callback'            => 'zhuige_shop_category',
            'permission_callback' => '__return_true'
        ] );
        register_rest_route( 'zhuige/v1', 'shop/goods/list', [
            'methods'             => 'GET',
            'callback'            => 'zhuige_shop_goods_list',
            'permission_callback' => '__return_true'
        ] );
        register_rest_route( 'zhuige/v1', 'shop/goods/detail', [
            'methods'             => 'GET',
            'callback'            => 'zhuige_shop_goods_detail',
            'permission_callback' => '__return_true'
        ] );
        register_rest_route( 'zhuige/v1', 'shop/goods/search', [
            'methods'             => 'GET',
            'callback'            => 'zhuige_shop_goods_search',
            'permission_callback' => '__return_true'
        ] );
        register_rest_route( 'zhuige/v1', 'shop/cart/add', [
            'methods'             => 'POST',
            'callback'            => 'zhuige_shop_cart_add',
            'permission_callback' => '__return_true'
        ] );
        register_rest_route( 'zhuige/v1', 'shop/cart/list', [
            'methods'             => 'GET',
            'callback'            => 'zhuige_shop_cart_list',
            'permission_callback' => '__return_true'
        ] );
        register_rest_route( 'zhuige/v1', 'shop/cart/clear', [
            'methods'             => 'POST',
            'callback'            => 'zhuige_shop_cart_clear',
            'permission_callback' => '__return_true'
        ] );
        register_rest_route( 'zhuige/v1', 'shop/order/create', [
            'methods'             => 'POST',
            'callback'            => 'zhuige_shop_order_create',
            'permission_callback' => '__return_true'
        ] );
        register_rest_route( 'zhuige/v1', 'shop/order/list', [
            'methods'             => 'GET',
            'callback'            => 'zhuige_shop_order_list',
            'permission_callback' => '__return_true'
        ] );
        register_rest_route( 'zhuige/v1', 'shop/order/detail', [
            'methods'             => 'GET',
            'callback'            => 'zhuige_shop_order_detail',
            'permission_callback' => '__return_true'
        ] );
    } );

    // 注册自定义文章类型（商品）
    add_action( 'init', function() {
        register_post_type( 'jq_goods', [
            'labels' => [
                'name'                  => '商品',
                'singular_name'         => '商品',
                'menu_name'             => '商品管理',
                'add_new'               => '发布商品',
                'add_new_item'          => '发布商品',
                'edit_item'             => '编辑商品',
                'new_item'              => '新商品',
                'view_item'             => '查看商品',
                'search_items'          => '搜索商品',
                'not_found'             => '没有找到商品',
                'not_found_in_trash'    => '回收站中没有商品'
            ],
            'public'       => true,
            'has_archive'  => true,
            'supports'     => [ 'title', 'editor', 'thumbnail' ],
            'show_in_menu' => 'zhuige-shop'
        ] );
        // 注册商品分类
        register_taxonomy( 'jq_goods_cat', 'jq_goods', [
            'labels' => [
                'name'                  => '商品分类',
                'singular_name'         => '商品分类',
                'search_items'          => '搜索商品分类',
                'all_items'             => '所有商品分类',
                'parent_item'           => '上级商品分类',
                'parent_item_colon'     => '上级商品分类：',
                'edit_item'             => '编辑商品分类',
                'update_item'           => '更新商品分类',
                'add_new_item'          => '新增商品分类',
                'new_item_name'         => '新商品分类名称',
                'menu_name'             => '商品分类',
            ],
            'public'       => true,
            'hierarchical' => true,
            'show_in_menu' => true,
        ] );
    } );

    // 注册管理后台菜单
    add_action( 'admin_menu', 'zhuige_shop_admin_menu' );
}

/**
 * 管理菜单注册函数
 */
function zhuige_shop_admin_menu() {
    add_menu_page(
        '追格商城',
        '追格商城',
        'manage_options',
        'zhuige-shop',
        'zhuige_shop_admin_home',
        'dashicons-cart',
        100
    );
    add_submenu_page(
        'zhuige-shop',
        '首页设置',
        '首页设置',
        'manage_options',
        'zhuige-shop',
        'zhuige_shop_admin_home'
    );
    // 自定义文章类型“jq_goods”将自动添加“商品管理”及“发布商品”子菜单（通过 show_in_menu 参数控制），
    // 这里手动添加“商品分类”和“订单管理”子菜单
    add_submenu_page(
        'zhuige-shop',
        '商品分类',
        '商品分类',
        'manage_options',
        'edit-tags.php?taxonomy=jq_goods_cat&post_type=jq_goods',
        ''
    );
    add_submenu_page(
        'zhuige-shop',
        '订单管理',
        '订单管理',
        'manage_options',
        'zhuige-shop-orders',
        'zhuige_shop_admin_orders'
    );
}

/**
 * 首页设置页面回调
 */
function zhuige_shop_admin_home() {
    require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/home.php';
}

/**
 * 订单管理页面回调
 */
function zhuige_shop_admin_orders() {
    require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/orders.php';
}

/**
 * 数据表创建函数
 * （建议由主插件在激活时统一调用本模块的创建表函数）
 */
function zhuige_shop_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // 订单表
    $table_order = $wpdb->prefix . 'zhuige_shop_order';
    $sql = "CREATE TABLE IF NOT EXISTS $table_order (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        order_no varchar(32) NOT NULL,
        user_id bigint(20) NOT NULL,
        goods_id bigint(20) NOT NULL,
        price decimal(10,2) NOT NULL,
        status varchar(32) NOT NULL,
        create_time datetime NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // 幻灯片表
    $table_slides = $wpdb->prefix . 'zhuige_shop_slides';
    $sql .= "CREATE TABLE IF NOT EXISTS $table_slides (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        title varchar(100) NOT NULL,
        image varchar(200) NOT NULL,
        link varchar(200),
        sort int(11) DEFAULT 0,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // 导航表
    $table_nav = $wpdb->prefix . 'zhuige_shop_nav';
    $sql .= "CREATE TABLE IF NOT EXISTS $table_nav (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        title varchar(100) NOT NULL,
        icon varchar(200) NOT NULL,
        link varchar(200),
        sort int(11) DEFAULT 0,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

// 调用模块初始化（如果主插件已有统一加载机制，可注释此行）
zhuige_shop_module_init();
