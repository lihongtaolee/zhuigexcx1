<?php

/**
 * 追格专题模块管理
 */

if (!defined('ABSPATH')) {
    exit;
}

// 注册专题模块菜单
add_action('admin_menu', 'zhuige_xcx_sgztmk_menu');
function zhuige_xcx_sgztmk_menu()
{
    add_submenu_page(
        'zhuige-xcx',
        '身高专题模块管理',
        '身高专题模块管理',
        'manage_options',
        'zhuige-xcx-sgztmk',
        'zhuige_xcx_sgztmk_page'
    );
}

// 专题模块配置页面
function zhuige_xcx_sgztmk_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    // 保存设置
    if (isset($_POST['zhuige_xcx_sgztmk_save'])) {
        $modules = [];
        foreach ($_POST['modules'] as $module) {
            $modules[] = [
                'id' => sanitize_text_field($module['id']),
                'title' => sanitize_text_field($module['title']),
                'icon' => sanitize_text_field($module['icon']),
                'left_module' => [
                    'title' => sanitize_text_field($module['left_title']),
                    'description' => sanitize_text_field($module['left_desc']),
                    'image' => sanitize_text_field($module['left_image']),
                    'button_text' => sanitize_text_field($module['left_button']),
                    'link' => sanitize_text_field($module['left_link']),
                    'value_api' => sanitize_text_field($module['left_value_api']),
                    'bg_color' => sanitize_text_field($module['left_bg_color'] ?: 'rgba(24, 144, 255, 0.1)')
                ],
                'right_top_module' => [
                    'title' => sanitize_text_field($module['right_top_title']),
                    'description' => sanitize_text_field($module['right_top_desc']),
                    'image' => sanitize_text_field($module['right_top_image']),
                    'button_text' => sanitize_text_field($module['right_top_button']),
                    'link' => sanitize_text_field($module['right_top_link']),
                    'bg_color' => sanitize_text_field($module['right_top_bg_color'] ?: 'rgba(0, 0, 0, 0.1)')
                ],
                'right_bottom_module' => [
                    'title' => sanitize_text_field($module['right_bottom_title']),
                    'description' => sanitize_text_field($module['right_bottom_desc']),
                    'image' => sanitize_text_field($module['right_bottom_image']),
                    'button_text' => sanitize_text_field($module['right_bottom_button']),
                    'link' => sanitize_text_field($module['right_bottom_link']),
                    'bg_color' => sanitize_text_field($module['right_bottom_bg_color'] ?: 'rgba(0, 0, 0, 0.1)')
                ]
            ];
        }

        update_option('zhuige_xcx_sgztmk_modules', $modules);
        echo '<div class="updated"><p><strong>设置已保存</strong></p></div>';
    }

    // 获取当前配置
    $modules = get_option('zhuige_xcx_sgztmk_modules');
    if (!$modules) {
        $modules = [];
    }
