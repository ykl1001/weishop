<?php 
namespace YiZan\Http\Controllers\Api\Proxy;

use YiZan\Services\Proxy\TotalViewService;

/**
 * 订单统计
 */
class TotalviewController extends BaseController 
{
    /**
     * 概况
     */
    public function total()
    {   

        $data = TotalViewService::total($this->proxy);
        
		return $this->outputData($data);
    }
    
}