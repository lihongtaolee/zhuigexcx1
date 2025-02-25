<?php
/*
 * 追格商城小程序
 * Author: 追格
 * Help document: https://www.zhuige.com/product/sc.html
 * github: https://github.com/zhuige-com/zhuige_shop
 * gitee: https://gitee.com/zhuige_com/zhuige_shop
 * License：GPL-2.0
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

if (!defined('ABSPATH')) {
	exit;
}

class ZhuiGe_Shop_Base_Controller extends WP_REST_Controller {
	public function __construct() {
		$this->namespace = 'zhuige/v1';
	}

	protected function base_response($code, $msg = '', $data = null) {
		$response = [
			'code' => $code,
			'msg' => $msg,
		];
		if ($data !== null) {
			$response['data'] = $data;
		}
		return $response;
	}

	protected function success($data = null) {
		return $this->base_response(0, '', $data);
	}

	protected function error($msg = '', $code = 1) {
		return $this->base_response($code, $msg);
	}
}