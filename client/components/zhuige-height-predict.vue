<template>
  <view class="height-predict-container">
    <!-- 模块头部 -->
    <view class="module-header">
      <text class="module-title">身高预测AI</text>
      <text class="detail-guide" @click="openDetail">详细预测</text>
    </view>
    <!-- 图表区域（使用原生 canvas 绘制图表） -->
    <view class="chart-container">
      <canvas id="heightChart" canvas-id="heightChart" class="chart-canvas"></canvas>
    </view>
    <!-- 数据展示区域 -->
    <view class="data-display">
      <view class="data-item">
        <text class="label">遗传身高</text>
        <text class="value">{{ geneticHeight }} cm</text>
      </view>
      <view class="data-item">
        <text class="label">现在实测身高</text>
        <text class="value">{{ currentHeight }} cm</text>
      </view>
      <view class="data-item">
        <text class="label">期望成年身高</text>
        <text class="value">{{ targetHeight }} cm</text>
      </view>
    </view>
    <!-- 可追高概率展示 -->
    <view class="probability">
      <text class="label">可追高概率</text>
      <text class="value">{{ probability }}%</text>
    </view>
    <!-- 地图展示区域 -->
    <view class="astronaut-container">
      <image class="astronaut" src="/static/sgtoolimages/astronaut.png" mode="widthFix"></image>
    </view>
  </view>
</template>

<script>
export default {
  name: 'zhuige-height-predict',
  props: {
    currentHeight: {
      type: Number,
      default: 165
    },
    geneticHeight: {
      type: Number,
      default: 175
    },
    targetHeight: {
      type: Number,
      default: 180
    },
    probability: {
      type: Number,
      default: 85
    }
  },
  data() {
    return {
      // 用于存储图表绘制时的上下文，可选
      chartCtx: null
    }
  },
  methods: {
    openDetail() {
      // 跳转到详细预测页
      uni.navigateTo({ url: '/pages/sgtool/sgycai/sgycai' });
    },
    // 使用原生 canvas API 绘制折线图（移除了设置 canvas.width/height）
    initChart(canvas, drawWidth, drawHeight) {
      // ...删除：canvas.width = width; canvas.height = height;
      
      const ctx = canvas.getContext('2d');
      ctx.clearRect(0, 0, drawWidth, drawHeight);
      // 定义年龄数组
      const ages = [5,6,7,8,9,10,11,12,13,14,15,16,17,18];
      const calcSeries = (baseValue) => {
        const base = baseValue - 30;
        return ages.map(age => base + ((age - 5)/(18-5))*30);
      };
      const geneticData = calcSeries(this.geneticHeight);
      const currentData = calcSeries(this.currentHeight);
      const targetData = calcSeries(this.targetHeight);
      const allData = geneticData.concat(currentData, targetData);
      const minY = Math.min(...allData);
      const maxY = Math.max(...allData);
      const marginLeft = 40, marginRight = 20, marginTop = 20, marginBottom = 40;
      const chartWidth = drawWidth - marginLeft - marginRight;
      const chartHeight = drawHeight - marginTop - marginBottom;
      
      ctx.strokeStyle = '#ffffff';
      ctx.lineWidth = 1;
      // x轴
      ctx.beginPath();
      ctx.moveTo(marginLeft, drawHeight - marginBottom);
      ctx.lineTo(drawWidth - marginRight, drawHeight - marginBottom);
      ctx.stroke();
      // y轴
      ctx.beginPath();
      ctx.moveTo(marginLeft, marginTop);
      ctx.lineTo(marginLeft, drawHeight - marginBottom);
      ctx.stroke();
      
      const mapY = (value) => marginTop + (maxY - value) / (maxY - minY) * chartHeight;
      const pointSpacing = chartWidth / (ages.length - 1);
      const drawSeries = (data, color) => {
        ctx.strokeStyle = color;
        ctx.lineWidth = 2;
        ctx.beginPath();
        data.forEach((val, index) => {
          const x = marginLeft + index * pointSpacing;
          const y = mapY(val);
          index === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
        });
        ctx.stroke();
      };
      drawSeries(geneticData, '#FF7F50');
      drawSeries(currentData, '#87CEFA');
      drawSeries(targetData, '#32CD32');
    },
    // 使用 selectorQuery 获取 canvas，添加 dpr 缩放
    initNativeCanvas() {
      const query = uni.createSelectorQuery().in(this);
      query.select('#heightChart').node(res => {
        const canvas = res.node;
        const dpr = uni.getSystemInfoSync().pixelRatio || 1;
        // 设定内部绘图尺寸根据设备像素比
        const realWidth = res.width * dpr;
        const realHeight = res.height * dpr;
        canvas.width = realWidth;
        canvas.height = realHeight;
        // 缩放绘图上下文
        const ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);
        // 使用原始获取的宽高（非乘 dpr 的值）供绘图使用
        this.initChart(canvas, res.width, res.height);
      }).exec();
    }
  },
  mounted() {
    // 确保 canvas 渲染完成后初始化图表
    this.$nextTick(() => {
      this.initNativeCanvas();
    });
  },
  watch: {
    // 当相关属性变化时，重新绘制图表
    currentHeight() {
      this.$nextTick(() => {
        this.initNativeCanvas();
      });
    },
    geneticHeight() {
      this.$nextTick(() => {
        this.initNativeCanvas();
      });
    },
    targetHeight() {
      this.$nextTick(() => {
        this.initNativeCanvas();
      });
    }
  }
}
</script>

<style scoped>
.height-predict-container {
  background: linear-gradient(to bottom, #1a1a2e, #16213e);
  border-radius: 20rpx;
  padding: 30rpx;
  margin: 20rpx 0;
  color: #fff;
}

/* 模块头部 */
.module-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20rpx;
}
.module-title {
  font-size: 32rpx;
  font-weight: bold;
}
.detail-guide {
  font-size: 28rpx;
  color: #4cd964;
}

/* 图表区域 */
.chart-container {
  width: 100%;
  height: 200rpx;
  margin-bottom: 20rpx;
}
.chart-canvas {
  width: 100%;
  height: 100%;
  display: block;
}

/* 数据展示区域 */
.data-display {
  display: flex;
  justify-content: space-around;
  margin-bottom: 20rpx;
}
.data-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.data-item .label {
  font-size: 28rpx;
  color: #8c8c8c;
  margin-bottom: 10rpx;
}
.data-item .value {
  font-size: 32rpx;
  color: #4cd964;
  font-weight: bold;
}

/* 可追高概率 */
.probability {
  text-align: center;
  padding-top: 20rpx;
  border-top: 2rpx solid rgba(255,255,255,0.1);
}
.probability .label {
  font-size: 28rpx;
  color: #8c8c8c;
  margin-bottom: 10rpx;
}
.probability .value {
  font-size: 36rpx;
  color: #007aff;
  font-weight: bold;
}

/* 地图图片 */
.astronaut-container {
  margin-top: 20rpx;
  text-align: center;
}
.astronaut {
  width: 80%;
  max-width: 300rpx;
}
</style>
