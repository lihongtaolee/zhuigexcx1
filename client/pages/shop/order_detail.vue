<template>
	<view class="page">
		<view class="order-detail">
			<view class="order-status" v-if="order">
				<view class="status-text" v-if="!order.paytime && !order.canceltime">待付款</view>
				<view class="status-text" v-if="order.paytime && !order.confirmtime">待发货</view>
				<view class="status-text" v-if="order.confirmtime">已完成</view>
				<view class="status-text" v-if="order.canceltime">已取消</view>
			</view>

			<view class="order-address" v-if="order">
				<view class="address-info">
					<view class="name-mobile">
						<text>{{order.address.name}}</text>
						<text>{{order.address.mobile}}</text>
					</view>
					<view class="address">{{order.address.province}}{{order.address.city}}{{order.address.district}}{{order.address.detail}}</view>
				</view>
			</view>

			<view class="order-goods" v-if="order">
				<view class="goods-item" v-for="(goods, index) in order.goods" :key="index">
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

			<view class="order-info" v-if="order">
				<view class="info-item">
					<text>订单编号</text>
					<text>{{order.trade_no}}</text>
				</view>
				<view class="info-item">
					<text>创建时间</text>
					<text>{{order.createtime}}</text>
				</view>
				<view class="info-item" v-if="order.paytime">
					<text>支付时间</text>
					<text>{{order.paytime}}</text>
				</view>
				<view class="info-item" v-if="order.confirmtime">
					<text>完成时间</text>
					<text>{{order.confirmtime}}</text>
				</view>
				<view class="info-item" v-if="order.canceltime">
					<text>取消时间</text>
					<text>{{order.canceltime}}</text>
				</view>
			</view>

			<view class="order-total" v-if="order">
				<text>实付款</text>
				<text class="price">￥{{order.price}}</text>
			</view>

			<view class="order-btns" v-if="order && !order.paytime && !order.canceltime">
				<button class="btn-pay" @click="clickPay">立即支付</button>
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
				order_id: undefined,
				order: undefined
			}
		},

		onLoad(options) {
			this.order_id = options.order_id;
			this.loadData();
		},

		onPullDownRefresh() {
			this.loadData();
		},

		methods: {
			/**
			 * 点击支付
			 */
			clickPay() {
				Rest.post(Api.ZHUIGE_SHOP_ORDER_PAY, {
					order_id: this.order_id
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

								setTimeout(() => {
									this.loadData();
								}, 1000);
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
			 * 加载订单详情
			 */
			loadData() {
				Rest.post(Api.ZHUIGE_SHOP_ORDER_DETAIL, {
					order_id: this.order_id
				}).then(res => {
					if (res.code == 0) {
						this.order = res.data.order;
					}
					uni.stopPullDownRefresh()
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style lang="scss" scoped>
	@import "@/style/main.css";

	.order-detail {
		padding: 20rpx;
	}

	.order-status {
		background: #fff;
		padding: 30rpx;
		border-radius: 10rpx;
		margin-bottom: 20rpx;
	}

	.status-text {
		font-size: 32rpx;
		font-weight: bold;
	}

	.order-address {
		background: #fff;
		padding: 30rpx;
		border-radius: 10rpx;
		margin-bottom: 20rpx;
	}

	.name-mobile {
		display: flex;
		justify-content: space-between;
		margin-bottom: 10rpx;
	}

	.address {
		color: #999;
		font-size: 28rpx;
	}

	.order-goods {
		background: #fff;
		padding: 30rpx;
		border-radius: 10rpx;
		margin-bottom: 20rpx;
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

	.order-info {
		background: #fff;
		padding: 30rpx;
		border-radius: 10rpx;
		margin-bottom: 20rpx;
	}

	.info-item {
		display: flex;
		justify-content: space-between;
		margin-bottom: 10rpx;
		font-size: 28rpx;
		color: #666;
	}

	.order-total {
		background: #fff;
		padding: 30rpx;
		border-radius: 10rpx;
		margin-bottom: 20rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.order-btns {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		background: #fff;
		padding: 20rpx;
		display: flex;
		justify-content: flex-end;
	}

	.btn-pay {
		background: #ff4400;
		color: #fff;
		font-size: 28rpx;
		padding: 10rpx 30rpx;
		border-radius: 30rpx;
	}
</style>