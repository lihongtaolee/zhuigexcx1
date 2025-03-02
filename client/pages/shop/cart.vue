<template>
	<view class="page">
		<view class="cart-container">
			<template v-if="getCartCount>0">
				<view class="cart-header">
					<view class="cart-count">
						购物数量
						<text>{{getCartCount}}</text>
					</view>
					<view class="cart-manage" @click="clickManage">{{manage?'取消':'管理'}}</view>
				</view>

				<view class="cart-list">
					<view v-for="(item,index) in cart" :key="index" class="cart-item">
						<view class="item-check">
							<uni-icons
								:type="manage ? (item.check_del==1 ? 'checkbox-filled' : 'circle') : (item.check_buy==1 ? 'checkbox-filled' : 'circle')"
								size="24" color="#ff4400" @click.stop="clickItemCheck(item.id)"></uni-icons>
						</view>
						<image class="item-image" @click="clickItem(item.id)" :src="item.thumbnail" mode="aspectFill"></image>
						<view class="item-info">
							<view @click="clickItem(item.id)" class="item-title">{{item.title}}</view>
							<view class="item-price-count">
								<view class="price">
									<text>￥</text>
									<text>{{item.price}}</text>
								</view>
								<view class="count-control">
									<text class="minus" @click="clickGoodsMinus(item.id, item.count, 1)">-</text>
									<input type="number" :value="item.count" @input="inputGoodsCount"
										:data-goods_id="item.id" />
									<text class="plus" @click="clickGoodsAdd(item.id, 1)">+</text>
								</view>
							</view>
						</view>
					</view>
				</view>

				<view class="cart-footer">
					<view class="select-all">
						<uni-icons @click="clickCheckAll"
							:type="manage ? (getCheckDelAll ? 'checkbox-filled' : 'circle') : (getCheckBuyAll ? 'checkbox-filled' : 'circle')"
							size="24" color="#ff4400"></uni-icons>
						<text @click="clickCheckAll"
							class="all-text">全选（{{manage ? getCheckDelCount : getCheckBuyCount}}件）</text>
					</view>
					<view class="action-area">
						<view class="total-price" v-if="!manage">
							<text>￥</text>
							<text>{{getCheckBuyAmount}}</text>
						</view>
						<view @click="clickNext" class="action-btn" :class="{'delete-btn':manage}">
							{{manage ? '立即删除' : '立即购买'}}
						</view>
					</view>
				</view>
			</template>

			<view v-else class="empty-cart" @click="clickCartEmpty">
				<image src="/static/shop/empty-cart.png" mode="aspectFit"></image>
				<view class="empty-text">暂无商品，去添加点什么吧~</view>
			</view>
		</view>
	</view>
</template>

<script>
	import Alert from '@/utils/alert'
	import Rest from '@/utils/rest'
	import Api from '@/utils/api'
	import { mapState, mapGetters, mapMutations } from 'vuex'
	import store from '@/store/index.js'

	export default {
		data() {
			return {
				manage: false
			}
		},

		computed: {
			...mapState([
				'cart'
			]),
			...mapGetters([
				'getCartCount',
				'getCheckBuyAll',
				'getCheckDelAll',
				'getCheckBuyCount',
				'getCheckDelCount',
				'getAllGoodsIds',
				'getCheckBuyAmount'
			])
		},

		onLoad() {
			// 页面加载时的初始化
		},

		onShow() {
			// 更新购物车角标
			this.updateCartBadge(this.getCartCount)

			// 获取购物车商品详情
			Rest.post(Api.ZHUIGE_SHOP_GOODS_CART, {
				goods_ids: this.getAllGoodsIds.join(',')
			}).then(res => {
				store.commit('cartSetGoodsList', {
					list: res.data.list
				})
			})
		},

		methods: {
			/**
			 * 更新购物车角标
			 */
			updateCartBadge(count) {
				if (count > 0) {
					uni.setTabBarBadge({
						index: 2,
						text: count.toString()
					})
				} else {
					uni.removeTabBarBadge({
						index: 2
					})
				}
			},

			/**
			 * 管理 切换
			 */
			clickManage() {
				this.manage = !this.manage
			},

			/**
			 * 打开商品详情
			 */
			clickItem(goods_id) {
				uni.navigateTo({
					url: '/pages/shop/detail?goods_id=' + goods_id
				})
			},

			/**
			 * 项目 选中 点击
			 */
			clickItemCheck(goods_id) {
				if (this.manage) {
					store.commit('cartSetGoodsCheckDel', {
						goods_id: goods_id
					})
				} else {
					store.commit('cartSetGoodsCheckBuy', {
						goods_id: goods_id
					})
				}
			},

			/**
			 * 添加商品数量
			 */
			clickGoodsAdd(goods_id, count) {
				store.commit('cartGoodsAdd', {
					goods_id: goods_id,
					count: count
				})

				this.updateCartBadge(this.getCartCount)
			},

			/**
			 * 减少商品数量
			 */
			clickGoodsMinus(goods_id, goods_count, count) {
				if (goods_count <= count) {
					uni.showModal({
						title: '提示',
						content: '要删除这个商品吗？',
						success: (res) => {
							if (res.confirm) {
								store.commit('cartGoodsDelete', {
									goods_id: goods_id
								})

								this.updateCartBadge(this.getCartCount)
							}
						}
					})
				} else {
					store.commit('cartGoodsMinus', {
						goods_id: goods_id,
						count: count
					})

					this.updateCartBadge(this.getCartCount)
				}
			},

			/**
			 * 设置商品数量
			 */
			inputGoodsCount(e) {
				let count = parseInt(e.detail.value)
				if (!count || count < 1) {
					count = 1
				}

				store.commit('cartSetGoodsCount', {
					goods_id: e.currentTarget.dataset.goods_id,
					count: count
				})

				this.updateCartBadge(this.getCartCount)
			},

			/**
			 * 全选
			 */
			clickCheckAll() {
				if (this.manage) {
					store.commit('cartCheckAllDel', {
						check: (this.getCheckDelAll ? 0 : 1)
					})
				} else {
					store.commit('cartCheckAllBuy', {
						check: (this.getCheckBuyAll ? 0 : 1)
					})
				}
			},

			/**
			 * 下一步
			 */
			clickNext() {
				if (this.manage) {
					if (this.getCheckDelCount == 0) {