<template>
	<view class="content" :style="background ? 'background: url(' + background + ') no-repeat top; background-size: 100% auto;' : ''">
		<uni-nav-bar :title="title" :color="nav_color" :background-color="nav_bgcolor" :border="false" :fixed="true" :statusBar="true" :placeholder="false">
			<view slot="left" @click="clickLink('/pages/shop/search')">
				<view class="zhuige-nav-search">
					<uni-icons type="search" size="20" :color="nav_color"></uni-icons>
					<text :style="{color:nav_color}">关键词...</text>
				</view>
			</view>
		</uni-nav-bar>

		<view class="zhuige-main-top">
			<view v-if="slides && slides.length>0" class="zhuige-swiper">
				<swiper indicator-dots="true" autoplay="autoplay" circular="ture" indicator-color="rgba(255,255,255, 0.3)" indicator-active-color="rgba(255,255,255, 0.8)" interval="5000" duration="150" easing-function="linear">
					<swiper-item v-for="(slide, index) in slides" :key="index" @click="clickLink(slide.link)">
						<view class="zhuige-swiper-title">{{slide.title}}</view>
						<image :src="slide.image" mode="aspectFill"></image>
					</swiper-item>
				</swiper>
			</view>

			<view v-if="icon_navs && icon_navs.length>0" class="zhuige-icon-menu">
				<view v-for="(icon, index) in icon_navs" :key="index" @click="clickLink(icon.link)">
					<image :src="icon.image" mode="aspectFill"></image>
					<text>{{icon.title}}</text>
				</view>
			</view>
		</view>

		<view v-if="home_rec" class="zhuige-recom">
			<view class="zhuige-title">
				<view>{{home_rec.title}}</view>
				<text>滑动查看</text>
			</view>
			<view v-if="home_rec.posts && home_rec.posts.length>0" class="zhuige-scroll">
				<scroll-view scroll-x="true">
					<view v-for="(post,index) in home_rec.posts" :key="index" @click="clickLink('/pages/shop/detail?goods_id=' + post.id)" class="zhuige-scroll-block">
						<image :src="post.thumbnail" mode="aspectFill"></image>
						<view>{{post.title}}</view>
					</view>
				</scroll-view>
			</view>
		</view>

		<view class="zhuige-goods-group">
			<view class="zhuige-goods-nav">
				<view class="zhuige-goods-scroll">
					<scroll-view>
						<view v-for="(item,index) in cats" :key="index" :class="cat_id==item.id?'active':''" @click="clickTab(item.id)">
							{{item.name}}
						</view>
					</scroll-view>
				</view>
				<view @click="clickCategory" class="zhuige-goods-more">
					<uni-icons type="bars" size="24"></uni-icons>
				</view>
			</view>

			<template v-if="goods_list.length>0">
				<view class="zhuige-goods-list">
					<view v-for="(item,index) in goods_list" :key="index" @click="clickLink('/pages/shop/detail?goods_id=' + item.id)" class="zhuige-goods">
						<image :src="item.thumbnail" mode="aspectFill"></image>
						<view class="zhuige-goods-text">
							<view class="zhuige-goods-title">
								<text v-if="item.badge" class="mark">{{item.badge}}</text>
								<text>{{item.title}}</text>
							</view>
							<view class="zhuige-goods-price">
								<view class="promotion">
									<text>￥</text>
									<text>{{item.price}}</text>
								</view>
								<view class="original">
									<text>￥{{item.orig_price}}</text>
								</view>
							</view>
						</view>
					</view>
				</view>
				<uni-load-more :status="loadMore"></uni-load-more>
			</template>
			<template v-else>
				<zhuige-nodata v-if="loaded"></zhuige-nodata>
			</template>
		</view>
		
		<view v-if="pop_ad" class="zhugie-pop-cover">
			<view @click="clickPopAd" class="zhuige-pop-box">
				<image mode="aspectFit" :src="pop_ad.image"></image>
				<view>
					<uni-icons @click="clickPopAdClose" type="close" size="32" color="#FFFFFF"></uni-icons>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
