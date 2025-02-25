<?php
/**
 * 追格商城设置控制器
 */
if (!defined('ABSPATH')) exit;

class Zhuige_Shop_Setting_Controller extends Zhuige_Shop_Base_Controller {
    public function __construct() {
        parent::__construct();
    }

    /**
     * 获取设置项
     *
     * @param string $key
     * @return mixed
     */
    public function get_setting($key) {
        return get_option($key);
    }

    /**
     * 更新设置项
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function update_setting($key, $value) {
        return update_option($key, $value);
    }
}
?>
