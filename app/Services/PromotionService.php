<?php namespace YiZan\Services;

use YiZan\Models\ActivityPromotion;
use YiZan\Models\PromotionSn;
use YiZan\Models\PromotionSellerCate;
use YiZan\Models\PromotionUnableDate;
use YiZan\Models\Promotion;
use YiZan\Models\Seller;
use YiZan\Models\User;
use YiZan\Models\OrderPromotion;
use YiZan\Models\GoodsSeller;
use YiZan\Models\ActivityLogs;
use YiZan\Models\SystemConfig;
use YiZan\Services\SystemConfigService as baseSystemConfigService;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use Lang,Exception, DB;
use Illuminate\Database\Eloquent\Model;
use YiZan\Models\PushMessage;
use YiZan\Models\ReadMessage;


class PromotionService extends BaseService
{

    /**
     * 获取会员第一个可用的优惠券
     * @param int $userId 会员编号
     */
    public static function getFirst($userId){
        $data = null;
        if ($userId > 0) {
            $result = PromotionSn::where('promotion_sn.user_id', $userId)
                ->where('promotion_sn.status',0)
                ->where('promotion_sn.use_time',0)
                ->where(function($query){
                    $query->where('promotion_sn.expire_time',0)
                        ->orWhere('promotion_sn.expire_time','>',UTC_TIME);
                })->leftJoin('promotion','promotion.id','=','promotion_sn.promotion_id')
                ->select('promotion.*','promotion_sn.*')->orderBy('promotion.data','desc')->first();
        }
        if ($result) {
            $result = $result->toArray();
            $data['id'] = $result['id'];
            $data['sn'] = $result['sn'];
            $data['status'] = $result['status'];
            $data['expireTime'] = $result['expireTime'];
            if ($result['type'] != 3) {
                $data['expireTimeStr'] = Time::toDate($result['expireTime'],'Y-m-d');
            } else {
                $data['expireTimeStr'] = '永久有效';
            }
            $data['name'] = $result['promotion']['type'] == 'offset' ? '抵用券' : '优惠券';
            $data['brief'] = $result['promotion']['name'];
            $data['type'] = $result['promotion']['type'];
            $data['money'] = $result['promotion']['money'];
        }
        return $data;
    }


