<?php 
namespace YiZan\Http\Controllers\Api\Buyer\User;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\Buyer\PromotionService;

class PromotionController extends UserAuthController {
	/**
	 * 获取会员的优惠券列表
	 */
	public function lists() {
		$data = PromotionService::getPromotionList(
				$this->userId,
				(int)$this->request('status'),
				max((int)$this->request('page'), 1),
				(int)$this->request('sellerId'),
                (double)round($this->request('money'),2)
			);
		return $this->outputData($data);
	}


	/**
	 * 优惠券兑换
	 */
	public function exchange() {
		$result = PromotionService::exchangePromotion(
                    $this->userId,
                    $this->request('sn'),
                    (int)$this->request('type')

                );
		return $this->output($result);
	}

	/**
	 * 优惠券兑换
	 */
	public function receive() {
		$result = PromotionService::receivePromotion($this->userId, (int)$this->request('id'));
		return $this->output($result);
	}

    /**
     * 获取可用的第一个优惠券
     */
    public function first() {
        $result = PromotionService::getFirst($this->userId);
        return $this->outputData($result);
    }

    /**
     * 获取优惠券详情
     */
    public function get() {
        $result = PromotionService::getById($this->userId,(int)$this->request('id'));
        return $this->outputData($result);
    }

    /**
     * 发优惠券
     */
    public function send(){
        $result = PromotionService::shareSend($this->userId,(int)$this->request('orderId'),(int)$this->request('activityId'),(int)$this->request('promotionId'));
        return $this->outputData($result);
    }

    /**
     * 优惠券消息弹框
     */
    public function couponmsg(){
        $data = PromotionService::couponMsg(
            $this->userId
        );
        return $this->output($data);
    }

    /**
     * 获取优惠券详情
     */
    public function getbest() {
        $result = PromotionService::getBest(
            $this->userId,
            $this->request('price'),
            $this->request('storeType') ,
            $this->request('sellerId') 
        );
        return $this->outputData($result);
    }
}