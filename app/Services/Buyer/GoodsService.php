<?php namespace YiZan\Services\Buyer;

use YiZan\Models\Buyer\GoodsType;
use YiZan\Models\Buyer\Goods;
use YiZan\Models\GoodsCate;
use YiZan\Models\GoodsNorms;
use YiZan\Models\GoodsSkuItem;
use YiZan\Models\GoodsStock;
use YiZan\Models\Order;
use YiZan\Models\Seller;
use YiZan\Models\GoodsExtend;
use YiZan\Models\SellerExtend;
use YiZan\Models\Activity;
use YiZan\Models\ShareLog;
use YiZan\Models\SellerIconRelated;
use YiZan\Models\SellerCate;
use YiZan\Models\ActivityGoods;
use YiZan\Models\ShoppingCart;
use YiZan\Services\ActivityService as baseActivityService;
use YiZan\Services\RegionService as baseRegionService;

use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;

use YiZan\Models\Invitation;
use Illuminate\Database\Query\Expression;
use DB,Cache,Config;
class GoodsService extends \YiZan\Services\GoodsService {

    //商家状态检测
    private static function sellerCheck($id){
        $seller = Seller::find($id);
        if(empty($seller) || $seller->is_check < 1 || $seller->status == 0 ){
            return false;
        }
        return true;
    }


    public static function  setShareNum($userId,$shareType = "goods" ,$shareUserId, $id){
        $bln = false;
        if(!$userId && !$shareUserId && !$id){
            return 0;
        }
        $res = ShareLog::where("goods_id",$id)->where("user_id",$shareUserId)->where("share_type",$shareType)->first();
        if(!$res){
            $data['goods_id'] = $id;
            $data['share_type']  = $shareType;
            $data['user_id']  = $shareUserId;
            $data['create_time']  = UTC_TIME;
            $data['create_day']   = UTC_DAY;
            if(ShareLog::insertGetId($data)){
                $bln = true;
            }
        }else{
            if(Time::toDate(UTC_TIME,'Ymd') > Time::toDate($res->create_time + 86400,'Ymd')){
                $bln = true;
            }else{
                if(Time::toDate(UTC_TIME,'Ymd') >= Time::toDate($res->create_time  + 86400 ,'Ymd')){
                    $update['create_time']  = UTC_TIME;
                    $update['create_day']   = UTC_DAY;
                    ShareLog::where('goods_id',$id)->where("share_type",$shareType)->update($update);
                }
            }
        }
         if($bln){
            if($shareType == "goods"){
				GoodsExtend::where('goods_id',$id)->increment("share_num");
			}else{
				SellerExtend::where('seller_id',$id)->increment("share_num");					
			}
			return 1;
        }
        return 0;
    }
    /**
     * 获取商品类的列表
     * @param [int] $id 商家编号
     */
    public static function  getSellerGoodsLists($userId, $id){

        if(!self::sellerCheck($id)){
            return null;
        }

        //特价商品活动
        $activity = baseActivityService::getSellerActivity($id);
        $activity = $activity['special'];
        //检索当天特价活动次数是否已使用完毕
        if(!empty($activity)){
            $activity = \YiZan\Services\Buyer\ActivityService::deleteSpecial($userId, $activity);
        }

        $list = GoodsCate::where('seller_id', $id)
            //->where('seller_id','<>', ONESELF_SELLER_ID)
            ->where('type', Goods::SELLER_GOODS)
            ->where('status', 1)
            ->with(['goods' => function($query) use($id) {
                $query->where('seller_id', $id)
                    ->where('status', 1)
                    ->orderBy('sort', 'asc');
            },'goods.norms' => function($query) use ($id){
                $query->where('seller_id', $id);
            },'goods.extend'])
            ->orderBy('sort','asc')->get()->toArray();



        foreach ($list as $key=>$val) {
            foreach ($list[$key]['goods'] as $k=>$v) {
                $list[$key]['goods'][$k]['salesCount'] = 0;
                if (!empty($v['extend'])) {
                    $list[$key]['goods'][$k]['salesCount'] = $v['extend']['salesVolume'];
                }
                $list[$key]['goods'][$k]['activity'] = $activity[$v['id']];
                unset($list[$key]['goods'][$k]['extend']);
            }
        }

        return $list;
    }



    /**
     * 获取商品类的列表
     * @param [int] $id 商家编号
     */
    public static function  getSellerGoodsLists2($userId, $id, $cateId,$page,$pageSize){
        if(!self::sellerCheck($id)){
            return null;
        }

        //特价商品活动
        $activity = baseActivityService::getSellerActivity($id);
        $activity = $activity['special'];
        //检索当天特价活动次数是否已使用完毕
        if(!empty($activity)){
            $activity = \YiZan\Services\Buyer\ActivityService::deleteSpecial($userId, $activity);
        }

        $list = Cache::get('GoodsCate_'.$id);
        if(empty($list)){
            $list = GoodsCate::where('seller_id', $id)
                ->where('type', Goods::SELLER_GOODS)
                ->where('status', 1)
                ->orderBy('sort','asc')
                ->get()
                ->toArray();
            Cache::put('GoodsCate_'.$id, $list, 1);
        }

        $cateId = !empty($cateId) ? $cateId : $list[0]['id'];

        foreach ($list as $key=>$val) {
            $list[$key]['goodscounts'] = Goods::where('cate_id',$val['id'])
                ->where('seller_id',$id)
                ->where('status',1)
                ->count();
            if($list[$key]['id'] == $cateId){
                $list[$key]['goods'] = Goods::where('cate_id',$cateId)
                    ->where('seller_id',$id)
                    ->where('status',1)
                    ->with('extend')
                    ->orderBy('sort','asc')
                    ->skip(($page - 1) * $pageSize)
                    ->take($pageSize)
                    ->get()
                    ->toArray();
                if(!empty($list[$key]['goods'])){
                    foreach ($list[$key]['goods'] as $k=>$v) {
                        $list[$key]['goods'][$k]['salesCount'] = 0;
                        if (!empty($v['extend'])) {
                            $list[$key]['goods'][$k]['salesCount'] = $v['extend']['salesVolume'];
                        }
                        $list[$key]['goods'][$k]['activity'] = $activity[$v['id']];
                        unset($list[$key]['goods'][$k]['extend']);
                    }
                }
            }
        }
        return $list;
    }

