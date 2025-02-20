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
  
  <!-- 修改后的身高预测AI模块容器 -->
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
	</view>
</template>

<script>
import Util from '@/utils/util';
import Api from '@/utils/api';
import Rest from '@/utils/rest';

import ZhuigeSwiper from "@/components/zhuige-swiper";
import ZhuigeIcons from "@/components/zhuige-icons";
import zhuigeHeightPredict from '@/components/zhuige-height-predict.vue';

export default {
	components: {
	  ZhuigeSwiper,
	  ZhuigeIcons,
	  zhuigeHeightPredict
	},
	data() {
	  return {
		isLoading: true,
		logo: undefined,
		style: '',
		heightData: {
		  currentHeight: 165,
		  geneticHeight: 175,
		  targetHeight: 180,
		  probability: 85,
		  baobaoname: '演示宝宝' // 默认演示数据
		},
		apiBaseUrl: 'https://x.erquhealth.com/wp-json/zhuige/sgtool',
		slides: [],
		icons: []
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
		// 用户已登录，直接刷新身高数据
		this.fetchUserHeightData();
	  } else {
		// 用户未登录，重置身高数据为默认值
		this.heightData = {
		  currentHeight: 165,
		  geneticHeight: 175,
		  targetHeight: 180,
		  probability: 85,
		  baobaoname: '演示宝宝' // 默认演示数据
		};
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
			this.isLoading = false;
		  })
		  .catch(err => {
			console.log(err);
			this.isLoading = false;
		  });
	  },
	  fetchUserHeightData() {
		const auth = uni.getStorageSync('zhuige_xcx_user');
		console.log('开始获取身高数据，用户认证信息：', auth);
		  
		  if (!auth || !auth.user_id) {
			console.log('用户未登录，使用默认数据');
			this.isLoading = false;
			return;
		  }
		
		  console.log('准备发起API请求，用户ID：', auth.user_id);
		  uni.request({
			url: `${this.apiBaseUrl}/get_user_height_data`,
			method: 'GET',
			data: {
			  user_id: auth.user_id
			},
			header: {
			  'content-type': 'application/json'
			},
			success: (res) => {
			  console.log('API响应数据：', res);
			  if (res.statusCode === 200 && res.data && res.data.code === 0) {
				const data = res.data.data;
				console.log('解析到的用户身高数据：', data);
				
				if (data && data.baobaoname) {
				  console.log('检测到完整的宝宝档案，更新所有数据');
				  this.heightData = {
					geneticHeight: Number(data.gender === 1 ? data.boy_genetic_height : data.girl_genetic_height),
					currentHeight: Number(data.current_height),
					targetHeight: Number(data.target_height),
					probability: Number(data.prediction_probability),
					baobaoname: data.baobaoname,
					updateTime: data.create_time
				  };
				  console.log('更新后的heightData：', this.heightData);
				} else {
				  console.log('未检测到宝宝档案，使用默认数据');
				  this.heightData = {
					currentHeight: 165,
					geneticHeight: 175,
					targetHeight: 180,
					probability: 85,
					baobaoname: '演示宝宝',
					updateTime: ''
				  };
				}
			  }
			},
			fail: (err) => {
			  console.error('API请求失败：', err);
			  uni.showToast({
				title: '获取数据失败',
				icon: 'none'
			  });
			}
		  });
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

.height-predict-wrapper {
	padding: 0;
	margin: 20rpx;
}

/* 新增作用域样式 */
.height-predict-wrapper {
  padding: 0 !important; /* 强制清除容器内边距 */
  margin: 20rpx 0 !important; /* 统一外边距 */
}

  /* 穿透作用域设置子组件容器样式 */
  :deep(.height-predict-container) {
    background: linear-gradient(135deg, #f5f5f5 0%, #e8f4ff 100%);
    border-radius: 24rpx;
    padding: 30rpx;
    margin: 0 !important;
    box-shadow: 0 8rpx 24rpx rgba(8, 99, 204, 0.1);
    position: relative;
    z-index: 1;
  }
</style>