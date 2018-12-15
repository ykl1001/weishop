<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\FxBaseService;
use Lang, Validator;

/**
 * 分销
 */
class FxController extends BaseController 
{
    /**
     * 消息列表
     */
    public function api()
    {
        $data = FxBaseService::requestApi
        (
            $this->request('path'), 
            $this->request('args')
        );
        
		return $this->outputData($data);
    }
}