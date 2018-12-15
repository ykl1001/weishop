<?php namespace YiZan\Services\Buyer;

use YiZan\Models\Seller;
use YiZan\Models\StaffServiceTime;
use YiZan\Models\SystemConfig;
use YiZan\Models\User;
use YiZan\Models\Goods;
use YiZan\Models\SellerExtend;
use YiZan\Models\GoodsExtend;
use YiZan\Models\SellerMap;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\SellerAuthenticate;
use YiZan\Models\SellerStaff;
use YiZan\Models\SellerCate;
use YiZan\Models\SellerStaffExtend;
use YiZan\Models\SellerDeliveryTime;
use YiZan\Models\SellerIconRelated;
use YiZan\Models\StaffServiceTimeSet;
use YiZan\Models\Region;
use YiZan\Models\Invitation;
use YiZan\Models\Activity;
use YiZan\Models\FreightTmp;
use YiZan\Models\FreightTmpCity;

use YiZan\Services\AdvService as baseAdvService;
use YiZan\Services\ActivityService as baseActivityService;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use DB, Lang, Validator, Config,Image;

class SellerService extends \YiZan\Services\SellerService
{
    /**
     * 我要开店资料
     */
    public static function sellerDatum($userId)
    {
        if(!$userId){
            return null;
        }
        $seller =  Seller::where('user_id',$userId)->with('authenticate','city')->first();
        return $seller;
    }

