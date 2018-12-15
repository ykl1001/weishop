<?php 
namespace YiZan\Http\Controllers\Api\System\System;

use YiZan\Services\SystemGoodsService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang;

/**
 * 服务管理
 */
class GoodsController extends BaseController {
    /**
     * 服务列表
     */
    public function lists() {
        $data = SystemGoodsService::getList(
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20),
            $this->request('name'),
            $this->request('type',1),
            $this->request('status',null)
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
            ONESELF_SELLER_ID,
            (int)$this->request('cateId'),
            (int)$this->request('systemTagListPid'),
            (int)$this->request('systemTagListId'),
            $this->request('ids')
        );
        return $this->output($data);
    }
    /*
     * 自营添加通用商品
     */
    public function getlists(){
        $data = SystemGoodsService::getlists(
            max((int)$this->request('page'), 1),
            (int)$this->request('pageSize', 20),
            $this->request('name'),
            $this->request('type',1),
            $this->request('status',null),
            intval($this->request('systemTagListPid')),
            intval($this->request('systemTagListId'))
        );
        return $this->outputData($data);
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
            $this->request('cityPrices', []),
            $this->request('detail'),
            $this->adminId
        );
        return $this->output($result);
    }
    /**
     * 获取通用服务
     */
    public function get() {
        $data = SystemGoodsService::getById((int)$this->request('id'));
        if ($data) {
            return $this->outputData($data->toArray());
        }
        return $this->outputCode(30214);
    }

    /**
     * 更新服务
     */
    public function update() {
        $result = SystemGoodsService::systemUpdate(
            intval($this->request('id')),
            intval($this->request('sellerId',0)),
            intval($this->request('type',1)),
            $this->request('name'),
            doubleval($this->request('price')),
            $this->request('brief'),
            $this->request('images'),
            intval($this->request('duration')),
            $this->request('unit'),
            intval($this->request('stock')),
            intval($this->request('buyLimit')),
            $this->request('norms'),
            intval($this->request('status')),
            intval($this->request('sort')),
            $this->request('systemTagListPid'),
            $this->request('systemTagListId'),
            $this->request('isSystem')
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
                (int)$this->request('status')
            );
        return $this->output($result);
    }
}