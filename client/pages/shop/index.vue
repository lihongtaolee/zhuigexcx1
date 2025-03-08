<template>
  <view class="content" :style="background ? 'background: url(' + background + ') no-repeat top; background-size: 100% auto;' : ''">
    <uni-nav-bar :title="title" :color="nav_color" :background-color="nav_bgcolor" :border="false" :fixed="true" :statusBar="true" :placeholder="false">
      <!-- 顶部小搜索框 -->
      <view slot="right" @click="clickLink('/pages/shop/search/search')">
        <view class="zhuige-nav-search">
          <uni-icons type="search" size="20" :color="nav_color"></uni-icons>
          <text :style="{color:nav_color}">关键词...</text>
        </view>
      </view>
    </uni-nav-bar>
    
    <!-- 调试信息，可以在正式环境中移除 -->
    <view class="zhuige-debug-info" v-if="false">
      <text>数据加载状态: {{loaded ? '已加载' : '加载中'}}</text>
      <text>商品数量: {{goods_list.length}}</text>
      <text>推荐商品: {{home_rec ? (home_rec.posts ? home_rec.posts.length : 0) : 0}}个</text>
    </view>

    <view class="zhuige-main-top">
      <!-- 大图轮播 -->
      <view v-if="slides && slides.length>0" class="zhuige-swiper">
        <swiper indicator-dots="true" autoplay="autoplay" circular="ture"
          indicator-color="rgba(255,255,255, 0.3)" indicator-active-color="rgba(255,255,255, 0.8)"
          interval="5000" duration="150" easing-function="linear">
          <swiper-item v-for="(slide, index) in slides" :key="index" @click="clickLink(slide.link)">
            <view class="zhuige-swiper-title">{{slide.title}}</view>
            <image :src="slide.image" mode="aspectFill"></image>
          </swiper-item>
        </swiper>
      </view>

      <!-- 自定义图标 -->
      <view v-if="icon_navs && icon_navs.length>0" class="zhuige-icon-menu">
        <view v-for="(icon, index) in icon_navs" :key="index" @click="clickLink(icon.link)">
          <image :src="icon.image" mode="aspectFill"></image>
          <text>{{icon.title}}</text>
        </view>
      </view>
    </view>

    <!-- 滑动推荐 -->
    <view v-if="home_rec && home_rec.posts && home_rec.posts.length>0" class="zhuige-recom">
      <view class="zhuige-title">
        <view>{{home_rec.title || '推荐商品'}}</view>
        <text>滑动查看</text>
      </view>
      <view class="zhuige-scroll">
        <scroll-view scroll-x="true">
          <view v-for="(post,index) in home_rec.posts" :key="index"
            @click="clickLink('/pages/shop/detail/detail?goods_id=' + post.id)" class="zhuige-scroll-block">
            <image :src="getGoodsThumbnail(post)" mode="aspectFill"></image>
            <view>{{post.title}}</view>
            <view class="zhuige-goods-price" v-if="post.price !== undefined">
              <view class="promotion">
                <text>￥</text>
                <text>{{post.price}}</text>
              </view>
              <view class="original" v-if="post.orig_price !== undefined">
                <text>￥{{post.orig_price}}</text>
              </view>
            </view>
          </view>
        </scroll-view>
      </view>
    </view>

    <view class="zhuige-goods-group">
      <!-- 滑动导航 -->
      <view class="zhuige-goods-nav">
        <view class="zhuige-goods-scroll">
          <scroll-view>
            <view v-for="(item,index) in cats" :key="index" :class="cat_id==item.id?'active':''"
              @click="clickTab(item.id)">
              {{item.name}}
            </view>
          </scroll-view>
        </view>
        <view @click="clickCategory" class="zhuige-goods-more">
          <uni-icons type="bars" size="24"></uni-icons>
        </view>
      </view>

      <!-- 商品列表 -->
      <template v-if="goods_list.length>0">
        <view class="zhuige-goods-list">
          <view v-for="(item,index) in goods_list" :key="index"
            @click="clickLink('/pages/shop/detail/detail?goods_id=' + item.id)" class="zhuige-goods">
            <image :src="getGoodsThumbnail(item)" mode="aspectFill"></image>
            <view class="zhuige-goods-text">
              <view class="zhuige-goods-title">
                <text v-if="item.badge" class="mark">{{item.badge}}</text>
                <text>{{item.title}}</text>
              </view>
              <view class="zhuige-goods-price" v-if="item.price !== undefined">
                <view class="promotion">
                  <text>￥</text>
                  <text>{{item.price}}</text>
                </view>
                <view class="original" v-if="item.orig_price !== undefined">
                  <text>￥{{item.orig_price}}</text>
                </view>
              </view>
            </view>
          </view>
        </view>
        <uni-load-more :status="loadMore"></uni-load-more>
      </template>
      <template v-else>
        <zhuige-nodata v-if="loaded"></zhuige-nodata>
      </template>
    </view>

    <view v-if="pop_ad" class="zhugie-pop-cover">
      <view @click="clickPopAd" class="zhuige-pop-box">
        <image mode="aspectFit" :src="pop_ad.image"></image>
        <view>
          <uni-icons @click="clickPopAdClose" type="close" size="32" color="#FFFFFF"></uni-icons>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
