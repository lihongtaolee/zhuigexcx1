<template>
	<view class="content"
		:style="background ? 'background: url(' + background + ') no-repeat top; background-size: 100% auto;' : ''">

		<uni-nav-bar :title="title" :color="nav_color" :background-color="nav_bgcolor" :border="false" :fixed="true" :statusBar="true" :placeholder="false">
			<!-- 顶部小搜索框 -->
			<view slot="left" @click="clickLink('/pages/shop/search/search')">
				<view class="zhuige-nav-search">
					<uni-icons type="search" size="20" :color="nav_color"></uni-icons>
					<text :style="{color:nav_color}">关键词...</text>
				</view>
			</view>
		</uni-nav-bar>

		<view class="zhuige-main-top">
			<!-- 大图轮播 -->
			<view v-if="slides && slides.length>0" class="zhuige-swiper">
				<swiper indicator-dots="true" autoplay="autoplay" circular="ture"
					indicator-color="rgba(255,255,255, 0.3)" indicator-active-color="rgba(255,255,255, 0.8)"
					interval="5000" duration="150" easing-function="linear">
					<swiper-item v-for="(slide, index) in slides" :key="index" @click="clickLink(slide.link)">
						<view class="zhuige-swiper-title">{{slide.title}}</view>
						<image :src="slide.image" mode="aspectFill"></image>
					</swiper-item>
				</swiper>
			</view>

			<!-- 自定义图标 -->
			<view v-if="icon_navs && icon_navs.length>0" class="zhuige-icon-menu">
				<view v-for="(icon, index) in icon_navs" :key="index" @click="clickLink(icon.link)">
					<image :src="icon.image" mode="aspectFill"></image>
					<text>{{icon.title}}</text>
				</view>
			</view>
		</view>

		<!-- 滑动推荐 -->
		<view v-if="home_rec" class="zhuige-recom">
			<view class="zhuige-title">
				<view>{{home_rec.title}}</view>
				<text>滑动查看</text>
			</view>
			<view v-if="home_rec.posts && home_rec.posts.length>0" class="zhuige-scroll">
				<scroll-view scroll-x="true">
					<view v-for="(post,index) in home_rec.posts" :key="index"
						@click="clickLink('/pages/shop/detail/detail?goods_id=' + post.id)" class="zhuige-scroll-block">
						<image :src="post.thumbnail" mode="aspectFill"></image>
						<view>{{post.title}}</view>
					</view>
				</scroll-view>
			</view>
		</view>

		<view class="zhuige-goods-group">
			<!-- 滑动导航 -->
			<view class="zhuige-goods-nav">
				<view class="zhuige-goods-scroll">
					<scroll-view>
						<view v-for="(item,index) in cats" :key="index" :class="cat_id==item.id?'active':''"
							@click="clickTab(item.id)">
							{{item.name}}
						</view>
					</scroll-view>
				</view>
				<view @click="clickCategory" class="zhuige-goods-more">
					<uni-icons type="bars" size="24"></uni-icons>
				</view>
			</view>

			<!-- 商品列表 -->
			<template v-if="goods_list.length>0">
				<view class="zhuige-goods-list">
					<view v-for="(item,index) in goods_list" :key="index"
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
import Util from '@/utils/util';
import Api from '@/utils/api';
import Rest from '@/utils/rest';

export default {
	data() {
		return {
			title: '',
			nav_color: 'rgb(255, 255, 255)',
			nav_bgcolor: 'rgba(255, 255, 255, 0)',
			background: undefined,
			slides: [],
			icon_navs: [],
			home_rec: undefined,
			cat_id: undefined,
			cats: [],
			goods_list: [],
			loaded: false,
			loadMore: 'more',
			page: 1,
			pop_ad: undefined,
		}
	},

	computed: {
		...mapGetters([
			'getCartCount'
		])
	},

	onLoad() {
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
		if (this.loadMore === 'more') {
			this.page++;
			this.loadGoods();
		}
	},

	methods: {
		clickLink(link) {
			Util.openLink(link);
		},

		loadSetting() {
			Rest.post(Api.URL('shop', 'home')).then(res => {
				this.background = res.data.background;
				this.slides = res.data.slides;
				this.icon_navs = res.data.icon_navs;
				this.home_rec = res.data.home_rec;
			});
		},

		loadCats() {
			Rest.post(Api.URL('shop', 'cats')).then(res => {
				this.cats = res.data;
			});
		},

		clickTab(cat_id) {
			this.cat_id = cat_id;
			this.page = 1;
			this.goods_list = [];
			this.loadMore = 'more';
			this.loadGoods();
		},

		clickCategory() {
			Util.openLink('/pages/shop/category/category');
		},

		loadGoods() {
			if (this.loadMore !== 'more') {
				return;
			}

			this.loadMore = 'loading';

			let data = {
				cat_id: this.cat_id,
				page: this.page
			};

			Rest.post(Api.URL('shop', 'goods'), data).then(res => {
				if (res.data.length > 0) {
					this.goods_list = this.goods_list.concat(res.data);
					this.loadMore = 'more';
				} else {
					this.loadMore = 'noMore';
				}
				this.loaded = true;
			});
		},

		clickPopAd() {
			if (this.pop_ad.link) {
				Util.openLink(this.pop_ad.link);
			}
		},

		clickPopAdClose() {
			this.pop_ad = undefined;
		}
	}
}
</script>

<style lang="scss">
@import '@/style/main.css';

.zhugie-pop-cover {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0, 0, 0, 0.5);
	z-index: 999;
	display: flex;
	align-items: center;
	justify-content: center;

	.zhuige-pop-box {
		position: relative;
		width: 80%;
		max-width: 600rpx;

		image {
			width: 100%;
			height: auto;
		}

		view {
			position: absolute;
			top: -20rpx;
			right: -20rpx;
		}
	}
}

.zhuige-main-top {
	padding: 20rpx;
}

.zhuige-recom {
	padding: 20rpx;

	.zhuige-title {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 20rpx;

		view {
			font-size: 32rpx;
			font-weight: bold;
			color: #333333;
		}

		text {
			font-size: 24rpx;
			color: #999999;
		}
	}

	.zhuige-scroll {
		scroll-view {
			white-space: nowrap;
		}

		.zhuige-scroll-block {
			display: inline-block;
			width: 240rpx;
			margin-right: 20rpx;

			image {
				width: 240rpx;
				height: 240rpx;
				border-radius: 8rpx;
			}

			view {
				font-size: 28rpx;
				color: #333333;
				margin-top: 10rpx;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
			}
		}
	}
}

.zhuige-goods-group {
	padding: 20rpx;

	.zhuige-goods-nav {
		display: flex;
		align-items: center;
		margin-bottom: 20rpx;

		.zhuige-goods-scroll {
			flex: 1;
			overflow: hidden;

			scroll-view {
				white-space: nowrap;

				view {
					display: inline-block;