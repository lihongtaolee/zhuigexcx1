<?php

/**
 * 追格小程序 - 商城模块
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class Shop_Base_Controller extends WP_REST_Controller
{
    public $routes = [];

    public function __construct()
    {
        $this->namespace = 'zhuige';
    }

    public function register_routes()
    {
        foreach ($this->routes as $key => $value) {
            register_rest_route($this->namespace, '/shop/' . $key, [
                [
                    'methods' => isset($value['method']) ? $value['method'] : WP_REST_Server::CREATABLE,
                    'callback' => [$this, $value['callback']]
                ]
            ]);
        }
    }

    public function param($request, $param_name, $default_value = false)
    {
        if (isset($request[$param_name])) {
            return sanitize_text_field(wp_unslash($request[$param_name]));
        }

        return $default_value;
    }

    public function make_response($code, $msg, $data = null)
    {
        $response = [
            'code' => $code,
            'msg' => $msg,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return $response;
    }

    public function make_success($data = null)
    {
        return $this->make_response(0, '操作成功！', $data);
    }

    public function make_error($msg = '', $code = 1)
    {
        return $this->make_response($code, $msg);
    }

    public function success($data = null)
    {
        return $this->make_success($data);
    }
}