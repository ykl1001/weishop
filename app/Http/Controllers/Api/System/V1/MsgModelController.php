<?php
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\MsgModelService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 消息模板
 */
class MsgModelController extends BaseController
{
    /**
     * 活动列表
     */
    public function lists()
    {
        $data = MsgModelService::getList();
        return $this->outputData($data);
    }

    /**
     * 消息模板
     */
    public function getId()
    {
        $data = MsgModelService::getId($this->request('id'));
        return $this->outputData($data);
    }

    /**
     * 活动列表
     */
    public function save()
    {
        $data = MsgModelService::save(
            $this->request('data')
        );
        return $this->output($data);
    }
}