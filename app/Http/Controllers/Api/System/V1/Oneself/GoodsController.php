<?php 
namespace YiZan\Http\Controllers\Api\System\Oneself;

use YiZan\Services\Oneself\GoodsService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang,DB;

/**
 *  自营商家管理
 */
class GoodsController extends BaseController {
    /**
     * 更新服务
     */
    public function systemUpdate() {
        $result = GoodsService::systemUpdate
        (
            intval($this->request('id')),
            ONESELF_SELLER_ID,
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
            $this->request('systemGoodsId')
        );

        if(intval($this->request('status')) == 0)
        {
            DB::table("shopping_cart")->where('goods_id', intval($this->request('id')))->delete();
        }

        return $this->output($result);
    }
    /**
     * 添加服务
     */
    public function systemAdd() {
        $result = GoodsService::systemSave(
            ONESELF_SELLER_ID,
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
            $this->request('systemGoodsId'),
            $this->request('goodsSn')
        );
        return $this->output($result);
    }

    public function delete(){
        $result = GoodsService::delete(
            $this->request('id'),
            intval($this->request('type')),
            intval($this->request('sellerId'))
        );
        return $this->output($result);
    }
}