<?php
// 文件路径：zhuige-xcx/addons/shop/plugin.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // 防止直接访问
}

// 引入 Codestar Framework
if ( ! class_exists( 'CSF' ) ) {
    require_once plugin_dir_path( __FILE__ ) . '../../../codestar-framework-master/codestar-framework.php';
}

error_log( "【商城模块】plugin.php 已加载" );

// 在后台加载 CSF 配置页面，CSF 将自动注册选项页面和菜单
if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/home.php';
}

class ShopModule {
    public function __construct() {
        error_log( "【商城模块】ShopModule 初始化" );
        // 其他初始化代码，如果需要的话
    }
}

return new ShopModule();
