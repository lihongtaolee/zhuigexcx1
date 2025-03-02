<?php

/**
 * 追格小程序 - 商城模块
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

//
// 订单管理
//
CSF::createSection($prefix, array(
    'id' => 'order',
    'title' => '订单设置',
    'icon'  => 'fas fa-shopping-cart',
    'fields' => array(

        array(
            'id'    => 'switch_auto_confirm',
            'type'  => 'switcher',
            'title' => '自动确认',
            'subtitle' => '是否开启自动确认收货',
            'default' => ''
        ),

        array(
            'id'      => 'auto_confirm_days',
            'type'    => 'number',
            'title'   => '自动确认天数',
            'subtitle' => '订单发货后多少天自动确认收货',
            'default' => '7',
            'dependency' => array('switch_auto_confirm', '==', '1'),
        ),

        array(
            'id'    => 'switch_auto_cancel',
            'type'  => 'switcher',
            'title' => '自动取消',
            'subtitle' => '是否开启未支付订单自动取消',
            'default' => '1'
        ),

        array(
            'id'      => 'auto_cancel_minutes',
            'type'    => 'number',
            'title'   => '自动取消时间',
            'subtitle' => '订单创建后多少分钟未支付自动取消',
            'default' => '30',
            'dependency' => array('switch_auto_cancel', '==', '1'),
        ),

    )
));