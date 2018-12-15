<?php
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\OrdertrackService;
use Lang, Validator;

/**
 * 活动管理
 */
class ActivityController extends BaseController{
    /**
     * 发送快递
     */
    public function postlogistics(){
        $result = OrderTrackService::get(
            $this->request('keycode'),
            $this->request('number'),
            $this->request('from'),
            $this->request('to'),
            $this->request('key'),
            $this->request('orderId'),
            $this->request('userId'),
            $this->request('sellerId'),
            $this->request('company')
        );
        return $this->outputData($result);
    }

    /**
     * 查询物流
     */
    public function get(){
        $result = OrderTrackService::getOrder(
            $this->sellerId,
            0,
            $this->request('id')
        );
        return $this->outputData($result);
    }
}