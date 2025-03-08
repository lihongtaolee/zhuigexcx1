/**
 * 追格小程序 - 商城模块
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

(function($) {
    'use strict';

    // 完全禁用所有日志输出
    function logDebug(message) {
        // 不执行任何操作，完全禁用日志
    }

    function logError(message) {
        // 不执行任何操作，完全禁用错误日志
    }

    // 初始化媒体上传器
    function initMediaUploader() {
        // 确保在商品编辑页面且媒体上传API可用
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            return;
        }

        // 确保幻灯片添加按钮正常工作
        $('#zhuige-goods-slide-add').on('click', function() {
            // 添加幻灯片功能已在PHP中实现
        });
    }

    // 初始化表单验证
    function initFormValidation() {
        // 表单提交前验证
        $('form#post').on('submit', function() {
            // 价格验证
            var origPrice = parseFloat($('#zhuige_goods_orig_price').val());
            var price = parseFloat($('#zhuige_goods_price').val());
            
            if (isNaN(origPrice) || origPrice <= 0) {
                $('#zhuige_goods_orig_price').val('1');
            }
            
            if (isNaN(price) || price <= 0) {
                $('#zhuige_goods_price').val('1');
            }
            
            // 库存验证
            var stock = parseInt($('#zhuige_goods_stock').val());
            if (isNaN(stock) || stock < 0) {
                $('#zhuige_goods_stock').val('0');
            }
            
            return true;
        });
    }

    // 文档就绪后执行
    $(document).ready(function() {
        // 检查是否在商品编辑页面
        var postType = $('#post_type').val();
        
        if (postType === 'jq_goods' || (typeof window.typenow !== 'undefined' && window.typenow === 'jq_goods')) {
            // 初始化媒体上传器
            initMediaUploader();
            
            // 初始化表单验证
            initFormValidation();
        }
    });

    // 清除任何可能存在的全局变量或定时器
    if (window.zhuigeShopMetaboxTimer) {
        clearTimeout(window.zhuigeShopMetaboxTimer);
        window.zhuigeShopMetaboxTimer = null;
    }
    
    // 禁用任何可能的循环检查函数
    window.showMetabox = function() { /* 空函数，防止循环调用 */ };
    window.checkMetabox = function() { /* 空函数，防止循环调用 */ };
    window.zhuigeShopDebug = false;

})(jQuery);