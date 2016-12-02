<?php
/**
 * 理财产品接口，包括：理财产品列表,理财产品明细
 * @author simon.wang
 */
class FinancingController extends \Prj\BaseCtrl{



    /**
	 * 添加参数 __VIEW__=json 返回json数据格式
	 * 列表项字段：
	 * {
	 *		waresId: 标的ID,
	 *		waresName：标的名称,
	 *		waresSN：第几期,
	 *		ICON:图标，如果有，没有的话，空串""，
	 *		priceStart:起投金额，
	 *		priceStep:递增金额
	 *		amount:总金额
	 *		remain:剩余金额
	 *		periodDesc:周期（天数，月数）
	 *		interestStart:起息T+?
	 *		guaranteeType：担保类型
	 *		yieldStatic:年化利率,8.4
	 *		yieldStaticAdd:上浮年化利率
	 * 		yieldFloatFrom:浮动收益下限
	 *		yieldFloatTo:浮动收益上限
	 *		tags：额外标签（新手标之类）
     *      更多的可以去数据字典中查询
	 * }
	 * @input int pageId 分页ID,从1开始
	 * @input int pageSize 分页尺寸
	 * @input int shelfId 类型Id 默认2000  2000/3000/4000
     * @input string order 排序 like sort_shelfId/rsort_shelfId
	 * @output {"code":200,"order":排序方式,"WaresList":{"rs":[【列表项1字段】，【列表项2字段】],"pager":{"pageId":1开始的数字,"total":总共多少符合条件的记录}}}
	 * @errors {"code":400,"msg":"arg_error"}
	 */

	public function byshelfAction()
	{
		$shelfId=$this->_request->get('shelfId',2000);
		$pageId=$this->_request->get('pageId',1)-0;
        $order = $this->_request->get('order');
        if(!empty($order))
        {
            $order = str_replace("_"," ",$order);//下划线分割
        }
		$this->loger->mainType = $shelfId; //日志记录
		$this->loger->target = $pageId;
		if($shelfId>0){
			$pager = new \Sooh\DB\Pager($this->_request->get('pageSize',5)-0);
			$pager->init(-1, $pageId);
            $where['timeStartPlan<'] = date('YmdHis',strtotime('+12 hours',time()));
            $where['statusCode'] = [
                \Prj\Consts\Wares::status_go,
                \Prj\Consts\Wares::status_ready,
                \Prj\Consts\Wares::status_open,
            ];
            //var_log($where,'>>>>>>>>>>');
			$wares = \Prj\Data\Wares::paged($shelfId, $pager,$order,$where);
            if(empty($wares) || \Sooh\Base\Tests\Bomb::blowup('wares_is_empty',false))
            {
                return $this->returnOK('content_empty');
            }
            foreach($wares as $k=>$v)
            {
                $wares[$k]['ymdPayPlan'] = date('YmdHis',$v['ymdPayPlan']);
                //$wares[$k]['item'] = '××基金';
                unset($wares[$k]['introDisplay']);
                unset($wares[$k]['iRecordVerID']);
                unset($wares[$k]['sLockData']);
                unset($wares[$k]['realRaise']);
                $wares[$k]['tags'] = preg_replace('/[X-Y]*/i','',$wares[$k]['tags']);
            }
            $this->_view->assign('order',$order);
			$this->_view->assign('waresList',$wares);
            $this->_view->assign('pager',$pager->toArray());
			$this->returnOK();
		}else{
			$this->returnError(\Sooh\Base\ErrException::msgErrorArg);
		}
       
	}

