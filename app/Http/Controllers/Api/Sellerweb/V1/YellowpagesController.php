<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\YellowPagesService;
use Lang, Validator;

/**
 * 公告
 */
class YellowPagesController extends BaseController
{ 
	//获取公告列表
	public function lists(){
		$result = YellowPagesService::getList(
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
        $result = YellowPagesService::getById($this->sellerId, intval($this->request('id')));
        return $this->outputData($result == false ? [] : $result->toArray());
    }

    /**
     * 保存公告
     */
    public function save(){
    	$result = YellowPagesService::save(
    			$this->sellerId,
    			intval($this->request('id')),
    			$this->request('mobile'),
    			$this->request('name'),
    			(int)$this->request('status')
    		);
    	return $this->output($result);
    }

    /**
     * 删除公告
     */
    public function delete(){
    	$result = YellowPagesService::delete(
            $this->sellerId, 
            $this->request('id')
        );
    	return $this->output($result);
    }



}