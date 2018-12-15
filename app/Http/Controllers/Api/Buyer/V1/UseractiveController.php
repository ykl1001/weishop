<?php
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\WeixinService;
class UseractiveController extends BaseController{

    /**
     * 获取微信JS信息配置
     */
    public function getweixin() {
        $payment = WeixinService::getweixin($this->request('url'));
        return $this->outputData($payment);
    }

    /**
     * 获取微信JS信息配置
     */
    public function getWeixinUser() {
        $payment = WeixinService::getUserFirst(
            $this->request('openid'),
            $this->request('openShareUserId')
        );
        return $this->outputData($payment);
    }

}
