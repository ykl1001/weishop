<?php 
namespace YiZan\Http\Controllers\Api\System\Admin;

use YiZan\Services\AdminRoleService;
use YiZan\Http\Controllers\Api\System\BaseController;
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
        $data = AdminRoleService::getlist(max((int)$this->request('page'), 1), max((int)$this->request('pageSize'), 20));
        
		return $this->outputData($data);
    }
    /**
     * 添加管理员组
     */
    public function create()
    {
        $result = AdminRoleService::create
        (
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
        $role = AdminRoleService::getById(intval($this->request('id')));
        
        return $this->outputData($role == false ? [] : $role->toArray());
    }
    /**
     * 更新管理员组
     */
    public function update()
    {
        $result = AdminRoleService::update
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
        $result = AdminRoleService::delete(
            $this->request('id')
        );
        return $this->output($result);
    }
}