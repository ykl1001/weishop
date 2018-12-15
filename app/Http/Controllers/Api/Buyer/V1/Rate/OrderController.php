<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Rate;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\Buyer\OrderRateService;

/**
 * 订单评价
 */
class OrderController extends UserAuthController {
	/**
	 * 评价
	 */
	public function create() {
		$result = OrderRateService::createRate(
				$this->userId,
				(int)$this->request('orderId'),
                (array)$this->request('images'),
				$this->request('content'),
                (int)$this->request('star'),
                (int)$this->request('isAno')
			);
		return $this->output($result);
	}

    /**
     * 全国店评价
     */
    public function createall() {
        $result = OrderRateService::createRateAll(
                $this->userId,
                (int)$this->request('isAll'),
                (int)$this->request('orderId'),
                (int)$this->request('shopStar'),
                (array)$this->request('comment'),
                (int)$this->request('isAno')
            );
        return $this->output($result);
    }

    /**
     * 评价列表
     */
    public function lists() {
        $data = OrderRateService::getList(
            (int)$this->request('sellerId'),
            (int)$this->request('type'),
            max((int)$this->request('page'),1)
        );
        return $this->outputData($data);
    }

    /**
     * 评价统计
     */
    public function statistics() {
        $data = OrderRateService::getCount(
            (int)$this->request('sellerId')
        );
        return $this->outputData($data);
    }

    /**
     * 评价列表
     */
    public function userlists() {
        $data = OrderRateService::getUserList(
            $this->userId,
            max((int)$this->request('page'),1)
        );
        return $this->outputData($data);
    }
}