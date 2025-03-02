<?php

/**
 * 追格小程序 - 商城模块
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Shop_Rest {
    public function __construct() {
        // 加载商品、订单和设置控制器
        require_once dirname(__FILE__) . '/class-shop-goods-controller.php';
        require_once dirname(__FILE__) . '/class-shop-order-controller.php';
        require_once dirname(__FILE__) . '/class-shop-setting-controller.php';

        // 注册REST API路由
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        // 实例化控制器
        $goods_controller = new Shop_Goods_Controller();
        $order_controller = new Shop_Order_Controller();
        $setting_controller = new Shop_Setting_Controller();

        // 注册商品相关路由
        register_rest_route('zhuige/shop', '/goods/(?P<action>[a-zA-Z_]+)', array(
            'methods' => 'GET',
            'callback' => array($goods_controller, 'handle_request'),
            'permission_callback' => '__return_true'
        ));

        // 注册订单相关路由
        register_rest_route('zhuige/shop', '/order/(?P<action>[a-zA-Z_]+)', array(
            'methods' => 'GET',
            'callback' => array($order_controller, 'handle_request'),
            'permission_callback' => '__return_true'
        ));

        // 注册设置相关路由
        register_rest_route('zhuige/shop', '/setting/(?P<action>[a-zA-Z_]+)', array(
            'methods' => 'GET',
            'callback' => array($setting_controller, 'handle_request'),
            'permission_callback' => '__return_true'
        ));
    }
}