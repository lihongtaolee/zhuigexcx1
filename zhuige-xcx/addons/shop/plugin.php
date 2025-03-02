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
}

// 引入商品类型定义
require_once plugin_dir_path( __FILE__ ) . 'inc/class-zhuige-shop-post_types.php';

class ShopModule {
    private $post_types;
    
    public function __construct() {
        error_log( "【商城模块】ShopModule 初始化" );
        
        // 初始化商品类型
        $this->post_types = new ZhuiGe_Shop_Post_Types();
        add_action('init', array($this->post_types, 'create_custom_post_type'));
        
        // 加载REST API
        if (file_exists(plugin_dir_path(__FILE__) . 'rest/class-zhuige-shop-rest.php')) {
            require_once plugin_dir_path(__FILE__) . 'rest/class-zhuige-shop-rest.php';
            new ZhuiGe_Shop_Rest();
        }
    }
}

return new ShopModule();
