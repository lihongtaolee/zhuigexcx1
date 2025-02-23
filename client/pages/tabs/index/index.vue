<template>
	<view class="content">
	  <!-- 搜索顶部模块 -->
	  <uni-nav-bar :fixed="true" :statusBar="true" :border="false" background-color="#0863cc">
		<view class="zhuige-top-bar" :style="style">
		  <template v-slot:left>
			<view v-if="logo" class="zhuige-top-logo">
			  <image mode="heightFix" :src="logo"></image>
			</view>
		  </template>
		  <view class="zhuige-top-search" @click="openLink('/pages/base/search/search')">
			<uni-icons type="search" color="#999999" size="18"></uni-icons>
			<text>关键词...</text>
		  </view>
		</view>
	  </uni-nav-bar>
	
	  <!-- 身高预测AI模块容器 -->
	  <view class="zhuige-wide-box height-predict-wrapper" v-if="!isLoading">
		<zhuige-height-predict
		  :currentHeight.sync="heightData.currentHeight"
		  :geneticHeight.sync="heightData.geneticHeight"
		  :targetHeight.sync="heightData.targetHeight"
		  :probability.sync="heightData.probability"
		  :baobaoname.sync="heightData.baobaoname"
		  :updateTime.sync="heightData.updateTime"
		/>
	  </view>
	
	  <!-- 轮播图模块 -->
	  <view v-if="slides && slides.length > 0" class="zhuige-wide-box">
		<zhuige-swiper :items="slides" />
	  </view>
	
	  <!-- 金刚位模块 -->
	  <view v-if="icons && icons.length > 0" class="zhuige-wide-box">
		<zhuige-icons :items="icons" />
	  </view>
	  
	  <!-- 身高专题模块 -->
	  <view class="zhuige-wide-box" v-if="!isModulesLoading && sgztmkModules && sgztmkModules.length > 0">
		<zhuige-sgztmk-module
		  v-for="(module, index) in sgztmkModules"
		  :key="module.id"
		  :moduleTitle="module.title"
		  :leftModule="{
			title: module.left_module.title,
			icon: module.icon,
			description: module.left_module.description,
			image: module.left_module.image,
			buttonText: module.left_module.button_text,
			link: module.left_module.link,
			value: module.left_module.value,
			bgColor: module.left_module.bg_color
		  }"
		  :rightTopModule="{
			title: module.right_top_module.title,
			description: module.right_top_module.description,
			image: module.right_top_module.image,
			buttonText: module.right_top_module.button_text,
			link: module.right_top_module.link,
			bgColor: module.right_top_module.bg_color
		  }"
		  :rightBottomModule="{
			title: module.right_bottom_module.title,
			description: module.right_bottom_module.description,
			image: module.right_bottom_module.image,
			buttonText: module.right_bottom_module.button_text,
			link: module.right_bottom_module.link,
			bgColor: module.right_bottom_module.bg_color
		  }"
		/>
	  </view>
	
	  <!-- 底部提示文本 -->
	  <view class="bottom-tip">
		<text>哎呀，滑到底啦！宝宝要长高高~</text>
	  </view>
	</view>
</template>
  
<script>
import Util from '@/utils/util';
import Api from '@/utils/api';
import Rest from '@/utils/rest';

import ZhuigeSwiper from "@/components/zhuige-swiper";
import ZhuigeIcons from "@/components/zhuige-icons";
import zhuigeHeightPredict from '@/components/zhuige-height-predict.vue';
import ZhuigeSgztmkModule from "@/components/zhuige-sgztmk-module.vue";

