<?php
/*
 * 追格商城小程序配置文件
 * Author: 追格
 * Help document: https://www.zhuige.com/docs/sc.html
 */

if (!defined('ABSPATH')) {
	exit;
}

// 数据库表前缀
define('ZHUIGE_SHOP_TABLE_PREFIX', 'zhuige_shop_');

// 商品自定义文章类型
define('ZHUIGE_SHOP_POST_TYPE_GOODS', 'jq_goods');

// 商品分类自定义分类法
define('ZHUIGE_SHOP_TAXONOMY_GOODS_CAT', 'jq_goods_cat');

// API接口前缀
define('ZHUIGE_SHOP_API_PREFIX', 'shop_');

// 商城相关页面路由
define('ZHUIGE_SHOP_PAGE_HOME', '/pages/shop/index');
define('ZHUIGE_SHOP_PAGE_DETAIL', '/pages/shop/detail');
define('ZHUIGE_SHOP_PAGE_CATEGORY', '/pages/shop/category');