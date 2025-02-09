(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/zhuige-nodata"],{"36eb":function(n,t,e){},"58f8":function(n,t,e){"use strict";var u=e("36eb"),a=e.n(u);a.a},"6db1":function(n,t,e){"use strict";e.d(t,"b",(function(){return u})),e.d(t,"c",(function(){return a})),e.d(t,"a",(function(){}));var u=function(){var n=this.$createElement;this._self._c},a=[]},"6db4":function(n,t,e){"use strict";e.r(t);var u=e("d5d9"),a=e.n(u);for(var i in u)["default"].indexOf(i)<0&&function(n){e.d(t,n,(function(){return u[n]}))}(i);t["default"]=a.a},cb68:function(n,t,e){"use strict";e.r(t);var u=e("6db1"),a=e("6db4");for(var i in a)["default"].indexOf(i)<0&&function(n){e.d(t,n,(function(){return a[n]}))}(i);e("58f8");var o=e("828b"),f=Object(o["a"])(a["default"],u["b"],u["c"],!1,null,null,null,!1,u["a"],void 0);t["default"]=f.exports},d5d9:function(n,t,e){"use strict";var u=e("47a9");Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var a=u(e("92bf")),i=u(e("827e")),o={name:"zhuige-nodata",props:{tip:{type:String,default:"哇哦，什么也没有~"},buttons:{type:Boolean,default:!1}},data:function(){return{isLogin:!1}},created:function(){this.isLogin=!!i.default.getUser()},methods:{openLink:function(n){a.default.openLink(n)}}};t.default=o}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/zhuige-nodata-create-component',
    {
        'components/zhuige-nodata-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('df3c')['createComponent'](__webpack_require__("cb68"))
        })
    },
    [['components/zhuige-nodata-create-component']]
]);
