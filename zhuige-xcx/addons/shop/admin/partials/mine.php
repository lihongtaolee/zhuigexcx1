<?php
/**
 * 追格商城个人中心页面设置
 */

CSF::createSection('zhuige-shop', array(
    'title'  => '个人中心',
    'icon'   => 'fas fa-user',
    'fields' => array(

        array(
            'id'      => 'mine_banner',
            'type'    => 'media',
            'title'   => '顶部背景图',
        ),

        array(
            'id'      => 'mine_menu',
            'type'    => 'group',
            'title'   => '菜单设置',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '菜单名称',
                ),
                array(
                    'id'      => 'icon',
                    'type'    => 'media',
                    'title'   => '菜单图标',
                ),
                array(
                    'id'      => 'link',
                    'type'    => 'text',
                    'title'   => '链接地址',
                ),
            ),
        ),

        array(
            'id'      => 'mine_about',
            'type'    => 'fieldset',
            'title'   => '关于我们',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '标题',
                    'default' => '关于我们',
                ),
                array(
                    'id'      => 'content',
                    'type'    => 'wp_editor',
                    'title'   => '内容',
                ),
            ),
        ),

    )
}));