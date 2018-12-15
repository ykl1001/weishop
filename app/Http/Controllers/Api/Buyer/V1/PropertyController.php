<?php namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\PropertyService;
use YiZan\Services\Buyer\PropertyUserService as PUService;
use YiZan\Services\RepairService;
use YiZan\Services\PropertyUserService;
use Lang, Validator;

class PropertyController extends BaseController
{
    /*
    * 物业介绍
    */
    public function detail() {
		
        $result = PropertyService::getProperty((int)$this->request('districtId'));

        return $this->outputData($result ? $result->toArray() : []);
    }


    /*
    * 报修列表
    */
    public function repairlists() {
        $result = RepairService::getRepairLists($this->userId, (int)$this->request('districtId'), max((int)$this->request('page'), 1));
        return $this->outputData($result);
    }

    public function repairget() {
        $result = RepairService::get((int)$this->request('id'), (int)$this->request('districtId'));
        return $this->outputData($result);
    }

    //报修类型
    public function typelists() {
        $result = RepairService::getRepairTypeLists();
        return $this->outputData($result);
    }

    public function createrepair()
    {
        $result = RepairService::createRepair(
            $this->userId,
            $this->request('districtId'),
            $this->request('typeId'),
            $this->request('images'),
            $this->request('content'),
            $this->request('apiTime')
        );

        return $this->output($result);
    }

    public function createrate(){
        $result = RepairService::createRate(
            $this->userId,
            (int)$this->request('id'),
            $this->request('content'),
            (int)$this->request('star')
        );
        return $this->output($result);
    }

    public function bindDeivce()
    {
        $result = PropertyUserService::bindDeivce(
            $this->user,
            $this->request('ticket'),
            $this->request('openid'),
            $this->request('deviceId'),
            $this->request('ksid'),
            $this->request('ktype'),
            $this->request('isopen',0)
        );

        return $this->outputData($result);
    }
    public function isBindDeivce()
    {
        $result = PropertyUserService::isBindDeivce(
            $this->user,
            $this->request('openid'),
            $this->request('deviceId')
        );

        return $this->outputData($result);
    }
    /*获取设备id*/
    public function qryAllKeys()
    {
        $result = PropertyUserService::qryAllKeys(
            $this->user,
            $this->request('openid'),
            $this->request('mtype',1),
            $this->request('auid'),
            $this->request('districtId')
        );

        return $this->outputData($result);
    }
    /*开门*/
    public function openDoor()
    {
        $result = PropertyUserService::openDoor(
            $this->request('openid'),
            $this->request('ktype'),
            $this->request('ksid'),
            $this->request('auid')
        );

        return $this->outputData($result);
    }

    /*
    * 小区摇一摇开关
    */
    public function shakeswitch(){
        $result = PUService::updateShakeswitch(
            $this->userId,
            $this->request('districtId'),
            $this->request('status')
        );
        if($result==1) $result=['code'=>0,'msg'=>'ok'];
        return $this->output($result);
    }

}

