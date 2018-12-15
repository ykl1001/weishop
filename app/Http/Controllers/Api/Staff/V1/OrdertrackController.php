<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\OrderTrackService;
use DB;
/**
 * 快递
 */
class OrdertrackController extends BaseController {

    /**
     * 发送快递
     */
    public function postlogistics(){
        $form = $this->request('from') ? $this->request('from') : $this->seller->province->name.$this->seller->city->name.$this->seller->area->name;
        $result = OrderTrackService::get(
            $this->request('keycode'),
            $this->request('number'),
            $form,
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
            $this->sellerId,
            0,
            $this->request('id')
        );
        return $this->outputData($result);
    }

}