<?php 
namespace YiZan\Services;

use YiZan\Models\Adv;
use YiZan\Models\SellerCate;
/**
 * 广告管理
 */
class AdvService extends BaseService {
	public static function getAdvByCode($code, $cityId = 0, $sellerCateId = 0){
        $city = $cityId > 0 ? [0,$cityId] : [0];
        $list = Adv::select('adv.*')
            ->join('adv_position', function($join) use($code) {
                $join->on('adv_position.id', '=', 'adv.position_id')
                    ->where('adv_position.code', '=', $code);
            })
            ->whereIn('adv.city_id', $city)
            ->where('adv.status',1)
            ->orderBy('adv.city_id','desc')
            ->orderBy('adv.sort','asc')
            ->orderBy('adv.id','asc');

        if ($code == 'BUYER_SELLER_BANNER') {
            $sellerCates = $sellerCateId > 0 ? [0,$sellerCateId] : [0];
            $sellerCate = SellerCate::where('id', $sellerCateId)->first();
            if ($sellerCate && $sellerCate->pid == 0) {
                $sellerChildCates = SellerCate::where('pid', $sellerCateId)->lists('id');
                if (!empty($sellerChildCates)) {
                    $sellerCates = array_merge($sellerCates, $sellerChildCates);

                }

            }
            $list->whereIn('adv.seller_cate_id', $sellerCates);
        }

        $list = $list->get()->toArray();
        if($code == 'BUYER_INDEX_ADV'){
            foreach($list as $key => &$value)
            {
                if(!empty($value['dataJson'])){
                    $value['dataJson'] = json_decode($value['dataJson'],true);
                }
                foreach($value['dataJson'] as $k=>$val){
                    $value['dataJson'][$k]["icon"] = $val["img"];
                    switch ($value['dataJson'][$k]['type']) {
                        case '1' : $value['dataJson'][$k]["url"] = u('wap#Seller/index', ['id' => $val['arg']]);  break;
                        case '2' : $value['dataJson'][$k]["url"] = ''; break;
                        case '3' : $value['dataJson'][$k]["url"] = u('wap#Goods/detail', ['goodsId' => $val['arg']]); break;
                        case '4' : $value['dataJson'][$k]["url"] = u('wap#Seller/detail', ['id' => $val['arg']]); break;
                        case '5' : $value['dataJson'][$k]["url"] = $val['arg']; break;
                        case '6' : $value['dataJson'][$k]["url"] = u('wap#Goods/detail', ['goodsId' => $val['arg']]); break;
                        case '7' : $value['dataJson'][$k]["url"] = u('wap#Article/detail', ['id' => $val['arg']]); break;
                        case '8' : $value['dataJson'][$k]["url"] = u('wap#UserCenter/signin'); break;
                        case '9' : $value['dataJson'][$k]["url"] = u('wap#Integral/index'); break;
                        case '10' : $value['dataJson'][$k]["url"] = u('wap#Oneself/index'); break;
                        case '11' : $value['dataJson'][$k]["url"] = u('wap#Property/index'); break;
                        case '12' : $value['dataJson'][$k]["url"] = u('wap#Property/livipayment'); break;
                    }
                }
            }
        }else{
            foreach($list as $key => $value)
            {
                $list[$key]["icon"] = $value["image"];
                switch ($value['type']) {
                    case '1' : $list[$key]["url"] = u('wap#Seller/index', ['id' => $value['arg']]);  break;
                    case '2' : $list[$key]["url"] = ''; break;
                    case '3' : $list[$key]["url"] = u('wap#Goods/detail', ['goodsId' => $value['arg']]); break;
                    case '4' : $list[$key]["url"] = u('wap#Seller/detail', ['id' => $value['arg']]); break;
                    case '5' : $list[$key]["url"] = $value['arg']; break;
                    case '6' : $list[$key]["url"] = u('wap#Goods/detail', ['goodsId' => $value['arg']]); break;
                    case '7' : $list[$key]["url"] = u('wap#Article/detail', ['id' => $value['arg']]); break;
                    case '8' : $list[$key]["url"] = u('wap#UserCenter/signin'); break;
                    case '9' : $list[$key]["url"] = u('wap#Integral/index'); break;
                    case '10' : $list[$key]["url"] = u('wap#Oneself/index'); break;
                    case '11' : $list[$key]["url"] = u('wap#Property/index'); break;
                    case '12' : $list[$key]["url"] = u('wap#Property/livipayment'); break;
                }
            }
        }


        return $list;
	}
}
