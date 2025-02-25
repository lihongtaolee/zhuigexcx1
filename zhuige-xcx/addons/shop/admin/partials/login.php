<?php
/**
 * 追格商城登录页面设置
 */

CSF::createSection('zhuige-shop', array(
    'title'  => '登录页面',
    'icon'   => 'fas fa-sign-in-alt',
    'fields' => array(

        array(
            'id'      => 'login_logo',
            'type'    => 'media',
            'title'   => '登录页LOGO',
        ),

        array(
            'id'      => 'login_title',
            'type'    => 'text',
            'title'   => '登录页标题',
            'default' => '追格商城',
        ),

        array(
            'id'      => 'login_desc',
            'type'    => 'textarea',
            'title'   => '登录页描述',
            'default' => '欢迎使用追格商城小程序',
        ),

        array(
            'id'      => 'login_agreement',
            'type'    => 'fieldset',
            'title'   => '用户协议',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '标题',
                    'default' => '用户协议',
                ),
                array(
                    'id'      => 'content',
                    'type'    => 'wp_editor',
                    'title'   => '内容',
                ),
            ),
        ),

        array(
            'id'      => 'login_privacy',
            'type'    => 'fieldset',
            'title'   => '隐私政策',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '标题',
                    'default' => '隐私政策',
                ),
                array(
                    'id'      => 'content',
                    'type'    => 'wp_editor',
                    'title'   => '内容',
                ),
            ),
        ),

    )
));