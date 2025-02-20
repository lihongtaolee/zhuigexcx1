-- 用户身高数据表
CREATE TABLE `wp_height_user_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '用户ID',
  `baobaoname` varchar(50) DEFAULT NULL COMMENT '宝宝姓名',
  `gender` tinyint(1) NOT NULL COMMENT '性别：1-男，2-女',
  `birthday` date DEFAULT NULL COMMENT '出生日期',
  `father_height` decimal(5,2) DEFAULT NULL COMMENT '父亲身高',
  `mother_height` decimal(5,2) DEFAULT NULL COMMENT '母亲身高',
  `boy_genetic_height` decimal(5,2) DEFAULT NULL COMMENT '男孩遗传身高',
  `girl_genetic_height` decimal(5,2) DEFAULT NULL COMMENT '女孩遗传身高',
  `current_height` decimal(5,2) NOT NULL COMMENT '现在实测身高',
  `bone_age` decimal(5,2) DEFAULT NULL COMMENT '现在骨龄',
  `weight` decimal(5,2) DEFAULT NULL COMMENT '现在体重(kg)',
  `measure_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '测量时间',
  `city` varchar(50) DEFAULT NULL COMMENT '所在城市',
  `target_height` decimal(5,2) DEFAULT NULL COMMENT '期望成年身高',
  `prediction_height` decimal(5,2) DEFAULT NULL COMMENT '可追高身高',
  `prediction_probability` decimal(5,2) DEFAULT NULL COMMENT '可追高概率',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_measure_time` (`measure_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户身高数据表';

-- 遗传身高基准表
CREATE TABLE `wp_height_predictions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) DEFAULT '' COMMENT '用户ID',
  `father_height` float NOT NULL COMMENT '父亲身高',
  `mother_height` float NOT NULL COMMENT '母亲身高',
  `boy_height` float NOT NULL COMMENT '男孩预测身高',
  `girl_height` float NOT NULL COMMENT '女孩预测身高',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='遗传身高基准表';

-- 身高专题模块配置表
CREATE TABLE `wp_height_sgztmk` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '模块标题',
  `icon` varchar(255) DEFAULT NULL COMMENT '模块图标',
  `left_title` varchar(100) DEFAULT NULL COMMENT '左侧标题',
  `left_desc` text DEFAULT NULL COMMENT '左侧描述',
  `left_image` varchar(255) DEFAULT NULL COMMENT '左侧图片',
  `left_button` varchar(50) DEFAULT NULL COMMENT '左侧按钮文字',
  `left_link` varchar(255) DEFAULT NULL COMMENT '左侧链接',
  `left_value_api` varchar(255) DEFAULT NULL COMMENT '左侧数值接口路径',
  `left_bg_color` varchar(20) DEFAULT '#F0F8FF' COMMENT '左侧背景色',
  `right_top_title` varchar(100) DEFAULT NULL COMMENT '右上标题',
  `right_top_desc` text DEFAULT NULL COMMENT '右上描述',
  `right_top_image` varchar(255) DEFAULT NULL COMMENT '右上图片',
  `right_top_button` varchar(50) DEFAULT NULL COMMENT '右上按钮文字',
  `right_top_link` varchar(255) DEFAULT NULL COMMENT '右上链接',
  `right_top_bg_color` varchar(20) DEFAULT '#F5F5F5' COMMENT '右上背景色',
  `right_bottom_title` varchar(100) DEFAULT NULL COMMENT '右下标题',
  `right_bottom_desc` text DEFAULT NULL COMMENT '右下描述',
  `right_bottom_image` varchar(255) DEFAULT NULL COMMENT '右下图片',
  `right_bottom_button` varchar(50) DEFAULT NULL COMMENT '右下按钮文字',
  `right_bottom_link` varchar(255) DEFAULT NULL COMMENT '右下链接',
  `right_bottom_bg_color` varchar(20) DEFAULT '#F5F5F5' COMMENT '右下背景色',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='身高专题模块配置表';