import Constants from "@/utils/constants.js";
import Util from '@/utils/util';
import Alert from '@/utils/alert';
import Api from '@/utils/api';
import Rest from '@/utils/rest';
import JiangqieNoData from "@/components/zhuige-nodata";
import { mapGetters } from 'vuex'

export default {
  name: "ShopHome",
  components: {
    JiangqieNoData
  },

  data() {
    this.share_title = undefined;
    this.share_thumb = undefined;

    return {
      background: undefined,
      
      title: '',
      nav_color: 'rgb(255, 255, 255)',
      nav_bgcolor: 'rgba(255, 255, 255, 0)',

      slides: [],
      icon_navs: [],
      home_rec: undefined,

      cats: [],
      cat_id: undefined,

      goods_list: [],
      loadMore: 'more',
      loaded: false,
      
      pop_ad: undefined,
    }
  },

  computed: {
    ...mapGetters([
      'getCartCount'
    ])
  },

  onLoad() {
    // 清除旧数据，确保页面刷新时重新加载
    this.goods_list = [];
    this.loaded = false;
    this.loadMore = 'more';
    
    // 加载设置和商品数据
    this.loadSetting();
    this.loadGoods();
  },

  onShow() {
    Util.updateCartBadge(this.getCartCount);
  },

  onPageScroll(e) {
    if (e.scrollTop > 20) {
      let nav_opacity = (e.scrollTop - 20) / 255;
      if (nav_opacity <= 1) {
        let factor = 255 * (1 - nav_opacity);
        this.nav_color = `rgb(${factor}, ${factor}, ${factor})`;
        this.nav_bgcolor = `rgba(255, 255, 255, ${nav_opacity})`;
        this.title = '商城';
      } else if (this.nav_color != 'rgb(255, 255, 255)') {
        this.nav_color = 'rgb(0, 0, 0)';
        this.nav_bgcolor = 'rgba(255, 255, 255, 1)';
      }
      uni.setNavigationBarColor({
        frontColor: '#000000',
        backgroundColor: '#ffffff',
      })
    } else {
      this.nav_color = 'rgb(255, 255, 255)';
      this.nav_bgcolor = 'rgba(255, 255, 255, 0)';
      this.title = '';
      uni.setNavigationBarColor({
        frontColor: '#ffffff',
        backgroundColor: '#ffffff'
      })
    }
  },

  onReachBottom() {
    if (this.loadMore == 'more') {
      this.loadGoods();
    }
  },

  onPullDownRefresh() {
    this.refresh();
  },

  methods: {
    refresh() {
      this.loadSetting();

      this.loaded = false;
      this.goods_list = [];
      this.loadGoods();
    },

    clickLink(link) {
      // 检查链接是否有效
      if (!link || link === '#' || link === 'javascript:;') {
        uni.showToast({
          title: '该功能暂未开放',
          icon: 'none'
        });
        return;
      }
      
      // 直接处理搜索页面和商品详情页面的跳转，避免使用通用的openLink
      if (link === '/pages/shop/search/search') {
        uni.navigateTo({
          url: link,
          fail: (res) => {
            console.error('搜索页面跳转失败:', res);
            uni.showToast({
              title: '搜索功能暂未开放',
              icon: 'none'
            });
          }
        });
        return;
      }
      
      // 处理商品详情页面
      if (link.indexOf('/pages/shop/detail/detail') === 0) {
        uni.navigateTo({
          url: link,
          fail: (res) => {
            console.error('商品详情页面跳转失败:', res);
            uni.showToast({
              title: '商品详情暂未开放',
              icon: 'none'
            });
          }
        });
        return;
      }
      
      // 其他链接使用通用处理
      Util.openLink(link);
    },

    clickTab(cat_id) {
      this.cat_id = cat_id;

      this.loaded = false;
      this.goods_list = [];
      this.loadGoods();
    },

    clickCategory() {
      uni.navigateTo({
        url: '/pages/shop/category/category'
      })
    },

    loadSetting() {
      // 添加调试日志
      console.log('开始加载商城设置');
      
      // 显示加载中提示
      uni.showLoading({
        title: '加载中...'
      });
      
      // 添加时间戳参数，强制不使用缓存（正确的URL参数格式）
      const timestamp = new Date().getTime();
      
      uni.request({
        url: Api.URL('shop', '') + '?_t=' + timestamp, // 修正URL格式，使用?而不是&
        success: (res) => {
          // 添加调试日志
          console.log('商城设置接口返回数据:', res.data);
          
          if (res.data && res.data.code === 0) {
            const data = res.data.data;
            getApp().globalData.appName = data.title;
            getApp().globalData.appDesc = data.desc;

            this.background = data.background;
            this.share_title = data.home_title;
            this.share_thumb = data.thumb;

            // 记录原始数据，便于调试
            console.log('幻灯片数据:', data.slides);
            console.log('导航项数据:', data.icon_navs);
            console.log('推荐商品数据:', data.home_rec);
            console.log('分类数据:', data.cats);

            this.slides = data.slides || [];
            this.icon_navs = data.icon_navs || [];
            
            // 处理推荐商品数据 - 直接使用现有数据，不再请求额外接口
            if (data.home_rec && data.home_rec.posts) {
              // 确保每个商品都有必要的字段
              data.home_rec.posts = data.home_rec.posts.map(post => ({
                ...post,
                price: post.price !== undefined ? post.price : '',
                orig_price: post.orig_price !== undefined ? post.orig_price : '',
                badge: post.badge !== undefined ? post.badge : ''
              }));
              this.home_rec = data.home_rec;
              console.log('处理后的推荐商品数据:', this.home_rec);
            } else {
              this.home_rec = {
                title: '推荐商品',
                posts: []
              };
              console.log('未找到推荐商品数据，使用默认值');
            }

            // 确保cats数组存在且不为空
            if (data.cats && data.cats.length > 0) {
              this.cats = data.cats;
              this.cat_id = this.cats[0].id;
            } else {
              this.cats = [];
              this.cat_id = undefined;
            }
            
            this.pop_ad = Util.getPopAd(data.pop_ad, Constants.ZHUIGE_INDEX_MAXAD_LAST_TIME);
          } else {
            console.error('加载商城设置失败:', res.data);
            
            // 显示错误提示
            uni.showToast({
              title: '加载商城设置失败',
              icon: 'none'
            });
          }
          uni.stopPullDownRefresh();
          uni.hideLoading();
        },
        fail: (err) => {
          console.error('请求商城设置接口失败:', err);
          uni.stopPullDownRefresh();
          uni.hideLoading();
          
          // 显示错误提示
          uni.showToast({
            title: '网络请求失败',
            icon: 'none'
          });
        }
      });
    },

    loadGoods() {
      if (this.loadMore == 'loading') {
        return;
      }
      this.loadMore = 'loading';

      // 显示加载中提示
      if (!this.goods_list.length) {
        uni.showLoading({
          title: '加载中...'
        });
      }

      // 添加时间戳参数，强制不使用缓存
      const timestamp = new Date().getTime();
      
      let params = {
        offset: this.goods_list.length,
        _t: timestamp // 添加时间戳防止缓存
      };

      if (this.cat_id) {
        params.cat_id = this.cat_id;
      }

      // 添加调试日志
      console.log('请求商品列表参数:', params);

      uni.request({
        url: Api.URL('shop', 'last'),
        method: 'GET', // 使用GET请求，与后端接口一致
        data: params,
        success: (res) => {
          // 添加调试日志
          console.log('商品列表接口返回数据:', res.data);
          
          if (res.data && res.data.code === 0) {
            // 确保返回的商品列表数据有效
            const goodsList = res.data.data.list || [];
            
            // 添加调试日志
            console.log('处理前的商品列表:', goodsList);
            
            // 直接处理商品数据，不再请求额外接口
            const processedList = goodsList.map(item => {
              // 确保基本字段存在
              return {
                ...item,
                // 如果没有thumbnail字段，尝试从其他字段获取
                thumbnail: item.thumbnail || (item.featured_image ? item.featured_image : null),
                // 确保价格字段存在
                price: item.price !== undefined ? item.price : '',
                orig_price: item.orig_price !== undefined ? item.orig_price : '',
                // 确保角标字段存在
                badge: item.badge !== undefined ? item.badge : '',
                // 处理幻灯片数据
                slide: item.slide || []
              };
            });
            
            console.log('处理后的商品列表:', processedList);
            
            // 强制刷新数据，确保视图更新
            if (this.goods_list.length === 0) {
              this.goods_list = processedList;
            } else {
              this.goods_list = [...this.goods_list, ...processedList];
            }
            
            this.loadMore = res.data.data.more;
            this.loaded = true;
          } else {
            console.error('加载商品列表失败:', res.data);
            this.loadMore = 'noMore';
            this.loaded = true;
            
            // 显示错误提示
            uni.showToast({
              title: '加载商品列表失败',
              icon: 'none'
            });
          }
          uni.hideLoading();
        },
        fail: (err) => {
          console.error('请求商品列表接口失败:', err);
          this.loadMore = 'noMore';
          this.loaded = true;
          uni.hideLoading();
          
          // 显示错误提示
          uni.showToast({
            title: '网络请求失败',
            icon: 'none'
          });
        }
      });
    },

    clickPopAd() {
      uni.setStorageSync(Constants.ZHUIGE_INDEX_MAXAD_LAST_TIME, new Date().getTime())
      Util.openLink(this.pop_ad.link);
      this.pop_ad = false;
    },

    clickPopAdClose() {
      this.pop_ad = false;
      uni.setStorageSync(Constants.ZHUIGE_INDEX_MAXAD_LAST_TIME, new Date().getTime())
    },

    getGoodsThumbnail(item) {
      // 如果有缩略图且不是默认图片，直接使用
      if (item.thumbnail && !item.thumbnail.includes('zhuige-xcx/public/images')) {
        return item.thumbnail;
      }
      
      // 如果有幻灯片，使用第一张幻灯片图片
      if (item.slide && Array.isArray(item.slide) && item.slide.length > 0) {
        // 处理不同的幻灯片数据格式
        const firstSlide = item.slide[0];
        if (firstSlide.image && firstSlide.image.url) {
          return firstSlide.image.url;
        } else if (firstSlide.image && typeof firstSlide.image === 'string') {
          return firstSlide.image;
        } else if (typeof firstSlide === 'string') {
          return firstSlide;
        }
      }
      
      // 如果有特色图片，使用特色图片
      if (item.featured_image) {
        return item.featured_image;
      }
      
      // 使用内置的默认图片
      return '/static/404.png';
    },
  }
}
</script>

