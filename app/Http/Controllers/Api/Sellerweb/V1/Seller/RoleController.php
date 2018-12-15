<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb\Seller;

use YiZan\Services\SellerRoleService;
use YiZan\Http\Controllers\Api\Sellerweb\BaseController;
use Lang, Validator;

/**
 * 管理员组
 */
class RoleController extends BaseController 
{
    /**
     * 管理员组列表
     */
    public function lists()
    {
        $data = SellerRoleService::getlist(
            $this->sellerId,
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        
		return $this->outputData($data);
    }
    /**
     * 添加管理员组
     */
    public function create()
    {
        $result = SellerRoleService::create
        (
            $this->sellerId,
            $this->request('name'),
            $this->request('access')
        );
        
        return $this->output($result);
    }
    /**
     * 获取管理员组
     */
    public function get()
    {
        $role = SellerRoleService::getById(
            $this->sellerId,
            intval($this->request('id'))
        );
        
        return $this->outputData($role == false ? [] : $role->toArray());
    }
    /**
     * 更新管理员组
     */
    public function update()
    {
        $result = SellerRoleService::update
        (
            intval($this->request('id')),
            $this->request('name'),
            $this->request('access')
        );
        
        return $this->output($result);
    }
    /**
     * 删除管理员组
     */
    public function delete()
    {
        $result = SellerRoleService::delete(
            $this->request('id')
        );
        return $this->output($result);
    }
}