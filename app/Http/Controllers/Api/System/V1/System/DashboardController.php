<?php 
namespace YiZan\Http\Controllers\Api\System\System;

use YiZan\Services\System\SystemConfigService;
use YiZan\Http\Controllers\Api\System\BaseController;
use YiZan\Services\System\UserService;
use YiZan\Services\DistrictService;
use YiZan\Services\SellerService;
use YiZan\Services\OrderService;
use YiZan\Services\GoodsService;
use Input;
/**
 * 系统概况
 */
class DashboardController extends BaseController
{
    /**
     * 网站统计 -> 商城
     */
    public function storeinfo()
    {
        $data['goodsTotal'] = GoodsService::oneselfGoodsCount();
        $data['ordersDay'] = OrderService::storeOrdersCount('day');
        $data['salesAmountDay'] = OrderService::storeSalesAmountSum('day');
        $data['ordersMonth'] = OrderService::storeOrdersCount('month');
        $data['salesAmountMonth'] = OrderService::storeSalesAmountSum('month');


        return $this->outputData($data);
    }
    /**
     * 网站统计 -> 加盟
     */
    public function sellerinfo()
    {
        $data['sellerTotal'] = SellerService::sellercount();
        $data['propertyTotal'] = SellerService::propertycount();
        $data['ordersDay'] = OrderService::ordersCount('day');
        $data['salesAmountDay'] = OrderService::salesAmountSum('day');
        $data['ordersMonth'] = OrderService::ordersCount('month');
        $data['salesAmountMonth'] = OrderService::salesAmountSum('month');

        return $this->outputData($data);
    }
	/**
     * 网站统计 -> 网站
	 */
	public function sysinfo()
    {
        $data['userTotal'] = UserService::count();
        $data['districTotal'] = DistrictService::count();
        return $this->outputData($data);
    }

}