<style lang="scss">
@import "@/style/main.css";

.content {
  display: flex;
  flex-direction: column;
}

.zhuige-nav-search {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, .3);
  border-radius: 30rpx;
  padding: 0 20rpx;
  height: 60rpx;
}

.zhuige-nav-search text {
  margin-left: 10rpx;
  font-size: 28rpx;
}

.zhuige-swiper {
  position: relative;
  width: 100%;
  height: 400rpx;
  margin-top: 20rpx;
}

.zhuige-swiper swiper {
  width: 100%;
  height: 100%;
}

.zhuige-swiper image {
  width: 100%;
  height: 100%;
}

.zhuige-swiper-title {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, .8), transparent);
  color: #fff;
  padding: 20rpx;
  font-size: 28rpx;
}

.zhuige-icon-menu {
  display: flex;
  flex-wrap: wrap;
  padding: 20rpx;
  background: #fff;
  border-radius: 20rpx;
  margin: -50rpx 20rpx 0;
  position: relative;
  z-index: 1;
}

.zhuige-icon-menu > view {
  width: 20%;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 20rpx 0;
}

.zhuige-icon-menu image {
  width: 80rpx;
  height: 80rpx;
  margin-bottom: 10rpx;
}

.zhuige-icon-menu text {
  font-size: 24rpx;
  color: #333;
}

