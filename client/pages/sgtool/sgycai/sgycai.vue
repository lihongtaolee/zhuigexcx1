<template>
    <view class="page-container">
      <view class="container">
        <view class="header">
          <text class="title">宝宝档案录入</text>
          <text class="subtitle">记录宝宝的成长轨迹～</text>
        </view>
        <view class="content">
          <!-- 宝宝基础信息 -->
          <view class="section">
            <text class="section-title">宝宝基础信息</text>
            <view class="input-group">
              <text>宝宝姓名:</text>
              <input class="cute-input" type="text" v-model="baobaoname" placeholder="请输入宝宝姓名" />
            </view>
            <view class="input-group">
              <text>性别:</text>
              <view class="gender-select">
                <text :class="['gender-option', gender === 1 ? 'active' : '']" @click="gender = 1">男宝</text>
                <text :class="['gender-option', gender === 2 ? 'active' : '']" @click="gender = 2">女宝</text>
              </view>
            </view>
            <view class="input-group">
              <text>出生日期:</text>
              <picker class="cute-input" mode="date" :value="birthday" :end="today" @change="onBirthdayChange">
                <view>{{ birthday || '请选择出生日期' }}</view>
              </picker>
            </view>
            <view class="input-group">
              <text>爸爸身高(cm):</text>
              <input class="cute-input" type="digit" v-model="fatherHeight" placeholder="请输入爸爸身高" />
            </view>
            <view class="input-group">
              <text>妈妈身高(cm):</text>
              <input class="cute-input" type="digit" v-model="motherHeight" placeholder="请输入妈妈身高" />
            </view>
            <view class="input-group">
              <text>所在城市:</text>
              <!-- 使用 uni-app 默认 region picker 返回三级地址 -->
              <picker class="cute-input" mode="region" :value="city" @change="onCityChange">
                <view>{{ cityText || '请选择所在城市' }}</view>
              </picker>
            </view>
          </view>
          <!-- 动态评测信息 -->
          <view class="section">
            <text class="section-title">动态评测信息</text>
            <view class="input-group">
              <text>现在实测身高(cm):</text>
              <input class="cute-input" type="digit" v-model="currentHeight" placeholder="请输入现在实测身高" />
            </view>
            <view class="input-group">
              <text>期望成年身高(cm):</text>
              <input class="cute-input" type="digit" v-model="targetHeight" placeholder="请输入期望成年身高" />
            </view>
            <view class="input-group">
              <text>现在骨龄(岁):</text>
              <input class="cute-input" type="digit" v-model="boneAge" placeholder="选填" />
            </view>
            <view class="input-group">
              <text>现在体重(kg):</text>
              <input class="cute-input" type="digit" v-model="weight" placeholder="请输入现在体重" />
            </view>
            <view class="input-group">
              <text>测量时间:</text>
              <!-- 给 picker 添加 ref 便于调试 -->
              <picker ref="datetimePicker" class="cute-input" mode="date" :value="measureTime" :end="today" @change="onMeasureTimeChange">
                <view>{{ measureTime || '请选择测量时间' }}</view>
              </picker>
            </view>
          </view>
          <button class="cute-button" @click="submitData" :disabled="isSubmitting">
            {{ isSubmitting ? '保存中...' : '保存档案' }}
          </button>
    
          <view v-if="isSubmitting" class="loading-container">
            <view class="loading-spinner"></view>
            <text class="loading-text">正在保存...</text>
          </view>
    
          <view v-if="errorMessage" class="error-message">
            <text>{{ errorMessage }}</text>
          </view>
        </view>
      </view>
    
      <view class="bottom-section">
        <view class="tips-container">
          <view class="tips-header">
            <view class="tips-icon">📌</view>
            <text class="tips-title">温馨提示</text>
          </view>
          <view class="tips-content">
            <text class="tips-text">定期记录宝宝的身高数据，有助于更好地追踪生长发育情况。建议每3个月测量一次身高体重。</text>
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
        baobaoname: '',
        gender: 1,
        birthday: '',
        fatherHeight: '',
        motherHeight: '',
        city: [],      // region picker 返回的数组
        cityText: '',  // 拼接后的三级地址字符串
        currentHeight: '',
        targetHeight: '',
        boneAge: '',
        weight: '',
        measureTime: '',
        errorMessage: '',
        isSubmitting: false,
        today: new Date().toISOString().split('T')[0],
        apiBaseUrl: 'https://x.erquhealth.com/wp-json/zhuige/sgtool',
        userId: null
      }
    },
    methods: {
      onBirthdayChange(e) {
        this.birthday = e.detail.value;
      },
      onMeasureTimeChange(e) {
        this.measureTime = e.detail.value;
      },
      onCityChange(e) {
        this.city = e.detail.value;
        this.cityText = this.city.join(' ');
      },
      async checkLoginStatus() {
        let user = Auth.getUser();
        if (!user || !user.token) {
          user = uni.getStorageSync('user');
        }
        if (!user || !user.token) {
          wx.showModal({
            title: '温馨提示',
            content: '请先登录后再录入宝宝档案',
            showCancel: false,
            confirmText: '去登录',
            success: (res) => {
              if (res.confirm) {
                uni.redirectTo({
                  url: '/pages/user/login/login?type=login&tip=录入宝宝档案'
                });
              } else {
                Util.navigateBack();
              }
            }
          });
          throw new Error('未登录');
        }
        if (!user.mobile) {
          wx.showModal({
            title: '温馨提示',
            content: '为了完善您的用户信息，请先绑定手机号',
            showCancel: false,
            confirmText: '去绑定',
            success: (res) => {
              if (res.confirm) {
                uni.redirectTo({
                  url: '/pages/user/login/login?type=mobile&tip=录入宝宝档案'
                });
              } else {
                Util.navigateBack();
              }
            }
          });
          throw new Error('未绑定手机号');
        }
        this.userId = user.user_id;
        return user;
      },
      async submitData() {
        this.errorMessage = '';
        let user;
        try {
          user = await this.checkLoginStatus();
        } catch (error) {
          return;
        }
    
        if (!this.baobaoname || !this.birthday || !this.currentHeight || !this.fatherHeight || !this.motherHeight || !this.cityText) {
          this.errorMessage = '请填写必填项（宝宝姓名、出生日期、实测身高、父母身高、所在城市）';
          return;
        }
    
        const fHeight = parseFloat(this.fatherHeight);
        const mHeight = parseFloat(this.motherHeight);
        if (fHeight < 140 || fHeight > 220 || mHeight < 140 || mHeight > 220) {
          this.errorMessage = '请输入合理的父母身高数值(140-220cm)';
          return;
        }
    
        const height = parseFloat(this.currentHeight);
        if (height < 30 || height > 200) {
          this.errorMessage = '请输入合理的实测身高数值(30-200cm)';
          return;
        }
    
        if (this.weight) {
          const weight = parseFloat(this.weight);
          if (weight < 2 || weight > 100) {
            this.errorMessage = '请输入合理的体重数值(2-100kg)';
            return;
          }
        }
    
        if (this.boneAge) {
          const boneAge = parseFloat(this.boneAge);
          if (boneAge < 0 || boneAge > 18) {
            this.errorMessage = '请输入合理的骨龄数值(0-18岁)';
            return;
          }
        }
    
        this.isSubmitting = true;
    
        try {
          const requestData = {
            user_id: this.userId,
            baobaoname: this.baobaoname,
            gender: this.gender,
            birthday: this.birthday,
            father_height: this.fatherHeight,
            mother_height: this.motherHeight,
            city: this.cityText,
            current_height: this.currentHeight,
            target_height: this.targetHeight || null,
            bone_age: this.boneAge || null,
            weight: this.weight || null,
            measure_time: this.measureTime || null
          };
    
          const response = await new Promise((resolve, reject) => {
            uni.request({
              url: `${this.apiBaseUrl}/save_user_data`,
              method: 'POST',
              data: requestData,
              header: {
                'content-type': 'application/json',
                'Authorization': 'Bearer ' + user.token
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
    
          if (response.code === 0) {
            uni.showToast({
              title: '保存成功',
              icon: 'success',
              duration: 2000
            });
            setTimeout(() => {
              uni.navigateBack();
            }, 2000);
          } else {
            this.errorMessage = response.msg || '保存失败，请重试';
          }
        } catch (error) {
          this.errorMessage = '网络连接失败，请重试';
        } finally {
          this.isSubmitting = false;
        }
      }
    },
    onShow() {
      this.checkLoginStatus().catch(() => {});
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
  .section {
    margin-bottom: 20px;
  }
  .section-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #FF6B6B;
  }
  .input-group {
    margin-bottom: 15px;
    display: flex;
    align-items: center;
  }
  .input-group text {
    width: 120px;
    color: #666;
  }
  .cute-input {
    flex: 1;
    height: 40px;
    border: 2px solid #FFB6C1;
    border-radius: 20px;
    padding: 0 15px;
    background-color: white;
  }
  .gender-select {
    flex: 1;
    display: flex;
    gap: 10px;
  }
  .gender-option {
    flex: 1;
    text-align: center;
    padding: 8px 0;
    background-color: #FFE4E1;
    border-radius: 20px;
    color: #666;
  }
  .gender-option.active {
    background-color: #FF6B6B;
    color: white;
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
  .error-message {
    color: #FF6B6B;
    text-align: center;
    margin-top: 10px;
  }
  .bottom-section {
    margin-top: auto;
    padding: 0 15px;
  }
  .tips-container {
    background: rgba(255,255,255,0.9);
    border-radius: 15px;
    padding: 15px;
    margin: 10px 0;
    box-shadow: 0 2px 10px rgba(255,107,107,0.1);
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
  .background-image {
    width: 80%;
    height: 200px;
    margin: 10px auto;
    opacity: 0.2;
    display: block;
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  </style>
  