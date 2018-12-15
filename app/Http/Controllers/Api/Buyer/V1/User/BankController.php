<?php 
namespace YiZan\Http\Controllers\Api\Buyer\User;

use YiZan\Http\Controllers\Api\Buyer\BaseController;
use YiZan\Services\Buyer\UserService;
use YiZan\Utils\Time;

class BankController extends BaseController {
	/**
	 * 检测会员是否登录
	 */
	public function __construct() {
		parent::__construct();
		if (!$this->user) {
			return $this->outputCode(99996);
		}
	}
    /**
     * 获取银行卡
     */
    public function getbank() {

        $result = UserService::getbank(
            $this->userId,
            (int)$this->request('id')
        );
        return $this->output($result);
    }

    /**
     * 获取银行卡
     */
    public function getAccount() {

        $result = UserService::getAccount(
            $this->userId
        );
        return $this->output($result);
    }
    /**
     * 申请提现
     */
    public function withdraw(){
        $data = UserService::applyUserAccount(
            $this->userId,
            (float)$this->request('amount')
        );
        return $this->output($data);
    }
    /**
     * 添加银行卡信息
     */
    public function savebankinfo() {
        $result = UserService::saveBankInfo(
            $this->userId,
            (int)$this->request('id'),
            $this->request('bank'),
            $this->request('bankNo'),
            $this->request('mobile'),
            $this->request('name'),
            $this->request('verifyCode')
        );
        return $this->output($result);
    }

    /**
     * 验证短信
     */
    public function verifyCodeCk() {
        $result = UserService::verifyCodeCk(
            $this->request('verifyCode'),
            $this->request('mobile')
        );
        return $this->output($result);
    }
}