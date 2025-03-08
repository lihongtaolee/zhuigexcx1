<?php
/**
 * 后台管理 - 商城首页设置
 * 追格商城后台原代码迁移，原封不动保留 CSF 配置
 *
 * 请确保 WordPress 环境中已加载 CSF（Codestar Framework）。
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // 防止直接访问
}

// 检查 CSF 是否加载
if ( ! class_exists( 'CSF' ) ) {
    echo '<div class="error notice"><p>错误：Codestar Framework 未加载，请确保 CSF 框架已正确安装和启用。</p></div>';
    return;
}

$prefix = 'zhuige_shop_options';

// 创建选项页面，menu_slug 必须与 WordPress 后台菜单中一致，此处设置为 "shop_home"
if ( class_exists( 'CSF' ) ) {
    CSF::createOptions( $prefix, array(
        'menu_title'      => '商城首页设置',
        'menu_slug'       => 'shop_home',
        'framework_title' => '商城首页设置',
        'show_reset_all'  => false,
        'menu_type'       => 'menu', // 可设置为 'menu' 或 'submenu'
        'menu_position'   => 60,
        'menu_icon'       => 'dashicons-store',
    ));

    // 创建配置分区，保留原有配置
    CSF::createSection($prefix, array(
        'id'    => 'home',
        'title' => '首页设置',
        'icon'  => 'fas fa-home',
        'fields' => array(
            array(
                'id'      => 'home_bg',
                'type'    => 'media',
                'title'   => '顶部背景图',
                'library' => 'image',
            ),
            array(
                'id'          => 'home_title',
                'type'        => 'text',
                'title'       => '分享标题',
                'placeholder' => '分享标题'
            ),
            array(
                'id'      => 'home_thumb',
                'type'    => 'media',
                'title'   => '分享缩略图',
                'library' => 'image',
            ),
            array(
                'id'     => 'home_slide',
                'type'   => 'group',
                'title'  => '幻灯片',
                'fields' => array(
                    array(
                        'id'       => 'title',
                        'type'     => 'text',
                        'title'    => '标题',
                    ),
                    array(
                        'id'      => 'image',
                        'type'    => 'media',
                        'title'   => '图片',
                        'library' => 'image',
                    ),
                    array(
                        'id'       => 'link',
                        'type'     => 'text',
                        'title'    => '链接',
                        'default'  => 'https://www.zhuige.com',
                    ),
                    array(
                        'id'    => 'switch',
                        'type'  => 'switcher',
                        'title' => '开启/停用',
                        'default' => '1'
                    ),
                ),
            ),
            array(
                'id'     => 'home_nav',
                'type'   => 'group',
                'title'  => '导航项',
                'fields' => array(
                    array(
                        'id'          => 'title',
                        'type'        => 'text',
                        'title'       => '标题',
                        'placeholder' => '标题'
                    ),
                    array(
                        'id'      => 'image',
                        'type'    => 'media',
                        'title'   => '图片',
                        'library' => 'image',
                    ),
                    array(
                        'id'       => 'link',
                        'type'     => 'text',
                        'title'    => '链接',
                        'default'  => 'https://www.zhuige.com',
                    ),
                    array(
                        'id'    => 'switch',
                        'type'  => 'switcher',
                        'title' => '开启/停用',
                        'default' => '1'
                    ),
                ),
            ),
            array(
                'id'     => 'home_rec',
                'type'   => 'fieldset',
                'title'  => '推荐商品',
                'fields' => array(
                    array(
                        'id'          => 'title',
                        'type'        => 'text',
                        'title'       => '标题',
                        'placeholder' => '标题'
                    ),
                    array(
                        'id'          => 'goods_ids',
                        'type'        => 'select',
                        'title'       => '选择商品',
                        'chosen'      => true,
                        'multiple'    => true,
                        'sortable'    => true,
                        'ajax'        => true,
                        'options'     => 'posts',
                        'query_args'  => array(
                            'post_type' => 'jq_goods',
                        ),
                        'placeholder' => '请选择商品',
                    ),
                    array(
                        'id'    => 'switch',
                        'type'  => 'switcher',
                        'title' => '开启/停用',
                        'subtitle' => '是否显示活动区域',
                        'default' => '1'
                    ),
                ),
            ),
            array(
                'id'          => 'goods_cat',
                'type'        => 'select',
                'title'       => '导航设置',
                'placeholder' => '选择分类',
                'chosen'      => true,
                'multiple'    => true,
                'sortable'    => true,
                'options'     => 'categories',
                'query_args'  => array(
                    'taxonomy'  => 'jq_goods_cat'
                ),
            ),
            array(
                'id'     => 'home_ad_pop',
                'type'   => 'fieldset',
                'title'  => '弹窗广告',
                'fields' => array(
                    array(
                        'id'      => 'image',
                        'type'    => 'media',
                        'title'   => '图片',
                        'library' => 'image',
                    ),
                    array(
                        'id'       => 'link',
                        'type'     => 'text',
                        'title'    => '链接',
                        'default'  => 'https://www.zhuige.com',
                    ),
                    array(
                        'id'    => 'switch',
                        'type'  => 'switcher',
                        'title' => '开启/停用',
                        'default' => '1'
                    ),
                    array(
                        'id'    => 'interval',
                        'type'  => 'number',
                        'title' => '间隔时间',
                        'subtitle' => '单位（小时）',
                    ),
                ),
            ),
        )
    ));
}

/**
 * 添加REST API接口实现
 * 注册商城首页和商品列表接口
 */
