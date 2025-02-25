<?php
/*
 * 追格商城小程序
 * Author: 追格
 * Help document: https://www.zhuige.com/product/sc.html
 * github: https://github.com/zhuige-com/zhuige_shop
 * gitee: https://gitee.com/zhuige_com/zhuige_shop
 * License：GPL-2.0
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

if (!defined('ABSPATH')) {
	exit;
}

// 注册商城模块
function zhuige_shop_register() {
	// 注册路由
	add_action('rest_api_init', function () {
		// 首页接口
		register_rest_route('zhuige/v1', 'shop/setting/home', [
			'methods' => 'GET',
			'callback' => 'zhuige_shop_setting_home',
			'permission_callback' => '__return_true'
		]);

		// 商品分类接口
		register_rest_route('zhuige/v1', 'shop/category', [
			'methods' => 'GET',
			'callback' => 'zhuige_shop_category',
			'permission_callback' => '__return_true'
		]);

		// 商品列表接口
		register_rest_route('zhuige/v1', 'shop/goods/list', [
			'methods' => 'GET',
			'callback' => 'zhuige_shop_goods_list',
			'permission_callback' => '__return_true'
		]);

		// 商品详情接口
		register_rest_route('zhuige/v1', 'shop/goods/detail', [
			'methods' => 'GET',
			'callback' => 'zhuige_shop_goods_detail',
			'permission_callback' => '__return_true'
		]);

		// 商品搜索接口
		register_rest_route('zhuige/v1', 'shop/goods/search', [
			'methods' => 'GET',
			'callback' => 'zhuige_shop_goods_search',
			'permission_callback' => '__return_true'
		]);

		// 购物车相关接口
		register_rest_route('zhuige/v1', 'shop/cart/add', [
			'methods' => 'POST',
			'callback' => 'zhuige_shop_cart_add',
			'permission_callback' => '__return_true'
		]);

		register_rest_route('zhuige/v1', 'shop/cart/list', [
			'methods' => 'GET',
			'callback' => 'zhuige_shop_cart_list',
			'permission_callback' => '__return_true'
		]);

		register_rest_route('zhuige/v1', 'shop/cart/clear', [
			'methods' => 'POST',
			'callback' => 'zhuige_shop_cart_clear',
			'permission_callback' => '__return_true'
		]);

		// 订单相关接口
		register_rest_route('zhuige/v1', 'shop/order/create', [
			'methods' => 'POST',
			'callback' => 'zhuige_shop_order_create',
			'permission_callback' => '__return_true'
		]);

		register_rest_route('zhuige/v1', 'shop/order/list', [
			'methods' => 'GET',
			'callback' => 'zhuige_shop_order_list',
			'permission_callback' => '__return_true'
		]);

		register_rest_route('zhuige/v1', 'shop/order/detail', [
			'methods' => 'GET',
			'callback' => 'zhuige_shop_order_detail',
			'permission_callback' => '__return_true'
		]);
	});

	// 注册商品自定义文章类型
	register_post_type('jq_goods', [
		'labels' => [
			'name' => '商品',
			'singular_name' => '商品'
		],
		'public' => true,
		'has_archive' => true,
		'supports' => ['title', 'editor', 'thumbnail']
	]);

	// 注册商品分类
	register_taxonomy('jq_goods_cat', 'jq_goods', [
		'labels' => [
			'name' => '商品分类',
			'singular_name' => '商品分类'
		],
		'public' => true,
		'hierarchical' => true
	]);
}
add_action('init', 'zhuige_shop_register');

// 注册管理后台菜单
function zhuige_shop_admin_menu() {
    add_menu_page(
        '追格商城Free', 
        '追格商城Free',
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

    add_submenu_page(
        'zhuige-shop',
        '商品管理',
        '商品管理',
        'manage_options',
        'edit.php?post_type=jq_goods',
        ''
    );

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
add_action('admin_menu', 'zhuige_shop_admin_menu');

// 首页设置页面回调
function zhuige_shop_admin_home() {
    require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/home.php';
}

// 订单管理页面回调
function zhuige_shop_admin_orders() {
    require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/orders.php';
}

// 激活时创建必要的数据表
function zhuige_shop_activate() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    // 订单表
    $table_name = $wpdb->prefix . 'zhuige_shop_order';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
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

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'zhuige_shop_activate');