<?php
class ZhuiGe_Shop_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    /**
     * 添加后台菜单
     */
    public function admin_menu() {
        // 追格商城Free菜单
        add_menu_page(
            '追格商城Free',
            '追格商城Free',
            'manage_options',
            'zhuige-shop',
            array($this, 'shop_settings_page'),
            'dashicons-store',
            100
        );

        // 追格商城Free子菜单
        add_submenu_page(
            'zhuige-shop',
            '基础设置',
            '基础设置',
            'manage_options',
            'zhuige-shop',
            array($this, 'shop_settings_page')
        );

        // 追格商品菜单
        add_menu_page(
            '追格商品',
            '追格商品',
            'manage_options',
            'edit.php?post_type=jq_goods',
            null,
            'dashicons-products',
            101
        );

        // 商品分类
        add_submenu_page(
            'edit.php?post_type=jq_goods',
            '商品分类',
            '商品分类',
            'manage_options',
            'edit-tags.php?taxonomy=jq_goods_cat&post_type=jq_goods'
        );
    }

    /**
     * 商城设置页面
     */
    public function shop_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('您没有权限访问此页面');
        }

        // 检查 CodeStar Framework 是否加载
        if (!class_exists('CSF')) {
            echo '<div class="wrap"><h1>CodeStar Framework 未加载，请检查插件依赖。</h1></div>';
            return;
        }

        // 使用 CodeStar Framework 创建设置页面
        CSF::createOptions('zhuige_shop', array(
            'menu_title'       => '追格商城Free',
            'menu_slug'        => 'zhuige-shop',
            'framework_title'  => '追格商城Free <small>by <a href="https://www.zhuige.com" target="_blank" title="追格">www.zhuige.com</a></small>',
            'menu_hidden'      => true,
            'show_bar_menu'    => false,
        ));

        // 基础设置
        CSF::createSection('zhuige_shop', array(
            'title'  => '基础设置',
            'fields' => array(
                array(
                    'id'    => 'home_background',
                    'type'  => 'media',
                    'title' => '首页背景图',
                ),
                array(
                    'id'     => 'home_slides',
                    'type'   => 'group',
                    'title'  => '首页轮播图',
                    'fields' => array(
                        array(
                            'id'    => 'title',
                            'type'  => 'text',
                            'title' => '标题'
                        ),
                        array(
                            'id'    => 'image',
                            'type'  => 'media',
                            'title' => '图片'
                        ),
                        array(
                            'id'    => 'link',
                            'type'  => 'text',
                            'title' => '链接'
                        ),
                    ),
                ),
                array(
                    'id'     => 'home_icon_navs',
                    'type'   => 'group',
                    'title'  => '首页图标导航',
                    'fields' => array(
                        array(
                            'id'    => 'title',
                            'type'  => 'text',
                            'title' => '标题'
                        ),
                        array(
                            'id'    => 'image',
                            'type'  => 'media',
                            'title' => '图标'
                        ),
                        array(
                            'id'    => 'link',
                            'type'  => 'text',
                            'title' => '链接'
                        ),
                    ),
                ),
                array(
                    'id'     => 'home_rec',
                    'type'   => 'fieldset',
                    'title'  => '首页推荐',
                    'fields' => array(
                        array(
                            'id'    => 'title',
                            'type'  => 'text',
                            'title' => '标题'
                        ),
                    ),
                ),
            )
        ));

        // 临时调试输出，确保页面有内容
        echo '<div class="wrap"><h1>追格商城Free 设置页面</h1></div>';
    }
}

new ZhuiGe_Shop_Admin();