?>
    <div class="wrap">
        <h1>身高专题模块管理</h1>
        <form method="post" action="">
            <div id="modules-container">
                <?php foreach ($modules as $index => $module) : ?>
                    <div class="module-item">
                        <h2>身高专题模块 <?php echo $index + 1; ?></h2>
                        <input type="hidden" name="modules[<?php echo $index; ?>][id]" value="<?php echo esc_attr($module['id']); ?>">
                        <table class="form-table">
                            <tr>
                                <th>模块标题</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][title]" value="<?php echo esc_attr($module['title']); ?>" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>模块图标</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][icon]" value="<?php echo esc_attr($module['icon']); ?>" class="regular-text" />
                                </td>
                            </tr>

                            <tr><th colspan="2"><h3>左侧模块配置</h3></th></tr>
                            <tr>
                                <th>标题</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][left_title]" value="<?php echo esc_attr($module['left_module']['title']); ?>" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>数值接口配置</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][left_value_api]" value="<?php echo esc_attr($module['left_module']['value_api']); ?>" class="regular-text" />
                                    <p class="description">配置格式：table:表名,field:字段名<br>示例：table:wp_height_user_data,field:boy_genetic_height<br>说明：获取身高数据表中的男孩遗传身高字段</p>
                                </td>
                            </tr>
                            <tr>
                                <th>描述</th>
                                <td>
                                    <textarea name="modules[<?php echo $index; ?>][left_desc]" class="large-text"><?php echo esc_textarea($module['left_module']['description']); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>图片</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][left_image]" value="<?php echo esc_attr($module['left_module']['image']); ?>" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>按钮文字</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][left_button]" value="<?php echo esc_attr($module['left_module']['button_text']); ?>" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>链接</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][left_link]" value="<?php echo esc_attr($module['left_module']['link']); ?>" class="regular-text" />
                                </td>
                            </tr>

                            <tr><th colspan="2"><h3>右上模块配置</h3></th></tr>
                            <tr>
                                <th>标题</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][right_top_title]" value="<?php echo esc_attr($module['right_top_module']['title']); ?>" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>描述</th>
                                <td>
                                    <textarea name="modules[<?php echo $index; ?>][right_top_desc]" class="large-text"><?php echo esc_textarea($module['right_top_module']['description']); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>图片</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][right_top_image]" value="<?php echo esc_attr($module['right_top_module']['image']); ?>" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>按钮文字</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][right_top_button]" value="<?php echo esc_attr($module['right_top_module']['button_text']); ?>" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>链接</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][right_top_link]" value="<?php echo esc_attr($module['right_top_module']['link']); ?>" class="regular-text" />
                                </td>
                            </tr>

                            <tr><th colspan="2"><h3>右下模块配置</h3></th></tr>
                            <tr>
                                <th>标题</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][right_bottom_title]" value="<?php echo esc_attr($module['right_bottom_module']['title']); ?>" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>描述</th>
                                <td>
                                    <textarea name="modules[<?php echo $index; ?>][right_bottom_desc]" class="large-text"><?php echo esc_textarea($module['right_bottom_module']['description']); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>图片</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][right_bottom_image]" value="<?php echo esc_attr($module['right_bottom_module']['image']); ?>" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>按钮文字</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][right_bottom_button]" value="<?php echo esc_attr($module['right_bottom_module']['button_text']); ?>" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>链接</th>
                                <td>
                                    <input type="text" name="modules[<?php echo $index; ?>][right_bottom_link]" value="<?php echo esc_attr($module['right_bottom_module']['link']); ?>" class="regular-text" />
                                </td>
                            </tr>
                        </table>
                        <button type="button" class="button remove-module">删除此模块</button>
                        <hr>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="button" id="add-module">添加新模块</button>

            <p class="submit">
                <input type="submit" name="zhuige_xcx_sgztmk_save" class="button-primary" value="保存设置" />
            </p>
        </form>

        <script>
        jQuery(document).ready(function($) {
            // 添加新模块
            $('#add-module').click(function() {
                var index = $('.module-item').length;
                var template = `
                    <div class="module-item">
                        <h2>专题模块 ${index + 1}</h2>
                        <input type="hidden" name="modules[${index}][id]" value="module_${Date.now()}">
                        <table class="form-table">
                            <tr>
                                <th>模块标题</th>
                                <td>
                                    <input type="text" name="modules[${index}][title]" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>模块图标</th>
                                <td>
                                    <input type="text" name="modules[${index}][icon]" class="regular-text" />
                                </td>
                            </tr>

                            <tr><th colspan="2"><h3>左侧模块配置</h3></th></tr>
                            <tr>
                                <th>标题</th>
                                <td>
                                    <input type="text" name="modules[${index}][left_title]" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>描述</th>
                                <td>
                                    <textarea name="modules[${index}][left_desc]" class="large-text"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>图片</th>
                                <td>
                                    <input type="text" name="modules[${index}][left_image]" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>按钮文字</th>
                                <td>
                                    <input type="text" name="modules[${index}][left_button]" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>链接</th>
                                <td>
                                    <input type="text" name="modules[${index}][left_link]" class="regular-text" />
                                </td>
                            </tr>

                            <tr><th colspan="2"><h3>右上模块配置</h3></th></tr>
                            <tr>
                                <th>标题</th>
                                <td>
                                    <input type="text" name="modules[${index}][right_top_title]" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>描述</th>
                                <td>
                                    <textarea name="modules[${index}][right_top_desc]" class="large-text"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>图片</th>
                                <td>
                                    <input type="text" name="modules[${index}][right_top_image]" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>按钮文字</th>
                                <td>
                                    <input type="text" name="modules[${index}][right_top_button]" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>链接</th>
                                <td>
                                    <input type="text" name="modules[${index}][right_top_link]" class="regular-text" />
                                </td>
                            </tr>

                            <tr><th colspan="2"><h3>右下模块配置</h3></th></tr>
                            <tr>
                                <th>标题</th>
                                <td>
                                    <input type="text" name="modules[${index}][right_bottom_title]" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th