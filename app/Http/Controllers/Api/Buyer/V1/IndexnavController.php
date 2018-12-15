<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\IndexNavService;
use YiZan\Http\Controllers\Api\Buyer\BaseController;
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
                false, 
                (int)$this->request('cityId')
            );
        return $this->outputData($data);
    }

    public function index(){
        $data = IndexNavService::getIndex();
        return $this->outputData($data);
    }

}