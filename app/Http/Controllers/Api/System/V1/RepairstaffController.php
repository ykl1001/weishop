<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\Sellerweb\RepairStaffService;
use YiZan\Services\Sellerweb\SellerService;
use YiZan\Services\SellerCateService;
use Lang, Validator,Log;

/**
 * 机构员工管理
 */
class RepairStaffController extends BaseController
{

    /**
     * 员工列表
     */
    public function lists()
    {

        $data = RepairStaffService::getSellerList
        (
            (int)$this->request('sellerId'),
            $this->request('name'),
            $this->request('mobile'),
           4,
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
		return $this->outputData($data);
    }

    /**
     * 员工搜索
     */
    public function search() {
        $data = RepairStaffService::searchGoods($this->request('name'),(int)$this->request('sellerId')
);
        return $this->outputData($data);
    }

    public function  getrepair(){

        $data = RepairStaffService::getRepair();
        return $this->outputData($data);
    }

    /**
     * 添加员工
     */
    public function create()
    {
        $result = RepairStaffService::saveStaff(
            0,
            (int)$this->request('sellerId'),
            strval($this->request('mobile')),
            trim($this->request('pwd')),
            strval($this->request('name')),
            $this->request('avatar'),
            4,
            $this->request('repairNumber'),
            (int)$this->request('repairTypeId'),
            (int)$this->request('sex'),
            (int)$this->request('status')
        );

        return $this->output($result);
    }
    /**
     * 获取员工
     */
    public function get()
    {
        $staff = RepairStaffService::getSystemSellerStaffById(
            (int)$this->request('id'),
            (int)$this->request('sellerId')

        );
        
        return $this->outputData($staff == false ? [] : $staff->toArray());
    }
    /**
     * 更新员工
     */
    public function update()
    {
        $result = RepairStaffService::saveStaff(
            (int)$this->request('id'),
            (int)$this->request('sellerId'),
            strval($this->request('mobile')),
            trim($this->request('pwd')),
            strval($this->request('name')),
            $this->request('avatar'),
            4,
            $this->request('repairNumber'),
            (int)$this->request('repairTypeId'),
            (int)$this->request('sex'),
            (int)$this->request('status')
        );
        return $this->output($result);
    }
    /**
     * 删除员工
     */
    public function delete()
    {
        $result = RepairStaffService::deleteSeller(
            $this->request('id'),
            (int)$this->request('sellerId')
        );
        
        return $this->output($result);
    }

    /**
     * 更新员工状态
     */
    public function updateStatus() {
        $result = RepairStaffService::updateStaffStatus(
            (int)$this->request('sellerId'),
            (int)$this->request('id'),
            (int)$this->request('status')
        );
        return $this->output($result);
    }



}