<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 宝宝身高数据管理
 */
class ZhuiGe_Xcx_Sgycai {
    private static $instance = null;

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    public static function getInstance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 插件激活时创建数据表
    public static function activate() {
        global $wpdb;
        $sql_file = dirname(plugin_dir_path(__FILE__)) . '/sql/install.sql';
        if ( file_exists( $sql_file ) ) {
            $sql = file_get_contents( $sql_file );
            if ( $sql ) {
                $sql = str_replace( 'wp_', $wpdb->prefix, $sql );
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                $result = dbDelta( $sql );
                error_log( '[ZhuiGe_Xcx_Sgycai] dbDelta 执行结果：' . print_r( $result, true ) );
                $table_name = $wpdb->prefix . 'height_user_data';
                if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) {
                    error_log( "[ZhuiGe_Xcx_Sgycai] 数据表 {$table_name} 创建失败" );
                } else {
                    error_log( "[ZhuiGe_Xcx_Sgycai] 数据表 {$table_name} 已创建" );
                }
            } else {
                error_log( "[ZhuiGe_Xcx_Sgycai] 无法读取 SQL 文件内容：{$sql_file}" );
            }
        } else {
            error_log( "[ZhuiGe_Xcx_Sgycai] SQL 文件不存在：{$sql_file}" );
        }
    }

    public function admin_menu() {
        add_menu_page(
            '宝宝身高预测AI',
            '宝宝身高预测AI',
            'manage_options',
            'zhuige-user-height-data',
            array( $this, 'user_height_data_page' ),
            'dashicons-chart-line',
            26
        );
    }

    public function register_rest_routes() {
        register_rest_route( 'sgtool/v1', '/save_user_data', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'save_user_data' ),
            'permission_callback' => '__return_true'
        ) );
    }

    public function save_user_data( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'height_user_data';
        $data = json_decode( $request->get_body(), true );

        // 检查必填字段
        if ( empty( $data['baobaoname'] ) || empty( $data['gender'] ) || empty( $data['birthday'] ) ||
             empty( $data['current_height'] ) || empty( $data['father_height'] ) ||
             empty( $data['mother_height'] ) || empty( $data['city'] ) ) {
            error_log("必填字段不全，接收到的数据：" . print_r($data, true));
            return array(
                'code' => 400,
                'msg'  => '请填写必填字段'
            );
        }

        // 直接使用传入的 user_id 或 get_current_user_id()
        $user_id = isset($data['user_id']) ? intval($data['user_id']) : get_current_user_id();
        error_log("使用的用户ID: " . $user_id);
        if ( ! $user_id ) {
            error_log("无有效用户，返回请先登录");
            return array(
                'code' => 401,
                'msg'  => '请先登录'
            );
        }

        // 计算遗传身高（中位父母身高法）
        $father = floatval($data['father_height']);
        $mother = floatval($data['mother_height']);
        $boy_genetic = ($father + $mother + 13) / 2;
        $girl_genetic = ($father + $mother - 13) / 2;
        $genetic = ($data['gender'] == 1) ? $boy_genetic : $girl_genetic;

        // 计算当前年龄：若 bone_age 存在则取骨龄，否则根据生日计算（取整数年）
        if (!empty($data['bone_age'])) {
            $age = floatval($data['bone_age']);
        } else {
            $birthDate = new DateTime($data['birthday']);
            $currentDate = new DateTime(current_time('mysql'));
            $age = $birthDate->diff($currentDate)->y;
        }
        $current_height = floatval($data['current_height']);
        // 预测可追高身高公式
        $predicted_addition = ($genetic - $current_height) * (1 - ($age / 18));
        $prediction_height = $current_height + $predicted_addition;
        // 可追高概率计算：target_height 必须大于0
        if (!empty($data['target_height']) && floatval($data['target_height']) > 0) {
            $prediction_probability = $prediction_height / floatval($data['target_height']);
        } else {
            $prediction_probability = null;
        }
        error_log("计算结果: boy_genetic={$boy_genetic}, girl_genetic={$girl_genetic}, prediction_height={$prediction_height}, prediction_probability={$prediction_probability}");

        $insert_data = array(
            'user_id'                => $user_id,
            'baobaoname'             => sanitize_text_field( $data['baobaoname'] ),
            'gender'                 => intval( $data['gender'] ),
            'birthday'               => sanitize_text_field( $data['birthday'] ),
            'father_height'          => $father,
            'mother_height'          => $mother,
            'boy_genetic_height'     => $boy_genetic,
            'girl_genetic_height'    => $girl_genetic,
            'current_height'         => $current_height,
            'bone_age'               => ! empty( $data['bone_age'] ) ? floatval( $data['bone_age'] ) : null,
            'weight'                 => ! empty( $data['weight'] ) ? floatval( $data['weight'] ) : null,
            'city'                   => sanitize_text_field( $data['city'] ),
            'target_height'          => ! empty( $data['target_height'] ) ? floatval( $data['target_height'] ) : null,
            'prediction_height'      => $prediction_height,
            'prediction_probability' => $prediction_probability,
            'measure_time'           => isset($data['measure_time']) ? sanitize_text_field($data['measure_time']) : current_time( 'mysql' )
        );

        $result = $wpdb->insert( $table_name, $insert_data );
        if ( $result === false ) {
            error_log("保存数据失败，插入数据：" . print_r( $insert_data, true ));
            return array(
                'code' => 500,
                'msg'  => '保存失败'
            );
        }
        error_log("保存数据成功，用户ID：{$user_id}");
        return array(
            'code' => 0,
            'msg'  => '保存成功'
        );
    }

    public function user_height_data_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '您没有足够的权限访问此页面。' ) );
        }
        echo '<div class="wrap">';
        echo '<h1>宝宝身高数据管理</h1>';
        global $wpdb;
        $table_name = $wpdb->prefix . 'height_user_data';
        $user_table_name = $wpdb->prefix . 'users';
        $meta_table = $wpdb->prefix . 'usermeta';
        $per_page = 20;
        $current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
        $offset = ( $current_page - 1 ) * $per_page;
        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        
        $sql = $wpdb->prepare( "
            SELECT
                hud.id,
                hud.user_id,
                u.display_name as username,
                um.meta_value as mobile,
                hud.baobaoname,
                hud.gender,
                hud.birthday,
                hud.father_height,
                hud.mother_height,
                hud.boy_genetic_height,
                hud.girl_genetic_height,
                hud.current_height,
                hud.bone_age,
                hud.weight,
                hud.measure_time,
                hud.city,
                hud.target_height,
                hud.prediction_probability,
                hud.create_time,
                hud.update_time
            FROM {$table_name} AS hud
            LEFT JOIN {$user_table_name} AS u ON hud.user_id = u.ID
            LEFT JOIN {$meta_table} AS um ON u.ID = um.user_id AND um.meta_key = 'zhuige_xcx_user_mobile'
            ORDER BY hud.measure_time DESC
            LIMIT %d OFFSET %d
        ", $per_page, $offset );
        
        $results = $wpdb->get_results( $sql );
        echo '<table class="wp-list-table widefat fixed striped table-view-list posts">';
        echo '<thead>
            <tr>
                <th>ID</th>
                <th>用户名</th>
                <th>手机号</th>
                <th>宝宝姓名</th>
                <th>性别</th>
                <th>出生日期</th>
                <th>父亲身高(cm)</th>
                <th>母亲身高(cm)</th>
                <th>男孩遗传身高(cm)</th>
                <th>女孩遗传身高(cm)</th>
                <th>现在实测身高(cm)</th>
                <th>现在骨龄</th>
                <th>现在体重(kg)</th>
                <th>测量时间</th>
                <th>所在城市</th>
                <th>期望成年身高</th>
                <th>可追高概率</th>
                <th>创建时间</th>
                <th>更新时间</th>
            </tr>
          </thead>';
        echo '<tbody>';
        if ( ! empty( $results ) ) {
            foreach ( $results as $row ) {
                echo '<tr>';
                echo '<td>' . esc_html( $row->id ) . '</td>';
                echo '<td>' . esc_html( $row->username ) . '</td>';
                echo '<td>' . esc_html( $row->mobile ) . '</td>';
                echo '<td>' . esc_html( $row->baobaoname ) . '</td>';
                echo '<td>' . ( $row->gender == 1 ? '男' : '女' ) . '</td>';
                echo '<td>' . esc_html( $row->birthday ) . '</td>';
                echo '<td>' . ( $row->father_height ? esc_html( $row->father_height ) : '-' ) . '</td>';
                echo '<td>' . ( $row->mother_height ? esc_html( $row->mother_height ) : '-' ) . '</td>';
                echo '<td>' . ( $row->boy_genetic_height ? esc_html( $row->boy_genetic_height ) : '-' ) . '</td>';
                echo '<td>' . ( $row->girl_genetic_height ? esc_html( $row->girl_genetic_height ) : '-' ) . '</td>';
                echo '<td>' . esc_html( $row->current_height ) . '</td>';
                echo '<td>' . ( $row->bone_age ? esc_html( $row->bone_age ) : '-' ) . '</td>';
                echo '<td>' . ( $row->weight ? esc_html( $row->weight ) : '-' ) . '</td>';
                echo '<td>' . esc_html( $row->measure_time ) . '</td>';
                echo '<td>' . esc_html( $row->city ) . '</td>';
                echo '<td>' . ( $row->target_height ? esc_html( $row->target_height ) : '-' ) . '</td>';
                echo '<td>' . ( $row->prediction_probability ? esc_html( $row->prediction_probability ) : '-' ) . '</td>';
                echo '<td>' . esc_html( $row->create_time ) . '</td>';
                echo '<td>' . esc_html( $row->update_time ) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="19">暂无数据</td></tr>';
        }
        echo '</tbody></table>';
        $total_pages = ceil( $total_items / $per_page );
        echo '<div class="tablenav bottom">';
        echo '<div class="tablenav-pages">';
        echo paginate_links( array(
            'base'      => add_query_arg( 'paged', '%#%' ),
            'format'    => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total'     => $total_pages,
            'current'   => $current_page
        ) );
        echo '</div></div>';
        echo '</div>';
    }
}

ZhuiGe_Xcx_Sgycai::getInstance();
