<?php
// 文件路径：zhuige-xcx/addons/shop/plugin.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // 防止直接访问
}

// 引入 Codestar Framework
if ( ! class_exists( 'CSF' ) ) {
    require_once plugin_dir_path( __FILE__ ) . '../../admin/codestar-framework/codestar-framework.php';
}

error_log( "【商城模块】plugin.php 已加载" );

// 在后台加载 CSF 配置页面，CSF 将自动注册选项页面和菜单
if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/home.php';
    require_once plugin_dir_path( __FILE__ ) . 'admin/goods.php';
    require_once plugin_dir_path( __FILE__ ) . 'inc/zhuige-shop-user-order.php';
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
            add_action('admin_init', array($this->admin, 'admin_init'));
            add_action('admin_menu', array($this->admin, 'admin_menu'), 20);
            
            // 确保商品属性设置在商品类型注册后执行
            add_action('init', array($this->admin, 'setup_goods_metabox'), 20);
        }
        
        // 加载REST API
        if (file_exists(plugin_dir_path(__FILE__) . 'rest/class-zhuige-shop-rest.php')) {
            require_once plugin_dir_path(__FILE__) . 'rest/class-zhuige-shop-rest.php';
            new ZhuiGe_Shop_Rest();
        }
    }
}

return new ShopModule();
