<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\SellerService;
use YiZan\Services\SellerCateService;
use YiZan\Services\PaymentService;
use DB;
/**
 * 服务人员
 */
class SellerController extends BaseController { 

    /**
     * 商家创建充值
     */
    public function recharge() {
        $data = PaymentService::createSellerPayLog(
            $this->sellerId,
            $this->request('money'),
            $this->request('payment')
        );
        return $this->output($data);
    }

	/**
	 * 商家评价列表
	 */
	public function evalist(){
		$result = SellerService::getOrderRates(
				$this->sellerId,
                (int)$this->request('type'),
				$this->request('page')
			);
		return $this->outputData($result);
	}

	/**
	 * 评价回复
	 */
	public function evareply(){ 
		$result = SellerService::replyOrderRate(
                $this->sellerId,
				$this->request('id'),
				$this->request('content')
			);
		return $this->output($result);
	}

	/**
	 * 商家经营类型
	 */
	public function trade(){
		$result = SellerCateService::getSellerCateLists($this->sellerId, max((int)$this->request('page'), 1));
		return $this->outputData($result);
	}

    /**
     * 添加银行卡信息
     */
    public function savebankinfo() {
        $result = SellerService::saveBankInfo(
            $this->sellerId,
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
        $result = SellerService::getBankInfo(
            $this->sellerId,
            (int)$this->request('id')
        );
        return $this->output($result);
    }

    /**
     * 删除银行卡信息
     */
    public function delbankinfo() {
        $result = SellerService::delBankInfo(
            $this->sellerId,
            (int)$this->request('id')
        );
        return $this->output($result);
    }

    /**
     * 添加银行卡信息
     */
    public function verifyCodeCk() {
        $result = SellerService::verifyCodeCk(
            $this->request('verifyCode'),
            $this->request('mobile')
        );
        return $this->output($result);
    }


    /**
     * 获取银行卡信息
     */
    public function getAccount() {
        $result = SellerService::getAccount(
            $this->sellerId
        );
        return $this->outputData($result);
    }

    /**
     * 获取运费模版列表
     */
    public function freightList() {
        $result = SellerService::freightList(
            $this->sellerId,
            $this->request('isDefault')
        );
        return $this->outputData($result);
    }
    /**
     * 保存运费模版
     */
    public function saveFreight() {
        $result = SellerService::saveFreight(
            $this->sellerId,
            (array)$this->request('data')
        );
        return $this->output($result);
    }

    /**
     * 删除运费模版
     */
    public function deleteFreight() {
        $result = SellerService::deleteFreight(
            $this->sellerId,
            (array)$this->request('id')
        );
        return $this->output($result);
    }

    /**
     * 获取银行卡信息
     */
    public function detail() {
        $result = SellerService::getInfo(
            $this->sellerId
        );
        return $this->outputData($result);
    }

    /**
     * 获取配送设置信息
     */
    public function sendsetget() {
        $result = SellerService::sendsetget(
            $this->sellerId
        );
        return $this->outputData($result);
    }

    /*保存配送设置*/
    public function sendsetSave() {
        $result = SellerService::sendsetSave(
            $this->sellerId,
            $this->request('serviceFee'),
            $this->request('deliveryFee'),
            $this->request('sendWay'),
            $this->request('sendType')
        );
        return $this->output($result);
    }

}