    /**
     * 获取会员优惠券
     * @param int $userId 会员编号
     */
    public static function getById($userId,$id){
        $data = null;
        $result = PromotionSn::where('user_id', $userId)
            ->where('id', $id)
            ->where('use_time',0)
            ->where('begin_time', '<=', UTC_TIME)
            ->where('expire_time', '>', UTC_TIME)
            ->with('promotion')->first();
        if ($result) {
            $result = $result->toArray();
            $data['id'] = $result['id'];
            $data['sn'] = $result['sn'];
            $data['status'] = false;
            $data['expireTimeStr'] = Time::toDate($result['expireTime'],'Y-m-d');
            $data['name'] =$result['promotion']['name'];
            $data['brief'] = $result['promotion']['brief'];
            $data['type'] = 'money';
            $data['money'] = $result['money'];
            $data['limitMoney'] = $result['promotion']['limitMoney'];
            $data['expireTime'] = $result['expireTime'];
            if ($result['useTime'] > 0 || $result['expireTime'] < UTC_TIME) {
                $data['status'] = true;
            }
        }
        return $data;
    }
    /**
     * 获取会员优惠券
     * @param int $userId 会员编号
     */
    public static function getBest($userId,$price,$storeType,$sellerId){

        $ioff = baseSystemConfigService::getConfigByCode('integral_off');

        $integralOpenType = baseSystemConfigService::getConfigByCode('integral_open_type');

        if($ioff && ($storeType == $integralOpenType || $integralOpenType == 2 )){
            $userIntegral = User::where('id', $userId)->pluck('integral');
            $val = SystemConfig::where('code', 'cash_integral')->pluck('val');
            $limitVal = SystemConfig::where('code', 'limit_cash_integral')->pluck('val');
            $payFees = $limitVal == 100 ? ceil($price) : $price; //抵现上限比例为100%时, 支付金额转为整数
            $limitCashMoney = (double)round(($limitVal / 100 * $payFees), 2); //抵现上限金额
            $ableCashMoney = (double)round(($val / 100 * $userIntegral), 2); // 当前可抵现金额
            $cashMoney = $ableCashMoney <= $limitCashMoney ? $ableCashMoney : $limitCashMoney; //最终抵现金额
            $integral = (int)($cashMoney / ($val / 100)); // 抵现积分
        }else{
            $integral = 0;
        }

        $data = null;
        if($integral == 0){
            $storeType = $storeType == 1 ? 5 :4;

            $promotionWids =  PromotionSellerCate::whereIn('seller_cate_id',function($query) use ($sellerId){
                $query->select('cate_id')
                    ->from('seller_cate_related')
                    ->where('seller_id', $sellerId);
            })->groupBy('promotion_id')->lists('promotion_id');

            $result = PromotionSn::where('promotion_sn.user_id', $userId)
                ->where('promotion_sn.use_time',0)
                ->where('promotion_sn.begin_time', '<=', UTC_TIME)
                ->where('promotion_sn.expire_time', '>', UTC_TIME)
                ->join('promotion','promotion.id','=','promotion_sn.promotion_id')
                ->where(function($query) use ($price,$promotionWids, $sellerId,$storeType){
                    $query->where('limit_money','<=',$price)
                        ->where(function($query) use ($promotionWids, $sellerId,$storeType){
                            $query->where(function($queryOne) use ($promotionWids){
                                $queryOne->where('promotion.use_type',2)
                                    ->whereIn('promotion.id',$promotionWids);
                            })->orWhere(function($queryTwo) use ($sellerId){
                                $queryTwo->where('promotion.use_type',3)
                                    ->where('promotion.seller_id',$sellerId);
                            })->orWhere(function($queryThree) use ($storeType){
                                $queryThree->where('promotion.is_store',1)
                                    ->where('promotion.use_type',$storeType);
                            })->orWhere('promotion.use_type',1);
                        })->whereNotIn('promotion.id', function($query){
                            $query->select('promotion_id')
                                ->from('promotion_unable_date')
                                ->where('date_time',UTC_DAY);
                        });
                })
                ->addselect('promotion_sn.*','promotion.id as id2','promotion.money','promotion.type','promotion.limit_money')
                ->orderBy('promotion.money','desc')
                ->orderBy('promotion.type','asc')
                ->first();

            if(!empty($result)){
                $result = $result->toArray();
                $data['id'] = $result['id'];
                $data['sn'] = $result['sn'];
                $data['status'] = false;
                $data['expireTimeStr'] = Time::toDate($result['expireTime'],'Y-m-d');
                $data['name'] =$result['name'];
                $data['brief'] = $result['brief'];
                $data['type'] = 'money';
                $data['money'] = $result['money'];
                $data['limitMoney'] = $result['limitMoney'];
                $data['expireTime'] = $result['expireTime'];
                if ($result['useTime'] > 0 || $result['expireTime'] < UTC_TIME) {
                    $data['status'] = true;
                }
            }
        }

        return $data;
    }


