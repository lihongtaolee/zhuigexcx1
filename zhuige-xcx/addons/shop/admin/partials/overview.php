<?php
/**
 * 追格商城概览页面
 */

CSF::createSection('zhuige-shop', array(
    'title'  => '概览',
    'icon'   => 'fas fa-rocket',
    'fields' => array(

        array(
            'type'    => 'heading',
            'content' => '追格商城Free',
        ),

        array(
            'type'    => 'content',
            'content' => '<p>欢迎使用追格商城小程序！</p>',
        ),

        array(
            'type'    => 'content',
            'content' => '<p>如需了解更多信息，请访问：<a href="https://www.zhuige.com" target="_blank">www.zhuige.com</a></p>',
        ),

    )
));
