<?php 
namespace YiZan\Http\Controllers\Api\Proxy;

use YiZan\Services\Proxy\ProxyService;
use YiZan\Http\Controllers\Api\Proxy\BaseController;
use Lang, Validator;

/**
 * 代理
 */
class ProxyController extends BaseController 
{

    /**
     * 修改密码
     */
    public function repwd() {
        $result = ProxyService::updateProxyPassword(
                $this->proxyId, 
                $this->request('oldPwd'), 
                $this->request('newPwd')
            );
        return $this->output($result);
    }

    /** 
     * 代理列表
     */
    public function lists(){
        $result = ProxyService::getLists(
                $this->proxy,
                $this->request('id'),
                $this->request('name'),
                $this->request('mobile'),
                (int)$this->request('provinceId'),
                (int)$this->request('cityId'),
                (int)$this->request('areaId'),
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 20)
            );
        return $this->outputData($result);
    }

    /**
     * 代理审核列表
     */
    public function authlists(){
        $data = ProxyService::getAuthLists(
                $this->proxy, 
                $this->request('name'),
                $this->request('mobile'),
                (int)$this->request('provinceId'),
                (int)$this->request('cityId'),
                (int)$this->request('areaId'),
                (int)$this->request('isCheck'),
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 20) 
            );
        return $this->outputData($data);
    }

    /** 
     * 二级代理列表
     */
    public function childs(){
        $result = ProxyService::getSecondLists(
                $this->proxy,
                $this->request('name')
            );
        return $this->outputData($result);
    }

    /**
     * 代理明细
     */
    public function edit() {
        $result = ProxyService::getProxyById(
                $this->proxy, 
                $this->request('id') 
            );
        return $this->outputData($result);
    }

    /**
     * 添加/修改代理
     */
    public function save(){
        $result = ProxyService::save(
                $this->proxy, 
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
                $this->request('status')
            );
        return $this->output($result);
    }

}