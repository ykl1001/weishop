<?php 
namespace YiZan\Http\Controllers\Api\Proxy\Goods;

use YiZan\Services\Proxy\GoodsCateService;
use YiZan\Http\Controllers\Api\Proxy\BaseController;
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
        $data = GoodsCateService::getList(
            $this->proxy,
            $this->request('sellerId'), 
            $this->request('type')
        );
        
		return $this->outputData($data);
    } 

    public function get()
    {
        $result = GoodsCateService::getSellerCate(
            $this->proxy,
            intval($this->request('sellerId')),
            intval($this->request('id'))
        );
        
        return $this->outputData($result);
    }
}