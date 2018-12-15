<?php 
namespace YiZan\Http\Controllers\Api\System\User;

use YiZan\Services\System\UserRefundService;
use YiZan\Http\Controllers\Api\System\BaseController;
use YiZan\Services\LogisticsService;

/**
 * 会员退款
 */
class RefundController extends BaseController {
    /**
     * 会员退款列表
     */
    public function lists()
    {
        $data = UserRefundService::getLists(
                $this->request('user'),
                $this->request('mobile'),
                $this->request('orderSn'),
                $this->request('beginTime'),
                $this->request('endTime'), 
                (int)$this->request('status'), 
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 20)
            );
        return $this->outputData($data);
    }

    /**
     * 退款处理
     */
    public function dispose() {
        $result = UserRefundService::dispose((int)$this->request('id'));
        
        return $this->output($result);
    }

    /**
     * 会员退款列表
     */
    public function getNationwideLists()
    {
        $data = UserRefundService::getNationwideLists(
            $this->request('user'),
            $this->request('mobile'),
            $this->request('orderSn'),
            $this->request('beginTime'),
            $this->request('endTime'),
            (int)$this->request('status'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($data);
    }


    /**
     * 退款处理
     */
    public function disposesave() {

        $result =LogisticsService::orderrund(
            $this->request('id'),
            $this->request('status'),
            $this->request('content')
        );
        return $this->output($result);
    }

}