export default {
	components: {
	  ZhuigeSwiper,
	  ZhuigeIcons,
	  zhuigeHeightPredict,
	  ZhuigeSgztmkModule
	},
	data() {
	  return {
		isLoading: true,
		logo: undefined,
		style: '',
		slides: [],
		icons: [],
		heightData: this.getDefaultHeightData(),
		apiBaseUrl: 'https://x.erquhealth.com/wp-json/zhuige/sgtool',
		sgztmkModules: [],
		isModulesLoading: true
	  }
	},
	onLoad() {
	  this.refresh();
	  this.fetchUserHeightData();
	},
	onShow() {
	  this.refresh();
	  const auth = uni.getStorageSync('zhuige_xcx_user');
	  if (auth && auth.user_id) {
		this.fetchUserHeightData();
	  } else {
		this.heightData = this.getDefaultHeightData();
	  }
	},
	methods: {
	  openLink(link) {
		Util.openLink(link);
	  },
	  refresh() {
		this.loadSetting();
	  },
	  loadSetting() {
		Rest.post(Api.URL('setting', 'home'))
		  .then(res => {
			this.logo = res.data.logo;
			this.slides = res.data.slides;
			this.icons = res.data.icons;
			if (res.data.style) {
			  this.style = res.data.style;
			}
			this.fetchSgztmkModules();
			this.isLoading = false;
		  })
		  .catch(err => {
			this.isLoading = false;
		  });
	  },
	  getDefaultHeightData() {
		return {
		  currentHeight: 165,
		  geneticHeight: 175,
		  targetHeight: 180,
		  probability: 85,
		  baobaoname: '演示宝宝',
		  updateTime: ''
		};
	  },
	  async fetchUserHeightData() {
		const auth = uni.getStorageSync('zhuige_xcx_user');
		
		if (!auth || !auth.user_id) {
		  this.heightData = this.getDefaultHeightData();
		  this.isLoading = false;
		  return;
		}
		
		try {
		  const res = await uni.request({
			url: `${this.apiBaseUrl}/get_user_height_data`,
			method: 'GET',
			data: { user_id: auth.user_id },
			header: { 'content-type': 'application/json' }
		  });
  
		  if (res.statusCode === 200 && res.data && res.data.code === 0) {
			const data = res.data.data;
			
			if (data && data.baobaoname && data.current_height && data.target_height && data.prediction_probability) {
			  this.heightData = {
				geneticHeight: Number(data.gender === 1 ? data.boy_genetic_height : data.girl_genetic_height),
				currentHeight: Number(data.current_height),
				targetHeight: Number(data.target_height),
				probability: Number(data.prediction_probability),
				baobaoname: data.baobaoname,
				updateTime: data.create_time
			  };
			} else {
			  this.heightData = this.getDefaultHeightData();
			}
		  }
		} catch (err) {
		  uni.showToast({ title: '获取数据失败', icon: 'none' });
		  this.heightData = this.getDefaultHeightData();
		} finally {
		  this.isLoading = false;
		}
	  },
	  async fetchSgztmkModules() {
		this.isModulesLoading = true;
		try {
		  const res = await Rest.post(Api.URL('sgtool', 'get_sgztmk_modules'));
		  if (res.data && Array.isArray(res.data.modules)) {
			const auth = uni.getStorageSync('zhuige_xcx_user');
			
			this.sgztmkModules = await Promise.all(res.data.modules.map(async module => {
			  if (module.left_module.value_api) {
				let value = '60';
				
				if (auth && auth.user_id) {
				  try {
					const apiUrl = module.left_module.value_api.replace('{user_id}', auth.user_id);
					
					const response = await uni.request({
					  url: apiUrl,
					  method: 'GET',
					  header: { 'content-type': 'application/json' }
					});
					
					if (response.data && response.data.code === 200 && response.data.data) {
					  const result = response.data.data;
					  if (Array.isArray(result) && result.length > 0) {
						const latestRecord = result[result.length - 1];
						const queryString = apiUrl.split('?')[1];
						const fieldsMatch = queryString.match(/fields=([^&]*)/);
						const fields = fieldsMatch ? fieldsMatch[1] : null;
						
						if (fields && latestRecord[fields] !== undefined) {
						  value = Number(latestRecord[fields]).toFixed(1);
						}
					  }
					}
				  } catch (error) {}
				}
				
				module.left_module.value = value;
			  }
			  return module;
			}));
		  } else {
			this.sgztmkModules = [];
		  }
		} catch (err) {
		  this.sgztmkModules = [];
		} finally {
		  this.isModulesLoading = false;
		}
	  }
	}
}
</script>
  
<style lang="scss">
.zhuige-top-logo {
	display: flex;
	align-items: center;
	margin-right: 15rpx;
	image {
	  height: 48rpx;
	  width: 128rpx;
	}
}

.zhuige-top-search {
	display: flex;
	align-items: center;
	width: 80%;
	height: 32px;
	padding-left: 20rpx;
	color: #999999;
	font-size: 28rpx;
	border: 1rpx solid #999999;
	border-radius: 16rpx;
}

.zhuige-wide-box {
	padding: 0 20rpx;
	margin-bottom: 20rpx;
}

/* 合并了重复定义，移除了 !important */
.height-predict-wrapper {
	padding: 0;
	margin: 20rpx 0;
}

:deep(.height-predict-container) {
	background: linear-gradient(135deg, #f5f5f5 0%, #e8f4ff 100%);
	border-radius: 24rpx;
	padding: 30rpx;
	margin: 0;
	box-shadow: 0 8rpx 24rpx rgba(8, 99, 204, 0.1);
	position: relative;
	z-index: 1;
}

.bottom-tip {
	text-align: center;
	padding: 40rpx 0;
	margin-bottom: 40rpx;
	
	text {
	  font-size: 28rpx;
	  color: #999999;
	}
}
</style>
