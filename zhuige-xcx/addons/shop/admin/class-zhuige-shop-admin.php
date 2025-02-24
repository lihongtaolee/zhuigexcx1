<?php
/**
 * 追格商城后台管理类
 */

class Zhuige_Shop_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('admin_menu', array($this, 'add_menu'));
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/zhuige-shop-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/zhuige-shop-admin.js', array('jquery'), $this->version, false);
    }

    public function add_menu() {
        $prefix = 'zhuige-shop';

        CSF::createOptions($prefix, array(
            'framework_title' => '追格商城Free <small>by <a href="https://www.zhuige.com" target="_blank" title="追格商城小程序">www.zhuige.com</a></small>',
            'menu_title' => '追格商城Free',
            'menu_slug'  => 'zhuige-shop',
            'menu_position' => 2,
            'show_bar_menu' => false,
            'show_sub_menu' => false,
            'footer_credit' => 'Thank you for creating with <a href="https://www.zhuige.com/" target="_blank">追格</a>',
            'menu_icon' => 'dashicons-layout',
        ));

        $base_dir = plugin_dir_path(__FILE__);
        $base_url = plugin_dir_url(__FILE__);
        require_once $base_dir . 'partials/overview.php';
        require_once $base_dir . 'partials/global.php';
        require_once $base_dir . 'partials/home.php';
        require_once $base_dir . 'partials/category.php';
        require_once $base_dir . 'partials/search.php';
        require_once $base_dir . 'partials/mine.php';
        require_once $base_dir . 'partials/login.php';
        require_once $base_dir . 'partials/goods.php';

        // 备份
        CSF::createSection($prefix, array(
            'title'       => '备份',
            'icon'        => 'fas fa-shield-alt',
            'fields'      => array(
                array(
                    'type' => 'backup',
                ),
            )
        ));

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

    public function display_plugin_admin_page() {
        require_once plugin_dir_path(__FILE__) . 'partials/zhuige-shop-admin-display.php';
    }

    public function save_settings() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        check_ajax_referer('zhuige_shop_admin', 'nonce');

        $shop_title = sanitize_text_field($_POST['shop_title']);
        $shop_description = sanitize_textarea_field($_POST['shop_description']);

        update_option('zhuige_shop_title', $shop_title);
        update_option('zhuige_shop_description', $shop_description);

        wp_send_json_success('设置已保存');
    }
}