<?php 
namespace YiZan\Services\System;

use YiZan\Models\SellerStaff;
use YiZan\Services\SystemConfigService as baseSystemConfigService;
use YiZan\Models\System\Order;
use YiZan\Models\Seller;
use YiZan\Models\StaffAppoint;
use YiZan\Models\StaffServiceTime;
use YiZan\Models\StaffLeave;
use YiZan\Models\Goods;
use YiZan\Models\PromotionSn;
use YiZan\Models\UserPayLog;
use YiZan\Models\OrderPromotion;

use YiZan\Services\SellerMoneyLogService as baseSellerMoneyLogService;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\SellerExtend;
use YiZan\Models\User;
use YiZan\Models\InvitationBackLog;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Exception, DB, Lang, Validator, App,Rand;

class OrderService extends \YiZan\Services\OrderService 
{
    /**
     * 订单不存在
     */
    const ORDER_NOT_EXIST = 50101;
    /**
     * 订单状态错误
     */
    const ORDER_STATUS_ERROR = 50103;
    /**
     * 该订单不允许删除
     */
    const ORDER_NOT_DELETE = 50104;

/**
 * 订单列表
 * @param  string $sn 订单号 
 * @param  string $userMobile 会员手机号
 * @param  string $sellerMobile 服务人员手机号
 * @param  int $beginTime 开始时间
 * @param  int $endTime 结束时间
 * @param  int $status 订单状态
 * @param  int $payStatus 支付状态
 * @param  int $sellerName 商家名称
 * @param  int $page 页码
 * @param  int $pageSize 每页数
 * @param  int $isAll 0:周边店 1：全国店
 * @return array          订单列表
 */
public static function getSystemList($orderType, $sn, $mobile, $beginTime, $endTime, $payStatus, $status, $sellerName, $page, $pageSize,$isSeller = false,$isIntegralGoods = 0, $isAll = 0, $provinceId,$cityId,$areaId,$payTypeStatus=0,$sendFee = 0)
{
    self::endOrder();
    $list = Order::orderBy('id', 'desc')->where('status','!=', ORDER_STATUS_ADMIN_DELETE)->where('is_all', $isAll);

    if(!$isIntegralGoods){
        if($isSeller){
            $list->where('seller_id', ONESELF_SELLER_ID);
        }else{
            $list->where('seller_id','!=', ONESELF_SELLER_ID);
        }
    }
    if ($orderType > 0) {
        $list->where('order_type', $orderType);
    }
    if($sn != '') {
        $list->where('sn', $sn);
    }
    if($mobile != '') {
        $list->where('mobile', $mobile);
    }
    if($beginTime > 0)
    {
        $list->where('create_time', '>=', $beginTime);
    }

    if($endTime > 0)
    {
        $list->where('create_time', '<=', $endTime);
    }

    if($payStatus  > '-1')
    {
        $list->where('pay_status',  $payStatus);
    }

    if($provinceId > 0)
    {
        $list->where('province_id', '=', $provinceId);
    }

    if($cityId > 0)
    {
        $list->where('city_id', '=', $cityId);
    }

    if($areaId > 0)
    {
        $list->where('area_id', '=', $areaId);
    }

    if($sendFee > 0)
    {
        $list->where('send_fee', '>', 0);
    }

    $list->where('is_integral_goods',  $isIntegralGoods);//积分订单

    if ($sellerName != '') {
        $sellerName = empty($sellerName) ? '' : String::strToUnicode($sellerName,'+');
        $list->whereIn('seller_id', function($query) use ($sellerName){
            $query->select('id')
                ->from('seller')
                ->whereRaw("MATCH(name_match) AGAINST('{$sellerName}' IN BOOLEAN MODE)");
        });
    }

    switch ($payTypeStatus) {
        case 1:
            $list->where('pay_status', 1);   //在线支付
            $list->where('pay_type','!=', 'cashOnDelivery');   //货到付款
            break;

        case 2:
            $list->where('pay_type', 'cashOnDelivery');   //货到付款
            break;

        case 3:
            $list->where('pay_status', 0)->where('pay_type', '<>', 'cashOnDelivery');   //未支付
            break;

        default:
            # code...
            break;
    }

    switch ($status) {
        case '1': //待发货（周边店使用）  102 110
            $list->whereIn('status',
                [
                    ORDER_STATUS_BEGIN_USER,
                    ORDER_STATUS_PAY_SUCCESS,
                    ORDER_STATUS_PAY_DELIVERY,
                    ORDER_STATUS_AFFIRM_SELLER
                ]);
            break;
        case '2'://待完成 106 107
            $list->whereIn('status',
                [
                    ORDER_STATUS_FINISH_STAFF
                ]) ;
            break;
        case '3':// 已完成
            $list->whereIn('status',
                [
                    ORDER_STATUS_FINISH_SYSTEM,
                    ORDER_STATUS_FINISH_USER,
                    ORDER_STATUS_USER_DELETE,
                    ORDER_STATUS_SELLER_DELETE
                ])->where(function($query){
                    $query->where('buyer_finish_time', '>', 0)
                        ->orWhereBetween('auto_finish_time', [1,UTC_TIME]);
                });
            break;
        case '4'://拒绝 301 302 303 400 401 402 403 404 311 312
            $list->whereIn('status',
                [
                    ORDER_STATUS_CANCEL_USER,
                    ORDER_STATUS_CANCEL_AUTO,
                    ORDER_STATUS_CANCEL_SELLER,
                    ORDER_STATUS_CANCEL_ADMIN
                ]) ;
            break;
        case '5'://退款
            $list->whereIn('status',
                [
                    ORDER_STATUS_REFUND_AUDITING,
                    ORDER_STATUS_REFUND_HANDLE,
                    ORDER_STATUS_REFUND_FAIL,
                    ORDER_STATUS_REFUND_SUCCESS
                ]) ;
            break;

        case '6'://待付款
            $list->whereIn('status',
                [
                    ORDER_STATUS_BEGIN_USER
                ]) ;
            break;

        case '7'://待发货（全国店使用）
            $list->whereIn('status',
                [
                    ORDER_STATUS_PAY_SUCCESS
                ]) ;
            break;
        case '8'://已发货（全国店使用）
            $list->whereIn('status',
                [
                    ORDER_STATUS_AFFIRM_SELLER
                ]) ;
            break;
        case '9'://待指派（周边店使用）
            $list->whereIn('status',
                [
                    ORDER_STATUS_SYSTEM_SEND,
                    ORDER_STATUS_CALL_SYSTEM_SEND
                ]) ;
            break;
        case '10'://配送中的订单（周边店使用）
            $list->whereIn('status',
                [
                    ORDER_STATUS_START_SERVICE
                ]);
            break;
        case '11'://配送完成（周边店使用）
            $list->whereIn('status',
                [
                    ORDER_STATUS_FINISH_STAFF,
                    ORDER_STATUS_FINISH_SYSTEM,
                    ORDER_STATUS_FINISH_USER
                ]) ;
            break;
        case '12'://异常订单
            $list->whereIn('status',
                [
                    ORDER_STATUS_CANCEL_USER,
                    ORDER_STATUS_CANCEL_SELLER,
                    ORDER_STATUS_CANCEL_ADMIN
                ]) ;
            break;
        case '13'://预指派订单
            $list->whereIn('status',
                [
                    ORDER_STATUS_GET_SYSTEM_SEND
                ]) ;
            break;
        default:
            break;
    }
    $totalCount = $list->count();
    $list = $list->skip(($page - 1) * $pageSize)
    ->take($pageSize)
    ->with('user', 'seller','seller.province','seller.city','seller.area', 'staff', 'firstLevel','secondLevel','thirdLevel','orderGoods')
    ->get()
    ->toArray();
    return ["list"=>$list, "totalCount"=>$totalCount];
    
}

