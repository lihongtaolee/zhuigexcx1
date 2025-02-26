<?php
/**
 * 后台管理 - 商城首页设置
 * 追格商城后台原代码迁移，原封不动保留 CSF 配置
 *
 * 请确保 WordPress 环境中已加载 CSF（Codestar Framework）。
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // 防止直接访问
}

// 检查 CSF 是否加载
if ( ! class_exists( 'CSF' ) ) {
    echo '<div class="error notice"><p>错误：Codestar Framework 未加载，请确保 CSF 框架已正确安装和启用。</p></div>';
    return;
}

$prefix = 'zhuige_shop_options';

// 创建选项页面，menu_slug 必须与 WordPress 后台菜单中一致，此处设置为 "shop_home"
if ( class_exists( 'CSF' ) ) {
    CSF::createOptions( $prefix, array(
        'menu_title'      => '商城首页设置',
        'menu_slug'       => 'shop_home',
        'framework_title' => '商城首页设置',
        'show_reset_all'  => false,
        'menu_type'       => 'menu', // 可设置为 'menu' 或 'submenu'
        'menu_position'   => 60,
        'menu_icon'       => 'dashicons-store',
    ));

    // 创建配置分区，保留原有配置
    CSF::createSection($prefix, array(
        'id'    => 'home',
        'title' => '首页设置',
        'icon'  => 'fas fa-home',
        'fields' => array(
            array(
                'id'      => 'home_bg',
                'type'    => 'media',
                'title'   => '顶部背景图',
                'library' => 'image',
            ),
            array(
                'id'          => 'home_title',
                'type'        => 'text',
                'title'       => '分享标题',
                'placeholder' => '分享标题'
            ),
            array(
                'id'      => 'home_thumb',
                'type'    => 'media',
                'title'   => '分享缩略图',
                'library' => 'image',
            ),
            array(
                'id'     => 'home_slide',
                'type'   => 'group',
                'title'  => '幻灯片',
                'fields' => array(
                    array(
                        'id'       => 'title',
                        'type'     => 'text',
                        'title'    => '标题',
                    ),
                    array(
                        'id'      => 'image',
                        'type'    => 'media',
                        'title'   => '图片',
                        'library' => 'image',
                    ),
                    array(
                        'id'       => 'link',
                        'type'     => 'text',
                        'title'    => '链接',
                        'default'  => 'https://www.zhuige.com',
                    ),
                    array(
                        'id'    => 'switch',
                        'type'  => 'switcher',
                        'title' => '开启/停用',
                        'default' => '1'
                    ),
                ),
            ),
            array(
                'id'     => 'home_nav',
                'type'   => 'group',
                'title'  => '导航项',
                'fields' => array(
                    array(
                        'id'          => 'title',
                        'type'        => 'text',
                        'title'       => '标题',
                        'placeholder' => '标题'
                    ),
                    array(
                        'id'      => 'image',
                        'type'    => 'media',
                        'title'   => '图片',
                        'library' => 'image',
                    ),
                    array(
                        'id'       => 'link',
                        'type'     => 'text',
                        'title'    => '链接',
                        'default'  => 'https://www.zhuige.com',
                    ),
                    array(
                        'id'    => 'switch',
                        'type'  => 'switcher',
                        'title' => '开启/停用',
                        'default' => '1'
                    ),
                ),
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
                        'id'          => 'goods_ids',
                        'type'        => 'select',
                        'title'       => '选择商品',
                        'chosen'      => true,
                        'multiple'    => true,
                        'sortable'    => true,
                        'ajax'        => true,
                        'options'     => 'posts',
                        'query_args'  => array(
                            'post_type' => 'jq_goods',
                        ),
                        'placeholder' => '请选择商品',
                    ),
                    array(
                        'id'    => 'switch',
                        'type'  => 'switcher',
                        'title' => '开启/停用',
                        'subtitle' => '是否显示活动区域',
                        'default' => '1'
                    ),
                ),
            ),
            array(
                'id'          => 'goods_cat',
                'type'        => 'select',
                'title'       => '导航设置',
                'placeholder' => '选择分类',
                'chosen'      => true,
                'multiple'    => true,
                'sortable'    => true,
                'options'     => 'categories',
                'query_args'  => array(
                    'taxonomy'  => 'jq_goods_cat'
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
                        'id'       => 'link',
                        'type'     => 'text',
                        'title'    => '链接',
                        'default'  => 'https://www.zhuige.com',
                    ),
                    array(
                        'id'    => 'switch',
                        'type'  => 'switcher',
                        'title' => '开启/停用',
                        'default' => '1'
                    ),
                    array(
                        'id'    => 'interval',
                        'type'  => 'number',
                        'title' => '间隔时间',
                        'subtitle' => '单位（小时）',
                    ),
                ),
            ),
        )
    ));
}
