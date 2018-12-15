<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\SellerService;
use YiZan\Services\SellerCateService;
use YiZan\Services\Buyer\HotWordsService;

use DB, View;
/**
 * 服务人员
 */
class SellerController extends BaseController {
    /**
     * 列表
     */
    public function lists()
    {
        $data = SellerService::getSellerList(
            (int)$this->request('id'),
            max((int)$this->request('page'), 1),
            (int)$this->request('sort'),
            $this->request('keyword'),
            $this->userId,
            $this->request('mapPoint')
        );

        return $this->outputData($data);
    }

    /**
     * 获取某个活动列表 免减活动商家 免运费活动商家...
     * cz
     */
    public function typelists()
    {
        $data = SellerService::getTypeSellerList(
            (int)$this->request('type'),
            max((int)$this->request('page'), 1),
            $this->userId,
            $this->request('mapPoint'),
            $this->request('cityId')
        );

        return $this->outputData($data);
    }

    /**
     * 列表
     */
    public function sellerdatum()
    {
        $data = SellerService::sellerDatum(
            $this->userId
        );

        return $this->outputData($data);
    }
	/*
	 * 搜索商品
	 * caiq 2016-04-01
	 * */
	public function goodslists() 
    {
		$data = SellerService::getGoodsList(
                (int)$this->request('id'),
				max((int)$this->request('page'), 1),
				(int)$this->request('pageSize'),
				(int)$this->request('sort'),
				$this->request('keyword'),
                $this->userId,
                $this->request('mapPoint'),
                $this->request('cityId'),
                $this->request('vsType',''),
                $this->request('sellerId','')
			);
        
		return $this->outputData($data);
	}
	/*
	 * 搜索商品
	 * caiq 2016-04-01
	 * */
	public function sellergoodslists() 
    {
		$data = SellerService::getSellerListByGoodsname(
                (int)$this->request('id'),
				max((int)$this->request('page'), 1),
				(int)$this->request('pageSize'),
				(int)$this->request('sort'),
				$this->request('keyword'),
                $this->userId,
                $this->request('mapPoint'),
                $this->request('cityId')
			);
        
		return $this->outputData($data);
	}
	/*
	 * 热门搜索关键词
	 * caiq 
	 * */
	public function hotsearchkeywords($cityId,$limit=5,$provinceId,$areaId) 
    {
		$data = HotWordsService::getLists($cityId,$limit);
        
		return $this->outputData($data);
	}	
	/**
	 * 详细
	 */
	public function detail() 
    {
        $data = SellerService::getSellerDetail((int)$this->request('id'), $this->userId);
		if (!$data) 
        {
			return $this->outputCode(30001);
		}
        
		return $this->outputData($data);
	}

	public function catelists() {
		$list = SellerCateService::getCatesAll((int)$this->request('id'), (int)$this->request('type'));
        return $this->outputData($list);
	}

	public function getcate() {
        $data = SellerCateService::get((int)$this->request('id'));
        return $this->outputData($data);
    }

    public function hotlists() 
    {
		$data = SellerService::getSellerList(
				(int)$this->request('id', 0),
				max((int)$this->request('page'), 1),
				(int)$this->request('sort', 1),
				$this->request('keyword'),
                $this->userId);
 
        $data = array_slice($data, 0, 5);
     
		return $this->outputData($data);
	}

	public function check(){
		$data = SellerService::checkUser($this->userId);
		return $this->output($data);
	}

	public function reg(){ 
		$result = SellerService::createSeller(
				$this->userId,
				(int)$this->request('sellerType'),
				(int)$this->request('storeType'), 
				$this->request('logo'),
				$this->request('name'),
				$this->request('cateIds'),
				$this->request('address'),
                $this->request('addressDetail'),
                (int)$this->request('provinceId'),
                (int)$this->request('cityId'),
                (int)$this->request('areaId'),
                $this->request('mapPointStr'),
                $this->request('mapPosStr'),
				$this->request('mobile'), 
				$this->request('pwd'), 
				$this->request('idcardSn'),
				$this->request('idcardPositiveImg'),
				$this->request('idcardNegativeImg'),
				$this->request('businessLicenceImg'), 
				$this->request('introduction'),
				$this->request('serviceFee'), 
				$this->request('deliveryFee'),
                $this->request('contacts'),
                $this->request('serviceTel'),
                $this->request('refundAddress')
			);
		// if ($result['code'] == 0) {
		// 	$seller = $result['data'];
		// 	$this->createToken($seller->id, $seller->pwd);
		// 	$seller = $seller->toArray();
		// 	$result['data'] = ['seller' => $seller];
	 //    	$result['token'] = $this->token;
	 //    	$result['sellerId'] = $seller['id'];
		// 	return $this->output($result);
		// }
		return $this->output($result);
	}


    public function mappos() {
    	$data = array(
            'mapPoint' => strval($this->request('mapPoint')),
            'mapPos' => $this->request('mapPos'),
            'mapPosStr' => $this->request('mapPos'),
            'mapPointStr' => strval($this->request('mapPoint')),
        );
    	// var_dump($data);
    	// exit;
    	View::share('data', $data);
        return View::make('api.seller.reg');
    }

}