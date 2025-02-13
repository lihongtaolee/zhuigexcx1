<template>
    <view>
        <view v-if="currentMode==='wrap'" class="zhuige-icon zhuige-wrap-icon" :style="nonScrollWrapStyle">
            <view v-for="(item, index) in items" :key="index" :style="nonScrollItemStyle">
                <view v-if="item.type && item.type=='clear'" class="view" @click="clickClear()">
                    <image class="image" mode="aspectFill" :src="item.image"></image>
                    <text class="text">{{item.title}}</text>
                </view>
                <view v-else-if="item.type && item.type=='score'" class="view" @click="clickScore()">
                    <image class="image" mode="aspectFill" :src="item.image"></image>
                    <text class="text">{{item.title}}</text>
                </view>
                <template v-else-if="item.type && item.type=='contact'">
                    <button open-type="contact" class="button-view">
                        <image class="image" mode="aspectFill" :src="item.image"></image>
                        <text class="text">{{item.title}}</text>
                    </button>
                    </template>
                <view v-else @click="openLink(item.link)">
                    <uni-badge v-if="item.badage" text="12" size="normal" type="error" absolute="rightTop" />
                    <image class="image" mode="aspectFill" :src="item.image"></image>
                    <text class="text">{{item.title}}</text>
                </view>
            </view>
        </view>

        <view v-else class="zhuige-icon zhuige-scroll-icon">
            <scroll-view class="scroll-view" scroll-x="true">
                <view class="nav-container" :style="{ width: containerWidth }">
                    <view v-for="(item, index) in items" :key="index" class="view">
                        <view v-if="item.type && item.type=='clear'" @click="clickClear()">
                            <image class="image" mode="aspectFill" :src="item.image"></image>
                            <text class="text">{{item.title}}</text>
                        </view>
                        <view v-else-if="item.type && item.type=='score'" class="view" @click="clickScore()">
                            <image class="image" mode="aspectFill" :src="item.image"></image>
                            <text class="text">{{item.title}}</text>
                        </view>
                        <template v-else-if="item.type && item.type=='contact'">
                            <button open-type="contact">
                                <image class="image" mode="aspectFill" :src="item.image"></image>
                                <text class="text">{{item.title}}</text>
                            </button>
                            </template>
                        <view v-else @click="openLink(item.link)">
                            <uni-badge v-if="item.badage" text="12" size="normal" type="error" absolute="rightTop" />
                            <image class="image" mode="aspectFill" :src="item.image"></image>
                            <text class="text">{{item.title}}</text>
                        </view>
                    </view>
                </view>
            </scroll-view>
        </view>
    </view>
</template>

