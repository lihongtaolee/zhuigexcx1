<?php
/**
 * 追格商城后台管理页面
 *
 * @link       https://www.zhuige.com
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

?>
<div class="wrap">
    <?php
    // 使用codestar-framework显示配置表单
    csf_get_options('zhuige-shop');
    ?>

    <hr/>

    <div class="zhuige-market">
        <div class="zhuige-market-title">追格资源</div>
        <div class="zhuige-market-content">
            <div class="zhuige-market-loading">加载中...</div>
        </div>
    </div>
</div>