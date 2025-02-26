<template>
  <view class="shop-home">
    <!-- 轮播图 -->
    <swiper :autoplay="true" interval="5000" indicator-dots>
      <block v-for="(item, index) in banners" :key="index">
        <swiper-item>
          <image :src="item.image" mode="aspectFill"></image>
        </swiper-item>
      </block>
    </swiper>
    <!-- 导航菜单 -->
    <view class="nav-menu">
      <block v-for="(nav, index) in navs" :key="index">
        <navigator :url="nav.url">
          <image :src="nav.icon" mode="aspectFit"></image>
          <text>{{ nav.title }}</text>
        </navigator>
      </block>
    </view>
    <!-- 商品列表 -->
    <view class="product-list">
      <block v-for="(product, index) in products" :key="index">
        <navigator :url="`/pages/shop/detail/detail?id=${product.id}`">
          <image :src="product.image" mode="aspectFill"></image>
          <text>{{ product.name }}</text>
          <text class="price">{{ product.price }}</text>
        </navigator>
      </block>
    </view>
  </view>
</template>

<script>
export default {
  name: "ShopHome",
  data() {
    return {
      banners: [],
      navs: [],
      products: []
    };
  },
  methods: {
    fetchHomeData() {
      uni.request({
        url: "https://x.erquhealth.com/api/shop/home", // 根据实际接口修改
        success: (res) => {
          if (res.data && res.data.code === 200) {
            this.banners = res.data.data.banners;
            this.navs = res.data.data.navs;
            this.products = res.data.data.products;
          }
        }
      });
    }
  },
  onLoad() {
    this.fetchHomeData();
  }
}
</script>

<style scoped>
.shop-home {
  display: flex;
  flex-direction: column;
}
.nav-menu {
  display: flex;
  justify-content: space-around;
  margin: 10px 0;
}
.product-list {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}
.product-list navigator {
  width: 48%;
  margin-bottom: 10px;
}
.price {
  color: red;
}
</style>