/*
 * 追格商城小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/sc.html
 * gitee: https://gitee.com/zhuige_com/zhuige_shop
 * github: https://github.com/zhuige-com/zhuige_shop
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

import Constants from "@/utils/constants.js";
import Util from '@/utils/util';
import Alert from '@/utils/alert';
import Api from '@/utils/api';
import Rest from '@/utils/rest';
import { mapGetters } from 'vuex';
import '@/style/shop.css';

export default {
	components: {
		ZhuigeNodata: () => import('@/components/zhuige-nodata')
	},
	
	data() {
		this.share_title = undefined;
		this.share_thumb = undefined;

		return {
			background: undefined,
			
			title: '',
			nav_color: 'rgb(255, 255, 255)',
			nav_bgcolor: 'rgba(255, 255, 255, 0)',

			slides: [],
			icon_navs: [],
			home_rec: undefined,

			cats: [],
			cat_id: undefined,

			goods_list: [],
			loadMore: 'more',
			loaded: false,
			
			pop_ad: undefined,
		}
	},

	computed: {
		...mapGetters([
			'getCartCount'
		])
	},

	onLoad(options) {
		this.loadSetting();
		this.loadGoods();
	},

	onShow() {
		Util.updateCartBadge(this.getCartCount);
	},
	
	onPageScroll(e) {
		if (e.scrollTop > 20) {
			let nav_opacity = (e.scrollTop - 20) / 255;
			if (nav_opacity <= 1) {
				let factor = 255 * (1 - nav_opacity);
				this.nav_color = `rgb(${factor}, ${factor}, ${factor})`;
				this.nav_bgcolor = `rgba(255, 255, 255, ${nav_opacity})`;
				this.title = '商城';
			} else if (this.nav_color != 'rgb(255, 255, 255)') {
				this.nav_color = 'rgb(0, 0, 0)';
				this.nav_bgcolor = 'rgba(255, 255, 255, 1)';
			}
			uni.setNavigationBarColor({
				frontColor: '#000000',
				backgroundColor: '#ffffff',
			})
		} else {
			this.nav_color = 'rgb(255, 255, 255)';
			this.nav_bgcolor = 'rgba(255, 255, 255, 0)';
			this.title = '';
			uni.setNavigationBarColor({
				frontColor: '#ffffff',
				backgroundColor: '#ffffff'
			})
		}
	},

	onReachBottom() {
		if (this.loadMore == 'more') {
			this.loadGoods();
		}
	},

	methods: {
		loadSetting() {
			let that = this;
			Rest.get(Api.ZHUIGE_SHOP_SETTING_HOME).then(res => {
				that.background = res.data.background;
				that.slides = res.data.slides;
				that.icon_navs = res.data.icon_navs;
				that.home_rec = res.data.home_rec;
				that.cats = res.data.cats;
				that.share_title = res.data.share_title;
				that.share_thumb = res.data.share_thumb;
				that.pop_ad = res.data.pop_ad;
			});
		},

		loadGoods() {
			let that = this;
			let offset = 0;
			if (this.loadMore == 'more') {
				offset = this.goods_list.length;
			} else {
				return;
			}

			this.loadMore = 'loading';
			Rest.get(Api.ZHUIGE_SHOP_GOODS_LAST, {
				offset: offset,
				cat_id: this.cat_id
			}).then(res => {
				if (offset == 0) {
					that.goods_list = res.data.goods;
				} else {
					that.goods_list = that.goods_list.concat(res.data.goods);
				}
				that.loadMore = res.data.more;
				that.loaded = true;
			});
		},

		clickTab(cat_id) {
			this.cat_id = cat_id;
			this.goods_list = [];
			this.loadMore = 'more';