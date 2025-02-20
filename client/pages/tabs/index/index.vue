<template>
	<view class="content">
	  <!-- 搜索顶部模块 -->
	  <uni-nav-bar :fixed="true" :statusBar="true" :border="false" background-color="#0863cc">
		<view class="zhuige-top-bar" :style="style">
		  <template v-slot:left>
			<view class="zhuige-top-logo" :class="{ 'logo-placeholder': !logo }">
			  <image v-if="logo" mode="heightFix" :src="logo"></image>
			</view>
		  </template>
		  <view class="zhuige-top-search" :class="{ 'search-loaded': logo }" @click="openLink('/pages/base/search/search')">
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

    <!-- 身高专题模块 -->
    <view class="zhuige-wide-box" v-if="!isLoading && sgztmkModules.length > 0">
      <zhuige-sgztmk-module
        v-for="(module, index) in sgztmkModules"
        :key="module.id"
        :leftModule="{
          moduleTitle: module.title,
          icon: module.icon,
          title: module.left_module.title,
          description: module.left_module.description,
          image: module.left_module.image,
          buttonText: module.left_module.button_text,
          link: module.left_module.link,
          valueApi: module.left_module.value_api,
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
	</view>
</template>

<script>
import Util from '@/utils/util';
import Api from '@/utils/api';
import Rest from '@/utils/rest';

import ZhuigeSwiper from "@/components/zhuige-swiper";
import ZhuigeIcons from "@/components/zhuige-icons";
import ZhuigeSgztmkModule from "@/components/zhuige-sgztmk-module.vue";
import zhuigeHeightPredict from '@/components/zhuige-height-predict.vue';

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
		heightData: {
		  currentHeight: 165,
		  geneticHeight: 175,
		  targetHeight: 180,
		  probability: 85,
		  baobaoname: '演示宝宝'
		},
		apiBaseUrl: 'https://x.erquhealth.com/wp-json/zhuige/sgtool',
		slides: [],
		icons: [],
		sgztmkModules: [] // 新增：身高专题模块数据
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
			// 获取身高专题模块数据
			this.fetchSgztmkModules();
			this.isLoading = false;
		  })
		  .catch(err => {
			console.log(err);
			this.isLoading = false;
		  });
	  },
	  // 新增：获取身高专题模块数据
	  fetchSgztmkModules() {
		Rest.post(Api.URL('sgtool', 'get_sgztmk_modules'))
		  .then(res => {
			if (res.data && res.data.modules) {
			  this.sgztmkModules = res.data.modules;
			}
		  })
		  .catch