<script>
import Util from '@/utils/util';
export default {
    name: "zhuige-icons",
    props: {
        // type 属性保留但本例中将根据 items 数量自动判断展示模式
        type: {
            type: String,
            default: "wrap"
        },
        items: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            currentMode: "wrap", // "wrap" 或 "scroll"
            containerWidth: "100%",
            nonScrollItemStyle: {},
            nonScrollWrapStyle: {}
        };
    },
    mounted() {
        this.adjustLayout();
    },
    watch: {
        items() {
            this.adjustLayout();
        }
    },
    methods: {
        adjustLayout() {
            const count = this.items.length;
            if (count > 10) {
                // 超过10个图标，采用横向滚动模式
                this.currentMode = "scroll";
                const pageCount = Math.ceil(count / 10);
                // 假设每页设计宽度为750rpx
                this.containerWidth = (pageCount * 750) + "rpx";
            } else {
                // 图标数不超过10，采用自动换行模式
                this.currentMode = "wrap";
                if (count <= 5 && count > 0) {
                    // 单排展示，每个图标宽度 = 100%/count
                    const percent = 100 / count;
                    this.nonScrollItemStyle = { width: percent + "%" };
                    // 设置为单排时，无换行间距
                    this.nonScrollWrapStyle = { display: "flex", flexWrap: "nowrap", padding: 0, margin: 0 };
                } else if (count > 5 && count <= 10) {
                    // 两排展示，每行5个，每个图标宽度固定20%
                    this.nonScrollItemStyle = { width: "20%" };
                    // 自动换行模式时，去除左右内边距以便与页面其他模块对齐
                    this.nonScrollWrapStyle = { display: "flex", flexWrap: "wrap", padding: 0, margin: 0 };
                }
            }
        },
        openLink(link) {
            Util.openLink(link);
        },
        // 清理缓存
        clickClear() {
            uni.showModal({
                title: '提示',
                content: '清除缓存 需要重新登录',
                success: res => {
                    if (res.confirm) {
                        uni.clearStorageSync();
                        uni.showToast({
                            title: '清除完毕'
                        });
                        uni.reLaunch({
                            url: '/pages/tabs/index/index'
                        });
                    }
                }
            });
        },
        // 打分评价
        clickScore() {
            var plugin = requirePlugin("wxacommentplugin");
            plugin.openComment({
                success: res => {
                    let lastTime = wx.getStorageSync('zhuige_comment_plugin_last_time');
                    if (!lastTime) {
                        lastTime = 0;
                    }
                    let now = new Date().getTime();
                    let legal = ((now - lastTime) > 30 * 86400000);
                    if (legal) {
                        wx.setStorageSync('zhuige_comment_plugin_last_time', now);
                    }
                    uni.showToast({
                        icon: 'none',
                        title: (legal ? '感谢评价' : '您近期已评价过')
                    });
                },
                fail: res => {
                    if (res.errCode != -3) {
                        uni.showToast({
                            icon: 'none',
                            title: '评价功能暂不可用'
                        });
                    }
                }
            });
        }
    }
};
</script>

<style>
/* =========== 自定义图标模块 =========== */
.zhuige-icon .view {
    /* 去除原有padding-bottom，改用margin-bottom控制行间距 */
    text-align: center;
}
.zhuige-wrap-icon .view {
    margin-bottom: 0;  /* 恢复正常间距 */
}
.zhuige-icon .button-view .image {
    padding-bottom: 6rpx;  /* 减小图标和文字之间的间距 */
}
.zhuige-icon .button-view::after {
    border: none;
}
.zhuige-icon {
    display: flex;
    flex-wrap: wrap;
    margin: 20rpx 0 16rpx;
    background-color: #ffffff;
}

.zhuige-icon > view {
    width: 20%;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 8rpx 0;
}

/*  更具体的选择器，提高优先级，覆盖 icons.wxss 中的样式  */
.zhuige-icon.zhuige-wrap-icon .view .image.icons--image,
.zhuige-icon.zhuige-scroll-icon .scroll-view .view .image.icons--image {
    margin-bottom: 6rpx !important;
}

/*  您之前定义的 .zhuige-icon .image 样式，可以保留，作为默认样式，在更高优先级样式不生效时起作用 */
.zhuige-icon.zhuige-wrap-icon .view .image,
.zhuige-icon.zhuige-scroll-icon .scroll-view .view .image {
    height: 80rpx;
    width: 80rpx;
    border-radius: 16rpx;
    margin-bottom: 6rpx !important;
}

.zhuige-icon .text.icons--text {
    margin-top: 8rpx !important;
    display: block;
    line-height: 1.4em;
    font-size: 24rpx;
    color: #333333;
    font-weight: 400;
}

.zhuige-scroll-icon {
    white-space: nowrap;
    padding: 0;
    margin: 0;
}

.zhuige-scroll-icon .scroll-view .view {
    display: inline-block;
    text-align: center;
}

.nav-container {
    display: flex;
    flex-wrap: nowrap;
}

.zhuige-wrap-icon {
    display: flex;
    flex-wrap: wrap;
    padding: 0;
}

.zhuige-wrap-icon > view {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.button-view {
    background: transparent;
    padding: 0;
    line-height: normal;
    border: none;
    display: flex;
    flex-direction: column;
    align-items: center;
}
</style>