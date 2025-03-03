<?php

/**
 * 追格小程序 - 商城模块
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class Shop_Setting_Controller extends ZhuiGe_Xcx_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'shop/setting';
		$this->routes = [
			'' => ['callback' => 'get_home', 'method' => 'GET'],
			'mine' => ['callback' => 'get_mine', 'method' => 'GET'],
			'login' => ['callback' => 'get_login', 'method' => 'GET'],
			'logout' => ['callback' => 'get_logout', 'method' => 'GET'],
			'search' => ['callback' => 'get_search', 'method' => 'GET'],
			'index' => ['callback' => 'get_home', 'method' => 'GET']
		];
	}

	/**
	 * 获取配置 首页
	 */
	public function get_home($request)
	{
		$data = [];

		//小程序名称
		$data['title'] = $this->get_option_value('basic_title', '追格商城小程序');

		//描述
		$data['desc'] = $this->get_option_value('basic_desc', '');

		//背景图
		$home_bg = $this->get_option_value('home_bg');
		$data['background'] = $this->get_option_image_url($home_bg, 'default_bg.jpg');

		// 首页分享标题
		$data['home_title'] = $this->get_option_value('home_title');

		//首页分享头图
		$home_thumb = $this->get_option_value('home_thumb');
		if ($home_thumb && $home_thumb['url']) {
			$data['thumb'] = $home_thumb['url'];
		}

		// 幻灯片
		$slides_org = $this->get_option_value('home_slide');
		$slides = [];
		if (is_array($slides_org)) {
			foreach ($slides_org as $item) {
				if ($item['switch'] && $item['image'] && $item['image']['url']) {
					$slides[] = [
						'title' => $item['title'],
						'image' => $item['image']['url'],
						'link' => $item['link'],
					];
				}
			}
		}
		$data['slides'] = $slides;

		//图标导航
		$icon_nav_org = $this->get_option_value('home_nav');
		$icon_navs = [];
		if (is_array($icon_nav_org)) {
			foreach ($icon_nav_org as $item) {
				if ($item['switch'] && $item['image'] && $item['image']['url']) {
					$icon_navs[] = [
						'image' => $item['image']['url'],
						'link' => $item['link'],
						'title' => $item['title'],
					];
				}
			}
		}
		$data['icon_navs'] = $icon_navs;

		//好货推荐
		$home_rec = $this->get_option_value('home_rec');
		if ($home_rec && $home_rec['switch']) {
			if (empty($home_rec['title'])) {
				$home_rec['title'] = '好货甄选';
			}

			$posts = [];
			if (!empty($home_rec['goods_ids'])) {
				$args = [
					'post_type' => 'jq_goods',
					'post__in' => $home_rec['goods_ids'],
					'orderby' => 'post__in',
					'posts_per_page' => -1,
					'ignore_sticky_posts' => 1,
				];

				$query = new WP_Query();
				$result = $query->query($args);
				foreach ($result as $item) {
					$posts[] = [
						'id' => $item->ID,
						'title' => $item->post_title,
						'thumbnail' => $this->get_one_post_thumbnail($item->ID)
					];
				}
			}
			unset($rec_head['goods_ids']);
			$home_rec['posts'] = $posts;

			$data['home_rec'] = $home_rec;
		}

		//分类导航tab
		$goods_cat = $this->get_option_value('goods_cat');
		$term_args = [
			'taxonomy' => 'jq_goods_cat',
			'hide_empty' => false,
			// 'parent'   => 0,
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

		//弹框广告
		$home_ad_pop = $this->get_option_value('home_ad_pop');
		if ($home_ad_pop && $home_ad_pop['switch'] && $home_ad_pop['image'] && $home_ad_pop['image']['url']) {
			$data['pop_ad'] = [
				'image' => $home_ad_pop['image']['url'],
				'link' => $home_ad_pop['link'],
				'interval' => $home_ad_pop['interval'],
			];
		}

		// 转换为前端需要的格式
		$response = [
			'banners' => $slides,
			'navs' => $icon_navs,
			'products' => []
		];

		// 获取最新商品
		$args = [
			'post_type' => 'jq_goods',
			'posts_per_page' => 10,
			'ignore_sticky_posts' => 1,
		];

		$query = new WP_Query();
		$result = $query->query($args);
		foreach ($result as $item) {
			$price = get_post_meta($item->ID, 'zhuige_goods_price', true);
			$response['products'][] = [
				'id' => $item->ID,
				'name' => $item->post_title,
				'image' => $this->get_one_post_thumbnail($item->ID),
				'price' => $price ? '¥' . $price : '免费'
			];
		}

		return $this->success($response);
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
	 * 图片配置项url
	 */
	private function get_option_image_url($image, $default = '')
	{
		if ($image && isset($image['url']) && $image['url']) {
			return $image['url'];
		}

		if ($default) {
			return ZHUIGE_XCX_BASE_URL . 'addons/shop/public/images/' . $default;
		}

		return '';
	}
}