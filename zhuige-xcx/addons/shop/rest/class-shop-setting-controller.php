<?php

/**
 * 追格小程序 - 商城模块
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class Shop_Setting_Controller extends Shop_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'shop';
		$this->routes = [
			''      => ['callback' => 'get_home', 'method' => 'GET'],
			'mine'  => ['callback' => 'get_mine', 'method' => 'GET'],
			'login' => ['callback' => 'get_login', 'method' => 'GET'],
			'logout'=> ['callback' => 'get_logout', 'method' => 'GET'],
			'search'=> ['callback' => 'get_search', 'method' => 'GET'],
			'index' => ['callback' => 'get_home', 'method' => 'GET'],
			'last'  => ['callback' => 'get_last', 'method' => 'GET']
		];
	}

	/**
	 * 获取配置 首页
	 */
	public function get_home($request)
	{
		error_log('get_home 方法被调用');
		$data = [];

		// 小程序名称
		$data['title'] = $this->get_option_value('basic_title', '追格商城小程序');
		error_log('get_home - title: ' . $data['title']);

		// 描述
		$data['desc'] = $this->get_option_value('basic_desc', '');
		error_log('get_home - desc: ' . $data['desc']);

		// 背景图
		$home_bg = $this->get_option_value('home_bg');
		$data['background'] = $this->get_option_image_url($home_bg, 'default_bg.jpg');
		error_log('get_home - background: ' . $data['background']);

		// 首页分享标题
		$data['home_title'] = $this->get_option_value('home_title');
		error_log('get_home - home_title: ' . $data['home_title']);

		// 首页分享头图
		$home_thumb = $this->get_option_value('home_thumb');
		if ($home_thumb && isset($home_thumb['url'])) {
			$data['thumb'] = $this->convert_to_relative($home_thumb['url']);
		}
		error_log('get_home - thumb: ' . (isset($data['thumb']) ? $data['thumb'] : '无'));

		// 幻灯片
		$slides_org = $this->get_option_value('home_slide');
		$slides = [];
		if (is_array($slides_org)) {
			foreach ($slides_org as $item) {
				if ($item['switch'] && isset($item['image']['url']) && $item['image']['url']) {
					$slides[] = [
						'title' => $item['title'],
						'image' => $this->convert_to_relative($item['image']['url']),
						'link'  => $item['link'],
					];
				}
			}
		}
		$data['slides'] = $slides;
		error_log('get_home - slides: ' . print_r($slides, true));

		// 图标导航
		$icon_nav_org = $this->get_option_value('home_nav');
		$icon_navs = [];
		if (is_array($icon_nav_org)) {
			foreach ($icon_nav_org as $item) {
				if ($item['switch'] && isset($item['image']['url']) && $item['image']['url']) {
					$icon_navs[] = [
						'image' => $this->convert_to_relative($item['image']['url']),
						'link'  => $item['link'],
						'title' => $item['title'],
					];
				}
			}
		}
		$data['icon_navs'] = $icon_navs;
		error_log('get_home - icon_navs: ' . print_r($icon_navs, true));

		// 好货推荐
		$home_rec = $this->get_option_value('home_rec');
		if ($home_rec && $home_rec['switch']) {
			if (empty($home_rec['title'])) {
				$home_rec['title'] = '好货甄选';
			}
			$posts = [];
			if (!empty($home_rec['goods_ids'])) {
				$args = [
					'post_type' => 'jq_goods',
					'post__in'  => $home_rec['goods_ids'],
					'orderby'   => 'post__in',
					'posts_per_page' => -1,
					'ignore_sticky_posts' => 1,
				];
				$query = new WP_Query();
				$result = $query->query($args);
				foreach ($result as $item) {
					$posts[] = [
						'id' => $item->ID,
						'title' => $item->post_title,
						'thumbnail' => $this->convert_to_relative($this->get_one_post_thumbnail($item->ID))
					];
				}
			}
			$home_rec['posts'] = $posts;
			$data['home_rec'] = $home_rec;
			error_log('get_home - home_rec: ' . print_r($home_rec, true));
		}

		// 分类导航 tab
		$goods_cat = $this->get_option_value('goods_cat');
		$term_args = [
			'taxonomy' => 'jq_goods_cat',
			'hide_empty' => false,
		];
		if (is_array($goods_cat) && count($goods_cat) > 0) {
			$term_args['include'] = $goods_cat;
			$term_args['orderby'] = 'include';
		}
		$terms = get_terms($term_args);
		$cats = [['id' => 0, 'name' => '最新']];
		foreach ($terms as $term) {
			$cats[] = [
				'id' => $term->term_id,
				'name' => $term->name
			];
		}
		$data['cats'] = $cats;
		error_log('get_home - cats: ' . print_r($cats, true));

		// 弹框广告
		$home_ad_pop = $this->get_option_value('home_ad_pop');
		if ($home_ad_pop && $home_ad_pop['switch'] && isset($home_ad_pop['image']['url']) && $home_ad_pop['image']['url']) {
			$data['pop_ad'] = [
				'image' => $this->convert_to_relative($home_ad_pop['image']['url']),
				'link'  => $home_ad_pop['link'],
				'interval' => $home_ad_pop['interval'],
			];
		}
		error_log('get_home - pop_ad: ' . (isset($data['pop_ad']) ? print_r($data['pop_ad'], true) : '无'));

		// 获取最新商品
		$args = [
			'post_type' => 'jq_goods',
			'posts_per_page' => 10,
			'ignore_sticky_posts' => 1,
		];
		$query = new WP_Query();
		$result = $query->query($args);
		$products = [];
		foreach ($result as $item) {
			// 获取商品元数据
			$goods_meta = get_post_meta($item->ID, 'zhuige-jq_goods-opt', true);
			$price = isset($goods_meta['price']) ? $goods_meta['price'] : '';
			
			$products[] = [
				'id' => $item->ID,
				'name' => $item->post_title,
				'image' => $this->convert_to_relative($this->get_one_post_thumbnail($item->ID)),
				'price' => $price ? '¥' . $price : '免费'
			];
		}
		$data['goods_list'] = $products;
		error_log('get_home - goods_list count: ' . count($products));

		// 返回完整的数据
		error_log('get_home - 返回完整数据: ' . print_r($data, true));
		return $this->success($data);
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
		// 修正默认图片路径
		return '/wp-content/plugins/zhuige-xcx/public/images/zhuige.jpg';
	}

	/**
	 * 获取配置
	 */
	private function get_option_value($key, $default = '')
	{
		static $options = false;
		if (!$options) {
			$options = get_option('zhuige_shop_options');
		}
		if (isset($options[$key]) && !empty($options[$key])) {
			return $options[$key];
		}
		return $default;
	}

	/**
	 * 图片配置项 URL（转换为相对路径）
	 */
	private function get_option_image_url($image, $default = '')
	{
		if ($image && is_array($image) && isset($image['url']) && $image['url']) {
			return $this->convert_to_relative($image['url']);
		}
		if ($default) {
			// 返回正确的默认图片URL
			return '/wp-content/plugins/zhuige-xcx/public/images/' . $default;
		}
		return '';
	}

	/**
	 * 将URL转换为完整URL（确保包含域名）
	 */
	private function convert_to_relative($url)
	{
		// 如果URL已经是完整URL，则直接返回
		if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
			return $url;
		}
		
		// 如果是相对路径，添加域名前缀
		$parsed = parse_url($url);
		$path = isset($parsed['path']) ? $parsed['path'] : $url;
		
		// 确保路径以/开头
		if (strpos($path, '/') !== 0) {
			$path = '/' . $path;
		}
		
		// 获取当前站点URL
		$site_url = get_site_url();
		
		// 返回完整URL
		return $site_url . $path;
	}

	/**
	 * 获取最新商品列表
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
					'terms' => $cat_id
				]
			];
		}

		$query = new WP_Query();
		$result = $query->query($args);
		$posts = [];
		foreach ($result as $item) {
			$goods_meta = get_post_meta($item->ID, 'zhuige-jq_goods-opt', true);
			$price = isset($goods_meta['price']) ? $goods_meta['price'] : '';
			$orig_price = isset($goods_meta['orig_price']) ? $goods_meta['orig_price'] : '';
			$badge = isset($goods_meta['badge']) ? $goods_meta['badge'] : '';
			
			$posts[] = [
				'id' => $item->ID,
				'title' => $item->post_title,
				'thumbnail' => $this->convert_to_relative($this->get_one_post_thumbnail($item->ID)),
				'price' => $price,
				'orig_price' => $orig_price,
				'badge' => $badge
			];
		}

		return $this->success([
			'list' => $posts,
			'more' => count($posts) == 10 ? 'more' : 'nomore'
		]);
	}
}
