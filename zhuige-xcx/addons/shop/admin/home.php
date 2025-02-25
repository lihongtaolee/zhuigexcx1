<?php
/*
 * 追格商城小程序
 * Author: 追格
 * Help document: https://www.zhuige.com/product/sc.html
 * github: https://github.com/zhuige-com/zhuige_shop
 * gitee: https://gitee.com/zhuige_com/zhuige_shop
 * License: GPL-2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 如果 Codestar Framework (CSF) 未加载，则提示错误信息
if ( ! class_exists( 'CSF' ) ) {
    echo '<div class="notice notice-error"><p>Codestar Framework (CSF) 未加载，请先安装或激活该插件。</p></div>';
    return;
}

CSF::createSection( 'zhuige-shop', array(
    'title'  => '首页设置',
    'icon'   => 'fas fa-home',
    'fields' => array(
        array(
            'id'      => 'background',
            'type'    => 'media',
            'title'   => '背景图片',
            'library' => 'image',
            'desc'    => '建议尺寸750x350'
        ),
        array(
            'id'     => 'slides',
            'type'   => 'group',
            'title'  => '幻灯片',
            'fields' => array(
                array(
                    'id'      => 'image',
                    'type'    => 'media',
                    'title'   => '图片',
                    'library' => 'image',
                    'desc'    => '建议尺寸750x350'
                ),
                array(
                    'id'    => 'title',
                    'type'  => 'text',
                    'title' => '标题'
                ),
                array(
                    'id'    => 'link',
                    'type'  => 'text',
                    'title' => '链接'
                ),
            )
        ),
        array(
            'id'     => 'icon_navs',
            'type'   => 'group',
            'title'  => '导航菜单',
            'fields' => array(
                array(
                    'id'      => 'image',
                    'type'    => 'media',
                    'title'   => '图标',
                    'library' => 'image',
                    'desc'    => '建议尺寸100x100'
                ),
                array(
                    'id'    => 'title',
                    'type'  => 'text',
                    'title' => '标题'
                ),
                array(
                    'id'    => 'link',
                    'type'  => 'text',
                    'title' => '链接'
                ),
            )
        ),
        array(
            'id'     => 'home_rec',
            'type'   => 'fieldset',
            'title'  => '推荐商品',
            'fields' => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),
                array(
                    'id'         => 'goods_ids',
                    'type'       => 'select',
                    'title'      => '选择商品',
                    'chosen'     => true,
                    'multiple'   => true,
                    'sortable'   => true,
                    'ajax'       => true,
                    'options'    => 'posts',
                    'query_args' => array(
                        'post_type' => 'jq_goods',
                    ),
                    'placeholder' => '请选择商品'
                ),
                array(
                    'id'       => 'switch',
                    'type'     => 'switcher',
                    'title'    => '开启/停用',
                    'subtitle' => '是否显示活动区域',
                    'default'  => '1'
                ),
            )
        ),
        array(
            'id'         => 'goods_cat',
            'type'       => 'select',
            'title'      => '导航设置',
            'placeholder'=> '选择分类',
            'chosen'     => true,
            'multiple'   => true,
            'sortable'   => true,
            'options'    => 'categories',
            'query_args' => array(
                'taxonomy' => 'jq_goods_cat'
            ),
        ),
        array(
            'id'     => 'home_ad_pop',
            'type'   => 'fieldset',
            'title'  => '弹窗广告',
            'fields' => array(
                array(
                    'id'      => 'image',
                    'type'    => 'media',
                    'title'   => '图片',
                    'library' => 'image',
                ),
                array(
                    'id'      => 'link',
                    'type'    => 'text',
                    'title'   => '链接',
                    'default' => 'https://www.zhuige.com',
                ),
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => '开启/停用',
                    'default' => '1'
                ),
                array(
                    'id'       => 'interval',
                    'type'     => 'number',
                    'title'    => '间隔时间',
                    'subtitle' => '单位（小时）',
                ),
            )
        ),
        array(
            'id'    => 'share_title',
            'type'  => 'text',
            'title' => '分享标题',
            'desc'  => '分享给好友时显示的标题'
        ),
        array(
            'id'      => 'share_thumb',
            'type'    => 'media',
            'title'   => '分享图片',
            'library' => 'image',
            'desc'    => '分享给好友时显示的图片'
        ),
    )
) );
