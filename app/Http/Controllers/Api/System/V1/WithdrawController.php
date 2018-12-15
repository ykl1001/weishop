<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\SellerWithdrawMoneyService;
use YiZan\Services\System\SellerStaffWithdrawMoneyService;


class WithdrawController extends BaseController {
    /**
     * 提现列表
     */
    public function lists() {
        $data = SellerWithdrawMoneyService::lists(
                $this->request('name'),
                (int)$this->request('status'),
                (int)$this->request('beginTime'),
                (int)$this->request('endTime'),
                (int)$this->request('type',0),
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 10)
            );
        return $this->outputData($data);
    }

    /**
     * 获取卖家提现消息
     */
    public function getwithdrawmessage() {
        $data = SellerWithdrawMoneyService::getwithdrawmessage();
        return $this->output($data);
    }

    /**
     * 提现列表
     */
    public function stafflists() {
        $data = SellerStaffWithdrawMoneyService::lists(
            $this->request('name'),
            (int)$this->request('status'),
            (int)$this->request('beginTime'),
            (int)$this->request('endTime'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 10)
        );
        return $this->outputData($data);
    }

    /**
     * 获取卖家提现消息
     */
    public function staffgetwithdrawmessage() {
        $data = SellerStaffWithdrawMoneyService::getwithdrawmessage();
        return $this->output($data);
    }

    /**
     * 提现处理社区
     */
    public function staffdispose() {
        $result = SellerStaffWithdrawMoneyService::dispose(
            $this->adminId,
            (int)$this->request('id'),
            $this->request('content'),
            (int)$this->request('status')
        );
        return $this->output($result);
    }
}