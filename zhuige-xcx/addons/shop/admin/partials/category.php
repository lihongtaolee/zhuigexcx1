<?php
/**
 * 追格商城分类页面设置
 */

CSF::createSection('zhuige-shop', array(
    'title'  => '分类页面',
    'icon'   => 'fas fa-list',
    'fields' => array(

        array(
            'id'      => 'category_banner',
            'type'    => 'group',
            'title'   => '轮播图',
            'fields'  => array(
                array(
                    'id'      => 'image',
                    'type'    => 'media',
                    'title'   => '图片',
                ),
                array(
                    'id'      => 'link',
                    'type'    => 'text',
                    'title'   => '链接',
                ),
            ),
        ),

        array(
            'id'      => 'category_style',
            'type'    => 'radio',
            'title'   => '分类样式',
            'options' => array(
                'grid'    => '网格',
                'list'    => '列表',
            ),
            'default' => 'grid',
        ),

        array(
            'id'      => 'category_columns',
            'type'    => 'number',
            'title'   => '网格列数',
            'default' => 3,
            'dependency' => array('category_style', '==', 'grid'),
        ),

    )
}));