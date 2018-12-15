<?php 
namespace YiZan\Http\Controllers\Api\System;
use YiZan\Services\System\StaffStimeService;

use YiZan\Models\Buyer\Seller;
use YiZan\Services\System\SellerService;
use YiZan\Services\SellerStimeService;
use YiZan\Services\ArticleService;
use Lang, Validator;

/**
 * 服务人员管理
 */
class SellerController extends BaseController 
{ 

    /**
     * 服务人员列表
     */
    public function propertylists() {
        $data = SellerService::getPropertysList(
            $this->request('name'),
            $this->request('districtName'),
            intval($this->request('provinceId')),
            intval($this->request('cityId')),
            intval($this->request('areaId')),
            intval($this->request('isCheck')),
            $this->request('isTotal'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
        return $this->outputData($data);
    } 

    /**
     * 添加服务人员
     */
    public function createproperty()
    {
        $authenticate = $this->request('authenticate');
        $result = SellerService::createProperty(   
            (int)$this->request('id'),
            $this->request('name'),
            $this->request('mobile'), 
            $this->request('pwd'),
            $this->request('contacts'),
            intval($this->request('districtId')), 
            $this->request('serviceTel'),  
            $authenticate['idcardSn'],  
            $authenticate['idcardPositiveImg'],
            $authenticate['idcardNegativeImg'], 
            $authenticate['businessLicenceImg'],
            (int)$this->request('proxyId')
        );
        
        return $this->output($result);
    }

    /**
     * 更新服务人员
     */
    public function updateproperty()
    {   
        $authenticate = $this->request('authenticate');
        $result = SellerService::updateProperty(
            $this->request('id'),
            $this->request('name'),
            $this->request('mobile'), 
            $this->request('pwd'),
            $this->request('contacts'),
            intval($this->request('districtId')), 
            $this->request('serviceTel'),  
            $authenticate['idcardSn'],  
            $authenticate['idcardPositiveImg'],
            $authenticate['idcardNegativeImg'], 
            $authenticate['businessLicenceImg'],
            (int)$this->request('proxyId')
        );
        
        return $this->output($result);
    }

    /**
     * 服务人员列表
     */
    public function lists()
    {
        $data = SellerService::getSystemList(
            $this->request('name'),
            $this->request('mobile'),
            intval($this->request('provinceId')),
            intval($this->request('cityId')),
            intval($this->request('areaId')),
            intval($this->request('status')),
            (int)$this->request('cateId'),
            $this->request('notIds'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		return $this->outputData($data);
    }

    /**
     * 活动已添加的服务人员列表
     */
    public function activityLists() {
        $data = SellerService::activityLists(
            $this->request('ids')
        );
        
        return $this->outputData($data);
    }

    /**
     * 服务人员搜索
     */
    public function search() {
        $data = SellerService::searchSeller($this->request('mobileName'));
        return $this->outputData($data);
    }

    /**
     * 添加服务人员
     */
    public function create()
    {
        $result = SellerService::saveSeller(
            0,
            $this->request('mobile'),
            trim($this->request('pwd')),
            $this->request('name'),
            (int)$this->request('type'),
            $this->request('contacts'),
            $this->request('address'),
            $this->request('logo'),
            $this->request('image'),
            intval($this->request('provinceId')),
            intval($this->request('cityId')),
            intval($this->request('areaId')),
            $this->request('brief'),
            $this->request('mapPos'),
            $this->request('mapPoint'),
            $this->request('idcardSn'),
            $this->request('businessLicenceImg'),
            $this->request('idcardPositiveImg'),
            $this->request('idcardNegativeImg'),
            $this->request('cateIds'),
            (float)$this->request('serviceFee'),
            (float)$this->request('deliveryFee'),
            (int)$this->request('isAvoidFee'),
            (float)$this->request('avoidFee'),
            $this->request('isAuthenticate'),
            $this->request('certificateImg'),
            $this->request('serviceTel'),
            $this->request('deliveryTime'),
            (int)$this->request('deduct'),
            (int)$this->request('isCashOnDelivery'), 
            (int)$this->request('proxyId'),  
            $this->request('sendWay'),
            $this->request('serviceWay'),
            $this->request('reserveDays'),
            $this->request('sendLoop'),
            (array)$this->request('authIcons'),
            (int)$this->request('storeType'),
            (string)$this->request('refundAddress'),
            $this->request('sendType'),
	     $this->request('schemeId')
        );
        
        return $this->output($result);
    }
    /**
     * 获取服务人员
     */
    public function get()
    {
        $seller = SellerService::getSystemSellerById(intval($this->request('id')));
        
        return $this->outputData($seller == false ? [] : $seller->toArray());
    }
    /**
     * 更新服务人员
     */
    public function update()
    {   
        $result = SellerService::saveSeller(
            (int)$this->request('id'),
            $this->request('mobile'),
            trim($this->request('pwd')),
            $this->request('name'),
            (int)$this->request('type'),
            $this->request('contacts'),
            $this->request('address'),
            $this->request('logo'),
            $this->request('image'),
            intval($this->request('provinceId')),
            intval($this->request('cityId')),
            intval($this->request('areaId')),
            $this->request('brief'),
            $this->request('mapPos'),
            $this->request('mapPoint'),
            $this->request('idcardSn'),
            $this->request('businessLicenceImg'),
            $this->request('idcardPositiveImg'),
            $this->request('idcardNegativeImg'),
            $this->request('cateIds'),
            (float)$this->request('serviceFee'),
            (float)$this->request('deliveryFee'),
            (int)$this->request('isAvoidFee'),
            (float)$this->request('avoidFee'),
            $this->request('isAuthenticate'),
            $this->request('certificateImg'),
            $this->request('serviceTel'),
            $this->request('deliveryTime'),
            $this->request('deduct'),
            (int)$this->request('isCashOnDelivery'),
            (int)$this->request('proxyId'),
            $this->request('sendWay'),
            $this->request('serviceWay'),
            $this->request('reserveDays'),
            $this->request('sendLoop'),
            (array)$this->request('authIcons'),
            (int)$this->request('storeType'),
            (string)$this->request('refundAddress'),
            $this->request('sendType'),
            $this->request('schemeId')
        );
        
        return $this->output($result);
    }
    /**
     * 删除服务人员
     */
    public function delete()
    {
        $result = SellerService::deleteSystem(
            $this->request('id')
        );
        
        return $this->output($result);
    }

    /**
     * 所有服务站
     */
    public function allseller() {
        $result = SellerService::all();
        return $this->output($result);
    }

    /**
     * 审核列表
     */
    public function authlists()
    {
        $data = SellerService::getSystemSellerList(
            $this->request('name'),
            intval($this->request('isCheck')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
        return $this->outputData($data);
    }

    /**
     * 审核修改
     */
    public function updatecheck()
    {   
       
        $data = SellerService::updateCheckStatus(
            intval($this->request('id')),
            intval($this->request('isCheck')),
            $this->request('checkVal'),
            (int)$this->request('deduct'),
            $this->request('storeType'),
            $this->request('provinceId'),
            $this->request('cityId'),
            $this->request('areaId')
        );
        
        return $this->output($data);
    }

    public function updatestatus()
    {
        $result = SellerService::updateStatus(intval($this->request('id')), (int)$this->request('status'), $this->request('field'));
        
        return $this->output($result);
    }

    public function bankupdate(){
        $result = SellerService::updateBankInfo((int)$this->request('id'),$this->request('bank'),$this->request('bankNo'),$this->request('mobile'),$this->request('name'),$this->request('verifyCode'));  
        return $this->output($result);
    }

    public function bankdelete(){   
        $result = SellerService::deleteBankInfo((int)$this->request('id'),$this->request('sellerId'));  
        return $this->output($result);
    }

    //审核物业
    public function updatepropertystatus()
    {
        $result = SellerService::updateProStatus(intval($this->request('id')), (int)$this->request('isCheck'), $this->request('checkVal'));
        
        return $this->output($result);
    }

    /**
     * 修改商家余额
     */
    public function updatebalance() {
        $result = SellerService::updatebalance(
            $this->adminId,
            (int)$this->request('sellerId'),
            $this->request('money'),
            (int)$this->request('type'),
            $this->request('remark')
        );
        return $this->output($result);
    }
    /**
     * 修改自营商家信息配置
     */
    public function oneselfsave()
    {
        $result = SellerService::oneselfsave(
            $this->request('id'),
            $this->request('name'),
            $this->request('logo'),
            $this->request('businessScope'),
            (int)$this->request('type',0),
            (float)$this->request('serviceFee'),
            (float)$this->request('deliveryFee'),
            (int)$this->request('isAvoidFee'),
            (float)$this->request('avoidFee'),
            $this->request('sendWay'),
            $this->request('reserveDays'),
            $this->request('sendLoop'),
            $this->request('serviceMode'),
            $this->request('serviceTel')
        );
        return $this->output($result);
    }
    /**
     *  员工服务时间设置
     */
    public function oneselfSellerInsert() {
        $result = StaffStimeService::insert(
            (int)$this->request('sellerId'),
            $this->request('weeks'),
            $this->request('hours')
        );
        return $this->output($result);
    }

    /**
     * 员工服务时间列表
     */
    public function oneselfSellerLists() {
        $list = StaffStimeService::getList((int)$this->request('id'));
        return $this->outputData($list);
    }

    /**
     * 员工服务时间更新
     */
    public function oneselfSellerUpdate() {
        $result = StaffStimeService::update(
            (int)$this->request('sellerId'),
            $this->request('id'),
            $this->request('weeks'),
            $this->request('hours')
        );
        return $this->output($result);
    }

    /**
     * 员工服务时间详情
     */
    public function oneselfSellerEdit() {
        $data = StaffStimeService::detail(
            (int)$this->request('sellerId'),
            $this->request('id')
        );
        return $this->outputData($data);
    }

    /**
     * 员工服务时间删除
     */
    public function oneselfSellerDelete() {
        $result = StaffStimeService::delete(
            (int)$this->request('sellerId'),
            $this->request('id')
        );
        return $this->output($result);
    }


    /**
     * 保存公告
     */
    public function oneselfNoticeSave(){
        $result = ArticleService::save(
            intval($this->request('id')),
            $this->request('title'),
            $this->request('content'),
            intval($this->request('sort')),
            max((int)$this->request('status'),1)
        );
        return $this->output($result);
    }

    /**
     * 批量添加分销
     */
    public function morefx() {
        $result = SellerService::morefx(
            $this->request('ids'),
            $this->request('schemeId')
        );
        return $this->output($result);
    }
}

