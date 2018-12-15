<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\PropertyOrderService;
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
            $this->user,
            $this->request('propertyFeeId'),
            $this->request('payment')
        );
        
        return $this->output($data);
    }  

    /**
     * 获取支付信息
     */
    public function payment(){
        $data = PropertyOrderService::getPaymentInfo(
            $this->user,
            $this->request('orderId'),
            $this->request('extend') 
        );
        
        return $this->output($data);
    }

    /**
     * 取消订单
     */
    public function cancel(){
        $data = PropertyOrderService::cancelOrder(
            $this->user,
            $this->request('id') 
        );
        
        return $this->output($data);
    }

}