add_action('rest_api_init', 'zhuige_shop_register_routes');

function zhuige_shop_register_routes() {
    // 注册商城首页接口
    register_rest_route('zhuige/shop', '', array(
        'methods' => 'GET',
        'callback' => 'zhuige_shop_home_callback',
        'permission_callback' => '__return_true'
    ));

    // 注册商品列表接口
    register_rest_route('zhuige/shop', '/last', array(
        'methods' => 'GET',
        'callback' => 'zhuige_shop_last_callback',
        'permission_callback' => '__return_true'
    ));
}

/**
 * 商城首页接口回调函数
 */
function zhuige_shop_home_callback($request) {
    // 添加调试日志
    error_log('【商城模块】商城首页接口被调用');
    
    try {
        // 获取商城首页设置
        $options = get_option('zhuige_shop_options');
        
        // 检查设置是否存在
        if (empty($options)) {
            error_log('【商城模块】警告：商城设置为空');
        } else {
            // 记录设置数据
            error_log('【商城模块】商城设置数据: ' . print_r($options, true));
        }
        
        // 构建返回数据
        $data = array(
            'title' => '追格商城小程序',
            'desc' => '',
            'background' => '',
            'home_title' => '',
            'thumb' => site_url(),
            'slides' => array(),
            'icon_navs' => array(),
            'home_rec' => array(
                'title' => '推荐商品',
                'posts' => array(),
                'switch' => '1'
            ),
            'cats' => array()
        );
        
        // 设置背景图
        if (isset($options['home_bg']) && !empty($options['home_bg']['url'])) {
            $data['background'] = $options['home_bg']['url'];
        }
        
        // 设置分享标题
        if (isset($options['home_title']) && !empty($options['home_title'])) {
            $data['home_title'] = $options['home_title'];
        }
        
        // 设置分享缩略图
        if (isset($options['home_thumb']) && !empty($options['home_thumb']['url'])) {
            $data['thumb'] = $options['home_thumb']['url'];
        }
        
        // 设置幻灯片
        if (isset($options['home_slide']) && is_array($options['home_slide'])) {
            foreach ($options['home_slide'] as $slide) {
                if (isset($slide['switch']) && $slide['switch'] == '1' && isset($slide['image']) && !empty($slide['image']['url'])) {
                    $data['slides'][] = array(
                        'title' => isset($slide['title']) ? $slide['title'] : '',
                        'image' => $slide['image']['url'],
                        'link' => isset($slide['link']) ? $slide['link'] : ''
                    );
                }
            }
        }
        
        // 设置导航项
        if (isset($options['home_nav']) && is_array($options['home_nav'])) {
            foreach ($options['home_nav'] as $nav) {
                if (isset($nav['switch']) && $nav['switch'] == '1' && isset($nav['image']) && !empty($nav['image']['url'])) {
                    $data['icon_navs'][] = array(
                        'title' => isset($nav['title']) ? $nav['title'] : '',
                        'image' => $nav['image']['url'],
                        'link' => isset($nav['link']) ? $nav['link'] : ''
                    );
                }
            }
        }
        
        // 设置推荐商品
        if (isset($options['home_rec']) && isset($options['home_rec']['switch']) && $options['home_rec']['switch'] == '1') {
            $data['home_rec']['title'] = isset($options['home_rec']['title']) ? $options['home_rec']['title'] : '推荐商品';
            
            // 获取推荐商品ID列表
            $goods_ids = isset($options['home_rec']['goods_ids']) ? $options['home_rec']['goods_ids'] : array();
            
            // 验证商品ID是否有效
            if (empty($goods_ids)) {
                error_log('【商城模块】警告：推荐商品ID列表为空');
            } else {
                // 记录推荐商品ID
                error_log('【商城模块】推荐商品ID: ' . print_r($goods_ids, true));
                
                // 查询推荐商品
                $args = array(
                    'post_type' => 'jq_goods',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'post__in' => $goods_ids,
                    'orderby' => 'post__in'
                );
                
                $query = new WP_Query($args);
                error_log('【商城模块】推荐商品查询: 找到 ' . $query->post_count . ' 个商品');
                
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $post_id = get_the_ID();
                        
                        // 获取商品元数据
                        $badge = get_post_meta($post_id, 'zhuige-jq_goods-opt_badge', true);
                        $orig_price = get_post_meta($post_id, 'zhuige-jq_goods-opt_orig_price', true);
                        $price = get_post_meta($post_id, 'zhuige-jq_goods-opt_price', true);
                        $slide = get_post_meta($post_id, 'zhuige-jq_goods-opt_slide', true);
                        
                        // 记录商品元数据
                        error_log('【商城模块】商品ID ' . $post_id . ' 元数据: badge=' . $badge . ', orig_price=' . $orig_price . ', price=' . $price);
                        
                        // 获取缩略图
                        $thumbnail = '';
                        if (has_post_thumbnail()) {
                            $thumbnail = get_the_post_thumbnail_url($post_id, 'full');
                        } elseif (!empty($slide) && is_array($slide) && isset($slide[0]['image']) && isset($slide[0]['image']['url'])) {
                            $thumbnail = $slide[0]['image']['url'];
                        }
                        
                        // 验证缩略图
                        if (empty($thumbnail)) {
                            error_log('【商城模块】警告：商品ID ' . $post_id . ' 缩略图为空');
                        }
                        
                        // 添加商品数据
                        $data['home_rec']['posts'][] = array(
                            'id' => $post_id,
                            'title' => get_the_title(),
                            'thumbnail' => $thumbnail,
                            'badge' => $badge,
                            'orig_price' => $orig_price,
                            'price' => $price,
                            'slide' => $slide
                        );
                    }
                    wp_reset_postdata();
                } else {
                    error_log('【商城模块】警告：未找到推荐商品');
                }
            }
        }
        
        // 设置分类
        if (isset($options['goods_cat']) && is_array($options['goods_cat'])) {
            foreach ($options['goods_cat'] as $cat_id) {
                $term = get_term($cat_id, 'jq_goods_cat');
                if ($term && !is_wp_error($term)) {
                    $data['cats'][] = array(
                        'id' => $term->term_id,
                        'name' => $term->name
                    );
                }
            }
        }
        
        // 设置弹窗广告
        if (isset($options['home_ad_pop']) && isset($options['home_ad_pop']['switch']) && $options['home_ad_pop']['switch'] == '1' && isset($options['home_ad_pop']['image']) && !empty($options['home_ad_pop']['image']['url'])) {
            $data['pop_ad'] = array(
                'image' => $options['home_ad_pop']['image']['url'],
                'link' => isset($options['home_ad_pop']['link']) ? $options['home_ad_pop']['link'] : '',
                'interval' => isset($options['home_ad_pop']['interval']) ? intval($options['home_ad_pop']['interval']) : 24
            );
        }
        
        // 记录返回数据
        error_log('【商城模块】商城首页接口返回数据: ' . print_r($data, true));
        
        return array(
            'code' => 0,
            'msg' => '操作成功！',
            'data' => $data
        );
    } catch (Exception $e) {
        // 记录异常
        error_log('【商城模块】商城首页接口异常: ' . $e->getMessage());
        
        return array(
            'code' => 1,
            'msg' => '服务器内部错误',
            'data' => array()
        );
    }
}

