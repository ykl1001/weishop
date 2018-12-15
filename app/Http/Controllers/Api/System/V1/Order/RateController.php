<?php 
namespace YiZan\Http\Controllers\Api\System\Order;

use YiZan\Services\System\OrderRateService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 评价管理
 */
class RateController extends BaseController 
{
    /**
     * 评价列表
     */
    public function lists()
    {
        $data = OrderRateService::getSystemList (
            $this->request('userMobile'),
            $this->request('goodsName'),
            $this->request('sellerMobile'),
            $this->request('staffMobile'),
            $this->request('orderSn'),
            intval($this->request('beginTime')),
            intval($this->request('endTime')),
            $this->request('result'),
            intval($this->request('replyStatus')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		return $this->outputData($data);
    }

    /**
     * 获取评价
     */
    public function get(){
        $data = OrderRateService::getOrderRateById($this->request('orderId'));
        
        return $this->outputData($data);
    }

    /**
     * 保存编辑的评价
     */
    public function save(){
        $result = OrderRateService::saveOrderRate (
            $this->request('data')
        );
        
        return $this->output($result);

    }

    /**
     * 评价回复
     */
    public function reply()
    {
        $result = OrderRateService::replySystem(intval($this->request('id')), $this->request('content'));
        
        return $this->output($result);
    }
    /**
     * 评价删除
     */
    public function delete()
    {
        $result = OrderRateService::deleteSystem(
            $this->request('id')
        );
        
        return $this->output($result);
    }
}