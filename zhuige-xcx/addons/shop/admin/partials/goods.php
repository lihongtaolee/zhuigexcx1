<?php
/**
 * 追格商城商品设置
 */

CSF::createSection('zhuige-shop', array(
    'title'  => '商品设置',
    'icon'   => 'fas fa-shopping-cart',
    'fields' => array(

        array(
            'id'      => 'goods_list_style',
            'type'    => 'radio',
            'title'   => '商品列表样式',
            'options' => array(
                'grid'    => '网格',
                'list'    => '列表',
            ),
            'default' => 'grid',
        ),

        array(
            'id'      => 'goods_columns',
            'type'    => 'number',
            'title'   => '商品列表列数',
            'default' => 2,
            'dependency' => array('goods_list_style', '==', 'grid'),
        ),

        array(
            'id'      => 'goods_show_price',
            'type'    => 'switcher',
            'title'   => '显示价格',
            'default' => true,
        ),

        array(
            'id'      => 'goods_show_sales',
            'type'    => 'switcher',
            'title'   => '显示销量',
            'default' => true,
        ),

    )
));