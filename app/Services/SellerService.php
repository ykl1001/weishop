<?php namespace YiZan\Services;

use YiZan\Models\Seller;
use YiZan\Models\Goods;
use YiZan\Models\Order;
use YiZan\Models\SellerExtend;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\SellerCreditRank;
use YiZan\Models\StaffServiceTime;
use YiZan\Models\SellerServiceTime;


use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;

use DB, Lang,Validator;

/**
 * 服务机构&个人
 */
class SellerService extends BaseService {
    /**
     * 获取服务机构&个人
     * @param  integer  $sellerId 编号
     * @param  integer  $userId   会员编号
     * @return Seller
     */
    public static function getSeller($sellerId, $userId = 0) {
        if ($userId > 0) {
            return Seller::with(array('collect' => function($query) use($userId) {
                    $query->where('user_id', $userId);
                }, 'extend.creditRank', 'staff'))->find($sellerId);
        } else {
            return Seller::with('extend.creditRank', 'staff')->find($sellerId);
        }
    }

    /**
     * 获取卖家
     * @param int $sellerId 卖家id
     * @param int $userId 用户id
     * @return array 卖家信息
     */
    public static function getById($sellerId = 0, $userId = 0) {
        if($sellerId > 0) {
            return Seller::with('province', 'city', 'area')->find($sellerId);
        }
        
        if($userId > 0) {
            return Seller::where("user_id", $userId)->with('province', 'city', 'area')->first();
        }
        return null;
    }

    /**
     * Summary of getList
     * @param mixed $cateId 
     * @param mixed $page 
     * @param mixed $order 
     * @param mixed $sort 
     * @param mixed $keywords 
     * @param mixed $userId 
     * @return mixed
     */
    public static function getList($cateId, $page, $sort, $keywords = '', $userId = 0) 
    {
        $dbPrefix = DB::getTablePrefix();
        $mapPoint = $mapPoint ? str_replace(",", " ", $mapPoint) : '0 0';
        $sql = "
SELECT  S.map_point_str AS mapPoint,
        S.id, 
        S.name, 
        S.logo, S.mobile, 
        S.brief AS detail,
        IF(C.seller_id IS NOT NULL, 1, 0) AS isCollect,
        NULL AS banner,
        '8:00–23:00' AS businessHours, 
        delivery_fee AS deliveryFee,
        service_fee AS serviceFee,
        CONCAT('起送价<span style=\"color:red\">￥',
            S.service_fee,
            '</span>&nbsp;运费<span style=\"color:#ff2d4b\">',
            S.delivery_fee,
            '</span>元&nbsp;<span style=\"color:#ff2d4b\"></span>包邮') AS freight, 
        S.service_tel AS tel, 
        S.address,
        E.order_count AS orderCount,
        (SELECT IFNULL(ROUND(SUM(star)/COUNT(id),1),0) FROM {$dbPrefix}order_rate as DR where DR.seller_id = S.id) AS score,
        (SELECT COUNT(id) FROM {$dbPrefix}goods as G where G.seller_id = S.id and G.type = 1) as countGoods,
        (SELECT COUNT(id) FROM {$dbPrefix}goods as GS where GS.seller_id = S.id and GS.type = 2) as countService
    FROM {$dbPrefix}seller AS S
        LEFT OUTER JOIN {$dbPrefix}user_collect AS C ON S.id = C.seller_id
            AND C.user_id = {$userId}
            AND {$userId} > 0
            AND C.type = 2 /* 店铺 */
        INNER JOIN {$dbPrefix}seller_extend AS E ON E.seller_id = S.id
        WHERE S.status = 1 /* 已审核 */
        ";
        
        if($cateId > 0)
        {
            $sql = "
{$sql}
            AND EXISTS
            (
                SELECT 1
                    FROM {$dbPrefix}seller_cate_related AS R
                    WHERE R.seller_id = S.id
                        AND R.cate_id = {$cateId}
            )
            ";
        }
        
        if (!empty($keywords)) 
        {
            $keywords = String::strToUnicode($keywords,'+');
            
            $sql = "
{$sql}
            AND MATCH(S.name_match) AGAINST('{$keywords}' IN BOOLEAN MODE)
            ";
        }
        
        switch ($sort) 
        {
            case 1: // 销量倒序
                $sort = "E.order_count DESC";
            break;

            case 2: // 起送价倒序
                $sort = "S.service_fee DESC";
            break;
            
            default:
                $sort = "S.sort ASC";
                break;
        }
        
        $start = max(0, ($page - 1) * 20);
        
        $end = $start + 20;
        
        $sql = "
{$sql}
            ORDER BY {$sort}, S.id ASC
            LIMIT {$start}, {$end}
            ";
       
        return DB::select($sql);
    }

