<?php
namespace YiZan\Http\Controllers\Api\Saas;

use YiZan\Services\Saas\WeixinService;
use Lang, Validator;

/**
 * 活动管理
 */
class WeixinController extends BaseController
{
    /**
     * 活动列表
     */
    public function authorize()
    {
        $data = WeixinService::authorize
        (
            
        );
        return $this->outputData($data);
    }
}