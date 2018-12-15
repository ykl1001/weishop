<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\PropertySystemService;
use Lang, Validator;

/**
 * 公告
 */
class PropertySystemController extends BaseController
{ 
	//获取公告列表
	public function lists(){
		$result = PropertySystemService::getList(
			$this->sellerId,
			$this->request('title'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
			);
		return $this->outputData($result);
	}

    /**
     * 获取公告详情
     */
    public function get() {
        $result = PropertySystemService::getById($this->sellerId, intval($this->request('id')));
        return $this->outputData($result == false ? [] : $result->toArray());
    }

    /**
     * 保存公告
     */
    public function save(){
    	$result = PropertySystemService::save(
    			$this->sellerId,
    			intval($this->request('id')),
    			$this->request('sort'),
    			$this->request('name'),
                $this->request('type'),
                (int)$this->request('status')
    		);
    	return $this->output($result);
    }

    /**
     * 删除公告
     */
    public function delete(){
    	$result = PropertySystemService::delete(
            $this->sellerId, 
            $this->request('id')
        );
    	return $this->output($result);
    }



}