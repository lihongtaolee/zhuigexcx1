<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 追格专题模块管理
 */
class ZhuiGe_Xcx_Sgztmk {
    private static $instance = null;

    private function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function activate() {
        // 在这里添加激活时需要执行的代码
        // 例如创建数据表等
    }

    public function admin_menu() {
        if (!current_user_can('manage_options')) {
            return;
        }
        add_menu_page(
            '身高专题模块管理',
            '身高专题模块管理',
            'manage_options',
            'zhuige-sgztmk',
            array($this, 'sgztmk_page'),
            'dashicons-chart-line',
            27
        );
    }

    public function sgztmk_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // 保存设置
        if (isset($_POST['zhuige_xcx_sgztmk_save'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'height_sgztmk';
            $modules = [];
            
            // 清空现有数据
            $wpdb->query("TRUNCATE TABLE {$table_name}");
            
            foreach ($_POST['modules'] as $module) {
                // 保存到options表
                $module_data = [
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
                        'bg_color' => sanitize_text_field($module['left_bg_color'] ?: '#F0F8FF')
                    ],
                    'right_top_module' => [
                        'title' => sanitize_text_field($module['right_top_title']),
                        'description' => sanitize_text_field($module['right_top_desc']),
                        'image' => sanitize_text_field($module['right_top_image']),
                        'button_text' => sanitize_text_field($module['right_top_button']),
                        'link' => sanitize_text_field($module['right_top_link']),
                        'bg_color' => sanitize_text_field(isset($module['right_top_bg_color']) ? $module['right_top_bg_color'] : '#F5F5F5')
                    ],
                    'right_bottom_module' => [
                        'title' => sanitize_text_field($module['right_bottom_title']),
                        'description' => sanitize_text_field($module['right_bottom_desc']),
                        'image' => sanitize_text_field($module['right_bottom_image']),
                        'button_text' => sanitize_text_field($module['right_bottom_button']),
                        'link' => sanitize_text_field($module['right_bottom_link']),
                        'bg_color' => sanitize_text_field($module['right_bottom_bg_color'] ?: '#F5F5F5')
                    ]
                ];
                $modules[] = $module_data;
                
                // 保存到数据库表
                $wpdb->insert(
                    $table_name,
                    array(
                        'title' => $module['title'],
                        'icon' => $module['icon'],
                        'left_title' => $module['left_title'],
                        'left_desc' => $module['left_desc'],
                        'left_image' => $module['left_image'],
                        'left_button' => $module['left_button'],
                        'left_link' => $module['left_link'],
                        'left_value_api' => $module['left_value_api'],
                        'left_bg_color' => $module['left_bg_color'] ?: '#F0F8FF',
                        'right_top_title' => $module['right_top_title'],
                        'right_top_desc' => $module['right_top_desc'],
                        'right_top_image' => $module['right_top_image'],
                        'right_top_button' => $module['right_top_button'],
                        'right_top_link' => $module['right_top_link'],
                        'right_top_bg_color' => isset($module['right_top_bg_color']) ? $module['right_top_bg_color'] : '#F5F5F5',
                        'right_bottom_title' => $module['right_bottom_title'],
                        'right_bottom_desc' => $module['right_bottom_desc'],
                        'right_bottom_image' => $module['right_bottom_image'],
                        'right_bottom_button' => $module['right_bottom_button'],
                        'right_bottom_link' => $module['right_bottom_link'],
                        'right_bottom_bg_color' => $module['right_bottom_bg_color'] ?: '#F5F5F5'
                    ),
                    array(
                        '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
                        '%s', '%s', '%s', '%s', '%s', '%s',
                        '%s', '%s', '%s', '%s', '%s', '%s'
                    )
                );
                

            }
            
            echo '<div class="updated"><p><strong>设置已保存</strong></p></div>';

        }

        // 从数据表获取当前配置
        global $wpdb;
        $table_name = $wpdb->prefix . 'height_sgztmk';
        $results = $wpdb->get_results("SELECT * FROM {$table_name}", ARRAY_A);
        $modules = array();
        
        foreach ($results as $row) {
            $module = array(
                'id' => $row['id'],
                'title' => $row['title'],
                'icon' => $row['icon'],
                'left_module' => array(
                    'title' => $row['left_title'],
                    'description' => $row['left_desc'],
                    'image' => $row['left_image'],
                    'button_text' => $row['left_button'],
                    'link' => $row['left_link'],
                    'value_api' => $row['left_value_api'],
                    'bg_color' => $row['left_bg_color']
                ),
                'right_top_module' => array(
                    'title' => $row['right_top_title'],
                    'description' => $row['right_top_desc'],
                    'image' => $row['right_top_image'],
                    'button_text' => $row['right_top_button'],
                    'link' => $row['right_top_link'],
                    'bg_color' => $row['right_top_bg_color']
                ),
                'right_bottom_module' => array(
                    'title' => $row['right_bottom_title'],
                    'description' => $row['right_bottom_desc'],
                    'image' => $row['right_bottom_image'],
                    'button_text' => $row['right_bottom_button'],
                    'link' => $row['right_bottom_link'],
                    'bg_color' => $row['right_bottom_bg_color']
                )
            );
            $modules[] = $module;
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
                                <tr>
                                    <th>背景色</th>
                                    <td>
                                        <input type="text" name="modules[<?php echo $index; ?>][left_bg_color]" value="<?php echo esc_attr($module['left_module']['bg_color'] ?: '#F0F8FF'); ?>" class="regular-text" />
                                        <p class="description">默认值：#F0F8FF</p>
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
                                <tr>
                                    <th>背景色</th>
                                    <td>
                                        <input type="text" name="modules[<?php echo $index; ?>][right_top_bg_color]" value="<?php echo esc_attr($module['right_top_module']['bg_color'] ?: '#F5F5F5'); ?>" class="regular-text" />
                                        <p class="description">默认值：#F5F5F5</p>
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
                                <tr>
                                    <th>背景色</th>
                                    <td>
                                        <input type="text" name="modules[<?php echo $index; ?>][right_bottom_bg_color]" value="<?php echo esc_attr($module['right_bottom_module']['bg_color'] ?: '#F5F5F5'); ?>" class="regular-text" />
                                        <p class="description">默认值：#F5F5F5</p>
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
                                <tr>
                                    <th>数值接口配置</th>
                                    <td>
                                        <input type="text" name="modules[${index}][left_value_api]" class="regular-text" />
                                        <p class="description">配置格式：table:表名,field:字段名<br>示例：table:wp_height_user_data,field:boy_genetic_height<br>说明：获取身高数据表中的男孩遗传身高字段</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th>背景色</th>
                                    <td>
                                        <input type="text" name="modules[${index}][left_bg_color]" value="#F0F8FF" class="regular-text" />
                                        <p class="description">默认值：#F0F8FF</p>
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
                                <tr>
                                    <th>背景色</th>
                                    <td>
                                        <input type="text" name="modules[${index}][right_top_bg_color]" value="#F5F5F5" class="regular-text" />
                                        <p class="description">默认值：#F5F5F5</p>
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
                                    <th>描述</th>
                                    <td>
                                        <textarea name="modules[${index}][right_bottom_desc]" class="large-text"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th>图片</th>
                                    <td>
                                        <input type="text" name="modules[${index}][right_bottom_image]" class="regular-text" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>按钮文字</th>
                                    <td>
                                        <input type="text" name="modules[${index}][right_bottom_button]" class="regular-text" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>链接</th>
                                    <td>
                                        <input type="text" name="modules[${index}][right_bottom_link]" class="regular-text" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>背景色</th>
                                    <td>
                                        <input type="text" name="modules[${index}][right_bottom_bg_color]" value="#F5F5F5" class="regular-text" />
                                        <p class="description">默认值：#F5F5F5</p>
                                    </td>
                                </tr>
                            </table>
                            <button type="button" class="button remove-module">删除此模块</button>
                            <hr>
                        </div>
                    `;
                    $('#modules-container').append(template);
                });

                // 删除模块
                $(document).on('click', '.remove-module', function() {
                    $(this).closest('.module-item').remove();
                });
            });
            </script>
        </div>
        <?php
    }
    public function register_rest_routes() {
        // 注册获取自定义数据的API路由
        register_rest_route('zhuige/sgtool', '/get_custom_data', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_custom_data'),
            'permission_callback' => '__return_true'
        ));

        // 注册获取身高专题模块数据的API路由
        register_rest_route('zhuige/sgtool', '/get_sgztmk_modules', array(
            'methods' => 'POST',
            'callback' => array($this, 'get_sgztmk_modules'),
            'permission_callback' => '__return_true'
        ));
    }

    /**
     * 获取自定义数据接口
     * 
     * @param WP_REST_Request $request
     * @return array
     */
    public function get_custom_data(WP_REST_Request $request) {
        global $wpdb;

        // 获取请求参数
        $table = $request->get_param('table');
        $fields = $request->get_param('fields'); // 格式：field1,field2,field3
        $conditions = $request->get_param('conditions'); // 格式：field1:value1,field2:value2
        $user_id = $request->get_param('user_id');

        // 验证必要参数
        if (empty($table) || empty($fields)) {
            return array(
                'code' => 400,
                'msg' => '缺少必要参数'
            );
        }

        // 安全检查：确保表名是以wp_开头
        if (strpos($table, $wpdb->prefix) !== 0) {
            $table = $wpdb->prefix . $table;
        }

        // 构建查询字段
        $select_fields = '*';
        if ($fields !== '*') {
            $field_array = explode(',', $fields);
            $safe_fields = array_map('sanitize_text_field', $field_array);
            $select_fields = implode(',', $safe_fields);
        }

        // 构建查询条件
        $where = array();
        $where_values = array();

        // 添加用户ID条件（如果提供）
        if ($user_id) {
            $where[] = 'user_id = %d';
            $where_values[] = $user_id;
        }

        // 处理其他查询条件
        if ($conditions) {
            $condition_pairs = explode(',', $conditions);
            foreach ($condition_pairs as $pair) {
                $parts = explode(':', $pair);
                if (count($parts) === 2) {
                    $field = sanitize_text_field($parts[0]);
                    $value = sanitize_text_field($parts[1]);
                    $where[] = "`$field` = %s";
                    $where_values[] = $value;
                }
            }
        }

        // 构建完整的SQL查询
        $sql = "SELECT $select_fields FROM $table";
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        // 执行查询
        $query = !empty($where_values) ? $wpdb->prepare($sql, $where_values) : $sql;
        $result = $wpdb->get_results($query, ARRAY_A);

        if ($wpdb->last_error) {
            return array(
                'code' => 500,
                'msg' => '查询执行失败',
                'error' => $wpdb->last_error
            );
        }

        return array(
            'code' => 200,
            'msg' => '获取成功',
            'data' => $result
        );
    }

    public function get_sgztmk_modules(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'height_sgztmk';
        $results = $wpdb->get_results("SELECT * FROM {$table_name}", ARRAY_A);
        $modules = array();

        if ($results) {
            foreach ($results as $row) {
                $module = array(
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'icon' => $row['icon'],
                    'left_module' => array(
                        'title' => $row['left_title'],
                        'description' => $row['left_desc'],
                        'image' => $row['left_image'],
                        'button_text' => $row['left_button'],
                        'link' => $row['left_link'],
                        'value_api' => $row['left_value_api'],
                        'bg_color' => $row['left_bg_color']
                    ),
                    'right_top_module' => array(
                        'title' => $row['right_top_title'],
                        'description' => $row['right_top_desc'],
                        'image' => $row['right_top_image'],
                        'button_text' => $row['right_top_button'],
                        'link' => $row['right_top_link'],
                        'bg_color' => $row['right_top_bg_color']
                    ),
                    'right_bottom_module' => array(
                        'title' => $row['right_bottom_title'],
                        'description' => $row['right_bottom_desc'],
                        'image' => $row['right_bottom_image'],
                        'button_text' => $row['right_bottom_button'],
                        'link' => $row['right_bottom_link'],
                        'bg_color' => $row['right_bottom_bg_color']
                    )
                );
                $modules[] = $module;
            }
        }

        return array(
            'code' => 0,
            'msg' => '获取成功',
            'data' => array(
                'modules' => $modules
            )
        );
    }
}

ZhuiGe_Xcx_Sgztmk::getInstance();
