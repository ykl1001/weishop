<?php 
namespace YiZan\Http\Controllers\Api\Staff;
use YiZan\Services\Staff\StaffService;

class StaffController extends BaseController {
	/**
	 * 更新员工信息
	 */
	public function update() {
        
		$result = StaffService::updateInfo(
                    $this->staffId,
                    $this->request('name'),
                    $this->request('avatar')
                );
		return $this->output($result);
	}

    /**
     * 更新员工常驻地址
     */
    public function address() {
        $result = StaffService::address(
            $this->staffId,
            trim($this->request('address')),
            $this->request('mapPoint')
        );
        return $this->output($result);
    }

    /**
     * 更新服务范围
     */
    public function range() {
        $result = StaffService::range(
            $this->userId,
            $this->request('mapPos')
        );
        return $this->output($result);
    }
	    /**
     * 更新服务范围
     */
    public function getbyid() {
        $result = StaffService::getById(
            $this->staffId,
            $this->request('extend')
        );
        return $this->output($result);
    }

    /**
     * 添加银行卡信息
     */
    public function savebankinfo() {
        $result = StaffService::saveBankInfo(
            $this->staffId,
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
     * 获取银行卡信息
     */
    public function getbankinfo() {
        $result = StaffService::getBankInfo(
            $this->staffId,
            (int)$this->request('id')
        );
        return $this->output($result);
    }
}