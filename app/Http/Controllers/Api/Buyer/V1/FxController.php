<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

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

    public function makewapurl(){
        $data = FxBaseService::makeWapUrl
        (
            $this->request('fanweId')
        );

        return $this->outputData($data);
    }


}