.zhuige-recom {
  margin: 20rpx;
  background: #fff;
  border-radius: 20rpx;
  padding: 20rpx;
}

.zhuige-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20rpx;
}

.zhuige-title view {
  font-size: 32rpx;
  font-weight: bold;
}

.zhuige-title text {
  font-size: 24rpx;
  color: #999;
}

.zhuige-scroll {
  white-space: nowrap;
}

.zhuige-scroll-block {
  display: inline-block;
  width: 300rpx;
  margin-right: 20rpx;
}

.zhuige-scroll-block image {
  width: 100%;
  height: 200rpx;
  border-radius: 10rpx;
}

.zhuige-scroll-block view {
  font-size: 28rpx;
  margin-top: 10rpx;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.zhuige-scroll-block .zhuige-goods-price {
  display: flex;
  align-items: center;
  margin-top: 5rpx;
}

/* 调试信息样式 */
.zhuige-debug-info {
  background: rgba(0,0,0,0.5);
  color: #fff;
  padding: 10rpx;
  font-size: 24rpx;
  display: none; /* 默认隐藏 */
}

.zhuige-goods-group {
  margin: 20rpx;
  background: #fff;
  border-radius: 20rpx;
  padding: 20rpx;
}

.zhuige-goods-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20rpx;
}

