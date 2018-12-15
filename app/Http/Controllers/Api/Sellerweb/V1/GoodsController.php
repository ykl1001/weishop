<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\GoodsService;
use YiZan\Services\GoodsCateService;
use YiZan\Models\Goods;
use DB;
class GoodsController extends BaseController {
	/**
	 * 服务列表
	 */
	public function lists() { 
		$data = GoodsService::getSystemList(
            $this->sellerId,
            $this->request('type'),
            $this->request('name'),
            (int)$this->request('cateId'),
            $this->request('status'),
            $this->request('notIds'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
		); 
		return $this->outputData($data);
	} 

    /**
     * 活动服务列表
     */
    public function activityLists() { 
        $data = GoodsService::activityLists(
            $this->sellerId,
            $this->request('ids')
        ); 
        return $this->outputData($data);
    } 

    /**
     * 添加商家服务活动
     */
    public function saveActivity() {
        $data = GoodsService::saveActivity(
            $this->sellerId,
            (array)$this->request('full'),
            (array)$this->request('special'),
            $this->request('checkType')
        );
        return $this->output($data);
    }

    /**
     * 查询已经存在的活动商品
     */
    public function hasActivityGoodsIds() {
        $data = GoodsService::hasActivityGoodsIds(
            $this->sellerId
        );
        return $this->outputData($data);
    }

	/**
	 * 服务详细
	 */
	public function get() {
		$data = GoodsService::getGoodsById(
            $this->sellerId, 
            (int)$this->request('goodsId')
        );  
        return $this->outputData($data);
	} 

    /**
     * 添加服务
     */
    public function create()
    {
        $result = GoodsService::systemCreate
        (
            $this->sellerId, 
            $this->seller->type,
            intval($this->request('type')),  
            $this->request('staffIds'),
            $this->request('name'),
            $this->request('price'),
            intval($this->request('cateId')), 
            $this->request('brief'),
            $this->request('images'),
            $this->request('duration'),
            $this->request('unit'), 
            $this->request('stock'),
            intval($this->request('buyLimit')),
            $this->request('norms'),
            intval($this->request('status')),
            intval($this->request('sort')),
            intval($this->request('deductType')),
            $this->request('deductVal', 0),
            $this->request('systemTagListPid'),
            $this->request('systemTagListId'),
            $this->request('systemGoodsId',0),
            $this->request('goodsSn',"")
        );
        
        return $this->output($result);
    }

    /**
     * 更新服务
     */
    public function update()
    {
        $result = GoodsService::systemUpdate
        (
            intval($this->request('id')),
            $this->sellerId, 
            $this->seller->type,
            intval($this->request('type')),  
            $this->request('staffIds'),
            $this->request('name'),
            $this->request('price'),
            intval($this->request('cateId')), 
            $this->request('brief'),
            $this->request('images'),
            $this->request('duration'),
            $this->request('unit'), 
            $this->request('stock'),
            intval($this->request('buyLimit')),
            $this->request('norms'),
            intval($this->request('status')),
            intval($this->request('sort')),
            intval($this->request('deductType')),
            $this->request('deductVal'),
            $this->request('systemTagListPid'),
            $this->request('systemTagListId'),
            $this->request('isSystem')
        );
        
        if(intval($this->request('status')) == 0)
        {
            DB::table("shopping_cart")->where('goods_id', intval($this->request('id')))->delete();
        }
        
        return $this->output($result);
    }
    /**
     * 删除服务
     */
    public function delete()
    {
        $result = GoodsService::deleteGoods(
            $this->sellerId,
            $this->request('id')
        );
        
        return $this->output($result);
    }

    /**
     * 通用更新状态的
     */
    public function updateStatus() {
        $val         = (int)$this->request('val');
        $id          = (int)$this->request('id');
        $result = array (
            'status'    => true,
            'code'      => self::SUCCESS,
            'data'      => $val,
            'msg'       => null
        ); 
        Goods::where('id', $id)
            ->where('seller_id', $this->sellerId)
            ->update(['status' => $val]);
        
        if($val == 0)
        {
            DB::table("shopping_cart")->where('goods_id', $id)->delete();
        }
        
        return $this->output($result);
    }

    /**
     * 菜单审核列表
     */
    public function auditlists() {
        $data = GoodsService::GoodsAuditLists(
            $this->sellerId,
            $this->request('name'),
            $this->request('disposeStatus'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
        
        return $this->outputData($data);
    }

    /**
     * 更改菜品的参与服务
     */
    public function joinService() {
        $result = GoodsService::joinService(
                (int)$this->request('id'),
                (int)$this->request('joinService')
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
     * 上下架
     */
    public function updown() {
        $result = GoodsService::updown(
                (int)$this->request('id'),
                (int)$this->request('status')
            );
        return $this->output($result);
    }

    /**
     * 添加菜品
     */
    public function save() {
        $result = GoodsService::save(
            (int)$this->request('id'),
            (int)$this->sellerId,
            (int)$this->request('typeId'),
            (int)$this->request('restaurantId'),
            $this->request('name'),
            $this->request('image'),
            $this->request('joinService'),
            $this->request('price'),
            $this->request('oldPrice'),
            $this->request('status',0),
            $this->request('sort',100)
        );
        return $this->output($result);
    }
}