<template>
	<view class="content">

		<!-- 大图轮播 -->
		<view v-if="goods && goods.slide && goods.slide.length>0" class="zhuige-detail-img">
			<swiper indicator-dots="true" autoplay="autoplay" circular="ture" indicator-color="rgba(255,255,255, 0.3)"
				indicator-active-color="rgba(255,255,255, 0.8)" interval="5000" duration="150" easing-function="linear">
				<swiper-item v-for="(item, index) in goods.slide" :key="index" @click="showSlides(index)">
					<image mode="aspectFill" :src="item.image.url"></image>
				</swiper-item>
			</swiper>
		</view>

		<view v-if="goods" class="zhuige-detail-title">
			<view class="goods-name">{{goods.title}}</view>
			<view class="goods-intro">
				<text v-if="goods.badge.length>0" class="mark">{{goods.badge}}</text>
				<text>{{goods.excerpt}}</text>
			</view>
			<view class="goods-option">
				<view class="price">
					<text>￥</text>
					<text>{{goods.price}}</text>
					<text>￥{{goods.orig_price}}</text>
				</view>
				<view class="numbers">
					<text>库存 {{goods.stock}}</text>
					<text>销量 {{goods.quantity}}</text>
				</view>
			</view>
		</view>

		<view v-if="goods" class="zhuige-goods-detail">
			<view class="goods-title">产品详情</view>
			<view class="goods-detail-view">
				<mp-html :content="goods.content" />
			</view>
		</view>
		
		<view v-if="goods && goods.recs" class="zhuige-goods-recs">
			<view class="recs-title">相关推荐</view>
			<view class="zhuige-goods-list">
				<view v-for="(item,index) in goods.recs" :key="index"
					@click="clickLink('/pages/shop/detail/detail?goods_id=' + item.id)" class="zhuige-goods">
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
		</view>

		<view class="zhuige-comment-list">
			<view class="zhuige-comment-header">
				<text>用户评论</text>
				<uni-icons @click="clickLink('/pages/shop/comment/comment?goods_id=' + goods_id)" type="chatbubble"
					size="24"></uni-icons>
			</view>

			<template v-if="comments && comments.length>0">
				<view v-for="(item,index) in comments" :key="index" class="zhuige-comment-item">
					<view>
						<image mode="aspectFill" class="avatar" :src="item.user.avatar"></image>
					</view>
					<view class="content-list">
						<view>{{item.user.name}}</view>
						<uni-rate :value="item.rate" :readonly="true" />
						<view class="content">{{item.content}}</view>
						<view class="time">{{item.time}}</view>
					</view>
				</view>
				<uni-load-more :status="loadMore"></uni-load-more>
			</template>
			<template v-else>
				<zhuige-nodata v-if="loaded"></zhuige-nodata>
			</template>
		</view>

		<view class="zhuige-goods-bar">
			<view @click="clickCart" class="zhuige-goods-cart-btn">
				<uni-icons type="cart" size="30" color="#ff4400"></uni-icons>
				<view>{{getCartCount}}</view>
			</view>
			<view class="zhuige-goods-btn">
				<view @click="cartGoodsAdd({goods_id: goods.id, count: 1})">加入购物车</view>
				<view @click="clickLink('/pages/shop/order_confirm/order_confirm?goods_id=' + goods.id)">立即购买</view>
			</view>
		</view>
	</view>
</template>

<script>
import Util from '@/utils/util';
import Alert from '@/utils/alert';
import Api from '@/utils/api';
import Rest from '@/utils/rest';

import {
	mapGetters,
	mapMutations
} from 'vuex'
import store from '@/store/index.js'

export default {
	components: {},
	
	data() {
		return {
			goods_id: 0,
			goods: undefined,

			comments: [],
			loadMore: 'more',
			loaded: false,
		}
	},

	computed: {
		...mapGetters([
			'getCartCount'
		])
	},

	onLoad(options) {
		if (!options.goods_id) {
			uni.reLaunch({
				url: '/pages/shop/index/index'
			})
			return;
		}
		this.goods_id = options.goods_id;

		this.loadGoods();
		this.loadComments(true);
	},

	onPullDownRefresh() {
		this.loadGoods();
		this.loadComments(true);
	},

	onReachBottom() {
		if (this.loadMore == 'more') {
			this.loadComments(false);
		}
	},

	onShareAppMessage() {
		return {
			title: this.goods.title + '_' + getApp().globalData.appName,
			path: 'pages/shop/detail/detail?goods_id=' + this.goods_id
		};
	},

	methods: {
		...mapMutations(['cartGoodsAdd']),

		clickLink(link) {
			Util.openLink(link);
		},

		showSlides(index) {
			let urls = [];
			for (let i = 0; i < this.goods.slide.length; i++) {
				urls.push(this.goods.slide[i].image.url);
			}
			uni.previewImage({
				urls: urls,
				current: index
			});
		},

		clickCart() {
			Util.openLink('/pages/shop/cart/cart');
		},

		loadGoods() {
			Rest.post(Api.URL('shop', 'goods_detail'), {
				goods_id: this.goods_id
			}).then(res => {
				this.goods = res.data;
				uni.stopPullDownRefresh();
			});
		},

		loadComments(refresh) {
			if (refresh) {
				this.comments = [];
				this.loadMore = 'more';
				this.loaded = false;
			}

			if (this.loadMore !== 'more') {
				return;
			}

			this.loadMore = 'loading';

			Rest.post(Api.URL('shop', 'goods_comments'), {
				goods_id: this.goods_id,
				offset: this.comments.length
			}).then(res => {
				if (res.data.length > 0) {
					this.comments = this.comments.concat(res.data);
					this.loadMore = 'more';
				} else {
					this.loadMore = 'noMore';
				}
				this.loaded = true;
				uni.stopPullDownRefresh();
			});
		}
	}
}
</script>

<style lang="scss" scoped>
@import "@/style/main.css";
</style>