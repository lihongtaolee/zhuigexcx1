<?php

/**
 * 追格小程序 - 商城模块
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class Shop_Goods_Controller extends ZhuiGe_Xcx_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'shop/goods';
		$this->routes = [
			'last' => 'get_last',
		];
	}

	/**
	 * 获取最新商品
	 */
	public function get_last($request)
	{
		$offset = $this->param_value($request, 'offset', 0);

		$args = [
			'post_type' => 'jq_goods',
			'posts_per_page' => 10,
			'offset' => $offset,
			'ignore_sticky_posts' => 1,
		];

		$query = new WP_Query();
		$result = $query->query($args);
		$products = [];
		foreach ($result as $item) {
			$price = get_post_meta($item->ID, 'zhuige_goods_price', true);
			$products[] = [
				'id' => $item->ID,
				'name' => $item->post_title,
				'image' => $this->get_one_post_thumbnail($item->ID),
				'price' => $price ? '¥' . $price : '免费'
			];
		}

		return $this->success($products);
	}

	/**
	 * 获取一张缩略图
	 */
	private function get_one_post_thumbnail($post_id)
	{
		$thumbnail_id = get_post_thumbnail_id($post_id);
		if ($thumbnail_id) {
			$image = wp_get_attachment_image_src($thumbnail_id, 'full');
			if ($image) {
				return $image[0];
			}
		}

		return ZhuiGe_Xcx::option_image_url(ZhuiGe_Xcx::option_value('default_thumbnail'), ZHUIGE_XCX_BASE_URL . 'public/images/zhuige.jpg');
	}
}