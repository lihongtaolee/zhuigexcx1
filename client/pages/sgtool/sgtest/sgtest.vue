<template>
  <view class="page-container">
    <view class="container">
      <view class="header">
        <text class="title">宝贝遗传身高预测</text>
        <text class="subtitle">来看看你未来会长多高呢～</text>
      </view>
      <view class="content">
        <view class="input-group">
          <text>爸爸身高:</text>
          <input class="cute-input" type="digit" v-model="fatherHeight" placeholder="请输入爸爸身高" />
          <text class="unit">cm</text>
        </view>
        <view class="input-group">
          <text>妈妈身高:</text>
          <input class="cute-input" type="digit" v-model="motherHeight" placeholder="请输入妈妈身高" />
          <text class="unit">cm</text>
        </view>
        <button class="cute-button" @click="calculateHeight" :disabled="isCalculating">
          {{ isCalculating ? '预测中...' : '开始神奇预测' }}
        </button>

        <view v-if="isCalculating" class="loading-container">
          <view class="loading-spinner"></view>
          <text class="loading-text">正在计算中...</text>
        </view>

        <view v-if="showSuccess" class="success-message">
          <text>预测完成！</text>
        </view>

        <view v-if="predictedHeight" class="result" :class="{'fade-in': showSuccess}">
          <view class="segmented-control">
            <text class="segment" :class="{active: isBoy}" @click="isBoy = true">小王子</text>
            <text class="segment" :class="{active: !isBoy}" @click="isBoy = false">小公主</text>
          </view>
          <view class="prediction-result">
            <text class="result-text">未来身高大约是：</text>
            <text class="height-number">{{ isBoy ? boyHeight : girlHeight }}</text>
            <text class="unit">cm</text>
          </view>
        </view>
        <view v-if="errorMessage" class="error-message">
          <text>{{ errorMessage }}</text>
        </view>
      </view>
    </view>

    <view class="bottom-section">
      <view class="slogan">
        <text>已有10000+小朋友参与了身高预测</text>
      </view>

      <view class="tips-container">
        <view class="tips-header">
          <view class="tips-icon">📌</view>
          <text class="tips-title">温馨提示</text>
        </view>
        <view class="tips-content">
          <text class="tips-text">身高受到多种因素的影响，包括遗传、营养、环境、生活习惯等，而遗传只是其中一个重要因素。</text>
          <view class="contact-info">
            <text class="highlight-text">全方位的身高管理解决方案</text>
            <view class="contact-detail">
              <text>请咨询客服微信：</text>
              <text class="wx-id">erquzhuli</text>
            </view>
            <text class="result-highlight">轻松长高10cm</text>
          </view>
        </view>
      </view>

      <image class="background-image" src="/static/sgtoolimages/giraffe.png" mode="aspectFit"></image>
    </view>
  </view>
</template>

<script>
import Auth from '@/utils/auth.js';
import Util from '@/utils/util.js';

export default {
  data() {
    return {
      fatherHeight: '',
      motherHeight: '',
      predictedHeight: false,
      isBoy: true,
      boyHeight: 0,
      girlHeight: 0,
      errorMessage: '',
      apiBaseUrl: 'https://x.erquhealth.com/wp-json/zhuige-xcx/v1',
      isCalculating: false,
      showSuccess: false,
      userId: null,
    }
  },
  methods: {
    async calculateHeight() {
      this.errorMessage = '';
      this.predictedHeight = false;
      this.showSuccess = false;

      if (!this.fatherHeight || !this.motherHeight) {
        this.errorMessage = '请输入父母身高';
        return;
      }

      const fHeight = parseFloat(this.fatherHeight);
      const mHeight = parseFloat(this.motherHeight);

      if (fHeight < 140 || fHeight > 220 || mHeight < 140 || mHeight > 220) {
        this.errorMessage = '请输入合理的身高数值(140-220cm)';
        return;
      }

      this.isCalculating = true;

      const localBoyHeight = ((fHeight + mHeight + 13) / 2).toFixed(1);
      const localGirlHeight = ((fHeight + mHeight - 13) / 2).toFixed(1);

      try {
        const requestData = {
          fatherHeight: fHeight,
          motherHeight: mHeight,
          user_id: this.userId,
        };

        const response = await new Promise((resolve, reject) => {
          uni.request({
            url: `${this.apiBaseUrl}/save-height`,
            method: 'POST',
            data: requestData,
            header: {
              'content-type': 'application/json'
            },
            success: (res) => {
              if (res.statusCode === 200) {
                resolve(res.data);
              } else {
                reject(new Error('网络请求失败'));
              }
            },
            fail: (err) => {
              reject(err);
            }
          });
        });

        await new Promise(resolve => setTimeout(resolve, 1000));

        if (response.code === 200) {
          this.boyHeight = parseFloat(response.data.boyHeight);
          this.girlHeight = parseFloat(response.data.girlHeight);
          this.showSuccess = true;
          setTimeout(() => {
            this.predictedHeight = true;
          }, 500);
          this.errorMessage = '';
        } else {
          this.boyHeight = parseFloat(localBoyHeight);
          this.girlHeight = parseFloat(localGirlHeight);
          this.showSuccess = true;
          setTimeout(() => {
            this.predictedHeight = true;
          }, 500);
          this.errorMessage = response.msg || '数据保存失败，显示本地计算结果';
        }
      } catch (error) {
        this.boyHeight = parseFloat(localBoyHeight);
        this.girlHeight = parseFloat(localGirlHeight);
        this.showSuccess = true;
        setTimeout(() => {
          this.predictedHeight = true;
        }, 500);
        this.errorMessage = '网络连接失败，显示本地计算结果';
      } finally {
        this.isCalculating = false;
      }
    },
    async checkLoginStatus() {
      const user = Auth.getUser();

      if (!user || !user.token) {
        return new Promise((resolve, reject) => {
          wx.showModal({
            title: '温馨提示',
            content: '为了记录您的身高预测信息，请先登录',
            showCancel: false,
            confirmText: '去登录',
            success: (res) => {
              if (res.confirm) {
                uni.redirectTo({
                  url: '/pages/user/login/login?type=login&tip=使用身高预测功能'
                });
              } else {
                Util.navigateBack();
              }
              reject(new Error('未登录'));
            }
          });
        });
      }

      if (!user.mobile) {
        return new Promise((resolve, reject) => {
          wx.showModal({
            title: '温馨提示',
            content: '为了完善您的用户信息，请先绑定手机号',
            showCancel: false,
            confirmText: '去绑定',
            success: (res) => {
              if (res.confirm) {
                uni.redirectTo({
                  url: '/pages/user/login/login?type=mobile&tip=使用身高预测功能'
                });
              } else {
                Util.navigateBack();
              }
              reject(new Error('未绑定手机号'));
            }
          });
        });
      }

      // 正确获取用户ID，并赋值到组件实例
      this.userId = user.user_id;
      return Promise.resolve(user);
    }
  },
  onLoad() {
    this.checkLoginStatus();
  }
}
</script>

