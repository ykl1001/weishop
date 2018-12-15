<?php 
namespace YiZan\Http\Controllers\Api\System;
use YiZan\Services\SystemTagListService;
use YiZan\Utils\Time;

class SystemTagListController extends BaseController {

	/**
	 * [lists 获取标签分类列表]
	 * @return [type] [description]
	 */
	public function lists() {
        $data = SystemTagListService::getList(
            $this->request('status')
        );
        return $this->outputData($data);
    }
	
	 public function lists2() {
        $data = SystemTagListService::getList2(
            $this->request('status')
        );
        return $this->outputData($data);
    }

    /**
     * [lists 获取标签分类列表]
     * @return [type] [description]
     */
    public function getListItem() {
        $data = SystemTagListService::getListItem(
            $this->request('status'),
            $this->request('pid'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($data);
    }

    /**
     *  创建商品标签分类
     */
    public function save() {
        $result = SystemTagListService::save(
        	intval($this->request('id')),
            strval($this->request('name')),
            intval($this->request('sort')),
            intval($this->request('status')),
            intval($this->request('pid')),
            intval($this->request('systemTagId')),
            strval($this->request('img'))
        );
        return $this->output($result);
    }

    /**
     * [get 获取单个分类信息]
     * @return [type] [description]
     */
    public function get() {
    	$result = SystemTagListService::get(
        	intval($this->request('id'))
        );
        return $this->outputData($result);
    }

    /**
     * [secondLevel 通过一级分类获取二级分类]
     * @return [type] [description]
     */
    public function secondLevel() {
        $result = SystemTagListService::secondLevel(
            $this->request('pid')
        );

        return $this->outputData($result);
    }

    /**
     * 标签分类删除
     */
    public function delete() {
        $result = SystemTagListService::delete(
            $this->request('id')
        );
        return $this->output($result);
    }


}