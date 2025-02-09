<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class ZhuiGe_BBS_Activator {
    public static function activate() {
        global $wpdb;

        // 注册自定义文章类型
        require_once ZHUIGE_XCX_ADDONS_DIR . 'zhuige-bbs/post-type.php';
        zhuige_xcx_bbs_create_custom_post_type();

        // 创建默认分类
        if (!term_exists('默认分类', 'zhuige_bbs_forum_cat')) {
            wp_insert_term('默认分类', 'zhuige_bbs_forum_cat', array(
                'description' => '默认圈子分类',
                'slug' => 'default'
            ));
        }

        // 确保自定义文章类型被正确注册
        register_post_type('zhuige_bbs_forum', array(
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'author'),
            'rewrite' => array('slug' => 'forum')
        ));

        // 刷新固定链接
        flush_rewrite_rules(true);
    }
}