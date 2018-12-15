<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\RepairService;
use YiZan\Services\Staff\SellerService;
use Lang, Validator, View;

/**
 * 订单
 */
class RepairController extends BaseController 
{
    /**
     * 订单列表
     */
    public function lists()
    {
        $data = RepairService::getList
        (
            $this->sellerId,
            $this->staffId,
            (int)$this->request('status'),
            $this->request('date'),
            trim($this->request('keywords')),
            max((int)$this->request('page'), 1),
            $this->request('new')
        );
        
		return $this->output($data);
    }
    /**
     * 获取订单
     */
    public function detail()
    {
        $order = RepairService::getRepairById(
            $this->sellerId,
            $this->staffId,
            (int)$this->request('id')
        );
        return $this->outputData($order);
    }

    /**
     * 订单状态改变
     */
    public function status() {
        $result = RepairService::updateRepair(
            $this->sellerId,
            $this->staffId,
            (int)$this->request('id'),
            (int)$this->request('status')
        );
        return $this->output($result);
    }
    
    /**
     * 完成订单
     */
    public function complete() {
        $data = RepairService::completeRepair(
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
        $data = RepairService::getSchedule(
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
        $result = RepairService::designate(
            $this->sellerId,
            (int)$this->request('id'),
            (int)$this->request('staffId')
        );
        return $this->output($result);
    }


}