    /**
     * [total 订单统计]
     * @param  [type] $orderType [订单类型]
     * @param  [type] $status    [订单状态]
     * @param  [type] $isAll     [是否是全国订单]
     * @return [type]            [description]
     */
    public static function total($orderType, $status, $isAll) {
        $list = Order::where('status','!=', ORDER_STATUS_ADMIN_DELETE)->where('is_all', $isAll);

        if ($orderType > 0) {
            $list->where('order_type', $orderType);
        }

        switch ($status) {
            case '1': //待发货（周边店使用）  102 110
                $list->whereIn('status',
                    [
                        ORDER_STATUS_BEGIN_USER,
                        ORDER_STATUS_PAY_SUCCESS,
                        ORDER_STATUS_PAY_DELIVERY,
                        ORDER_STATUS_AFFIRM_SELLER
                    ]);
                break;
            case '2'://待完成 106 107
                $list->whereIn('status',
                    [
                        ORDER_STATUS_FINISH_STAFF
                    ]) ;
                break;
            case '3':// 已完成
                $list->whereIn('status',
                    [
                        ORDER_STATUS_FINISH_SYSTEM,
                        ORDER_STATUS_FINISH_USER,
                        ORDER_STATUS_USER_DELETE,
                        ORDER_STATUS_SELLER_DELETE
                    ])->where(function($query){
                        $query->where('buyer_finish_time', '>', 0)
                            ->orWhereBetween('auto_finish_time', [1,UTC_TIME]);
                    });
                break;
            case '4'://拒绝 301 302 303 400 401 402 403 404 311 312
                $list->whereIn('status',
                    [
                        ORDER_STATUS_CANCEL_USER,
                        ORDER_STATUS_CANCEL_AUTO,
                        ORDER_STATUS_CANCEL_SELLER,
                        ORDER_STATUS_CANCEL_ADMIN
                    ]) ;
                break;
            case '5'://退款
                $list->whereIn('status',
                    [
                        ORDER_STATUS_REFUND_AUDITING,
                        ORDER_STATUS_REFUND_HANDLE,
                        ORDER_STATUS_REFUND_FAIL,
                        ORDER_STATUS_REFUND_SUCCESS
                    ]) ;
                break;

            case '6'://待付款
                $list->whereIn('status',
                    [
                        ORDER_STATUS_BEGIN_USER
                    ]) ;
                break;

            case '7'://待发货（全国店使用）
                $list->whereIn('status',
                    [
                        ORDER_STATUS_PAY_SUCCESS
                    ]) ;
                break;
            case '8'://已发货（全国店使用）
                $list->whereIn('status',
                    [
                        ORDER_STATUS_AFFIRM_SELLER
                    ]) ;
                break;

            default:
                break;
        }

        $list = $list->count('id');
        return (int)$list;
    }

	/**
     * 获取订单
     * @param  int $id 订单id
     * @return array   订单
     */
	public static function getSystemOrderById($id) 
    {
        self::endOrder();
        $data = Order::where('id', $id)->with('user', 'seller', 'staff', 'orderGoods','payment')->first();
        return $data;
	}
    
    /**
     * 更新订单
     * @param  int $id 订单id
     * @param  int $status 状态
     * @param  string $content 处理结果
     * @return array   更新结果
     */
	public static function updateSystemOrder($id, $status, $refuseContent) 
    {
        //订单完结
        if($status == ORDER_STATUS_FINISH_USER)
        {
            $userId = self::getSystemOrderById($id)->user_id;
            return \YiZan\Services\OrderService::confirmOrder($userId, $id);
        }

		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.update_info')
		];

        if(($status != ORDER_STATUS_AFFIRM_SELLER &&
                $status != ORDER_STATUS_CANCEL_ADMIN && 
                    $status != ORDER_STATUS_START_SERVICE && 
                        $status != ORDER_STATUS_FINISH_STAFF &&
                            $status != ORDER_STATUS_CANCEL_USER))
        {
            $result['code'] = self::ORDER_STATUS_ERROR;
	    	return $result;
        }
        
        $order = Order::where('id', $id)->first();
        //没有订单
		if ($order == false) 
        {
			$result['code'] = self::ORDER_NOT_EXIST;
            
	    	return $result;
		}

        if(( $order->isCanAccept == false &&
                $order->isCanCancel == false &&
                    $order->isCanStartService == false &&
                        $order->isCanFinish == false) )
        {
            $result['code'] = self::ORDER_STATUS_ERROR;
            
	    	return $result;
        }
        
