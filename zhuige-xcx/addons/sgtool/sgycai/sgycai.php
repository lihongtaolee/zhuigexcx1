<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 身高预测AI管理菜单
 */
class ZhuiGe_Xcx_Sgycai {
    private static $instance = null;

    private function __construct() {
        // 添加菜单
        add_action('admin_menu', array($this, 'admin_menu'));
        // 注册REST API路由
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function admin_menu() {
        // 身高预测AI主菜单
        add_menu_page(
            '身高预测AI',
            '身高预测AI',
            'manage_options',
            'zhuige-xcx-sgycai',
            array($this, 'menu_page'),
            'dashicons-chart-line',
            26
        );

        // 宝宝身高数据子菜单
        add_submenu_page(
            'zhuige-xcx-sgycai',
            '宝宝身高数据',
            '宝宝身高数据',
            'manage_options',
            'zhuige-user-height-data',
            array($this, 'user_height_data_page')
        );
    }

    public function menu_page() {
        $action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : '';
        if ($action == 'edit') {
            include ZHUIGE_XCX_ADDONS_DIR . 'sgtool/sgycai/pages/sgycai-edit.php';
        } else {
            include ZHUIGE_XCX_ADDONS_DIR . 'sgtool/sgycai/pages/sgycai-list.php';
        }
    }

    public function register_rest_routes() {
        register_rest_route('sgtool/v1', '/save_user_data', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_user_data'),
            'permission_callback' => '__return_true'
        ));
    }

    public function save_user_data(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'height_user_data';

        $data = json_decode($request->get_body(), true);

        // 验证必填字段
        if (empty($data['nickname']) || empty($data['gender']) || empty($data['birthday']) || empty($data['current_height'])) {
            return array(
                'code' => 400,
                'msg' => '请填写必填字段'
            );
        }

        // 获取当前用户ID
        $user_id = get_current_user_id();
        if (!$user_id) {
            return array(
                'code' => 401,
                'msg' => '请先登录'
            );
        }

        $insert_data = array(
            'user_id' => $user_id,
            'nickname' => sanitize_text_field($data['nickname']),
            'gender' => intval($data['gender']),
            'birthday' => sanitize_text_field($data['birthday']),
            'bone_age' => !empty($data['bone_age']) ? floatval($data['bone_age']) : null,
            'weight' => !empty($data['weight']) ? floatval($data['weight']) : null,
            'current_height' => floatval($data['current_height']),
            'measure_time' => current_time('mysql')
        );

        $result = $wpdb->insert($table_name, $insert_data);

        if ($result === false) {
            return array(
                'code' => 500,
                'msg' => '保存失败'
            );
        }

        return array(
            'code' => 0,
            'msg' => '保存成功'
        );
    }

    public function user_height_data_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('您没有足够的权限访问此页面。'));
        }

        echo '<div class="wrap">';
        echo '<h1>宝宝身高数据管理</h1>';

        global $wpdb;
        $table_name = $wpdb->prefix . 'height_user_data';
        $user_table_name = $wpdb->prefix . 'users';

        // 分页设置
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;

        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

        // 获取数据
        $sql = $wpdb->prepare("
            SELECT
                hud.*,
                u.user_login,
                u.user_email
            FROM {$table_name} AS hud
            LEFT JOIN {$user_table_name} AS u ON hud.user_id = u.ID
            ORDER BY hud.measure_time DESC
            LIMIT %d OFFSET %d
        ", $per_page, $offset);

        $results = $wpdb->get_results($sql);

        // 显示数据表格
        echo '<table class="wp-list-table widefat fixed striped table-view-list posts">';
        echo '<thead>
            <tr>
                <th>ID</th>
                <th>用户名</th>
                <th>宝宝昵称</th>
                <th>性别</th>
                <th>出生日期</th>
                <th>骨龄(岁)</th>
                <th>体重(kg)</th>
                <th>实测身高(cm)</th>
                <th>测量时间</th>
            </tr>
          </thead>';
        echo '<tbody>';

        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->id) . '</td>';
            echo '<td>' . esc_html($row->user_login) . '</td>';
            echo '<td>' . esc_html($row->nickname) . '</td>';
            echo '<td>' . ($row->gender == 1 ? '男' : '女') . '</td>';
            echo '<td>' . esc_html($row->birthday) . '</td>';
            echo '<td>' . ($row->bone_age ? esc_html($row->bone_age) : '-') . '</td>';
            echo '<td>' . ($row->weight ? esc_html($row->weight) : '-') . '</td>';
            echo '<td>' . esc_html($row->current_height) . '</td>';
            echo '<td>' . esc_html($row->measure_time) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        // 分页导航
        $total_pages = ceil($total_items / $per_page);
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
        echo '</div></div>';

        echo '</div>';
    }
}

ZhuiGe_Xcx_Sgycai::getInstance();