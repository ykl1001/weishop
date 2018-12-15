<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\RoomFeeService;
use Lang, Validator;

/**
 * 房间物业费
 */
class RoomfeeController extends BaseController 
{
    /**
     * 房间物业费列表
     */
    public function lists()
    {
        $data = RoomFeeService::getLists(
            $this->sellerId,
            $this->request('name'),
            $this->request('build'),
            $this->request('roomNum'), 
            $this->request('payitemId'), 
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20) 
        );
        
        return $this->outputData($data);
    } 

    /**
     * 搜索房间物业费列表
     */
    public function search()
    {
        $data = RoomFeeService::getSearchLists(
            $this->sellerId,
            $this->request('buildId'),
            $this->request('roomId'),   
            $this->request('name')
        );
        
        return $this->outputData($data);
    }  

    /**
     * 保存房间物业费
     */
    public function save()
    {
        $result = RoomFeeService::save(
            $this->sellerId,
            (int)$this->request('id'),
            $this->request('buildId'),
            $this->request('roomId'),
            $this->request('payitemId'), 
            $this->request('remark')
        );
        
        return $this->output($result);
    }


    /**
     * 删除房间物业费
     */
    public function delete()
    {
        $result = RoomFeeService::delete(
            $this->sellerId,
            $this->request('id')
        );
        
        return $this->output($result);
    }


    public function get()
    {
        $result = RoomFeeService::getById(
            $this->sellerId,
            intval($this->request('id'))
        );
        return $this->outputData($result);
    }
 

}

