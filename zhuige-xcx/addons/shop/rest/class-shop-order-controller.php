<?php

/**
 * 追格小程序 - 商城模块
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class Shop_Order_Controller extends ZhuiGe_Xcx_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'zhuige/shop/order';
		$this->routes = [
			'create' => 'create_order',
			'list' => 'get_order_list',
			'detail' => 'get_order_detail',
			'cancel' => 'cancel_order',
			'pay' => 'pay_order',
			'confirm' => 'confirm_order',
			'delete' => 'delete_order',
		];
	}

	/**
	 * 创建订单
	 */
	public function create_order($request)
	{
		$user_id = $this->check_login($request);
		if (!$user_id) {
			return $this->make_error('请先登录');
		}

		$goods_id = (int)($this->param_value($request, 'goods_id'));
		if (!$goods_id) {
			return $this->make_error('缺少参数');
		}

		$count = (int)($this->param_value($request, 'count', 1));
		if ($count <= 0) {
			return $this->make_error('数量必须大于0');
		}

		$address_id = (int)($this->param_value($request, 'address_id'));
		if (!$address_id) {
			return $this->make_error('请选择收货地址');
		}

		// 获取商品信息
		global $wpdb;
		$table_posts = $wpdb->prefix . 'posts';
		$table_postmeta = $wpdb->prefix . 'postmeta';

		$goods = $wpdb->get_row($wpdb->prepare(
			"SELECT p.ID, p.post_title, p.post_excerpt, pm1.meta_value as price, pm2.meta_value as thumb 
			FROM $table_posts p 
			LEFT JOIN $table_postmeta pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'zhuige_goods_price' 
			LEFT JOIN $table_postmeta pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'zhuige_goods_thumb' 
			WHERE p.ID = %d AND p.post_type = 'zhuige_goods' AND p.post_status = 'publish'",
			$goods_id
		));

		if (!$goods) {
			return $this->make_error('商品不存在或已下架');
		}

		// 获取收货地址
		$table_address = $wpdb->prefix . 'zhuige_shop_address';
		$address = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_address WHERE id = %d AND user_id = %d",
			$address_id, $user_id
		));

		if (!$address) {
			return $this->make_error('收货地址不存在');
		}

		// 创建订单
		$table_order = $wpdb->prefix . 'zhuige_shop_order';
		$order_no = 'ZG' . date('YmdHis') . mt_rand(1000, 9999);
		$total_price = $goods->price * $count;

		$wpdb->insert(
			$table_order,
			[
				'user_id' => $user_id,
				'order_no' => $order_no,
				'goods_id' => $goods_id,
				'goods_title' => $goods->post_title,
				'goods_thumb' => $goods->thumb,
				'goods_price' => $goods->price,
				'count' => $count,
				'total_price' => $total_price,
				'consignee' => $address->consignee,
				'mobile' => $address->mobile,
				'address' => $address->province . $address->city . $address->district . $address->address,
				'createtime' => time(),
				'status' => 'wait_pay'
			]
		);

		$order_id = $wpdb->insert_id;

		return $this->make_success(['order_id' => $order_id, 'order_no' => $order_no]);
	}

	/**
	 * 获取订单列表
	 */
	public function get_order_list($request)
	{
		$user_id = $this->check_login($request);
		if (!$user_id) {
			return $this->make_error('请先登录');
		}

		$status = $this->param_value($request, 'status', '');
		$page = (int)($this->param_value($request, 'page', 1));
		$per_page = (int)($this->param_value($request, 'per_page', 10));

		global $wpdb;
		$table_order = $wpdb->prefix . 'zhuige_shop_order';

		$where = "WHERE user_id = $user_id";
		if ($status) {
			$where .= " AND status = '$status'";
		}

		$limit = 'LIMIT ' . ($page - 1) * $per_page . ',' . $per_page;

		$orders = $wpdb->get_results(
			"SELECT * FROM $table_order $where ORDER BY createtime DESC $limit"
		);

		$list = [];
		foreach ($orders as $order) {
			$list[] = [
				'id' => $order->id,
				'order_no' => $order->order_no,
				'goods_title' => $order->goods_title,
				'goods_thumb' => $order->goods_thumb,
				'goods_price' => $order->goods_price,
				'count' => $order->count,
				'total_price' => $order->total_price,
				'status' => $order->status,
				'createtime' => date('Y-m-d H:i:s', $order->createtime)
			];
		}

		$count = $wpdb->get_var("SELECT COUNT(*) FROM $table_order $where");

		return $this->make_success([
			'list' => $list,
			'total' => (int)$count,
			'pages' => ceil($count / $per_page)
		]);
	}

	/**
	 * 获取订单详情
	 */
	public function get_order_detail($request)
	{
		$user_id = $this->check_login($request);
		if (!$user_id) {
			return $this->make_error('请先登录');
		}

		$order_id = (int)($this->param_value($request, 'order_id'));
		if (!$order_id) {
			return $this->make_error('缺少参数');
		}

		global $wpdb;
		$table_order = $wpdb->prefix . 'zhuige_shop_order';

		$order = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_order WHERE id = %d AND user_id = %d",
			$order_id, $user_id
		));

		if (!$order) {
			return $this->make_error('订单不存在');
		}

		$data = [
			'id' => $order->id,
			'order_no' => $order->order_no,
			'goods_id' => $order->goods_id,
			'goods_title' => $order->goods_title,
			'goods_thumb' => $order->goods_thumb,
			'goods_price' => $order->goods_price,
			'count' => $order->count,
			'total_price' => $order->total_price,
			'consignee' => $order->consignee,
			'mobile' => $order->mobile,
			'address' => $order->address,
			'status' => $order->status,
			'createtime' => date('Y-m-d H:i:s', $order->createtime),
			'paytime' => $order->paytime ? date('Y-m-d H:i:s', $order->paytime) : '',
			'confirmtime' => $order->confirmtime ? date('Y-m-d H:i:s', $order->confirmtime) : ''
		];

		return $this->make_success($data);
	}

	/**
	 * 取消订单
	 */
	public function cancel_order($request)
	{
		$user_id = $this->check_login($request);
		if (!$user_id) {
			return $this->make_error('请先登录');
		}

		$order_id = (int)($this->param_value($request, 'order_id'));
		if (!$order_id) {
			return $this->make_error('缺少参数');
		}

		global $wpdb;
		$table_order = $wpdb->prefix . 'zhuige_shop_order';

		$order = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_order WHERE id = %d AND user_id = %d",
			$order_id, $user_id
		));

		if (!$order) {
			return $this->make_error('订单不存在');
		}

		if ($order->status != 'wait_pay') {
			return $this->make_error('只能取消待支付的订单');
		}

		$wpdb->update(
			$table_order,
			[
				'status' => 'canceled',
				'canceltime' => time()
			],
			['id' => $order_id]
		);

		return $this->make_success('取消订单成功');
	}

	/**
	 * 支付订单
	 */
	public function pay_order($request)
	{
		$user_id = $this->check_login($request);
		if (!$user_id) {
			return $this->make_error('请先登录');
		}

		$order_id = (int)($this->param_value($request, 'order_id'));
		if (!$order_id) {
			return $this->make_error('缺少参数');
		}

		global $wpdb;
		$table_order = $wpdb->prefix . 'zhuige_shop_order';

		$order = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_order WHERE id = %d AND user_id = %d",
			$order_id, $user_id
		));

		if (!$order) {
			return $this->make_error('订单不存在');
		}

		if ($order->status != 'wait_pay') {
			return $this->make_error('只能支付待支付的订单');
		}

		// 模拟支付成功
		$wpdb->update(
			$table_order,
			[
				'status' => 'wait_confirm',
				'paytime' => time()
			],
			['id' => $order_id]
		);

		return $this->make_success('支付成功');
	}

	/**
	 * 确认收货
	 */
	public function confirm_order($request)
	{
		$user_id = $this->check_login($request);
		if (!$user_id) {
			return $this->make_error('请先登录');
		}

		$order_id = (int)($this->param_value($request, 'order_id'));
		if (!$order_id) {
			return $this->make_error('缺少参数');
		}

		global $wpdb;
		$table_order = $wpdb->prefix . 'zhuige_shop_order';

		$order = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_order WHERE id = %d AND user_id = %d",
			$order_id, $user_id
		));

		if (!$order) {
			return $this->make_error('订单不存在');
		}

		if ($order->status != 'wait_confirm') {
			return $this->make_error('只能确认待收货的订单');
		}

		$wpdb->update(
			$table_order,
			[
				'status' => 'completed',
				'confirmtime' => time()
			],
			['id' => $order_id]
		);

		return $this->make_success('确认收货成功');
	}

	/**
	 * 删除订单
	 */
	public function delete_order($request)
	{
		$user_id = $this->check_login($request);
		if (!$user_id) {
			return $this->make_error('请先登录');
		}

		$order_id = (int)($this->param_value($request, 'order_id'));
		if (!$order_id) {
			return $this->make_error('缺少参数');
		}

		global $wpdb;
		$table_order = $wpdb->prefix . 'zhuige_shop_order';

		$order = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_order WHERE id = %d AND user_id = %d",
			$order_id, $user_id
		));

		if (!$order) {
			return $this->make_error('订单不存在');
		}

		if ($order->status != 'completed' && $order->status != 'canceled') {
			return $this->make_error('只能删除已完成或已取消的订单');
		}

		$wpdb->delete(
			$table_order,
			['id' => $order_id]
		);

		return $this->make_success('删除订单成功');
	}
}