<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\PayItemService;
use Lang, Validator;

/**
 * 收费项目
 */
class PayitemController extends BaseController 
{
    /**
     * 收费项目列表
     */
    public function lists()
    {
        $data = PayItemService::getLists(
            $this->sellerId,
            $this->request('name'),  
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20),
            (int)$this->request('isAll')
        );
        
		return $this->outputData($data);
    }  

    /**
     * 保存收费项目
     */
    public function save()
    {
        $result = PayItemService::save(
            $this->sellerId,
            (int)$this->request('id'),
            $this->request('name'),
            (float)$this->request('price'),
            (int)$this->request('chargingItem'),
            (int)$this->request('chargingUnit') 
        );
        
        return $this->output($result);
    } 

    /**
     * 删除报修
     */
    public function delete()
    {
        $result = PayItemService::delete(
            $this->sellerId, 
            intval($this->request('id'))
        );
        
        return $this->output($result);
    } 

    public function get()
    {
        $result = PayItemService::get(
            $this->sellerId,
            intval($this->request('id'))
        );
        return $this->outputData($result);
    }


}

