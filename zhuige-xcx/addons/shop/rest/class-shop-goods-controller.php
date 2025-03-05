<?php

/**
 * 追格小程序 - 商城模块
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class Shop_Goods_Controller extends Shop_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'shop';
		$this->routes = [
			'last' => ['callback' => 'get_last', 'method' => 'GET']
		];
	}

	/**
	 * 获取最新商品
	 */
	public function get_last($request)
	{
		$offset = $this->param($request, 'offset', 0);
		$cat_id = $this->param($request, 'cat_id', 0);

		$args = [
			'post_type' => 'jq_goods',
			'posts_per_page' => 10,
			'offset' => $offset,
			'ignore_sticky_posts' => 1,
		];

		if ($cat_id) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'jq_goods_cat',
					'field' => 'term_id',
					'terms' => [$cat_id]
				]
			];
		}

		$query = new WP_Query();
		$result = $query->query($args);
		$goods_list = [];
		foreach ($result as $item) {
			// 获取商品元数据
			$goods_meta = get_post_meta($item->ID, 'zhuige-jq_goods-opt', true);
			$price = isset($goods_meta['price']) ? $goods_meta['price'] : '0';
			$orig_price = isset($goods_meta['orig_price']) ? $goods_meta['orig_price'] : $price;
			$badge = isset($goods_meta['badge']) ? $goods_meta['badge'] : '';

			$goods_list[] = [
				'id' => $item->ID,
				'title' => $item->post_title,
				'thumbnail' => $this->get_one_post_thumbnail($item->ID),
				'price' => $price ? $price : '0',
				'orig_price' => $orig_price ? $orig_price : $price,
				'badge' => $badge ? $badge : ''
			];
		}

		$more = 'nomore';
		if (count($goods_list) >= 10) {
			$more = 'more';
		}

		return $this->success([
			'list' => $goods_list,
			'more' => $more
		]);
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

		return ZhuiGe_Xcx::option_image_url(ZhuiGe_Xcx::option_value('default_thumbnail'), 'public/images/zhuige.png');
	}
}