        if($status == ORDER_STATUS_CANCEL_ADMIN)
        {
            $order->status = ORDER_STATUS_CANCEL_ADMIN;
            
            $order->cancel_time = UTC_TIME;

            $order->cancel_remark = $refuseContent;
        }
        else if($status == ORDER_STATUS_AFFIRM_SELLER)
        {
            $order->status = ORDER_STATUS_AFFIRM_SELLER;
            
            $order->seller_confirm_time = UTC_TIME;

            //全国店，发货时候写入自动完成时间参数
            if($order->is_all == 1)
            {
                $autoFinishDay = baseSystemConfigService::getConfigByCode('system_buyer_order_confirm_all');
                $autoFinishTime = $autoFinishDay * 24 * 3600 + UTC_TIME;
                $order->auto_finish_time = $autoFinishTime;
            }else{
                //cz周边店
                $send_type = Seller::where('id',$order->seller_id)->pluck('send_type');
                if(($send_type == 1 && !$order->IsCashOnDelivery) && $order->order_type == 1 ){
					$order->status = ORDER_STATUS_SYSTEM_SEND;
					//看看自动分配是否有人
					if($order->order_type == 2 || ($order->send_way == 1 && $order->order_type == 1))
					{
						$newstaffId = OrderService::autopei($order->id);
						if($newstaffId > 0){
							$order->status = ORDER_STATUS_GET_SYSTEM_SEND;
							$order->seller_staff_id = $newstaffId;
						}else{
                            $result['code'] = 60808;
                            return $result;
                        }
					}
				}
            }

        }
        else if($status == ORDER_STATUS_START_SERVICE)
        {
            $order->status = ORDER_STATUS_START_SERVICE;
        }
        else if($status == ORDER_STATUS_CANCEL_USER)
        {
            $order->status = ORDER_STATUS_CANCEL_USER;
        }
        else if($status == ORDER_STATUS_FINISH_STAFF)
        {
            $order->status = ORDER_STATUS_FINISH_STAFF;

            $order->staff_finish_time = UTC_TIME; 

            //周边店，服务人员确认完成写入自动完成时间参数
            if($order->is_all == 0)
            {
                $autoFinishDay = baseSystemConfigService::getConfigByCode('system_buyer_order_confirm');
                $autoFinishTime = $autoFinishDay * 24 * 3600 + UTC_TIME;
                $order->auto_finish_time = $autoFinishTime;
            }

            //（完成订单时处理）如果是到店，则更新消费码
            if( in_array($order->send_way, [2,3]) )
            {
                $data['auth_code_use'] = -1;
            }
        }

