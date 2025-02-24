jQuery(document).ready(function($) {
    $('.zhuige-shop-admin-button').on('click', function() {
        // 处理表单提交
        var form = $(this).closest('form');
        var data = form.serialize();
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    alert('保存成功！');
                } else {
                    alert('保存失败：' + response.message);
                }
            },
            error: function() {
                alert('网络错误，请稍后重试！');
            }
        });
    });
});