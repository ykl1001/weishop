<?php
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\ActivityService;
use Lang, Validator;

/**
 * 活动管理
 */
class ActivityController extends BaseController
{
    /**
     * 活动列表
     */
    public function lists()
    {
        $data = ActivityService::getList
        (
            $this->sellerId,
            $this->request('name'),
            $this->request('startTime'),
            $this->request('endTime'),
            (int)$this->request('type'),
            $this->request('page'),
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($data);
    }

    /**
     * 获取活动
     */
    public function get()
    {
        $Activity = ActivityService::getById(
            $this->sellerId,
            intval($this->request('id'))
        );

        return $this->outputData($Activity == false ? [] : $Activity->toArray());
    }

    /**
     * 作废
     */
    public function cancellation() {
        $result = ActivityService::cancellation
        (
            $this->sellerId,
            (int)$this->request('id')
        );

        return $this->output($result);
    }
}