.zhuige-goods-scroll {
  flex: 1;
  overflow: hidden;
}

.zhuige-goods-scroll scroll-view {
  white-space: nowrap;
}

.zhuige-goods-scroll view {
  display: inline-block;
  padding: 10rpx 30rpx;
  font-size: 28rpx;
  color: #666;
}

.zhuige-goods-scroll .active {
  color: #ff4400;
  font-weight: bold;
  position: relative;
}

.zhuige-goods-scroll .active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 30rpx;
  right: 30rpx;
  height: 4rpx;
  background: #ff4400;
  border-radius: 2rpx;
}

.zhuige-goods-more {
  padding: 0 20rpx;
}

.zhuige-goods-list {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}

.zhuige-goods {
  width: 48%;
  margin-bottom: 20rpx;
  background: #f9f9f9;
  border-radius: 10rpx;
  overflow: hidden;
}

.zhuige-goods image {
  width: 100%;
  height: 200rpx;
}

.zhuige-goods-text {
  padding: 10rpx;
}

.zhuige-goods-title {
  font-size: 28rpx;
  margin-bottom: 10rpx;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.zhuige-goods-title .mark {
  background: #ff4400;
  color: #fff;
  font-size: 20rpx;
  padding: 2rpx 10rpx;
  border-radius: 4rpx;
  margin-right: 10rpx;
}

.zhuige-goods-price {
  display: flex;
  align-items: center;
}

.promotion {
  color: #ff4400;
  font-size: 32rpx;
  font-weight: bold;
  margin-right: 10rpx;
}

.promotion text:first-child {
  font-size: 24rpx;
}

.original {
  color: #999;
  font-size: 24rpx;
  text-decoration: line-through;
}

/**
 * 弹窗 start
 */
.zhugie-pop-cover {
  position: fixed;
  height: 100%;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, .6);
  z-index: 998;
  top: 0;
  left: 0;
}

.zhuige-pop-box {
  width: 600rpx;
  height: 600rpx;
  position: relative;
  text-align: center;
}

.zhuige-pop-box image {
  height: 100%;
  width: 100%;
}

.zhuige-pop-box view {
  position: absolute;
  bottom: -48rpx;
  height: 48rpx;
  width: 48rpx;
  z-index: 999;
  left: 50%;
  margin-left: -24rpx;
}
/**
 * 弹窗 end
 */
</style>
