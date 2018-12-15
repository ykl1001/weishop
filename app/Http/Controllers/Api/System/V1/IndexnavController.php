<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\IndexNavService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 首页底部导航
 */
class IndexnavController extends BaseController 
{
    /**
     * 列表
     */
    public function lists(){
        $data = IndexNavService::getLists(
                $this->request('name'), 
                (int)$this->request('cityId'),
                (int)$this->request('status'),
                max((int)$this->request('page'), 1), 
                (int)$this->request('pageSize', 20)
            );
        return $this->outputData($data);
    } 

    /**
     * 代理详情
     */
    public function detail(){
        $data = IndexNavService::getById($this->request('id'));
        return $this->outputData($data);
    }

    /**
     * 添加/修改代理
     */
    public function save(){
        $result = IndexNavService::save(
                $this->request('id'),
                $this->request('name'),  
                (int)$this->request('cityId'),
                $this->request('icon'),
                $this->request('type'),
                (int)$this->request('sort'),
                (int)$this->request('isSystem'),
                (int)$this->request('status'),
                (int)$this->request('isIndex')
            );
        return $this->output($result);
    }
 
    /**
     * 删除代理
     */
    public function delete(){
        $result = IndexNavService::delete($this->request('id'));
        return $this->output($result);
    }

}