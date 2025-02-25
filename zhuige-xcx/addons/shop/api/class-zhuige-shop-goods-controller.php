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

class ZhuiGe_Shop_Goods_Controller extends ZhuiGe_Shop_Base_Controller {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * 获取商品列表
	 */
	public function get_goods_list($request) {
		$cat_id = $request->get_param('cat_id');
		$offset = $request->get_param('offset');
		$per_page = 10;

		$args = [
			'post_type' => 'jq_goods',
			'post_status' => 'publish',
			'posts_per_page' => $per_page,
			'offset' => $offset,
			'orderby' => 'date',
			'order' => 'DESC'
		];

		if ($cat_id) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'jq_goods_cat',
					'field' => 'term_id',
					'terms' => $cat_id
				]
			];
		}

		$query = new WP_Query($args);
		$goods_list = [];

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$goods_list[] = [
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'thumbnail' => get_the_post_thumbnail_url(),
					'price' => get_post_meta(get_the_ID(), 'price', true),
					'sales' => get_post_meta(get_the_ID(), 'sales', true)
				];
			}
		}
		wp_reset_postdata();

		return $this->success([
			'goods' => $goods_list,
			'more' => count($goods_list) >= $per_page ? 1 : 0
		]);
	}

	/**
	 * 获取商品详情
	 */
	public function get_goods_detail($request) {
		$goods_id = $request->get_param('goods_id');
		if (!$goods_id) {
			return $this->error('缺少参数');
		}

		$post = get_post($goods_id);
		if (!$post) {
			return $this->error('商品不存在');
		}

		$data = [
			'id' => $post->ID,
			'title' => $post->post_title,
			'content' => $post->post_content,
			'thumbnail' => get_the_post_thumbnail_url($post->ID),
			'price' => get_post_meta($post->ID, 'price', true),
			'sales' => get_post_meta($post->ID, 'sales', true),
			'stock' => get_post_meta($post->ID, 'stock', true),
			'gallery' => get_post_meta($post->ID, 'gallery', true)
		];

		return $this->success($data);
	}
}