    /**
     * 获取会员优惠券列表
     * @param  integer $userId 会员编号
     * @param  integer $status 状态
     * @param  integer $page   页码
     * @return array           优惠券数组
     */
    public static function getPromotionList($userId, $status, $page, $sellerId, $money) {

        /*
         * 奇葩要求 zg
         * ReadMessage::where('read_time',0)
                   ->where('user_id', $userId)
                   ->whereIn('message_id',function($query) use ($userId, $sellerId){
                           $query->select('id')
                               ->from('push_message')
                               ->where('seller_id', $sellerId);
                   })->update(['read_time' => UTC_TIME]);
         */
        $data = ['count'=>0, 'list'=>[]];
        $list = PromotionSn::where('is_del',0)->where('user_id', $userId);

        switch ($status) {
            case 1://已失效
                $list->where(function($query) {
                    $query->Where('use_time', '>', 0)
                        ->orWhere('expire_time','<', UTC_TIME);
                });
                break;
            case 2://可用
                $promotionWids =  PromotionSellerCate::whereIn('seller_cate_id',function($query) use ($sellerId){
                    $query->select('cate_id')
                        ->from('seller_cate_related')
                        ->where('seller_id', $sellerId);
                })->groupBy('promotion_id')->lists('promotion_id');
                $storeType = Seller::where('id',$sellerId)->pluck('store_type') == 1 ? 5 :4;
                $promotionIds = Promotion::where('limit_money','<=',$money)
                    ->where(function($query) use ($promotionWids, $sellerId,$storeType){
                        $query->where(function($queryOne) use ($promotionWids){
                            $queryOne->where('use_type',2)
                                ->whereIn('id',$promotionWids);
                        })->orWhere(function($queryTwo) use ($sellerId){
                            $queryTwo->where('use_type',3)
                                ->where('seller_id',$sellerId);
                        })->orWhere(function($queryThree) use ($storeType){
                            $queryThree->where('is_store',1)
                                ->where('use_type',$storeType);
                        })->orWhere('use_type',1);
                    })->whereNotIn('id', function($query){
                        $query->select('promotion_id')
                            ->from('promotion_unable_date')
                            ->where('date_time',UTC_DAY);
                    })->lists('id');

                $list->where('use_time', 0)
                    ->where('begin_time', '<=', UTC_TIME)
                    ->where('expire_time', '>=', UTC_TIME)
                    ->whereIn('promotion_id',$promotionIds);

                break;

            default://未使用
                $list->where('use_time',0)
                    ->where('expire_time','>',UTC_TIME);
                break;
        }



        $data['count'] = $list->count();
        $list = $list->with('promotion')->orderBy('id','desc')->skip(($page - 1) * 20)->take(20)->get()->toArray();

        foreach ($list as $key => $val){
            $data['list'][$key]['id'] = $val['id'];
            $data['list'][$key]['sn'] = $val['sn'];
            $data['list'][$key]['status'] = false;
            $data['list'][$key]['beginTimeStr'] = Time::toDate($val['beginTime'],'Y-m-d');
            $data['list'][$key]['expireTimeStr'] = Time::toDate($val['expireTime'],'Y-m-d');
            $data['list'][$key]['name'] =$val['promotion']['name'];
            $data['list'][$key]['brief'] = $val['promotion']['brief'];
            if ($val['promotion']['useType'] == 2) {
                $data['list'][$key]['useTypeStr'] = '仅限于指定分类使用';
            } elseif ($val['promotion']['useType'] == 3) {
                $data['list'][$key]['useTypeStr'] = "仅限于‘".$val['seller']['name']."’店铺使用";
            }  elseif ($val['promotion']['useType'] ==4) {
                $data['list'][$key]['useTypeStr'] = '仅限于周边店使用';
            }  elseif ($val['promotion']['useType'] == 5) {
                $data['list'][$key]['useTypeStr'] = '仅限于全国店使用';
            } else {
                $data['list'][$key]['useTypeStr'] = '不限制';
            }
            $data['list'][$key]['type'] = 'money';
            $data['list'][$key]['money'] = $val['money'];
            $data['list'][$key]['limitMoney'] = $val['promotion']['limitMoney'];
            $data['list'][$key]['expireTime'] = $val['expireTime'];
            $data['list'][$key]['useTime'] = $val['useTime'];
            if ($val['useTime'] > 0 || $val['expireTime'] < UTC_TIME) {
                $data['list'][$key]['status'] = true;
            }
        }
        // dd($data);
        return $data;
    }


    /**
     * 优惠券兑换
     * @param  int    $userId 会员编号
     * @param  string $sn     兑换码
     * @return array          会员优惠券信息
     */
    public static function exchangePromotion($userId, $sn) {
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.user_exchange_promotion')
        );
        if (empty($sn)) {//兑换码为空
            $result['code'] = 10301;
            return $result;
        }


        $promotionSn = PromotionSn::where('sn', $sn)->first();
        if (!$promotionSn) {//不存在
            $result['code'] = 10309;
            return $result;
        } elseif ($promotionSn['user_id'] > 0) {//已兑换
            $result['code'] = 10303;
            return $result;
        }
        $expire_time = $promotionSn->expire_time;
        if ($expire_time <= UTC_TIME) {
            $result['code'] = 10304;
            return $result;
        }


        $promotionSn->user_id = $userId;
        $promotionSn->send_time = UTC_TIME;
        if (false === $promotionSn->save()) {
            $result['code'] = 10305;
            return $result;
        }
        $datas = $promotionSn->with('promotion')->where('id', $promotionSn->id)->first()->toArray();

