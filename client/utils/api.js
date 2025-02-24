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
const SHOP = {
	// 商品相关
	GOODS_LAST: URL('shop', 'goods_last'),
	GOODS_DETAIL: URL('shop', 'goods_detail'),
	GOODS_CATEGORY: URL('shop', 'goods_category'),
	GOODS_LIST: URL('shop', 'goods_list'),

	// 购物车相关
	CART_ADD: URL('shop', 'cart_add'),
	CART_DEL: URL('shop', 'cart_del'),
	CART_LIST: URL('shop', 'cart_list'),
	CART_CLEAR: URL('shop', 'cart_clear'),

	// 订单相关
	ORDER_CREATE: URL('shop', 'order_create'),
	ORDER_DETAIL: URL('shop', 'order_detail'),
	ORDER_LIST: URL('shop', 'order_list'),
	ORDER_CANCEL: URL('shop', 'order_cancel'),
	ORDER_PAY: URL('shop', 'order_pay'),
	ORDER_CONFIRM: URL('shop', 'order_confirm')
};

export default {
	URL,
	SHOP
};