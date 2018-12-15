<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\FeedbackService;
use Lang, Validator;

/**
 * 意见反馈
 */
class FeedbackController extends BaseController 
{
    /**
     * 反馈列表
     */
    public function lists()
    {
        $data = FeedbackService::getList (
            $this->request('type'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		return $this->outputData($data);
    }
    /**
     * 处理反馈
     */
    public function dispose()
    {
        $result = FeedbackService::dispose(intval($this->request('id')), $this->request('content'), $this->adminId);
        
        return $this->output($result);
    }
    /**
     * 删除反馈
     */
    public function delete()
    {
        $result = FeedbackService::delete(
            $this->request('id')
        );
        
        return $this->output($result);
    }
}