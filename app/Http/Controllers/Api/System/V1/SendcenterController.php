<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\SendcenterService;
use Input;
/**
 * 配送中心
 */
class SendcenterController extends BaseController 
{
   	/**
     * 人员配送数据
	 */
	public function stafflist()
    {
        $data = SendcenterService::stafflist(
            $this->request('time'),
            $this->request('beginTime'), 
            $this->request('endTime'),
            $this->request('cityName'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		return $this->outputData($data);
    }

    /**
     * 城市数据统计
     */
    public function citylist() {
    	$data = SendcenterService::citylist(
            $this->request('time'),
            $this->request('beginTime'), 
            $this->request('endTime'),
            $this->request('cityName'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		return $this->outputData($data);
    }

}