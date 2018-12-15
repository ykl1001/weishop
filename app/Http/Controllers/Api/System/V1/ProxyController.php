<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\ProxyService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 代理
 */
class ProxyController extends BaseController 
{
    /**
     * 代理列表
     */
    public function lists(){
        $data = ProxyService::getLists(
                $this->request('name'),
                (int)$this->request('provinceId'),
                (int)$this->request('cityId'),
                (int)$this->request('areaId'),
                max((int)$this->request('page'), 1), 
                (int)$this->request('pageSize', 20), 
                (int)$this->request('level'), 
                (int)$this->request('isAll')
            );
        return $this->outputData($data);
    }

    /**
     * 代理审核列表
     */
    public function authlists(){
        $data = ProxyService::getAuthLists(
                $this->request('name'),
                (int)$this->request('provinceId'),
                (int)$this->request('cityId'),
                (int)$this->request('areaId'),
                (int)$this->request('isCheck'),
                max((int)$this->request('page'), 1), 
                (int)$this->request('pageSize', 20), 
                (int)$this->request('level'), 
                (int)$this->request('isAll')
            );
        return $this->outputData($data);
    }

    /**
     * 代理详情
     */
    public function detail(){
        $data = ProxyService::getById($this->request('id'));
        return $this->outputData($data);
    }

    /**
     * 添加/修改代理
     */
    public function save(){
        $result = ProxyService::save(
                $this->request('id'),
                $this->request('name'),
                $this->request('pwd'),
                $this->request('realName'),
                $this->request('mobile'),
                $this->request('pid'),
                $this->request('level'),
                $this->request('provinceId'),
                $this->request('cityId'),
                $this->request('areaId'),
                $this->request('thirdArea'),
                $this->request('status'),
                $this->request('checkVal'),
                $this->request('isCheck')
            );
        return $this->output($result);
    }

    /**
     * 审核代理
     */
    public function audit(){
        $result = ProxyService::audit(
                $this->request('id'), 
                $this->request('checkVal'),
                $this->request('isCheck')
            );
        return $this->output($result);
    }

    /**
     * 删除代理
     */
    public function delete(){
        $result = ProxyService::delete(
            $this->request('id')
        );
        return $this->output($result);
    }

}