    /**
     * Summary of getSellerDetail
     * @param mixed $id
     * @param mixed $userId
     * @return mixed
     */
    public static function getSellerDetail($id, $userId = 0)
    {
        $dbPrefix = DB::getTablePrefix();

        $sql = "
SELECT  S.map_point_str AS mapPointStr,
        S.id,
        S.name,
        S.province_id,
        S.city_id,
        S.area_id,
        S.create_time AS createTime,
        S.logo,S.mobile,S.image,
        S.brief AS detail,
        S.store_type AS storeType,
        E.collect_count AS collectCount,
		E.share_num AS shareNum,
        IF(C.seller_id IS NOT NULL, 1, 0) AS isCollect,
        NULL AS banner,
        delivery_fee AS deliveryFee,
        service_fee AS serviceFee,
        CONCAT('<font color=\"#979797\">起送价</font><font color=\"red\">￥',
            S.service_fee,
            '</font>&nbsp;<font color=\"#979797\">运费</font><font color=\"#ff2d4b\">',
            S.delivery_fee,
            '</font><font color=\"#979797\">元</font>&nbsp;<font color=\"#ff2d4b\"></font>') AS freight,
        S.service_tel AS tel,
        S.address,
        E.order_count AS orderCount,
        S.avoid_fee AS avoidFee,
        S.is_avoid_fee AS isAvoidFee,
        (SELECT IFNULL(ROUND(SUM(star)/COUNT(id),1),0) FROM {$dbPrefix}order_rate as DR where DR.seller_id = S.id) AS score
    FROM {$dbPrefix}seller AS S
        LEFT OUTER JOIN {$dbPrefix}user_collect AS C ON S.id = C.seller_id
            AND C.user_id = {$userId}
            AND {$userId} > 0
            AND C.type = 2 /* 店铺 */
        INNER JOIN {$dbPrefix}seller_extend AS E ON E.seller_id = S.id
        WHERE S.id = {$id}
        and S.status = 1 and S.is_check = 1 and S.is_del = 0
        ";
        $data = DB::selectOne($sql);

        if(empty($data)){
            return $data;
        }

        if($data->collectCount > 10000){
            $data->collectCount = round($data->collectCount/10000,1).'万';
        }

        if($data->shareNum > 10000){
            $data->shareNum = round($data->shareNum/10000,1).'万';
        }

        $adv = baseAdvService::getAdvByCode('BUYER_JNIWCZKA', $data->city_id);

        foreach($adv as $key => $value)
        {
            if($value['type'] == 7) //文章
            {
                $adv[$key]['type'] = '5';
                $adv[$key]["arg"] = u('Wap#Article/detailapp',array('id'=>$value['arg']));
            }
        }
        $data->banner =$adv;
        $data->countGoods = Goods::where('seller_id', $data->id)->where('status',1)->where('type', 1)->count();//商品
        $data->countService = Goods::where('seller_id', $data->id)->where('status',1)->where('type', 2)->count();//服务

        if($data->storeType == 1){
            $data->province = RegionService::getById($data->province_id);//省
            $data->city = RegionService::getById($data->city_id);//市
            $data->area = RegionService::getById($data->area_id);//区
            $data->authenticateImg = SellerAuthenticate::where('seller_id',$data->id)->pluck('business_licence_img');//营业执照
        }


        $time =  SellerDeliveryTime::where('seller_id', $data->id)->get()->toArray();
        foreach ($time as $k => $v) {
            $data->stimes[] = $v['stime'] . '-' . $v['etime'];
        }
        $data->deliveryTime = $data->stimes ? implode(',', $data->stimes) : '00:00-24:00';
        $isDelivery = SellerService::isCanBusiness($data->id);
        $data->isDelivery = $isDelivery;
        $mapPoint = explode(',', $data->mapPointStr);
        $data->mapPoint['x'] = $mapPoint[0];
        $data->mapPoint['y'] = $mapPoint[1];

        $hours = Time::toDate(UTC_TIME, 'H:i');
        $week = Time::toDate(UTC_TIME, 'w');
        $data->serviceTimesCount = StaffServiceTime::where('seller_id', $id)
            ->where('week', $week)
            ->where('begin_time', '<=', $hours)
            ->where(function($query) use ($hours){
                $query->where('end_time', '>=', $hours)
                    ->orWhere('end_time', '00:00');
            })->count();

        $stime = [];
        $serviceTimes = StaffServiceTime::where('seller_id', $id)->where('week',$week)->get()->toArray();
        foreach ($serviceTimes as $key => $val) {
            $stime[$key]= $val['beginTime'] . '-'. $val['endTime'];
        }
        $data->businessHours = !empty($stime) ? implode(' ', $stime) : '商家休息中';
        $data->sellerAuthIcon = SellerIconRelated::where('seller_id', $id)->with('icon')->get()->toArray();
        $data->activity = baseActivityService::getSellerActivity($id);

        return $data;
    }
    /**
     * Summary of getSellerList
     * @param mixed $cateId
     * @param mixed $page
     * @param mixed $sort
     * @param mixed $keywords
     * @param mixed $userId
     * @return mixed
     */
    public static function getSellerList($cateId, $page, $sort, $keywords = '', $userId = 0, $mapPoint = '0 0')
    {
        $hours = Time::toDate(UTC_TIME, 'H:i');
        $week = Time::toDate(UTC_TIME, 'w');
        $dbPrefix = DB::getTablePrefix();
        $mapPoint = $mapPoint == '' ? '0 0' : str_replace(',', ' ', $mapPoint);
        $selfSellerId = ONESELF_SELLER_ID;
        $sql = "
SELECT  S.map_point_str AS mapPointStr,
        S.id,
        S.name,
        S.logo,S.mobile,S.image,
        S.province_id,S.city_id,S.area_id,
        S.brief AS detail,
        IF(C.seller_id IS NOT NULL, 1, 0) AS isCollect,
        NULL AS banner,
        S.delivery_fee AS deliveryFee,
        S.service_fee AS serviceFee,
        S.store_type AS storeType,

        CONCAT('<font color=\"#979797\">起送价</font><font color=\"red\">￥',
            S.service_fee,
            '</font>&nbsp;<font color=\"#979797\">运费</font><font color=\"#ff2d4b\">',
            S.delivery_fee,
            '</font><font color=\"#979797\">元</font>&nbsp;<font color=\"#ff2d4b\"></font>') AS freight,
        S.service_tel AS tel,
        S.address,
        E.order_count AS orderCount,
        (SELECT IFNULL(ROUND(SUM(star)/COUNT(id),1),0) FROM {$dbPrefix}order_rate as DR where DR.seller_id = S.id) AS score,
        (SELECT COUNT(id) FROM {$dbPrefix}goods as G where G.seller_id = S.id and G.type = 1 and G.status = 1) as countGoods,
        (SELECT COUNT(id) FROM {$dbPrefix}goods as GS where GS.seller_id = S.id and GS.type = 2 and GS.status = 1) as countService,
        (SELECT COUNT(1) FROM {$dbPrefix}staff_service_time as SST where SST.seller_id = S.id and SST.week = {$week} and SST.begin_time <= '{$hours}' and (SST.end_time >= '{$hours}' or  SST.end_time = '00:00')) as serviceTimesCount,
        ST_Distance(S.map_point,GeomFromText('POINT({$mapPoint})')) AS distance
    FROM {$dbPrefix}seller AS S
        LEFT OUTER JOIN {$dbPrefix}user_collect AS C ON S.id = C.seller_id
            AND C.user_id = {$userId}
            AND {$userId} > 0
            AND C.type = 2 /* 店铺 */
        INNER JOIN {$dbPrefix}seller_extend AS E ON E.seller_id = S.id
        WHERE S.is_check = 1 /* 已审核 */
        AND S.status = 1 /* 正常 */
        AND S.id <> {$selfSellerId}
        AND S.is_del = 0
        AND (S.store_type = 1 OR (S.store_type = 0 AND ST_Contains(S.map_pos,GeomFromText('Point({$mapPoint})'))))   /*查询在范围内的商家 */
        ";
        if($cateId > 0)
        {
            $cateIds = [$cateId];
            $childs = SellerCate::where('pid', $cateId)->get();
            foreach ($childs as $child) {
                $cateIds[] = $child['id'];
            }
            $cateIds = implode(',', $cateIds);
            $sql = "
{$sql}
            AND EXISTS
            (
                SELECT 1
                    FROM {$dbPrefix}seller_cate_related AS R
                    WHERE R.seller_id = S.id
                        AND R.cate_id IN ({$cateIds})
            )
            ";
        }

        if (!empty($keywords))
        {
            /*$keywords = String::strToUnicode($keywords,'+');

            $sql = "
{$sql}
            AND MATCH(S.name_match) AGAINST('{$keywords}' IN BOOLEAN MODE)
            ";*/
            $sql = "{$sql} AND S.name like '%{$keywords}%' ";
        }

        switch ($sort)
        {
            case 1: // 销量倒序
                $sort = "E.order_count DESC";
                break;

            case 2: // 起送价倒序
                $sort = "S.service_fee ASC";
                break;

            case 3: // 距离最近
                $sort = "distance ASC";
                break;

            case 4: // 评分最高
                $sort = "score DESC";
                break;

            default:
                $sort = "S.sort ASC";
                break;
        }
        $size = 20;

        $start = max(0, ($page - 1) * $size);


        $sql = "
{$sql}
            ORDER BY {$sort}, S.id ASC
            LIMIT {$start}, {$size}
            ";

        $res = DB::select($sql);
        foreach ($res as $key => $value) {
            $mapPoint = explode(',', $value->mapPointStr);
            $res[$key]->mapPoint['x'] = $mapPoint[0];
            $res[$key]->mapPoint['y'] = $mapPoint[1];

            $sql = "SELECT * FROM {$dbPrefix}staff_service_time where seller_id = ". $value->id;
            $stime =  DB::select($sql);
            $count = count($stime);
            $res[$key]->businessHours = $count > 0 ? $stime[0]->begin_time . '-' . $stime[$count - 1]->end_time : '0:00-24:00';

            $time =  SellerDeliveryTime::where('seller_id', $res[$key]->id)->get()->toArray();
            foreach ($time as $k => $v) {
                $res[$key]->stimes[] = $v['stime'] . '-' . $v['etime'];
            }
            $res[$key]->deliveryTime = $res[$key]->stimes ? implode(',', $res[$key]->stimes) : '00:00-24:00';

            $isDelivery = SellerService::isCanBusiness($value->id);
            $res[$key]->isDelivery = $isDelivery;
            if($res[$key]->storeType == 1){
                $res[$key]->isBussiness = 1;
            }else{
                $res[$key]->isBussiness = $value->serviceTimesCount > 0 ? true : false;
            }

            $res[$key]->sellerAuthIcon = SellerIconRelated::where('seller_id', $value->id)->with('icon')->get()->toArray();
            $res[$key]->activity = baseActivityService::getSellerActivity($value->id);

//            S.province_id,S.city_id,S.area_id,
            if($res[$key]->province_id == 0){
                $res[$key]->city = Region::where("id",$res[$key]->city_id)->where('is_service', 1)->first();
            }else{
                $res[$key]->province = Region::where("id",$res[$key]->province_id)->where('is_service', 1)->first();
                $res[$key]->city = Region::where("id",$res[$key]->city_id)->where('is_service', 1)->first();
            }

            // print_r($isDelivery);

        }

        //print_r($res);
        return $res;
    }

