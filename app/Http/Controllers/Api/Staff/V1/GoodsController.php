<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use Illuminate\Support\Facades\View;
use YiZan\Services\Staff\GoodsService;
use YiZan\Services\SystemGoodsService;
use YiZan\Http\Controllers\Api\Staff\BaseController;
use YiZan\Models\Goods;

/**
 * 服务管理
 */
class GoodsController extends BaseController {

    /**
     * 服务列表
     */
    public function systemGoodslists() {
        $data = SystemGoodsService::getSellerList(
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'),20),
            $this->request('name'),
            $this->request('type',1),
            $this->request('status',1),
            $this->request('systemTagListPid'),
            $this->request('systemTagListId')
        );
        return $this->outputData($data);
    }
    /**
     * 服务列表
     */
    public function systemGoodsEdit() {
        $data = SystemGoodsService::getById(
            $this->request('id')
        );
        return $this->outputData($data);
    }

    /**
     * 服务列表
     */
    public function lists() {
        $data = GoodsService::getLists(
            $this->sellerId,
            max((int)$this->request('page'), 1), 
            $this->request('id'),
            (int)$this->request('status'),
            $this->request('keywords')
        );
		return $this->outputData($data);
    }

    /**
     * 活动商品列表（未选）
     */
    public function activityAllLists() { 
        $data = GoodsService::activityAllLists(
            $this->sellerId,
            $this->request('notIds'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        ); 
        return $this->outputData($data);
    } 

    /**
     * 活动商品列表（已选）
     */
    public function activityLists() { 
        $data = GoodsService::activityLists(
            $this->sellerId,
            $this->request('ids')
        ); 
        return $this->outputData($data);
    } 

    /**
     * 查询已经存在的活动商品
     */
    public function hasActivityGoodsIds() {
        $data = \YiZan\Services\Sellerweb\GoodsService::hasActivityGoodsIds(
            $this->sellerId
        );
        return $this->outputData($data);
    }

    /**
     * 上下架删除操作
     */
    public function op() {
        $data = GoodsService::opGoods(
            $this->sellerId,
            $this->request('ids'),
            (int)$this->request('type')
        );
        return $this->output($data);
    }

    /**
     * 添加编辑服务
     */
    public function edit() {
        $data = GoodsService::goodsUpdate(
            $this->sellerId,
            (int)$this->request('id'),
            (int)$this->request('tradeId'),
            $this->request('name'),
            $this->request('imgs'),
            $this->request('norms'),
            $this->request('brief'),
            $this->request('price'),
            $this->request('duration'),
            $this->request('staffs'),
            $this->request('stock'),
            (int)$this->request('systemTagListPid'),
            (int)$this->request('systemTagListId'),
            (int)$this->request('systemGoodsId'),
            $this->request('goodsSn')
        );
        return $this->output($data);
    }

//    /**
//     * 编辑详情
//     */
//    public function detail(){
//        $sellerId = $this->sellerId;
//        $id = (int)$this->request('id');
//        $data = Goods::where('id', $id)->where('seller_id', $sellerId)->first();
//        if ($data){
//            $data = $data->toArray();
//        } else {
//            $data = [];
//        }
//        View::share('data', $data);
//        return View::make('api.goods.detail');
//    }

    /**
     * 查看详情
     */
    public function detail(){
        $sellerId = $this->sellerId;
        $id = (int)$this->request('id');
        $data = Goods::where('id', $id)->where('seller_id', $sellerId)->with('extend','norms','seller','goodsStaff.staffers','systemTagListPid','systemTagListId')->first();
        if ($data->id){
            $data = $data->toArray();
            foreach($data['goodsStaff'] as $v){
                $data['allStaffId'] .= $v['staffId'].",";
                $data['allStaffName'] .= $v['staffers']['name'].",";
            }
            $data['allStaffId'] = substr($data['allStaffId'], 0, -1);
            $data['allStaffName'] = substr($data['allStaffName'], 0, -1);
            unset($data['goodsStaff']);
        } else {
            $data = [];
        }
        return $this->output($data);
    }
}