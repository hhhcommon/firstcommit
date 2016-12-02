CREATE DEFINER = CURRENT_USER PROCEDURE `batchUpdateUserRedpacket`()
    COMMENT '����ˢ��user_0��user_1�ĺ�����'
BEGIN
/**
����ˢ��user_0��user_1�ĺ�����
���裺
1����user���л�ȡuserId�ļ��ϣ�
2������vouchers��ͳ��ÿ��userId��Ӧ�ĺ���ͣ�
3������user��ĺ����
@author��LiangYanQing
*/
	DECLARE cur_userId BIGINT;
	DECLARE dtNow BIGINT;
	DECLARE cur_amountSum INT;
	/* ��ȡtb_user_0�е�userId */
	DECLARE col_userId0 CURSOR FOR
		SELECT userId FROM db_p2p.tb_user_0;
	/* ��ȡtb_user_1�е�userId */
	DECLARE col_userId1 CURSOR FOR
		SELECT userId FROM db_p2p.tb_user_1;

	/* ��ʽ����ǰʱ��ΪYmdHis */
	SET dtNow = DATE_FORMAT(NOW(), '%Y%m%d%H%i%s');
	SET cur_amountSum = 0;

	/* ��CURSOR����� */
	OPEN col_userId0;
		BEGIN
			/* ����EXIT HANDLER��ֹ���� */
			DECLARE EXIT HANDLER FOR 1329 BEGIN END;
			/* ����tb_user_0�е�userId */
			LOOP
				/* ȡ����ǰuserId */
				FETCH col_userId0 INTO cur_userId;
				/* ��ѯtb_vouchers_0����ȡ����δ���ڿ��õĺ���ܶ� */
				SELECT sum(amount) INTO cur_amountSum FROM db_p2p.tb_vouchers_0 WHERE `userId` = cur_userId AND voucherType = 8 AND dtUsed = 0 AND statusCode = 0 AND dtExpired > dtNow;

				/* ����/��ʼ��NULLΪ0 */
				IF cur_amountSum IS NULL THEN SET cur_amountSum = 0;
				END IF;

				/* ����tb_user_0�ĺ��������������ʱ�� */
				UPDATE db_p2p.tb_user_0 SET redPacket = cur_amountSum, redPacketRecentlyExpired = dtNow WHERE `userId` = cur_userId;

				SET cur_amountSum = 0;
			END LOOP;
		END;
	/* �ر�CURSOR�Ľ���� */
	CLOSE col_userId0;

	/* �������ѯtb_user_1��tb_vouchers_1������һģһ�� */
	OPEN col_userId1;
		BEGIN
			DECLARE EXIT HANDLER FOR 1329 BEGIN END;

			LOOP
				FETCH col_userId1 INTO cur_userId;
				SELECT sum(amount) INTO cur_amountSum FROM db_p2p.tb_vouchers_1 WHERE `userId` = cur_userId AND voucherType = 8 AND dtUsed = 0 AND statusCode = 0 AND dtExpired > dtNow;

				IF cur_amountSum IS NULL THEN SET cur_amountSum = 0;
				END IF;

				UPDATE db_p2p.tb_user_1 SET redPacket = cur_amountSum, redPacketRecentlyExpired = dtNow WHERE `userId` = cur_userId;

				SET cur_amountSum = 0;
			END LOOP;
		END;
	CLOSE col_userId1;
END;