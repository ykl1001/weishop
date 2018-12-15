<?php 
namespace YiZan\Http\Controllers\Api\System\System;

use YiZan\Services\SystemCateService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 服务分类
 */
class CateController extends BaseController
{
    /**
     * 分类列表
     */
    public function lists()
    {
        $data = SystemCateService::getList(
            max($this->request('page'),1),
            max($this->request('pageSize'),20)
        );
        
        return $this->outputData($data);
    }
    /**
     * 添加分类
     */
    public function create()
    {
        $result = SystemCateService::create
        (
            $this->request('name'), 
            $this->request('pid', 0),
            $this->request('status'),
            $this->request('logo'),
            (int)$this->request('type'),
            intval($this->request('sort'))
        );
        
        return $this->output($result);
    }
    /**
     * 更新分类
     */
    public function update()
    {
        $result = SystemCateService::update
        (
            (int)$this->request('id'),
            $this->request('name'),
            $this->request('pid', 0),
            $this->request('status'),
            $this->request('logo'),
            (int)$this->request('type'),
            intval($this->request('sort'))
        );
        
        return $this->output($result);
    }
    /**
     * 删除分类
     */
    public function delete()
    {
        $result = SystemCateService::delete((array)$this->request('id'));
        
        return $this->output($result);
    }

    /**
     * 获取分类
     */

    public function get() {
        $data = SystemCateService::get((int)$this->request('id'));
        return $this->outputData($data);
    }

    /**
     * 无分页分类
     */
    public function all() {
        $list = SystemCateService::getAll();
        return $this->outputData($list);
    }

    /**
     * 无分页分类
     */
    public function catesall() {
        $list = SystemCateService::getSystemTradeCatesAll();
        return $this->outputData($list);
    }
}