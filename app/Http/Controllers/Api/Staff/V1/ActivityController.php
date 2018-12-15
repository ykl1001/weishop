<?php
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\ActivityService;
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
            $this->sellerId
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

    /**
     * 保存满减活动
     */
    public function activityFull() {
        $result = ActivityService::activityFull
        (
            $this->sellerId,
            strval($this->request('startTime')),
            strval($this->request('endTime')),
            $this->request('joinNumber'),
            (double)$this->request('fullMoney'),
            (double)$this->request('cutMoney')
        );

        return $this->output($result);
    }

    /**
     * 保存特价
     */
    public function activitySpecial() {
        $result = ActivityService::activitySpecial
        (
            $this->sellerId,
            $this->request('startTime'),
            $this->request('endTime'),
            $this->request('joinNumber'),
            $this->request('sale'),
            (array)$this->request('ids')
        );

        return $this->output($result);
    }
}