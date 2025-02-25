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

class ZhuiGe_Shop_Order_Controller extends ZhuiGe_Shop_Base_Controller {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * 创建订单
	 */
	public function create_order($request) {
		$user_id = get_current_user_id();
		if (!$user_id) {
			return $this->error('请先登录');
		}

		$goods_id = $request->get_param('goods_id');
		$quantity = $request->get_param('quantity');

		if (!$goods_id || !$quantity) {
			return $this->error('参数错误');
		}

		$post = get_post($goods_id);
		if (!$post) {
			return $this->error('商品不存在');
		}

		$price = get_post_meta($goods_id, 'price', true);
		$stock = get_post_meta($goods_id, 'stock', true);

		if ($quantity > $stock) {
			return $this->error('库存不足');
		}

		global $wpdb;
		$order_no = date('YmdHis') . rand(1000, 9999);
		$total_price = $price * $quantity;

		$result = $wpdb->insert(
			$wpdb->prefix . 'zhuige_shop_order',
			[
				'order_no' => $order_no,
				'user_id' => $user_id,
				'goods_id' => $goods_id,
				'price' => $total_price,
				'status' => 'pending',
				'create_time' => current_time('mysql')
			]
		);

		if ($result === false) {
			return $this->error('创建订单失败');
		}

		return $this->success(['order_no' => $order_no]);
	}

	/**
	 * 获取订单列表
	 */
	public function get_order_list($request) {
		$user_id = get_current_user_id();
		if (!$user_id) {
			return $this->error('请先登录');
		}

		$offset = $request->get_param('offset');
		$per_page = 10;

		global $wpdb;
		$orders = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}zhuige_shop_order WHERE user_id = %d ORDER BY create_time DESC LIMIT %d OFFSET %d",
				$user_id,
				$per_page,
				$offset
			)
		);

		$order_list = [];
		foreach ($orders as $order) {
			$goods = get_post($order->goods_id);
			$order_list[] = [
				'order_no' => $order->order_no,
				'goods_title' => $goods->post_title,
				'price' => $order->price,
				'status' => $order->status,
				'create_time' => $order->create_time
			];
		}

		return $this->success([
			'orders' => $order_list,
			'more' => count($order_list) >= $per_page ? 1 : 0
		]);
	}

	/**
	 * 获取订单详情
	 */
	public function get_order_detail($request) {
		$user_id = get_current_user_id();
		if (!$user_id) {
			return $this->error('请先登录');
		}

		$order_no = $request->get_param('order_no');
		if (!$order_no) {
			return $this->error('参数错误');
		}

		global $wpdb;
		$order = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}zhuige_shop_order WHERE order_no = %s AND user_id = %d",
				$order_no,
				$user_id
			)
		);

		if (!$order) {
			return $this->error('订单不存在');
		}

		$goods = get_post($order->goods_id);
		$data = [
			'order_no' => $order->order_no,
			'goods_title' => $goods->post_title,
			'goods_thumbnail' => get_the_post_thumbnail_url($goods->ID),
			'price' => $order->price,
			'status' => $order->status,
			'create_time' => $order->create_time
		];

		return $this->success($data);
	}
}