<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb\Order;

use YiZan\Services\Sellerweb\OrderRateService;
use YiZan\Http\Controllers\Api\Sellerweb\BaseController;
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
            $this->sellerId,
            $this->request('userMobile'),
            $this->request('goodsName'),
            $this->request('sellerMobile'),
            $this->request('orderSn'),
            $this->request('beginTime'),
            $this->request('endTime'),
            $this->request('result'),
            intval($this->request('replyStatus')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		return $this->outputData($data);
    } 
    /**
     * 根据ID获取评价
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function get(){
        $data = OrderRateService::getReply(
            $this->sellerId,
            $this->request('id')
        );
        return $this->outputData($data);
    }
    /**
     * 评价回复
     */
    public function reply()
    {
        $result = OrderRateService::replySystem(
            $this->sellerId,
            intval($this->request('id')),
            $this->request('content')
        );
        
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

    /**
     * [orderlists 全国店评价列表]
     * @return [type] [description]
     */
    public function orderlists() {
        $result = OrderRateService::orderlists(
            $this->sellerId,
            $this->request('star'),
            $this->request('sn'),
            $this->request('beginTime'),
            $this->request('endTime'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
        return $this->outputData($result);
    }

    /**
     * 全国店订单评价详情
     */
    public function alldetail() {
        $result = OrderRateService::alldetail(
            $this->sellerId,
            $this->request('orderId')
        );
        
        return $this->outputData($result);
    }

    /**
     * 回复全国店评价
     */
    public function allreply() {
        $result = OrderRateService::allreply(
            $this->sellerId,
            $this->request('data')
        );
        
        return $this->output($result);
    }

}