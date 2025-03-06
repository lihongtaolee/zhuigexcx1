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

    // 调试模式开关 - 设置为false关闭所有调试日志
    window.zhuigeShopDebug = false;

    // 调试日志函数
    function logDebug(message) {
        if (typeof console !== 'undefined' && window.zhuigeShopDebug === true) {
            console.log('[追格商城调试] ' + message);
        }
    }

    // 错误日志函数
    function logError(message) {
        if (typeof console !== 'undefined' && window.zhuigeShopDebug === true) {
            console.error('[追格商城错误] ' + message);
        }
    }

    // 初始化媒体上传器
    function initMediaUploader() {
        // 确保在商品编辑页面且媒体上传API可用
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            return;
        }

        logDebug('初始化媒体上传器');

        // 媒体上传器已在PHP中初始化，这里只添加一些额外的功能
        
        // 确保幻灯片添加按钮正常工作
        $('#zhuige-goods-slide-add').on('click', function() {
            logDebug('添加新幻灯片项');
        });
    }

    // 初始化表单验证
    function initFormValidation() {
        // 表单提交前验证
        $('form#post').on('submit', function() {
            logDebug('表单提交，验证商品数据');
            
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
            logDebug('商品编辑页面已加载');
            
            // 初始化媒体上传器
            initMediaUploader();
            
            // 初始化表单验证
            initFormValidation();
        }
    });

})(jQuery);