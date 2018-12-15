<?php 
namespace YiZan\Http\Controllers\Api\Proxy;

use YiZan\Services\Proxy\GoodsService;
use Lang, Validator;

/**
 * 商品/服务管理
 */
class GoodsController extends BaseController 
{
    /**
     * 商品/服务列表
     */
    public function lists() {
        $data = GoodsService::getSystemList(
            $this->proxy,
            $this->request('sellerId'),
            $this->request('type'),
            $this->request('name'),
            (int)$this->request('cateId'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
        
		return $this->outputData($data);
    }
    
    /**
     * 商品/服务详情
     */
    public function get() {
        $data = GoodsService::getSystemGoodsById(
            $this->proxy,
            intval($this->request('id')),
            $this->request('sellerId'),
            $this->request('type')
        );
        return $this->outputData($data == false ? [] : $data->toArray());
    }  
 
}