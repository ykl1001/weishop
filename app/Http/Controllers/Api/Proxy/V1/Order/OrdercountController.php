<?php 
namespace YiZan\Http\Controllers\Api\Proxy\Order;

use YiZan\Services\Proxy\OrderCountService;
use YiZan\Http\Controllers\Api\Proxy\BaseController;

/**
 * 订单统计
 */
class OrdercountController extends BaseController 
{
    /**
     * 概况
     */
    public function total()
    {
        $data = OrderCountService::total($this->proxy,(int)$this->request('type'));
		return $this->outputData($data);
    }
    

    /**
     * [orderNum 订单数量统计]
     */
    public function ordernum() {
        $data = OrderCountService::getOrderNumTotal
        (
            $this->request('beginTime'),
            $this->request('endTime')
        ); 
        return $this->output($data);
    }
}