/**
 * 商品列表接口回调函数
 */
function zhuige_shop_last_callback($request) {
    // 获取请求参数
    $offset = isset($request['offset']) ? intval($request['offset']) : 0;
    $cat_id = isset($request['cat_id']) ? intval($request['cat_id']) : 0;
    
    // 构建查询参数
    $args = array(
        'post_type' => 'jq_goods',
        'post_status' => 'publish',
        'posts_per_page' => 10,
        'offset' => $offset
    );
    
    // 如果指定了分类，添加分类筛选
    if ($cat_id > 0) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'jq_goods_cat',
                'field' => 'term_id',
                'terms' => $cat_id
            )
        );
    }
    
    // 查询商品
    $query = new WP_Query($args);
    $goods_list = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            
            // 获取商品元数据
            $badge = get_post_meta($post_id, 'zhuige-jq_goods-opt_badge', true);
            $orig_price = get_post_meta($post_id, 'zhuige-jq_goods-opt_orig_price', true);
            $price = get_post_meta($post_id, 'zhuige-jq_goods-opt_price', true);
            $slide = get_post_meta($post_id, 'zhuige-jq_goods-opt_slide', true);
            
            // 获取缩略图
            $thumbnail = '';
            if (has_post_thumbnail()) {
                $thumbnail = get_the_post_thumbnail_url($post_id, 'full');
            } elseif (!empty($slide) && is_array($slide) && isset($slide[0]['image']) && isset($slide[0]['image']['url'])) {
                $thumbnail = $slide[0]['image']['url'];
            }
            
            // 添加商品数据
            $goods_list[] = array(
                'id' => $post_id,
                'title' => get_the_title(),
                'thumbnail' => $thumbnail,
                'badge' => $badge,
                'orig_price' => $orig_price,
                'price' => $price,
                'slide' => $slide
            );
        }
        wp_reset_postdata();
    }
    
    // 判断是否还有更多数据
    $more = 'noMore';
    if (count($goods_list) == 10) {
        $more = 'more';
    }
    
    return array(
        'code' => 0,
        'msg' => '操作成功！',
        'data' => array(
            'list' => $goods_list,
            'more' => $more
        )
    );
}
