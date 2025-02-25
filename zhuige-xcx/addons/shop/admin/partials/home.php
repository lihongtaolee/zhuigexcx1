<?php
/**
 * 追格商城首页设置
 */

CSF::createSection('zhuige-shop', array(
    'title'  => '首页设置',
    'icon'   => 'fas fa-home',
    'fields' => array(

        array(
            'id'      => 'home_rec',
            'type'    => 'fieldset',
            'title'   => '推荐商品',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '标题',
                    'default' => '推荐商品',
                ),
                array(
                    'id'      => 'goods_ids',
                    'type'    => 'select',
                    'title'   => '选择商品',
                    'chosen'  => true,
                    'multiple'=> true,
                    'sortable'=> true,
                    'ajax'    => true,
                    'options' => 'posts',
                    'query_args' => array(
                        'post_type'  => 'jq_goods',
                        'posts_per_page' => -1
                    ),
                ),
            ),
        ),

        array(
            'id'      => 'home_banner',
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

    )
));