<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\SystemGoodsService;
use YiZan\Services\System\GoodsService;
use Lang, Validator;
use DB;

/**
 * 菜品管理
 */
class GoodsController extends BaseController 
{
    /**
     * 菜品列表
     */
    public function lists() {
        $data = GoodsService::getSystemList(
            $this->request('sellerId'),
            $this->request('type'),
            $this->request('name'),
            (int)$this->request('cateId'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
        
		return $this->outputData($data);
    }

    /**
     * 更新菜品
     */
    public function update() {
        
        $result = GoodsService::systemUpdate
        (
            intval($this->request('id')),
            intval($this->request('sellerId')),
            $this->seller->type,
            intval($this->request('type')),  
            $this->request('staffIds'),
            $this->request('name'),
            doubleval($this->request('price')), 
            intval($this->request('cateId')), 
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
            $this->request('systemTagListId')
        );
        
        if(intval($this->request('status')) == 0)
        {
            DB::table("shopping_cart")->where('goods_id', intval($this->request('id')))->delete();
        }
        
        return $this->output($result);
    }

    /**
     * 添加菜品
     */
    public function create() {
        $result = GoodsService::systemSave(
            intval($this->request('sellerId')),
            $this->seller->type,
            intval($this->request('type')),  
            $this->request('staffIds'),
            $this->request('name'),
            doubleval($this->request('price')), 
            intval($this->request('cateId')), 
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
            $this->request('systemGoodstId'),
            $this->request('goodsSn'),
            $this->request('isSystem')
        );
        return $this->output($result);
    }

    /**
     * 菜品列表
     */
    public function goodslist() {
        $data = GoodsService::goodslist(
            (int)$this->request('restaurantId'),
            $this->request('name'),
            $this->request('status'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
        
        return $this->outputData($data);
    }

    /**
     * 菜品搜索
     */
    public function search() {
        $data = GoodsService::searchGoods($this->request('name'), (int)$this->request('sellerId'));
        return $this->outputData($data);
    }

    /**
     * 获取菜品
     */
    public function get() {
        $goods = GoodsService::getSystemGoodsById(intval($this->request('id')));
        return $this->outputData($goods == false ? [] : $goods->toArray());
    }

    /**
     * 删除菜品
     */
    public function delete() {
        $result = GoodsService::deleteSystem(intval($this->request('id')));
        return $this->output($result);
    }

    /**
     * 审核菜品
     */
    public function auditGoods() {
        $result = GoodsService::auditGoods(
                (int)$this->request('id'),
                (int)$this->request('status'),
                (int)$this->request('isSystem'),
                $this->request('disposeResult', ''),
                $this->request('cityPrices', []),
                $this->adminId
            );
        return $this->output($result);
    }

    /**
     * 更改菜品的参与菜品
     */
    public function joinService() {
        $result = GoodsService::joinService(
                (int)$this->request('id'),
                (int)$this->request('joinService')
            );
        return $this->output($result);
    }

    /**
     * 菜品审核
     */
    public function dispose() {
        $result = GoodsService::dispose(
            (int)$this->request('id'),
            $this->request('status'),
            $this->request('remark')
        );
        return $this->output($result);
    }

    /**
     * 服务列表
     */
    public function servicelists() {
        $data = GoodsService::getServiceList(
            (int)$this->request('type'),
            trim($this->request('name')),
            max((int)$this->request('page'), 1),
            (int)$this->request('pageSize', 20)
        );

        return $this->outputData($data);
    }

    /**
     * 增加服务
     */
    public function createservice() {
        $result = GoodsService::saveService(
            0,
            (int)$this->request('type'),
            trim($this->request('name')),
            $this->request('image'),
            $this->request('brief'),
            (int)$this->request('sort')
        );
        return $this->output($result);
    }


    /**
     * 更新服务
     */
    public function updateservice() {
        $result = GoodsService::saveService(
            (int)$this->request('id'),
            (int)$this->request('type'),
            trim($this->request('name')),
            $this->request('image'),
            $this->request('brief'),
            (int)$this->request('sort')
        );
        return $this->output($result);
    }

    /**
     * 删除服务
     */
    public function deleteservice() {
        $result = GoodsService::deleteService($this->request('id'));
        return $this->output($result);
    }

    /**
     * 添加服务
     */
    public function systemAdd() {
        $result = GoodsService::systemSave(
            intval($this->request('sellerId')),
           1,
            intval($this->request('type')),
            $this->request('staffIds'),
            $this->request('name'),
            doubleval($this->request('price')),
            intval($this->request('cateId')),
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
            $this->request('id'),
            $this->request('goodsSn'),
            $this->request('isSystem')

        );
        return $this->output($result);
    }
    public function oneChannel() {
        $data = SystemGoodsService::oneChannel(
            (int)$this->request('sellerId'),
            (int)$this->request('cateId'),
            (int)$this->request('systemTagListPid'),
            (int)$this->request('systemTagListId'),
            $this->request('ids')
        );
        return $this->output($data);
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
}