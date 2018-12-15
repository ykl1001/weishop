<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\OrderService;
use YiZan\Services\Staff\SellerService;
use Lang, Validator, View;

/**
 * 订单
 */
class OrderController extends BaseController 
{
    /**
     * 订单列表
     */
    public function lists()
    {

        $data = OrderService::getList
        (
            $this->sellerId,
            $this->staffId,
            (int)$this->request('status'),
            $this->request('date'),
            trim($this->request('keywords')),
            max((int)$this->request('page'), 1)
        );
        
		return $this->outputData($data);
    }
    /**
     * 获取订单
     */
    public function detail()
    {
        $order = OrderService::getOrderById(
            $this->sellerId,
            $this->staffId,
            (int)$this->request('id')
        );
        return $this->outputData($order);
    }
    /**
     * 更新订单(old)
     */
    /*public function status()
    {
        $result = OrderService::updateStaffOrder(intval($this->request('id')), $this->staffId, intval($this->request('status')));
        
        return $this->output($result);
    }*/

    /**
     * 订单状态改变
     */
    public function status() {
        $result = OrderService::updateOrder(
            $this->sellerId,
            $this->staffId,
            (int)$this->request('id'),
            (int)$this->request('status'),
            trim($this->request('remark')),
            $this->request('express')
        );
        return $this->output($result);
    }
    
    /**
     * 完成订单
     */
    public function complete() {
        $data = OrderService::completeOrder(
            $this->staffId,
            (int)$this->request('id'),
            $this->request('code')
        );
        return $this->output($data);
    }

    /**
     * 日程列表
     */
    public function schedule() {
        $data = OrderService::getSchedule(
            $this->staffId,
            max((int)$this->request('type'),1),
            max((int)$this->request('page'),1)
        );
        return $this->outputData($data);
    }


    /**
     * 订单分配人员
     */
    public function designate() {
        $result = OrderService::designate(
            $this->sellerId,
            (int)$this->request('id'),
            (int)$this->request('staffId')
        );
        return $this->output($result);
    }

    /**
     * 服务、配送人员列表
     */
    public function stafflist(){
        $result = SellerService::getStaffLists(
            $this->sellerId, 
            (int)$this->request('type')
        );
        return $this->outputData($result);
    }

    /**
     * 经营统计
     */
    public function statistics(){
        $days = max((int)$this->request('days'),1);
        $data = OrderService::businessStat(
            $this->sellerId,
            $days
        );
        return $this->outputData($data);
        return View::make('api.statistics.index');
    }

    /**
     * 获取消费码 验证订单
     */
    public function checkcode() {
        $result = OrderService::checkcode(
            $this->sellerId, 
            strval($this->request('code'))
        );
        return $this->output($result);
    }
    /**
     * 退款详情
     */
    public function refundDetail() {
        $result = OrderService::refundDetail(
            (int)$this->request('userId'),
           (int)$this->request('orderId')
        );
        return $this->outputData($result);
    }
    /**
     * 获取订单
     */
    public function detailnewstaffid()
    {
        $order = OrderService::getOrderById(
            0,
            $this->request('newStaffId'),
            (int)$this->request('id')
        );
        return $this->outputData($order);
    }

}