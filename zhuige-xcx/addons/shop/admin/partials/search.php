<?php
/**
 * 追格商城搜索页面设置
 */

CSF::createSection('zhuige-shop', array(
    'title'  => '搜索页面',
    'icon'   => 'fas fa-search',
    'fields' => array(

        array(
            'id'      => 'search_banner',
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
            'id'      => 'search_placeholder',
            'type'    => 'text',
            'title'   => '搜索框提示文字',
            'default' => '搜索商品',
        ),

        array(
            'id'      => 'search_hot',
            'type'    => 'group',
            'title'   => '热门搜索',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '关键词',
                ),
            ),
        ),

    )
}));