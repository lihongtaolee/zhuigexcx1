/**
 * 不熟悉uniapp和WordPress的新朋友，请仔细阅读文档
 * 99%的安装部署问题都可以在文档中找到答案
 * 文档: https://www.zhuige.com/docs/zg.html
 * 
 * 修改成你的域名
 */
const YOUR_DOMIAN = 'x.erquhealth.com';

/**
 * 拼接url
 */
function URL(module, action) {
	return `https://${YOUR_DOMIAN}/wp-json/zhuige/${module}/${action}`;
}

// 商城相关接口
const ZHUIGE_SHOP_SETTING_HOME = URL('shop', 'setting/home');
const ZHUIGE_SHOP_GOODS_LAST = URL('shop', 'goods/last');
const ZHUIGE_SHOP_GOODS_DETAIL = URL('shop', 'goods/detail');
const ZHUIGE_SHOP_GOODS_SEARCH = URL('shop', 'goods/search');
const ZHUIGE_SHOP_CART_ADD = URL('shop', 'cart/add');
const ZHUIGE_SHOP_CART_LIST = URL('shop', 'cart/list');
const ZHUIGE_SHOP_CART_CLEAR = URL('shop', 'cart/clear');
const ZHUIGE_SHOP_ORDER_CREATE = URL('shop', 'order/create');
const ZHUIGE_SHOP_ORDER_LIST = URL('shop', 'order/list');
const ZHUIGE_SHOP_ORDER_DETAIL = URL('shop', 'order/detail');

export default {
	URL,
	ZHUIGE_SHOP_SETTING_HOME,
	ZHUIGE_SHOP_GOODS_LAST,
	ZHUIGE_SHOP_GOODS_DETAIL,
	ZHUIGE_SHOP_GOODS_SEARCH,
	ZHUIGE_SHOP_CART_ADD,
	ZHUIGE_SHOP_CART_LIST,
	ZHUIGE_SHOP_CART_CLEAR,
	ZHUIGE_SHOP_ORDER_CREATE,
	ZHUIGE_SHOP_ORDER_LIST,
	ZHUIGE_SHOP_ORDER_DETAIL
};