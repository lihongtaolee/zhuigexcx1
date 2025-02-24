<?php
class ZhuiGe_Shop_Base {
    protected function __construct() {
        // 引入商品类型注册文件
        require_once dirname(__FILE__) . '/post-types.php';
    }

    /**
     * 成功返回
     */
    protected function success($data) {
        return ['code' => 0, 'msg' => '', 'data' => $data];
    }

    /**
     * 失败返回
     */
    protected function error($msg) {
        return ['code' => 1, 'msg' => $msg, 'data' => ''];
    }

    /**
     * 获取整型参数
     */
    protected function param_int($request, $key, $default = 0) {
        $value = $request->get_param($key);
        if ($value === null || !is_numeric($value)) {
            return $default;
        }

        return intval($value);
    }

    /**
     * 获取字符串参数
     */
    protected function param_string($request, $key, $default = '') {
        $value = $request->get_param($key);
        if ($value === null) {
            return $default;
        }

        return sanitize_text_field($value);
    }

    /**
     * 获取数组参数
     */
    protected function param_array($request, $key, $default = []) {
        $value = $request->get_param($key);
        if ($value === null || !is_array($value)) {
            return $default;
        }

        return $value;
    }
}