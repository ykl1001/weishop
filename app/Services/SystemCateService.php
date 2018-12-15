<?php 
namespace YiZan\Services;

use YiZan\Models\GoodsCate;
use YiZan\Models\SellerCate;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\Seller;
use YiZan\Utils\String;
use DB, Validator, Lang;

class SystemCateService extends BaseService
{
    /**
     * 获取系统行业分类
     * @return array
     */
    public static function getSystemTradeCatesAll(){

        DB::connection()->enableQueryLog();
        $list = SellerCate::select('seller_cate.*')
            ->where('seller_cate.pid',"=","0")
            ->orderBy('seller_cate.sort', 'ASC')
            ->with('childs');
            $list->join('seller_cate_related', function($join)  {
                $join->on('seller_cate_related.seller_id', '=', 0);
            });
        $list = $list->get()->toArray();

        $queries = DB::getQueryLog();

        print_r([$queries]);die;
        return $list;
    }
}
