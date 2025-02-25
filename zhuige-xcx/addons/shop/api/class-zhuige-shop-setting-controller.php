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

require_once dirname(__FILE__) . '/class-zhuige-shop-base-controller.php';

class ZhuiGe_Shop_Setting_Controller extends ZhuiGe_Shop_Base_Controller {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * 获取首页设置
	 */
	public function get_home_setting($request) {
		$slides = get_option('zhuige-shop-home-slides', []);
		$nav = get_option('zhuige-shop-home-nav', []);
		$recommend = get_option('zhuige-shop-home-recommend', []);

		$data = [
			'slides' => $slides,
			'nav' => $nav,
			'recommend' => $recommend
		];

		return $this->success($data);
	}
}