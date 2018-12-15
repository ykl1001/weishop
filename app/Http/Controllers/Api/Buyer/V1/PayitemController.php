<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\PayItemService;
use Lang, Validator;

/**
 * 收费项目
 */
class PayitemController extends BaseController 
{
    /**
     * 收费项目列表
     */
    public function lists()
    {
        $data = PayItemService::getLists($this->request('sellerId'));
        
		return $this->outputData($data);
    }    

}

