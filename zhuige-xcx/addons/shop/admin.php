<?php
class ZhuiGe_Shop_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        // 在 admin_init 时注册商品 metabox
        add_action('admin_init', array($this, 'register_goods_metabox'));
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

        // 追格商城Free子菜单（基础设置）
        add_submenu_page(
            'zhuige-shop',
            '基础设置',
            '基础设置',
            'manage_options',
            'zhuige-shop',
            array($this, 'shop_settings_page')
        );

        // 追格商品菜单（自定义添加，不让自定义文章类型自动生成菜单）
        add_menu_page(
            '追格商品',
            '追格商品',
            'manage_options',
            'edit.php?post_type=jq_goods',
            null,
            'dashicons-products',
            101
        );

        // 删除 WordPress 自动生成的重复子菜单项
        remove_submenu_page('edit.php?post_type=jq_goods', 'edit.php?post_type=jq_goods');

        // 商品分类，作为追格商品菜单下的子菜单
        add_submenu_page(
            'edit.php?post_type=jq_goods',
            '商品分类',
            '商品分类',
            'manage_options',
            'edit-tags.php?taxonomy=jq_goods_cat&post_type=jq_goods'
        );
    }

    /**
     * 注册追格商品元数据的 metabox，确保在编辑商品时显示
     */
    public function register_goods_metabox() {
        $prefix_jq_goods_opts = 'zhuige-jq_goods-opt';
        if ( class_exists('CSF') ) {
            CSF::createMetabox($prefix_jq_goods_opts, array(
                'title'     => '追格商城设置',
                'post_type' => 'jq_goods',
            ));

            CSF::createSection($prefix_jq_goods_opts, array(
                'fields' => array(
                    array(
                        'id'      => 'slide',
                        'type'    => 'group',
                        'title'   => '幻灯片',
                        'fields'  => array(
                            array(
                                'id'      => 'image',
                                'type'    => 'media',
                                'title'   => '图片',
                                'library' => 'image',
                            ),
                        ),
                    ),
                    array(
                        'id'          => 'badge',
                        'type'        => 'text',
                        'title'       => '角标',
                        'placeholder' => '角标'
                    ),
                    array(
                        'id'      => 'orig_price',
                        'type'    => 'number',
                        'title'   => '原价格',
                        'unit'    => '元',
                        'default' => '1',
                    ),
                    array(
                        'id'      => 'price',
                        'type'    => 'number',
                        'title'   => '促销价格',
                        'unit'    => '元',
                        'default' => '1',
                    ),
                    array(
                        'id'      => 'stock',
                        'type'    => 'number',
                        'title'   => '库存',
                        'unit'    => '套',
                        'default' => '100',
                    ),
                    array(
                        'id'      => 'quantity',
                        'type'    => 'number',
                        'title'   => '销量',
                        'unit'    => '套',
                        'default' => '0',
                    ),
                )
            ));
        }
    }

    /**
     * 商城设置页面
     */
    public function shop_settings_page() {
        if ( ! current_user_can('manage_options') ) {
            wp_die('您没有权限访问此页面');
        }
        // 检查 CodeStar Framework 是否加载
        if ( ! class_exists('CSF') ) {
            echo '<div class="wrap"><h1>CodeStar Framework 未加载，请检查插件依赖。</h1></div>';
            return;
        }
        
        // 创建选项组（统一使用 id "zhuige-shop"）
        CSF::createOptions('zhuige-shop', array(
            'menu_title'      => '追格商城Free',
            'menu_slug'       => 'zhuige-shop',
            'framework_title' => '追格商城Free <small>by <a href="https://www.zhuige.com" target="_blank" title="追格">www.zhuige.com</a></small>',
            'menu_hidden'     => true,
            'show_bar_menu'   => false,
        ));

        // 包含 admin/partials 目录下的各个配置文件，
        // 使得 overview.php、global.php、home.php 内的 CSF::createSection 被执行，完成各子页面的注册
        include_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/partials/overview.php';
        include_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/partials/global.php';
        include_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/partials/home.php';

        // 如有需要，可额外注册一个基础设置 section
        CSF::createSection('zhuige-shop', array(
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
    }
}

new ZhuiGe_Shop_Admin();
