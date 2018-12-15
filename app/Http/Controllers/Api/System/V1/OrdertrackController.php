<?php
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\OrderTrackService;
use Lang, Validator;

/**
 * 快递
 */
class OrderTrackController extends BaseController{
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
            $this->request('company'),
            $this->request('type'),
            $this->request('remark')
        );
        return $this->outputData($result);
    }

    /**
     * 查询物流
     */
    public function get(){
        $result = OrderTrackService::getOrder(
            $this->request('sellerId'),
            $this->request('userId'),
            $this->request('id')
        );
        return $this->outputData($result);
    }

    /**
     * 获取地址String
     */
    public function addressStr() {
        $result = OrderTrackService::addressStr(
            $this->request('provinceId'),
            $this->request('cityId'),
            $this->request('areaId')
        );
        return $this->outputData($result);
    }
}