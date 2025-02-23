<template>
  <view class="sgztmk-module-wrapper">
    <!-- 专题模块头部：icon + 大标题 -->
    <view class="module-header">
      <image class="header-icon" :src="leftModule.icon" mode="aspectFit"></image>
      <text class="header-title">{{ moduleTitle }}</text>
    </view>

    <!-- 整个模块内容容器 -->
    <view class="sgztmk-module-container" v-show="isLoaded">
      <!-- 左侧模块 -->
      <view class="left-module" @click="handleClick(leftModule.link)" :style="{ 'background-color': leftModule.bgColor }">
        <!-- 显示圆形数值区域，居中显示 -->
        <view class="value-circle" v-if="leftModule.value">
          <text class="value-text">{{ Math.round(leftModule.value) }}</text>
        </view>
        <view class="module-content">
          <text class="module-title">{{ leftModule.title }}</text>
          <text class="module-desc">{{ leftModule.description }}</text>
          <view class="module-button" v-if="leftModule.buttonText">
            <text>{{ leftModule.buttonText }}</text>
          </view>
        </view>
        <image
          v-if="leftModule.image"
          class="module-image"
          :src="leftModule.image"
          mode="aspectFit"
          lazy-load
          @load="handleImageLoad"
        ></image>
      </view>

      <!-- 右侧模块容器 -->
      <view class="right-modules">
        <!-- 右上模块 -->
        <view class="right-module" @click="handleClick(rightTopModule.link)" :style="{ 'background-color': rightTopModule.bgColor }">
          <view class="module-content">
            <text class="module-title">{{ rightTopModule.title }}</text>
            <text class="module-desc">{{ rightTopModule.description }}</text>
            <view class="module-button" v-if="rightTopModule.buttonText">
              <text>{{ rightTopModule.buttonText }}</text>
            </view>
          </view>
          <image
            v-if="rightTopModule.image"
            class="module-image"
            :src="rightTopModule.image"
            mode="aspectFit"
            lazy-load
            @load="handleImageLoad"
          ></image>
        </view>

        <!-- 右下模块 -->
        <view class="right-module" @click="handleClick(rightBottomModule.link)" :style="{ 'background-color': rightBottomModule.bgColor }">
          <view class="module-content">
            <text class="module-title">{{ rightBottomModule.title }}</text>
            <text class="module-desc">{{ rightBottomModule.description }}</text>
            <view class="module-button" v-if="rightBottomModule.buttonText">
              <text>{{ rightBottomModule.buttonText }}</text>
            </view>
          </view>
          <image
            v-if="rightBottomModule.image"
            class="module-image"
            :src="rightBottomModule.image"
            mode="aspectFit"
            lazy-load
            @load="handleImageLoad"
          ></image>
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
    moduleTitle: {
      type: String,
      required: true,
      default: ''
    },
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
        bgColor: '#F0F8FF'
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
  computed: {
    expectedImages() {
      let count = 0;
      if (this.leftModule.image) count++;
      if (this.rightTopModule.image) count++;
      if (this.rightBottomModule.image) count++;
      return count;
    }
  },
  methods: {
    handleClick(link) {
      if (!link) return;
      
      if (typeof link === 'object' && link.type === 'miniprogram') {
        uni.navigateToMiniProgram({
          appId: link.appId,
          path: link.path,
          timeout: 10000,
          success: () => {},
          fail: (err) => {
            uni.showToast({
              title: '跳转失败，请重试',
              icon: 'none',
              duration: 2000
            });
          }
        });
        return;
      }
      
      if (link.startsWith('/pages/')) {
        const cleanLink = link.replace(/\.vue$/, '');
        const maxRetries = 3;
        let retryCount = 0;
      
        const tryNavigate = () => {
          uni.navigateTo({
            url: cleanLink,
            timeout: 10000,
            success: () => {},
            fail: (err) => {
              if (retryCount < maxRetries) {
                retryCount++;
                setTimeout(tryNavigate, 1000);
              } else {
                uni.showToast({
                  title: '跳转失败，请稍后再试',
                  icon: 'none',
                  duration: 2000
                });
                uni.reLaunch({
                  url: cleanLink,
                  fail: () => {}
                });
              }
            }
          });
        };
      
        tryNavigate();
      } else if (link.startsWith('http://') || link.startsWith('https://')) {
        uni.navigateTo({
          url: `/pages/base/webview/webview?url=${encodeURIComponent(link)}`,
          timeout: 10000,
          fail: (err) => {
            uni.showToast({
              title: '链接跳转失败，请重试',
              icon: 'none',
              duration: 2000
            });
          }
        });
      }
    },
    handleImageLoad() {
      this.loadedImages++;
      if (this.loadedImages >= this.expectedImages) {
        this.isLoaded = true;
      }
    }
  },
  created() {
    if (this.expectedImages === 0) {
      this.isLoaded = true;
    }
  }
};
</script>

<style lang="scss" scoped>
.sgztmk-module-wrapper {
  background: #fff;
  border-radius: 24rpx;
  overflow: hidden;
  margin-bottom: 20rpx;
}

.module-header {
  display: flex;
  align-items: center;
  padding: 10rpx; // 缩小内边距
  margin-bottom: 5rpx; // 缩小标题与内容的间距
}

.header-icon {
  width: 40rpx;
  height: 40rpx;
  margin-right: 5rpx; // 缩小图标与标题之间的间距
}

.header-title {
  font-size: 32rpx;
  color: #000;
  font-weight: normal;
}

.sgztmk-module-container {
  display: flex;
  gap: 10rpx; // 缩小左右模块间隔
  padding: 10rpx; // 缩小内边距
  height: 420rpx;
}

.left-module {
  flex: 1;
  height: 100%;
  border-radius: 16rpx;
  position: relative;
  overflow: hidden;
}

.value-circle {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 3;
}

.value-text {
  font-size: 80rpx;
  font-weight: bold;
  color: #1890ff;
  text-shadow: 2rpx 2rpx 4rpx rgba(0, 0, 0, 0.2);
  transform: translateY(-4rpx);
}

.right-modules {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 10rpx; // 缩小右侧模块间隔
  height: 100%;
}

.right-module {
  flex: 1;
  border-radius: 16rpx;
  position: relative;
  overflow: hidden;
}

.module-content {
  position: absolute;
  z-index: 2;
  padding: 10rpx; // 缩小内容区域内边距
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: flex-start; // 内容靠左上对齐
  box-sizing: border-box;
  gap: 8rpx; // 缩小标题、描述、按钮之间的间距
}

.module-title {
  font-size: 28rpx;
  font-weight: normal;
  color: #000;
}

.module-desc {
  font-size: 24rpx;
  color: #0863cc;
  text-decoration: underline;
  cursor: pointer;
}

.left-module .module-desc {
  color: #666;
  text-decoration: none;
  margin-bottom: 0; // 去除默认下边距
}

.module-button {
  display: inline-flex;
  padding: 6rpx 16rpx;
  background: #0863cc;
  border-radius: 20rpx;
  align-self: flex-start;
}

.module-button text {
  font-size: 24rpx;
  color: #fff;
  white-space: nowrap;
}

.module-image {
  position: absolute;
  right: 10rpx; // 调整图片位置
  bottom: 10rpx;
  width: 120rpx;
  height: 120rpx;
  z-index: 1;
}

.left-module .module-image {
  right: 50%;
  transform: translateX(50%);
  width: 140rpx;
  height: 140rpx;
}
</style>
