<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\PropertyOrderService;
use Lang, Validator;

/**
 * 物业订单
 */
class PropertyorderController extends BaseController 
{
    /**
     * 创建物业订单
     */
    public function create()
    { 
        $data = PropertyOrderService::createOrder(
            $this->seller,
            $this->request('propertyFeeId'),
            $this->request('puserId')
        );
        
        return $this->output($data);
    }  

    /**
     * 物业订单明细
     */
    public function get()
    { 
        $data = PropertyOrderService::getById(
            $this->sellerId,
            $this->request('id') 
        );
        
        return $this->outputData($data);
    }  

    /**
     * 物业订单列表
     */
    public function lists()
    {
        $data = PropertyOrderService::getLists(
            (int)$this->request('sellerId'),
            $this->request('name'),
            $this->request('buildId'),
            $this->request('roomId'),
            $this->request('sn'),
            $this->request('beginTime'),
            $this->request('endTime'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
        return $this->outputData($data);
    } 
}

