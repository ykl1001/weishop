<?php 
namespace YiZan\Http\Controllers\Api\Proxy\Order;

use YiZan\Services\Proxy\OrderRateService;
use YiZan\Http\Controllers\Api\Proxy\BaseController;
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
            $this->proxy,
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
        $data = OrderRateService::getOrderRateById($this->request('id'));
        
        return $this->outputData($data);
    }

    /**
     * 保存编辑的评价
     */
    public function save(){
        $result = OrderRateService::saveOrderRate (
            $this->request('id'),
            $this->request('star'),
            $this->request('content'),
            $this->request('images'),
            $this->request('reply')
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
        $result = OrderRateService::deleteSystem(intval($this->request('id')));
        
        return $this->output($result);
    }
}