    public static function getTypeSellerList($type,$page, $userId = 0, $mapPoint = '0 0',$cityId)
    {
        $hours = Time::toDate(UTC_TIME, 'H:i');
        $week = Time::toDate(UTC_TIME, 'w');
        $dbPrefix = DB::getTablePrefix();
        $mapPoint = $mapPoint == '' ? '0 0' : str_replace(',', ' ', $mapPoint);
        $selfSellerId = ONESELF_SELLER_ID;

        $user_seller = 0; //是不是全部都要有免减活动 0不是 1是
        if($type == 1){
            $lists = Activity::where('type', 5)
                ->where('start_time', '<', UTC_TIME)
                ->where('end_time', '>', UTC_TIME)
                ->where('time_status', 1)
                ->with('activitySeller')
                ->orderBy('id', 'desc')
                ->get()
                ->toArray();

            if(empty($lists)){
                return false;
            }
            $sellerIds = [];
            foreach($lists as $key=>$val){
                if($val['useSeller'] == 0 && $val['isSystem'] ==1){
                    $user_seller = 1;
                    break;
                }else{
                    if($val['isSystem'] == 0){ //不是平台加的商家
                        array_push($sellerIds,$val['sellerId']);
                    }else{
                        foreach($val['activitySeller'] as $key2=>$val2){
                            array_push($sellerIds,$val2['sellerId']);
                        }
                    }

                }
            }
            $sellerIds = array_unique($sellerIds);
            $sellerIds = implode(",",$sellerIds);
        }else{
            $user_seller = 1;
        }

        DB::connection()->enableQueryLog();

        $sql = "
SELECT  S.map_point_str AS mapPointStr,
        S.id,
        S.name,
        S.logo,S.mobile,S.image,
        S.province_id,S.city_id,S.area_id,
        S.brief AS detail,
        IF(C.seller_id IS NOT NULL, 1, 0) AS isCollect,
        NULL AS banner,
        CONCAT('<font color=\"#979797\">起送价</font><font color=\"red\">￥',
            S.service_fee,
            '</font>&nbsp;<font color=\"#979797\">运费</font><font color=\"#ff2d4b\">',
            S.delivery_fee,
            '</font><font color=\"#979797\">元</font>&nbsp;<font color=\"#ff2d4b\"></font>') AS freight,
        S.delivery_fee AS deliveryFee,
        S.service_fee AS serviceFee,
        S.store_type AS storeType,
        S.service_tel AS tel,
        S.address,
        E.order_count AS orderCount,
        S.avoid_fee AS avoidFee,
        S.is_avoid_fee AS isAvoidFee,
        (SELECT IFNULL(ROUND(SUM(star)/COUNT(id),1),0) FROM {$dbPrefix}order_rate as DR where DR.seller_id = S.id) AS score,
        (SELECT COUNT(id) FROM {$dbPrefix}goods as G where G.seller_id = S.id and G.type = 1 and G.status = 1) as countGoods,
        (SELECT COUNT(id) FROM {$dbPrefix}goods as GS where GS.seller_id = S.id and GS.type = 2 and GS.status = 1) as countService,
        (SELECT COUNT(1) FROM {$dbPrefix}staff_service_time as SST where SST.seller_id = S.id and SST.week = {$week} and SST.begin_time <= '{$hours}' and (SST.end_time >= '{$hours}' or  SST.end_time = '00:00')) as serviceTimesCount,
        ST_Distance(S.map_point,GeomFromText('POINT({$mapPoint})')) AS distance
    FROM {$dbPrefix}seller AS S
        LEFT OUTER JOIN {$dbPrefix}user_collect AS C ON S.id = C.seller_id
            AND C.user_id = {$userId}
            AND {$userId} > 0
            AND C.type = 2 /* 店铺 */
        INNER JOIN {$dbPrefix}seller_extend AS E ON E.seller_id = S.id
        #ADD_JOIN_SQL#
        WHERE S.is_check = 1 /* 已审核 */
        AND S.status = 1 /* 正常 */
        AND S.id <> {$selfSellerId}
        AND S.is_del = 0
        AND (S.store_type = 1 OR (S.store_type = 0 AND ST_Contains(S.map_pos,GeomFromText('Point({$mapPoint})'))))   /*查询在范围内的商家 */
        ";

        $add_join_sql = '';
        if($user_seller == 0 && $type == 1){
            $sql .= " AND S.id in({$sellerIds})";
        }else if($type == 2){
            $sql .= " AND S.avoid_fee > 0";
        }else if($type == 3){
            $not_sellerIds = [];
            $freightTmpCity = FreightTmp::where('money','>',0)->with('tmpcity')->get();
            $freightTmpCity = !empty($freightTmpCity) ? $freightTmpCity->toArray() : '';

            $city_info = RegionService::getById($cityId);//市
            if($city_info['pid'] > 0){
                $provice_info = RegionService::getById($city_info['pid']);//省
            }

            $zx = array("1", "18", "795", "2250");
            foreach ($freightTmpCity as $key => $value) {
                if(empty($value['tmpcity'])){
                    array_push($not_sellerIds, $value['sellerId']);
                }else{
                    foreach($value['tmpcity'] as $k2=>$v2){
                        if(in_array($v2['regionId'],$zx)){ //直辖市
                            if($v2['regionId'] == $city_info['id']){
                                array_push($not_sellerIds, $value['sellerId']);
                            }
                        }else{//省 市 或者 直辖市的区
                            $city_info2 = RegionService::getById($v2['regionId']);
                            if(in_array($city_info2['pid'],$zx)){//直辖市的区直接退出
                                break;
                            }

                            if($city_info2['pid'] == 0){//省
                                if($v2['regionId'] == $provice_info['id']){
                                    array_push($not_sellerIds, $value['sellerId']);
                                }
                            }else{
                                if($v2['regionId'] == $city_info['id']){
                                    array_push($not_sellerIds, $value['sellerId']);
                                }
                            }
                        }
                    }
                }
            }

            if(empty($not_sellerIds)){
                $sql .= " AND S.delivery_fee = 0";
            }else{
                $not_sellerIds = array_unique($not_sellerIds);
                $not_sellerIds = implode(",",$not_sellerIds);
                $sql .= " AND S.delivery_fee = 0 AND S.id not in ({$not_sellerIds})";
            }

        }
        $sql = str_replace('#ADD_JOIN_SQL#', $add_join_sql, $sql);
        $size = 20;
        $start = max(0, ($page - 1) * $size);

        $sql = "
{$sql}
            ORDER BY S.store_type ASC, distance ASC, score DESC, S.id ASC
            LIMIT {$start}, {$size}
            ";

        $res = DB::select($sql);

        $queries = DB::getQueryLog();
        // print_r($queries);exit;

        foreach ($res as $key => $value) {
            $mapPoint = explode(',', $value->mapPointStr);
            $res[$key]->mapPoint['x'] = $mapPoint[0];
            $res[$key]->mapPoint['y'] = $mapPoint[1];

            $sql = "SELECT * FROM {$dbPrefix}staff_service_time where seller_id = ". $value->id;
            $stime =  DB::select($sql);
            $count = count($stime);
            $res[$key]->businessHours = $count > 0 ? $stime[0]->begin_time . '-' . $stime[$count - 1]->end_time : '0:00-24:00';

            $time =  SellerDeliveryTime::where('seller_id', $res[$key]->id)->get()->toArray();
            foreach ($time as $k => $v) {
                $res[$key]->stimes[] = $v['stime'] . '-' . $v['etime'];
            }
            $res[$key]->deliveryTime = $res[$key]->stimes ? implode(',', $res[$key]->stimes) : '00:00-24:00';

            $isDelivery = SellerService::isCanBusiness($value->id);
            $res[$key]->isDelivery = $isDelivery;
            if($res[$key]->storeType == 1){
                $res[$key]->isBussiness = 1;
            }else{
                $res[$key]->isBussiness = $value->serviceTimesCount > 0 ? true : false;
            }

            $res[$key]->activity = baseActivityService::getSellerActivity($value->id);

            if($res[$key]->province_id == 0){
                $res[$key]->city = Region::where("id",$res[$key]->city_id)->where('is_service', 1)->first();
            }else{
                $res[$key]->province = Region::where("id",$res[$key]->province_id)->where('is_service', 1)->first();
                $res[$key]->city = Region::where("id",$res[$key]->city_id)->where('is_service', 1)->first();
            }

            $res[$key]->noShow = 1;
        }

        return $res;
    }