    public static function checkServiceArea($sellerId, $mapPoint) {
        $seller = Seller::find($sellerId);
        if (!$seller) {
            return false;
        }
        $count = Seller::where('id', $sellerId)
            ->whereRaw("ST_Contains(map_pos,GeomFromText('Point({$mapPoint})'))")
            ->count();
        
        return $count > 0;
    }

    public static function incrementExtend($sellerId, $field, $num = 1) 
    {
        SellerExtend::where('seller_id',$sellerId)->increment($field, $num);
    }

    public static function decrementExtend($sellerId, $field, $num = 1) 
    {
        $extend = SellerExtend::where('seller_id',$sellerId)->first();
        
        if($extend == true && $extend->$field > 0)
        {
            SellerExtend::where('seller_id',$sellerId)->decrement($field, $num);
        }
    }

    public static function updateComment($sellerId, $credit, $specialtyScore, $communicateScore, $punctualityScore) 
    {
        $extend = SellerExtend::where('seller_id',$sellerId)->first();
        $extend->comment_total_count++;
        $extend->comment_specialty_total_score += $specialtyScore;
        $extend->comment_specialty_avg_score = $extend->comment_specialty_total_score / $extend->comment_total_count;

        $extend->comment_communicate_total_score += $communicateScore;
        $extend->comment_communicate_avg_score = $extend->comment_communicate_total_score / $extend->comment_total_count;

        $extend->comment_punctuality_total_score += $punctualityScore;
        $extend->comment_punctuality_avg_score = $extend->comment_punctuality_total_score / $extend->comment_total_count;

        switch($credit) {
            case 'good'://好评
                $extend->comment_good_count++;
                $extend->credit_score++;
                $extend->credit_rank_id = self::getCreditRankId($extend->credit_rank_id, $extend->credit_score);
            break;

            case 'neutral'://中评
                $extend->comment_neutral_count++;
            break;

            case 'bad'://差评
                $extend->comment_bad_count++;
                $extend->credit_score--;
                $extend->credit_rank_id = self::getCreditRankId($extend->credit_rank_id, $extend->credit_score);
            break;
        }
        $extend->save();
    }
    /**
     * 删除评价重新统计
     * @param int $sellerId 商家编号
     * @param string $credit 评价类型
     * @param double $specialtyScore 专业总分
     * @param double $communicateScore 沟通总分
     * @param double $punctualityScore 守时总分
     */
    public static function deleteComment($sellerId, $credit, $specialtyScore, $communicateScore, $punctualityScore) 
    {
        $extend = SellerExtend::where('seller_id',$sellerId)->first();
        if(!$extend){
            return false;
        }
        $extend->comment_total_count--;
        $extend->comment_specialty_total_score -= $specialtyScore;
        $extend->comment_specialty_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_specialty_total_score / $extend->comment_total_count;

        $extend->comment_communicate_total_score -= $communicateScore;
        $extend->comment_communicate_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_communicate_total_score / $extend->comment_total_count;

        $extend->comment_punctuality_total_score -= $punctualityScore;
        $extend->comment_punctuality_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_punctuality_total_score / $extend->comment_total_count;

        switch($credit) {
            case 'good'://好评
                $extend->comment_good_count--;
                $extend->credit_score--;
                $extend->credit_rank_id = self::getCreditRankId($extend->credit_rank_id, $extend->credit_score);
                break;

            case 'neutral'://中评
                $extend->comment_neutral_count--;
                break;

            case 'bad'://差评
                $extend->comment_bad_count--;
                $extend->credit_score--;
                $extend->credit_rank_id = self::getCreditRankId($extend->credit_rank_id, $extend->credit_score);
                break;
        }
        $extend->save();
    }

