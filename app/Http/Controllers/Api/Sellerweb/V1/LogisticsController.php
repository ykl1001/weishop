<?php
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\LogisticsService;
use Lang, Validator;
use DB;
/**
 *  物流
 */
class LogisticsController extends BaseController
{
    /**
     * 退款列表
     */
    public function refund() {
        $data =LogisticsService::refund(
            $this->userId,
            $this->request('orderId'),
            $this->request('refundType'),
            $this->request('content'),
            $this->request('refundExplain'),
            $this->request('images')
        );
        
		return $this->output($data);
    }
    /**
     * 退款
     */
    public function refundById() {
        $data =LogisticsService::refundStaffById(
            $this->sellerId,
            $this->request('id')
        );

        return $this->outputData($data);
    }


    /**
     * 更改物流状态
     */
    public function refundsave() {
        $data =LogisticsService::refundsave(
            $this->sellerId,
            $this->request('id'),
            $this->request('orderId'),
            $this->request('status')
        );

        return $this->output($data);
    }

    /**
     * 更改物流状态
     */
    public function refunddispose() {
        $data =LogisticsService::refunddispose(
            $this->sellerId,
            $this->request('id'),
            $this->request('orderId'),
            $this->request('status'),
            $this->request('content'),
            $this->request('refundExplain'),
            $this->request('images')
        );

        return $this->output($data);
    }
}