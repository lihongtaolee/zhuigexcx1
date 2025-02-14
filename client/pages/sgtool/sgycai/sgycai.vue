<template>
  <view class="page-container">
    <view class="container">
      <view class="header">
        <text class="title">宝宝档案录入</text>
        <text class="subtitle">记录宝宝的成长轨迹～</text>
      </view>
      <view class="content">
        <view class="input-group">
          <text>宝宝昵称:</text>
          <input class="cute-input" type="text" v-model="nickname" placeholder="请输入宝宝昵称" />
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
            <view>{{birthday || '请选择出生日期'}}</view>
          </picker>
        </view>
        <view class="input-group">
          <text>骨龄(岁):</text>
          <input class="cute-input" type="digit" v-model="boneAge" placeholder="选填" />
        </view>
        <view class="input-group">
          <text>体重(kg):</text>
          <input class="cute-input" type="digit" v-model="weight" placeholder="选填" />
        </view>
        <view class="input-group">
          <text>身高(cm):</text>
          <input class="cute-input" type="digit" v-model="currentHeight" placeholder="请输入当前身高" />
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
      nickname: '',
      gender: 1,
      birthday: '',
      boneAge: '',
      weight: '',
      currentHeight: '',
      errorMessage: '',
      isSubmitting: false,
      today: new Date().toISOString().split('T')[0],
      apiBaseUrl: 'https://x.erquhealth.com/wp-json/sgtool/v1'
    }
  },
  methods: {
    onBirthdayChange(e) {
      this.birthday = e.detail.value;
    },
    async submitData() {
      this.errorMessage = '';
      
      if (!this.nickname || !this.birthday || !this.currentHeight) {
        this.errorMessage = '请填写必填项（昵称、出生日期、身高）';
        return;
      }

      const height = parseFloat(this.currentHeight);
      if (height < 30 || height > 200) {
        this.errorMessage = '请输入合理的身高数值(30-200cm)';
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
          nickname: this.nickname,
          gender: this.gender,
          birthday: this.birthday,
          bone_age: this.boneAge || null,
          weight: this.weight || null,
          current_height: this.currentHeight
        };

        const response = await new Promise((resolve, reject) => {
          uni.request({
            url: `${this.apiBaseUrl}/save_user_data`,
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
  onLoad() {
    const user = Auth.getUser();

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
      return;
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
      return;
    }
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
  width: 100px;
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