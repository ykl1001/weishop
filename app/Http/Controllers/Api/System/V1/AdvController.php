<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\AdvService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 广告管理
 */
class AdvController extends BaseController 
{
    /**
     * 广告列表
     */
    public function lists()
    {
        $data = AdvService::getList
        (
            $this->request('code'),
            $this->request('page'),
            max((int)$this->request('pageSize'), 20)
        );
        
		return $this->outputData($data);
    }
    /**
     * 添加广告
     */
    public function create()
    {
        $result = AdvService::create
        (
            intval($this->request('cityId')),
            intval($this->request('positionId')),
            $this->request('name'),
            $this->request('image'),
            $this->request('bgColor'),
            $this->request('type'),
            $this->request('arg'),
            intval($this->request('sort')),
            intval($this->request('status')),
            (int)$this->request('sellerCateId'),
            $this->request('isAdv'),
            (int)$this->request('mouldId'),
            $this->request('upData')
        );
        
        return $this->output($result);
    }
    /**
     * 获取广告
     */
    public function get()
    {
        $adv = AdvService::getById(intval($this->request('id')));
        
        return $this->outputData($adv == false ? [] : $adv->toArray());
    }    
    /**
     * 更新广告
     */
    public function update()
    {
        $result = AdvService::update
        (
            intval($this->request('id')),
            intval($this->request('cityId')),
            intval($this->request('positionId')),
            $this->request('name'),
            $this->request('image'),
            $this->request('bgColor'),
            $this->request('type'),
            $this->request('arg'),
            intval($this->request('sort')),
            intval($this->request('status')),
            (int)$this->request('sellerCateId'),
            $this->request('isAdv'),
            (int)$this->request('mouldId'),
            $this->request('upData')
        );
        
        return $this->output($result);
    }
    /**
     * 设置状态
     */
    public function setstatus()
    {
        $result = AdvService::delete(intval($this->request('id')), intval($this->request('status')));
        
        return $this->output($result);
    }
    /**
     * 删除广告
     */
    public function delete()
    {
        $result = AdvService::delete($this->request('id'));
        return $this->output($result);
    }
    /**
     * 广告列表
     */
    public function OneselfAdvlists()
    {
        $data = AdvService::OneselfAdvlists
        (
            $this->request('code'),
            $this->request('page'),
            max((int)$this->request('pageSize'), 20)
        );

        return $this->outputData($data);
    }
}