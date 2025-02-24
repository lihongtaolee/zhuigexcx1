<?php
class ZhuiGe_Shop_Controller extends ZhuiGe_Shop_Base {
    public function __construct() {
        parent::__construct();
    }

    /**
     * 商城首页数据
     */
    public function home($request) {
        $data = [
            'background' => ZhuiGe_Shop::option_value('home_background'),
            'slides' => ZhuiGe_Shop::option_value('home_slides'),
            'icon_navs' => ZhuiGe_Shop::option_value('home_icon_navs'),
            'home_rec' => $this->get_home_rec()
        ];

        return $this->success($data);
    }

    /**
     * 获取商品分类
     */
    public function cats($request) {
        $terms = get_terms([
            'taxonomy' => 'zhuige_shop_cat',
            'hide_empty' => false,
            'orderby' => 'term_order',
            'order' => 'DESC'
        ]);

        $cats = [];
        foreach ($terms as $term) {
            $cats[] = [
                'id' => $term->term_id,
                'name' => $term->name
            ];
        }

        return $this->success($cats);
    }

    /**
     * 获取商品列表
     */
    public function goods($request) {
        $cat_id = $this->param_int($request, 'cat_id', 0);
        $page = $this->param_int($request, 'page', 1);

        $args = [
            'post_type' => 'zhuige_shop_goods',
            'posts_per_page' => 10,
            'paged' => $page,
            'orderby' => 'date',
            'order' => 'DESC'
        ];

        if ($cat_id) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'zhuige_shop_cat',
                    'field' => 'term_id',
                    'terms' => $cat_id
                ]
            ];
        }

        $query = new WP_Query();
        $posts = $query->query($args);
        $goods = [];
        foreach ($posts as $post) {
            $goods[] = $this->format_goods_item($post);
        }

        return $this->success($goods);
    }

    /**
     * 获取商品详情
     */
    public function detail($request) {
        $goods_id = $this->param_int($request, 'goods_id', 0);
        if (!$goods_id) {
            return $this->error('缺少参数');
        }

        $post = get_post($goods_id);
        if (!$post) {
            return $this->error('商品不存在');
        }

        $data = $this->format_goods_detail($post);
        return $this->success($data);
    }

    /**
     * 格式化商品列表项
     */
    private function format_goods_item($post) {
        $thumbnail = get_post_meta($post->ID, 'thumbnail', true);
        $price = get_post_meta($post->ID, 'price', true);
        $orig_price = get_post_meta($post->ID, 'orig_price', true);
        $badge = get_post_meta($post->ID, 'badge', true);

        return [
            'id' => $post->ID,
            'title' => $post->post_title,
            'thumbnail' => $thumbnail,
            'price' => $price,
            'orig_price' => $orig_price,
            'badge' => $badge
        ];
    }

    /**
     * 格式化商品详情
     */
    private function format_goods_detail($post) {
        $data = $this->format_goods_item($post);
        $data['content'] = apply_filters('the_content', $post->post_content);
        $data['excerpt'] = get_post_meta($post->ID, 'excerpt', true);
        $data['slide'] = get_post_meta($post->ID, 'slide', true);
        $data['stock'] = get_post_meta($post->ID, 'stock', true);
        $data['quantity'] = get_post_meta($post->ID, 'quantity', true);

        return $data;
    }

    /**
     * 获取首页推荐商品
     */
    private function get_home_rec() {
        $home_rec = ZhuiGe_Shop::option_value('home_rec');
        if (!$home_rec) {
            return null;
        }

        $args = [
            'post_type' => 'zhuige_shop_goods',
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC'
        ];

        $query = new WP_Query();
        $posts = $query->query($args);
        $goods = [];
        foreach ($posts as $post) {
            $goods[] = $this->format_goods_item($post);
        }

        return [
            'title' => $home_rec['title'],
            'posts' => $goods
        ];
    }
}