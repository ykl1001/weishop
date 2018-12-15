<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\MenuService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 活动管理
 */
class MenuController extends BaseController 
{
    /**
     * 活动列表
     */
    public function lists()
    {
        $data = MenuService::getList
        (
            $this->request('type',"common"),
            $this->request('page'),
            max((int)$this->request('pageSize'), 20)
        );
		return $this->outputData($data);
    }

    /**
     * 获取活动
     */
    public function get()
    {
        $Menu = MenuService::getById(intval($this->request('id')));
        
        return $this->outputData($Menu == false ? [] : $Menu->toArray());
    }

    /**
     * 更新活动
     */
    public function update()
    {
        $result = MenuService::update
        (
            $this->request('id'),
            $this->request('name'),
            $this->request('cityId'),
            $this->request('menuIcon'),
            $this->request('type'),
            $this->request('arg'),
            intval($this->request('sort')),
            intval($this->request('status')),
            $this->request('platformType','common')
        );
        
        return $this->output($result);
    }

    /**
     * 删除活动
     */
    public function delete()
    {
        $result = MenuService::delete(
            $this->request('id')
        );
        return $this->output($result);
    }


    public function updateStatus(){
        $result = MenuService::updateStatus(
            (int)$this->request('id'),
            abs((int)$this->request('status'))
        );
        return $this->output($result);
    }

}