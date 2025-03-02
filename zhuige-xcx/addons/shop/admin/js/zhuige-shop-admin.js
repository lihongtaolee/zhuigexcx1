/**
 * 追格商城小程序
 * Author: 追格
 * Help document: https://www.zhuige.com/product/sc.html
 * github: https://github.com/zhuige-com/zhuige_shop
 * gitee: https://gitee.com/zhuige_com/zhuige_shop
 * License：GPL-2.0
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

(function ($) {
    'use strict';

    $(document).ready(function () {
        // 商品规格管理
        $('.add-spec-btn').on('click', function () {
            var specHtml = '<div class="goods-spec-item">'
                + '<input type="text" class="spec-name" placeholder="规格名称">'
                + '<input type="text" class="spec-price" placeholder="价格">'
                + '<input type="text" class="spec-stock" placeholder="库存">'
                + '<span class="remove-spec">删除</span>'
                + '</div>';
            $('.goods-specs').append(specHtml);
        });

        $(document).on('click', '.remove-spec', function () {
            $(this).parent('.goods-spec-item').remove();
        });

        // 商品图片管理
        $('.remove-image').on('click', function () {
            $(this).parent('.goods-image').remove();
        });

        // 订单状态切换
        $('.order-status-switch').on('change', function () {
            var orderId = $(this).data('order-id');
            var status = $(this).val();
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'zhuige_shop_update_order_status',
                    order_id: orderId,
                    status: status
                },
                success: function (response) {
                    if (response.success) {
                        alert('订单状态更新成功');
                    } else {
                        alert('订单状态更新失败');
                    }
                }
            });
        });

        // 评论管理
        $('.comment-approve').on('click', function () {
            var commentId = $(this).data('comment-id');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'zhuige_shop_approve_comment',
                    comment_id: commentId
                },
                success: function (response) {
                    if (response.success) {
                        alert('评论审核成功');
                        location.reload();
                    } else {
                        alert('评论审核失败');
                    }
                }
            });
        });

        $('.comment-delete').on('click', function () {
            if (!confirm('确定要删除这条评论吗？')) {
                return;
            }
            var commentId = $(this).data('comment-id');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'zhuige_shop_delete_comment',
                    comment_id: commentId
                },
                success: function (response) {
                    if (response.success) {
                        alert('评论删除成功');
                        location.reload();
                    } else {
                        alert('评论删除失败');
                    }
                }
            });
        });

        // 商品表单验证
        $('#zhuige-shop-goods-form').on('submit', function (e) {
            var title = $('#goods-title').val();
            var price = $('#goods-price').val();
            var stock = $('#goods-stock').val();

            if (!title) {
                alert('请输入商品标题');
                e.preventDefault();
                return false;
            }

            if (!price || isNaN(price)) {
                alert('请输入正确的商品价格');
                e.preventDefault();
                return false;
            }

            if (!stock || isNaN(stock)) {
                alert('请输入正确的商品库存');
                e.preventDefault();
                return false;
            }

            return true;
        });
    });

})(jQuery);