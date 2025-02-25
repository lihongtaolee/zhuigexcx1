<?php
/**
 * 追格商城模块配置文件
 * 定义常量和全局配置参数。
 */
if (!defined('ABSPATH')) exit;

define('ZHUIGE_SHOP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ZHUIGE_SHOP_PLUGIN_URL', plugin_dir_url(__FILE__));

// 可根据需要自定义数据表前缀，通常采用 WordPress 默认前缀 + 模块标识
define('ZHUIGE_SHOP_TABLE_PREFIX', 'zhuige_shop_');
?>
