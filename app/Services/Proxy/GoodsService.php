<?php 
namespace YiZan\Services\Proxy;

use YiZan\Models\System\Goods;
use YiZan\Models\System\GoodsExtend;
use YiZan\Utils\String; 
use Illuminate\Database\Query\Expression;
use DB, Validator;

class GoodsService extends \YiZan\Services\GoodsService {


	/**
     * 菜品列表
     * @param  array $proxy 代理信息
     * @param  int $sellerId 商家编号
     * @param  int $type Goods类型
     * @param  string goods名称
     * @param  int $cateId 分类编号
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          菜品信息
	 */
	public static function getSystemList($proxy, $sellerId, $type, $name, $cateId, $page, $pageSize)
    {
        $data = [
            'sellerId' => $sellerId 
        ]; 
        if($proxy->pid){
            $parentProxy = Proxy::find($proxy->pid);
        }
        switch ($proxy->level) {
            case '2':
                $data['firstLevel'] = (int)$proxy->pid;
                $data['secondLevel'] = (int)$proxy->id;
                $data['thirdLevel'] = 0;
                break;
            case '3':
                $data['firstLevel'] = (int)$parentProxy->pid;
                $data['secondLevel'] = (int)$parentProxy->id;
                $data['thirdLevel'] = (int)$proxy->id;
                break; 
            default:
                $data['firstLevel'] = (int)$proxy->id;
                $data['secondLevel'] = 0;
                $data['thirdLevel'] = 0;
                break;
        } 

        $list = Goods::orderBy('goods.id', 'desc')
                           ->where('goods.type', $type)
                           ->join('seller', function($join) use($data) {
                                $join->on('goods.seller_id', '=', 'seller.id')  
                                     ->where('seller.id', '=', $data['sellerId']);

                                if($data['firstLevel'] > 0){
                                    $join->where('seller.first_level', '=', $data['firstLevel']);
                                }

                                if($data['second_level'] > 0){
                                    $join->where('seller.second_level', '=', $data['secondLevel']);
                                }

                                if($data['third_level'] > 0){
                                    $join->where('seller.third_level', '=', $data['thirdLevel']);
                                } 
                            });

        if ($cateId == true) {
            $list->where('goods.cate_id', $cateId);
        } 

        if ($name == true) {
            $list->where('goods.name', 'like', '%'.$name.'%');
        } 

        if($status > 0) {
            $list->where('goods.status', $status - 1);
        }

        $totalCount = $list->count();
        
        $list = $list->skip(($page - 1) * $pageSize)
                     ->take($pageSize)
                     ->select('goods.*')
                     ->with('cate','seller')
                     ->get()
                     ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
	}
 
    /**
     * 获取菜品
     * @param  int $id 菜品id
     * @return array   菜品
     */
	public static function getSystemGoodsById($proxy, $id, $sellerId, $type) {
        // DB::connection()->enableQueryLog();  
        $data = [
            'sellerId' => $sellerId 
        ]; 
        if($proxy->pid){
            $parentProxy = Proxy::find($proxy->pid);
        }
        switch ($proxy->level) {
            case '2':
                $data['firstLevel'] = (int)$proxy->pid;
                $data['secondLevel'] = (int)$proxy->id;
                $data['thirdLevel'] = 0;
                break;
            case '3':
                $data['firstLevel'] = (int)$parentProxy->pid;
                $data['secondLevel'] = (int)$parentProxy->id;
                $data['thirdLevel'] = (int)$proxy->id;
                break; 
            default:
                $data['firstLevel'] = (int)$proxy->id;
                $data['secondLevel'] = 0;
                $data['thirdLevel'] = 0;
                break;
        } 

        $goods = Goods::where('goods.type', $type)
                      ->join('seller', function($join) use($data) {
                            $join->on('goods.seller_id', '=', 'seller.id')  
                                 ->where('seller.id', '=', $data['sellerId']);
                                if($data['firstLevel'] > 0){
                                    $join->where('seller.first_level', '=', $data['firstLevel']);
                                }

                                if($data['second_level'] > 0){
                                    $join->where('seller.second_level', '=', $data['secondLevel']);
                                }

                                if($data['third_level'] > 0){
                                    $join->where('seller.third_level', '=', $data['thirdLevel']);
                                } 
                      });

        $goods = $goods->where('goods.id', $id)
                       ->select('goods.*') 
                       ->with('cate')
                       ->first(); 
        // print_r(DB::getQueryLog());exit;
        if($goods == true) {
            $goods->staff_ids = DB::table("goods_staff")
                ->select("seller_staff.id", "seller_staff.name")
                ->join("seller_staff", "seller_staff.id", "=", "goods_staff.staff_id")
                ->where("goods_staff.goods_id", $id)
                ->get();  
        } 
        return $goods; 
	}
 
}
