<?php 
namespace YiZan\Http\Controllers\Api\System;

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

    /**
     * 后台同步登陆
     */
    public function makemanageurl() 
    {
        $data = FxBaseService::makeManageUrl();
        
        return $this->outputData($data);
    }

}