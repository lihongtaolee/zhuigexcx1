<?php

/**
 * 追格小程序 - 商城模块
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

// 分类设置
CSF::createSection($prefix, array(
    'id'    => 'category',
    'title' => '分类设置',
    'icon'  => 'fas fa-coins',
    'fields' => array(

        array(
            'id'          => 'category_cat',
            'type'        => 'select',
            'title'       => '导航设置',
            'placeholder' => '选择分类',
            'chosen'      => true,
            'multiple'    => true,
            'sortable'    => true,
            'options'     => 'categories',
            'query_args'  => array(
                'taxonomy'  => 'jq_goods_cat',
                'parent' => 0
            ),
        ),

        array(
            'id'      => 'category_sub_count',
            'type'    => 'number',
            'title'   => '二级分类商品个数',
            'default' => 4,
        ),

    )
));