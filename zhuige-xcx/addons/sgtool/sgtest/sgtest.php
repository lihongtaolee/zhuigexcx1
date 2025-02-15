<?php
// 在文件顶部添加 CORS 头部设置（根据实际需求配置）
add_action('rest_api_init', function() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
});

/**
 * Plugin Name:     sgtool
 * Plugin URI:      https://erquhealth.com/
 * Description:     身高预测工具
 * Version:         1.0.0
 * Author:          二区健康
 * Author URI:      https://erquhealth.com/
 * License:         GPLv2 or later
 * License URI:     http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain:     sgtool
 */

if (!defined('WPINC')) {
    die;
}

// 注册 REST API 接口
add_action('rest_api_init', function () {
    register_rest_route('zhuige-xcx/v1', '/save-height', array(
        'methods' => 'POST',
        'callback' => 'save_height_data',
        'permission_callback' => '__return_true'
    ));
});

function save_height_data(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'height_predictions';

    // 检查表是否存在，如不存在则创建（建议放在插件激活钩子中）
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    if (!$table_exists) {
        create_height_predictions_table();
    }

    $data = json_decode($request->get_body(), true);

    // 从前端获取数据
    $father_height = floatval($data['fatherHeight']);
    $mother_height = floatval($data['motherHeight']);

    // 输入验证
    if (!$father_height || !$mother_height) {
        return array(
            'code' => 400,
            'msg' => '请输入父母身高'
        );
    }
    if ($father_height < 140 || $father_height > 220 || $mother_height < 140 || $mother_height > 220) {
        return array(
            'code' => 400,
            'msg' => '请输入合理的身高数值(140-220cm)'
        );
    }

    // 计算预测值
    $boy_height = ($father_height + $mother_height + 13) / 2;
    $girl_height = ($father_height + $mother_height - 13) / 2;

    // 获取用户信息，前端传递字段为 user_id
    $user_id = isset($data['user_id']) ? sanitize_text_field($data['user_id']) : '';

    $insert_data = array(
        'user_id' => $user_id,
        'father_height' => $father_height,
        'mother_height' => $mother_height,
        'boy_height' => $boy_height,
        'girl_height' => $girl_height,
        'created_at' => current_time('mysql')
    );

    $result = $wpdb->insert(
        $table_name,
        $insert_data,
        array('%s', '%f', '%f', '%f', '%f', '%s')
    );

    if ($result === false) {
        return array(
            'code' => 500,
            'msg' => '数据保存失败'
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
    return $response_data;
}

function create_height_predictions_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'height_predictions';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id varchar(100) DEFAULT '',
        father_height float NOT NULL,
        mother_height float NOT NULL,
        boy_height float NOT NULL,
        girl_height float NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// 后台管理菜单
add_action('admin_menu', 'zhuige_xcx_menu');
function zhuige_xcx_menu() {
    add_menu_page(
        '身高预测数据',
        '遗传身高预测',
        'manage_options',
        'zhuige-height-data',
        'zhuige_xcx_height_data_page',
        'dashicons-chart-line',
        25
    );
}

function zhuige_xcx_height_data_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('您没有足够的权限访问此页面。'));
    }
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'height_predictions';
        $wpdb->delete($table_name, array('id' => $_GET['id']), array('%d'));
    }
    echo '<div class="wrap">';
    echo '<h1>遗传身高预测数据管理</h1>';

    global $wpdb;
    $table_name = $wpdb->prefix . 'height_predictions';
    $user_table_name = $wpdb->prefix . 'users';
    $usermeta_table_name = $wpdb->prefix . 'usermeta';

    // 分页设置
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;

    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

    // 获取数据
    $sql = $wpdb->prepare("
        SELECT
            hp.*,
            u.user_login,
            u.user_email,
            um_nickname.meta_value AS wp_nickname,
            um_mobile.meta_value AS user_phone_number
        FROM {$table_name} AS hp
        LEFT JOIN {$user_table_name} AS u ON hp.user_id = u.ID
        LEFT JOIN {$usermeta_table_name} AS um_nickname ON u.ID = um_nickname.user_id AND um_nickname.meta_key = 'nickname'
        LEFT JOIN {$usermeta_table_name} AS um_mobile ON u.ID = um_mobile.user_id AND um_mobile.meta_key = 'zhuige_xcx_user_mobile'
        ORDER BY hp.created_at DESC
        LIMIT %d OFFSET %d
    ", $per_page, $offset);

    $results = $wpdb->get_results($sql);

    // 显示数据表格
    echo '<table class="wp-list-table widefat fixed striped table-view-list posts">';
    echo '<thead>
        <tr>
            <th width="5%">ID</th>
            <th width="10%">用户ID</th>
            <th width="15%">用户昵称</th>
            <th width="15%">手机号</th>
            <th width="10%">父亲身高(cm)</th>
            <th width="10%">母亲身高(cm)</th>
            <th width="15%">男孩预测身高(cm)</th>
            <th width="15%">女孩预测身高(cm)</th>
            <th width="10%">预测时间</th>
            <th width="5%">操作</th>
        </tr>
      </thead>';
    echo '<tbody>';
    if ($results) {
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->id) . '</td>';
            echo '<td>' . esc_html($row->user_id) . '</td>';
            echo '<td>' . esc_html($row->wp_nickname) . '</td>';
            echo '<td>' . esc_html($row->user_phone_number) . '</td>';
            echo '<td>' . number_format($row->father_height, 1) . '</td>';
            echo '<td>' . number_format($row->mother_height, 1) . '</td>';
            echo '<td>' . number_format($row->boy_height, 1) . '</td>';
            echo '<td>' . number_format($row->girl_height, 1) . '</td>';
            echo '<td>' . date('Y-m-d H:i', strtotime($row->created_at)) . '</td>';
            echo '<td><a href="?page=zhuige-height-data&action=delete&id=' . $row->id . '" onclick="return confirm(\'确定要删除这条记录吗？\')" class="button button-small">删除</a></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="10">暂无数据</td></tr>';
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
