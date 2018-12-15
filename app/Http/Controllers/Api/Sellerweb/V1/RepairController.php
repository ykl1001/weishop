<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\RepairService;
use Lang, Validator;

/**
 * 报修
 */
class RepairController extends BaseController 
{
    /**
     * 报修列表
     */
    public function lists()
    {
        $data = RepairService::getLists(
            $this->sellerId,
            $this->request('name'),
            $this->request('build'),
            $this->request('roomNum'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20),
            (int)$this->request('status'),
            $this->request('userName'),
            $this->request('staffName')

        );
        
		return $this->outputData($data);
    } 


    /**
     * 处理报修
     */
    public function save()
    {
        $result = RepairService::save(
            (int)$this->request('id'),
            $this->sellerId,
            (int)$this->request('status')
        );
        
        return $this->output($result);
    }


    /**
     * 删除报修
     */
    public function delete()
    {
        $result = RepairService::delete(
            $this->request('id'),
            $this->sellerId

        );
        
        return $this->output($result);
    }



    public function get()
    {
        $result = RepairService::getById(intval($this->request('id')));
        return $this->outputData($result);
    }


   public function getRepair(){
       $result = RepairService::getRepair(
           (int)$this->request('type'),
           $this->sellerId
       );

       return $this->outputData($result);
   }

    public function designate(){
        $result = RepairService::designate(
            (int)$this->request('id'),
            (int)$this->request('staffId'),
            (int)$this->request('status'),
            $this->sellerId
        );

        return $this->outputData($result);
    }


}