	/**
	 * 根据对应标的ID提供显示指定标的的数据
	 * 标的数据包括：
	 * {
	 *		waresId: 标的ID,
	 *		waresName：标的名称,
	 *		waresSN：第几期,
	 *		ICON:图标，如果有，没有的话，空串""，
	 *		priceStart:起投金额，
	 *		priceStep:递增金额
	 *		amount:总金额
	 *		remain:剩余金额
	 *		periodDesc:周期（天数，月数）
	 *		interestStart:起息T+?
	 *		guaranteeType：担保类型
	 *		guaranteeDesc：担保详细信息
	 *		yieldStatic:年化利率
	 *		yieldStaticAdd:上浮年化利率
	 *		yieldFloatFrom:浮动收益下限
	 *		yieldFloatTo:浮动收益上限
	 *		tags：额外标签（新手标之类）
	 *		waresDesc：。。。。。
     *      更多的可以去数据字典中查询
     *      webUrl H5地址
     *      images 图片列表 (http://xiaoxia.zzwwsfks.com/public/getImage&fileId=wares_14574917817462)
     *          id 身份证
     *          book 户口本
     *          married 婚姻状态
     *          credit 征信报告
     *          inspect 实地考察图册
     *          contract 借款抵押保证合同
     *          receipt  转账回执
     *          property  房产证
	 * }
	 * @input string id 理财产品Id
     * @input int pageId 投资记录分页
     * @input int pageSize 投资记录分页
	 * @output {"code":200,"Financing":【标的数据】}
	 * @errors {"code":400,"msg":"arg_error"}
	 * @errors {"code":400,"msg":"ware_flow"} 流标了
	 */
	public function detailAction()
	{
        //var_log($_SERVER);
		$id = $this->_request->get('id');
        $pageId = $this->_request->get('pageId',1)-0;
        $pageSize = $this->_request->get('pageSize',10)-0;
        $pager = new \Sooh\DB\Pager($pageSize);
        $pager->init(-1, $pageId);

		$sys = \Prj\Data\Wares::getCopy($id);
		$sys->load();
		if(!$sys->exists()){
			return $this->returnError(\Sooh\Base\ErrException::msgErrorArg);
		}
        $show = 1;
        if($sys->getField('statusCode')==\Prj\Consts\Wares::status_go){
            $userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
            if(empty($userId)){
               $show = 0;
            }else{
                $num = $this->_buyTime($id,$userId);
                if(!$num){
                    $show = 0;
                }else{
                    $this->_view->assign('buyTimes',$num);
                }
            }
        }
        //$this->_view->assign('show',$show);
        //? 禁止未上架标的显示
        if($sys->getField('statusCode')<=0 && $sys->getField('statusCode')!=-3) //todo 流标展示
        {
           // return $this->returnError('标的不存在');
            return $this->returnError(\Prj\Lang\Broker::getMsg('financing.wares_not_existing'));
        }

		$this->loger->target = $id;
        $className = '\Prj\WaresTpl\\'.$sys->getField('viewTPL').'\Viewer';
		$r = $this->getDetail($sys);
        $r['show'] = $show;
        $this->loger->waresName = $sys->getField('waresName');
		if($sys->getField('statusCode')==\Prj\Consts\Wares::status_open){
			$r['uniqueOpId']=  \Lib\Misc\UniqueOp::createFor($id,'orders/add');
		}else{
			$r['uniqueOpId']='';
		}
		$this->_view->assign('financing',$r);

		$this->returnOK();
	}

    public function getDetail(\Prj\Data\Wares $ware){
        $className = '\Prj\WaresTpl\\'.$ware->getField('viewTPL').'\Viewer';
        $r = array(
            'waresId'=>$ware->getPKey()['waresId'],
            'waresName'=>$ware->getField('waresName'),
            'mainType'=>$ware->getField('mainType'),
            //'mainTypeName'=>$this->typeNameArr[$ware->getField('mainType')],
            'waresSN'=>$ware->getField('waresSN'),
            "deadLine"=>$ware->getField('deadLine'),
            "dlUnit"=>$ware->getField('dlUnit'),
            'timeStartPlan'=>$ware->getField('timeStartPlan'),
            'returnType'=>$ware->getField('returnType'),
            //'returnTypeName'=>$this->returnTypeNameArr[$ware->getField('returnType')],
            'ymdPayPlan'=>date('YmdHis',$ware->getField('ymdPayPlan')),
            'statusCode'=>$ware->getField('statusCode'),
            'ICON'=>'todo',
            'priceStart'=>$ware->getField('priceStart'),
            'priceStep'=>$ware->getField('priceStep'),
            'tags'=>$ware->getField('tags'),
            'yieldStatic'=>$ware->getField('yieldStatic'),
            'yieldStaticAdd'=>$ware->getField('yieldStaticAdd'),
            'yieldFloatFrom'=>$ware->getField('yieldFloatFrom'),
            'yieldFloatTo'=>$ware->getField('yieldFloatTo'),
            'amount'=>$ware->getField('amount'),
            'remain'=>$ware->getField('remain'),
            'periodDesc'=>'xx天（对于月的31日怎么处理）',
            'introView'=>$ware->getField('viewTPL'),
            'introDisplay'=>['a'=>'','b'=>[]],
            'item'=>'中华基金',
            'webUrl'=>\Sooh\Base\Tools::uri(['waresId'=>$ware->getPKey()['waresId']],'newDec'),
            'images'=>$className::getImgList($ware->getField('introDisplay')),
        );
        return $r;
    }

