<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\InvitationService;
use YiZan\Services\WeixinService;

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
        $result = InvitationService::getById(intval($this->request('id')));
        
        return $this->outputData($result);
    }

    /**
     * 二维码
     */
    public function cancode()
    {
        $data = InvitationService::cancode
        (
            $this->request('type'),
            $this->request('id')
        );
        return $this->output($data);
    }


    /**
     * 获取微信JS信息配置
     */
    public function getweixin() {
        $payment = WeixinService::getweixin($this->request('url'));

        return $this->outputData($payment);
    }


    public function userc() {
        $payment = InvitationService::userc(
            $this->request('type'),
            $this->request('id')
        );
        return $this->output($payment);
    }

    public function getuserlists() {
        $data = InvitationService::getUserLists(
            $this->userId,  
            'user',
            $this->request('userId'),
            $this->request('level'),
            $this->request('page') ? (int)$this->request('page') : 1,
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($data);
    }

    public function getrecords() {
        $data = InvitationService::getRecords( 
            $this->userId, 
            'user',
            $this->request('page') ? (int)$this->request('page') : 1,
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($data);
    }

}