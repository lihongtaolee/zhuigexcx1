<template>
	<view :style="{height: statusBarHeight}" class="uni-status-bar">
		<slot></slot>
	</view>
</template>

<script>
	export default {
		name: 'UniStatusBar',
		data() {
			return {
				statusBgColor: '#ffffff'
			}
		},
		props: {
			bgColor: {
				type: String,
				default: ''
			}
		},
		computed: {
			statusBgColor() {
				return this.bgColor || this.statusBgColor
			},
			statusBarHeight() {
				return this.getStatusBarHeight()
			}
		},
		mounted() {
			this.getStatusBarHeight()
		},
		methods: {
			getStatusBarHeight() {
				let statusBarHeight = uni.getSystemInfoSync().statusBarHeight + 'px'
				return statusBarHeight
			}
		}
	}
</script>

<style lang="scss" scoped>
	.uni-status-bar {
		width: 100%;
	}
</style>