    /**
     * 搜索店铺及店铺商品 caiq
     * @param mixed $cateId
     * @param mixed $page
     * @param mixed $sort
     * @param mixed $keywords
     * @param mixed $userId
     * @return mixed
     */
    public static function getSellerListByGoodsname($cateId, $page,$pageSize = 5, $sort, $keywords = '', $userId = 0, $mapPoint = '0 0',$cityId = 0)
    {
        if (empty($keywords))
        {
            return false;
        }
        $dbPrefix = DB::getTablePrefix();
        $mapPoint = $mapPoint == '' ? '0 0' : str_replace(',', ' ', $mapPoint);
        $week = Time::toDate(UTC_TIME, 'w');
        $hours = Time::toDate(UTC_TIME, 'H:i');
        $ds = array();
        //按商品名称搜索
        $sql_sellers = "
		SELECT  S.store_type AS storeType,S.province_id,S.city_id,S.area_id,  S.map_point_str AS mapPointStr,S.id,S.name,S.logo,S.mobile,S.image,S.avoid_fee AS avoidFee,S.is_avoid_fee AS isAvoidFee,delivery_fee AS deliveryFee,service_fee AS serviceFee,
        IF(C.seller_id IS NOT NULL, 1, 0) AS isCollect,
        (SELECT IFNULL(ROUND(SUM(star)/COUNT(id),1),0) FROM {$dbPrefix}order_rate as DR where DR.seller_id = S.id) AS score,			
		(SELECT COUNT(id) FROM {$dbPrefix}goods as G where G.seller_id = S.id and G.type = 1 and G.status = 1) as countGoods,
		(SELECT COUNT(id) FROM {$dbPrefix}goods as GS where GS.seller_id = S.id and GS.type = 2 and GS.status = 1) as countService,
        (SELECT COUNT(1) FROM {$dbPrefix}staff_service_time as SST where SST.seller_id = S.id and SST.week = {$week} and SST.begin_time <= '{$hours}' and (SST.end_time >= '{$hours}' or  SST.end_time = '00:00')) as serviceTimesCount,
        (ST_Distance(S.map_point,GeomFromText('POINT({$mapPoint})'))/0.0111) AS distance,
		E.order_count AS orderCount
    	FROM {$dbPrefix}seller AS S
		INNER JOIN {$dbPrefix}seller_extend AS E ON E.seller_id = S.id
        LEFT OUTER JOIN {$dbPrefix}user_collect AS C ON S.id = C.seller_id
            AND C.user_id = {$userId}
            AND {$userId} > 0
            AND C.type = 2 /* 店铺 */
        WHERE (store_type = 1 OR (store_type = 0 AND city_id = {$cityId} OR province_id = {$cityId}) )
		AND  S.status= 1 /* 正常 */
        AND S.is_check = 1 /* 已审核 */
        AND S.is_del = 0
        AND (S.store_type = 1 OR (S.store_type = 0 AND ST_Contains(S.map_pos,GeomFromText('Point({$mapPoint})'))))   /* 查询在范围内的商家 */
		AND (S.goods_keywords LIKE '%{$keywords}%' OR S.name LIKE '%{$keywords}%'  )";

        if($cateId > 0)
        {
            $cateIds = [$cateId];
            $childs = SellerCate::where('pid', $cateId)->get();
            foreach ($childs as $child) {
                $cateIds[] = $child['id'];
            }
            $cateIds = implode(',', $cateIds);
            $sql_sellers__________ = "
		{$sql_sellers}
            AND EXISTS
            (
                SELECT 1
                    FROM {$dbPrefix}seller_cate_related AS R
                    WHERE R.seller_id = S.id 
                        AND R.cate_id IN ({$cateIds})
            )
            ";
        }


        switch ($sort)
        {
            case 1: // 销量倒序
                $sort = "distance ASC";
                break;
            case 2: // 距离最近
                $sort = "score DESC";
                break;
            default:
                $sort = "distance ASC";
                break;
        }

        $size = $pageSize;
        $start = max(0, ($page - 1) * $size);

        $sql_sellers = " {$sql_sellers} ORDER BY {$sort}, S.id ASC LIMIT {$start}, {$size} ";
        //
        $sellerList = DB::select($sql_sellers);
        if($page==1&&!empty($sellerList)){
            //商家数
            $sql_seller_total = "
				SELECT  count(S.id) AS seller_total
		    	FROM {$dbPrefix}seller AS S
		        WHERE (store_type = 1 OR (store_type = 0 AND city_id = {$cityId} OR province_id = {$cityId}) )
				AND  S.status = 1
		        AND S.is_check = 1
		        AND S.is_del = 0
                AND (S.store_type = 1 OR (S.store_type = 0 AND ST_Contains(S.map_pos,GeomFromText('Point({$mapPoint})'))))   /* 查询在范围内的商家 */
				AND (S.goods_keywords LIKE '%{$keywords}%' OR S.name LIKE '%{$keywords}%'  )";
            //商品数
            $sql_goods_total = "
				SELECT count(id) AS goods_total
				FROM {$dbPrefix}goods 
				WHERE seller_id IN(SELECT  S.id
							    	FROM {$dbPrefix}seller AS S
							        WHERE (store_type = 1 OR (store_type = 0 AND city_id = {$cityId} OR province_id = {$cityId}) )
									AND  S.status = 1
							        AND S.is_check = 1 
                                    AND (S.store_type = 1 OR (S.store_type = 0 AND ST_Contains(S.map_pos,GeomFromText('Point({$mapPoint})'))))   /* 查询在范围内的商家 */
									AND (S.goods_keywords LIKE '%{$keywords}%' OR S.name LIKE '%{$keywords}%')) 
				AND status=1 AND stock>0  AND name LIKE '%{$keywords}%'";
            $ds['seller_total'] = DB::select($sql_seller_total)[0]->seller_total;
            $ds['goods_total'] = DB::select($sql_goods_total)[0]->goods_total;
        }

        $keySearch = " AND name LIKE '%{$keywords}%'";
        /* 搜索店铺  */
        $noGoods = FALSE;
        if(empty($sellerList)){
            $noGoods = TRUE;
            $sql_sellers = "
				SELECT  S.store_type AS storeType,S.province_id,S.city_id,S.area_id, S.map_point_str AS mapPointStr,S.id,S.name,S.logo,S.mobile,S.image,S.avoid_fee AS avoidFee,S.is_avoid_fee AS isAvoidFee,delivery_fee AS deliveryFee,service_fee AS serviceFee,
		        IF(C.seller_id IS NOT NULL, 1, 0) AS isCollect,
				E.order_count AS orderCount,
		        (SELECT IFNULL(ROUND(SUM(star)/COUNT(id),1),0) FROM {$dbPrefix}order_rate as DR where DR.seller_id = S.id) AS score,				
				(SELECT COUNT(id) FROM {$dbPrefix}goods as G where G.seller_id = S.id and G.type = 1 and G.status = 1) as countGoods,
				(SELECT COUNT(id) FROM {$dbPrefix}goods as GS where GS.seller_id = S.id and GS.type = 2 and GS.status = 1) as countService,
		        (SELECT COUNT(1) FROM {$dbPrefix}staff_service_time as SST where SST.seller_id = S.id and SST.week = {$week} and SST.begin_time <= '{$hours}' and (SST.end_time >= '{$hours}' or  SST.end_time = '00:00')) as serviceTimesCount,
		        (ST_Distance(S.map_point,GeomFromText('POINT({$mapPoint})'))/0.0111) AS distance
		    	FROM {$dbPrefix}seller AS S
				INNER JOIN {$dbPrefix}seller_extend AS E ON E.seller_id = S.id
		        LEFT OUTER JOIN {$dbPrefix}user_collect AS C ON S.id = C.seller_id
		            AND C.user_id = {$userId}
		            AND {$userId} > 0
		            AND C.type = 2 /* 店铺 */
				WHERE (store_type = 1 OR (store_type = 0 AND city_id = {$cityId} OR province_id = {$cityId}) )
				AND  S.status = 1 /* 正常 */
		        AND S.is_check = 1 /* 已审核 */
		        AND S.is_del = 0
		        AND (S.store_type = 1 OR (S.store_type = 0 AND ST_Contains(S.map_pos,GeomFromText('Point({$mapPoint})'))))   /* 查询在范围内的商家 */
				AND (S.goods_keywords LIKE '%{$keywords}%' OR S.name LIKE '%{$keywords}%'  )";
            $keySearch = " ";
            $sql_sellers = " {$sql_sellers} ORDER BY {$sort}, S.id ASC LIMIT {$start}, {$size} ";
            $sellerList = DB::select($sql_sellers);

        }
        /* 获取商品*/
        foreach ($sellerList as $key => $value) {
            //if($noGoods) break;
            $mapPoint = explode(',', $value->mapPointStr);
            $sellerList[$key]->mapPoint['x'] = $mapPoint[0];
            $sellerList[$key]->mapPoint['y'] = $mapPoint[1];
            $g_sql = "SELECT id,name,price,seller_id,stock FROM {$dbPrefix}goods where seller_id = ". $value->id
                ." AND status=1 AND type=1 AND stock>0 {$keySearch} ORDER BY sort DESC, price ASC LIMIT 5";
            $seller_goods =  DB::select($g_sql);
            $sellerList[$key]->goods = $seller_goods;
            $sellerList[$key]->sellerAuthIcon = SellerIconRelated::where('seller_id', $value->id)->with('icon')->get()->toArray();

            $sellerList[$key]->countGoods = $value->countGoods;

            $sellerList[$key]->activity = baseActivityService::getSellerActivity($value->id);
            if($value->storeType == 1){
                if($sellerList[$key]->province_id == 0){
                    $sellerList[$key]->city = Region::where("id",$sellerList[$key]->city_id)->where('is_service', 1)->first();
                }else{
                    $sellerList[$key]->province = Region::where("id",$sellerList[$key]->province_id)->where('is_service', 1)->first();
                    $sellerList[$key]->city = Region::where("id",$sellerList[$key]->city_id)->where('is_service', 1)->first();
                }
            }
        }
        $ds['sellerlist'] = $sellerList;
        return $ds;
    }
    /**
     * 搜索商品	caiq
     * @param mixed $cateId
     * @param mixed $page
     * @param mixed $sort
     * @param mixed $keywords
     * @param mixed $userId
     * @return mixed
     */
    public static function getGoodsList($cateId, $page,$pageSize = 6 , $sort, $keywords = '', $userId = 0, $mapPoint = '0 0',$cityId = 0,$vsType='',$sellerId = '')
    {
        $ds = array();
        if (empty($keywords))
        {
            return false;
        }
        $dbPrefix = DB::getTablePrefix();
        $mapPoint = $mapPoint == '' ? '0 0' : str_replace(',', ' ', $mapPoint);
        $sql_cats = "";
        if($cateId > 0)
        {
            $cateIds = [$cateId];
            $childs = SellerCate::where('pid', $cateId)->get();
            foreach ($childs as $child) {
                $cateIds[] = $child['id'];
            }
            $cateIds = implode(',', $cateIds);
            $sql_cats = " AND G.cate_id IN({$cateIds})";
        }

        switch ($sort)
        {
            case 1: // 距离
                $sort = "S.distance ASC";
                break;
            case 2: // 价格
                $sort = "G.price ASC";
                break;
            case 3: // 价格
                $sort = "E.sales_volume desc";
                break;

            default:
                if($sellerId){
                    $sort = "E.sales_volume desc";

                }else{
                    $sort = "S.distance ASC";

                }
                break;
        }

        $size = $pageSize;
        $start = max(0, ($page - 1) * $size);

        if($sellerId){

            //商品数
            $sql_goods_total = "SELECT count(G.id) AS goods_total
								FROM {$dbPrefix}goods AS G
								WHERE G.status=1 AND G.stock>0 AND G.seller_id ={$sellerId}
								AND G.name LIKE '%{$keywords}%' ";
            $ds['goods_total'] = DB::select($sql_goods_total)[0]->goods_total;
            /* 查询商品*/

            $sql_goods = "SELECT E.sales_volume, G.name AS goods_name ,G.images AS goods_image,G.id AS goods_id,G.price,S.distance,S.`name`,S.store_type AS storeType,S.province_id,S.city_id,S.area_id,S.id AS sellerId
				 FROM {$dbPrefix}goods AS G
				INNER JOIN (SELECT  id,`name`,`province_id`,`city_id`,`area_id`,`store_type`,(ST_Distance(map_point,GeomFromText('Point({$mapPoint})'))/0.0111) AS distance FROM {$dbPrefix}seller
																				WHERE status = 1 	/* 已审核 */
																				AND is_check = 1 /* 正常 */
																				AND (goods_keywords LIKE '%{$keywords}%' OR name LIKE '%{$keywords}%')) AS S
				ON G.seller_id = {$sellerId}
				left JOIN {$dbPrefix}goods_extend as E on  E.goods_id = G.id
				WHERE G.status=1 AND G.stock > 0 AND G.seller_id IN (S.id) {$sql_cats}
				AND G.name LIKE '%{$keywords}%' ORDER BY {$sort}, G.cate_id ASC LIMIT {$start}, {$size}";
            $goodsList = DB::select($sql_goods);

        }else{


            if($page==1){
                //商品数
                $sql_goods_total = "SELECT count(G.id) AS goods_total
								FROM {$dbPrefix}goods AS G INNER JOIN
								(SELECT  id FROM {$dbPrefix}seller
											WHERE (store_type = 1 OR (store_type = 0 AND city_id = {$cityId} OR province_id = {$cityId}) )
											AND status = 1
											AND is_check = 1
											AND (store_type = 1) OR (store_type = 0 AND ST_Contains(map_pos,GeomFromText('Point({$mapPoint})')))
											AND (goods_keywords LIKE '%{$keywords}%' OR name LIKE '%{$keywords}%')) AS S
								ON G.seller_id = S.id
								WHERE G.status=1 AND G.stock>0 AND G.seller_id IN (S.id)
								AND G.name LIKE '%{$keywords}%' ";
                //商家数
                $sql_seller_total = "SELECT  count(id) AS seller_total FROM {$dbPrefix}seller WHERE (store_type = 1 OR (store_type = 0 AND city_id = {$cityId} OR province_id = {$cityId}) )
										AND status = 1
										AND is_check = 1
										AND (goods_keywords LIKE '%{$keywords}%' OR name LIKE '%{$keywords}%'  )";
                $ds['goods_total'] = DB::select($sql_goods_total)[0]->goods_total;
                $ds['seller_total'] = DB::select($sql_seller_total)[0]->seller_total;
            }
            if($vsType == ""){
                /* 查询商品 */
                $sql_goods = "SELECT G.name AS goods_name ,G.images AS goods_image,G.id AS goods_id,G.price,S.distance,S.`name`,S.store_type AS storeType,S.province_id,S.city_id,S.area_id,S.id AS sellerId
				FROM {$dbPrefix}goods AS G INNER JOIN
				(SELECT  id,`name`,`province_id`,`city_id`,`area_id`,`store_type`,(ST_Distance(map_point,GeomFromText('Point({$mapPoint})'))/0.0111) AS distance FROM {$dbPrefix}seller
																				
																				WHERE (store_type = 1 OR (store_type = 0 AND city_id = {$cityId} OR province_id = {$cityId}) )
																				AND status = 1 	/* 已审核 */
																				AND is_check = 1 /* 正常 */
																				AND (store_type = 1  OR (store_type = 0 AND ST_Contains(map_pos,GeomFromText('Point({$mapPoint})'))))

																				/*AND ST_Contains(map_pos,GeomFromText('Point({$mapPoint})'))*/
																				AND (goods_keywords LIKE '%{$keywords}%' OR name LIKE '%{$keywords}%')) AS S 


				ON G.seller_id = S.id
				WHERE G.status=1 AND G.stock > 0 AND G.seller_id IN (S.id) {$sql_cats}
				AND G.name LIKE '%{$keywords}%' ORDER BY {$sort}, G.cate_id ASC LIMIT {$start}, {$size}";


            }else{

                $sellerId = ONESELF_SELLER_ID;

                $scope = Seller::find($sellerId);
                $scope = $scope->businessScope;
                array_push($scope,0);
                if(!in_array($cityId,$scope)){
                    return null;
                }
                /* 查询商品 */
                $sql_goods = "SELECT G.name AS goods_name ,G.images AS goods_image,G.id AS goods_id,G.price,S.distance,S.`name`,S.store_type AS storeType,S.province_id,S.city_id,S.area_id,S.id AS sellerId
				FROM {$dbPrefix}goods AS G INNER JOIN
				(SELECT  id,`name` ,`province_id`,`city_id`,`area_id`,`store_type`,(ST_Distance(map_point,GeomFromText('Point({$mapPoint})'))/0.0111) AS distance FROM {$dbPrefix}seller
																				WHERE status = 1 	/* 已审核 */
																				AND id = {$sellerId} 	/* 已审核 */
																				AND is_check = 1 /* 正常 */
																				/*AND ST_Contains(map_pos,GeomFromText('Point({$mapPoint})'))*/
																				)AS S 
				ON G.seller_id = S.id
				WHERE G.status=1  AND G.stock>0 AND G.seller_id IN (S.id) {$sql_cats}
				AND G.name LIKE '%{$keywords}%' ORDER BY {$sort}, G.cate_id ASC LIMIT {$start}, {$size}";
            }

            $goodsList = DB::select($sql_goods);
        }


        $isAllUserPrimary = Invitation::where("user_status",1)->pluck('is_all_user_primary') / 100;
        foreach ($goodsList as $key=>$val) {
            if(strstr($val->goods_image,',') == false){
                $goodsList[$key]->images = $val->goods_image;
                $goodsList[$key]->image = $val->goods_image[0];
            }else{
                $images = explode(',',$val->goods_image);
                $goodsList[$key]->images = $images[0];
                // unset($goodsList[$key]->goods_image);
            }
            $goodsList[$key]->sales_volume = GoodsExtend::where('goods_id',$val->goods_id)->pluck("sales_volume");
            $goodsList[$key]->shareNum = GoodsExtend::where('goods_id',$val->goods_id)->pluck("share_num");
            if($goodsList[$key]->province_id == 0){
                $goodsList[$key]->city = Region::where("id",$val->city_id)->where('is_service', 1)->first();
            }else{
                $goodsList[$key]->province = Region::where("id",$val->province_id)->where('is_service', 1)->first();
                $goodsList[$key]->city = Region::where("id",$val->city_id)->where('is_service', 1)->first();
            }
            if($isAllUserPrimary > 0){
                $goodsList[$key]->isAllUserPrimary = (double)round($isAllUserPrimary * $val->price,2);
            }
        }

        $ds['goodslist'] = $goodsList;
        return $ds;
    }


