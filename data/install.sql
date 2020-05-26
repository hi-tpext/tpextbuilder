CREATE TABLE IF NOT EXISTS `__PREFIX__attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '后台用户id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '前台用户id',
  `name` varchar(55) NOT NULL DEFAULT '' COMMENT '名称',
  `mime` varchar(125) NOT NULL DEFAULT '' COMMENT 'mime类型',
  `suffix` varchar(10) NOT NULL DEFAULT '' COMMENT '后缀',
  `size` double(10,3) unsigned DEFAULT '0' COMMENT '大小',
  `sha1` varchar(40) NOT NULL DEFAULT '' COMMENT 'sha1',
  `storage` varchar(40) NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT 'url链接',
  `create_time` datetime NOT NULL DEFAULT '2020-01-01 00:00:00' COMMENT '添加时间',
  `update_time` datetime NOT NULL DEFAULT '2020-01-01 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  INDEX(`admin_id`),
  INDEX(`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='上传文件表';