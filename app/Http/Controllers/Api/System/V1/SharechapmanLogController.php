<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\SharechapmanLogService;


class SharechapmanLogController extends BaseController {
    /**
     * 提现列表
     */
    public function lists() {
        $data = SharechapmanLogService::lists(
                $this->request('name'),
                $this->request('mobile'),
                (int)$this->request('status'),
                (int)$this->request('beginTime'),
                (int)$this->request('endTime'),
                max((int)$this->request('page'), 1),
                max((int)$this->request('pageSize'), 10)
            );
        return $this->outputData($data);
    }

    /**
     * 处理社区
     */
    public function dispose() {
        $result = SharechapmanLogService::dispose(
            $this->adminId,
            (int)$this->request('id'),
            $this->request('content'),
            (int)$this->request('status')
        );
        return $this->output($result);
    }
}