<?php
// 文件路径：zhuige-xcx/addons/shop/plugin.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // 防止直接访问
}

// 定义常量，表明商品属性元数据面板使用手动创建方式，不使用CSF框架
if (!defined('ZHUIGE_DISABLE_CSF_GOODS_METABOX')) {
    define('ZHUIGE_DISABLE_CSF_GOODS_METABOX', true);
}

// 引入 Codestar Framework
if ( ! class_exists( 'CSF' ) ) {
    require_once plugin_dir_path( __FILE__ ) . '../../admin/codestar-framework/codestar-framework.php';
}

error_log( "【商城模块】plugin.php 已加载" );

// 在后台加载 CSF 配置页面，CSF 将自动注册选项页面和菜单
if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/home.php';
    require_once plugin_dir_path( __FILE__ ) . 'admin/goods.php'; // 注意：此文件中已移除CSF创建商品元数据面板的代码
    require_once plugin_dir_path( __FILE__ ) . 'inc/zhuige-shop-user-order.php';
} else {
    // 在前台也需要加载home.php，确保REST API接口注册
    require_once plugin_dir_path( __FILE__ ) . 'admin/home.php';
}

// 引入商品类型定义
require_once plugin_dir_path( __FILE__ ) . 'inc/class-zhuige-shop-post_types.php';

// 引入管理类
require_once plugin_dir_path( __FILE__ ) . 'admin/class-shop-admin.php';

class ShopModule {
    private $post_types;
    private $admin;
    
    public function __construct() {
        error_log( "【商城模块】ShopModule 初始化" );
        
        // 初始化商品类型
        $this->post_types = new ZhuiGe_Shop_Post_Types();
        add_action('init', array($this->post_types, 'create_custom_post_type'), 5); // 提前注册商品类型
        
        // 初始化管理类
        if (is_admin()) {
            $this->admin = new Shop_Admin('zhuige-shop', '1.0.0');
            add_action('admin_enqueue_scripts', array($this->admin, 'enqueue_styles'));
            add_action('admin_enqueue_scripts', array($this->admin, 'enqueue_scripts'));
            add_action('admin_menu', array($this->admin, 'create_menu'));
            add_action('admin_menu', array($this->admin, 'admin_menu'), 20);
            
            // 注意：商品属性元数据面板已在 Shop_Admin 构造函数中注册，使用手动创建方式
        }
        
        // 加载REST API
        if (file_exists(plugin_dir_path(__FILE__) . 'rest/class-zhuige-shop-rest.php')) {
            require_once plugin_dir_path(__FILE__) . 'rest/class-zhuige-shop-rest.php';
            new ZhuiGe_Shop_Rest();
        }
    }
}

error_log( "【商城模块】shop/plugin.php 已成功加载" );

return new ShopModule();
