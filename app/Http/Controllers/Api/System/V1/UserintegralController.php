<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\UserIntegralService;

use Lang, Validator;

/**
 * 会员积分
 */
class UserIntegralController extends BaseController
{
    /**
     * 积分列表
     */
    public function lists()
    {
        $data = UserIntegralService::getList
        (
            trim($this->request('name')),
            trim($this->request('mobile')),
            trim($this->request('beginTime')),
            trim($this->request('endTime')),
            max($this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($data);
    }

}