    /**
     * 获取服务类的列表
     * @param [int] $id 商家编号
     */
    public static function getSellerServiceLists($userId, $id,$cateId = '',$page, $pageSize){
        if(!self::sellerCheck($id)){
            return null;
        }

        //特价商品活动
        $activity = baseActivityService::getSellerActivity($id);
        $activity = $activity['special'];
        //检索当天特价活动次数是否已使用完毕
        if(!empty($activity)){
            $activity = \YiZan\Services\Buyer\ActivityService::deleteSpecial($userId, $activity);
        }
        $list = Goods::where('seller_id', $id)
            ///->where('seller_id','<>', ONESELF_SELLER_ID)
            ->where('type', Goods::SELLER_SERVICE)
            ->where('status', 1)
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        foreach ($list as $k=>$v) {
            $list[$k]['duration'] = $v['unit'] == 1 ? $v['duration'] * 60 : $v['duration'];
            $list[$k]['activity'] = $activity[$v['id']];
        }
        return $list;
    }

    /**
     * 根据编号获取服务
     * @param  integer $goodsId 服务编号
     * @param  integer $userId  会员编号
     * @return array            服务信息
     */
    public static function getById($goodsId, $userId = 0) {
        $goods = Goods::with('extend', 'stockGoods', 'seller');
        //->where('seller_id','<>', ONESELF_SELLER_ID);
        if ($userId > 0) {
            $goods->with(['collect' => function($query) use($userId) {
                $query->where('user_id', $userId);
            }]);
        }
        $goods = $goods->find($goodsId);

        if(!self::sellerCheck($goods->seller_id)){
            return null;
        }
        $isAllUserPrimary = Invitation::where("user_status",1)->pluck('is_all_user_primary') / 100;
        if ($goods) {
            if($goods->seller->store_type == 1){
                $goods->seller->province = RegionService::getById($goods->seller->province_id);//省
                $goods->seller->city = RegionService::getById($goods->seller->city_id);//市
                $goods->seller->area = RegionService::getById($goods->seller->area_id);//区

                $goods->sellerAuthIcon = SellerIconRelated::where('seller_id', $goods->seller->id)->with('icon')->get()->toArray();
                if($goods->extend->sales_volume > 10000){
                    $goods->extend->sales_volume = round($goods->extend->sales_volume/10000,1).'万';
                }
                if($goods->extend->share_num > 10000){
                    $goods->extend->share_num = round($goods->extend->share_num/10000,1).'万';
                }
                if($isAllUserPrimary > 0){
                    $goods->is_all_user_primary = $isAllUserPrimary;//round($isAllUserPrimary * $goods->price,2);
                }
            }

            $goods = $goods->toArray();
            $goods['salesCount'] = !empty($goods['extend']) ? $goods['extend']['salesVolume'] : 0;
        } else {
            $goods = [];
        }
        return $goods;
    }
    /**
     * 获取服务详情
     * @param int $id 服务编号
     * @param int $type 服务类型（为空则为跑腿和家政 直接查ID）
     */
    public static function getServiceDetail($id) {
        $data = Goods::where('id', $id)
            ->where('status', '1')
            // ->where('seller_id', '0')
            ->selectRaw("*, (select count(1) from ".env('DB_PREFIX')."order as o where o.goods_id = ".env('DB_PREFIX')."goods.id) as saleCount,
                                    (select count(1) from ".env('DB_PREFIX')."order_rate as r where r.goods_id = ".env('DB_PREFIX')."goods.id) as commentCount")
            ->first();
        return $data;
    }

    /**
     * 检查商品是否被限制购买
     * @param  Object $goods     商品
     * @param  int    $normsId    规格编号
     * @param  int    $userId    会员编号
     * @param  int    $amount    数量
     * @return boolean           是否可以购买 返回true表示可以买，返回false表示不可以买
     */
    public static function checkGoodsLimit($goods, $skuSn, $userId, $amount){
        DB::connection()->enableQueryLog();
        if(($goods->buy_limit <= 0 && $goods->type == 1) || $goods->type == 2){
            return true;
        }
        $data = [
            'goodsId' => $goods->id,
            'skuSn' => $skuSn
        ];
        $dbPrefix = DB::getTablePrefix();
        //获取当前商品的总的已购买数量
        $num = Order::where('user_id', $userId)
            ->whereNotIn('status', [ORDER_STATUS_CANCEL_USER,
                ORDER_STATUS_CANCEL_AUTO,
                ORDER_STATUS_CANCEL_SELLER,
                ORDER_STATUS_CANCEL_ADMIN,
                ORDER_STATUS_USER_DELETE,
                ORDER_STATUS_SELLER_DELETE,
                ORDER_STATUS_ADMIN_DELETE,
                ORDER_STATUS_REFUND_SUCCESS])
            ->join('order_goods', function($join) use($data) {
                $join->on('order_goods.order_id', '=', 'order.id')
                    ->where('order_goods.sku_sn', '=', $data['skuSn'])
                    ->where('order_goods.goods_id', '=', $data['goodsId']);
            })
            ->select(DB::raw('IFNULL(sum('.$dbPrefix.'order_goods.num), 0) as total_num'))
            ->first();
        //file_put_contents('/mnt/wwwroot/o2o/storage/logs/ccccc.log', print_r(DB::getQueryLog(), true), FILE_APPEND); 
        if($num) {
            $num = $num->toArray();
            $current_amount = $num['totalNum'] + $amount;

        } else {
            $current_amount = $amount;
        }

        if($current_amount > $goods->buy_limit) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * [goodsTagLists 通过分类标签获取商品]
     * @param  [type] $systemListId [二级分类编号]
     * @param  [type] $type         [排序类型：1=价格由低到高 2=距离由低到高]
     * @param  [type] $page         [description]
     * @param  [type] $pageSize     [description]
     * @return [type]               [description]
     */
    public static function goodsTagLists($systemListId, $apoint, $type, $page, $pageSize) {
        $apoint = $apoint == '' ? '0 0' : str_replace(',', ' ', $apoint);
        $lists = Goods::where('system_tag_list_id', $systemListId)
            ->where('status', 1)
            ->whereIn('seller_id', function($query) use ($apoint){
                $query->select('id')
                    ->from('seller')
                    ->whereRaw(" ST_Contains(map_pos,GeomFromText('Point({$apoint})'))")
                    ->where('status', 1)
                    ->where('is_check', 1)
                    ->orWhere("seller_id",ONESELF_SELLER_ID)
                    ->orWhere("store_type",1);
            });


        if($type == 1)
        {
            //按价格
            $lists = $lists->orderBy('price', 'ASC');
        }
        else
        {
            //按距离
            $lists = $lists->with(['seller' => function($query)
            {
                $query->addSelect(DB::raw("ST_Distance(".env('DB_PREFIX')."seller.map_point,GeomFromText('POINT({$apoint})')) AS map_distance"));
                $query->orderBy('map_distance', 'ASC');

            }]);
        }

        $lists = $lists->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('seller')
            ->get()
            ->toArray();
        return $lists;
    }

    /**
     * 获取商品类的列表
     * @param [int] $id 商家编号
     */
    public function getRecommendZG($page,$sellerId,$orderBy,$noIndex,$cateId,$cityId,$mapPoint){
        if($page == 10 && $noIndex = 0){
            return '';
        }

        $mapPoint = $mapPoint == '' ? '0 0' : str_replace(',', ' ', $mapPoint);
            $count = Goods::where('type', Goods::SELLER_GOODS)
                ->join('goods_extend', 'goods_extend.goods_id', '=', 'goods.id')
                ->where('status', 1);

            if($sellerId > 0){
                $count = $count->where('goods.seller_id',$sellerId)->count();
            }else{
                $count = $count->count();
            }

            $pagesource = ceil($count/20);

            if(!empty($sellerId)){
                if($page > $pagesource){
                    return '';
                }
            }
            $lastnum = $count%20;

            if($orderBy && $sellerId != 0){ //有商家 有排序进来
                $desc = 'desc';
                if($orderBy == 'pricedesc' || $orderBy == 'priceasc'){
                    $desc = $orderBy == 'pricedesc' ? 'desc' : 'asc';
                    $orderBy = 'goods.price';
                }else if($orderBy == 'sales_volume'){
                    $orderBy = 'goods_extend.sales_volume';
                }else if($orderBy == 'money'){
                    $desc =  'desc';
                    $orderBy = 'goods.price';
                }

                $list = Goods::where('type', Goods::SELLER_GOODS)
                    ->join('goods_extend', 'goods_extend.goods_id', '=', 'goods.id')
                    ->where('status', 1);
                if(!empty($cateId)){
                    $list->where('cate_id',$cateId);
                }
                $list = $list->where('goods.seller_id',$sellerId)
                    ->skip(($page - 1) * 20)
                    ->take(20)
                    ->orderBy($orderBy,$desc)
                    ->get();

                if(!empty($list)){
                    $list = $list->toArray();
                }else{
                    return '';
                }
            }else{
                $hours = Time::toDate(UTC_TIME, 'H:i');
                $week = Time::toDate(UTC_TIME, 'w');
                $limit = ($page-1)*10;

                if($sellerId > 0){
                    $listId = Goods::where('type', Goods::SELLER_GOODS)
                        ->join('goods_extend', 'goods_extend.goods_id', '=', 'goods.id')
                        ->where('goods.status', 1)
                        ->where('goods.seller_id',$sellerId);
                    if(!empty($cateId)){
                        $listId->where('cate_id',$cateId);
                    }
                }else{
                    $listIdsql = 'SELECT
                                    `'.env("DB_PREFIX").'goods`.`id`
                                FROM
                                    `'.env("DB_PREFIX").'goods`
                                INNER JOIN `'.env("DB_PREFIX").'goods_extend` ON `'.env("DB_PREFIX").'goods_extend`.`goods_id` = `'.env("DB_PREFIX").'goods`.`id`
                                INNER JOIN `'.env("DB_PREFIX").'seller` ON `'.env("DB_PREFIX").'seller`.`id` = `'.env("DB_PREFIX").'goods`.`seller_id`
                                                AND `'.env("DB_PREFIX").'seller`.`status` = 1
                                                AND `'.env("DB_PREFIX").'seller`.`is_check` = 1
                                LEFT JOIN `'.env("DB_PREFIX").'staff_service_time` ON `'.env("DB_PREFIX").'staff_service_time`.`seller_id` = `'.env("DB_PREFIX").'goods`.`seller_id`
                                                AND `week` = '.$week.'
                                                AND `begin_time` <= "'.$hours.'"
                                                AND (`end_time` >= "'.$hours.'" or `end_time` = "00:00")
                                WHERE `'.env("DB_PREFIX").'goods`.`type` = 1
                                                AND `'.env("DB_PREFIX").'goods`.`status` = 1
                                                AND ('.env("DB_PREFIX").'seller.store_type = 1 OR ('.env("DB_PREFIX").'seller.store_type = 0 AND `'.env("DB_PREFIX").'staff_service_time`.service_time_id IS NOT NULL))
												AND ('.env("DB_PREFIX").'seller.store_type = 1 OR ('.env("DB_PREFIX").'seller.store_type = 0 AND '.env("DB_PREFIX").'seller.city_id = '.$cityId.' OR '.env("DB_PREFIX").'seller.province_id = '.$cityId.') )
												AND ('.env("DB_PREFIX").'seller.store_type = 1 OR ('.env("DB_PREFIX").'seller.store_type = 0 AND ST_Contains('.env("DB_PREFIX").'seller.map_pos,GeomFromText("Point('.$mapPoint.')"))))   /*查询在范围内的商家 */
                                ORDER BY
                                    `'.env("DB_PREFIX").'goods_extend`.`sales_volume` DESC
                                LIMIT '.$limit.',10';
                }

                if($pagesource == $page){
                    if($sellerId > 0) {
                        //最后一次
                        $list =  $listId->skip(($page - 1) * 10)
                            ->take($lastnum)
                            ->orderBy('goods_extend.sales_volume','desc')
                            ->get();
                        if(!empty($list)){
                            $list = $list->toArray();
                            $orderBy = 1;
                        }else{
                            return '';
                        }
                    }else{
                        return '';
                    }
                }else{
                    if($sellerId > 0) {
                        $listId = $listId->skip(($page - 1) * 10)
                            ->take(10)
                            ->orderBy('goods_extend.sales_volume', 'desc')
                            ->lists('id');
                    }else{
                        $listId = Cache::get('listId_'.$limit);
                        if(empty($listId)){
                            $listId = DB::select($listIdsql);
                            $listId = json_decode(json_encode($listId),true);
                            $new_listId = [];
                            foreach($listId as $k=>$v){
                                $new_listId[$k] = $v['id'];
                            }
                            $listId = $new_listId;
                            Cache::put('listId_'.$limit,$listId,5);
                        }
                    }

                    if(!empty($listId)){
                        $listIdstring = implode(',',$listId);
                        $listIdstring = rtrim($listIdstring,',');
                    }else{
                        return '';
                    }

                    if(!empty($sellerId)){
                        if(!empty($cateId)){
                            $sql = '(SELECT g.*,g_e.*,g_e.sales_volume as salesVolume FROM '.env("DB_PREFIX").'goods AS g
                    INNER JOIN '.env("DB_PREFIX").'goods_extend AS g_e ON g_e.goods_id = g.id
                    where g.type = 1 AND g.seller_id = '.$sellerId.' AND g.cate_id = '.$cateId.' AND g.status = 1 ORDER BY g_e.sales_volume DESC LIMIT '.$limit.',10)
                    UNION
                    (SELECT g.*,g_e.*,g_e.sales_volume as salesVolume FROM '.env("DB_PREFIX").'goods AS g
                    INNER JOIN '.env("DB_PREFIX").'goods_extend AS g_e ON g_e.goods_id = g.id
                    WHERE g.type = 1 AND g.seller_id = '.$sellerId.' AND g.cate_id = '.$cateId.' AND g.id NOT IN ('.$listIdstring.' ) AND g.status = 1 ORDER BY g_e.sales_volume ASC,g.id DESC LIMIT '.$limit.',10)';
                        }else{
                            $sql = '(SELECT g.*,g_e.*,g_e.sales_volume as salesVolume  FROM '.env("DB_PREFIX").'goods AS g
                    INNER JOIN '.env("DB_PREFIX").'goods_extend AS g_e ON g_e.goods_id = g.id
                    where g.type = 1 AND g.seller_id = '.$sellerId.' AND g.status = 1 ORDER BY g_e.sales_volume DESC LIMIT '.$limit.',10)
                    UNION
                    (SELECT g.*,g_e.*,g_e.sales_volume as salesVolume FROM '.env("DB_PREFIX").'goods AS g
                    INNER JOIN '.env("DB_PREFIX").'goods_extend AS g_e ON g_e.goods_id = g.id
                    WHERE g.type = 1 AND g.seller_id = '.$sellerId.' AND g.id NOT IN ('.$listIdstring.' ) AND g.status = 1 ORDER BY g_e.sales_volume ASC,g.id DESC LIMIT '.$limit.',10)';
                        }

                        $list = DB::select($sql);
                        $list = json_decode(json_encode($list),true);
                    }else{
                        $sql = '(SELECT g.*,g_e.*,s.name as name2,g_e.sales_volume as salesVolume,s.store_type,s.address,s.province_id,s.city_id FROM '.env("DB_PREFIX").'goods AS g
                    INNER JOIN '.env("DB_PREFIX").'goods_extend AS g_e ON g_e.goods_id = g.id INNER JOIN '.env("DB_PREFIX").'seller AS s ON s.id = g.seller_id
                        AND `s`.`status` = 1
                        AND `s`.`is_check` = 1
                    LEFT JOIN `yz_staff_service_time` ON `yz_staff_service_time`.`seller_id` = g.`seller_id`
                        AND `week` = '.$week.'
                        AND `begin_time` <= "'.$hours.'"
                        AND (`end_time` >= "'.$hours.'" or `end_time` = "00:00")
                    WHERE
                            g.type = 1
                        AND g.status = 1
                        AND (s.store_type = 1 OR (s.store_type = 0 AND `yz_staff_service_time`.service_time_id IS NOT NULL))
                    ORDER BY g_e.sales_volume DESC LIMIT '.$limit.',10)
                    UNION
                    (SELECT g.*,g_e.*,s.name as name2,g_e.sales_volume as salesVolume,s.store_type,s.address,s.province_id,s.city_id FROM '.env("DB_PREFIX").'goods AS g
                    INNER JOIN '.env("DB_PREFIX").'goods_extend AS g_e ON g_e.goods_id = g.id INNER JOIN '.env("DB_PREFIX").'seller AS s ON s.id = g.seller_id
                        AND `s`.`status` = 1
                        AND `s`.`is_check` = 1
                    LEFT JOIN `yz_staff_service_time` ON `yz_staff_service_time`.`seller_id` = g.`seller_id`
                        AND `week` = '.$week.'
                        AND `begin_time` <= "'.$hours.'"
                        AND (`end_time` >= "'.$hours.'" or `end_time` = "00:00")
                    WHERE
                            g.type = 1
                        AND g.status = 1
                        AND (s.store_type = 1 OR (s.store_type = 0 AND `yz_staff_service_time`.service_time_id IS NOT NULL))
                        AND (s.store_type = 1 OR (s.store_type = 0 AND s.city_id = '.$cityId.' OR s.province_id = '.$cityId.') )
                        AND (s.store_type = 1 OR (s.store_type = 0 AND ST_Contains(s.map_pos,GeomFromText("Point('.$mapPoint.')"))))   /*查询在范围内的商家 */
                        AND g.id NOT IN ('.$listIdstring.' )
                    ORDER BY g_e.sales_volume ASC,g.id DESC LIMIT '.$limit.',10)';

                        $list = Cache::get('list_'.$limit.'_'.$cityId);
                        if(empty($list)){
                            $list = DB::select($sql);
                            $list = json_decode(json_encode($list),true);
                            Cache::put('list_'.$limit.'_'.$cityId,$list,5);
                        }
                    }
                }
            }

