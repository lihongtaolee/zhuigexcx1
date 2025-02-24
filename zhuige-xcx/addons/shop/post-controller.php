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

class ZhuiGe_Shop_Post_Controller extends Shop_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'post';
		$this->routes = [
			'get_posts' => 'get_posts',
			'get_post' => 'get_post',
		];
	}

	/**
	 * 文章列表
	 */
	public function get_posts($request)
	{
		$offset = (int)($this->param_value($request, 'offset', 0));
		$cat = (int)($this->param_value($request, 'cat', 0));
		$search = $this->param_value($request, 'search', '');

		$args = ["post_type" => "zhuige_shop_post", 'orderby' => 'date', 'posts_per_page' => 10, 'offset' => $offset];
		if ($cat) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'zhuige_shop_post_cat',
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
		$posts_array = [];
		foreach ($posts as $post) {
			$data = ["id" => $post->ID];
			$data["title"] = $post->post_title;
			$data["thumbnail"] = $this->get_post_thumbnail($post->ID);
			$data["excerpt"] = $this->get_post_excerpt($post);
			$data["time"] = $post->post_date;
			$data["view_count"] = (int)get_post_meta($post->ID, 'zhuige_view_count', true);
			$posts_array[] = $data;
		}

		$result = ["posts" => $posts_array];
		return $this->success($result);
	}

	/**
	 * 文章详情
	 */
	public function get_post($request)
	{
		$post_id = (int)($this->param_value($request, 'post_id'));

		$post = get_post($post_id);
		if (empty($post)) {
			return $this->error('文章不存在');
		}

		$data = ["id" => $post->ID];
		$data["title"] = $post->post_title;
		$data["content"] = apply_filters('the_content', $post->post_content);
		$data["time"] = $post->post_date;
		$data["view_count"] = (int)get_post_meta($post->ID, 'zhuige_view_count', true);

		$result = ["post" => $data];
		return $this->success($result);
	}
}