    public static function getCreditRankId($id, $score) {
        $credit_rank = SellerCreditRank::where('min_score', '<=', $score)
                                    ->where('max_score', '>=', $score)
                                    ->first();
        if ($credit_rank && $credit_rank->id != $id) {
            return $credit_rank->id;
        }
        return $id;
    }

    /**
     * 获取10个推荐商家
     * @param string $apoint 经纬度
     */
    public static function getRecommendSellers($apoint, $cityId,$page = 1){
        $apoint = $apoint == '' ? '0 0' : str_replace(',', ' ', $apoint);

        $page = ($page !=1) ? $page : 1;
        $list = Seller::where('status', 1)
                      ->where('is_check', 1)
                      ->where('type', '<>', 3)
                      ->where('id', '<>', ONESELF_SELLER_ID)
                      ->where('store_type',0)
                      ->whereNotNull('map_point')
                      ->where(function($query) use ($cityId){
                           $query->where('city_id', $cityId)
                               ->orWhere('province_id', $cityId);
                      })->whereRaw(" ST_Contains(map_pos,GeomFromText('POINT({$apoint})')) ");
        if($apoint){
            $list->addSelect(DB::raw("ST_Distance(".env('DB_PREFIX')."seller.map_point,GeomFromText('POINT({$apoint})')) AS map_distance"));
            $list->orderBy('map_distance', 'ASC');
        }
        $hours = Time::toDate(UTC_TIME, 'H:i');
        $week = Time::toDate(UTC_TIME, 'w');
        $list = $list->addSelect(DB::raw(
            env('DB_PREFIX')."seller.*,
            (select IFNULL(ROUND(SUM(star)/COUNT(id),1),0) as score from ".env('DB_PREFIX')."order_rate as o where o.seller_id = ".env('DB_PREFIX')."seller.id) as score,
            (select count(id) from ".env('DB_PREFIX')."goods as g where g.seller_id = ".env('DB_PREFIX')."seller.id and g.type = 1 and g.status = 1) as countGoods,
            (select count(id) from ".env('DB_PREFIX')."goods as s where s.seller_id = ".env('DB_PREFIX')."seller.id and s.type = 2 and s.status = 1) as countService
            "))
                     ->skip(($page - 1) * 10)
                     ->take(10)
                     ->with(['sellerAuthIcon.icon','deliveryTimes','extend','serviceTimesCount' => function($query) use ($week, $hours){
                            $query->where('week', $week)
                                ->where('begin_time', '<=', $hours)
                                ->where(function($queryOne) use ($hours){
                                    $queryOne->where('end_time', '>=', $hours)
                                            ->orWhere('end_time', '00:00');
                                });
                     }])->get()->toArray();
        $dbPrefix = DB::getTablePrefix();
        foreach ($list as $key => $value) {
            if ($value['serviceFee'] > 0) {
                $serviceFee = '<font color="#979797">起送价</font><font color="red">￥'.$value['serviceFee'].'</font>';
            } else {
                $serviceFee = '<font color="#979797">无起送价</font>';
            }
            $html = $serviceFee . '&nbsp;<font color="#979797">运费</font><font color="#ff2d4b">'.$value['deliveryFee'].'</font><font color="#979797">元</font>&nbsp;<font color="#ff2d4b"></font>';
            $list[$key]['freight'] = $html;
            // $list[$key]['isDelivery'] = 0;
            $time =  $value['deliveryTimes'];
            foreach ($time as $k => $v) {
                $list[$key]['stimes'][] = $v['stime'] . '-' . $v['etime'];
            }
            $list[$key]['deliveryTime'] = $list[$key]['stimes'] ? implode(',', $list[$key]['stimes']) : '00:00-24:00';

            $isDelivery = self::isCanBusiness($value->id);
            $list[$key]['isDelivery'] = $isDelivery;
            if (empty($value['mapPointStr'])) {
                unset($list[$key]['mapPoint']);
            }
            $list[$key]['orderCount'] = $value['extend']['orderCount'];
            unset($list[$key]['extend']);
            $list[$key]['isBussiness'] = $value['serviceTimesCount'] > 0 ? true : false;

            //商户活动
            $list[$key]['activity'] = ActivityService::getSellerActivity($value['id']);
        }

        return $list;
    }

    /**
     * 是否营业
     */
    public static function isCanBusiness($sellerId) {
        $isDelivery = 0;
        $hours = Time::toDate(UTC_TIME, 'H:i');
        $week = Time::toDate(UTC_TIME, 'w');
        $servicetime = StaffServiceTime::where('seller_id', $sellerId)
                                    ->where('week', $week)
                                    ->where('begin_time','<=', $hours)
                                    ->where(function($query) use ($hours){
                                        $query->where('end_time', '>=', $hours)->orWhere('end_time', '00:00');
                                    })
                                    ->first();

        $isDelivery = $servicetime > 0 ? 1 : 0;
        return $isDelivery;
    }

    /**
     * 经营类型
     */
    public static function getSellerCateLists($sellerId){
        $list = SellerCateRelated::where('seller_id', $sellerId) 
                                 ->with('cates')
                                 ->get();
        
        foreach ($list as $key => $value) { 
            $list[$key] = $value['cates'];
        }
        return $list;
    }

    /** 
     * 获取订单年份
     */
    public function getyear($seller = 0){
        $list = Order::select(DB::raw("FROM_UNIXTIME(create_time,'%Y') as year_name"));
        if($seller != 0){
            $list->where('seller_id',$seller);
        }
        $list = $list->where('create_time', '<>', '')
                     ->groupBy('year_name')
                     ->get()
                     ->toArray();
        return $list;
    }

    /**
     * 商家月统计
     * 
     * 额业额 ＝ 实付金额+平台满减+首单减+优惠券+积分抵扣
     *
     * 佣金=（实付金额+平台满减+首单减+优惠券+积分抵扣）* 佣金比例 = 营业额 * 佣金比例
     *
     * 入账金额 = （实付金额+平台满减+首单减+优惠券+积分抵扣）- 佣金 = 营业额 - 佣金
     */
    public static function getBusinessListByMonth($sellerId, $month, $year, $cityId){   
        DB::connection()->enableQueryLog();
        $prefix = DB::getTablePrefix();
        $current = $year.'-'.sprintf("%02d", $month);
        $t = Time::toDate(Time::toTime($current), 't');  
        if($current == Time::toDate(UTC_TIME, 'Y-m')){
            $t = Time::toDate(UTC_TIME, 'd');
        } else if(Time::toTime($current) > Time::toTime(Time::toDate(UTC_DAY, 'Y-m'))){
            return ["list" => [], "sum" => []];
        }
        $sumsql = "SELECT seller_id,IFNULL(sum(pay_fee + system_full_subsidy + activity_new_money + discount_fee + integral_fee), 0) as totalPayfee,
                    IFNULL((SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0) + integral_fee + IF(discount_fee > total_fee,total_fee,discount_fee) + system_full_subsidy + activity_new_money - drawn_fee - send_fee)),0) AS totalSellerFee,
                    count(".$prefix."order.id) as totalNum,
                    IFNULL(sum(".$prefix."order.drawn_fee), 0) as totalDrawnfee , 
                    IFNULL(SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)), 0) AS totalOnline, 
                    IFNULL(SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)), 0) AS totalCash,
                    IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee,
                    IFNULL(sum(integral_fee), 0) as totalIntegralFee,
                        IFNULL(sum(system_full_subsidy), 0) as systemFullSubsidy,
                        IFNULL(sum(seller_full_subsidy), 0) as sellerFullSubsidy,
                        IFNULL(sum(activity_goods_money), 0) as activityGoodsMoney,
                        IFNULL(sum(activity_new_money), 0) as activityNewMoney,
                        IFNULL(sum(send_fee), 0) as sendFee
            FROM ".$prefix."order
            WHERE pay_status = 1 
            AND seller_id = ".$sellerId."
            AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
            OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) 
            OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) 
            OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))
            AND FROM_UNIXTIME(".$prefix."order.create_time + 28800,'%Y-%m') = '".$current."'";
        $sum = DB::select($sumsql);
        $seller = Seller::find($sellerId);
        /**
         sql说明：左连接order表 查询 id, name, totalPayfee(总金额), totalNum(总数量), totalDrawnfee(总佣金), totalOnline(总在线支付), totalCash(总现金支付), totalDiscountFee(总优惠金额), totalSellerFee(商家盈利)
         在线和现金支付数据 通过子查询实现
         */
        $sql = "select IFNULL(sum(".$prefix."order.pay_fee + ".$prefix."order.system_full_subsidy + ".$prefix."order.activity_new_money + ".$prefix."order.discount_fee + ".$prefix."order.integral_fee), 0) as totalPayfee,
                IFNULL((SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0) + integral_fee +IF(discount_fee > total_fee,total_fee,discount_fee) + system_full_subsidy + activity_new_money - drawn_fee - send_fee)),0) AS totalSellerFee,
                count(".$prefix."order.id) as totalNum,
                IFNULL(sum(".$prefix."order.drawn_fee), 0) as totalDrawnfee ,
                IFNULL(SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)), 0) AS totalOnline, 
                IFNULL(SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)), 0) AS totalCash,
                IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee,
                IFNULL(sum(integral_fee), 0) as totalIntegralFee,
                    IFNULL(sum(system_full_subsidy), 0) as systemFullSubsidy,
                    IFNULL(sum(seller_full_subsidy), 0) as sellerFullSubsidy,
                    IFNULL(sum(activity_goods_money), 0) as activityGoodsMoney,
                    IFNULL(sum(activity_new_money), 0) as activityNewMoney,
                    IFNULL(sum(send_fee), 0) as sendFee,
                FROM_UNIXTIME(create_time + 28800,'%Y-%m-%d') as daytime
                from ".$prefix."order 
                where ".$prefix."order.seller_id = ".$sellerId." 
                and ".$prefix."order.pay_status = 1  
                and FROM_UNIXTIME(".$prefix."order.create_time + 28800,'%Y-%m') = '".$current."' 
                AND (".$prefix."order.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                OR (".$prefix."order.status = ".ORDER_STATUS_USER_DELETE." AND ".$prefix."order.buyer_finish_time > 0 AND ".$prefix."order.cancel_time IS NULL) 
                OR (".$prefix."order.status = ".ORDER_STATUS_SELLER_DELETE." AND ".$prefix."order.auto_finish_time > 0 AND ".$prefix."order.cancel_time IS NULL) 
                OR (".$prefix."order.status = ".ORDER_STATUS_ADMIN_DELETE." AND ".$prefix."order.auto_finish_time > 0 AND ".$prefix."order.cancel_time IS NULL))
                GROUP BY FROM_UNIXTIME(".$prefix."order.create_time + 28800,'%Y-%m-%d')";
        $queryData = DB::select($sql);  
        $list = [];
        for($i = 1; $i <= $t; $i++) {
            $daytime = $current . '-' . sprintf("%02d", $i);  
            $dayData = [
                'totalPayfee' => 0,
                'totalNum' => 0,
                'totalDrawnfee' => 0,
                'totalSellerFee' => 0,
                'totalOnline' => 0,
                'totalCash' => 0,
                'totalIntegralFee' => 0,
                'totalDiscountFee' => 0, 
                'daytime' => $daytime,
                'systemFullSubsidy' => 0,
                'sellerFullSubsidy' => 0,
                'activityGoodsMoney' => 0,
                'activityNewMoney' => 0,
            ]; 
            $bool = false;
            foreach ($queryData as $item) { 
                $item = (array)$item;
                if($item['daytime'] == $daytime){
                    $bool = true; 
                    break;
                }
            }
            if($bool){
                $list[] = $item;
            } else {
                $list[] = $dayData;
            }
        }
        return ["list" => $list, "sum" => $sum[0],'seller'=>$seller];
    }

    /**
     * 商家日统计
     */
    public static function getBusinessListByDay($sellerId, $day, $sn, $status, $page, $pageSize){
        $prefix = DB::getTablePrefix();   
        $list = Order::where('seller_id', $sellerId) ;
        
        if($sn == true){
            $list->where('sn', $sn);
        }
        
        $timestr = 'create_time';
        
        if($status == true){
            switch ($status) {
                case '2'://已取消
                    $arrStatus = [ORDER_STATUS_CANCEL_USER, ORDER_STATUS_CANCEL_AUTO, ORDER_STATUS_CANCEL_SELLER, ORDER_STATUS_CANCEL_ADMIN, ORDER_STATUS_REFUND_AUDITING,ORDER_STATUS_CANCEL_REFUNDING,ORDER_STATUS_REFUND_HANDLE,ORDER_STATUS_REFUND_FAIL,ORDER_STATUS_REFUND_SUCCESS,ORDER_REFUND_SELLER_AGREE,ORDER_REFUND_SELLER_REFUSE,ORDER_REFUND_ADMIN_AGREE,ORDER_REFUND_ADMIN_REFUSE,ORDER_STATUS_USER_DELETE,ORDER_STATUS_SELLER_DELETE, ORDER_STATUS_ADMIN_DELETE]; 
                    break;
                case '3'://未完成
                    $arrStatus = [ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_START_SERVICE,ORDER_STATUS_PAY_DELIVERY,ORDER_STATUS_AFFIRM_SELLER,ORDER_STATUS_FINISH_STAFF]; 
                    break;
                
                default://已完成
                    $arrStatus = [ORDER_STATUS_FINISH_SYSTEM, ORDER_STATUS_FINISH_USER, ORDER_STATUS_USER_DELETE, ORDER_STATUS_SELLER_DELETE, ORDER_STATUS_ADMIN_DELETE];   
                    break;
            }
            $list->whereIn('status', $arrStatus);
        }   
        
        $list->whereRaw("FROM_UNIXTIME(".$timestr." + 28800,'%Y-%m-%d') = '".$day."'");

        $totalCount = $list->count(); 

        $list = $list->skip(($page - 1) * $pageSize)
                     ->take($pageSize)
                     ->get()
                     ->toArray();

        $sum = Order::where('seller_id', $sellerId)
                    ->whereRaw("FROM_UNIXTIME(create_time + 28800,'%Y-%m-%d') = '".$day."' AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))")
                    ->where('pay_status', 1) 
                    ->selectRaw("seller_id, count(id) as totalNum, IFNULL((SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0) + integral_fee + system_full_subsidy + activity_new_money + IF(discount_fee > total_fee,total_fee,discount_fee) - drawn_fee)),0) AS totalSellerFee") 
                    ->first() ; 
        $sum['seller'] = Seller::find($sellerId);
        return ["list" => $list, "totalCount" => $totalCount, "sum" => $sum];
    }

    /**
     * 验证身份证号
     * @param $vStr
     * @return bool
     */
    public function isCreditNo($vStr)
    {
        $vCity = array(
            '11', '12', '13', '14', '15', '21', '22',
            '23', '31', '32', '33', '34', '35', '36',
            '37', '41', '42', '43', '44', '45', '46',
            '50', '51', '52', '53', '54', '61', '62',
            '63', '64', '65', '71', '81', '82', '91'
        );

        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr))
        {
            return false;
        }

        if (!in_array(substr($vStr, 0, 2), $vCity))
        {
            return false;
        }

        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);

        if ($vLength == 18)
        {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        }
        else
        {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }

        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday)
        {
            return false;
        }
        if ($vLength == 18)
        {
            $vSum = 0;

            for ($i = 17; $i >= 0; $i--)
            {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
            }

            if ($vSum % 11 != 1)
            {
                return false;
            }
        }

        return true;
    }
	/**
     * 平台自营商家保存基础信息
		$this->request('id'),
		$this->request('name'),
		$this->request('logo'),
		$this->request('citys'),
		(int)$this->request('type',0),
		(float)$this->request('serviceFee'),
		(float)$this->request('deliveryFee'),
		(int)$this->request('isAvoidFee'),
		(float)$this->request('avoidFee'),
		$this->request('sendWay'),
		$this->request('reserveDays'),
		$this->request('sendLoop'),
		$this->request('serviceMode')
     */
    public static function oneselfsave($id,$name, $logo, $businessScope,$type, $serviceFee,$deliveryFee,$isAvoidFee,$avoidFee,$sendWay,$reserveDays,$sendLoop,$serviceMode,$serviceTel){
		
		$result = array('code' => self::SUCCESS,
						'data' => null,
						'msg' => '');

		$rules = array(
		   'name' 				 => ['required', 'max:20'],
		   'logo'              	 => ['required'],
		   'serviceFee'          => ['max:5'],
		   'deliveryFee'         => ['max:5'], 
		   'reserveDays'		 => ['required'],
		   'sendLoop'			 => ['required','gt:0']
		);

		$messages = array(
			'name.required' 				=> 30604,
			'name.max' 						=> 30922,
			'logo.required'                 => 10110,
			'serviceFee.max'     			=> 10617,
			'deliveryFee.max'    			=> 10618,
			'reserveDays.required'			=> 30925,	// 请填写可预约天数
			'reserveDays.max'				=> 30926,	// 请设置可预约天数范围在0~30之间
			'sendLoop.required'				=> 30927,	// 请设置配送时间周期
			'sendLoop.gt'					=> 30928,	// 配送时间周期必须大于0
		);

		$validator = Validator::make([
			'name' 					=> $name,
			'logo'                	=> $logo,
			'serviceFee' 			=> $serviceFee,
			'deliveryFee'			=> $deliveryFee,
			'reserveDays'			=> $reserveDays,
			'sendLoop'				=> $sendLoop,
		], $rules, $messages);

		//验证信息
		if ($validator->fails()) {
			$messages       = $validator->messages();
			$result['code'] = $messages->first();

			return $result;
		}
		if(count($sendWay) < 1) {
			$result['code'] = 30929;    // 请至少选择一个配送方式
            return $result;
		}

        if(count($businessScope) < 1) {
            $result['code'] = 309291;    // 请至少选择一个经营范围
            return $result;
        }

		if($reserveDays < 0 || $reserveDays >30 || !is_numeric($reserveDays)) {
            $result['code'] = 30926;    //请设置可预约天数范围在0~30之间
            return $result;
        }

        if($sendLoop <= 0 || !is_numeric($sendLoop)) {
            $result['code'] = 30928;    //配送时间周期必须大于0
            return $result;
        }
        if(!in_array($serviceMode,[1,2])) {
            $result['code'] = 40702;    //配送方式不正确或为空
            return $result;
        }

		//如果设置了满减 存入满减金额 如果没有设置 清空满减金额
		$isAvoidFee = $isAvoidFee == 1 ? $isAvoidFee : 0;
		$avoidFee = $isAvoidFee == 1 ? $avoidFee : null;
		
		if ($id > 0) {
			$seller = Seller::find($id);
			if (!$seller) {//服务站不存在
				$result['code'] = 30211;
				return $result;
			}
		} else {
			$seller = new Seller();
			$seller->type = $type;
		}
		
		DB::beginTransaction();
		
		try {
            $seller->business_scope  = base64_encode(serialize($businessScope));
            $seller->service_mode  = $serviceMode;
			$seller->name            = $name;
			$seller->province_id     = 0;
			$seller->city_id         = 0;
			$seller->area_id         = 0;
			$seller->service_fee 	 = $serviceFee;
			$seller->delivery_fee    = $deliveryFee;
			$seller->is_avoid_fee    = $isAvoidFee;
			$seller->avoid_fee    	 = $avoidFee;
			$seller->is_check 		 = 1;
			$seller->send_way 	 	 = count($sendWay) > 1 ? implode(",", $sendWay) : $sendWay[0]; 
			$seller->reserve_days	 = $reserveDays;
            $seller->send_loop		 = $sendLoop;
            $seller->service_tel		 = $serviceTel;
			$seller->save();
			
			$logo = self::moveSellerImage($seller->id, $logo);
			if (!$logo) {//转移图片失败
				$result['code'] = 10202;
				return $result;
			}
			$seller->logo = $logo;
			$seller->save();
            //保存扩展信息
            $sellerextend = SellerExtend::where('seller_id', $seller->id)->first();
            if (!$sellerextend) {
                $sellerExtend = new SellerExtend();
                $sellerExtend->seller_id = $seller->id;
                $sellerExtend->save();
            }
            DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$result['code'] = 99999;
		}

		return $result;
	}
    /**
     * 服务时间添加
     * @param $sellerId 商家编号
     * @param $goodsId 服务编号
     * @param array $weeks 星期, 0:星期日 1:星期一 2:星期二 3:星期三 4:星期四 5:星期五 6:星期六
     * @param array $hours 时间
     */
    public static function insert($sellerId,  $weeks, $hours) {

        $result = [
            'code'   => 0,
            'data'   => null,
            'msg'    => Lang::get('api_sellerweb.success.add')
        ];
        $check_seller = Seller::where('id', $sellerId)->first();

        if (!$check_seller) {
            $result['code'] = 50223; //商家不存在
            return $result;
        }

        if (!is_array($weeks) || count($weeks) < 1 || !is_array($hours) || count($hours) < 1 ) {
            $result['code'] = 50701; //选择的天和服务时间不能为空
            return $result;
        }

        //时间是否已经设置过
        $check = SellerServiceTime::whereIn("week", $weeks)->where('seller_id', $sellerId)->first();
        if ($check) {
            $result['code'] = 50702;
            return $result;
        }

        DB::beginTransaction();
        $sid = SellerServiceTimeSet::insertGetId([
            'seller_id' => $sellerId,
            'week' => json_encode($weeks),
            'hours' => json_encode($hours)
        ]);
        if ($sid > 0) {
            try {

                //服务时间表数据插入
                asort($hours);
                $hours = array_unique(array_values($hours));
                $beginTime = null;
                $endTime = null;
                $nextHour = null;
                for($i = 0, $count = count($hours); $i < $count; $i++) {
                    if($beginTime == null) {
                        $beginTime = $hours[$i];
                        $endTime = Time::toTime($hours[$i]) + 30 * 60;
                    }
                    if (isset($hours[$i + 1])) {
                        $nextHour = Time::toTime($hours[$i + 1]);
                    }
                    if( $endTime != $nextHour) {
                        foreach ($weeks as $value) {
                            SellerServiceTime::insert([
                                'service_time_id'   => $sid,
                                'seller_id'         => $sellerId,
                                'week'              => $value,
                                'begin_time'        => $beginTime,
                                'end_time'          =>Time::toDate($endTime,'H:i'),
                                'end_stime'         =>Time::toDate($endTime - 1,'H:i:s')
                            ]);
                        }
                        $beginTime = null;
                        $endTime = null;
                    }
                    else {
                        $endTime +=  30 * 60;
                    }
                }

                DB::commit();
                return  $result;
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 50703;
                return $result;
            }
        } else {
            DB::rollback();
            $result['code'] = 50703;
            return $result;
        }

    }
    /**
     * 获取商家总数
     * @return mixed
     */
    public static function sellercount(){
        return Seller::where(['is_check' => 1])
                        ->where('id','<>',ONESELF_SELLER_ID)
                        ->whereIn('type' , [1,2])->count();
    }
    /**
     * 获取物业总数
     * @return mixed
     */
    public static function propertycount(){
        return Seller::where(['type' => 3,'is_check' => 1])
                        ->where('id','<>',ONESELF_SELLER_ID)
                        ->count();
    }
}