        $isAllUserPrimary = Invitation::where("user_status",1)->pluck('is_all_user_primary') / 100;
            $zi_sellerId = ONESELF_SELLER_ID;
            if(empty($sellerId)){
                foreach ($list as $key=>$val) {
                    $list[$key]['stype'] = $val['seller_id'] == $zi_sellerId ? 1 : 0;
                    $list[$key]['province'] = baseRegionService::getById($val['province_id']);
                    $list[$key]['city'] = baseRegionService::getById($val['city_id']);
                    if(strstr($val['images'],',') == true){
                        $images = explode(',',$val['images']);
                        $list[$key]['images'] = $images[0];
                    }
                    if($isAllUserPrimary > 0){
                        $list[$key]['isAllUserPrimary'] = round($isAllUserPrimary * $val['price'],2);
                    }
                    $list[$key]['shareNum'] = $list[$key]['share_num'] = GoodsExtend::where("goods_id",$val['id'])->pluck("share_num");
                    $list[$key]['image'] = $images;
                }

                shuffle($list);
            }else{
                $seller = Seller::where('id',$sellerId)->first();
                if(!empty($seller)){
                    $seller = $seller->toArray();
                    $province = baseRegionService::getById($seller['provinceId']);
                    $city = baseRegionService::getById($seller['cityId']);
                    foreach ($list as $key=>$val) {
                        $list[$key]['stype'] = $sellerId == $zi_sellerId ? 1 : 0;
                        $list[$key]['province'] = $province;
                        $list[$key]['address'] = $seller['address'];
                        $list[$key]['store_type'] = $seller['storeType'];

                        $list[$key]['city'] = $city;
                        if(!empty($orderBy)){
                            $list[$key]['images'] = $val['images'][0];
                        }else{
                            if(strstr($val['images'],',') == true){
                                $images = explode(',',$val['images']);
                                $list[$key]['images'] = $images[0];
                            }
                        }
                        if($isAllUserPrimary > 0){
                            $list[$key]['isAllUserPrimary'] = round($isAllUserPrimary *  $val['price'],2);
                        }
                        $list[$key]['shareNum'] = $list[$key]['share_num'] = GoodsExtend::where("goods_id",$val['id'])->pluck("share_num");
                        $list[$key]['image'] = $images;

                    }
                }
            }
            return $list;
        }

    /**
     * 获取商品类的列表
     * @param [int] $id 商家编号
     */
    public static function getRecommendGoods($page,$sellerId,$orderBy,$noIndex,$cateId,$cityId,$mapPoint){
        $is_zg = Config::get('app.is_zg');
        if($is_zg === true){
            $list = self::getRecommendZG($page,$sellerId,$orderBy,$noIndex,$cateId,$cityId,$mapPoint);
            return $list;
        }

        if($page == 10 && $noIndex = 0){
            return '';
        }
        $mapPoint = $mapPoint == '' ? '0 0' : str_replace(',', ' ', $mapPoint);
        if($sellerId != 0){ //有商家 有排序进来
            $desc = 'desc';
            if($orderBy == 'pricedesc' || $orderBy == 'priceasc'){
                $desc = $orderBy == 'pricedesc' ? 'desc' : 'asc';
                $orderBy = 'goods.price';
            }else if($orderBy == 'sales_volume'){
                $orderBy = 'goods_extend.sales_volume';
            }else if($orderBy == 'money'){
                $desc =  'desc';
                $orderBy = 'goods.price';
            }

            $list = Goods::where('type', Goods::SELLER_GOODS)
                ->join('goods_extend', 'goods_extend.goods_id', '=', 'goods.id')
                ->where('status', 1);
            if(!empty($cateId)){
                $list->where('cate_id',$cateId);
            }
            $list = $list->where('goods.seller_id',$sellerId)
                ->skip(($page - 1) * 20)
                ->take(20)
                ->orderBy($orderBy,$desc)
                ->get();

            if(!empty($list)){
                $list = $list->toArray();
            }else{
                return '';
            }
        }else{
            $hours = Time::toDate(UTC_TIME, 'H:i');
            $week = Time::toDate(UTC_TIME, 'w');
            $limit = ($page-1)*20;

            $sql = '(SELECT g.*,g_e.*,s.name as name2,g_e.sales_volume as salesVolume,s.store_type,s.address,s.province_id,s.city_id FROM '.env("DB_PREFIX").'goods AS g
            INNER JOIN '.env("DB_PREFIX").'goods_extend AS g_e ON g_e.goods_id = g.id INNER JOIN '.env("DB_PREFIX").'seller AS s ON s.id = g.seller_id
                AND `s`.`status` = 1
                AND `s`.`is_check` = 1
            LEFT JOIN `'.env("DB_PREFIX").'staff_service_time` ON `'.env("DB_PREFIX").'staff_service_time`.`seller_id` = g.`seller_id`
                AND `week` = '.$week.'
                AND `begin_time` <= "'.$hours.'"
                AND (`end_time` >= "'.$hours.'" or `end_time` = "00:00")
            WHERE
                    g.type = 1
                AND g.status = 1
                AND (s.store_type = 1 OR (s.store_type = 0 AND `'.env("DB_PREFIX").'staff_service_time`.service_time_id IS NOT NULL))
                AND (s.store_type = 1 OR (s.store_type = 0 AND s.city_id = '.$cityId.' OR s.province_id = '.$cityId.') )
                AND (s.store_type = 1 OR (s.store_type = 0 AND ST_Contains(s.map_pos,GeomFromText("Point('.$mapPoint.')"))))   /*查询在范围内的商家 */

            ORDER BY g_e.sales_volume DESC LIMIT '.$limit.',20)';

            $list = Cache::get('list_'.$limit.'_'.$cityId);
            if(empty($list)){
                $list = DB::select($sql);
                $list = json_decode(json_encode($list),true);
                Cache::put('list_'.$limit.'_'.$cityId,$list,5);
            }
        }