<style scoped>
.page-container {
  position: relative;
  min-height: 100vh;
  width: 100%;
  display: flex;
  flex-direction: column;
  background-color: #FFF5E7;
}
.container {
  flex: 1;
  padding-bottom: 20px;
}
.header {
  text-align: center;
  margin: 30px 0;
}
.title {
  font-size: 24px;
  font-weight: bold;
  color: #FF6B6B;
}
.subtitle {
  font-size: 14px;
  color: #888;
  margin-top: 5px;
  display: block;
}
.content {
  padding: 20px;
  background-color: rgba(255,255,255,0.8);
  border-radius: 20px;
  margin: 0 15px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.input-group {
  margin-bottom: 15px;
  display: flex;
  align-items: center;
}
.input-group text {
  width: 80px;
  color: #666;
}
.cute-input {
  flex: 1;
  height: 40px;
  border: 2px solid #FFB6C1;
  border-radius: 20px;
  padding: 0 15px;
  background-color: white;
  margin-right: 5px;
}
.unit {
  color: #666;
  margin-left: 5px;
}
.cute-button {
  width: 80%;
  height: 45px;
  margin: 20px auto;
  background: linear-gradient(45deg, #FF6B6B, #FFB6C1);
  color: white;
  border-radius: 25px;
  font-size: 18px;
  font-weight: bold;
  box-shadow: 0 4px 8px rgba(255,107,107,0.3);
}
.cute-button:disabled {
  opacity: 0.7;
  background: linear-gradient(45deg, #FFB6C1, #FFC0CB);
}
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 20px 0;
}
.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #FFE4E1;
  border-top: 4px solid #FF6B6B;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
.loading-text {
  margin-top: 10px;
  color: #666;
  font-size: 14px;
}
.success-message {
  text-align: center;
  color: #4CAF50;
  margin: 10px 0;
  font-size: 16px;
  animation: fadeIn 0.5s ease-in;
}
.segmented-control {
  display: flex;
  margin: 15px 0;
  background-color: #FFE4E1;
  border-radius: 20px;
  padding: 4px;
}
.segment {
  flex: 1;
  text-align: center;
  padding: 10px 0;
  border-radius: 16px;
}
.segment.active {
  background-color: #FF6B6B;
  color: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.prediction-result {
  text-align: center;
  margin-top: 20px;
  padding: 15px;
  background-color: #FFF0F5;
  border-radius: 15px;
}
.result-text {
  font-size: 16px;
  color: #666;
}
.height-number {
  font-size: 28px;
  font-weight: bold;
  color: #FF6B6B;
  margin: 0 5px;
}
.error-message {
  color: #FF6B6B;
  text-align: center;
  margin-top: 10px;
}
.bottom-section {
  margin-top: auto;
  padding: 0 15px;
}
.slogan {
  text-align: center;
  padding: 15px 0;
  color: #888;
  font-size: 14px;
}
.tips-container {
  background: rgba(255, 255, 255, 0.9);
  border-radius: 15px;
  padding: 15px;
  margin: 10px 0;
  box-shadow: 0 2px 10px rgba(255, 107, 107, 0.1);
}
.tips-header {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}
.tips-icon {
  font-size: 20px;
  margin-right: 8px;
}
.tips-title {
  color: #FF6B6B;
  font-size: 16px;
  font-weight: bold;
}
.tips-content {
  padding: 5px 0;
}
.tips-text {
  color: #666;
  font-size: 14px;
  line-height: 1.6;
}
.contact-info {
  margin-top: 10px;
  text-align: center;
}
.highlight-text {
  color: #FF6B6B;
  font-size: 15px;
  font-weight: bold;
  display: block;
  margin: 8px 0;
}
.contact-detail {
  background: #FFF5E7;
  padding: 8px 15px;
  border-radius: 20px;
  display: inline-block;
  margin: 5px 0;
}
.wx-id {
  color: #FF6B6B;
  font-weight: bold;
  margin-left: 5px;
}
.result-highlight {
  color: #FF6B6B;
  font-size: 16px;
  font-weight: bold;
  display: inline-block;
  margin-top: 8px;
  background: linear-gradient(45deg, #FFE4E1, #FFF5E7);
  padding: 8px 20px;
  border-radius: 20px;
}
.background-image {
  width: 80%;
  height: 200px;
  margin: 10px auto;
  opacity: 0.2;
  display: block;
}
.fade-in {
  animation: fadeIn 0.5s ease-in;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>
