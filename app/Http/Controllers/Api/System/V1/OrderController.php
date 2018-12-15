<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\OrderService;
use YiZan\Services\LogisticsService;
use Lang, Validator;

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
        $data = OrderService::getSystemList
        (
           (int)$this->request('orderType'),
            $this->request('sn'),
            $this->request('mobile'),
            (int)$this->request('beginTime'),
            (int)$this->request('endTime'),
            $this->request('payStatus'),
            (int)$this->request('status'),
            trim($this->request('sellerName')),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20),
            $this->request('isSeller'),
            (int)$this->request('isIntegralGoods'),
            (int)$this->request('isAll'),
            (int)$this->request('provinceId'),
            (int)$this->request('cityId'),
            (int)$this->request('areaId'),
            (int)$this->request('payTypeStatus'),
            (int)$this->request('sendFee')
        );
        
		return $this->outputData($data);
    }
    /**
     * 获取订单
     */
    public function get()
    {
        $order = OrderService::getSystemOrderById((int)$this->request('id'));
        return $this->outputData($order == false ? [] : $order->toArray());
    }


    /**
     * 更新订单
     */
    public function update()
    {
        $result = OrderService::updateSystemOrder(intval($this->request('id')), intval($this->request('status')), $this->request('refuseContent'));
        
        return $this->output($result);
    }
    /**
     * 删除订单
     */
    public function delete()
    {
        //die;//暂先隐藏此功能
        $result = OrderService::deleteSystemOrder(intval($this->request('id')));
        
        return $this->output($result);
    }

    /**
     * 更改日期
     */
    public function updatedate() {
        $result = OrderService::updateDate(
            (int)$this->request('id'),
            $this->request('beginTime'),
            $this->request('endTime')
        );
        return $this->output($result);
    }

    /**
     * 指派人员
     */
    public function updatestaff() {
        $result = OrderService::updateStaff(
            (array)$this->request('orderIds'),
            (int)$this->request('staffId')
        );
        return $this->output($result);
    }
    
    /**
     * 服务站指派人员
     */
    public function designate() {
        $result = OrderService::designate(
            $this->request('orderId'),
            (int)$this->request('staffId'),
            $this->request('serviceContent'),
            $this->request('money')
        );
        return $this->output($result);
    }
    /**
     * 随机指派人员
     */
    public function ranupdate() {
        $result = OrderService::ranUpdate(
            (int)$this->request('orderId'),
            $this->request('serviceContent'),
              $this->request('money')
        );
        return $this->output($result);
    }

    /**
     * 随机指派人员
     */
    public function ranupdatestaff() {
        $result = OrderService::ranUpdateStaff(
            (int)$this->request('id'),
            (array)$this->request('orderIds')
        );
        return $this->output($result);
    }


    /**
     * 退款处理
     */
    public function refund() {
        $result = OrderService::refund(
            $this->adminId,
            (int)$this->request('id'),
            (int)$this->request('status'),
            trim($this->request('remark'))
        );
        return $this->output($result);
    }

    public function refundById() {
        $data =LogisticsService::refundById(
            (int)$this->request('userId'),
            $this->request('orderId')
        );

        return $this->outputData($data);
    }

    /**
     * 退款日志
     */
    public function refundView() {
        $result = OrderService::refundDetail(
            (int)$this->request('userId'),
            (int)$this->request('orderId')
        );
        return $this->outputData($result);
    }

    /**
     * 统计
     */
    public function total() {
        $result = OrderService::total(
            (int)$this->request('orderType'),
            (int)$this->request('status'),
            (int)$this->request('isAll')
        );
        return $this->outputData($result);
    }

    /**
     * 更改订单配送员
     */
    public function changestaffsystem(){
        $data = OrderService::changeStaffSystem(
            $this->request('id'),
            $this->request('changesellerStaffId')
        );
        return $this->output($data);
    }
    /**
     * 定时任务
     */
    public function endOrder() {
        $result = OrderService::endOrder(
            1
        );
        return $this->outputData($result);
    }
}