//        $isAllUserPrimary = Invitation::where("user_status",1)->pluck('is_all_user_primary') / 100;
        $zi_sellerId = ONESELF_SELLER_ID;
        if(empty($sellerId)){
            foreach ($list as $key=>$val) {
                $list[$key]['stype'] = $val['seller_id'] == $zi_sellerId ? 1 : 0;
                $list[$key]['province'] = baseRegionService::getById($val['province_id']);
                $list[$key]['city'] = baseRegionService::getById($val['city_id']);
                if(strstr($val['images'],',') == true){
                    $images = explode(',',$val['images']);
                    $list[$key]['images'] = $images[0];
                }
                $list[$key]['image'] = $val['images'];
//                if($isAllUserPrimary > 0){
//                    $list[$key]['isAllUserPrimary'] = round($isAllUserPrimary * $val['price'],2);
//                }
                $list[$key]['salePrice'] = ActivityGoods::where("goods_id",$val['id'])->pluck("sale_price");
            }
            shuffle($list);
        }else{
            $seller = Seller::where('id',$sellerId)->first();
            if(!empty($seller)){
                $seller = $seller->toArray();
                $province = baseRegionService::getById($seller['provinceId']);
                $city = baseRegionService::getById($seller['cityId']);
                foreach ($list as $key=>$val) {
                    $list[$key]['stype'] = $sellerId == $zi_sellerId ? 1 : 0;
                    $list[$key]['province'] = $province;
                    $list[$key]['address'] = $seller['address'];
                    $list[$key]['store_type'] = $seller['storeType'];
                    $list[$key]['share_num'] = $list[$key]['shareNum'];
                    $list[$key]['sales_volume'] = $list[$key]['salesVolume'];
                    if($seller['storeType'] == 1){
//                        if($isAllUserPrimary > 0){
//                            $list[$key]['isAllUserPrimary'] = round($isAllUserPrimary * $val['price'],2);
//                        }
                    }else{
                        $list[$key]['salePrice'] = ActivityGoods::where("goods_id",$val['id'])->pluck("sale_price");
                    }

                    $list[$key]['city'] = $city;
                    if(!empty($orderBy)){
                        $list[$key]['images'] = $val['images'][0];
                    }else{
                        if(strstr($val['images'],',') == true){
                            $images = explode(',',$val['images']);
                            $list[$key]['images'] = $images[0];
                        }
                    }
                    $list[$key]['image'] = $val['images'];
                }
            }
        }
        return $list;
    }
    /**
     * 获取商品类的列表
     * @param [int] $id 分类Id
     */
    public static function  getGoodsLists($id,$sort,$page,$pageSize){


        /*
         *   switch ($sort)
                    {
                        case 1: // 距离
                            $sort = ['distance' => 'desc'];
                            break;
                        case 2: // 价格
                            $sort = ['price' => 'desc'];
                            break;
                        case 3: // 销量
                            $sort = ['sales_volume' => 'desc'];
                            break;
                    }
         */
        $list = GoodsCate::where('trade_id', $id)
            ->where('seller_id','<>', ONESELF_SELLER_ID)
            ->where('type', Goods::SELLER_GOODS)
            ->where('status', 1)
            ->with(['goods' => function($query) use($sort) {
                $query->where('status', 1);
                switch ($sort)
                {
                    case 3: // 价格
                        $query->orderBy('price', 'desc');
                        break;
                    case 4: // 价格
                        $query->orderBy('price', 'asc');
                        break;
                }
            },'goods.norms' => function($query){
                $query->where('seller_id', "goods_cate.seller_id");
            },'goods.extend' => function($query) use($sort){
                switch ($sort)
                {
                    case 1: // 销量
                        $query->orderBy('sales_volume', 'desc');
                        break;
                }
            },'goods.seller'])
            ->orderBy('sort','asc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();

        $isAllUserPrimary = Invitation::where("user_status",1)->pluck('is_all_user_primary') / 100;


        foreach ($list as $key=>$val) {
            foreach ($val['goods'] as $k=>$v) {
                if( $v['seller']['storeType']){
                    $list[$key]['goods'][$k]['province'] = baseRegionService::getById($v['seller']['provinceId']);
                    $list[$key]['goods'][$k]['city'] = baseRegionService::getById($v['seller']['cityId']);
                }
                $list[$key]['goods'][$k]['salePrice'] = ActivityGoods::where("goods_id",$v['id'])->pluck("sale_price");
                $list[$key]['goods'][$k]['storeType'] = $v['seller']['storeType'];
                if($isAllUserPrimary > 0){
                    $list[$key]['goods'][$k]['isAllUserPrimary'] = (double)round($isAllUserPrimary * $v['price'],2);
                }

                unset($list[$key]['goods'][$k]['seller']);
            }
        }
        return $list;
    }
	
	/**
     * 获取商品类的列表
     * @param [int] $id 分类Id
     */
    public static function  getGoodsListsDsy($userId,$id,$mapPoint,$sort,$page,$pageSize,$cityId){

		if($mapPoint == null || $id == 0) {
            return [];
        }
        $mapPoint = $mapPoint == '' ? '0 0' : str_replace(',', ' ', $mapPoint);
        $dbPrefix = DB::getTablePrefix();
		$cateIds = [$id];
		$childs = SellerCate::where('pid', $id)->get();
		foreach ($childs as $child) {
			$cateIds[] = $child['id'];
		}
		if(empty($cateIds)){
			 return [];
		}
		$list = Goods::where('goods.status', STATUS_ENABLED)
			 ->where('goods.type', Goods::SELLER_GOODS)                    
			->join('goods_extend', 'goods_extend.goods_id', '=', "goods.id")
			->join('seller', 'goods.seller_id', '=', "seller.id")
			->join('seller_cate_related', 'seller.id', '=', "seller_cate_related.seller_id")
            ->where('seller.status', STATUS_ENABLED)
            ->whereIn("seller_cate_related.cate_id",$cateIds)
			->whereRaw("({$dbPrefix}seller.store_type = 1 OR ({$dbPrefix}seller.store_type = 0 AND ST_Contains({$dbPrefix}seller.map_pos,GeomFromText('Point({$mapPoint})'))))")
            ->whereRaw("
				(
					{$dbPrefix}seller.store_type = 1 OR
					(

						{$dbPrefix}seller.store_type = 0 AND
							(
								{$dbPrefix}seller.city_id  = {$cityId} OR {$dbPrefix}seller.province_id = {$cityId}
							)
					)
				)
			")
			->select(
					'goods.*',
					'seller.name as seller_name',
					'seller.province_id',
					'seller.city_id',
					'seller.store_type',
					'goods_extend.sales_volume',
					'goods_extend.share_num'
			);
        switch ($sort)
        {
            case 1: // 销量
                $list->orderBy('goods_extend.sales_volume', 'desc');
                break;
            case 3: // 价格
                $list->orderBy('goods.price', 'desc');
                break;
            case 4: // 价格
                $list->orderBy('goods.price', 'asc');
                break;
        }
        $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();

        $isAllUserPrimary = Invitation::where("user_status",1)->pluck('is_all_user_primary') / 100;
        $lists = [];
        foreach ($list as $key=>$val) {
            $lists[$key]['goods'][$key] = $val;            
			$lists[$key]['goods'][$key]['province'] = baseRegionService::getById($val['provinceId']);
			$lists[$key]['goods'][$key]['city'] = baseRegionService::getById($val['cityId']);
            $lists[$key]['goods'][$key]['salePrice'] = ActivityGoods::where("goods_id",$val['id'])->pluck("sale_price");
            $lists[$key]['goods'][$key]['storeType'] = $val['storeType'];
            if($isAllUserPrimary > 0){
                $lists[$key]['goods'][$key]['isAllUserPrimary'] = (double)round($isAllUserPrimary * $val['price'],2);
            }
            $lists[$key]['goods'][$key]['image'] = $val['images'][0];
            $lists[$key]['goods'][$key]['extend']['salesVolume'] = $val['salesVolume'];
            $lists[$key]['goods'][$key]['extend']['shareNum'] = $val['shareNum'];
            unset($list[$key]);
        }
        return $lists;
    }

    /**
     * 获取商品类的列表
     * @param [int] $id 分类Id
     */
    public static function  getTypeGoodsListsDsy($userId,$mapPoint,$sort,$page,$pageSize){

        if($mapPoint == null) {
            return [];
        }
        $mapPoint = $mapPoint == '' ? '0 0' : str_replace(',', ' ', $mapPoint);
        $dbPrefix = DB::getTablePrefix();

        $lists = Activity::where('type', 6)
            ->where('start_time', '<', UTC_TIME)
            ->where('end_time', '>', UTC_TIME)
            ->where('time_status', 1)
            ->with('activityGoods')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
        if(empty($lists)){
            return false;
        }

        $goodsIds = [];
        foreach($lists as $key=>$val){
            foreach($val['activityGoods'] as $key2=>$val2){
                array_push($goodsIds,$val2['goodsId']);
            }
        }
        $goodsIds = array_unique($goodsIds);

        $list = Goods::where('goods.status', STATUS_ENABLED)
            ->where('goods.type', Goods::SELLER_GOODS)
            ->join('goods_extend', 'goods_extend.goods_id', '=', "goods.id")
            ->join('seller', 'goods.seller_id', '=', "seller.id")
            ->where('seller.status', STATUS_ENABLED)
            ->whereIn('goods.id',$goodsIds)
            ->whereRaw("({$dbPrefix}seller.store_type = 1 OR ({$dbPrefix}seller.store_type = 0 AND ST_Contains({$dbPrefix}seller.map_pos,GeomFromText('Point({$mapPoint})'))))")
            ->select(
                'goods.*',
                'seller.name as seller_name',
                'seller.province_id',
                'seller.city_id',
                'seller.store_type',
                'goods_extend.sales_volume',
                'goods_extend.share_num'
            );

        $list->addSelect(DB::raw("ST_Distance({$dbPrefix}seller.map_point,GeomFromText('POINT({$mapPoint})')) AS distance"));
        $list->orderBy('seller.store_type', 'asc');
        $list->orderBy('distance', 'asc');
        $list->orderBy('goods_extend.sales_volume', 'desc');

        $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();

        $lists = [];
        foreach ($list as $key=>$val) {
            $lists[$key] = $val;
            $lists[$key]['image'] = $val['images'][0];
            $sale = ActivityGoods::where("goods_id",$val['id'])->pluck("sale");
            $lists[$key]['salePrice'] = number_format($val['price']*($sale/10),2);
            $lists[$key]['num'] = ShoppingCart::where("user_id",$userId)->where('goods_id',$val['id'])->pluck("num");
            $lists[$key]['normsId'] = GoodsNorms::where("goods_id",$val['id'])->where('seller_id',$val['sellerId'])->orderBy('id','asc')->pluck("id");

            $lists[$key]['extend']['salesVolume'] = $val['salesVolume'];
            $lists[$key]['extend']['shareNum'] = $val['shareNum'];
            unset($list[$key]);
        }
        return $lists;
    }
	
	/**
     * 首页好货推荐(自动任务生成缓存)
     * @param [int] $id 商家编号
     */
    public static function getRecommendGoodsCache(){
        $hours = Time::toDate(UTC_TIME, 'H:i');
        $week = Time::toDate(UTC_TIME, 'w');
        $sql = 'SELECT g.*,g_e.*,s.name as name2,g_e.sales_volume as salesVolume,s.store_type,s.address,s.province_id,s.city_id FROM '.env("DB_PREFIX").'goods AS g
            LEFT JOIN '.env("DB_PREFIX").'goods_extend AS g_e ON g_e.goods_id = g.id LEFT JOIN '.env("DB_PREFIX").'seller AS s ON s.id = g.seller_id
                AND s.`status` = 1
                AND s.`is_check` = 1
            LEFT JOIN `'.env("DB_PREFIX").'staff_service_time` ON `'.env("DB_PREFIX").'staff_service_time`.`seller_id` = g.`seller_id`
                AND `week` = '.$week.'
                AND `begin_time` <= "'.$hours.'"
                AND (`end_time` >= "'.$hours.'" or `end_time` = "00:00")
            WHERE
                    g.type = 1
                AND g.status = 1
                AND (s.store_type = 1 OR (s.store_type = 0 AND `'.env("DB_PREFIX").'staff_service_time`.service_time_id IS NOT NULL))
            ORDER BY g_e.sales_volume DESC LIMIT 0,200';

        $list = Cache::get('RecommendGoodsListsCache');
        if((UTC_TIME - $list['cacheTime']) >= 5 * 60){
            $lists = DB::select($sql);
            $lists = json_decode(json_encode($lists),true);
            $data = [
                'cacheTime' => UTC_TIME,
                'data' => $lists
            ];
            Cache::forever('RecommendGoodsListsCache', $data);
        }
    }

    public function skus($goodsId) {
        $goods = Goods::find($goodsId);
        if (!$goods) {
            $result['code'] = 60511;
            return $result;
        }

        $result = ['goods' => $goods->toArray(), 'skus' => [], 'stocks' => []];

        $list = GoodsSkuItem::where('goods_id', $goodsId)->where('group_id',$goods->stock_type_id)->orderBy('sort', 'ASC')->get();
        foreach ($list as $item) {
            if (!isset($result['skus'][$item->group_name])) {
                $result['skus'][$item->group_name] = [
                    'name'  => $item->group_name,
                    'sort'  => $item->sort,
                    'items' => [[
                        'id' => $item->id,
                        'name' => $item->name,
                        'image' => $item->image,
                    ]
                    ]
                ];
            } else {
                $result['skus'][$item->group_name]['items'][] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'image' => $item->image,
                ];
            }
        }

        $result['skus'] = array_values($result['skus']);

        $list = GoodsStock::where('goods_id', $goodsId)->get();
        foreach ($list as $item) {
            $result['stocks'][] = [
                'sn' => $item->sku_sn,
                'price' => $item->price,
                'market_price' => $item->market_price,
                'stock_count' => $item->stock_count,
                'sale_count' => $item->sale_count,
            ];
        }

        return $result;
    }
}