        //（接单时处理）判断是否是到店
        if($status == ORDER_STATUS_AFFIRM_SELLER && in_array($order->send_way, [2,3])) {
            $order->auth_code = Helper::getCode(1,12);
            $order->auth_code_use = 1; //-1：已使用  1：未使用
        }
        $bln = false;

//        DB::beginTransaction();
//        try {
            $order->save();
            $data = self::getSystemOrderById($id)->toArray();
            if($status == ORDER_STATUS_CANCEL_USER){
                self::userR($order);
            }else {
                if ($status == ORDER_STATUS_CANCEL_ADMIN) {
                    self::cancelOrderStock($id);
                    $isRefundBalance = \YiZan\Models\SystemConfig::where('code', 'is_refund_balance')->pluck('val');

                //取消返还优惠券
                if ((int)$data['promotionSnId'] > 0) {
                    PromotionSn::where('id', $data['promotionSnId'])->update(['use_time' => 0]);
                }

                //退还积分
                if ((int)$data['integral'] > 0) {
                    \YiZan\Services\UserIntegralService::createIntegralLog($data['userId'], 1, 7, $id, 0, $data['integral']);
                }
                //是否是返现订单
                if ($data['isInvitation']) {
                    InvitationBackLog::where('order_id',$data['id'])->where('user_id',$data['userId'])->update([
                        'is_refund' => 1,
                        'update_time' => UTC_TIME
                    ]);

                }
            }


            if ($status == ORDER_STATUS_CANCEL_ADMIN && $order->isCashOnDelivery()) {
                //如果是货到付款则退还商家支付的抽成金额
                $sellerMoneyLog = SellerMoneyLog::where('related_id', $order->id)
                    ->where('type', SellerMoneyLog::TYPE_DELIVERY_MONEY)
                    ->first();
                //增加商家金额
                if ($sellerMoneyLog) {
                    //增加商家金额
                    SellerExtend::where('seller_id', $data['sellerId'])
                        ->increment('money', abs($sellerMoneyLog->money));
                    //写入增加金额日志
                    baseSellerMoneyLogService::createLog(
                        $data['sellerId'],
                        SellerMoneyLog::TYPE_DELIVERY_MONEY,
                        $id,
                        $data['drawnFee'],
                        '现金支付订单' . $order->sn . '取消，佣金返还',
                        1
                    );
                }
            }


            //如果未支付订单 包含余额支付金额 则退款
            if($status == ORDER_STATUS_CANCEL_ADMIN  && $order->pay_status == ORDER_PAY_STATUS_NO && $order->pay_money > 0.0001){
                //返还支付金额给会员余额
                $user = User::find($order->user_id);
                $user->balance = $user->balance + abs($order->pay_money);
                $user->save();
                //创建退款日志
                $userPayLog = new UserPayLog;
                $userPayLog->payment_type   = 'balancePay';
                $userPayLog->pay_type       = 3;//退款
                $userPayLog->user_id        = $order->user_id;
                $userPayLog->order_id       = $order->id;
                $userPayLog->activity_id    = $order->activity_id > 0 ? $order->activity_id : 0;
                $userPayLog->seller_id      = $order->seller_id;
                $userPayLog->money          = $order->pay_money;
                $userPayLog->balance        = $user->balance;
                $userPayLog->content        = '平台取消订单退款';
                $userPayLog->create_time    = UTC_TIME;
                $userPayLog->create_day     = UTC_DAY;
                $userPayLog->status         = 1;
                $userPayLog->sn = Helper::getSn();
                $userPayLog->save();

            }

            //当已支付且支付金额大于0且状态为取消订单时退款
            if ($status == ORDER_STATUS_CANCEL_ADMIN &&
                $order->pay_status == ORDER_PAY_STATUS_YES &&
                $order->pay_fee >= 0.0001 &&
                $order->isCashOnDelivery === false
            ) {
                $pay_type = $order->getPayType();

                //返还支付金额给会员余额
                $user = User::find($order->user_id);
                if($user > 0){
                    if ($isRefundBalance == 1) {
                        $user->balance = $user->balance + abs($order->pay_fee);
                        $user->save();
                    } elseif($isRefundBalance == 0 && $order->pay_money > 0.0001) {
                        $user->balance = $user->balance + abs($order->pay_money);
                        $user->save();
                    }
                }

                $userPayLogs = UserPayLog::where('order_id', $order->id)
                    ->where('pay_type', 1)
                    ->where('status', 1)
                    ->get()
                    ->toArray();

                if (count($userPayLogs) > 0) {
                    $userRefundLog = [];
                    $userPayLog = [];

                    foreach ($userPayLogs as $k => $v) {
                        if ($v['paymentType'] == 'balancePay') {
                            $userRefundLog[] = [
                                "sn" => $order->sn,
                                "user_id" => $order->user_id,
                                "order_id" => $order->id,
                                "trade_no" => $v['tradeNo'],
                                "seller_id" => $order->seller_id,
                                "payment_type" => $v['paymentType'],
                                "money" => $v['money'],
                                "content" => "平台取消",
                                "create_time" => UTC_TIME,
                                "create_day" => UTC_DAY,
                                "status" => 1
                            ];
                        } else {
                            $userRefundLog[] = [
                                "sn" => $order->sn,
                                "user_id" => $order->user_id,
                                "order_id" => $order->id,
                                "trade_no" => $v['tradeNo'],
                                "seller_id" => $order->seller_id,
                                "payment_type" => $v['paymentType'],
                                "money" => $v['money'],
                                "content" => "平台取消",
                                "create_time" => UTC_TIME,
                                "create_day" => UTC_DAY,
                                "status" => 0
                            ];
                        }

                        if ($isRefundBalance == 1) {
                            $userRefundLog[$k]['status'] = 1;
                            $userRefundLog[$k]['content'] = '平台取消,退回用户余额';
                            $userPayLog[$k] = [
                                'payment_type'  => $v['paymentType'],
                                'pay_type'       => 3,//退款
                                'user_id'        => $v['userId'],
                                'order_id'       => $order->id,
                                'activity_id'    => $order->activity_id > 0 ? $order->activity_id : 0,
                                'seller_id'      => $order->seller_id,
                                'money'           => $v['money'],
                                'balance'         => $user->balance,
                                'content'         => '平台取消订单,退回用户余额',
                                'create_time'    => UTC_TIME,
                                'create_day'     => UTC_DAY,
                                'status'          => 1,
                                'sn'              => Helper::getSn()
                            ];
                        } elseif ($v['paymentType'] == 'balancePay') {
                            $userPayLog = [
                                'payment_type'  => $v['paymentType'],
                                'pay_type'       => 3,//退款
                                'user_id'        => $v['userId'],
                                'order_id'       => $order->id,
                                'activity_id'    => $order->activity_id > 0 ? $order->activity_id : 0,
                                'seller_id'      => $order->seller_id,
                                'money'           => $v['money'],
                                'balance'         => $user->balance,
                                'content'         => '平台取消订单退款',
                                'create_time'    => UTC_TIME,
                                'create_day'     => UTC_DAY,
                                'status'          => 1,
                                'sn'              => Helper::getSn()
                            ];
                        }



                    }

                    if (!empty($userPayLog)) {
                        UserPayLog::insert($userPayLog);
                    }

                    DB::table('refund')->insert($userRefundLog);

                    baseSellerMoneyLogService::createLog(
                        $order->seller_id,
                        SellerMoneyLog::TYPE_ORDER_REFUND,
                        $id,
                        $order->pay_fee,
                        '订单取消退款：' . $order->sn
                    );
                }


                }
                //当已支付且商家金额大于0且状态为取消订单时扣除商家金额
                if ($status == ORDER_STATUS_CANCEL_ADMIN &&
                    $order->pay_status == ORDER_PAY_STATUS_YES &&
                    $order->seller_fee >= 0.0001 &&
                    $order->isCashOnDelivery === false
                ) {
                    if ((int)$order->buyer_finish_time > 0 || ((int)$order->auto_finish_time > 0 && (int)$order->auto_finish_time <= UTC_TIME)) {
                        \YiZan\Services\SellerService::decrementExtend($order->seller_id, 'total_money', $order->seller_fee);
                        \YiZan\Services\SellerService::decrementExtend($order->seller_id, 'money', $order->seller_fee);
                    } else {
                        \YiZan\Services\SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
                    }
                }
            }
//            DB::commit();
//            $bln = true;
//        } catch(Exception $e) {
//            $result['code'] = 99999;
//            DB::rollback();
//            $bln = false;
//        }
        if($bln){
            //当状态为接受订单或者拒绝订单的时候,推送消息
            if ($status == ORDER_STATUS_AFFIRM_SELLER ||
                $status == ORDER_STATUS_CANCEL_ADMIN
            ) {
                $noticeTpe = $status == ORDER_STATUS_AFFIRM_SELLER ? 'order.accept' : 'order.refund';
                PushMessageService::notice($data['userId'], $data['user']['mobile'], $noticeTpe, $data, ['sms', 'app'], 'buyer', 3, $id);

                //cz 如果订单在staff没有sellerid给系统配送人员发短信
                if($data['staff']['sellerId'] == 0 && $noticeTpe == 'order.refund'){
                    $url = u('staff#Index/index',['id'=>$data['id'],'staffUserId'=>$data['staff']['userId'],'newStaffId'=>$data['staff']['id'],'isChange'=>1]);
                    PushMessageService::notice($data['staff']['userId'],'', 'order.refund', $data,['app'], 'staff',6, $url, '');
                }

            }
            if($status == ORDER_STATUS_CANCEL_USER){
                //通知服务人员
                if (count($data['staff']) > 0) {
                    PushMessageService::notice($data['staff']['userId'], $data['staff']['mobile'], 'order.cancel', $data,['sms','app'],'staff',3, $data['id']);
                }
                PushMessageService::notice($data['user']['id'], $data['user']['mobile'], 'order.usercancel', $data,['sms','app'],'buyer', 3, $data['id']);
            }
        }
	    return $result;
    }
    
	/**
     * 删除订单
     * @param int  $id 订单id
     * @return array   删除结果
     */
	public static function deleteSystemOrder($id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.delete_info')
		];

		$order = Order::where('id', $id)->first();
        //没有订单
		if ($order == false)
        {
			$result['code'] = self::ORDER_NOT_EXIST;
            
	    	return $result;
		}

		//当订单状态不为卖家删除,订单不能删除
		if ($order->status != ORDER_STATUS_SELLER_DELETE)
        {
			$result['code'] = self::ORDER_NOT_DELETE;
            
	    	return $result;
		}
        
		Order::where('id', $id)->update(['status' => ORDER_STATUS_ADMIN_DELETE]);
        
		return $result;
	}

    /**
     * 更改订单服务日期
     * @param  int $id 订单编号
     * @param int $beginTime 开始时间
     * @param int $endTime 结束时间
     */
    public static function updateDate($id, $beginTime, $endTime) {
        $result = [
                'code'	=> 0,
                'data'	=> null,
                'msg'	=> Lang::get('api_system.success.update_info')
            ];
        $check = Order::where('id', $id)->with('goods')->first();
        if (!$check) {
            $result['code'] = 80001; //订单是否存在
            return $result;
        }
        if ($check->goods->price_type == 2 && ($endTime - $beginTime) != $check->goods_duration) {
            $result['code'] = 80005; //按时收费 ,时长必须与下单时的时长相同
            return $result;
        }
        if ((int)$beginTime < 1 || (int)$endTime < 1) {
            $result['code'] = 80002; //开始时间和结束时间不能为空
            return $result;
        }
        if ($endTime <= $beginTime) {
            $result['code'] = 80003; //结束时间必须大于开始时间
            return $result;
        }

        if (Time::toDate($beginTime,'Ymd') != Time::toDate($endTime, 'Ymd')) {
            $result['code'] = 80008; //开始时间和结束时间必须在同一天
            return $result;
        }

        $check_stime = StaffServiceTime::where('begin_time', '<=', Time::toDate($beginTime,'H:i'))
                                         ->where('end_time', '>=', Time::toDate($endTime, 'H:i'))
                                         ->where('staff_id', $check->staff_id)
                                         ->where('week', Time::toDate($beginTime, 'w'))
                                         ->count();
        if ($check_stime < 1) {
            $result['code'] = 80007; //时间段不在服务人员服务时间段内
            return $result;
        }

        $check_appoint = StaffAppoint::where('appoint_time', '>=' ,$beginTime)
                                        ->where('appoint_time', '<=', $endTime)
                                        ->where(function($query) use ($id) {
                                                $query->whereNotIn('order_id', [0,$id])
                                                      ->orWhere('is_leave', 1);
                                        })->where('staff_id', $check->seller_staff_id)
                                        ->first();
        if ($check_appoint) {
            $result['code'] = 80006; //此段时间是否有预约或者请假
            return $result;
        }
        DB::beginTransaction();
        try {
            //更改订单时间
            Order::where('id', $id)->update([
                'appoint_time' => $beginTime,
                'service_end_time' => $endTime,
                'appoint_hour' => Time::toDate($beginTime,'H'),
                'appoint_day' => Time::toDayTime($beginTime),
                'service_end_hour' => Time::toDate($endTime,'H'),
                'service_end_day' => Time::toDayTime($endTime),
                'goods_duration' => ($endTime - $beginTime)
            ]);
            //更新预约时间表
            StaffAppoint::where('staff_id', $check->seller_staff_id)->where('order_id',$id)->update(['order_id' => 0,'status' => 0]);
            $btime = $beginTime % 1800 == 0 ? $beginTime : ceil($beginTime/1800) * 1800;
            $etime = $endTime % 1800 == 0 ? $endTime : ceil($endTime/1800) * 1800;
            $appoint_data = [];
            for ($i = $btime; $i < $etime; $i += 1800) {
                $appoint = StaffAppoint::where('appoint_time',$i)
                    ->where('staff_id', $check->seller_staff_id)
                    ->first();
                if ($appoint) {
                    StaffAppoint::where('staff_id',$check->seller_staff_id)
                        ->where('appoint_time', $i)
                        ->update([
                            'appoint_week' => Time::toDate($i,'w'),
                            'order_id' => $id,
                            'status' => 1
                        ]);
                } else {
                    $appoint_data[] = [
                        'staff_id' => $check->seller_staff_id,
                        'seller_id' => $check->seller_id,
                        'appoint_time' => $i,
                        'appoint_day' => Time::toDayTime($i),
                        'appoint_week' => Time::toDate($i,'w'),
                        'order_id' => $id,
                        'status' => 1
                    ];

                }
            }
            if (count($appoint_data) > 0) {
                StaffAppoint::insert($appoint_data);
            }
            DB::commit();
            return $result;
        } catch(Exception $e) {
            $result['code'] = 80004;
            DB::rollback();
            return $result;
        }

    }

    /**
     * 指定指派人员
     * @param array $orderIds 订单编号数组
     * @param int $staffId 员工编号
     */
    public static function updateStaff($orderIds, $staffId) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_system.success.update_info')
        ];
        //允许更改的订单状态
        $allow_status = [ORDER_STATUS_WAIT_PAY,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_AFFIRM_SERVICE,ORDER_STATUS_STAFF_ACCEPT];

        $order_count = Order::whereIn('id', $orderIds)->whereIn('status', $allow_status)->count();
        if ($order_count != count($orderIds) || $order_count < 1) {
            $result['code'] = 80101; //订单编号不合法
            return $result;
        }
        $check_staff = SellerStaff::where('id', $staffId)->where('status', 1)->first();
        if (!$check_staff) {
            $result['code'] = 80102; // 服务人员不存在
            return  $result;
        }

        $order_list = Order::whereIn('id', $orderIds)->whereIn('status', $allow_status)->select('id', 'appoint_time','service_end_time')->get()->toArray();
        $appoint = StaffAppoint::where('staff_id', $staffId)->where('order_id',0)->where('is_leave',0);
        $service = StaffServiceTime::where('staff_id', $staffId);
        foreach ($order_list as $key=>$val) {
            $btime = $val['appointTime'];
            $etime = $val['serviceEndTime'];
            $week = Time::toDate($val['appointTime'], 'w');
            $bhour = Time::toDate($val['appointTime'], 'H:i');
            $ehour = Time::toDate($val['serviceEndTime'], 'H:i');
            $appoint->where(function($query) use ($btime, $etime){
                $query->where('appoint_time', '>=', $btime)
                    ->where('appoint_time', '<=', $etime);
            });

            $service->where(function($query) use ($week,$bhour, $ehour){
                $query->where('appoint_time', '>=', $bhour)
                    ->where('appoint_time', '<=', $ehour)
                    ->where('week', $week);
            });
        }
        $appoint_count = $appoint->count();
        $service_count = $service->count();
        if ($appoint_count > 0 && $service_count < 1) {
            $result['code'] = 80103; //不在服务人员服务时间内
            return $result;
        }

        DB::beginTransaction();
        try {
            //更新订单表
            Order::whereIn('id', $orderIds)->update([
                'seller_staff_id' => $staffId,
                'seller_id' => $check_staff->selle_id,
                'designate_type' => 1
            ]);

            //更新预约表
            $appoint_data = [];
            foreach ($order_list as $k=>$v) {
                $btime = $val['appointTime'] % 1800 == 0 ? $val['appointTime'] : ceil($val['appointTime']/1800) * 1800;
                $etime = $val['serviceEndTime'] % 1800 == 0 ? $val['serviceEndTime'] : ceil($val['serviceEndTime']/1800) * 1800;
                for ($i = $btime; $i < $etime; $i += 1800) {
                    $appoint = StaffAppoint::where('appoint_time',$i)
                        ->where('staff_id', $staffId)
                        ->first();
                    if ($appoint) {
                        StaffAppoint::where('staff_id',$staffId)
                            ->where('appoint_time', $i)
                            ->update([
                                'appoint_week' => Time::toDate($i,'w'),
                                'order_id' => $v['id'],
                                'status' => 1
                            ]);
                    } else {
                        $appoint_data[] = [
                            'staff_id' => $staffId,
                            'seller_id' => $check_staff->seller_id,
                            'appoint_time' => $i,
                            'appoint_day' => Time::toDayTime($i),
                            'appoint_week' => Time::toDate($i,'w'),
                            'order_id' => $v['id'],
                            'status' => 1
                        ];

                    }
                }
            }
            if (count($appoint_data) > 0) {
                StaffAppoint::insert($appoint_data);
            }
            DB::commit();
            return $result;
        } catch(Exception $e) {
            $result['code'] = 80104;
            DB::rollback();
            return $result;
        }
    }

    /**
     * 指定指派人员
     * @param array $orderIds 订单编号数组
     * @param int $staffId 员工编号
     */
    public static function designate($orderId, $staffId,$serviceContent,$money) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_system.success.update_info')
        ];
        
        //允许更改的订单状态
        $allow_status = [ORDER_STATUS_BEGIN_SERVICE,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_AFFIRM_SERVICE,ORDER_PAY_STATUS_YES];
        $order_list = Order::where('id', $orderId)->whereIn('status', $allow_status)->first();
        if (!$order_list) {
            $result['code'] = 80107; // 不能指派
            return  $result;
        }
        if($order_list->order_type < 2){
            $rules = array(
                'orderId'	      => ['required'],
                'staffId'	      => ['required'],
            );
            
            $messages = array(
                'orderId.required'	=> '8010911',
                'staffId.required'	=> '8010912',
            );
            $validator = Validator::make([
                'orderId'     =>$orderId,
                'staffId'     =>$staffId,
            ], $rules, $messages);
        }else{
            $rules = array(
                'orderId'	      => ['required'],
                'staffId'	      => ['required'],
                'serviceContent'  => ['required'],
                'money'	          => ['required','regex:/^-?\d+$/'],
            );
            
            $messages = array(
                'orderId.required'	=> '8010911',
                'staffId.required'	=> '8010912',
                'serviceContent.required'	 => '80108',
                'money.required'	         => '80109',
                'money.regex'	             => '80113',
            );
            $validator = Validator::make([
                'orderId'     =>$orderId,
                'staffId'     =>$staffId,
                'serviceContent'=>$serviceContent,
                'money'=>$money,
            ], $rules, $messages);
        }
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }  
        $check_staff = SellerStaff::where('id', $staffId)->where('status', 1)->first();
        if (!$check_staff) {
            $result['code'] = 80106; // 服务人员不存在
            return  $result;
        }
        $is_ok = false;
        DB::beginTransaction();
        try {

            if($order_list->order_type < 2){
                //更新订单表
                Order::where('id', $orderId)->update([
                    'seller_staff_id' => $staffId,
                    'status'    => ORDER_STATUS_ASSIGN_SERVICE,
                    'seller_confirm_time' => UTC_TIME,
                ]);
            }else{               
                //更新订单表
                Order::where('id', $orderId)->update([
                    'seller_staff_id' => $staffId,
                    'seller_id' => $check_staff->seller->id,
                    'status'    => ORDER_STATUS_ASSIGN_SERVICE,
                    'total_fee' =>$money,
                    'service_content'=>$serviceContent,
                    'seller_confirm_time' => UTC_TIME,
                ]);
            }

            $result['code'] = 80000;
            $is_ok = true;
            DB::commit();
        } catch(Exception $e) {
            $result['code'] = 80104;
            DB::rollback();
        }

        if($is_ok){
            $data = $order_list->toArray();
            $old_staff = SellerStaff::where('id', $order_list->seller_staff_id)->where('status', 1)->first()->toArray();
            if($old_staff &&  $check_staff->id != $old_staff['id']){
                $url = u('staff#Index/index',['id'=>$orderId,'staffUserId'=>$old_staff['userId'],'newStaffId'=>$old_staff['id'],'isChange'=>2]);
                PushMessageService::notice($old_staff['userId'], $old_staff['mobile'], 'order.changesellerstaff', $data, ['sms','app'], 'staff', 6, $url);//修改前的服务人员推送
            }
            if($check_staff){
                PushMessageService::notice($check_staff->user_id, $check_staff->mobile, 'order.designate', $data, ['sms','app'], 'staff', 3, $orderId,'neworder.caf');//修改后的服务人员推送
            }
        }
        return $result;
    }
    
    /**
     * 随机指派人员
     * @param $orderId 订单编号数组
     */
    public static function ranUpdate($orderId,$serviceContent,$money) {
     $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_system.success.update_info')
        ];
        
        //允许更改的订单状态
        $allow_status = [ORDER_STATUS_BEGIN_SERVICE,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_AFFIRM_SERVICE,ORDER_PAY_STATUS_YES];
        $order_list = Order::where('id', $orderId)->whereIn('status', $allow_status)->first();
        if (!$order_list) {
            $result['code'] = ORDER_NOT_EXIST; // 订单不存在
            return  $result;
        }
        if (!$order_list) {
            $result['code'] = 80107; // 不能指派
            return  $result;
        }
        if($order_list->order_type < 2){
            $rules = array(
                'orderId'	      => ['required'],
            );
            
            $messages = array(
                'orderId.required'	=> '8010911',
            );
            $validator = Validator::make([
                'orderId'     =>$orderId,
            ], $rules, $messages);
        }else{
            $rules = array(
                'orderId'	      => ['required'],
                'serviceContent'  => ['required'],
                'money'	          => ['required','regex:/^-?\d+$/'],
            );
            
            $messages = array(
                'orderId.required'	=> '8010911',
                'serviceContent.required'	 => '80108',
                'money.required'	         => '80109',
                'money.regex'	             => '80113',
            );
            $validator = Validator::make([
                'orderId'     =>$orderId,
                'serviceContent'=>$serviceContent,
                'money'=>$money,
            ], $rules, $messages);
        }
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }  
        
        $check_staff = SellerStaff::where('status',1)->get()->toArray();
        $staff = $check_staff[array_rand($check_staff,1)];
        if (!$staff) {
            $result['code'] = 80106; // 服务人员不存在
            return  $result;
        }
        DB::beginTransaction();
        try {
        
            if($order_list->order_type < 2){
                //更新订单表
                Order::where('id', $orderId)->update([
                    'seller_staff_id' => $staff['id'],
                    'seller_id' =>$staff['seller_id'],
                    'status'    => ORDER_STATUS_ASSIGN_SERVICE,
                    'seller_confirm_time' => UTC_TIME,
                ]);
            }else{
                //更新订单表
                Order::where('id', $orderId)->update([
                    'seller_staff_id' =>$staff['id'],
                    'seller_id' => $staff['seller_id'],
                    'status'    => ORDER_STATUS_ASSIGN_SERVICE,
                    'total_fee' =>$money,
                    'service_content'=>$serviceContent,
                    'seller_confirm_time' => UTC_TIME,
                ]);
            }
            $result['code'] = 80000;
            DB::commit();
        } catch(Exception $e) {
            $result['code'] = 80104;
            DB::rollback();
        }
        return $result;
    }
    /**
     * 随机指派人员
     * @param int $id 请假记录编号
     * @param array $orderIds 订单编号数组
     */
    public static function ranUpdateStaff($id, $orderIds) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_system.success.update_info')
        ];

        $staff_id = StaffLeave::where('id', $id)->pluck('staff_id');
        if ((int)$staff_id < 1) {
            $result['code'] = 50601; //请假记录不存在
            return $result;
        }
        //允许更改的订单状态
        $allow_status = [ORDER_STATUS_WAIT_PAY,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_AFFIRM_SERVICE,ORDER_STATUS_STAFF_ACCEPT];
        $order_count = Order::whereIn('id', $orderIds)->whereIn('status', $allow_status)->count();
        if ($order_count != count($orderIds) || $order_count < 1) {
            $result['code'] = 80101; //订单编号不合法
            return $result;
        }

        $order_list = Order::whereIn('id', $orderIds)->whereIn('status', $allow_status)->select('id','appoint_time','service_end_time')->get()->toArray();

        $com_sql = 'select staff_id from (';

        foreach ($order_list as $key => $val) {

            $query = 'select staff_id from '.env('DB_PREFIX').'staff_service_time where staff_id not in(';

            $query .= 'select staff_id from '.env('DB_PREFIX').'staff_appoint where appoint_time >= ' . $val['appointTime'] . ' and appoint_time <= '. $val['serviceEndTime'] . ' and (order_id != 0 or is_leave = 1) and appoint_week = '.Time::toDate($val['appointTime'], 'w');

            $query .= ') and week = ' . Time::toDate($val['appointTime'], 'w') . ' and begin_time <= "' . Time::toDate($val['appointTime'], 'H:i') . '" and end_time >= "' . Time::toDate($val['serviceEndTime'], 'H:i') . '" and staff_id != '. $staff_id;

            if ($key == 0) {
                $com_sql .= $query;
            } else {
                $com_sql .= ' UNION ALL '.$query;
            }
        }

        $com_sql .= ') as stids group by staff_id HAVING COUNT(staff_id) = '.$order_count;

        $staff_ids = DB::select($com_sql);

        if (count($staff_ids) < 1) {
            $result['code'] = 80105;
            return $result;
        } else {
            foreach ($staff_ids as $key=>$val) {
                $staff_ids[$key] = $val->staff_id;
            }
            $staffId = $staff_ids[rand(0,count($staff_ids)-1)];
            $staff = SellerStaff::where('id', $staffId)->first();
            DB::beginTransaction();
            try {
                //更新订单表
                Order::whereIn('id', $orderIds)->update([
                    'seller_staff_id' => $staffId,
                    'seller_id' => $staff->selle_id,
                    'designate_type' => 1
                ]);

                //更新预约表
                $appoint_data = [];
                foreach ($order_list as $k=>$v) {
                    $btime = $val['appointTime'] % 1800 == 0 ? $val['appointTime'] : ceil($val['appointTime']/1800) * 1800;
                    $etime = $val['serviceEndTime'] % 1800 == 0 ? $val['serviceEndTime'] : ceil($val['serviceEndTime']/1800) * 1800;
                    for ($i = $btime; $i < $etime; $i += 1800) {
                        $appoint = StaffAppoint::where('appoint_time',$i)
                            ->where('staff_id', $staffId)
                            ->first();
                        if ($appoint) {
                            StaffAppoint::where('staff_id',$staffId)
                                ->where('appoint_time', $i)
                                ->update([
                                    'appoint_week' => Time::toDate($i,'w'),
                                    'order_id' => $v['id'],
                                    'status' => 1
                                ]);
                        } else {
                            $appoint_data[] = [
                                'staff_id' => $staffId,
                                'seller_id' => $staff->selle_id,
                                'appoint_time' => $i,
                                'appoint_day' => Time::toDayTime($i),
                                'appoint_week' => Time::toDate($i,'w'),
                                'order_id' => $v['id'],
                                'status' => 1
                            ];

                        }
                    }
                }
                if (count($appoint_data) > 0) {
                    StaffAppoint::insert($appoint_data);
                }
                $result['msg'] = '已指派给'.$staff->name;
                DB::commit();
                return $result;
            } catch(Exception $e) {
                $result['code'] = 80104;
                DB::rollback();
                return $result;
            }
        } 
    }


    /**
     * 退款处理
     * @param int $adminId 操作管理员编号
     * @param int $id 订单编号
     * @param int $status 退款状态
     * @param string $remark 退款备注
     */
    public static function refund($adminId, $id, $status, $remark) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_system.success.handle')
        ];

        $check = Order::where('id', $id)->first();
        if (!$check) {
            $result['code'] = '50101';
            return $result;
        }
        //需要修改的订单状态及订单当前状态是否正确
        if ($status != ORDER_REFUND_ADMIN_AGREE &&
            $status != ORDER_REFUND_ADMIN_REFUSE &&
            $check->status != ORDER_STATUS_REFUND_AUDITING &&
            $check->status != ORDER_REFUND_SELLER_AGREE &&
            $check->pay_status != 1 && $check->pay_fee <= 0)
        {
            $result['code'] = '50103';
            return $result;
        }

        //当为拒绝退款时,必须输入备注
        if ($status == ORDER_REFUND_ADMIN_REFUSE && $remark == '') {
            $result['code'] = '50102';
            return $result;
        }

        $res = Order::where('id', $id)->update([
            'status' => $status,
            'dispose_refund_admin' => $adminId,
            'dispose_refund_time' => UTC_TIME,
            'dispose_refund_remark' => $remark
        ]);
        
        if($status == ORDER_REFUND_ADMIN_AGREE &&
            $check->pay_fee >= 0.0001 && 
            $check->pay_status == ORDER_PAY_STATUS_YES)
        {
            $userPayLog = DB::table('user_pay_log')
                ->where("order_id", $check->id)
                ->where("status", 1)
                ->frist();
            
            if($userPayLog != null)
            {
                DB::table('refund')->insert(
                    [
                        "sn"=>$check->sn,
                        "user_id"=>$check->user_id,
                        "order_id"=>$check->id,
                        "trade_no"=>$userPayLog->trade_no,
                        "seller_id"=>$check->seller_id,
                        "payment_type"=>$userPayLog->payment_type,
                        "money"=>$check->pay_fee,
                        "content"=>"申请退款",
                        "create_time"=>UTC_TIME,
                        "create_day"=>UTC_DAY,
                        "status"=>0
                    ]);

                baseSellerMoneyLogService::createLog(
                    $check->seller_id,
                    SellerMoneyLog::TYPE_ORDER_REFUND,
                    $check->id,
                    $check->pay_fee,
                    '订单退款：'.$check->sn
                );
            }
        }
        

        //操作失败
        $result = [
            'code' => '-1',
            'data' => null,
            'msg'  => Lang::get('api_system.error.handle')
        ];

        return  $result;
    }

    /**
     * 邀请分佣列表
     */
    public static function invitationlist($sn, $buyer, $invitor, $status, $page, $pageSize){
        $prefix = DB::getTablePrefix();
        DB::connection()->enableQueryLog();    
        $list = Order::leftJoin('invitation_back_log', function($join){
                            $join->on('order.id', '=', 'invitation_back_log.order_id'); 
                        })
                     ->where('order.is_invitation', 1);
        if($buyer == true){
            $buyerId = User::where('name', $buyer)->pluck('id');
            $list->where('invitation_back_log.user_id', '=', $buyerId);
        }
        if($invitor == true){
            $invitorId = User::where('name', $invitor)->pluck('id');
            $list->where('invitation_back_log.invitation_id', '=', $invitorId);
        }
        if($status > 0){
            if($status == 1){
                //查询未完结的订单
                $list->whereRaw("(".$prefix."order.status NOT IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.",".ORDER_STATUS_USER_DELETE.",".ORDER_STATUS_SELLER_DELETE.",".ORDER_STATUS_ADMIN_DELETE.")
                    AND buyer_finish_time IS NULL )");
            } elseif($status == 2) {
                //查询已完结的订单
                $list->whereRaw("(".$prefix."order.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                    OR (".$prefix."order.status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) 
                    OR (".$prefix."order.status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) 
                    OR (".$prefix."order.status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) 
                    OR (".$prefix."order.status = ".ORDER_STATUS_REFUND_SUCCESS." AND cancel_time IS NOT NULL))");

            }
        }
        if($sn == true){
            $list->where('order.sn', $sn);
        }

        $totalCount = count($list->groupBy('order.id')->lists('sn')); 

        $list = $list->select('order.*')
                     ->selectRaw('IFNULL(SUM(IF('.$prefix.'invitation_back_log.level = 1, '.$prefix.'invitation_back_log.return_fee, 0)), 0) AS level1,IFNULL(SUM(IF('.$prefix.'invitation_back_log.level = 2, '.$prefix.'invitation_back_log.return_fee, 0)), 0) AS level2,IFNULL(SUM(IF('.$prefix.'invitation_back_log.level = 3, '.$prefix.'invitation_back_log.return_fee, 0)), 0) AS level3')
                     ->skip(($page - 1) * $pageSize)
                     ->take($pageSize)
                     ->with('user') 
                     ->orderBy('order.id')
                     ->get();
                     // print_r(DB::getQueryLog());exit;
        return ["list" => $list, "totalCount" => $totalCount];
    }

    /**
     * 更改系统配送人员
     */
    public function changeStaffSystem($id,$changesellerStaffId){
        $order = Order::where('id',$id)->where('is_all', 0)->with('staff')->first();
        //没有订单
        if ($order == false)
        {
            $result['code'] = 50101;
            return $result;
        }

        //订单状态不对
        if($order->status == ORDER_STATUS_AFFIRM_SELLER || $order->status == ORDER_STATUS_START_SERVICE){
            $result['code'] = 50103;
            return $result;
        }

        $changesellerStaff = SellerStaff::where('id',$changesellerStaffId)->where('is_system', 1)->first();
        //没有订单
        if ($changesellerStaff == false)
        {
            $result['code'] = 80102;
            return $result;
        }
        //如果老的和现在的相同
        if($order->seller_staff_id == $changesellerStaffId){
            $result['code'] = 0;
            return $result;
        }

        DB::beginTransaction();
        try {
            $sellerStaffId = $order->seller_staff_id; //原服务人员的Id
            $staffUserId = $order->staff->user_id;  //原服务人员的用户的Id

            $data['status'] = ORDER_STATUS_GET_SYSTEM_SEND;
            $data['seller_staff_id'] = $changesellerStaffId;
            Order::where('id',$id)->update($data);

            $order = $order->toArray();

            //发推送说订单已经被接了
            $sellerStaffId_sellerId = SellerStaff::where('id',$sellerStaffId)->pluck('seller_id');
            if($sellerStaffId_sellerId == 0){
                $url = u('staff#Index/index',['id'=>$id,'staffUserId'=>$staffUserId,'newStaffId'=>$changesellerStaffId,'isChange'=>2]);
                PushMessageService::notice($staffUserId, '', 'order.changesellerstaff', $order, ['app'], 'staff', 6, $url);
            }
            //给新人发送一个订单有新的订单
            PushMessageService::notice( $changesellerStaff->user_id, '', 'order.create', $order,['app'], 'staff', 3, $id,"neworder.caf");
            DB::commit();
        } catch(Exception $e) {
            $result['code'] = 80104;
            DB::rollback();
            return $result;
        }


        $result['code'] = 0;
        return $result;
    }

}
