-- 用户身高数据表
CREATE TABLE IF NOT EXISTS `wp_height_user_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '用户ID',
  `nickname` varchar(50) DEFAULT NULL COMMENT '宝宝昵称',
  `gender` tinyint(1) NOT NULL COMMENT '性别：1-男，2-女',
  `birthday` date DEFAULT NULL COMMENT '出生日期',
  `bone_age` decimal(5,2) DEFAULT NULL COMMENT '骨龄',
  `weight` decimal(5,2) DEFAULT NULL COMMENT '体重(kg)',
  `genetic_height` decimal(5,2) DEFAULT NULL COMMENT '遗传身高',
  `current_height` decimal(5,2) NOT NULL COMMENT '实测身高',
  `target_height` decimal(5,2) DEFAULT NULL COMMENT '可追身高',
  `prediction_probability` decimal(5,2) DEFAULT NULL COMMENT '可追高概率',
  `measure_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '测量时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_measure_time` (`measure_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户身高数据表';

-- 遗传身高基准表
CREATE TABLE IF NOT EXISTS `wp_height_predictions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) DEFAULT '' COMMENT '用户ID',
  `father_height` float NOT NULL COMMENT '父亲身高',
  `mother_height` float NOT NULL COMMENT '母亲身高',
  `boy_height` float NOT NULL COMMENT '男孩预测身高',
  `girl_height` float NOT NULL COMMENT '女孩预测身高',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='遗传身高基准表';