    /**
     * 检查是否注册商家并且通过
     * @param int $id 会员编号
     */
    public function checkUser($id){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => '成功'
        );

        $data = Seller::where('seller.user_id', $id)
            ->join("seller_authenticate", "seller.id", "=", "seller_authenticate.seller_id")
            ->select("*")
            ->first();

        if($data)
        {
            if($data->is_check == 1)
            {
                $data->appurl = u("wap#seller/app");
                $result['code'] = 30019;
                $result['msg'] = '失败';
            }

            $data->detail = $data->brief;
            DB::connection()->enableQueryLog();
            $data->cateIds = SellerCateRelated::where("seller_id", $data->id)
                ->join("seller_cate", "seller_cate_related.cate_id", "=", "seller_cate.id")
                ->select("seller_cate.id", "seller_cate.name")
                ->get()
                ->toArray();
        }

        $result['data'] = $data;

        return $result;
    }

    /**
     * 商家注册
     */
    public static function createSeller($userId, $sellerType, $storeType, $logo, $name, $cateIds, $address,$addressDetail,$provinceId,$cityId,$areaId,$mapPointStr,$mapPosStr, $mobile, $pwd, $idcardSn, $idcardPositiveImg, $idcardNegativeImg, $businessLicenceImg, $introduction, $serviceFee, $deliveryFee,$contacts, $serviceTel, $refundAddress) {
        ini_set("max_execution_time", "40");

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.submit')
        );

        $seller = Seller::where('user_id', $userId)
            ->first();

        if($seller && $seller->is_check != -1)
        {
            $result['code'] = 30022;
            return $result;
        }
        $rules = array(
            'mobile'                        => ['required','regex:/^1[0-9]{10}$/'],
            'name'                          => ['required','min:2','max:30'],
            'logo'                          => ['required'],
            'cateIds'                       => ['required'],
            'address'                       => ['required'],
            'idcardSn'                      => ['required'],
            'idcardPositiveImg'             => ['required'],
            'idcardNegativeImg'             => ['required'],
            'contacts'                       => ['required'],
            'serviceTel'                     => ['required']
            // 'businessLicenceImg'            => ['required'],
            // 'introduction'                  => ['required'],
        );

        $messages = array(
            'mobile.required'               => '30020',
            'mobile.regex'                  => '30021',
            'name.required'                 => '30003',
            'name.min'                      => '30004',
            'name.max'                      => '30005',
            'logo.required'                 => '30006',
            'cateIds.required'              => '30007',
            'address.required'              => '30008',   // 请输入地址
            'idcardSn.required'             => '30009',
            // 'idcardSn.regex'                => '30010',
            'idcardPositiveImg.required'    => '30011',
            'idcardNegativeImg.required'    => '30012',
            'contacts.required'              =>  '30023',
            'serviceTel.required'           =>  '30024',
            // 'serviceTel.regex'              =>  '30025'
            // 'businessLicenceImg.required'   => '30013',
            // 'introduction.required'         => '30014',   //

        );

        $validator = Validator::make([
            'mobile'                => $mobile,
            'name'                  => $name,
            'logo'                  => $logo,
            'cateIds'               => $cateIds,
            'address'               => $address,
            'idcardSn'              => $idcardSn,
            'idcardPositiveImg'     => $idcardPositiveImg,
            'idcardNegativeImg'     => $idcardNegativeImg,
            'contacts'              => $contacts,
            'serviceTel'              => $serviceTel
            // 'businessLicenceImg'    => $businessLicenceImg,
            // 'introduction'          => $introduction,
        ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        if($seller == false)
        {
            $seller = new Seller;
        }

        // 身份证号格式错误
        $resu = SellerService::isCreditNo($idcardSn);
        if(!$resu){
            $result['code'] = 30010;
            return $result;
        }

        $isMob="/^1[0-9]{10}$/";
        $isTel="/^([0-9]{3,4})?[0-9]{7,8}$/";

        if(!preg_match($isMob, $serviceTel) && !preg_match($isTel, $serviceTel))
        {
            $result['code'] = 30025;
            return $result;
        }

        //全国店验证退货地址
        //周边店 验证范围
        if($storeType == 1)
        {
            $refundAddress = trim($refundAddress);
            if(empty($refundAddress)){
                $result['code'] = 30630;
                return $result;
            }
        }
        else
        {
            if(empty($mapPosStr)){
                $point = explode(',', $mapPointStr);
                $point1 = ($point[0] + 0.01) . ',' . ($point[1] + 0.01);
                $point2 = ($point[0] + 0.01) . ',' . ($point[1] - 0.01);
                $point3 = ($point[0] - 0.01) . ',' . ($point[1] - 0.01);
                $point4 = ($point[0] - 0.01) . ',' . ($point[1] + 0.01);
                $mapPosStr = $point1 . '|' . $point2 . '|' . $point3 . '|' . $point4;
            }

            $mapPos = Helper::foramtMapPos($mapPosStr);

            if (!$mapPos) {
                $result['code'] = 30617;    // 服务范围错误
                return $result;
            }
            $seller->map_pos          = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            $seller->map_pos_str      = $mapPos['str'];
        }
        DB::beginTransaction();
        $mapPoint = Helper::foramtMapPoint($mapPointStr);
        if (!$mapPoint){
            $result['code'] = 30615;    // 地图定位错误
            return $result;
        }
        $seller->map_point        = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
        $seller->map_point_str    = $mapPoint;
        try {
            $user = User::find($userId);
            if (!$user) {
                $result['code'] = 30015;
                return $result;
            }

            $logo = self::moveUserImage($user->id, $logo);
            if (!$logo) {//转移图片失败
                $result['code'] = 30016;
                return $result;
            }

            //cz
            if(!$provinceId){
                $zx = array("1", "18", "795", "2250");
                if(in_array($cityId,$zx)){
                    $provinceId = $cityId;
                }else{
                    $city_arrs = Region::where('id',$cityId)->first();
                    if(!empty($city_arrs)){
                        $provinceId = $city_arrs->pid;
                    }
                }
            }

            $seller->type             = $sellerType;
            $seller->store_type       = $storeType;
            $seller->user_id          = $user->id;
            $seller->mobile           = $mobile;
            $seller->logo             = $logo;
            $seller->name             = $name;
            $seller->name_match       = String::strToUnicode($name, '+');
            $seller->address          = $address;
            $seller->address_detail   = $addressDetail;
            $seller->province_id      = $provinceId;
            $seller->city_id          = $cityId;
            $seller->area_id          = $areaId;
            $seller->create_time      = UTC_TIME;
            $seller->create_day       = UTC_DAY;
            $seller->brief            = $introduction;
            $seller->service_fee      = floatval($serviceFee);
            $seller->delivery_fee     = floatval($deliveryFee);
            $seller->contacts         = $contacts;
            $seller->service_tel      = $serviceTel;
            $seller->is_check         = 0;
            $seller->refund_address   = $refundAddress;

            $seller->save();
            //创建商家扩展信息表
            if(SellerExtend::where("seller_id", $seller->id)->first() == false)
            {
                $sellerExtend = new SellerExtend();
                $sellerExtend->seller_id = $seller->id;
                $sellerExtend->save();
            }

            $auth = SellerAuthenticate::where('idcard_sn', $idcardSn)->first();

            if($auth == true)
            {
                if($auth->seller_id != $seller->id)
                {
                    $result['code'] = 30017;    //身份证号码已存在
                    DB::rollback();
                    return $result;
                }
            }
            else
            {
                $auth = SellerAuthenticate::where('seller_id', $seller->id)->first();

                if($auth == false)
                {
                    $auth = new SellerAuthenticate();
                }
            }

            $idcardPositiveImg = self::moveSellerImage($seller->id, $idcardPositiveImg);
            if (!$idcardPositiveImg)
            {
                //转移图片失败
                $result['code'] = 30016;
                return $result;
            }

            $idcardNegativeImg = self::moveSellerImage($seller->id, $idcardNegativeImg);
            if (!$idcardNegativeImg)
            {
                //转移图片失败
                $result['code'] = 30016;
                return $result;
            }

            if($seller->type == Seller::SERVICE_ORGANIZATION)
            {
                if($businessLicenceImg == false)
                {
                    $result['code'] = 30013; // 公司营业执照相片不能为空
                    return $result;
                }

                $seller_auth = SellerAuthenticate::where('seller_id',$seller->id)->select('business_licence_img','certificate_img')->get();
                //如果不相同才有水印
                if($seller_auth->business_licence_img != $businessLicenceImg){
                    $watermark = SystemConfig::getConfig('watermark_logo');
                    if(!empty($watermark)){
                        //水印图片
                        $businessLicenceImg = \YiZan\Utils\Image::watermark($businessLicenceImg);
                    }else{
                        $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);
                        if (!$businessLicenceImg){
                            //转移图片失败
                            $result['code'] = 30016;
                            return $result;
                        }
                    }
                }
            }

            if ($seller->type == Seller::SELF_ORGANIZATION)
            {
                $seller_auth = SellerAuthenticate::where('seller_id',$seller->id)->select('business_licence_img','certificate_img')->get();
                //如果不相同才有水印
                if($seller_auth->certificate_img != $businessLicenceImg){
                    $watermark = SystemConfig::getConfig('watermark_logo');
                    if(!empty($watermark)){
                        //水印图片
                        $businessLicenceImg = \YiZan\Utils\Image::watermark($businessLicenceImg);
                    }else{
                        $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);
                        if (!$businessLicenceImg){
                            //转移图片失败
                            $result['code'] = 30016;
                            return $result;
                        }
                    }
                }

                // if($businessLicenceImg == false)
                // {
                //     $result['code'] = 10207; // 资质证书不能为空
                //     return $result;
                // }

                // $certificateImg = self::moveSellerImage($seller->id, $businessLicenceImg);

                // if (!$certificateImg) 
                // {
                //     //转移图片失败
                //     $result['code'] = 30016;
                //     return $result;
                // }
                // $certificateImg = $certificateImg;
            }

            $auth->seller_id            = $seller->id;
            $auth->idcard_sn            = $idcardSn;
            $auth->idcard_positive_img  = $idcardPositiveImg;
            $auth->idcard_negative_img  = $idcardNegativeImg;
            $auth->certificate_img      = $businessLicenceImg; //isset($certificateImg) ? $certificateImg : NULL;
            $auth->business_licence_img = $businessLicenceImg;
            $auth->update_time          = UTC_TIME;
            $auth->save();

            //if($sellerType == Seller::SELF_ORGANIZATION)
            //{
            //如果个人加盟版 则保存至员工表
            $staff = SellerStaff::where("user_id", $user->id)->where("seller_id", $seller->id)->first();

            if($staff == false)
            {
                $staff = new SellerStaff();
            }
            //个人加盟、商家加盟都生成一个员工
            if($sellerType == Seller::SELF_ORGANIZATION) {
                $staff->type           = 0;
            } else {
                $staff->type           = 3;
            }
            $staff->user_id            = $user->id;
            $staff->seller_id          = $seller->id;
            $staff->avatar             = $user->avatar;
            $staff->mobile             = $mobile;
            $staff->name               = $name;
            $staff->name_match         = String::strToUnicode($name.$mobile);
            $staff->address            = $address;
            $staff->status             = 1;
            $staff->create_time        = UTC_TIME;
            $staff->create_day         = UTC_DAY;
            $staff->save();

            //保存员工扩展信息
            if(SellerStaffExtend::where("staff_id", $staff->id)->where("seller_id", $seller->id)->first() == false)
            {
                $sellerStaffExtend = new SellerStaffExtend();
                $sellerStaffExtend->staff_id = $staff->id;
                $sellerStaffExtend->seller_id = $seller->id;;
                $sellerStaffExtend->save();
            }

            //}
            // var_dump($cateIds);
            SellerCateRelated::where('seller_id', $seller->id)->delete();
            $cateIds = is_array($cateIds) ? $cateIds : explode(',', $cateIds);
            foreach ($cateIds as $key => $value)
            {
                $cate = new SellerCateRelated();
                $cate->seller_id = $seller->id;
                $cate->cate_id = $value;
                $cate->save();
            }

            DB::commit();

            // $mapPoint = '0,0';
            // $mapPos["pos"] = '0 0,10 0,10 10,0 10,0 0';

            // if(SellerMap::where("seller_id", $seller->id)->first() == false)
            // {
            //     $sellerMap = new SellerMap();
            //     $sellerMap->seller_id       = $seller->id;
            //     $sellerMap->map_point       = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            //     $sellerMap->map_pos         = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            //     $sellerMap->save();
            // }

            //周边店保存地理信息
            if($storeType == 1)
            {
                //全国店无范围,默认范围值 无效的 只为填充数据
                $mapPos = '31.90797991052 102.20781720873,31.913517218413 102.2418346142,31.894941463557 102.25701865688,31.887726973701 102.23846511045,31.887216933335 102.21802328888,31.90797991052 102.20781720873';
                $addMapArr['map_pos'] = DB::raw("GeomFromText('Polygon((". $mapPos ."))')");
                $addMapArr['map_point'] = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPointStr) . ")')");
            }
            else
            {
                $addMapArr['map_pos'] = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
                $addMapArr['map_point'] = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            }

            $addMapArr['seller_id'] = $seller->id;

            if(SellerMap::where("seller_id", $seller->id)->first() == false)
            {
                SellerMap::insert($addMapArr);
            }

            $result['data'] = $seller;
        }
        catch (Exception $e)
        {
            DB::rollback();
            $result['code'] = 30018;
        }
        return $result;
    }


}
