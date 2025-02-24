<?php

class ZhuiGe_Shop_Post_Types
{
    public function create_custom_post_type()
    {
        $goods_labels = array(
            'name'               => '追格商品',
            'singular_name'      => '追格商品',
            'add_new'            => '新建商品',
            'add_new_item'       => '新建一个商品',
            'edit_item'          => '编辑商品',
            'new_item'           => '新建商品',
            'all_items'          => '所有商品',
            'view_item'          => '查看商品',
            'search_items'       => '搜索商品',
            'not_found'          => '没有找到有关商品',
            'not_found_in_trash' => '回收站里面没有相关商品',
            'parent_item_colon'  => '',
            'menu_name'          => '追格商品'
        );
        $goods_args = array(
            'labels'        => $goods_labels,
            'description'   => '我们网站的追格商品信息',
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
            'has_archive'   => true
        );
        register_post_type('jq_goods', $goods_args);

        $goods_cat_labels = array(
            'name'              => '商品分类',
            'singular_name'     => '商品分类',
            'search_items'      => '搜索分类',
            'all_items'         => '所有分类',
            'parent_item'       => '该分类的上级分类',
            'parent_item_colon' => '该分类的上级分类：',
            'edit_item'         => '编辑分类',
            'update_item'       => '更新分类',
            'add_new_item'      => '添加新的分类',
            'new_item_name'     => '商品分类',
            'menu_name'         => '商品分类',
        );
        $goods_cat_args = array(
            'labels'        => $goods_cat_labels,
            'hierarchical'  => true,
        );
        register_taxonomy('jq_goods_cat', 'jq_goods', $goods_cat_args);
    }
}

// 自动注册自定义文章类型和分类
if ( function_exists( 'add_action' ) ) {
    $zhui_ge_shop_post_types = new ZhuiGe_Shop_Post_Types();
    add_action( 'init', array( $zhui_ge_shop_post_types, 'create_custom_post_type' ) );
}
