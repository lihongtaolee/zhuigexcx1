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

class ZhuiGe_Shop_User_Order_List extends WP_List_Table
{

	public function __construct()
	{
		parent::__construct(array(
			'singular' => '追格商城订单',    // Singular name of the listed records.
			'plural'   => '追格商城订单',    // Plural name of the listed records.
			'ajax'     => false,       		// Does this table support ajax?
		));
	}

	public function get_datas($per_page = 5, $page_number = 1, $search = null)
	{
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}zhuige_shop_user_order WHERE 1=1";

		if ($search) {
			$sql .= " AND `trade_no` LIKE '%" . esc_sql($search) . "%'";
		}

		$sql = $this->parseZGZT($sql);

		$orderby = (isset($_REQUEST['orderby'])) ? sanitize_text_field(wp_unslash($_REQUEST['orderby'])) : '';
		$order = (isset($_REQUEST['order'])) ? sanitize_text_field(wp_unslash($_REQUEST['order'])) : '';
		if (!empty($orderby)) {
			$sql .= ' ORDER BY ' . esc_sql($orderby);
			$sql .= !empty($order) ? ' ' . esc_sql($order) : ' ASC';
		} else {
			$sql .= ' ORDER BY createtime DESC';
		}

		$sql .= $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, ($page_number - 1) * $per_page);

		$result = $wpdb->get_results($sql, 'ARRAY_A');

		return $result;
	}

	public function get_columns()
	{
		$columns = array(
			'cb'        => '<input type="checkbox" />', // Render a checkbox instead of text.
			'trade_no'		=> '订单号',
			'user'	    	=> '用户',
			'goods'			=> '商品',
			'remark'		=> '备注',
			'addressee'		=> '收件人',
			'express'		=> '快递',
			'status'		=> '状态',
			'createtime'	=> '创建时间'
		);

		return $columns;
	}

	protected function get_sortable_columns()
	{
		$sortable_columns = array(
			'createtime'  => array('createtime', false),
		);

		return $sortable_columns;
	}

	protected function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'trade_no':
			case 'remark':
				return $item[$column_name];
			default:
				return print_r($item, true); // Show the whole array for troubleshooting purposes.
		}
	}

	protected function column_cb($item)
	{
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			'order_ids',  				// Let's simply repurpose the table's singular label ("movie").
			$item['id']                 // The value of the checkbox should be the record's ID.
		);
	}

	protected function column_user($item)
	{
		$nickname = get_user_meta($item['user_id'], 'nickname', true);
		return $nickname;
	}

	protected function column_goods($item)
	{
		$goods_list = unserialize($item['goods_list']);

		$content = "<table>";
		foreach ($goods_list as $goods) {
			$content .= "<tr>";
			$content .= "<td><img src='" . $goods['thumb'] . "' style='width:48px;height:48px;'/></td>";
			$content .= "<td>" . $goods['name'] . "-";
			$content .= "" . $goods['price'] . "元 X " . $goods['count'] . "</td>";
			$content .= "</tr>";
		}
		$content .= "</table>";

		return $content;
	}

	protected function column_addressee($item)
	{
		$value = "<div>收件人：" . $item['addressee'] . "</div>";
		$value .= "<div>手机号：" . $item['mobile'] . "</div>";
		$value .= "<div>地址：" . $item['address'] . "</div>";
		return $value;
	}

	protected function column_express($item)
	{
		$value = "<div>" . $item['express_type'] . "</div>";
		$value .= "<div>快递单号：" . $item['express_no'] . "</div>";

		$page = (isset($_REQUEST['page'])) ? sanitize_text_field(wp_unslash($_REQUEST['page'])) : '';

		// Build edit row action.
		$edit_query_args = array(
			'page'   => $page,
			'action' => 'edit',
			'id'  => $item['id'],
		);

		$actions['edit'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url(wp_nonce_url(add_query_arg($edit_query_args, 'admin.php'), 'edit_' . $item['id'])),
			'编辑'
		);

		$value .= $this->row_actions($actions);

		return $value;
	}

	protected function column_status($item)
	{
		$status = '未知';
		if ($item['paytime']) {
			if ($item['express_type'] && $item['express_no']) {
				if ($item['confirmtime']) {
					$status = '已确认收货';
				} else {
					$status = '待收货';
				}
			} else {
				$status = '<span style="color:#CC0000">待发货</span>';
			}
		} else {
			if ($item['canceltime']) {
				$status = '<span style="color:#CCCCCC">已取消</span>';
			} else {
				$status = '待付款';
			}
		}

		return $status;
	}

	protected function column_createtime($item)
	{
		return wp_date("Y-m-d H:i:s", $item['createtime']);
	}

	protected function get_bulk_actions()
	{
		$actions = array(
			'bulk_delete' => '删除',
		);

		return $actions;
	}

	protected function process_bulk_action()
	{
		$action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : '';
		if ('bulk_delete' == $action) {
			if (isset($_GET['order_ids'])) {
				$order_ids = $_GET['order_ids'];
				global $wpdb;
				$table_shop_user_order = $wpdb->prefix . 'zhuige_shop_user_order';
				foreach ($order_ids as $id) {
					$wpdb->delete(
						$table_shop_user_order,
						['id' => $id],
						['%d']
					);
				}
			}
		}
	}

	public function prepare_items($search = '')
	{
		$this->process_bulk_action();

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$per_page = 10;
		$current_page = $this->get_pagenum();
		$total_items = $this->record_count($search);

		$this->items = $this->get_datas($per_page, $current_page, $search);

		$this->set_pagination_args([
			'total_items' => $total_items, // WE have to calculate the total number of items.
			'per_page'    => $per_page // WE have to determine how many items to show on a page.
		]);
	}

	public function record_count($search = '')
	{
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}zhuige_shop_user_order WHERE 1=1";

		if ($search) {
			$sql .= " AND `trade_no` LIKE '%" . esc_sql($search) . "%'";
		}

		$sql = $this->parseZGZT($sql);

		return $wpdb->get_var($sql);
	}

	public function parseZGZT($sql)
	{
		$zgzt = isset($_GET['zgzt']) ? sanitize_text_field(wp_unslash($_GET['zgzt'])) : '';
		if ($zgzt == 'dfh') {
			$sql .= " AND paytime>0 AND (express_type='' OR express_no='')";
		} else if ($zgzt == 'dsh') {
			$sql .= " AND paytime>0 AND express_type!='' AND express_no!='' AND confirmtime=0";
		} else if ($zgzt == 'ysh') {
			$sql .= " AND paytime>0 AND express_type!='' AND express_no!='' AND confirmtime>0";
		} else if ($zgzt == 'dfk') {
			$sql .= " AND paytime=0 AND canceltime=0";
		} else if ($zgzt == 'yqx') {
			$sql .= " AND canceltime>0";
		}

		return $sql;
	}

	public function views()
	{
		$views = array();
		$current = (!empty($_REQUEST['zgzt']) ? sanitize_text_field(wp_unslash($_REQUEST['zgzt'])) : 'all');

		//All link
		$class = ($current == 'all' ? ' class="current"' : '');
		$all_url = remove_query_arg('zgzt');
		$views['all'] = "<a href='{$all_url}' {$class} >全部</a>";

		$foo_status = array(
			'dfk' => '待付款',
			'dfh' => '待发货',
			'dsh' => '待收货',
			'ysh' => '已收货',
			'yqx' => '已取消',
		);

		foreach ($foo_status as $zgzt => $title) {
			$class = ($current == $zgzt ? ' class="current"' : '');
			$foo_url = add_query_arg('zgzt', $zgzt);
			$views[$zgzt] = "<a href='{$foo_url}' {$class} >{$title}</a>";
		}

		return $views;
	}
}