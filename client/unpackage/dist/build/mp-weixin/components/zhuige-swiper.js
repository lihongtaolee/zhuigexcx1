(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/zhuige-swiper"],{"0019":function(t,e,n){"use strict";n.r(e);var i=n("473d"),u=n.n(i);for(var r in i)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return i[t]}))}(r);e["default"]=u.a},"473d":function(t,e,n){"use strict";(function(t){var i=n("47a9");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var u=i(n("435b")),r={name:"zhuige-swiper",props:{type:{type:String,default:""},items:{type:Array,default:[]},previousMargin:{type:String,default:"0rpx"},nextMargin:{type:String,default:"0rpx"}},data:function(){return{}},methods:{clickItem:function(e){if(this.items[e].link)u.default.openLink(this.items[e].link);else{var n=[];this.items.forEach((function(t){n.push(t.image)})),t.previewImage({current:e,urls:n})}}}};e.default=r}).call(this,n("df3c")["default"])},"52d0":function(t,e,n){"use strict";var i=n("d8b6"),u=n.n(i);u.a},"5b99":function(t,e,n){"use strict";n.d(e,"b",(function(){return i})),n.d(e,"c",(function(){return u})),n.d(e,"a",(function(){}));var i=function(){var t=this.$createElement;this._self._c},u=[]},d8b6:function(t,e,n){},fcc3:function(t,e,n){"use strict";n.r(e);var i=n("5b99"),u=n("0019");for(var r in u)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return u[t]}))}(r);n("52d0");var a=n("828b"),f=Object(a["a"])(u["default"],i["b"],i["c"],!1,null,null,null,!1,i["a"],void 0);e["default"]=f.exports}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/zhuige-swiper-create-component',
    {
        'components/zhuige-swiper-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('df3c')['createComponent'](__webpack_require__("fcc3"))
        })
    },
    [['components/zhuige-swiper-create-component']]
]);
