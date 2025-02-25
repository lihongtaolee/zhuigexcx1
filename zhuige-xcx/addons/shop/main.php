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
		register_rest_route('zhuige/v1', 'shop/home', [
			'methods' => 'GET',
			'callback' => 'zhuige_shop_home',
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
        '追格商城', 
        '追格商城',
        'manage_options',
        'zhuige-shop',
        'zhuige_shop_admin_page',
        'dashicons-cart',
        100
    );
}
add_action('admin_menu', 'zhuige_shop_admin_menu');

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