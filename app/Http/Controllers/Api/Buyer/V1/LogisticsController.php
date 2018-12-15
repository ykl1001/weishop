<?php
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\LogisticsService;
use Lang, Validator;
use DB;
/**
 *  积分商品
 */
class LogisticsController extends BaseController
{
    /**
     * 积分商品列表
     */
    public function refund() {
        $data =LogisticsService::refund(
            $this->userId,
            $this->request('orderId'),
            $this->request('refundType'),
            $this->request('content'),
            $this->request('refundExplain'),
            $this->request('images'),
            $this->request('type')
        );
        
		return $this->output($data);
    }
    public function refundDel() {
        $data =LogisticsService::refundDel(
            $this->userId,
            $this->request('id')
        );

        return $this->output($data);
    }

    /**
     * 积分商品列表
     */
    public function refundById() {
        $data =LogisticsService::refundById(
            $this->userId,
            $this->request('orderId')
        );

        return $this->outputData($data);
    }

    /**
     * 更改物流状态
     */
    public function userrefunddispose() {
        $data =LogisticsService::userrefunddispose(
            $this->userId,
            $this->request('id'),
            $this->request('orderId'),
            $this->request('status'),
            $this->request('company'),
            $this->request('keycode'),
            $this->request('number'),
            $this->request('images')
        );

        return $this->output($data);
    }

}