USE db_p2p;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('RANDNAME_STR_LIB', 'ΰ������������ɽ�ʲ�����������ɴﰲ��������׳��˼Ⱥ���ܽ���ǫ�����֮�ֺ��ʲ���������Խ�����ʱ̩����Ӣ�������������֥����÷��������ɺɯ���������Ǻɵ��ؾ���Է�ܰ�����ع���ѱ�ˬ������ϣ����Ʈ�������������������������Ӱ��֦˼��', '#��������ֿ�#����Ϊ�����ַ�');

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('WECHAT_EMPTY_PIC', 'http://res.xiaoxialicai.com/app/misc/wechatEmptyPic.png', '#΢�ſհ�ͷ��');

delete from `tb_config_ram`;

insert into db_p2p.tb_config set k='dbsql.ver',v='141-lyq' ON DUPLICATE KEY UPDATE v='141-lyq';