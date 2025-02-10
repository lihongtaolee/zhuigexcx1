(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/zhuige-tab"],{"0934":function(n,t,e){},"309e":function(n,t,e){"use strict";e.r(t);var u=e("f0aa"),i=e.n(u);for(var a in u)["default"].indexOf(a)<0&&function(n){e.d(t,n,(function(){return u[n]}))}(a);t["default"]=i.a},4804:function(n,t,e){"use strict";var u=e("0934"),i=e.n(u);i.a},"9fa8":function(n,t,e){"use strict";e.r(t);var u=e("edbc"),i=e("309e");for(var a in i)["default"].indexOf(a)<0&&function(n){e.d(t,n,(function(){return i[n]}))}(a);e("4804");var c=e("828b"),o=Object(c["a"])(i["default"],u["b"],u["c"],!1,null,null,null,!1,u["a"],void 0);t["default"]=o.exports},edbc:function(n,t,e){"use strict";e.d(t,"b",(function(){return i})),e.d(t,"c",(function(){return a})),e.d(t,"a",(function(){return u}));var u={uniIcons:function(){return Promise.all([e.e("common/vendor"),e.e("uni_modules/uni-icons/components/uni-icons/uni-icons")]).then(e.bind(null,"d68c"))}},i=function(){var n=this.$createElement;this._self._c},a=[]},f0aa:function(n,t,e){"use strict";var u=e("47a9");Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;u(e("435b"));var i={name:"zhuige-tab",props:{type:{type:String,default:"simple"},tabs:{type:Array,default:[]},curTab:{type:String,default:""},opt:{type:Boolean,default:!1}},data:function(){return{}},methods:{clickTab:function(n){this.$emit("clickTab",n)},clickTabOpt:function(){this.$emit("clickTabOpt",{})}}};t.default=i}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/zhuige-tab-create-component',
    {
        'components/zhuige-tab-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('df3c')['createComponent'](__webpack_require__("9fa8"))
        })
    },
    [['components/zhuige-tab-create-component']]
]);
