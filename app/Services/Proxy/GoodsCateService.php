<?php 
namespace YiZan\Services\Proxy;

use YiZan\Models\GoodsCate;
use YiZan\Models\GoodsTag;
use YiZan\Models\Seller;
use YiZan\Models\Goods;
use YiZan\Utils\String;
use DB, Validator;

class GoodsCateService extends \YiZan\Services\GoodsCateService 
{ 
    /**
     * 服务分类列表
     * @param array $proxy 代理
     * @param int $sellerId 商家编号
     * @param int $type 类型
     * @return array          服务分类信息
     */
    public static function getList($proxy, $sellerId, $type) 
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
        $list = GoodsCate::orderBy('goods_cate.sort', "ASC")
                         ->join('seller', function($join) use($data) {
                            $join->on('goods_cate.seller_id', '=', 'seller.id')  
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
        if($type > 0){
            $list->where('goods_cate.type', $type);
        }
        $data = $list->select("goods_cate.*")
                     ->get()
                     ->toArray();
        return $data;
    }

    /** 
     * 获取商家分类
     */
    public static function getSellerCate($proxy, $sellerId, $id){
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
        $cate = GoodsCate::orderBy('goods_cate.sort', "ASC")
                         ->join('seller', function($join) use($data) {
                            $join->on('goods_cate.seller_id', '=', 'seller.id')  
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
                          })
                         ->where('goods_cate.id', $id)
                         ->select("goods_cate.*")
                         ->first();
        return $cate ? $cate->toArray() : [];     
    }
 
}