    public function decAction()
    {
        $waresId = $this->_request->get('waresId');
      //  if(empty($waresId))$this->returnError('args_error');
        if(empty($waresId))$this->returnError(\Prj\Lang\Broker::getMsg('financing.args_error'));
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
       // if(!$ware->exists())$this->returnError('ware_null');
        if(!$ware->exists())$this->returnError(\Prj\Lang\Broker::getMsg('financing.wares_not_existing'));
        $introDisplay = $ware->getField('introDisplay');
        //var_log($introDisplay,'dec>>>>>>>>>>>>>>>');
        $this->_view->assign('dec',$introDisplay['a']);
    }

    public function newDecAction(){
        // \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresId = $this->_request->get('waresId');
        if(empty($waresId))return $this->returnError(\Prj\Lang\Broker::getMsg('financing.args_error'));
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        if(!$ware->load())return $this->returnError(\Prj\Lang\Broker::getMsg('system.server_busy'));
        $tpl = $ware->getField('viewTPL');
        $className = '\Prj\WaresTpl\\'.$tpl.'\Viewer';
        $data = $ware->getField('introDisplay');

        $data = $className::format($data);

        $this->_view->assign('data',$data);
        $this->_view->assign('view',$className);
        $this->_view->display('financing/newdec_'.strtolower($tpl).'.phtml');
    }

    /**
     * 标的的投资记录
     * By Hand 15.11.20
     * $input int waresId
     * $input int pageId
     * $input int pageSize
     * $input string lastPage
     */
    public function investListAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresId = $this->_request->get('waresId');
        $pageId = $this->_request->get('pageId',1)-0;
        $pageSize = $this->_request->get('pageSize',10)-0;
        $pager = new \Sooh\DB\Pager($pageSize);
        $pager->init(-1, $pageId);
      //  if(empty($waresId))return $this->returnError('args_error');
        if(empty($waresId))return $this->returnError(\Prj\Lang\Broker::getMsg('financing.args_error'));
        $rs = $this->_invest($waresId,$pager);
        $this->_view->assign('list',$rs?$rs:[]);
        $this->returnOK();
    }

    protected function _invest($waresId,$pager)
    {
        $where = [
            'waresId'=>$waresId,
            'orderStatus'=>\Prj\Consts\OrderStatus::$running,
        ];
        $lastPage = $this->_request->get('lastPage');
        if(!empty($lastPage))$lastPage = json_decode($lastPage,true);
        $rss = \Prj\Data\Investment::pager(null,$pager,null,null,$where,'rsort orderTime',$lastPage);
        $rs = $rss['records'];
        $this->_view->assign('lastPage',json_encode($rss['lastPage']));
        if(!empty($rs))
        {
            $num = 0;
            foreach($rs as $k=>$v)
            {
                if($num==50)break;
                $num++;
                $name = $v['nickname'];
                if(empty($name)){
                    $rs[$k]['nickname'] = '***';
                    continue;
                }
                $len = mb_strlen($name,'utf-8');
                $tail = str_pad('',$len-1,'*');
                $name = mb_substr($name,0,1,'utf-8');
                $rs[$k]['nickname'] = $name.$tail;
                $new[$k]['nickname'] = $name.$tail;
                $new[$k]['amount'] = $v['amount'];
                $new[$k]['amountExt'] = $v['amountExt'];
                $new[$k]['orderTime'] = $v['orderTime'];
                $new[$k]['orderStatus'] = $v['orderStatus'];
            }
        }
        $this->_view->assign('pager',$pager->toArray());

        return $new;
    }

    protected function _buyTime($waresId,$userId){
        $where = [
            'waresId'=>$waresId,
            'userId'=>$userId,
            'orderStatus'=>\Prj\Consts\OrderStatus::$running,
        ];
        return \Prj\Data\Investment::loopGetRecordsCount($where);
    }
}