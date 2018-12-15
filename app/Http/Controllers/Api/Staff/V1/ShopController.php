<?php 
namespace YiZan\Http\Controllers\Api\Staff;
use YiZan\Services\Staff\SellerService;
use YiZan\Services\Staff\StaffService;
use Lang, Validator, View, Input;

class ShopController extends BaseController {
	/**
	 * 获取店铺信息
	 */
	public function info() {
		$result = SellerService::getSellerInfo($this->sellerId);
		return $this->output($result);
	}

    /**
     * 营业时间
     */
    public function time() {
        $result = SellerService::time($this->sellerId);
        return $this->output($result);
    }
    /**
     * 营业时间
     */
    public function savetime() {
        $result = SellerService::savetime($this->sellerId, $this->request('businessHour'));
        return $this->output($result);
    }
    /**
     * 更新店铺信息
     */
    public function edit() {
        $result = SellerService::update(
            $this->sellerId,
            $this->request('shopdatas')
        );
        if($result['code']){
            return $this->output($result);
        }
        return $this->outputData($result);
    }
    /**
     * 是否营业
     */
    public function isStatus() {
        $result = SellerService::isStatus(
            $this->sellerId,
            $this->request('type','status'),
            $this->request('status')
        );
        return $this->output($result);
    }

    /**
     * 货到付款
     */
    public function isDelivery() {
        $result = SellerService::isStatus(
            $this->sellerId,
            $this->request('type','delivery'),
            $this->request('delivery')
        );
        return $this->output($result);
    }

    /**
     * 商家账单
     */
    public function account() {
        $result = SellerService::getSellerAccount(
            $this->sellerId,
            (int)$this->request('type'),
            (int)$this->request('status'),
            max((int)$this->request('page'), 1)
        );
        return $this->output($result);
    }

    public function sellermap() {
        $option = array(
            'address' => strval($this->request('address')),
            'mapPoint' => strval($this->request('mapPoint')),
            'mapPos' => $this->request('mapPos'),
        );
        // var_dump($option);
        // exit;
        $data = SellerService::setSellerMap(
            $this->sellerId,
            $option
        );
        $args = [
            'token' => $this->token,
            'userId' => $this->userId
        ];
        //print_r($data);
        View::share('args', $args);
        View::share('data', $data);
        return View::make('api.seller.index');
    }

    /**
     * 配送人员账单
     */
    public function staffbill() {
        $result = StaffService::getStaffAccount(
            $this->staffId,
            (int)$this->request('type'),
            (int)$this->request('status'),
            max((int)$this->request('page'), 1)
        );
        return $this->output($result);
    }

    /**
     * 获取店铺信息
     */
    public function staffinfo() {
        $result = StaffService::getStaffInfo($this->staffId);
        return $this->output($result);
    }
    
}