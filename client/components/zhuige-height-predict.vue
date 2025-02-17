<template>
  <view class="height-predict-container">
    <!-- 模块头部 -->
    <view class="module-header">
      <text class="module-title">身高预测AI</text>
      <text class="detail-guide" @click="openDetail">详细预测</text>
    </view>
    <!-- 图表区域（使用原生 canvas） -->
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
  </view>
</template>

<script>
import axios from 'axios'
import * as echarts from 'echarts'

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
      chart: null
    }
  },
  methods: {
    openDetail() {
      // 跳转到详细预测页
      uni.navigateTo({ url: '/pages/sgtool/sgycai/sgycai' });
    },
    // 初始化图表，使用 native canvas 节点
    initChart(canvas, width, height, dpr) {
      const chart = echarts.init(canvas, null, {
        width: width,
        height: height,
        devicePixelRatio: dpr
      });
      // 生成图表配置（示例：三条折线曲线）
      const ages = [5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];
      const geneticBase = Number(this.geneticHeight) - 30;
      const currentBase = Number(this.currentHeight) - 30;
      const targetBase = Number(this.targetHeight) - 30;
      const geneticData = ages.map(age => Number((geneticBase + ((age - 5) / (18 - 5)) * 30).toFixed(1)));
      const currentData = ages.map(age => Number((currentBase + ((age - 5) / (18 - 5)) * 30).toFixed(1)));
      const targetData = ages.map(age => Number((targetBase + ((age - 5) / (18 - 5)) * 30).toFixed(1)));

      const option = {
        color: ['#FF7F50', '#87CEFA', '#32CD32'],
        tooltip: { trigger: 'axis' },
        legend: {
          data: ['遗传身高', '现在实测身高', '期望成年身高'],
          textStyle: { color: '#fff' }
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: '3%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          boundaryGap: false,
          data: ages,
          axisLine: { lineStyle: { color: '#fff' } }
        },
        yAxis: {
          type: 'value',
          axisLine: { lineStyle: { color: '#fff' } }
        },
        series: [
          { name: '遗传身高', type: 'line', data: geneticData },
          { name: '现在实测身高', type: 'line', data: currentData },
          { name: '期望成年身高', type: 'line', data: targetData }
        ]
      };
      chart.setOption(option);
      return chart;
    },
    // 更新图表数据（当 props 变化时调用）
    updateChart() {
      if (this.chart) {
        const ages = [5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];
        const geneticBase = Number(this.geneticHeight) - 30;
        const currentBase = Number(this.currentHeight) - 30;
        const targetBase = Number(this.targetHeight) - 30;
        const geneticData = ages.map(age => Number((geneticBase + ((age - 5) / (18 - 5)) * 30).toFixed(1)));
        const currentData = ages.map(age => Number((currentBase + ((age - 5) / (18 - 5)) * 30).toFixed(1)));
        const targetData = ages.map(age => Number((targetBase + ((age - 5) / (18 - 5)) * 30).toFixed(1)));
  
        const option = {
          color: ['#FF7F50', '#87CEFA', '#32CD32'],
          tooltip: { trigger: 'axis' },
          legend: {
            data: ['遗传身高', '现在实测身高', '期望成年身高'],
            textStyle: { color: '#fff' }
          },
          grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
          },
          xAxis: {
            type: 'category',
            boundaryGap: false,
            data: ages,
            axisLine: { lineStyle: { color: '#fff' } }
          },
          yAxis: {
            type: 'value',
            axisLine: { lineStyle: { color: '#fff' } }
          },
          series: [
            { name: '遗传身高', type: 'line', data: geneticData },
            { name: '现在实测身高', type: 'line', data: currentData },
            { name: '期望成年身高', type: 'line', data: targetData }
          ]
        };
        this.chart.setOption(option);
      }
    },
    // 通过原生 canvas 组件初始化图表
    initNativeCanvas() {
      const query = uni.createSelectorQuery().in(this);
      query.select('#heightChart').node(res => {
        const canvas = res.node;
        const width = res.width;
        const height = res.height;
        const dpr = uni.getSystemInfoSync().pixelRatio;
        this.chart = this.initChart(canvas, width, height, dpr);
      }).exec();
    }
  },
  mounted() {
    // 初始化原生 canvas 图表
    this.initNativeCanvas();
    // 获取后端数据并更新组件数据与图表
    axios.get('/api/sgtool/height')
      .then(response => {
        const data = response.data;
        // 根据性别字段选择遗传身高（默认男孩遗传身高）
        this.currentHeight = data.current_height;
        this.geneticHeight = (data.gender && data.gender == 2) ? data.girl_genetic_height : data.boy_genetic_height;
        this.targetHeight = data.target_height;
        this.probability = data.prediction_probability;
        this.updateChart();
      })
      .catch(error => {
        console.error('获取身高数据失败:', error);
      });
  },
  watch: {
    currentHeight() {
      this.updateChart();
    },
    geneticHeight() {
      this.updateChart();
    },
    targetHeight() {
      this.updateChart();
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
  height: 300rpx;
  margin-bottom: 20rpx;
}
/* 设置 canvas 为 100% 宽高，保证初始化时有尺寸 */
.chart-canvas {
  width: 100%;
  height: 100%;
  display: block;
}

/* 数据展示 */
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
</style>
