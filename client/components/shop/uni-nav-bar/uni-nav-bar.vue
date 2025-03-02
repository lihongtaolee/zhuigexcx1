<template>
	<view class="uni-navbar">
		<uni-status-bar v-if="statusBar" />
		<view :class="{'uni-navbar--fixed':fixed,'uni-navbar--shadow':shadow,'uni-navbar--border':border}" :style="{background:backgroundColor,borderBottomColor:borderColor}" class="uni-navbar__content">
			<view :style="{color:color,backgroundColor:backgroundColor}" class="uni-navbar__header uni-navbar__content_view">
				<view @tap="onClickLeft" class="uni-navbar__header-btns uni-navbar__header-btns-left uni-navbar__content_view">
					<view class="uni-navbar__content_view" v-if="leftIcon.length">
						<uni-icons :color="color" :type="leftIcon" size="24" />
					</view>
					<view :class="{'uni-navbar-btn-icon-left':!leftIcon.length}" class="uni-navbar-btn-text uni-navbar__content_view" v-if="leftText.length">
						<text :style="{color:color,fontSize:'14px'}">{{ leftText }}</text>
					</view>
					<slot name="left" />
				</view>
				<view class="uni-navbar__header-container uni-navbar__content_view">
					<view class="uni-navbar__header-container-inner uni-navbar__content_view" v-if="title.length">
						<text class="uni-nav-bar-text" :style="{color: color }">{{ title }}</text>
					</view>
					<slot />
				</view>
				<view @tap="onClickRight" class="uni-navbar__header-btns uni-navbar__header-btns-right uni-navbar__content_view">
					<view class="uni-navbar__content_view" v-if="rightIcon.length">
						<uni-icons :color="color" :type="rightIcon" size="24" />
					</view>
					<view class="uni-navbar-btn-text uni-navbar__content_view" v-if="rightText.length && !rightIcon.length">
						<text class="uni-nav-bar-right-text">{{ rightText }}</text>
					</view>
					<slot name="right" />
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import uniStatusBar from "../uni-status-bar/uni-status-bar.vue";
	import uniIcons from "@/uni_modules/uni-icons/components/uni-icons/uni-icons.vue";

	/**
	 * NavBar 自定义导航栏
	 * @description 导航栏组件，主要用于头部导航
	 */
	export default {
		name: "UniNavBar",
		components: {
			uniStatusBar,
			uniIcons
		},
		props: {
			title: {
				type: String,
				default: ""
			},
			leftText: {
				type: String,
				default: ""
			},
			rightText: {
				type: String,
				default: ""
			},
			leftIcon: {
				type: String,
				default: ""
			},
			rightIcon: {
				type: String,
				default: ""
			},
			fixed: {
				type: [Boolean, String],
				default: false
			},
			color: {
				type: String,
				default: "#000000"
			},
			backgroundColor: {
				type: String,
				default: "#FFFFFF"
			},
			statusBgColor: {
				type: String,
				default: ""
			},
			statusBar: {
				type: [Boolean, String],
				default: false
			},
			shadow: {
				type: [Boolean, String],
				default: false
			},
			border: {
				type: [Boolean, String],
				default: true
			},
			height: {
				type: [Number, String],
				default: 44
			},
			borderColor: {
				type: String,
				default: "#f0f0f0"
			}
		},
		methods: {
			onClickLeft() {
				this.$emit("clickLeft");
			},
			onClickRight() {
				this.$emit("clickRight");
			}
		}
	};
</script>

<style lang="scss" scoped>
	$nav-height: 44px;
	.uni-navbar {
		width: 100%;
	}

	.uni-navbar__content {
		position: relative;
		width: 100%;
		background-color: $uni-bg-color;
		overflow: hidden;
	}

	.uni-navbar__content_view {
		line-height: $nav-height;
	}

	.uni-navbar__header {
		display: flex;
		flex-direction: row;
		width: 100%;
		height: $nav-height;
		line-height: $nav-height;
		font-size: 16px;
	}

	.uni-navbar__header-btns {
		display: flex;
		flex-wrap: nowrap;
		width: 120rpx;
		padding: 0 6px;
		justify-content: center;
		align-items: center;
	}

	.uni-navbar__header-btns-left {
		display: flex;
		width: 150rpx;
		justify-content: flex-start;
	}

	.uni-navbar__header-btns-right {
		display: flex;
		width: 150rpx;
		padding-right: 30rpx;
		justify-content: flex-end;
	}

	.uni-navbar__header-container {
		flex: 1;
	}

	.uni-navbar__header-container-inner {
		display: flex;
		flex: 1;
		align-items: center;
		justify-content: center;
		font-size: 28rpx;
	}

	.uni-navbar__placeholder-view {
		height: $nav-height;
	}

	.uni-navbar--fixed {
		position: fixed;
		z-index: 998;
	}

	.uni-navbar--shadow {
		box-shadow: 0 1px 6px #ccc;
	}

	.uni-navbar--border {
		border-bottom-width: 1rpx;
		border-bottom-style: solid;
		border-bottom-color: $uni-border-color;
	}

	.uni-ellipsis-1 {
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
</style>