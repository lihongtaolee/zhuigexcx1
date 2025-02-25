<?php
/**
 * 追格商城基础控制器
 */
if (!defined('ABSPATH')) exit;

class Zhuige_Shop_Base_Controller {
    public function __construct() {
        $this->log("基础控制器初始化");
    }

    /**
     * 日志记录函数（可扩展为写入文件或其他方式）
     *
     * @param string $message
     */
    public function log($message) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("[追格商城] " . $message);
        }
    }
    
    public function init() {
        $this->log("业务初始化");
    }
}
?>
