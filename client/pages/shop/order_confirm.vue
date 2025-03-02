<template>
	<view class="page">
		<view class="order-confirm">
			<view @click="clickAddress" class="address-set">
				<template v-if="address">
					<view class="address-info">
						<view class="name-mobile">
							<text>{{address.addressee}}</text>
							<text>{{address.mobile}}</text>
						</view>
						<view class="address">{{address.address}}</view>
					</view>
				</template>
				<view class="address-tips" v-else>请选择地址</view>
			</view>

			<view class="goods-list">
				<view v-for="(item,index) in goods_list" :key="index" class="goods-item">
					<image class="goods-image" :src="item.thumbnail" mode="aspectFill"></image>
					<view class="goods-info">
						<view class="goods-title">{{item.title}}</view>
						<view class="goods-price-count">
							<text class="price">￥{{item.price}}</text>
							<text class="count">x{{item.count}}</text>
						</view>
					</view>
				</view>
			</view>

			<view class="order-remark">
				<view class="title">备注信息</view>
				<textarea placeholder="请输入备注…" v-model="remark"></textarea>
			</view>

			<view class="pay-type">
				<view class="title">支付方式</view>
				<view class="wechat-pay">
					<uni-icons type="weixin" size="32" color="#4CBF00"></uni-icons>
					<text>微信支付</text>
				</view>
			</view>

			<view class="order-footer">
				<view class="goods-count">
					<text>共{{goodsCount}}件商品</text>
				</view>
				<view class="order-total">
					<view class="total-price">
						<text>￥</text>
						<text>{{goodsAmount}}</text>
					</view>
					<view @click="clickOrderSubmit" class="submit-btn">立即付款</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import Alert from '@/utils/alert'
	import Rest from '@/utils/rest'
	import Api from '@/utils/api'
	import { mapGetters } from 'vuex'
	import store from '@/store/index.js'

	export default {
		data() {
			return {
				address: false,
				goods_list: [],
				remark: ''
			}
		},

		computed: {
			...mapGetters([
				'getCheckBuyGoodsIds',
				'getCheckBuyCount',
				'getCheckBuyAmount'
			]),

			goodsCount() {
				let count = 0
				this.goods_list.forEach((item) => {
					count += item.count
				})
				return count
			},

			goodsAmount() {
				let amount = 0
				this.goods_list.forEach((item) => {
					if (item.price && item.count) {
						amount += parseFloat((item.price * item.count).toFixed(2))
					}
				})
				return amount
			}
		},

		onLoad(options) {
			this.address = uni.getStorageSync('zhuige_shop_address')

			let goods_ids = ''
			if (options.goods_id) {
				goods_ids = options.goods_id
			} else {
				goods_ids = this.getCheckBuyGoodsIds.join(',')
			}

			if (goods_ids.length == 0) {
				uni.reLaunch('/pages/index/index')
				return
			}

			Rest.post(Api.ZHUIGE_SHOP_GOODS_CART, {
				goods_ids: goods_ids
			}).then(res => {
				let goods_list = res.data.list
				if (options.goods_id) {
					goods_list[0].count = 1
				} else {
					for (let i = 0; i < goods_list.length; i++) {
						for (let j = 0; j < store.state.cart.length; j++) {
							if (store.state.cart[j].id == goods_list[i].id) {
								goods_list[i].count = store.state.cart[j].count
							}
						}
					}
				}
				this.goods_list = goods_list
			})
		},

		methods: {
			clickAddress() {
				uni.chooseAddress({
					success: (res) => {
						this.address = {
							addressee: res.userName,
							mobile: res.telNumber,
							address: res.provinceName + res.cityName + res.countyName + res.detailInfo
						}
						uni.setStorageSync('zhuige_shop_address', this.address)
					},
					fail: (res) => {
						if (res.errMsg && res.errMsg.indexOf('cancel') < 0) {
							Alert.error(res.errMsg)
						}
					}
				})
			},

			clickOrderSubmit() {
				if (!this.address) {
					Alert.toast('请选择地址')
					return
				}

				if (!this.goods_list || this.goods_list.length == 0) {
					Alert.toast('请选择商品')
					return
				}

				let goods_list = []
				this.goods_list.forEach((item) => {
					goods_list.push({
						id: item.id,
						count: item.count
					})
				})

				Rest.post(Api.ZHUIGE_SHOP_ORDER_CREATE, {
					addressee: this.address.addressee,
					mobile: this.address.mobile,
					address: this.address.address,
					goods_list: JSON.stringify(goods_list),
					remark: this.remark
				}).then(res => {
					if (res.code == 0) {
						store.commit('cartGoodsBuyCheck')

						// #ifdef MP-WEIXIN
						let pay_params = res.data.pay_params

						// 发起微信支付
						wx.requestPayment({
							timeStamp: pay_params.timeStamp,
							nonceStr: pay_params.nonceStr,
							package: pay_params.package,
							signType: 'MD5',
							paySign: pay_params.paySign,
							success: () => {
								Alert.toast('支付成功')
								uni.redirectTo({
									url: '/pages/shop/order_detail?order_id=' + res.data.order_id
								})
							},
							fail: (err) => {
								Alert.toast('支付失败')
								console.log(err)
							}
						})
						// #endif

						// #ifndef MP-WEIXIN
						Alert.toast('平台暂不支持')
						uni.redirectTo({
							url: '/pages/shop/order_detail?order_id=' + res.data.order_id
						})
						// #endif
					} else {
						Alert.toast(res.msg)
					}
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.order-confirm {
		padding: 20rpx;
	}

	.address-set {
		background: #fff;
		padding: 20rpx;
		border-radius: 10rpx;
		margin-bottom: 20rpx;
	}

	.address-info {
		.name-mobile {
			display: flex;
			gap: 20rpx;
			font-size: 28rpx;
			margin-bottom: 10rpx;
		}

		.address {
			font-size: 24rpx;
			color: #666;
		}
	}

	.address-tips {
		font-size: 28rpx;
		color: #999;
	}

	.goods-list {
		background: #fff;
		padding: 20rpx;
		border-radius: 10rpx;
		mar