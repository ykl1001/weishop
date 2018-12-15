<?php 
namespace YiZan\Http\Controllers\Api\buyer;

use YiZan\Services\OrderTrackService;
use DB;
/**
 * 服务人员
 */
class OrdertrackController extends BaseController {

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
            0,
            $this->userId,
            $this->request('id')
        );
        return $this->outputData($result);
    }

}