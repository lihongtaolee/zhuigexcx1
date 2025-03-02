<template>
	<view class="page">
		<view class="order-manage">
			<view class="order-tabs">
				<view class="tab-item" :class="{active: cur_tab === ''}" @click="clickTab('')">
					<text>全部</text>
					<text class="count" v-if="all_count > 0">{{all_count}}</text>
				</view>
				<view class="tab-item" :class="{active: cur_tab === 'create'}" @click="clickTab('create')">
					<text>待付款</text>
					<text class="count" v-if="create_count > 0">{{create_count}}</text>
				</view>
				<view class="tab-item" :class="{active: cur_tab === 'pay'}" @click="clickTab('pay')">
					<text>待发货</text>
					<text class="count" v-if="pay_count > 0">{{pay_count}}</text>
				</view>
				<view class="tab-item" :class="{active: cur_tab === 'confirm'}" @click="clickTab('confirm')">
					<text>已完成</text>
					<text class="count" v-if="confirm_count > 0">{{confirm_count}}</text>
				</view>
				<view class="tab-item" :class="{active: cur_tab === 'cancel'}" @click="clickTab('cancel')">
					<text>已取消</text>
					<text class="count" v-if="cancel_count > 0">{{cancel_count}}</text>
				</view>
			</view>

			<view class="order-list" v-if="loaded">
				<view class="order-item" v-for="(order, index) in orders" :key="index">
					<view class="order-header">
						<text class="order-no">订单号：{{order.trade_no}}</text>
						<text class="order-status" v-if="!order.paytime && !order.canceltime">待付款</text>
						<text class="order-status" v-if="order.paytime && !order.confirmtime">待发货</text>
						<text class="order-status" v-if="order.confirmtime">已完成</text>
						<text class="order-status" v-if="order.canceltime">已取消</text>
					</view>

					<view class="order-goods" @click="clickDetail(order)">
						<view class="goods-item" v-for="(goods, gindex) in order.goods" :key="gindex">
							<image class="goods-image" :src="goods.image" mode="aspectFill"></image>
							<view class="goods-info">
								<view class="goods-title">{{goods.title}}</view>
								<view class="goods-price-count">
									<text class="price">￥{{goods.price}}</text>
									<text class="count">x{{goods.count}}</text>
								</view>
							</view>
						</view>
					</view>

					<view class="order-footer">
						<view class="order-total">
							<text>共{{order.goods_count}}件商品 合计：</text>
							<text class="price">￥{{order.price}}</text>
						</view>

						<view class="order-btns">
							<button class="btn-cancel" v-if="!order.paytime && !order.canceltime" @click="clickCancel(order)">取消订单</button>
							<button class="btn-pay" v-if="!order.paytime && !order.canceltime" @click="clickPay(order)">立即支付</button>
							<button class="btn-delete" v-if="order.canceltime || order.confirmtime" @click="clickDelete(order)">删除订单</button>
						</view>
					</view>
				</view>

				<view class="no-more" v-if="loadMore === 'nomore'">没有更多了</view>
			</view>
		</view>
	</view>
</template>

