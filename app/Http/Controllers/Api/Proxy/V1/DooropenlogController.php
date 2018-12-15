<?php 
namespace YiZan\Http\Controllers\Api\Proxy;

use YiZan\Services\Proxy\DoorOpenLogService;
use Lang, Validator;

/**
 * 开门记录
 */
class DooropenlogController extends BaseController 
{
    /**
     * 开门记录
     */
    public function lists() {
        $data = DoorOpenLogService::getLists(
            $this->request('sellerId'),
            $this->request('districtId'),
            $this->request('doorName'),
            $this->request('userName'),
            $this->request('beginTime'),
            $this->request('endTime'),
            (int)$this->request('isTotal'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
        
		return $this->outputData($data);
    } 
}