<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\SellerStaffService;
use YiZan\Services\Sellerweb\SellerService;
use YiZan\Services\SellerCateService;
use Lang, Validator;

/**
 * 机构员工管理
 */
class SellerstaffController extends BaseController
{
    /**
     * 员工列表
     */
    public function lists()
    {
        $data = SellerStaffService::getSellerList
        (
            $this->sellerId,
            $this->request('name'),
            $this->request('mobile'),
            $this->request('type'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
		return $this->outputData($data);
    }

    /**
     * 员工搜索
     */
    public function search() {
        $data = SellerStaffService::searchGoods($this->request('name'), $this->sellerId);
        return $this->outputData($data);
    }

    /**
     * 添加员工
     */
    public function create()
    {
        $result = SellerStaffService::saveStaff(
            0,
            $this->sellerId,
            strval($this->request('mobile')),
            trim($this->request('pwd')),
            strval($this->request('name')),
            $this->request('avatar'),
            (int)$this->request('type'),
            strval($this->request('address')),
            strval($this->request('mapPoint')),
            (int)$this->request('provinceId'),
            (int)$this->request('cityId'),
            (int)$this->request('areaId'),
            (int)$this->request('sex'),
            $this->request('authentication'),
            $this->request('authenticateImg'),
            $this->request('mapPos'),
            (int)$this->request('status')
        );

        return $this->output($result);
    }
    /**
     * 获取员工
     */
    public function get()
    {
        $staff = SellerStaffService::getSystemSellerStaffById(
            (int)$this->request('id'),
            $this->sellerId
        );
        
        return $this->outputData($staff == false ? [] : $staff->toArray());
    }
    /**
     * 更新员工
     */
    public function update()
    {
        $result = SellerStaffService::saveStaff(
            (int)$this->request('id'),
            $this->sellerId,
            strval($this->request('mobile')),
            trim($this->request('pwd')),
            strval($this->request('name')),
            $this->request('avatar'),
            $this->request('type'),
            strval($this->request('address')),
            strval($this->request('mapPoint')),
            (int)$this->request('provinceId'),
            (int)$this->request('cityId'),
            (int)$this->request('areaId'),
            (int)$this->request('sex'),
            $this->request('authentication'),
            $this->request('authenticateImg'),
            $this->request('mapPos'),
            (int)$this->request('status')
        );
        return $this->output($result);
    }
    /**
     * 删除员工
     */
    public function delete()
    {
        $result = SellerStaffService::deleteSeller(
            $this->request('id'),
            $this->sellerId
        );
        
        return $this->output($result);
    }

    /**
     * 更新员工状态
     */
    public function updateStatus() {
        $result = SellerStaffService::updateStaffStatus(
            $this->sellerId,
            (int)$this->request('id'),
            (int)$this->request('status')
        );
        return $this->output($result);
    }

    public function cateall() {
        $list = SellerCateService::getCatesAll();
        return $this->outputData($list);
    }

    
    public function getstaffschedule() {
        $list = SellerStaffService::getStaffSchedule((int)$this->request('id'), $this->sellerId);
        return $this->outputData($list);
    }
    /**
     * 保存运费模版
     */
    public function saveFreight() {
        $result = SellerService::saveFreight(
            $this->sellerId,
            (array)$this->request('data')
        );
        return $this->output($result);
    }
    /**
     * 获取运费模版列表
     */
    public function freightList() {
        $result = SellerService::freightList(
            $this->sellerId,
            $this->request('isDefault')
        );
        return $this->outputData($result);
    }
}