<?php
/**
 * 追格商城全局设置
 */

CSF::createSection('zhuige-shop', array(
    'title'  => '全局设置',
    'icon'   => 'fas fa-cog',
    'fields' => array(

        array(
            'id'      => 'basic',
            'type'    => 'fieldset',
            'title'   => '基础设置',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '商城名称',
                    'default' => '追格商城',
                ),
                array(
                    'id'      => 'description',
                    'type'    => 'textarea',
                    'title'   => '商城描述',
                    'default' => '追格商城小程序',
                ),
            ),
        ),

        array(
            'id'      => 'share',
            'type'    => 'fieldset',
            'title'   => '分享设置',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '分享标题',
                    'default' => '追格商城',
                ),
                array(
                    'id'      => 'image',
                    'type'    => 'media',
                    'title'   => '分享图片',
                ),
            ),
        ),

        array(
            'id'      => 'goods',
            'type'    => 'fieldset',
            'title'   => '商品设置',
            'fields'  => array(
                array(
                    'id'      => 'list_style',
                    'type'    => 'radio',
                    'title'   => '商品列表样式',
                    'options' => array(
                        'grid' => '网格',
                        'list' => '列表',
                    ),
                    'default' => 'grid',
                ),
                array(
                    'id'      => 'columns',
                    'type'    => 'number',
                    'title'   => '商品列表列数',
                    'default' => 2,
                ),
                array(
                    'id'      => 'show_price',
                    'type'    => 'switcher',
                    'title'   => '显示价格',
                    'default' => '1',
                ),
                array(
                    'id'      => 'show_sales',
                    'type'    => 'switcher',
                    'title'   => '显示销量',
                    'default' => '1',
                ),
            ),
        ),

    )
));