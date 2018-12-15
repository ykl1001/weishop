<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\GoodsCateService;
use YiZan\Http\Controllers\Api\Staff\BaseController;

/**
 * 商品/服务分类 
 */
class GoodscateController extends BaseController {
    /**
     * 分类列表
     */
    public function lists() {
        $data = GoodsCateService::getGoodsCateLists(
            $this->sellerId,
            (int)$this->request('type'),
            max((int)$this->request('page'), 1)
        );
		return $this->outputData($data);
    }

    /**
     * 删除分类列表
     */
    public function del(){
        $result = GoodsCateService::deleteCate(
            $this->sellerId,
            $this->request('id')
        );
        return $this->output($result);
    }
    /**
     * 删除分类列表
     */
    public function getById(){
        $result = GoodsCateService::getById(
            $this->sellerId,
            $this->request('id')
        );
        return $this->output($result);
    }

    /**
     * 编辑分类
     */
    public function edit(){ 
    	$result = GoodsCateService::editCate( 
            $this->sellerId,
            $this->request('id') ,
            $this->request('tradeId') ,
            $this->request('name')  ,
            $this->request('type')  
            ); 
    	return $this->output($result);
    }

    /**
     * 分类排序
     */
    public function sort(){
    	$result = GoodsCateService::sortCate(
            $this->sellerId,
            $this->request('data')
           	); 
    	return $this->output($result);
    }

}