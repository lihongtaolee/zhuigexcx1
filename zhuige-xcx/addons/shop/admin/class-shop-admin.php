<?php

/**
 * 追格小程序 - 商城模块
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class Shop_Admin
{
	private $shop;

	private $version;

	public function __construct($shop, $version)
	{
		$this->shop = $shop;
		$this->version = $version;
		
		// 使用正确的钩子注册元数据框
		add_action('admin_init', array($this, 'admin_init'));
		
		// 在post.php和post-new.php页面加载时注册元数据框
		add_action('load-post.php', array($this, 'setup_goods_metabox'));
		add_action('load-post-new.php', array($this, 'setup_goods_metabox'));
		
		// 添加保存钩子
		add_action('save_post_jq_goods', array($this, 'save_goods_metabox'), 10, 3);
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->shop, ZHUIGE_XCX_BASE_URL . 'addons/shop/admin/css/zhuige-shop-admin.css', array(), $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		// 获取当前屏幕
		$screen = get_current_screen();
		
		// 只在商品编辑页面加载媒体上传脚本
		if ($screen && ($screen->post_type == 'jq_goods')) {
			wp_enqueue_media();
		}
		
		// 添加版本号参数，确保浏览器不使用缓存的脚本
		$version = $this->version . '.' . time();
		wp_enqueue_script($this->shop, ZHUIGE_XCX_BASE_URL . 'addons/shop/admin/js/zhuige-shop-admin.js', array('jquery'), $version, true);
		
		// 添加内联脚本，清除任何可能的循环检查
		wp_add_inline_script($this->shop, '
			// 清除任何可能存在的定时器
			if (window.zhuigeShopMetaboxTimer) {
				clearTimeout(window.zhuigeShopMetaboxTimer);
				window.zhuigeShopMetaboxTimer = null;
			}
			
			// 禁用任何可能的循环检查函数
			window.showMetabox = function() { /* 空函数，防止循环调用 */ };
			window.checkMetabox = function() { /* 空函数，防止循环调用 */ };
			window.zhuigeShopDebug = false;
		');
	}

	public function setup_goods_metabox()
	{
		// 获取当前屏幕
		$screen = get_current_screen();
		
		// 只在商品编辑页面添加元数据框
		if (!$screen || $screen->post_type !== 'jq_goods') {
			return;
		}
		
		// 添加调试日志
		error_log('开始注册商品属性面板');
		
		// 添加商品属性元数据框
		add_meta_box(
			'zhuige_goods_attributes',           // 元数据框ID
			'追格商城设置',                      // 标题
			array($this, 'render_goods_metabox'), // 回调函数
			'jq_goods',                          // 文章类型
			'normal',                            // 上下文
			'high'                               // 优先级
		);
		
		error_log('商品属性面板注册完成');
	}

	/**
	 * 渲染商品属性元数据框
	 */
	public function render_goods_metabox($post)
	{
		// 添加安全检查
		wp_nonce_field('zhuige_goods_metabox', 'zhuige_goods_metabox_nonce');

		// 获取已保存的元数据
		$slide = get_post_meta($post->ID, 'zhuige-jq_goods-opt_slide', true);
		$badge = get_post_meta($post->ID, 'zhuige-jq_goods-opt_badge', true);
		$orig_price = get_post_meta($post->ID, 'zhuige-jq_goods-opt_orig_price', true);
		$price = get_post_meta($post->ID, 'zhuige-jq_goods-opt_price', true);
		$stock = get_post_meta($post->ID, 'zhuige-jq_goods-opt_stock', true);
		$quantity = get_post_meta($post->ID, 'zhuige-jq_goods-opt_quantity', true);

		// 设置默认值
		if (empty($orig_price)) $orig_price = '1';
		if (empty($price)) $price = '1';
		if (empty($stock)) $stock = '100';
		if (empty($quantity)) $quantity = '0';

		// 调试信息
		error_log('渲染商品属性面板，商品ID: ' . $post->ID);
		error_log('已保存数据: 角标=' . $badge . ', 原价=' . $orig_price . ', 促销价=' . $price . ', 库存=' . $stock . ', 销量=' . $quantity);
		if (!empty($slide) && is_array($slide)) {
			error_log('幻灯片数量: ' . count($slide));
		} else {
			error_log('幻灯片为空');
		}

		// 输出HTML表单
		?>
		<div class="zhuige-goods-metabox">
			<style>
				.zhuige-goods-metabox {
					padding: 10px;
				}
				.zhuige-goods-field {
					margin-bottom: 15px;
				}
				.zhuige-goods-field label {
					display: block;
					font-weight: bold;
					margin-bottom: 5px;
				}
				.zhuige-goods-field input[type="text"],
				.zhuige-goods-field input[type="number"] {
					width: 100%;
					max-width: 400px;
				}
				.zhuige-goods-slide-items {
					margin-top: 10px;
				}
				.zhuige-goods-slide-item {
					display: flex;
					align-items: center;
					margin-bottom: 10px;
					border: 1px solid #ddd;
					padding: 10px;
					background: #f9f9f9;
				}
				.zhuige-goods-slide-preview {
					width: 80px;
					height: 80px;
					background-size: cover;
					background-position: center;
					margin-right: 10px;
					border: 1px solid #ddd;
				}
				.zhuige-goods-slide-actions {
					margin-top: 10px;
				}
			</style>

			<!-- 幻灯片 -->
			<div class="zhuige-goods-field">
				<label>幻灯片</label>
				<div class="zhuige-goods-slide-items" id="zhuige-goods-slide-container">
					<?php
					if (!empty($slide) && is_array($slide)) {
						foreach ($slide as $index => $item) {
							$image_url = '';
							$image_id = '';
							
							if (isset($item['image']) && isset($item['image']['url'])) {
								$image_url = $item['image']['url'];
								$image_id = $item['image']['id'];
							} elseif (isset($item['image']) && is_numeric($item['image'])) {
								$image_url = wp_get_attachment_url($item['image']);
								$image_id = $item['image'];
							}
							?>
							<div class="zhuige-goods-slide-item">
								<div class="zhuige-goods-slide-preview" style="background-image: url('<?php echo esc_url($image_url); ?>')"></div>
								<div>
									<input type="hidden" name="zhuige_goods_slide[<?php echo $index; ?>][image]" value="<?php echo esc_attr($image_id); ?>">
									<button type="button" class="button zhuige-goods-slide-upload">选择图片</button>
									<button type="button" class="button zhuige-goods-slide-remove">删除</button>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
				<div class="zhuige-goods-slide-actions">
					<button type="button" class="button button-primary" id="zhuige-goods-slide-add">添加幻灯片</button>
				</div>
			</div>

			<!-- 角标 -->
			<div class="zhuige-goods-field">
				<label for="zhuige_goods_badge">角标</label>
				<input type="text" id="zhuige_goods_badge" name="zhuige_goods_badge" value="<?php echo esc_attr($badge); ?>" placeholder="角标">
			</div>

			<!-- 原价格 -->
			<div class="zhuige-goods-field">
				<label for="zhuige_goods_orig_price">原价格 (元)</label>
				<input type="number" id="zhuige_goods_orig_price" name="zhuige_goods_orig_price" value="<?php echo esc_attr($orig_price); ?>" step="0.01" min="0">
			</div>

			<!-- 促销价格 -->
			<div class="zhuige-goods-field">
				<label for="zhuige_goods_price">促销价格 (元)</label>
				<input type="number" id="zhuige_goods_price" name="zhuige_goods_price" value="<?php echo esc_attr($price); ?>" step="0.01" min="0">
			</div>

			<!-- 库存 -->
			<div class="zhuige-goods-field">
				<label for="zhuige_goods_stock">库存 (套)</label>
				<input type="number" id="zhuige_goods_stock" name="zhuige_goods_stock" value="<?php echo esc_attr($stock); ?>" min="0">
			</div>

			<!-- 销量 -->
			<div class="zhuige-goods-field">
				<label for="zhuige_goods_quantity">销量 (套)</label>
				<input type="number" id="zhuige_goods_quantity" name="zhuige_goods_quantity" value="<?php echo esc_attr($quantity); ?>" min="0">
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			// 媒体上传器
			var mediaUploader;
			
			// 添加幻灯片
			$('#zhuige-goods-slide-add').on('click', function(e) {
				e.preventDefault();
				
				var index = $('.zhuige-goods-slide-item').length;
				var newItem = `
					<div class="zhuige-goods-slide-item">
						<div class="zhuige-goods-slide-preview"></div>
						<div>
							<input type="hidden" name="zhuige_goods_slide[${index}][image]" value="">
							<button type="button" class="button zhuige-goods-slide-upload">选择图片</button>
							<button type="button" class="button zhuige-goods-slide-remove">删除</button>
						</div>
					</div>
				`;
				
				$('#zhuige-goods-slide-container').append(newItem);
			});
			
			// 选择图片
			$(document).on('click', '.zhuige-goods-slide-upload', function(e) {
				e.preventDefault();
				
				var button = $(this);
				var slideItem = button.closest('.zhuige-goods-slide-item');
				var imageInput = slideItem.find('input[type="hidden"]');
				var imagePreview = slideItem.find('.zhuige-goods-slide-preview');
				
				// 创建媒体上传器
				if (!mediaUploader) {
					mediaUploader = wp.media({
						title: '选择幻灯片图片',
						button: {
							text: '使用此图片'
						},
						multiple: false
					});
				}
				
				// 当选择图片时
				mediaUploader.off('select').on('select', function() {
					var attachment = mediaUploader.state().get('selection').first().toJSON();
					imageInput.val(attachment.id);
					imagePreview.css('background-image', 'url(' + attachment.url + ')');
					console.log('[追格商城调试] 已选择图片: ID=' + attachment.id + ', URL=' + attachment.url);
				});
				
				mediaUploader.open();
			});
			
			// 删除幻灯片
			$(document).on('click', '.zhuige-goods-slide-remove', function(e) {
				e.preventDefault();
				$(this).closest('.zhuige-goods-slide-item').remove();
				
				// 重新排序索引
				$('.zhuige-goods-slide-item').each(function(index) {
					$(this).find('input[type="hidden"]').attr('name', 'zhuige_goods_slide[' + index + '][image]');
				});
				
				console.log('[追格商城调试] 已删除幻灯片项');
			});
			
			// 表单提交前验证
			$('form#post').on('submit', function() {
				console.log('[追格商城调试] 表单提交，验证商品数据');
				return true;
			});
		});
		</script>
		<?php
	}

	/**
	 * 保存商品属性元数据
	 */
	public function save_goods_metabox($post_id, $post, $update)
	{
		// 安全检查
		if (!isset($_POST['zhuige_goods_metabox_nonce']) || !wp_verify_nonce($_POST['zhuige_goods_metabox_nonce'], 'zhuige_goods_metabox')) {
			error_log('商品属性保存失败：安全检查未通过');
			return;
		}

		// 检查自动保存
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			error_log('商品属性保存失败：自动保存中');
			return;
		}

		// 检查权限
		if (!current_user_can('edit_post', $post_id)) {
			error_log('商品属性保存失败：权限不足');
			return;
		}

		// 检查是否是商品类型
		if ($post->post_type !== 'jq_goods') {
			error_log('商品属性保存失败：不是商品类型');
			return;
		}

		// 添加调试日志
		error_log('开始保存商品属性数据，商品ID: ' . $post_id);
		error_log('POST数据: ' . print_r($_POST, true));

		// 保存幻灯片数据
		if (isset($_POST['zhuige_goods_slide']) && is_array($_POST['zhuige_goods_slide'])) {
			$slide_data = array();
			foreach ($_POST['zhuige_goods_slide'] as $item) {
				if (!empty($item['image'])) {
					$image_id = intval($item['image']);
					$image_url = wp_get_attachment_url($image_id);
					
					if ($image_url) {
						$slide_data[] = array(
							'image' => array(
								'id' => $image_id,
								'url' => $image_url
							)
						);
						error_log('添加幻灯片图片: ID=' . $image_id . ', URL=' . $image_url);
					}
				}
			}
			
			if (!empty($slide_data)) {
				update_post_meta($post_id, 'zhuige-jq_goods-opt_slide', $slide_data);
				error_log('保存幻灯片数据成功: ' . count($slide_data) . '张图片');
			} else {
				delete_post_meta($post_id, 'zhuige-jq_goods-opt_slide');
				error_log('删除幻灯片数据 (空数组)');
			}
		} else {
			error_log('未提交幻灯片数据');
		}

		// 保存角标
		if (isset($_POST['zhuige_goods_badge'])) {
			$badge = sanitize_text_field($_POST['zhuige_goods_badge']);
			update_post_meta($post_id, 'zhuige-jq_goods-opt_badge', $badge);
			error_log('保存角标: ' . $badge);
		}

		// 保存原价格
		if (isset($_POST['zhuige_goods_orig_price'])) {
			$orig_price = sanitize_text_field($_POST['zhuige_goods_orig_price']);
			update_post_meta($post_id, 'zhuige-jq_goods-opt_orig_price', $orig_price);
			error_log('保存原价格: ' . $orig_price);
		}

		// 保存促销价格
		if (isset($_POST['zhuige_goods_price'])) {
			$price = sanitize_text_field($_POST['zhuige_goods_price']);
			update_post_meta($post_id, 'zhuige-jq_goods-opt_price', $price);
			error_log('保存促销价格: ' . $price);
		}

		// 保存库存
		if (isset($_POST['zhuige_goods_stock'])) {
			$stock = sanitize_text_field($_POST['zhuige_goods_stock']);
			update_post_meta($post_id, 'zhuige-jq_goods-opt_stock', $stock);
			error_log('保存库存: ' . $stock);
		}

		// 保存销量
		if (isset($_POST['zhuige_goods_quantity'])) {
			$quantity = sanitize_text_field($_POST['zhuige_goods_quantity']);
			update_post_meta($post_id, 'zhuige-jq_goods-opt_quantity', $quantity);
			error_log('保存销量: ' . $quantity);
		}

		error_log('商品属性数据保存完成');
	}

	public function create_menu()
	{
		$prefix = 'zhuige-shop';

		CSF::createOptions($prefix, array(
			'framework_title' => '追格商城 <small>by <a href="https://www.zhuige.com" target="_blank" title="追格小程序">www.zhuige.com</a></small>',
			'menu_title' => '追格商城',
			'menu_slug'  => 'zhuige-shop',
			'menu_position' => 2,
			'show_bar_menu' => false,
            'show_sub_menu' => true,
			'footer_credit' => 'Thank you for creating with <a href="https://www.zhuige.com/" target="_blank">追格</a>',
			'menu_icon' => 'dashicons-layout',
		));

		$base_dir = plugin_dir_path(__FILE__);
		$base_url = plugin_dir_url(__FILE__);
		// 只引入存在的文件
		// require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/overview.php';
		// require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/global.php';
		require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/home.php';
		require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/category.php';
		// require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/search.php';
		// require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/mine.php';
		// require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/login.php';
		require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/goods.php';
		require_once ZHUIGE_XCX_ADDONS_DIR . 'shop/admin/order.php';

		//
        // 备份
        //
        CSF::createSection($prefix, array(
            'title'       => '备份',
            'icon'        => 'fas fa-shield-alt',
            'fields'      => array(
                array(
                    'type' => 'backup',
                ),
            )
        ));

		//过滤ID - 修复多选情况下 ID丢失造成的bug
		function zhuige_shop_sanitize_ids($ids, $type='') {
			if (!is_array($ids)) {
				return $ids;
			}

			$ids_n = [];
			foreach ($ids as $id) {
				if (($type=='cat' && get_category($id))) {
					$ids_n[] = $id;
				} else if ($type=='post' || $type=='page') {
					$post = get_post($id);
					if ($post && $post->post_status == 'publish') {
						$ids_n[] = $id;
					}
				}
			}
			return $ids_n;
		}

		function zhuige_shop_save_before( &$data, $option ) {
			$data['home_rec']['goods_ids'] = zhuige_shop_sanitize_ids($data['home_rec']['goods_ids'], 'post');
			return $data;
		}
		add_filter( 'csf_zhuige-shop_save', 'zhuige_shop_save_before', 10, 2 );
	}

	public function admin_init()
	{
		$this->handle_external_redirects();
	}

	public function admin_menu()
	{
		add_submenu_page('zhuige-shop', '', '追格产品', 'manage_options', 'ZhuiGe_Shop_setup', array(&$this, 'handle_external_redirects'));
		add_submenu_page('zhuige-shop', '', '新版下载', 'manage_options', 'ZhuiGe_Shop_upgrade', array(&$this, 'handle_external_redirects'));
	}

	public function handle_external_redirects()
	{
		$page = isset($_GET['page']) ? $_GET['page'] : '';
		
		if ('ZhuiGe_Shop_setup' === $page) {
			wp_redirect('https://www.zhuige.com/product.html');
			die;
		}

		if ('ZhuiGe_Shop_upgrade' === $page) {
			wp_redirect('https://www.zhuige.com/product/zhuige-wpmall.html');
			die;
		}
	}
}