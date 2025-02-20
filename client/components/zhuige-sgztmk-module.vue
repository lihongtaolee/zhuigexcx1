<template>
  <view class="sgztmk-module-wrapper">
    <!-- 专题模块头部：icon+大标题 -->
    <view class="module-header">
      <image class="header-icon" :src="leftModule.icon" mode="aspectFit"></image>
      <text class="header-title">{{leftModule.title}}</text>
    </view>

    <view class="sgztmk-module-container" v-show="isLoaded">
      <!-- 左侧模块 -->
      <view class="left-module" @click="handleClick(leftModule.link)" :style="{'background-color': leftModule.bgColor}">
        <!-- 新增：圆形数值展示区域 -->
        <view class="value-circle" v-if="leftModule.value !== undefined">
          <text class="value-text">{{leftModule.value}}</text>
        </view>
        <view class="module-content">
          <text class="module-title">{{ leftModule.title }}</text>
          <text class="module-desc">{{ leftModule.description }}</text>
          <view class="module-button" v-if="leftModule.buttonText">
            <text>{{ leftModule.buttonText }}</text>
          </view>
        </view>
        <image class="module-image" :src="leftModule.image" mode="aspectFit" lazy-load @load="handleImageLoad"></image>
      </view>

      <!-- 右侧模块容器 -->
      <view class="right-modules">
        <!-- 右上模块 -->
        <view class="right-module" @click="handleClick(rightTopModule.link)" :style="{'background-color': rightTopModule.bgColor}">
          <view class="module-content">
            <text class="module-title">{{ rightTopModule.title }}</text>
            <text class="module-desc">{{ rightTopModule.description }}</text>
            <view class="module-button" v-if="rightTopModule.buttonText">
              <text>{{ rightTopModule.buttonText }}</text>
            </view>
          </view>
          <image class="module-image" :src="rightTopModule.image" mode="aspectFit" lazy-load @load="handleImageLoad"></image>
        </view>

        <!-- 右下模块 -->
        <view class="right-module" @click="handleClick(rightBottomModule.link)" :style="{'background-color': rightBottomModule.bgColor}">
          <view class="module-content">
            <text class="module-title">{{ rightBottomModule.title }}</text>
            <text class="module-desc">{{ rightBottomModule.description }}</text>
            <view class="module-button" v-if="rightBottomModule.buttonText">
              <text>{{ rightBottomModule.buttonText }}</text>
            </view>
          </view>
          <image class="module-image" :src="rightBottomModule.image" mode="aspectFit" lazy-load @load="handleImageLoad"></image>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
export default {
  name: 'zhuige-sgztmk-module',
  data() {
    return {
      loadedImages: 0,
      isLoaded: false
    }
  },
  props: {
    leftModule: {
      type: Object,
      required: true,
      default: () => ({
        title: '',
        icon: '',
        description: '',
        image: '',
        buttonText: '',
        link: '',
        value: undefined,
        bgColor: '#F0F8FF',
        valueApi: ''
      })
    },
    rightTopModule: {
      type: Object,
      required: true,
      default: () => ({
        title: '',
        description: '',
        image: '',
        buttonText: '',
        link: '',
        bgColor: '#F5F5F5'
      })
    },
    rightBottomModule: {
      type: Object,
      required: true,
      default: () => ({
        title: '',
        description: '',
        image: '',
        buttonText: '',
        link: '',
        bgColor: ''
      })
    }
  },
  methods: {
    handleClick(link) {
      if (link) {
        uni.navigateTo({
          url: link
        });
      }
    },
    handleImageLoad() {
      this.loadedImages++;
      if (this.loadedImages === 3) { // 所有图片加载完成
        this.isLoaded = true;
      }
    },
    async fetchValue() {
      if (this.leftModule.valueApi) {
        try {
          const response = await uni.request({
            url: this.leftModule.valueApi,
            method: 'GET'
          });
          if (response.data && response.data.value !== undefined) {
            this.leftModule.value = Math.min(100, Math.max(0, response.data.value));
          }
        } catch (error) {
          console.error('获取数值失败:', error);
        }
      }
    }
  },
  created() {
    // 如果没有图片，直接显示模块
    if (!this.leftModule.image && !this.rightTopModule.image && !this.rightBottomModule.image) {
      this.isLoaded = true;
    }
    // 获取左侧数值
    this.fetchValue();
  }
};
</script>

<style lang="scss" scoped>
.sgztmk-module-wrapper {
  background: #fff;
  border-radius: 24rpx;
  overflow: hidden;
}

/* 专题模块头部 */
.module-header {
  display: flex;
  align-items: center;
  padding: 20rpx;
  margin-bottom: 0;
}

.header-icon {
  width: 40rpx;
  height: 40rpx;
  margin-right: 10rpx;
}

.header-title {
  font-size: 32rpx;
  color: #000;
  font-weight: normal;
}

/* 主体模块容器 */
.sgztmk-module-container {
  display: flex;
  gap: 20rpx;
  padding: 20rpx;
  height: 420rpx;
}

/* 左侧模块 */
.left-module {
  flex: 1;
  height: 100%;
  border-radius: 16rpx;
  position: relative;
  overflow: hidden;
}

/* 新增：圆形数值展示区域 */
.value-circle {
  position: absolute;
  top: 20rpx;
  right: 20rpx;
  width: 100rpx;
  height: 100rpx;
  background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.6));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 4rpx 4rpx 10rpx rgba(0,0,0,0.1),
              -4rpx -4rpx 10rpx rgba(255,255,255,0.5);
  z-index: 3;
}

.value-text {
  font-size: 36rpx;
  font-weight: bold;
  background: linear-gradient(135deg, #1890ff, #096dd9);
  -webkit-background-clip: text;
  color: transparent;
}

/* 右侧模块容器 */
.right-modules {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 20rpx;
  height: 100%;
}

/* 右侧单个模块 */
.right-module {
  flex: 1;
  border-radius: 16rpx;
  position: relative;
  overflow: hidden;
}

/* 模块内容 */
.module-content {
  position: absolute;
  z-index: 2;
  padding: 20rpx;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  box-sizing: border-box;
}

.module-title {
  font-size: 28rpx;
  font-weight: normal;
  color: #000;
  margin-bottom: 10rpx;
}

.module-desc {
  font-size: 24rpx;
  color: #666;
  margin-bottom: 20rpx;
}

.module-button {
  display: inline-block;
  padding: 8rpx 20rpx;
  background: rgba(0,0,0,0.1);
  border-radius: 20rpx;
}

.module-button text {
  font-size: 24rpx;
  color: #333;
}

.module-image {
  position: absolute;
  right: 20rpx;
  bottom: 20rpx;
  width: 120rpx;
  height: 120rpx;
  z-index: 1;
}
</style>