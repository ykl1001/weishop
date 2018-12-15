<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\PropertyFeeService;
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
            $this->sellerId,
            $this->request('buildId'),
            $this->request('roomId'),
            $this->request('name'), 
            $this->request('payitemId'), 
            $this->request('status'), 
            $this->request('beginTime'), 
            $this->request('endTime'), 
            $this->request('propertyFeeId'), 
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20) 
        );
        
		return $this->outputData($data);
    }  

    /**
     * 保存物业费
     */
    public function save()
    {
        $result = PropertyFeeService::save(
            $this->sellerId, 
            $this->request('buildId'),
            $this->request('roomId'),
            $this->request('roomFeeId'), 
            $this->request('beginTime'), 
            $this->request('num'), 
            (int)$this->request('isAutoSet')
        );
        
        return $this->output($result);
    }


    /**
     * 检查
     */
    public function check()
    { 
        $result = PropertyFeeService::check(
            $this->sellerId, 
            $this->request('id') 
        );
        
        return $this->output($result);
    }

    /**
     * 删除物业费
     */
    public function delete()
    {
        $result = PropertyFeeService::delete(
            $this->sellerId,
            $this->request('id')
        );
        
        return $this->output($result);
    }


    public function get()
    {
        $result = PropertyFeeService::getById(
            $this->sellerId,
            intval($this->request('id'))
        );
        return $this->outputData($result);
    }
 

}