<script>
	import Alert from '@/utils/alert'
	import Rest from '@/utils/rest'
	import Api from '@/utils/api'

	export default {
		data() {
			return {
				cur_tab: '',
				orders: [],
				loadMore: 'loading',
				loaded: false,
				all_count: 0,
				create_count: 0,
				pay_count: 0,
				confirm_count: 0,
				cancel_count: 0
			}
		},

		onLoad() {
			this.loadCount();
			this.loadOrders(true);
		},

		onPullDownRefresh() {
			this.refresh();
		},

		onReachBottom() {
			if (this.loadMore === 'more') {
				this.loadOrders(false);
			}
		},

		methods: {
			/**
			 * 点击 Tab
			 */
			clickTab(tab) {
				if (this.cur_tab === tab) {
					return;
				}

				this.cur_tab = tab;
				this.loadOrders(true);
			},

			/**
			 * 点击 订单详情
			 */
			clickDetail(order) {
				uni.navigateTo({
					url: '/pages/shop/order_detail?order_id=' + order.id
				});
			},

			/**
			 * 点击 取消订单
			 */
			clickCancel(order) {
				uni.showModal({
					title: '提示',
					content: '确定要取消订单吗？',
					success: (res) => {
						if (!res.confirm) {
							return;
						}

						Rest.post(Api.ZHUIGE_SHOP_ORDER_CANCEL, {
							order_id: order.id
						}).then(res => {
							if (res.code == 0) {
								order.canceltime = res.data.canceltime;
								this.loadCount();
							} else {
								Alert.toast(res.msg);
							}
						}, err => {
							console.log(err)
						});
					}
				});
			},

			/**
			 * 点击 删除订单
			 */
			clickDelete(order) {
				uni.showModal({
					title: '提示',
					content: '确定要删除订单吗？',
					success: (res) => {
						if (!res.confirm) {
							return;
						}

						Rest.post(Api.ZHUIGE_SHOP_ORDER_DELETE, {
							order_id: order.id
						}).then(res => {
							if (res.code == 0) {
								let orders = [];
								this.orders.forEach((item, index) => {
									if (item.id != order.id) {
										orders.push(item);
									}
								})
								this.orders = orders;
								this.loadCount();
							} else {
								Alert.toast(res.msg);
							}
						}, err => {
							console.log(err)
						});
					}
				});
			},

			/**
			 * 点击支付
			 */
			clickPay(order) {
				Rest.post(Api.ZHUIGE_SHOP_ORDER_PAY, {
					order_id: order.id
				}).then(res => {
					if (res.code == 0) {
						// #ifdef MP-WEIXIN
						let pay_params = res.data.pay_params;

						// 发起微信支付
						wx.requestPayment({
							timeStamp: pay_params.timeStamp,
							nonceStr: pay_params.nonceStr,
							package: pay_params.package,
							signType: 'MD5',
							paySign: pay_params.paySign,
							success: res3 => {
								Alert.toast('支付成功');

								order.paytime = true;
							},
							fail: res4 => {
								Alert.toast('取消支付');
							},
						});
						// #endif

						// #ifndef MP-WEIXIN
						Alert.toast('平台暂不支持');
						// #endif
					} else {
						Alert.toast(res.msg);
					}
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 刷新
			 */
			refresh() {
				this.loadCount();
				this.loadOrders(true);
			},

			/**
			 * 加载 订单列表
			 */
			loadOrders(refresh) {
				if (this.loadMore == 'loading') {
					return;
				}
				this.loadMore = 'loading';

				Rest.post(Api.ZHUIGE_SHOP_ORDER_LIST, {
					offset: refresh ? 0 : this.orders.length,
					filter: this.cur_tab
				}).then(res => {
					this.orders = refresh ? res.data.orders : this.orders.concat(res.data.orders);
					this.loadMore = res.data.more;
					this.loaded = true;

					if (refresh) {
						uni.stopPullDownRefresh()
					}
				});
			},

			/**
			 * 加载 订单数量
			 */
			loadCount() {
				Rest.post(Api.ZHUIGE_SHOP_ORDER_COUNT).then(res => {
					this.all_count = res.data.all_count;
					this.create_count = res.data.create_count;
					this.pay_count = res.data.pay_count;
					this.confirm_count = res.data.confirm_count;
					this.cancel_count = res.data.cancel_count;
				});
			}
		}
	}
</script>

<style lang="scss" scoped>
	@import "@/style/main.css";

	.order-manage {
		padding: 20rpx;
	}

	.order-tabs {
		display: flex;
		justify-content: space-between;
		background: #fff;
		padding: 20rpx;
		border-radius: 10rpx;
		margin-bottom: 20rpx;
	}

	.tab-item {
		position: relative;
		padding: 10rpx 0;
		font-size: 28rpx;
		color: #666;
	}

	.tab-item.active {
		color: #ff4400;
		font-weight: bold;
	}

	.count {
		position: absolute;
		top: 0;
		right: -15rpx;
		background: #ff4400;
		color: #fff;
		font-size: 20rpx;
		padding: 0 8rpx;
		border-radius: 20rpx;
	}

	.order-list {
		padding-bottom: 100rpx;
	}

	.order-item {
		background: #fff;
		border-radius: 10rpx;
		margin-bottom: 20rpx;
		overflow: hidden;
	}

	.order-header {
		display: flex;
		justify-content: space-between;
		padding: 20rpx;
		border-bottom: 1px solid #f5f5f5;
	}

	.order-no {
		font-size: 24rpx;
		color: #999;
	}

	.order-status {
		font-size: 24rpx;
		color: #ff4400;
	}

	.order-goods {
		padding: 20rpx;
		border-bottom: 1px solid #f5f5f5;
	}

	.goods-item {
		display: flex;
		margin-bottom: 20rpx;
	}

	.goods-image {
		width: 160rpx;
		height: 160rpx;
		border-radius: 10rpx;
		margin-right: 20rpx;
	}

	.goods-info {
		flex: 1;
	}

	.goods-title {
		font-size: 28rpx;
		margin-bottom: 10rpx;
	}

	.goods-price-count {
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.price {
		color: #ff4400;
		font-size: 32rpx;
	}

	.count {
		color: #999;
		font-size: 28rpx;
	}

	.order-footer {
		padding: 20rpx;
	}

	.order-total {
		display: flex;
		justify-content: flex-end;
		align-items: center;
		margin-bottom: 20rpx;
		font-size: 28rpx;
	}

	.order-btns {
		display: flex;
		justify-content: flex-end;
	}

	.btn-cancel, .btn-delete {
		background: #f5f5f5;
		color: #666;
		font-size: 24rpx;
		padding: 10rpx 20rpx;
		border-radius: 30rpx;
		margin-left: 20rpx;
	}

	.btn-pay {
		background: #ff4400;
		color: #fff;
		font-size: 24rpx;
		padding: 10rpx 20rpx;
		border-radius: 30rpx;
		margin-left: 20rpx;
	}

	.no-more {
		text-align: center;
		color: #999;
		font-size: 24rpx;
		padding: 20rpx 0;
	}
</style>