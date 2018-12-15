<?php 
namespace YiZan\Http\Controllers\Api\Proxy\Seller;

use YiZan\Services\SellerCateService;
use YiZan\Http\Controllers\Api\Proxy\BaseController;
use Lang, Validator;

/**
 * 服务分类
 */
class CateController extends BaseController
{ 

    /**
     * 无分页分类
     */
    public function all() {
        $list = SellerCateService::getAll();
        return $this->outputData($list);
    }

    /**
     * 无分页分类
     */
    public function catesall() {
        $list = SellerCateService::getSellerCatesAll();
        return $this->outputData($list);
    }
}