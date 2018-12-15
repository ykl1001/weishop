<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\LiveService;
use Lang, Validator;

/**
 * 活动
 */
class LiveController extends UserAuthController 
{
    public function getcompany(){
        $result = LiveService::getCompany(
            $this->request('name'),
            $this->request('id'),
            $this->request('pid'),
            $this->request('level'),
            $this->request('typepay')
        );
        return $this->outputData($result);
    }

    public function arrearage(){
        $result = LiveService::getArrearage(
            $this->request('provinceName'),
            $this->request('provinceId'),
            $this->request('cityName'),
            $this->request('cityId'),
            $this->request('code'),
            $this->request('unitname'),
            $this->request('account'),
            $this->request('type'),
            $this->request('payProjectId'),
            $this->request('cardid'),
            $this->request('productName')
        );
        return $this->outputData($result);
    }

    public function query(){
        $result = LiveService::getQuery(
            $this->request('provinceId'),
            $this->request('cityId'),
            $this->request('code'),
            $this->request('payProjectId')
        );
        return $this->outputData($result);
    }

    public function order(){
        $result = LiveService::getOrder(
            $this->request('sn')
        );
        return $this->outputData($result);
    }

    public function lists(){
        $result = LiveService::getList(
            $this->userId,
            max((int)$this->request('page'), 1)
        );
        return $this->outputData($result);
    }
}