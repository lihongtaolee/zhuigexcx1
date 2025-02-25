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

    <!-- 修改：注释掉身高专题模块 -->
    <!--
    <view class="zhuige-wide-box" v-if="!isModulesLoading && sgztmkModules && sgztmkModules.length > 0">
      <zhuige-sgztmk-module
        v-for="(module, index) in sgztmkModules"
        :key="module.id"
        :leftModule="{
          title: module.left_module.title,
          icon: module.icon,
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
    -->
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
		  baobaoname: '演示宝宝',
		  updateTime: ''
		},
		apiBaseUrl: 'https://x.erquhealth.com/wp-json/zhuige/sgtool',
		slides: [],
		icons: [],
		sgztmkModules: [], // 新增：身高专题模块数据
		isModulesLoading: true // 新增：身高专题模块加载状态
	  }
	},
	onLoad() {
	  console.log('[首页] 页面加载(onLoad)开始...');
	  console.log('[首页] 当前组件数据状态:', JSON.stringify({
	    isLoading: this.isLoading,
	    logo: this.logo,
	    heightData: this.heightData,
	    slides: this.slides,
	    icons: this.icons,
	    sgztmkModules: this.sgztmkModules
	  }));
	  this.refresh();
	  this.fetchUserHeightData();
	  console.log('[首页] 页面加载(onLoad)完成');
	},
	onShow() {
	  console.log('[首页] 页面显示(onShow)开始...');
	  console.log('[首页] 当前组件状态:', JSON.stringify({
	    isLoading: this.isLoading,
	    isModulesLoading: this.isModulesLoading
	  }));
	  this.refresh();
	  const auth = uni.getStorageSync('zhuige_xcx_user');
	  console.log('[首页] 用户登录状态:', auth ? '已登录' : '未登录');
	  if (auth && auth.user_id) {
		console.log('[首页] 用户已登录，用户ID:', auth.user_id);
		this.fetchUserHeightData();
	  } else {
		console.log('[首页] 用户未登录，使用默认身高数据');
		this.heightData = {
		  currentHeight: 165,
		  geneticHeight: 175,
		  targetHeight: 180,
		  probability: 85,
		  baobaoname: '演示宝宝'
		};
	  }
	  console.log('[首页] 页面显示(onShow)完成');
	},
	methods: {
	  openLink(link) {
		Util.openLink(link);
	  },
	  refresh() {
		this.loadSetting();
	  },
	  loadSetting() {
		console.log('[首页] 开始加载基础设置数据...');
		Rest.post(Api.URL('setting', 'home'))
		  .then(res => {
			console.log('[首页] 基础设置数据返回:', JSON.stringify(res));
			this.logo = res.data.logo;
			this.slides = res.data.slides;
			this.icons = res.data.icons;
			if (res.data.style) {
			  this.style = res.data.style;
			}
			console.log('[首页] 基础数据解析完成，开始获取身高专题模块...');
			this.fetchSgztmkModules();
			this.isLoading = false;
		  })
		  .catch(err => {
			console.error('[首页] 加载基础设置失败:', err);
			this.isLoading = false;
		  });
	  },
	  // 新增：获取身高专题模块数据
	  fetchSgztmkModules() {
	    this.isModulesLoading = true;
	    console.log('[身高专题模块] 开始获取数据...');
	    const requestUrl = Api.URL('sgtool', 'get_sgztmk_modules');
	    console.log('[身高专题模块] 请求URL:', requestUrl);
	
	    Rest.post(requestUrl)
	      .then(res => {
	        console.log('[身高专题模块] 接口返回原始数据:', JSON.stringify(res));
	        if (res.data && Array.isArray(res.data.modules)) {
	          this.sgztmkModules = res.data.modules;
	          console.log('[身高专题模块] 数据解析成功，模块数量:', this.sgztmkModules.length);
	          
	          if (this.sgztmkModules.length === 0) {
	            console.warn('[身高专题模块] 接口返回的模块数组为空');
	          } else {
	            console.log('[身高专题模块] 解析后的模块数据:', JSON.stringify(this.sgztmkModules));
	            // 检查每个模块的结构完整性
	            this.sgztmkModules.forEach((module, index) => {
	              const moduleCheck = {
	                id: module.id ? '✓' : '✗ 缺失ID',
	                title: module.title ? '✓' : '✗ 缺失标题',
	                icon: module.icon ? '✓' : '✗ 缺失图标',
	                left_module: this._checkModuleData(module.left_module),
	                right_top_module: this._checkModuleData(module.right_top_module),
	                right_bottom_module: this._checkModuleData(module.right_bottom_module)
	              };
	              console.log(`[身高专题模块] 模块${index + 1}数据完整性检查:`, moduleCheck);
	              
	              if (Object.values(moduleCheck).some(v => v.includes('✗'))) {
	                console.warn(`[身高专题模块] 模块${index + 1}数据不完整，请检查:`, moduleCheck);
	              }
	            });
	          }
	        } else {
	          this.sgztmkModules = [];
	          console.warn('[身高专题模块] 数据格式异常:', res.data);
	          if (!res.data) {
	            console.error('[身高专题模块] 接口返回数据为空');
	          } else if (!Array.isArray(res.data.modules)) {
	            console.error('[身高专题模块] modules不是数组类型:', typeof res.data.modules);
	          }
	        }
	      })
	      .catch(err => {
	        console.error('[身高专题模块] 获取数据失败:', err);
	        console.error('[身高专题模块] 错误详情:', err.message || '未知错误');
	        this.sgztmkModules = [];
	      })
	      .finally(() => {
	        const loadingStatus = !this.isModulesLoading;
	        console.log('[身高专题模块] 加载状态更新:', loadingStatus);
	        console.log('[身高专题模块] 最终模块数量:', this.sgztmkModules.length);
	        this.isModulesLoading = false;
	      });
	  },
	  
	  _checkModuleData(moduleData) {
	    if (!moduleData) return '✗ 数据缺失';
	    const requiredFields = ['title', 'description', 'image', 'button_text', 'link', 'bg_color'];
	    const missingFields = requiredFields.filter(field => !moduleData[field]);
	    return missingFields.length === 0 ? '✓' : `✗ 缺失字段: ${missingFields.join(', ')}`;
	  },
	  },