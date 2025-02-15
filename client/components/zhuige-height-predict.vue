<template>
  <view class="height-predict-container">
    <view class="astronaut-display">
      <image class="astronaut" mode="aspectFill" src="/static/astronaut.png"></image>
      <view class="height-data">
        <view class="height-item">
          <text class="label">实测身高</text>
          <text class="value">{{currentHeight}} cm</text>
        </view>
        <view class="height-item">
          <text class="label">遗传身高</text>
          <text class="value">{{geneticHeight}} cm</text>
        </view>
        <view class="height-item">
          <text class="label">可追高</text>
          <text class="value">{{potentialHeight}} cm</text>
        </view>
        <view class="probability">
          <text class="label">达成概率</text>
          <text class="value">{{probability}}%</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
export default {
  name: 'zhuige-height-predict',
  props: {
    currentHeight: {
      type: Number,
      default: 0
    },
    geneticHeight: {
      type: Number,
      default: 0
    },
    potentialHeight: {
      type: Number,
      default: 0
    },
    probability: {
      type: Number,
      default: 0
    }
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