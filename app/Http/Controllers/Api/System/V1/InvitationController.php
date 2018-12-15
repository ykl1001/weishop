<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\InvitationService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 分享返现
 */
class InvitationController extends BaseController 
{
    /**
     * 列表
     */
    public function lists()
    {
        $data = InvitationService::getList
        (
            $this->request('page'),
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($data);
    }

    /**
     * 获取分享返现
     */
    public function get()
    {
        $result = InvitationService::getById();
        
        return $this->outputData($result);
    }

    /**
     * 更新分享返现
     */
    public function save()
    {
        $result = InvitationService::save
        (
            (int)$this->request('id'),
            (int)$this->request('userStatus'),
            (float)$this->request('userPercent'),
            (float)$this->request('userPercentSecond'),
            (float)$this->request('userPercentThird'),
            (int)$this->request('sellerStatus'),
            (float)$this->request('sellerPercent'),
            (float)$this->request('sellerPercentSecond'),
            (float)$this->request('sellerPercentThird'),
            (double)$this->request('fullMoney'),
            (string)$this->request('shareTitle'),
            (string)$this->request('shareContent'),
            (string)$this->request('shareLogo'),
            (string)$this->request('inviteLogo'),
            (string)$this->request('shareExplain'),
            (string)$this->request('shareDescribe'),
            (string)$this->request('pointsNoExplain'),
            (float)$this->request('isAllUserPrimary'),
            (float)$this->request('isAllUserPercent'),
            (float)$this->request('isAllUserPercentSecond'),
            (float)$this->request('isAllUserPercentThird'),
            $this->request('purchaseAgreement'),
            $this->request('privilegeDetails'),
            (double)$this->request('protocolFee')
        );
        
        return $this->output($result);
    }

    /**
     * 获取分享返现订单
     */
    public function orderlist() { 
        $result = InvitationService::orderlist
        (
            (string)$this->request('sn'),
            (string)$this->request('buyer'),
            (string)$this->request('invitor'),
            (string)$this->request('status'),
            (int)$this->request('orderType') ? (int)$this->request('orderType') : 2,
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        
        return $this->outputData($result);
    }

    /**
     * 获取邀请用户返现统计
     */
    public function userlist() {
        $result = InvitationService::userlist
        (
            (string)$this->request('userName'),
            (string)$this->request('invitationName'),
            (int)$this->request('type') ? (int)$this->request('type') : 1,
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        
        return $this->outputData($result);
    }

    /**
     * 被邀请的列表
     */
    public function invitationlist() {
        $result = InvitationService::invitationlist
        (
            (string)$this->request('invitationId'),
            (string)$this->request('sn'),
            (string)$this->request('userName'),
            (int)$this->request('status'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        
        return $this->outputData($result);
    }

    /**
     * 返现统计
     */
    public function statistics() {
        $result = InvitationService::statistics
        (
            (int)$this->request('year'),
            (int)$this->request('month') 
        );
        
        return $this->outputData($result);
    }

    /**
     * 返现统计
     */
    public function moneylog() {
        $result = InvitationService::moneyLog
        (
            $this->request('paySn'),
            $this->request('beginTime'),
            $this->request('endTime'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );

        return $this->outputData($result);
    }
}