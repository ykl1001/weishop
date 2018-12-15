<?php 
namespace YiZan\Http\Controllers\Api\System\Proxy;

use YiZan\Services\System\SellerService;
use YiZan\Services\System\ProxyService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 商家营业统计
 */
class StatisticsController extends BaseController{  

    /**
     * 商家营业统计列表
     */
    public function lists()
    {
        $data = ProxyService::getStatisticsList(
            $this->request('name'),
            intval($this->request('month')),
            intval($this->request('year')),
            intval($this->request('cityId')), 
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
        return $this->outputData($data);
    }

    /**
     * 代理商家列表
     */
    public function sellerlists(){
        $data = ProxyService::getSellerListByMonth(
            $this->request('proxyId'),
            intval($this->request('month')),
            intval($this->request('year')), 
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
        return $this->outputData($data);
    }

    /**
     * 商家营业月统计列表
     */
    public function monthlists()
    {
        $data = SellerService::getBusinessListByMonth(
            $this->request('sellerId'),
            intval($this->request('month')),
            intval($this->request('year')),
            intval($this->request('cityId')) 
        );
        
        return $this->outputData($data);
    }

    /**
     * 商家营业天统计列表
     */
    public function daylists()
    {
        $data = SellerService::getBusinessListByDay(
            $this->request('sellerId'),
            $this->request('day'), 
            $this->request('sn'), 
            intval($this->request('status')), 
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
        return $this->outputData($data);
    } 

}

