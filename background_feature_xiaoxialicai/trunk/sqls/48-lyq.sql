-- -消息表
USE `db_p2p`;

CREATE TABLE `tb_message_0` (
	`msgId` BIGINT (20) UNSIGNED NOT NULL COMMENT '消息ID',
	`title` VARCHAR (150) NOT NULL DEFAULT '' COMMENT '标题',
	`content` text NOT NULL COMMENT '内容',
	`ext` VARCHAR (255) NOT NULL DEFAULT '' COMMENT '扩展字段',
	`sendId` BIGINT (20) UNSIGNED NOT NULL COMMENT '发送者ID',
	`receiverId` BIGINT (20) UNSIGNED NOT NULL COMMENT '接受者ID',
	`create_time` BIGINT (20) UNSIGNED NOT NULL COMMENT '创建时间',
	`status` TINYINT (4) NOT NULL DEFAULT 0 COMMENT '状态（0：未读1：已读-1：已删除）',
	`type` VARCHAR (255) NOT NULL COMMENT '消息类型：注册、红包。。',
	`iRecordVerID` INT (11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
	PRIMARY KEY (`msgId`),
	INDEX (`sendId`),
	INDEX (`receiverId`)
) ENGINE = MyISAM DEFAULT CHARACTER
SET = utf8 COMMENT = '消息表' CHECKSUM = 0 DELAY_KEY_WRITE = 0;

CREATE TABLE `tb_message_1` (
	`msgId` BIGINT (20) UNSIGNED NOT NULL COMMENT '消息ID',
	`title` VARCHAR (150) NOT NULL DEFAULT '' COMMENT '标题',
	`content` text NOT NULL COMMENT '内容',
	`ext` VARCHAR (255) NOT NULL DEFAULT '' COMMENT '扩展字段',
	`sendId` BIGINT (20) UNSIGNED NOT NULL COMMENT '发送者ID',
	`receiverId` BIGINT (20) UNSIGNED NOT NULL COMMENT '接受者ID',
	`create_time` BIGINT (20) UNSIGNED NOT NULL COMMENT '创建时间',
	`status` TINYINT (4) NOT NULL DEFAULT 0 COMMENT '状态（0：未读1：已读-1：已删除）',
	`type` VARCHAR (255) NOT NULL COMMENT '消息类型：注册、红包。。',
	`iRecordVerID` INT (11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
	PRIMARY KEY (`msgId`),
	INDEX (`sendId`),
	INDEX (`receiverId`)
) ENGINE = MyISAM DEFAULT CHARACTER
SET = utf8 COMMENT = '消息表' CHECKSUM = 0 DELAY_KEY_WRITE = 0;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 48) ON DUPLICATE KEY UPDATE v = 48;