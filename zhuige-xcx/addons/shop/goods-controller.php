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

class ZhuiGe_Shop_Goods_Controller extends Shop_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'goods';
		$this->routes = [
			'get_goods_list' => 'get_goods_list',
			'get_goods' => 'get_goods',
		];
	}

	/**
	 * 商品列表
	 */
	public function get_goods_list($request)
	{
		$offset = (int)($this->param_value($request, 'offset', 0));
		$cat = (int)($this->param_value($request, 'cat', 0));
		$search = $this->param_value($request, 'search', '');

		$args = ["post_type" => "zhuige_shop_goods", 'orderby' => 'date', 'posts_per_page' => 10, 'offset' => $offset];
		if ($cat) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'zhuige_shop_goods_cat',
					'field'    => 'id',
					'terms'    => $cat,
				],
			];
		}
		if (!empty($search)) {
			$args['s'] = $search;
		}

		$query = new WP_Query();
		$posts = $query->query($args);
		$goods_array = [];
		foreach ($posts as $post) {
			$data = ["id" => $post->ID];
			$data["title"] = $post->post_title;
			$data["thumbnail"] = $this->get_post_thumbnail($post->ID);
			$data["price"] = get_post_meta($post->ID, 'zhuige_goods_price', true);
			$data["sale_count"] = (int)get_post_meta($post->ID, 'zhuige_goods_sale_count', true);
			$goods_array[] = $data;
		}

		$result = ["goods" => $goods_array];
		return $this->success($result);
	}

	/**
	 * 商品详情
	 */
	public function get_goods($request)
	{
		$goods_id = (int)($this->param_value($request, 'goods_id'));

		$post = get_post($goods_id);
		if (empty($post)) {
			return $this->error('商品不存在');
		}

		$data = ["id" => $post->ID];
		$data["title"] = $post->post_title;
		$data["content"] = apply_filters('the_content', $post->post_content);
		$data["price"] = get_post_meta($post->ID, 'zhuige_goods_price', true);
		$data["sale_count"] = (int)get_post_meta($post->ID, 'zhuige_goods_sale_count', true);
		$data["images"] = get_post_meta($post->ID, 'zhuige_goods_images', true);

		$result = ["goods" => $data];
		return $this->success($result);
	}
}