<?php

class ZhuiGe_Shop {
    protected $plugin_name;
    protected $version;
    protected $loader;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-zhuige-shop-post_types.php';

        $post_types = new ZhuiGe_Shop_Post_Types();
        $post_types->create_custom_post_type();
    }

    private function define_admin_hooks() {
        $plugin_admin = new Zhuige_Shop_Admin($this->plugin_name, $this->version);

        add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_scripts'));
    }

    private function define_public_hooks() {
        // 添加前台钩子
    }

    public function run() {
        // 运行插件
    }

    /**
     * 获取配置
     */
    public static function option_value($key, $default = '') {
        static $options = false;
        if (!$options) {
            $options = get_option('zhuige-shop');
        }

        if (isset($options[$key]) && !empty($options[$key])) {
            return $options[$key];
        }

        return $default;
    }

    /**
     * 追格商品属性
     */
    public static function post_goods_property($post_id, $key, $default = '') {
        $options = get_post_meta($post_id, 'zhuige-jq_goods-opt', true);
        if (isset($options[$key]) && !empty($options[$key])) {
            return $options[$key];
        }

        return $default;
    }
}