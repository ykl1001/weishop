<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\GoodsService;
use YiZan\Services\GoodsCateService;

class GoodsController extends BaseController {
    /**
     * 服务列表
     */
    public function lists() {
        $data = GoodsService::getSellerGoodsLists(
            $this->userId,
            $this->request('id')
        );
        return $this->outputData($data);
    }

	/**
	 * 服务列表
	 */
	public function lists2() {
		$data = GoodsService::getSellerGoodsLists2(
				$this->userId,
				$this->request('id'),
                $this->request('cateId'),
                max((int)$this->request('page'), 1),
                max((int)$this->request('pageSize'), 10)
			);
		return $this->outputData($data);
	}

    /**
     * 折扣商品
     */
    public function typelists() {
        $data = GoodsService::getTypeGoodsListsDsy(
            $this->userId,
            $this->request('mapPoint'),
            (int)$this->request('sort'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($data);
    }

    /**
     * 服务列表
     */
    public function getlists() {
        $data = GoodsService::getGoodsLists(
            $this->request('id'),
            (int)$this->request('sort'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($data);
    }
    /**
     * 商品 DSY
     */
    public function getGoodsListsDsy() {
        $data = GoodsService::getGoodsListsDsy(
            $this->userId,
            $this->request('id'),
            $this->request('mapPoint'),
            (int)$this->request('sort'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20),
            $this->request('cityId')
        );
        return $this->outputData($data);
    }

    /**
     * 服务列表
     */
    public function setsharenum() {
        $data = GoodsService::setShareNum(
            $this->userId,
            $this->request('shareType'),
            $this->request('shareUserId'),
            $this->request('id')
        );
        return $this->outputData($data);
    }

	public function setbrowse() {
		$code = GoodsService::setBrowse((int)$this->request('goodsId'), $this->userId);
		return $this->outputCode($code);
	}

	/**
	 * 服务详细
	 */
	public function detail() {
        $data = GoodsService::getById((int)$this->request('goodsId'), $this->userId);

        if ($data && $data['status'] == STATUS_ENABLED) {
            if(isset($data['collect'])){
                unset($data['collect']);
                $data['iscollect'] = 1;
            } else {
                unset($data['collect']);
                $data['iscollect'] = 0;
            }
            $data['url'] = u('wap#Goods/appbrief',['goodsId'=>$data['id']]);
            return $this->outputData($data);
        }
		return $this->outputCode(40002);
	}

    /*
     * 服务分类
     */
    public function goodCateList() {
        return $this->outputData(GoodsCateService::wapGetList());
    }
    /*
     * 服务二级分类
     */
    public function goodCateList2()
    {
        return $this->outputData(GoodsCateService::wapGetList2((int)$this->request('cateId')));
    }
    /**
	 * 可预约时间
	 */
	public function appointday() {
		$data = GoodsService::getCanAppointHours(
				(int)$this->request('goodsId'),
				(int)$this->request('duration'),
				(int)$this->request('staffId')
			);
		if (!$data) {
			return $this->outputCode(40001);
		}
		return $this->outputData($data);
	}

	/**
	 * 获取分类商品
	 */
	public function goodstaglists() {
		$data = GoodsService::goodsTagLists(
            intval($this->request('systemListId')),
            $this->request('apoint'),
            intval($this->request('type')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
		return $this->outputData($data);
	}

	public function skus() {
	    $data = GoodsService::skus((int)$this->request('goodsId'));
	    return $this->outputData($data);
    }
}