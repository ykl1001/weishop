<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Rate;

use YiZan\Http\Controllers\Api\Buyer\BaseController;
use YiZan\Services\Buyer\OrderRateService;

/**
 * 服务评价
 */
class GoodsController extends BaseController {
	/**
	 * 评价
	 */
	public function lists() {
		$data = OrderRateService::goodsRates(
				(int)$this->request('goodsId'),
				$this->request('type'),
				max((int)$this->request('page'), 1)
			);
		return $this->outputData($data);
	}

	/**
	 * 整体评分
	 */
	public function statistics() {
		$data = OrderRateService::goodsStatistics(
				(int)$this->request('goodsId')
			);
		return $this->outputData($data);
	}
}