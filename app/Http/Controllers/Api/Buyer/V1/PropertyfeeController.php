<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\PropertyFeeService;
use Lang, Validator;

/**
 * 物业费
 */
class PropertyfeeController extends BaseController 
{
    /**
     * 物业费列表
     */
    public function lists()
    {
        $data = PropertyFeeService::getLists(
            $this->userId,
            $this->request('sellerId'),
            $this->request('payitemId')
        );
        
		return $this->outputData($data);
    }   

    /**
     * 物业费缴费记录
     */
    public function paylists()
    {
        $data = PropertyFeeService::getPayLists(
            $this->userId,
            $this->request('sellerId') 
        );
        
        return $this->outputData($data);
    }

    /**
     * 通过id去找
     */
    public function getbyidslists()
    {
        $data = PropertyFeeService::getByIdsLists(
            $this->userId,
            $this->request('ids')
        );

        return $this->outputData($data);
    }
}

