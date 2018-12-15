<?php 
namespace YiZan\Http\Controllers\Api\Proxy;

use YiZan\Services\Proxy\SellerStaffService;
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
        $data = SellerStaffService::getSystemList
        (
            $this->proxy,
            $this->request('sellerId'),
            $this->request('mobile'),
            $this->request('name'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
        
		return $this->outputData($data);
    }

    /**
     * 员工搜索
     */
    public function search() {
        $data = SellerStaffService::searchGoods($this->request('name'), (int)$this->request('sellerId'));
        return $this->outputData($data);
    }

     /**
     * 员工推送搜索
     */
    public function searchs() {
        $data = SellerStaffService::searchUser($this->request('name'));
        return $this->outputData($data);
    }

    /**
     * 员工
     */
    public function goodsStaff()
    {
        $data = SellerStaffService::getGoodsStaff((int)$this->request('goodsId'));
        
        return $this->outputData($data);
    } 

    /**
     * 获取员工
     */
    public function get()
    {
        $staff = SellerStaffService::getSystemSellerStaffById((int)$this->request('id'));
        
        return $this->outputData($staff == false ? [] : $staff->toArray());
    } 

   
}