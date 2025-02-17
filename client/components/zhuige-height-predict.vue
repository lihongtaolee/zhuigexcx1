<template>
  <view class="height-predict-container">
    <view class="demo-tag">演示数据</view>
    <view class="astronaut-display">
      <image class="astronaut" mode="aspectFill" src="/static/sgtoolimages/astronaut.png"></image>
      <view class="height-data">
        <view class="height-item">
          <text class="label">遗传身高</text>
          <text class="value">{{ geneticHeight }} cm</text>
        </view>
        <view class="height-item">
          <text class="label">现在实测身高</text>
          <text class="value">{{ currentHeight }} cm</text>
        </view>
        <view class="height-item">
          <text class="label">期望成年身高</text>
          <text class="value">{{ targetHeight }} cm</text>
        </view>
        <view class="height-item">
          <text class="label">可追高概率</text>
          <text class="value">{{ probability }}%</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
import axios from 'axios';
export default {
  name: 'zhuige-height-predict',
  data() {
    return {
      currentHeight: 165,
      geneticHeight: 175,
      targetHeight: 180,
      probability: 85
    };
  },
  mounted() {
    axios.get('/api/sgtool/height')
      .then(response => {
        const data = response.data;
        this.currentHeight = data.current_height;
        // 根据用户性别决定遗传身高使用男孩或女孩字段，默认为男孩遗传身高
        this.geneticHeight = (data.gender && data.gender == 2) ? data.girl_genetic_height : data.boy_genetic_height;
        this.targetHeight = data.target_height;
        this.probability = data.prediction_probability;
      })
      .catch(error => {
        console.error('获取身高数据失败:', error);
      });
  }
}
</script>

<style>
.height-predict-container {
  background: linear-gradient(to bottom, #1a1a2e, #16213e);
  border-radius: 20rpx;
  padding: 30rpx;
  margin: 20rpx;
  color: #fff;
  position: relative;
}

.demo-tag {
  position: absolute;
  top: 10rpx;
  right: 10rpx;
  background-color: rgba(255, 255, 255, 0.2);
  padding: 4rpx 12rpx;
  border-radius: 8rpx;
  font-size: 20rpx;
  color: #fff;
}

.astronaut-display {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.astronaut {
  width: 200rpx;
  height: 200rpx;
}

.height-data {
  flex: 1;
  margin-left: 30rpx;
}

.height-item {
  margin-bottom: 20rpx;
}

.label {
  font-size: 24rpx;
  color: #8c8c8c;
  margin-right: 20rpx;
}

.value {
  font-size: 32rpx;
  color: #4cd964;
  font-weight: bold;
}

.probability {
  margin-top: 30rpx;
  padding-top: 20rpx;
  border-top: 2rpx solid rgba(255,255,255,0.1);
}

.probability .value {
  color: #007aff;
}
</style>