        $data['id'] = $datas['id'];
        $data['sn'] = $datas['sn'];
        $data['status'] = false;
        $data['expireTimeStr'] = Time::toDate($datas['expireTime'],'Y-m-d');
        $data['name'] =$datas['promotion']['name'];
        $data['brief'] = $datas['promotion']['brief'];
        $data['type'] = 'money';
        $data['money'] = $datas['money'];
        $data['limitMoney'] = $datas['promotion']['limitMoney'];
        $data['expireTime'] = $datas['expireTime'];
        if ($datas['useTime'] > 0 || $datas['expireTime'] < UTC_TIME) {
            $data['status'] = true;
        }
        $result['data'] = $data;
        return $result;
    }
    /**
     * 领取优惠卷
     * @param int $userId 用户编号
     * @param int $promotionId 优惠卷编号
     */
    /**
     * [receivePromotion 领取优惠券]
     * @param  [type]  $userId       [会员id]
     * @param  [type]  $promotionId  [优惠券id]
     * @param  boolean $isSystemSend [是否是系统发放]
     * @param  boolean $isBuy        [是否是购买]
     * @return [type]                [description]
     */
    public static function receivePromotion($userId, $promotionId ,$activityId,$isSystemSend = false, $isBuy = false,$zi = 'Z')
    {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.user_get_promotion')
        );
        $promotion = Promotion::where("id", $promotionId)->where('status',1)->first();
        if($promotion == false || ($promotion->send_type != 1 && !$isSystemSend ) ){
            $result["code"] = 10302; // 优惠券不存在
            return $result;
        }
        $promotionSn = PromotionSn::where("promotion_id", $promotionId)
            ->where('use_time',0)
            ->where('user_id', 0)
            ->where(function($query){
                $query->where('expire_time',0)
                    ->orWhere('expire_time', '>', UTC_TIME);
            })
            ->with('promotion')->first();
        if ($promotionSn) {//不存在
            $expire_time = self::getExpireTime($promotionSn);
        } else {
            $expire_time = self::getExpireTime(null, $promotion);
        }

        if ($expire_time === false || ((int)$expire_time != 1 && $expire_time < UTC_TIME)) {
            $result['code'] = 10304;
            return $result;
        }

        if($promotion->type == 1){
            $beginTime = $promotion->begin_time;
        }else{
            $beginTime = UTC_TIME;
        }
        if ($promotionSn) {
            $promotionSn->user_id = $userId;
            $promotionSn->send_time = UTC_TIME;
            $promotionSn->expire_time = (int)$expire_time == 1 ? 0 : $expire_time;
            $promotionSn->save();
        } else {
            $promotionSn = new PromotionSn();
            $promotionSn->user_id = $userId;
            $promotionSn->promotion_id = $promotionId;
            $promotionSn->send_time = UTC_TIME;
            $promotionSn->create_time = UTC_TIME;
            $promotionSn->expire_time = (int)$expire_time == 1 ? 0 : $expire_time;
            $promotionSn->money = $promotion->money;
            $promotionSn->activity_id = $activityId;
            $promotionSn->begin_time = $beginTime;
            do {
                try {
                    $promotionSn->sn = $zi.String::randString(9,'1');
                    $promotionSn->save();
                    $bln = true;
                } catch (Exception $e) {
                    $bln = false;
                }
            } while(!$bln);
        }
        return $result;
    }
    /**
     * 随机字符串
     * @param int $length 位数
     * @return string
     */
    static function generateRand($length = 8 ) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $password = "";

        $count = strlen($chars) - 1;

        for ( $i = 0; $i < $length; $i++ ) {
            $password .= $chars[mt_rand(0, $count)];
        }

        return $password;
    }
    /**
     * 记算优惠券过期时间
     * @param  [type] $promotionSn [description]
     * @param  [type] $promotion   [description]
     * @return [type]              [description]
     */
    public static function getExpireTime($promotionSn, $promotion = null) {
        if (!$promotion) {
            $promotion = Promotion::where('id', $promotionSn->promotion_id)->first();
        }

        if (!$promotion) {
            return false;
        }
        $expire_time = 0;//默认为永不过期

        $promotion = $promotion->toArray();

        if ($promotion['endTime'] > 0) {//如果有使用结束时间
            $expire_time = $promotion['endTime'];
        }
        if ($promotion['expireDay'] > 0) {//过期天数
            $send_time = $promotionSn['send_time'];

            if ($send_time < 1) {//如果还未发放时,以当前时间天
                $send_time = UTC_DAY;
            }

            $time = $promotion['expireDay'] * 86400 + $send_time;
            // if ($expire_time == 0 || $time < $expire_time) {
            $expire_time = $time;
            // }
        }

        return $expire_time;
    }

    /**
     * 会员注册的优惠
     * @param $uid
     */
    public static function issueUserRegPromotion($uid)
    {
        $list = \YiZan\Models\Activity::where('status', 1)
            ->where("type", 2)
            ->where('start_time', '<', UTC_TIME)
            ->where('end_time', '>', UTC_TIME)
            ->with('promotion.promotion')
            ->first();


        if(!empty($list)){
            $list = $list->toArray();
            $i = 0;//发送优惠券的数量
            $money = 0;//优惠券的总金额
            foreach($list['promotion'] as $activity)
            {
                $i++;
                $money += $activity['promotion']['money'];
                if($activity['num'] > 0){
                    //发放优惠券
                    $result = PromotionService::receivePromotion($uid, $activity['promotionId'], $activity['activityId'],true, true);
                    ActivityPromotion::where('id',$activity['id'])->decrement('num');
                }else if($activity['num'] <= 0){
                    $result = PromotionService::receivePromotion($uid, $activity['promotionId'], $activity['activityId'],true, true);
                }
            }
            if($result['code'] == 0){
                $message = ['num'=>$i,'money'=>$money];
                //消息推送
                PushMessageService::notice($uid, '', 'order.message', $message, ['app'],'buyer',5, 0);
            }
        }
    }

    /**
     * 分享送优惠券
     */
    public function shareSend($userId,$orderId,$activityId,$promotionId){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $is_have_count = ActivityLogs::where('order_id',$orderId)->where('user_id',$userId)->count();
        if($is_have_count < 1){
            DB::beginTransaction();
            try {
                $activityLogs = new ActivityLogs();
                $activityLogs->user_id = $userId;
                $activityLogs->order_id = $orderId;
                $activityLogs->activity_id = $activityId;
                $activityLogs->create_time = UTC_TIME;
                $activityLogs->save();

                PromotionService::receivePromotion($userId, $promotionId, $activityId,true, true,'F');
                ActivityPromotion::where('id',$activityId)->decrement('num');

                DB::commit();
            }catch (Exception $e){
                DB::rollback();
            }
        }

        return $result;
    }


    /**
     * [returnPromotion 退还会员优惠券]
     * @param  [object] $order 	[订单信息]
     * @return [bool]           [是否成功]
     */
    public static function returnPromotion($order){
        $order_promotion = OrderPromotion::where('order_id', $order->id)
            ->where('user_id', $order->user_id)
            ->where('seller_id', $order->seller_id)
            ->first();

        if(empty($order_promotion)){
            return true;
        }

        DB::beginTransaction();
        try{
            //更新优惠券为可用
            PromotionSn::where('id', $order_promotion->promotion_sn_id)
                ->update(['status'=>PromotionSn::STATUS_DISABLED]);

            //删除order_promotion表记录
            OrderPromotion::where('id', $order_promotion->id)
                ->delete();

            DB::commit();
        }catch(Exception $e){
            DB::rallback();
            return false;
        }
        return true;
    }

    /**
     * 根据Id获取优惠券
     */
    public static function getPromotionById($userId,$id) {
        //where('seller_id', $sellerId)->
        $promotion = PromotionSn::where('id',$id)->where("user_id",$userId)->first();
        if($promotion){
            return  $promotion->toArray();
        }
        return null;
    }

    /**
     * 创建优惠券
     */
    public function createPromotion($userId, $promotionId, $endTime, $expireDay, $perpetual,$goodsCate){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.get_promotion_success')
        );

        $promotionSn = new PromotionSn;
        $promotionSn->user_id          = $userId;
        $promotionSn->seller_id        = 0;
        $promotionSn->sn               = String::randString(8, 1);
        $promotionSn->promotion_id     = $promotionId;
        $promotionSn->send_time      = UTC_TIME;
        $promotionSn->create_time      = UTC_TIME;
        $promotionSn->expire_time = $endTime;
        if ($expireDay > 0) {//如果有有效天数
            $promotionSn->expire_time = UTC_TIME + $expireDay * 86400;
        }
        $promotionSn->type      = $perpetual;
        $promotionSn->goods_cate      = $goodsCate;

        $promotionSn->save();

        return $result;
    }

    /**
     * [regGetPromotion 领取优惠券]
     * @param  [type]  $userId       [会员id]
     * @param  [type]  $promotionId  [优惠券id]
     * @return [type]                [description]
     */
    public static function regGetPromotion($userId, $promotionId)
    {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ''
        );

        $promotion = Promotion::where("id", $promotionId)->where('status',1)->first();

        if($promotion == false){
            $result["code"] = 10302; // 优惠券不存在
            return $result;
        }
        if(($promotion->begin_time >= UTC_TIME ||
                $promotion->end_time <= UTC_TIME) && $promotion->perpetual == 2){
            $result["code"] = 10304; // 优惠券已过期
            return $result;
        }

        $promotionSn = new PromotionSn();
        $promotionSn->seller_id = 0;
        $promotionSn->user_id = $userId;
        $promotionSn->promotion_id = $promotionId;
        $promotionSn->send_time = UTC_TIME;
        $promotionSn->create_time = UTC_TIME;
        $promotionSn->expire_time = self::getExpireTime(null, $promotion);
        if ($promotionSn->expire_time === false) {
            $result['code'] = 10304;
            return $result;
        }

        do {
            try {
                $promotionSn->sn = String::randString(8, 2);
                $promotionSn->save();
                $bln = true;
            } catch (Exception $e) {
                $bln = false;
            }
        } while(!$bln);

        return $result;
    }


    /**
     * 获取会员第一个可用的优惠券
     * @param int $userId 会员编号
     */
    public static function couponMsg($userId){

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        if(!$userId){
            return $result;
        }
        $user = User::find($userId);
        if(!$user){
            return $result;
        }
        $res = PromotionSn::where('promotion_sn.user_id', $userId)
            ->select('promotion_sn.expire_time','promotion_sn.begin_time','promotion_sn.is_send_msg','promotion_sn.id',"promotion_sn.money")
            ->where('promotion_sn.is_del',0)
            ->where('promotion_sn.use_time',0)
            ->where(function($query){
                $query->where('promotion_sn.expire_time',0)
                    ->orWhere('promotion_sn.expire_time','>',UTC_TIME);
            })->leftJoin('promotion','promotion.id','=','promotion_sn.promotion_id');

        $firstall = clone $res;
        $firstall->orderBy('promotion_sn.expire_time','asc');
        $datas = $firstall->get();
        $codeVal = SystemConfigService::getConfigByCode("coupon_return_alert_time"); //天

        $msg['num'] = 0;
        $bln = false;
        $blns = false;
        foreach($datas as $ks => $first){
            $end = $first->expire_time - UTC_TIME;
            $endTime = sprintf("%.1f",($end) /   24 / 3600);
            switch ($first->is_send_msg) {
                case 0:
                    if($endTime <= 1 && $endTime > 0){
                        $bln = true;
                        $data = ['is_send_msg'=>2];
                    }else{
                        if($codeVal >= $endTime && $codeVal >= 1){
                            $bln = true;
                            $data = ['is_send_msg'=>1];
                        }
                    }
                    break;
                case 1 :
                    if( ($endTime > 0 && $codeVal >= $endTime) || UTC_DAY ==  Time::toDayTime($first->expire_time)  ||  UTC_DAY >  $first->check_day){
                        $bln = true;
                        $data = ['is_send_msg'=>2];
                    }else{
                        $bln = false;
                    }
                    break;
                default:
                    $bln = false;
            }
            if($bln){
                $blns = true;
                $msg['num'] ++;
                $msg['money'] += $first->money;

                $data['check_day'] = UTC_DAY;
                PromotionSn::where("id",$first->id)->update($data);
            }
        }
        if($blns){
            PushMessageService::notice($user->id,$user->mobile, 'promotion.sendmessage', $msg, ['app'],'buyer',5, 0);
        }
        $all = clone $res;
        $all = $all->where('promotion_sn.is_check',0);
        $numall =  $all->count();
        if($numall){
            $result['data'] = [
                'num' => $numall,
            ];
            $result['data']['content'] = "恭喜您获得".$numall."张新的优惠券";
            $ids = [];
            $all = $all->get()->toArray();
            foreach($all as $k => $vs){
                $ids[$k] = $vs['id'];
            }
            if($ids){
                PromotionSn::whereIn("id",$ids)->update(['is_check' =>1]);
            }
        }
        return $result;
    }
}