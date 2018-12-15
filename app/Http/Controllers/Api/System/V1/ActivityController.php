<?php
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\ActivityService;
use YiZan\Http\Controllers\Api\System\BaseController;
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
            $this->request('name'),
            (int)$this->request('status'),
            (int)$this->request('startTime'),
            (int)$this->request('endTime'),
            (int)$this->request('type'),
            $this->request('page'),
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($data);
    }
    /**
     * 添加活动
     */
    public function create()
    {
        $result = ActivityService::create
        (
            $this->request('name'),
            $this->request('image'),
            $this->request('startTime'),
            $this->request('endTime'),
            $this->request('content'),
            $this->request('promotion'),
            $this->request('type'),
            intval($this->request('sort')),
            intval($this->request('status'))
        );

        return $this->output($result);
    }
    /**
     * 获取活动
     */
    public function get()
    {
        $Activity = ActivityService::getById(intval($this->request('id')));

        return $this->outputData($Activity == false ? [] : $Activity->toArray());
    }

    /**
     * 更新活动
     */
    public function update()
    {
        $result = ActivityService::update
        (
            $this->request('id'),
            $this->request('name'),
            $this->request('image'),
            $this->request('startTime'),
            $this->request('endTime'),
            $this->request('content'),
            $this->request('promotion'),
            $this->request('type'),
            intval($this->request('sort')),
            intval($this->request('status')),
            (int)$this->request('promotionId')
        );

        return $this->output($result);
    }


    /**
     * 保存注册活动
     */
    public function save()
    {
        $result = ActivityService::saveActivityReg
        (
            (int)$this->request('id'),
            $this->request('name'),
            $this->request('startTime'),
            $this->request('endTime'),
            (int)$this->request('status'),
            (int)$this->request('promotionId'),
            (int)$this->request('num')
        );

        return $this->output($result);
    }

    /**
     * 删除活动
     */
    public function delete()
    {
        $result = ActivityService::delete(
            $this->request('id')
        );
        return $this->output($result);
    }

    /**
     * 获取分享活动
     */
    public function activity(){
        $result = ActivityService::getActivity(
            (int)$this->request('id'),
            (int)$this->request('type')
        );
        return $this->output($result);
    }

    /**
     * 获取活动的优惠券
     */
    public function getPromotionLists(){
        $result = ActivityService::getPromotionLists();
        return $this->output($result);
    }

    /**
     * 更新注册活动
     */
    public function registerUpdate()
    {

        $result = ActivityService::registerUpdate
        (
            $this->request('id'),
            $this->request('name'),
            $this->request('startTime'),
            $this->request('endTime'),
            $this->request('promotion'),
            $this->request('type'),
            intval($this->request('status'))
        );

        return $this->output($result);
    }

    /**
     * 更新注册活动
     */
    public function shareUpdate()
    {
        $result = ActivityService::shareUpdate
        (
            $this->request('id'),
            $this->request('name'),
            $this->request('bgimage'),
            $this->request('startTime'),
            $this->request('endTime'),
            (int)$this->request('promotionId'),
            (int)$this->request('num'),
            (double)$this->request('money'),
            (int)$this->request('sharePromotionNum'),
            $this->request('title'),
            $this->request('detail'),
            $this->request('image'),
            $this->request('buttonName'),
            $this->request('buttonUrl'),
            $this->request('brief'),
            $this->request('count'),
            $this->request('type'),
            intval($this->request('status')),
            intval($this->request('limitGet'))
        );

        return $this->output($result);
    }

    /**
     * 添加满减活动
     */
    public function saveFull()
    {
        $result = ActivityService::saveFull
        (
            (string)$this->request('startTime'),
            (string)$this->request('endTime'),
            (int)$this->request('type'),
            (double)$this->request('fullMoney'),
            (double)$this->request('cutMoney'),
            $this->request('joinNumber'),
            (int)$this->request('useSeller'),
            (array)$this->request('ids'),
            (int)$this->request('isSystem')
        );

        return $this->output($result);
    }

    /**
     * 添加首单立减活动
     */
    public function saveNew() {
        $result = ActivityService::saveNew
        (
            (string)$this->request('startTime'),
            (string)$this->request('endTime'),
            (int)$this->request('type'),
            (double)$this->request('fullMoney'),
            (double)$this->request('cutMoney'),
            (int)$this->request('useSeller'),
            (array)$this->request('ids'),
            (int)$this->request('isSystem')
        );

        return $this->output($result);
    }

    /**
     * 作废
     */
    public function cancellation() {
        $result = ActivityService::cancellation
        (
            (int)$this->request('id')
        );

        return $this->output($result);
    }
}