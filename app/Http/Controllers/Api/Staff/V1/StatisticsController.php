<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\StatisticsService; 
use Lang, Validator;

class StatisticsController extends BaseController {
	
	/**
	 * 统计明细
	 */
	public function detail(){  
		$data = StatisticsService::getStatisticsDetail(
            $this->staffId,
            $this->request('month'),
            $this->request('page')
        );
		return $this->outputData($data);
	}
	
	
	/**
	 * 按月份来统计
	 */
	public function month(){  
		$data = StatisticsService::getStatisticsByMonth($this->staffId,$this->request('page'));
		return $this->outputData($data);
	}


	/**
	 * 营业额
	 */
	public function revenue() {
		$data = \YiZan\Services\Sellerweb\StatisticsService::revenue(
				$this->sellerId,
				$this->request('beginDate'),
				$this->request('endDate'),
				(int)$this->request('type')
		);
		return $this->outputData($data);
	}

}