USE db_logs;

CREATE TABLE `tb_regevt` (
	`ip` VARCHAR (16) NULL DEFAULT '' COMMENT 'IP��ַ',
	`sessionId` VARCHAR (64) NULL DEFAULT '' COMMENT 'sessionId',
	`ymd` INT (11) UNSIGNED NULL DEFAULT 0 COMMENT '������',
	`his` INT (11) UNSIGNED NULL DEFAULT 0 COMMENT 'ʱ����',
	`a` TINYINT (4) UNSIGNED NULL DEFAULT 0 COMMENT '�ֻ����볤��',
	`b` TINYINT (4) UNSIGNED NULL DEFAULT 0 COMMENT '�������볤��',
	`c` TINYINT (4) UNSIGNED NULL DEFAULT 0 COMMENT '������֤�����볤��',
	`reged` TINYINT (4) UNSIGNED NULL DEFAULT 0 COMMENT '�Ƿ�ע�᣺0δע�᣻1ע��',
	`sendcode` TINYINT (4) UNSIGNED NULL DEFAULT 0 COMMENT '�Ƿ��ȡ��֤�룺0δ��ȡ��1�ѻ�ȡ',
	`lt` VARCHAR (16) NULL DEFAULT '' COMMENT '��󽹵�������',
	`source` VARCHAR (60) NULL DEFAULT '' COMMENT '������',
	`channel` INT (255) UNSIGNED NULL DEFAULT 0 COMMENT 'ǰ������',
	`_post`  blob NULL,
  `_cookie`  blob NULL,
  `_get`  blob NULL
) ENGINE = MyISAM DEFAULT CHARACTER
SET = utf8 CHECKSUM = 0 DELAY_KEY_WRITE = 0;

INSERT INTO db_p2p.tb_config
SET k = 'dbsql.ver',
 v = '144-lyq' ON DUPLICATE KEY UPDATE v = '144-lyq';