<?php 
namespace YiZan\Http\Controllers\Api\Proxy;

use YiZan\Services\Proxy\OrderService;
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
            $this->proxy,
           (int)$this->request('orderType'),
            $this->request('sn'),
            $this->request('mobile'),
            (int)$this->request('beginTime'),
            (int)$this->request('endTime'),
            $this->request('payStatus'),
            (int)$this->request('status'),
            trim($this->request('sellerName')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		return $this->outputData($data);
    }
    /**
     * 获取订单
     */
    public function get()
    {
        $order = OrderService::getSystemOrderById($this->proxy,(int)$this->request('id'));
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
        die;//暂先隐藏此功能
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
}