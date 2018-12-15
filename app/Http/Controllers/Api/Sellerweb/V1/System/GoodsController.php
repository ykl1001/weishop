<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb\System;

use YiZan\Services\SystemGoodsService;
use YiZan\Http\Controllers\Api\Sellerweb\BaseController;
use Lang;

/**
 * 服务管理
 */
class GoodsController extends BaseController {
    /**
     * 通用服务列表
     */
    public function lists() {
        $data = SystemGoodsService::getSellerList(
            max((int)$this->request('page'), 1),
            (int)$this->request('pageSize', 20),
            $this->request('name'),
            $this->request('type',1),
            $this->request('status',1),
            $this->request('systemTagListPid'),
            $this->request('systemTagListId')
        );
		return $this->outputData($data);
    }
	
	public function oneChannelCk() {
        $data = SystemGoodsService::oneChannelCk(
            (int)$this->request('cateId'),
            (int)$this->request('systemTagListPid'),
            (int)$this->request('systemTagListId'),
            $this->request('ids')
        );
		return $this->output($data);
    }
	
		public function oneChannel() {
        $data = SystemGoodsService::oneChannel(
            $this->sellerId,
            (int)$this->request('cateId'),
            (int)$this->request('systemTagListPid'),
            (int)$this->request('systemTagListId'),
            $this->request('ids')
        );
		return $this->output($data);
    }

    /**
     * 添加服务
     */
    public function create(){
        $result = SystemGoodsService::saveGoods(
            0,
            $this->request('name'),
            (int)$this->request('priceType'), 
            (double)$this->request('price'), 
            (double)$this->request('marketPrice'),
            (int)$this->request('cateId'), 
            $this->request('brief'),
            $this->request('images', []),
            (int)$this->request('duration'),
            (int)$this->request('sort', 100),
            $this->request('cityPrices', [])
        );
        return $this->output($result);
    }
    /**
     * 获取通用服务
     */
    public function get() {
        $data = SystemGoodsService::getById((int)$this->request('id')); 
        $result = $data ? $data->toArray() : []; 
        return $this->outputData($result);
    }

    /**
     * 更新服务
     */
    public function update() {
        $result = SystemGoodsService::saveGoods(
            (int)$this->request('id'),
            $this->request('name'),
            (int)$this->request('priceType'), 
            (double)$this->request('price'), 
            (double)$this->request('marketPrice'),
            (int)$this->request('cateId'), 
            $this->request('brief'),
            $this->request('images', []),
            (int)$this->request('duration'),
            (int)$this->request('sort', 100),
            $this->request('cityPrices', [])
        );
        return $this->output($result);
    }

    /**
     * 删除服务
     */
    public function delete() {
        $result = SystemGoodsService::deleteGoods($this->request('id'));
        return $this->output($result);
    }

    /**
     * 更新服务状态
     */
    public function updateStatus() {
        $result = SystemGoodsService::updateStatus(
                (int)$this->request('id'),
                abs((int)$this->request('status'))
            );
        return $this->output($result);
    }
}