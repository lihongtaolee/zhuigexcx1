<?php

// 在文件顶部添加以下代码
add_action('rest_api_init', function() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
});

/**
 * Plugin Name:     sgtool
 * Version:         1.0.0
 * Author:          二区健康
 * Author URI:      https://erquhealth.com/
 */

if (!defined('WPINC')) {
    die;
}


function activate_jiangqie_api()
{
    require_once JIANG_QIE_API_BASE_DIR . 'includes/class-jiangqie-api-activator.php';
    JiangQie_API_Activator::activate();
    // 创建身高预测表
    create_height_predictions_table();
}

// REST API 接口
add_action('rest_api_init', function () {
    register_rest_route('jiangqie-api/v1', '/save-height', array(
        'methods' => 'POST',
        'callback' => 'save_height_data',
        'permission_callback' => '__return_true'
    ));
});

function save_height_data(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'height_predictions';
    
    // 检查表是否存在，如果不存在则创建
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    if (!$table_exists) {
        error_log('Height Prediction - Table does not exist, creating...');
        create_height_predictions_table();
    }
    
    // 检查表结构
    $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'user_nickname'");
    if (empty($column_exists)) {
        error_log('Height Prediction - Adding user_nickname column...');
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN user_nickname varchar(100) DEFAULT ''");
    }

    $data = json_decode($request->get_body(), true);
    error_log('Height Prediction - Received data: ' . print_r($data, true));
    
    // 从前端获取数据
    $father_height = floatval($data['fatherHeight']);
    $mother_height = floatval($data['motherHeight']);
    
    // 验证输入
    if (!$father_height || !$mother_height) {
        return array(
            'code' => 400,
            'msg' => '请输入父母身高'
        );
    }
    
    // 验证数值合理性
    if ($father_height < 140 || $father_height > 220 || 
        $mother_height < 140 || $mother_height > 220) {
        return array(
            'code' => 400,
            'msg' => '请输入合理的身高数值(140-220cm)'
        );
    }

    // 计算男女预测身高
    $boy_height = ($father_height + $mother_height + 13) / 2;
    $girl_height = ($father_height + $mother_height - 13) / 2;

    // 获取用户信息
    $user_id = isset($data['userId']) ? sanitize_text_field($data['userId']) : '';
    $user_nickname = isset($data['userNickname']) ? sanitize_text_field($data['userNickname']) : '';

    $insert_data = array(
        'user_id' => $user_id,
        'user_nickname' => $user_nickname,
        'father_height' => $father_height,
        'mother_height' => $mother_height,
        'boy_height' => $boy_height,
        'girl_height' => $girl_height,
        'created_at' => current_time('mysql')
    );
    
    error_log('Height Prediction - Inserting data: ' . print_r($insert_data, true));
    
    $result = $wpdb->insert(
        $table_name,
        $insert_data,
        array('%s', '%s', '%f', '%f', '%f', '%f', '%s')
    );

    if ($result === false) {
        error_log('Height Prediction - Database Error: ' . $wpdb->last_error);
        return array(
            'code' => 500,
            'msg' => '数据保存失败',
            'debug' => $wpdb->last_error
        );
    }

    $response_data = array(
        'code' => 200,
        'msg' => '保存成功',
        'data' => array(
            'boyHeight' => number_format($boy_height, 1),
            'girlHeight' => number_format($girl_height, 1),
            'predictedHeight' => true
        )
    );
    
    error_log('Height Prediction - Success response: ' . print_r($response_data, true));
    return $response_data;
}

// 创建数据库表
function create_height_predictions_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'height_predictions';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id varchar(100) DEFAULT '',
        user_nickname varchar(100) DEFAULT '',
        father_height float NOT NULL,
        mother_height float NOT NULL,
        boy_height float NOT NULL,
        girl_height float NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // 检查表是否创建成功
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    if (!$table_exists) {
        error_log('Height Prediction - Failed to create table');
    } else {
        error_log('Height Prediction - Table created successfully');
    }
}

// 后台管理菜单
add_action('admin_menu', 'jiangqie_api_menu');

function jiangqie_api_menu() {
    add_menu_page(
        '身高预测数据',
        '身高预测',
        'manage_options',
        'jiangqie-height-data',
        'jiangqie_height_data_page',
        'dashicons-chart-line',
        25
    );
}

// 修改后台显示部分的代码
function jiangqie_height_data_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('您没有足够的权限访问此页面。'));
    }

    // 处理删除操作
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'height_predictions';
        $wpdb->delete($table_name, array('id' => $_GET['id']), array('%d'));
    }

    echo '<div class="wrap">';
    echo '<h1>身高预测数据管理</h1>';

    global $wpdb;
    $table_name = $wpdb->prefix . 'height_predictions';
    
    // 分页设置
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    // 获取总记录数
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    
    // 获取数据
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));

    // 显示数据表格
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th width="5%">ID</th>
                <th width="15%">用户ID</th>
                <th width="15%">用户昵称</th>
                <th width="10%">父亲身高(cm)</th>
                <th width="10%">母亲身高(cm)</th>
                <th width="15%">男孩预测身高(cm)</th>
                <th width="15%">女孩预测身高(cm)</th>
                <th width="10%">预测时间</th>
                <th width="5%">操作</th>
            </tr>
          </thead>';
    echo '<tbody>';

    foreach ($results as $row) {
        // 确保数值为浮点数
        $father_height = is_numeric($row->father_height) ? floatval($row->father_height) : 0;
        $mother_height = is_numeric($row->mother_height) ? floatval($row->mother_height) : 0;
        $boy_height = is_numeric($row->boy_height) ? floatval($row->boy_height) : 0;
        $girl_height = is_numeric($row->girl_height) ? floatval($row->girl_height) : 0;

        echo '<tr>';
        echo '<td>' . esc_html($row->id) . '</td>';
        echo '<td>' . esc_html($row->user_id) . '</td>';
        echo '<td>' . esc_html($row->user_nickname) . '</td>';
        echo '<td>' . number_format($father_height, 1) . '</td>';
        echo '<td>' . number_format($mother_height, 1) . '</td>';
        echo '<td>' . number_format($boy_height, 1) . '</td>';
        echo '<td>' . number_format($girl_height, 1) . '</td>';
        echo '<td>' . date('Y-m-d H:i', strtotime($row->created_at)) . '</td>';
        echo '<td><a href="?page=jiangqie-height-data&action=delete&id=' . $row->id . '" 
                onclick="return confirm(\'确定要删除这条记录吗？\')" 
                class="button button-small">删除</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';

    // 分页导航
    $total_pages = ceil($total_items / $per_page);
    if ($total_pages > 1) {
        echo '<div class="tablenav bottom">';
        echo '<div class="tablenav-pages">';
        echo paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total' => $total_pages,
            'current' => $current_page
        ));
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
}

?>